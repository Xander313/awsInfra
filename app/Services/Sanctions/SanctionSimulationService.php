<?php

namespace App\Services\Sanctions;

use App\Models\IAM\AppUser;
use App\Models\Risk\Incident;
use App\Models\Risk\Org;
use App\Models\Risk\SanctionCoefficient;
use App\Models\Risk\SanctionSimulation;
use App\Support\Sanctions\SanctionWizardDefinition;

class SanctionSimulationService
{
    public function __construct(
        private readonly SanctionCalculationService $calculationService
    ) {
    }

    public function createFromState(array $state, ?int $orgId = null, ?AppUser $user = null): SanctionSimulation
    {
        $resultSummary = $this->calculationService->calculate($state);

        return SanctionSimulation::query()->create(
            $this->buildPayload($state, $resultSummary, $orgId, $user)
        );
    }

    public function buildPayload(array $state, array $resultSummary, ?int $orgId = null, ?AppUser $user = null): array
    {
        $general = data_get($state, 'general', []);
        $incident = $this->resolveIncident($state);

        return [
            'org_id' => $orgId,
            'incident_id' => $incident?->incident_id,
            'org_name' => $this->resolveOrgName($orgId),
            'created_by_user_id' => $user?->user_id,
            'created_by_user_name' => $user?->full_name,
            'rule_set' => 'default',
            'case_name' => data_get($general, 'case_name'),
            'entity_type' => (string) data_get($general, 'entity_type'),
            'company_role' => (string) data_get($general, 'company_role'),
            'deterministic_fine_usd' => (float) data_get($resultSummary, 'components.fine.amount_usd', 0),
            'monte_carlo_min_usd' => $this->nullableFloat(data_get($resultSummary, 'monte_carlo.summary.minimum')),
            'monte_carlo_mean_usd' => $this->nullableFloat(data_get($resultSummary, 'monte_carlo.summary.mean')),
            'monte_carlo_max_usd' => $this->nullableFloat(data_get($resultSummary, 'monte_carlo.summary.maximum')),
            'wizard_snapshot' => $state,
            'incident_snapshot' => $this->buildIncidentSnapshot($incident),
            'result_snapshot' => $resultSummary,
            'documentation_snapshot' => [
                'documentation' => SanctionWizardDefinition::documentation(),
                'assumptions' => SanctionWizardDefinition::assumptions(),
                'reference_maps' => [
                    'data_types' => SanctionWizardDefinition::dataTypesMap(),
                    'impact_levels' => SanctionWizardDefinition::impactLevelsMap(),
                    'intentionality_options' => SanctionWizardDefinition::intentionalityOptionsMap(),
                ],
                'saved_at' => now()->toIso8601String(),
            ],
            'coefficient_snapshot' => $this->coefficientSnapshot(),
        ];
    }

    private function coefficientSnapshot(): array
    {
        return SanctionCoefficient::query()
            ->where('rule_set', 'default')
            ->orderBy('group_name')
            ->orderBy('sort_order')
            ->orderBy('display_name')
            ->get([
                'coefficient_id',
                'rule_set',
                'group_name',
                'coefficient_key',
                'display_name',
                'coefficient_class',
                'value_numeric',
                'value_type',
                'active_flag',
                'value_editable',
                'toggle_allowed',
                'updated_at',
            ])
            ->map(fn (SanctionCoefficient $coefficient) => [
                'coefficient_id' => $coefficient->coefficient_id,
                'rule_set' => $coefficient->rule_set,
                'group_name' => $coefficient->group_name,
                'coefficient_key' => $coefficient->coefficient_key,
                'display_name' => $coefficient->display_name,
                'coefficient_class' => $coefficient->coefficient_class,
                'value_numeric' => (float) $coefficient->value_numeric,
                'value_type' => $coefficient->value_type,
                'active_flag' => (bool) $coefficient->active_flag,
                'value_editable' => (bool) $coefficient->value_editable,
                'toggle_allowed' => (bool) $coefficient->toggle_allowed,
                'updated_at' => optional($coefficient->updated_at)?->toIso8601String(),
            ])
            ->values()
            ->all();
    }

    private function resolveOrgName(?int $orgId): ?string
    {
        if (!$orgId) {
            return null;
        }

        return Org::query()
            ->where('org_id', $orgId)
            ->value('name');
    }

    private function nullableFloat(mixed $value): ?float
    {
        return $value === null ? null : (float) $value;
    }

    private function resolveIncident(array $state): ?Incident
    {
        $incidentId = data_get($state, 'meta.incident_id');
        if (!$incidentId) {
            return null;
        }

        return Incident::query()
            ->with(['org.regulatoryProfile', 'system', 'processingActivity'])
            ->find($incidentId);
    }

    private function buildIncidentSnapshot(?Incident $incident): ?array
    {
        if (!$incident) {
            return null;
        }

        $profile = $incident->org?->regulatoryProfile;

        return [
            'incident_id' => $incident->incident_id,
            'incident_code' => $incident->incident_code,
            'title' => $incident->title,
            'description' => $incident->description,
            'org_id' => $incident->org_id,
            'org_name' => $incident->org?->name,
            'status' => $incident->status,
            'severity' => $incident->severity,
            'company_role' => $incident->company_role,
            'occurred_at' => optional($incident->occurred_at)?->toIso8601String(),
            'detected_at' => optional($incident->detected_at)?->toIso8601String(),
            'closed_at' => optional($incident->closed_at)?->toIso8601String(),
            'data_subject_count' => $incident->data_subject_count,
            'data_volume_amount' => $incident->data_volume_amount !== null ? (float) $incident->data_volume_amount : null,
            'affected_data_types' => $incident->affected_data_types ?? [],
            'confidentiality_impact' => $incident->confidentiality_impact,
            'integrity_impact' => $incident->integrity_impact,
            'availability_impact' => $incident->availability_impact,
            'vulnerable_groups_flag' => (bool) $incident->vulnerable_groups_flag,
            'system' => $incident->system ? [
                'system_id' => $incident->system->system_id,
                'name' => $incident->system->name,
            ] : null,
            'processing_activity' => $incident->processingActivity ? [
                'pa_id' => $incident->processingActivity->pa_id,
                'name' => $incident->processingActivity->name,
            ] : null,
            'regulatory_profile' => $profile ? [
                'entity_type' => $profile->entity_type,
                'business_volume_usd' => $profile->business_volume_usd !== null ? (float) $profile->business_volume_usd : null,
                'sbu_reference' => $profile->sbu_reference !== null ? (float) $profile->sbu_reference : null,
                'reference_year' => $profile->reference_year,
            ] : null,
            'snapshotted_at' => now()->toIso8601String(),
        ];
    }
}
