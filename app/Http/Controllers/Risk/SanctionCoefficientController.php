<?php

namespace App\Http\Controllers\Risk;

use App\Http\Controllers\Controller;
use App\Models\Risk\SanctionCoefficient;
use App\Models\Risk\SanctionSimulation;
use App\Support\Sanctions\SanctionWizardDefinition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SanctionCoefficientController extends Controller
{
    public function index()
    {
        $coefficients = $this->baseQuery()->get();
        $wizardState = session(SanctionWizardDefinition::sessionKey(), []);
        $groupSummary = $coefficients
            ->groupBy('group_name')
            ->map(function ($items, $group) {
                return [
                    'group' => $group,
                    'count' => $items->count(),
                    'active_count' => $items->filter(fn (SanctionCoefficient $item) => $item->active_flag)->count(),
                ];
            })
            ->values();

        $lastUpdatedAt = $coefficients->max('updated_at');
        $simulationMetrics = $this->simulationMetrics();

        return view('risk.sanctions.index', [
            'coefficients' => $coefficients,
            'groupSummary' => $groupSummary,
            'totalCoefficients' => $coefficients->count(),
            'activeCoefficients' => $coefficients->filter(fn (SanctionCoefficient $item) => $item->active_flag)->count(),
            'lastUpdatedAt' => $lastUpdatedAt,
            'hasWizardState' => !empty($wizardState),
            'wizardCurrentStep' => (int) data_get($wizardState, 'meta.current_step', 1),
            'wizardState' => $wizardState,
            'simulationMetrics' => $simulationMetrics,
        ]);
    }

    public function coefficients()
    {
        return response()->json([
            'data' => $this->baseQuery()->get(),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'coefficients' => ['required', 'array', 'min:1'],
            'coefficients.*.coefficient_id' => ['required', 'integer'],
            'coefficients.*.value_numeric' => ['required', 'numeric'],
            'coefficients.*.active_flag' => ['nullable', 'boolean'],
        ]);

        $rows = collect($data['coefficients']);
        $ids = $rows->pluck('coefficient_id')->map(fn ($id) => (int) $id)->values();

        $existing = SanctionCoefficient::query()
            ->whereIn('coefficient_id', $ids)
            ->get()
            ->keyBy('coefficient_id');

        if ($existing->count() !== $ids->unique()->count()) {
            return response()->json([
                'message' => 'Uno o más coeficientes no existen.',
            ], 422);
        }

        $ruleSet = (string) (optional($existing->first())->rule_set ?: 'default');
        $proposedStates = [];

        foreach ($rows as $row) {
            $coefficient = $existing->get((int) $row['coefficient_id']);
            $proposedActive = $this->resolveProposedActiveFlag($coefficient, $row);
            $proposedValue = $this->resolveProposedValue($coefficient, $row);

            if (!$coefficient->allowsValueEdit() && (string) $proposedValue !== (string) $coefficient->value_numeric) {
                return response()->json([
                    'message' => "El coeficiente '{$coefficient->coefficient_key}' es de solo lectura.",
                ], 422);
            }

            if (!$coefficient->allowsToggle() && $proposedActive === false) {
                $message = $coefficient->coefficient_class === SanctionCoefficient::CLASS_NORMATIVE_FIXED
                    ? "El coeficiente normativo fijo '{$coefficient->coefficient_key}' no puede desactivarse."
                    : "El coeficiente configurable del modelo '{$coefficient->coefficient_key}' debe permanecer activo.";

                return response()->json([
                    'message' => $message,
                ], 422);
            }

            $proposedStates[$coefficient->coefficient_id] = $proposedActive;
        }

        $incompleteMessage = $this->validateRuleSetCompleteness($ruleSet, $proposedStates);
        if ($incompleteMessage !== null) {
            return response()->json([
                'message' => $incompleteMessage,
            ], 422);
        }

        DB::transaction(function () use ($rows, $existing) {
            foreach ($rows as $row) {
                $coefficient = $existing->get((int) $row['coefficient_id']);

                SanctionCoefficient::query()
                    ->where('coefficient_id', (int) $row['coefficient_id'])
                    ->update([
                        'value_numeric' => $this->resolveProposedValue($coefficient, $row),
                        'active_flag' => $this->resolveProposedActiveFlag($coefficient, $row),
                        'updated_at' => now(),
                    ]);
            }
        });

        return response()->json([
            'message' => 'Coeficientes actualizados correctamente.',
            'data' => $this->baseQuery()->get(),
        ]);
    }

    private function validateRuleSetCompleteness(string $ruleSet, array $proposedStates): ?string
    {
        $ruleSetCoefficients = SanctionCoefficient::query()
            ->where('rule_set', $ruleSet)
            ->get()
            ->keyBy('coefficient_id');

        $requiredKeys = collect(SanctionCoefficient::requiredKeys());
        $presentKeys = $ruleSetCoefficients->pluck('coefficient_key');

        $missingRequired = $requiredKeys->diff($presentKeys)->values();
        if ($missingRequired->isNotEmpty()) {
            return 'El set activo está incompleto. Faltan coeficientes obligatorios: ' . $missingRequired->join(', ') . '.';
        }

        $inactiveRequired = $ruleSetCoefficients
            ->filter(function (SanctionCoefficient $coefficient) use ($proposedStates) {
                if (!$coefficient->mustRemainActive()) {
                    return false;
                }

                $active = $proposedStates[$coefficient->coefficient_id] ?? $coefficient->active_flag;

                return $active !== true;
            })
            ->pluck('coefficient_key')
            ->values();

        if ($inactiveRequired->isNotEmpty()) {
            return 'El set activo quedaría incompleto. Coeficientes obligatorios inactivos: ' . $inactiveRequired->join(', ') . '.';
        }

        return null;
    }

    private function resolveProposedActiveFlag(SanctionCoefficient $coefficient, array $row): bool
    {
        if (!$coefficient->allowsToggle()) {
            return true;
        }

        if (array_key_exists('active_flag', $row) && $row['active_flag'] !== null) {
            return (bool) $row['active_flag'];
        }

        return $coefficient->active_flag;
    }

    private function resolveProposedValue(SanctionCoefficient $coefficient, array $row): mixed
    {
        if (!$coefficient->allowsValueEdit()) {
            return $coefficient->value_numeric;
        }

        return $row['value_numeric'];
    }

    private function baseQuery()
    {
        return SanctionCoefficient::query()
            ->where('rule_set', 'default')
            ->orderBy('group_name')
            ->orderBy('sort_order')
            ->orderBy('display_name');
    }

    private function simulationMetrics(): array
    {
        if (!Schema::hasTable('risk.sanction_simulation')) {
            return [
                'count' => 0,
                'latestAt' => null,
                'latestFine' => null,
            ];
        }

        $query = SanctionSimulation::query();
        $orgId = session('org_id');

        if ($orgId !== null) {
            $query->where('org_id', (int) $orgId);
        }

        $latest = (clone $query)->latest('created_at')->first();

        return [
            'count' => (clone $query)->count(),
            'latestAt' => $latest?->created_at,
            'latestFine' => $latest?->deterministic_fine_usd,
        ];
    }
}
