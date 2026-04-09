<?php

namespace App\Http\Controllers\Risk;

use App\Http\Controllers\Controller;
use App\Models\Risk\SanctionSimulation;
use App\Services\Sanctions\SanctionCalculationService;
use App\Services\Sanctions\SanctionSimulationService;
use App\Support\Sanctions\SanctionWizardDefinition;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SanctionSimulationController extends Controller
{
    public function __construct(
        private readonly SanctionCalculationService $calculationService,
        private readonly SanctionSimulationService $simulationService
    ) {
    }

    public function index(Request $request): View
    {
        $orgId = $this->currentOrgId();
        $query = SanctionSimulation::query()->orderByDesc('created_at');

        if ($orgId !== null) {
            $query->where('org_id', $orgId);
        }

        $simulations = $query->paginate(15);

        return view('risk.sanctions.simulations.index', [
            'simulations' => $simulations,
            'currentOrgId' => $orgId,
        ]);
    }

    public function storeCurrent(Request $request): RedirectResponse
    {
        $state = $request->session()->get(SanctionWizardDefinition::sessionKey(), []);
        $firstIncomplete = $this->firstIncompleteStep($state);

        if ($firstIncomplete < 7) {
            return redirect()
                ->route('risk.ui.sanctions.wizard.show', ['step' => $firstIncomplete])
                ->with('error', 'Completa el asistente antes de guardar la simulación.');
        }

        try {
            $simulation = $this->simulationService->createFromState(
                $state,
                $this->resolveOrgId($state),
                $request->user()
            );
        } catch (\Throwable $exception) {
            return redirect()
                ->route('risk.ui.sanctions.wizard.show', ['step' => 7])
                ->with('error', 'No se pudo guardar la simulación. Verifica la disponibilidad de la base de datos y los coeficientes activos.');
        }

        $incidentLabel = data_get($state, 'meta.incident_label');
        $statusMessage = $incidentLabel
            ? 'La simulación se guardó correctamente y quedó vinculada al incidente ' . $incidentLabel . '.'
            : 'La simulación se guardó correctamente.';

        return redirect()
            ->route('risk.ui.sanctions.simulations.show', $simulation)
            ->with('status', $statusMessage);
    }

    public function show(Request $request, SanctionSimulation $simulation): View
    {
        $this->guardCurrentOrg($simulation);
        $simulation->loadMissing(['incident', 'officialForIncident']);

        return view('risk.sanctions.simulations.show', [
            'simulation' => $simulation,
            'wizardState' => $simulation->wizard_snapshot ?? [],
            'resultSummary' => $simulation->result_snapshot ?? [],
            'documentation' => data_get($simulation->documentation_snapshot, 'documentation', []),
            'assumptions' => data_get($simulation->documentation_snapshot, 'assumptions', []),
            'referenceMaps' => data_get($simulation->documentation_snapshot, 'reference_maps', []),
        ]);
    }

    public function currentReport(Request $request): View|RedirectResponse
    {
        $state = $request->session()->get(SanctionWizardDefinition::sessionKey(), []);
        $firstIncomplete = $this->firstIncompleteStep($state);

        if ($firstIncomplete < 7) {
            return redirect()
                ->route('risk.ui.sanctions.wizard.show', ['step' => $firstIncomplete])
                ->with('error', 'Completa el asistente antes de exportar el informe.');
        }

        try {
            $resultSummary = $this->calculationService->calculate($state);
        } catch (\Throwable $exception) {
            return redirect()
                ->route('risk.ui.sanctions.wizard.show', ['step' => 7])
                ->with('error', 'No se pudo generar el informe del resultado actual.');
        }
        $payload = $this->simulationService->buildPayload(
            $state,
            $resultSummary,
            $this->resolveOrgId($state),
            $request->user()
        );

        return view('risk.sanctions.simulations.report', [
            'mode' => 'current',
            'record' => $payload,
        ]);
    }

    public function report(Request $request, SanctionSimulation $simulation): View
    {
        $this->guardCurrentOrg($simulation);

        return view('risk.sanctions.simulations.report', [
            'mode' => 'saved',
            'simulation' => $simulation,
            'record' => [
                'org_name' => $simulation->org_name,
                'created_by_user_name' => $simulation->created_by_user_name,
                'created_at' => optional($simulation->created_at)?->toIso8601String(),
                'wizard_snapshot' => $simulation->wizard_snapshot,
                'result_snapshot' => $simulation->result_snapshot,
                'documentation_snapshot' => $simulation->documentation_snapshot,
                'coefficient_snapshot' => $simulation->coefficient_snapshot,
            ],
        ]);
    }

    private function currentOrgId(): ?int
    {
        $orgId = session('org_id');

        return $orgId !== null ? (int) $orgId : null;
    }

    private function resolveOrgId(array $state): ?int
    {
        return $this->currentOrgId()
            ?? data_get($state, 'meta.incident_org_id');
    }

    private function guardCurrentOrg(SanctionSimulation $simulation): void
    {
        $currentOrgId = $this->currentOrgId();

        if ($currentOrgId !== null && $simulation->org_id !== null && (int) $simulation->org_id !== $currentOrgId) {
            throw new NotFoundHttpException();
        }
    }

    private function firstIncompleteStep(array $state): int
    {
        for ($step = 1; $step < 7; $step++) {
            if (!$this->isStepComplete($step, $state)) {
                return $step;
            }
        }

        return 7;
    }

    private function isStepComplete(int $step, array $state): bool
    {
        return match ($step) {
            1 => filled(data_get($state, 'general.entity_type'))
                && filled(data_get($state, 'general.company_role'))
                && (
                    (data_get($state, 'general.entity_type') === 'privada' && filled(data_get($state, 'general.business_volume_usd')))
                    || (data_get($state, 'general.entity_type') === 'publica' && filled(data_get($state, 'general.sbu_reference')))
                ),
            2 => filled(data_get($state, 'cdi.code')) && filled(data_get($state, 'cdi.severity')),
            3 => count(data_get($state, 'pdi.answers', [])) === count(SanctionWizardDefinition::pdiQuestions()),
            4 => filled(data_get($state, 'ndv.confidentiality_impact'))
                && filled(data_get($state, 'ndv.integrity_impact'))
                && filled(data_get($state, 'ndv.availability_impact'))
                && count(data_get($state, 'ndv.data_types', [])) > 0
                && filled(data_get($state, 'ndv.data_subject_count'))
                && filled(data_get($state, 'ndv.data_volume_amount'))
                && (data_get($state, 'ndv.vulnerable_groups') === false || filled(data_get($state, 'ndv.vulnerable_groups'))),
            5 => filled(data_get($state, 'int.level')),
            6 => filled(data_get($state, 'rer.applies')) || data_get($state, 'rer.applies') === false,
            default => false,
        };
    }
}
