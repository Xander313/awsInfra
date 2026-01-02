<?php

namespace App\Http\Controllers\Audit;

use App\Http\Controllers\Controller;
use App\Models\Audit\Audit;
use App\Models\IAM\AppUser;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    /**
     * Mostrar listado de auditorías
     */
    public function index()
    {
        $audits = Audit::where('org_id', session('org_id'))
            ->with('auditor', 'org')
            ->orderBy('planned_at')
            ->get();

        return view('audit.audits.index', compact('audits'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        $users = AppUser::all();
        return view('audit.audits.create', compact('users'));
    }

    /**
     * Guardar nueva auditoría
     */
    public function store(Request $request)
    {
        $request->validate([
            'audit_type' => 'required|string|max:255',
            'scope' => 'nullable|string',
            'auditor_user_id' => ['nullable', 'exists:' . AppUser::class . ',user_id'],
            'planned_at' => 'nullable|date',
            'status' => 'required|string|in:PLANNED,IN_PROGRESS,COMPLETED,CLOSED',
        ]);

        Audit::create([
            'org_id' => session('org_id'),
            'audit_type' => $request->audit_type,
            'scope' => $request->scope,
            'auditor_user_id' => $request->auditor_user_id,
            'planned_at' => $request->planned_at,
            'status' => $request->status,
        ]);

        return redirect()->route('audits.index')
            ->with('success', 'Auditoría creada correctamente.');
    }

    /**
     * Mostrar detalle de una auditoría
     */
    public function show(Audit $audit)
    {
        $this->authorizeAudit($audit);
        $audit->load('auditor', 'findings.correctiveActions');

        return view('audit.audits.show', compact('audit'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(Audit $audit)
    {
        $this->authorizeAudit($audit);
        $users = AppUser::all();

        return view('audit.audits.create', compact('audit', 'users'));
    }

    /**
     * Actualizar auditoría existente
     */
    public function update(Request $request, Audit $audit)
    {
        $this->authorizeAudit($audit);

        $request->validate([
            'audit_type' => 'required|string|max:255',
            'scope' => 'nullable|string',
            'auditor_user_id' => ['nullable', 'exists:' . AppUser::class . ',user_id'],
            'planned_at' => 'nullable|date',
            'status' => 'required|string|in:PLANNED,IN_PROGRESS,COMPLETED,CLOSED',
        ]);

        $data = $request->only([
            'audit_type',
            'scope',
            'auditor_user_id',
            'planned_at',
            'status',
        ]);

        if ($request->status === 'CLOSED' && !$audit->executed_at) {
            $data['executed_at'] = now();
        }

        $audit->update($data);

        return redirect()->route('audits.index')
            ->with('success', 'Auditoría actualizada correctamente.');
    }

    /**
     * Cambio rápido de estado vía AJAX
     */
    public function changeStatus(Request $request, Audit $audit)
    {
        $this->authorizeAudit($audit);

        $request->validate([
            'status' => 'required|string|in:PLANNED,IN_PROGRESS,COMPLETED,CLOSED',
        ]);

        $audit->status = $request->status;

        if ($request->status === 'CLOSED' && !$audit->executed_at) {
            $audit->executed_at = now();
        }

        $audit->save();

        return response()->json([
            'success' => true,
            'status' => $audit->status
        ]);
    }


    /**
     * Validación de pertenencia a la organización activa
     */
    private function authorizeAudit(Audit $audit)
    {
        $sessionOrg = session('org_id');

        if (!$sessionOrg) {
            abort(403, 'No existe organización activa en sesión');
        }

        if ((int)$audit->org_id !== (int)$sessionOrg) {
            abort(403, 'Acceso no autorizado a esta auditoría');
        }
    }
}
