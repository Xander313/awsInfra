<?php

namespace App\Http\Controllers\Audit;

use App\Http\Controllers\Controller;
use App\Models\Audit\AuditFinding;
use App\Models\Audit\Audit;
use App\Models\Audit\Control;
use Illuminate\Http\Request;

class AuditFindingController extends Controller
{
    public function index()
    {
        if ($r = $this->requireOrg('Active una organización para visualizar los hallazgos.')) return $r;

        $findings = AuditFinding::whereHas('audit', function ($q) {
                $q->where('org_id', session('org_id'));
            })
            ->with(['audit', 'control'])
            ->orderBy('severity')
            ->get();

        return view('audit.findings.index', compact('findings'));
    }

    public function create()
    {
        if ($r = $this->requireOrg('Debe activar una organización antes de registrar hallazgos.')) return $r;

        $audits = Audit::where('org_id', session('org_id'))->get();
        $controls = Control::where('org_id', session('org_id'))->get();

        return view('audit.findings.create', compact('audits', 'controls'));
    }

    public function store(Request $request)
    {
        if ($r = $this->requireOrg('Debe activar una organización.')) return $r;

        $data = $request->validate([
            'audit_id' => ['required', 'exists:' . Audit::class . ',audit_id'],
            'control_id' => ['nullable', 'exists:' . Control::class . ',control_id'],
            'severity' => 'required|string|max:50',
            'description' => 'required|string|max:1000',
            'status' => 'required|string|in:open,in_progress,closed',
        ]);

        // ✅ validar que audit pertenezca a la org activa
        Audit::where('audit_id', $data['audit_id'])
            ->where('org_id', session('org_id'))
            ->firstOrFail();

        // ✅ si viene control, validar que sea de la misma org
        if (!empty($data['control_id'])) {
            Control::where('control_id', $data['control_id'])
                ->where('org_id', session('org_id'))
                ->firstOrFail();
        }

        AuditFinding::create([
            'audit_id' => $data['audit_id'],
            'control_id' => $data['control_id'] ?? null,
            'severity' => $data['severity'],
            'description' => $data['description'],
            'status' => $data['status'],
        ]);

        return redirect()->route('findings.index')
            ->with('success', 'Hallazgo creado correctamente.');
    }

    public function show(AuditFinding $finding)
    {
        $this->authorizeFinding($finding);

        $finding->load(['audit', 'control', 'correctiveActions.owner']);

        return view('audit.findings.show', compact('finding'));
    }

    public function edit(AuditFinding $finding)
    {
        $this->authorizeFinding($finding);

        $audits = Audit::where('org_id', session('org_id'))->get();
        $controls = Control::where('org_id', session('org_id'))->get();

        // ✅ MISMA vista create para editar
        return view('audit.findings.create', compact('finding', 'audits', 'controls'));
    }

    public function update(Request $request, AuditFinding $finding)
    {
        $this->authorizeFinding($finding);

        $data = $request->validate([
            'audit_id' => ['required', 'exists:' . Audit::class . ',audit_id'],
            'control_id' => ['nullable', 'exists:' . Control::class . ',control_id'],
            'severity' => 'required|string|max:50',
            'description' => 'required|string|max:1000',
            'status' => 'required|string|in:open,in_progress,closed',
        ]);

        // ✅ validar que audit pertenezca a la org activa
        Audit::where('audit_id', $data['audit_id'])
            ->where('org_id', session('org_id'))
            ->firstOrFail();

        // ✅ si viene control, validar que sea de la misma org
        if (!empty($data['control_id'])) {
            Control::where('control_id', $data['control_id'])
                ->where('org_id', session('org_id'))
                ->firstOrFail();
        }

        $finding->update([
            'audit_id' => $data['audit_id'],
            'control_id' => $data['control_id'] ?? null,
            'severity' => $data['severity'],
            'description' => $data['description'],
            'status' => $data['status'],
        ]);

        return redirect()->route('findings.index')
            ->with('success', 'Hallazgo actualizado correctamente.');
    }

    // ✅ Para el dropdown AJAX en index
    public function changeStatus(Request $request, AuditFinding $finding)
    {
        $data = $request->validate([
            'status' => 'required|string|in:open,in_progress,closed',
        ]);

        $this->authorizeFinding($finding);

        $finding->status = $data['status'];
        $finding->save();

        return response()->json([
            'success' => true,
            'status' => $finding->status,
        ]);
    }

    // =========================
    // Helpers
    // =========================
    private function requireOrg(string $message)
    {
        // ✅ CORRECCIÓN MÍNIMA: NO usar send() ni exit (rompe flash session)
        if (!session()->has('org_id')) {
            return redirect()->route('orgs.index')->with('warning', $message);
        }
        return null;
    }

    private function authorizeFinding(AuditFinding $finding): void
    {
        // Asegura que audit venga cargado para comparar org_id
        $finding->loadMissing('audit');

        if (!session()->has('org_id') ||
            (int)$finding->audit->org_id !== (int)session('org_id')) {
            abort(403, 'El hallazgo no pertenece a la organización activa.');
        }
    }
}
