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
        $findings = AuditFinding::whereHas('audit', function ($q) {
                $q->where('org_id', session('org_id'));
            })
            ->with('audit', 'control')
            ->orderBy('severity')
            ->get();

        return view('audit.findings.index', compact('findings'));
    }

    public function create()
    {
        $audits = Audit::where('org_id', session('org_id'))->get();
        $controls = Control::where('org_id', session('org_id'))->get();

        return view('audit.findings.create', compact('audits', 'controls'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'audit_id' => ['required', 'exists:' . Audit::class . ',audit_id'],
            'control_id' => ['nullable', 'exists:' . Control::class . ',control_id'],
            'severity' => 'required|string|max:50',
            'description' => 'nullable|string',
            'status' => 'required|string|max:50',
        ]);

        $audit = Audit::where('audit_id', $request->audit_id)
            ->where('org_id', session('org_id'))
            ->firstOrFail();

        if ($request->control_id) {
            Control::where('control_id', $request->control_id)
                ->where('org_id', session('org_id'))
                ->firstOrFail();
        }

        AuditFinding::create([
            'audit_id' => $audit->audit_id,
            'control_id' => $request->control_id,
            'severity' => $request->severity,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        return redirect()->route('findings.index')
            ->with('success', 'Hallazgo creado correctamente.');
    }

    public function show(AuditFinding $finding)
    {
        $this->authorizeFinding($finding);

        $finding->load('audit', 'control', 'correctiveActions.owner');

        return view('audit.findings.show', compact('finding'));
    }

    public function edit(AuditFinding $finding)
    {
        $this->authorizeFinding($finding);

        $audits = Audit::where('org_id', session('org_id'))->get();
        $controls = Control::where('org_id', session('org_id'))->get();

        return view('audit.findings.create', compact('finding', 'audits', 'controls'));
    }

    public function update(Request $request, AuditFinding $finding)
    {
        $this->authorizeFinding($finding);

        $request->validate([
            'audit_id' => ['required', 'exists:' . Audit::class . ',audit_id'],
            'control_id' => ['nullable', 'exists:' . Control::class . ',control_id'],
            'severity' => 'required|string|max:50',
            'description' => 'nullable|string|max:1000',
            'status' => 'required|string|in:open,in_progress,closed',
        ]);

        $finding->update($request->only([
            'audit_id',
            'control_id',
            'severity',
            'description',
            'status',
        ]));

        return redirect()->route('findings.index')
            ->with('success', 'Hallazgo actualizado correctamente.');
    }


    /**
     * Cambio rápido de estado vía AJAX
     */
    public function changeStatus(Request $request, AuditFinding $finding)
    {
        $this->authorizeFinding($finding);

        $request->validate([
            'status' => 'required|string|in:open,in_progress,closed',
        ]);

        $finding->status = $request->status;
        $finding->save();

        return response()->json([
            'success' => true,
            'status' => $finding->status
        ]);
    }

    private function authorizeFinding(AuditFinding $finding)
    {
        if (!session()->has('org_id')) {
            abort(403, 'No hay organización activa');
        }

        if ((int) $finding->audit->org_id !== (int) session('org_id')) {
            abort(403, 'El hallazgo no pertenece a la organización activa');
        }
    }
}
