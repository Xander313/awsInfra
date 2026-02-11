<?php

namespace App\Http\Controllers\Audit;

use App\Http\Controllers\Controller;
use App\Models\Audit\Audit;
use App\Models\IAM\AppUser;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function index()
    {
        if (!session()->has('org_id')) {
            return redirect()
                ->route('orgs.index')
                ->with('warning', 'Active una organización para visualizar sus auditorías.');
        }

        $audits = Audit::where('org_id', session('org_id'))
            ->with('auditor', 'org')
            ->orderBy('planned_at')
            ->get();

        return view('audit.audits.index', compact('audits'));
    }

    public function create()
    {
        if (!session()->has('org_id')) {
            return redirect()
                ->route('orgs.index')
                ->with('warning', 'Debe activar una organización antes de registrar auditorías.');
        }

        $users = AppUser::all();
        return view('audit.audits.create', compact('users'));
    }

    public function store(Request $request)
    {
        if (!session()->has('org_id')) {
            return redirect()
                ->route('orgs.index')
                ->with('warning', 'Debe activar una organización antes de registrar auditorías.');
        }

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

    public function changeStatus(Request $request, Audit $audit)
    {
        $data = $request->validate([
            'status' => 'required|string|in:PLANNED,IN_PROGRESS,COMPLETED,CLOSED',
        ]);

        $this->authorizeAudit($audit);

        $audit->status = $data['status'];
        $audit->save();

        return response()->json([
            'success' => true,
            'status' => $audit->status
        ]);
    }

    private function authorizeAudit(Audit $audit)
    {
        if (!session()->has('org_id') || (int)$audit->org_id !== (int)session('org_id')) {
            abort(403, 'Acceso no autorizado.');
        }
    }

    public function show(Audit $audit)
    {
        $this->authorizeAudit($audit);

        $audit->load(['org', 'auditor', 'findings']);

        return view('audit.audits.show', compact('audit'));
    }

    // ✅ NUEVO: EDIT (usa la MISMA vista create)
    public function edit(Audit $audit)
    {
        $this->authorizeAudit($audit);

        $users = AppUser::all();

        // 👇 misma vista, pero ahora con $audit para que entre en "Editar"
        return view('audit.audits.create', compact('audit', 'users'));
    }

    // ✅ NUEVO: UPDATE (para que el formulario de editar guarde cambios)
    public function update(Request $request, Audit $audit)
    {
        $this->authorizeAudit($audit);

        $request->validate([
            'audit_type' => 'required|string|max:255',
            'scope' => 'nullable|string',
            'auditor_user_id' => ['nullable', 'exists:' . AppUser::class . ',user_id'],
            'planned_at' => 'nullable|date',
            'executed_at' => 'nullable|date',
            'status' => 'required|string|in:PLANNED,IN_PROGRESS,COMPLETED,CLOSED',
        ]);

        $audit->update([
            'audit_type' => $request->audit_type,
            'scope' => $request->scope,
            'auditor_user_id' => $request->auditor_user_id,
            'planned_at' => $request->planned_at,
            'executed_at' => $request->executed_at,
            'status' => $request->status,
        ]);

        return redirect()->route('audits.index')
            ->with('success', 'Auditoría actualizada correctamente.');
    }
}


