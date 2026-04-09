<?php

namespace App\Http\Controllers\Audit;

use App\Http\Controllers\Controller;
use App\Support\DashboardCache;
use App\Models\Audit\CorrectiveAction;
use App\Models\Audit\AuditFinding;
use App\Models\IAM\AppUser;
use Illuminate\Http\Request;

class CorrectiveActionController extends Controller
{
    public function index()
    {
        if (!session()->has('org_id')) {
            return redirect()
                ->route('orgs.index')
                ->with('warning', 'Debe activar una organización para visualizar las acciones correctivas.');
        }

        $actions = CorrectiveAction::whereHas('finding.audit', function ($q) {
                $q->where('org_id', session('org_id'));
            })
            ->with('finding.audit', 'owner')
            ->orderBy('due_at')
            ->get();

        return view('audit.corrective_actions.index', compact('actions'));
    }

    public function create()
    {
        if (!session()->has('org_id')) {
            return redirect()
                ->route('ors.index')
                ->with('warning', 'Debe activar una organización antes de registrar acciones correctivas.');
        }

        $findings = AuditFinding::whereHas('audit', function ($q) {
            $q->where('org_id', session('org_id'));
        })->get();

        $users = AppUser::all();

        return view('audit.corrective_actions.create', compact('findings', 'users'));
    }

    public function store(Request $request)
    {
        if (!session()->has('org_id')) {
            return redirect()
                ->route('orgs.index')
                ->with('warning', 'Debe activar una organización.');
        }

        $request->validate([
            'finding_id' => ['required', 'exists:' . AuditFinding::class . ',finding_id'],
            'owner_user_id' => ['nullable', 'exists:' . AppUser::class . ',user_id'],
            'due_at' => 'nullable|date',
            'status' => 'required|string|max:50',
            'closed_at' => 'nullable|date',
            'outcome' => 'nullable|string',
        ]);

        $finding = AuditFinding::where('finding_id', $request->finding_id)
            ->whereHas('audit', function ($q) {
                $q->where('org_id', session('org_id'));
            })
            ->firstOrFail();

        CorrectiveAction::create([
            'finding_id' => $finding->finding_id,
            'owner_user_id' => $request->owner_user_id,
            'due_at' => $request->due_at,
            'status' => $request->status,
            'closed_at' => $request->closed_at,
            'outcome' => $request->outcome,
        ]);

        DashboardCache::forgetCurrentOrg();

        return redirect()->route('corrective_actions.index')
            ->with('success', 'Acción correctiva creada correctamente.');
    }

    public function show(CorrectiveAction $action)
    {
        $this->authorizeAction($action);

        $action->load('finding.audit', 'owner');

        return view('audit.corrective_actions.show', ['action' => $action]);
    }

    public function edit(CorrectiveAction $action)
    {
        $this->authorizeAction($action);

        $findings = AuditFinding::whereHas('audit', function ($q) {
            $q->where('org_id', session('org_id'));
        })->get();

        $users = AppUser::all();

        return view('audit.corrective_actions.create', [
            'action'   => $action,
            'findings' => $findings,
            'users'    => $users
        ]);
    }

    public function update(Request $request, CorrectiveAction $action)
    {
        $this->authorizeAction($action);

        $request->validate([
            'finding_id' => ['required', 'exists:' . AuditFinding::class . ',finding_id'],
            'owner_user_id' => ['nullable', 'exists:' . AppUser::class . ',user_id'],
            'due_at' => 'nullable|date',
            'status' => 'required|string|max:50',
            'closed_at' => 'nullable|date',
            'outcome' => 'nullable|string',
        ]);

        $action->update($request->only([
            'finding_id',
            'owner_user_id',
            'due_at',
            'status',
            'closed_at',
            'outcome',
        ]));

        DashboardCache::forgetCurrentOrg();

        return redirect()->route('corrective_actions.index')
            ->with('success', 'Acción correctiva actualizada correctamente.');
    }

    public function destroy(CorrectiveAction $action)
    {
        $this->authorizeAction($action);

        $action->update([
            'status' => 'closed', // 👈 corregido: debe coincidir con tus valores reales
            'closed_at' => now(),
        ]);

        DashboardCache::forgetCurrentOrg();

        return redirect()->route('corrective_actions.index')
            ->with('success', 'Acción correctiva cerrada correctamente.');
    }

    public function changeStatus(Request $request, CorrectiveAction $action)
    {
        $this->authorizeAction($action);

        $request->validate([
            'status' => 'required|string|in:open,in_progress,closed',
        ]);

        $action->status = $request->status;

        if ($request->status === 'closed' && !$action->closed_at) {
            $action->closed_at = now();
        }

        $action->save();
        DashboardCache::forgetCurrentOrg();

        return response()->json([
            'success' => true,
            'status' => $action->status
        ]);
    }

    private function authorizeAction(CorrectiveAction $action)
    {
        if (!session()->has('org_id')) {
            abort(403, 'No hay organización activa.');
        }

        if (!$action->finding || !$action->finding->audit) {
            abort(404, 'La acción correctiva no está asociada correctamente.');
        }

        if ((int) $action->finding->audit->org_id !== (int) session('org_id')) {
            abort(403, 'La acción correctiva no pertenece a la organización activa.');
        }
    }
}
