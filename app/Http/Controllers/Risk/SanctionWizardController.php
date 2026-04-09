<?php

namespace App\Http\Controllers\Risk;

use App\Http\Controllers\Controller;
use App\Models\Risk\Incident;
use App\Models\Risk\SanctionCoefficient;
use App\Services\Sanctions\SanctionCalculationService;
use App\Support\Sanctions\SanctionInputNormalizer;
use App\Support\Sanctions\SanctionWizardDefinition;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SanctionWizardController extends Controller
{
    private const LAST_STEP = 7;

    public function __construct(
        private readonly SanctionCalculationService $calculationService
    ) {
    }

    public function show(Request $request, int $step = 1): View|RedirectResponse
    {
        $step = $this->normalizeStep($step);
        $state = $this->getState($request);
        $accessibleStep = $this->resolveAccessibleStep($step, $state);

        if ($accessibleStep !== $step) {
            return redirect()->route('risk.ui.sanctions.wizard.show', ['step' => $accessibleStep]);
        }

        Arr::set($state, 'meta.current_step', $step);
        $this->putState($request, $state);

        $resultSummary = null;
        $calculationError = null;

        if ($step === self::LAST_STEP) {
            try {
                $resultSummary = $this->calculationService->calculate($state);
            } catch (\Throwable $exception) {
                $calculationError = 'No se pudo completar el cálculo determinista. Verifica la disponibilidad de la base de datos y de los coeficientes configurados.';
            }
        }

        return view('risk.sanctions.wizard.layout', [
            'currentStep' => $step,
            'steps' => SanctionWizardDefinition::steps(),
            'wizardState' => $state,
            'wizardContext' => $this->buildWizardContext($state),
            'completedSteps' => $this->completedSteps($state),
            'stepView' => $this->stepView($step),
            'pageData' => $this->buildPageData($step, $state),
            'resultSummary' => $resultSummary,
            'calculationError' => $calculationError,
        ]);
    }

    public function startFromIncident(Request $request, Incident $incident): RedirectResponse
    {
        $this->authorizeIncident($incident);
        $incident->loadMissing(['org.regulatoryProfile', 'system', 'processingActivity']);

        $state = $this->buildStateFromIncident($incident);
        $this->putState($request, $state);

        return redirect()
            ->route('risk.ui.sanctions.wizard.show', ['step' => 1])
            ->with('status', 'El cálculo sancionatorio fue iniciado desde el incidente ' . $incident->incident_code . '.');
    }

    public function methodology(): View
    {
        return view('risk.sanctions.methodology', [
            'documentation' => SanctionWizardDefinition::documentation(),
        ]);
    }

    public function store(Request $request, int $step): RedirectResponse
    {
        $step = $this->normalizeStep($step);
        $state = $this->getState($request);

        if ($step === self::LAST_STEP) {
            return redirect()->route('risk.ui.sanctions.wizard.show', ['step' => self::LAST_STEP]);
        }

        $validated = $this->validateStep($request, $step, $state);
        $state = $this->mergeState($state, $step, $validated);
        Arr::set($state, 'meta.current_step', min($step + 1, self::LAST_STEP));

        $this->putState($request, $state);

        return redirect()->route('risk.ui.sanctions.wizard.show', ['step' => min($step + 1, self::LAST_STEP)]);
    }

    public function reset(Request $request): RedirectResponse
    {
        $request->session()->forget($this->sessionKey());

        return redirect()
            ->route('risk.ui.sanctions')
            ->with('status', 'El asistente de cálculo de multas fue reiniciado.');
    }

    private function validateStep(Request $request, int $step, array $state): array
    {
        return match ($step) {
            1 => $this->validateGeneralStep($request, $state),
            2 => $this->validateCdiStep($request, $state),
            3 => $this->validatePdiStep($request),
            4 => $this->validateNdvStep($request),
            5 => $this->validateIntStep($request),
            6 => $this->validateRerStep($request),
            default => [],
        };
    }

    private function validateGeneralStep(Request $request, array $state): array
    {
        $validated = $request->validate([
            'case_name' => ['nullable', 'string', 'max:180'],
            'entity_type' => ['required', Rule::in(['privada', 'publica'])],
            'company_role' => ['required', Rule::in(['responsable', 'encargado'])],
            'business_volume_usd' => ['nullable', 'string', 'max:50'],
            'sbu_reference' => ['nullable', 'string', 'max:50'],
        ], [], [
            'case_name' => 'nombre de referencia',
            'entity_type' => 'tipo de entidad',
            'company_role' => 'rol de la empresa',
            'business_volume_usd' => 'volumen de negocio',
            'sbu_reference' => 'SBU de referencia',
        ]);

        if ($validated['entity_type'] === 'privada') {
            $validated['business_volume_usd'] = SanctionInputNormalizer::parseRequiredPositiveNumber(
                $validated['business_volume_usd'] ?? null,
                'business_volume_usd',
                'volumen de negocio USD'
            );
            $validated['sbu_reference'] = null;
        }

        if ($validated['entity_type'] === 'publica') {
            $validated['sbu_reference'] = SanctionInputNormalizer::parseRequiredPositiveNumber(
                $validated['sbu_reference'] ?? null,
                'sbu_reference',
                'SBU de referencia'
            );
            $validated['business_volume_usd'] = null;
        }

        $roleChanged = data_get($state, 'general.company_role') !== $validated['company_role'];

        return $validated + ['_role_changed' => $roleChanged];
    }

    private function validateCdiStep(Request $request, array $state): array
    {
        $role = data_get($state, 'general.company_role');
        $catalog = SanctionWizardDefinition::cdiCatalogForRole($role);

        if (!$this->isStepComplete(1, $state)) {
            throw ValidationException::withMessages([
                'infraction_code' => 'El paso 1 no está completo. Guarda primero los datos generales del caso.',
            ]);
        }

        if ($role === null || empty($catalog)) {
            throw ValidationException::withMessages([
                'infraction_code' => 'No se pudo cargar el catálogo de infracciones para el rol seleccionado.',
            ]);
        }

        $allowedCodes = collect($catalog)
            ->flatten(1)
            ->pluck('code')
            ->values()
            ->all();

        $allowedSeverities = array_keys($catalog);

        $validated = $request->validate([
            'severity' => ['required', Rule::in($allowedSeverities)],
            'infraction_code' => ['required', Rule::in($allowedCodes)],
        ], [], [
            'severity' => 'categoria',
            'infraction_code' => 'infracción',
        ]);

        $selected = collect($catalog[$validated['severity']] ?? [])
            ->firstWhere('code', $validated['infraction_code']);

        if (!$selected) {
            throw ValidationException::withMessages([
                'infraction_code' => 'La infracción seleccionada no corresponde al rol o la categoría activos.',
            ]);
        }

        return [
            'role' => $role,
            'severity' => $validated['severity'],
            'severity_label' => $validated['severity'] === 'leve' ? 'Infracción leve' : 'Infracción grave',
            'code' => $validated['infraction_code'],
            'label' => $selected['title'],
            'description' => $selected['description'] ?? null,
        ];
    }

    private function validatePdiStep(Request $request): array
    {
        $rules = [];

        foreach (SanctionWizardDefinition::pdiQuestions() as $question) {
            $rules['answers.' . $question['key']] = ['required', 'boolean'];
        }

        return $request->validate($rules, [], [
            'answers' => 'respuestas PDI',
        ]);
    }

    private function validateNdvStep(Request $request): array
    {
        $impactValues = collect(SanctionWizardDefinition::impactLevels())->pluck('value')->all();
        $dataTypeValues = collect(SanctionWizardDefinition::dataTypes())->pluck('value')->all();
        $validated = $request->validate([
            'confidentiality_impact' => ['required', Rule::in($impactValues)],
            'integrity_impact' => ['required', Rule::in($impactValues)],
            'availability_impact' => ['required', Rule::in($impactValues)],
            'data_types' => ['required', 'array', 'min:1'],
            'data_types.*' => ['required', Rule::in($dataTypeValues)],
            'data_subject_count' => ['required', 'integer', 'min:1'],
            'data_volume_amount' => ['required', 'string', 'max:50'],
            'vulnerable_groups' => ['required', 'boolean'],
        ], [], [
            'confidentiality_impact' => 'afectacion a confidencialidad',
            'integrity_impact' => 'afectacion a integridad',
            'availability_impact' => 'afectacion a disponibilidad',
            'data_types' => 'tipos de datos afectados',
            'data_subject_count' => 'numero de titulares',
            'data_volume_amount' => 'volumen de datos',
            'vulnerable_groups' => 'grupos vulnerables',
        ]);

        $validated['data_volume_amount'] = SanctionInputNormalizer::parseRequiredPositiveNumber(
            $request->input('data_volume_amount'),
            'data_volume_amount',
            'volumen de datos personales afectados'
        );

        return $validated;
    }

    private function validateIntStep(Request $request): array
    {
        $allowed = collect(SanctionWizardDefinition::intentionalityOptions())->pluck('value')->all();

        $validated = $request->validate([
            'level' => ['required', Rule::in($allowed)],
        ], [], [
            'level' => 'grado de intencionalidad',
        ]);

        $selected = collect(SanctionWizardDefinition::intentionalityOptions())
            ->firstWhere('value', $validated['level']);

        return [
            'level' => $validated['level'],
            'label' => $selected['label'] ?? $validated['level'],
            'description' => $selected['description'] ?? null,
        ];
    }

    private function validateRerStep(Request $request): array
    {
        return $request->validate([
            'applies' => ['required', 'boolean'],
        ], [], [
            'applies' => 'reiteracion y reincidencia',
        ]);
    }

    private function mergeState(array $state, int $step, array $validated): array
    {
        switch ($step) {
            case 1:
                if (($validated['_role_changed'] ?? false) === true) {
                    unset($state['cdi'], $state['pdi'], $state['ndv'], $state['int'], $state['rer']);
                }

                unset($validated['_role_changed']);
                $state['general'] = $validated;
                break;
            case 2:
                $state['cdi'] = $validated;
                break;
            case 3:
                $state['pdi'] = $validated;
                break;
            case 4:
                $state['ndv'] = $validated;
                break;
            case 5:
                $state['int'] = $validated;
                break;
            case 6:
                $state['rer'] = $validated;
                break;
        }

        return $state;
    }

    private function buildPageData(int $step, array $state): array
    {
        $role = data_get($state, 'general.company_role');

        return match ($step) {
            1 => [
                'defaultSbu' => $this->defaultSbuValue(),
            ],
            2 => [
                'role' => $role,
                'cdiCatalog' => SanctionWizardDefinition::cdiCatalogForRole($role),
            ],
            3 => [
                'pdiQuestions' => SanctionWizardDefinition::pdiQuestions(),
            ],
            4 => [
                'dataTypes' => SanctionWizardDefinition::dataTypes(),
                'dataVolumeBands' => SanctionWizardDefinition::dataVolumeBands(),
                'impactLevels' => SanctionWizardDefinition::impactLevels(),
                'impactLevelsMap' => SanctionWizardDefinition::impactLevelsMap(),
            ],
            5 => [
                'intentionalityOptions' => SanctionWizardDefinition::intentionalityOptions(),
            ],
            6 => [],
            7 => [
                'dataTypesMap' => SanctionWizardDefinition::dataTypesMap(),
                'pdiQuestionsCount' => count(SanctionWizardDefinition::pdiQuestions()),
                'impactLevelsMap' => SanctionWizardDefinition::impactLevelsMap(),
                'intentionalityOptionsMap' => SanctionWizardDefinition::intentionalityOptionsMap(),
                'documentation' => SanctionWizardDefinition::documentation(),
            ],
            default => [],
        };
    }

    private function resolveAccessibleStep(int $step, array $state): int
    {
        $firstIncomplete = $this->firstIncompleteStep($state);

        if ($step > $firstIncomplete) {
            return $firstIncomplete;
        }

        return $step;
    }

    private function firstIncompleteStep(array $state): int
    {
        for ($step = 1; $step < self::LAST_STEP; $step++) {
            if (!$this->isStepComplete($step, $state)) {
                return $step;
            }
        }

        return self::LAST_STEP;
    }

    private function completedSteps(array $state): array
    {
        $completed = [];

        for ($step = 1; $step < self::LAST_STEP; $step++) {
            if ($this->isStepComplete($step, $state)) {
                $completed[] = $step;
            }
        }

        return $completed;
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

    private function getState(Request $request): array
    {
        return $request->session()->get($this->sessionKey(), []);
    }

    private function putState(Request $request, array $state): void
    {
        $request->session()->put($this->sessionKey(), $state);
    }

    private function buildWizardContext(array $state): array
    {
        return [
            'source' => data_get($state, 'meta.source'),
            'incident_id' => data_get($state, 'meta.incident_id'),
            'incident_code' => data_get($state, 'meta.incident_code'),
            'incident_title' => data_get($state, 'meta.incident_title'),
            'incident_label' => data_get($state, 'meta.incident_label'),
            'system_name' => data_get($state, 'meta.system_name'),
            'processing_activity_name' => data_get($state, 'meta.processing_activity_name'),
            'preloaded_fields' => data_get($state, 'meta.preloaded_fields', []),
            'preload_warnings' => data_get($state, 'meta.preload_warnings', []),
            'regulatory_profile_missing' => (bool) data_get($state, 'meta.regulatory_profile_missing', false),
        ];
    }

    private function sessionKey(): string
    {
        return SanctionWizardDefinition::sessionKey();
    }

    private function stepView(int $step): string
    {
        return match ($step) {
            1 => 'risk.sanctions.wizard.step_1_general',
            2 => 'risk.sanctions.wizard.step_2_cdi',
            3 => 'risk.sanctions.wizard.step_3_pdi',
            4 => 'risk.sanctions.wizard.step_4_ndv',
            5 => 'risk.sanctions.wizard.step_5_int',
            6 => 'risk.sanctions.wizard.step_6_rer',
            7 => 'risk.sanctions.wizard.step_7_result',
        };
    }

    private function normalizeStep(int $step): int
    {
        return max(1, min(self::LAST_STEP, $step));
    }

    private function defaultSbuValue(): float
    {
        $value = SanctionCoefficient::query()
            ->where('rule_set', 'default')
            ->where('coefficient_key', 'sbu_default')
            ->value('value_numeric');

        return $value !== null ? (float) $value : 470.0;
    }

    private function buildStateFromIncident(Incident $incident): array
    {
        $profile = $incident->org?->regulatoryProfile;
        $general = [
            'case_name' => trim($incident->incident_code . ' - ' . $incident->title),
        ];
        $ndv = [];
        $preloadedFields = ['Nombre de referencia del caso'];
        $warnings = [];

        if ($profile?->entity_type && in_array($profile->entity_type, ['privada', 'publica'], true)) {
            $general['entity_type'] = $profile->entity_type;
            $preloadedFields[] = 'Tipo de entidad';

            if ($profile->entity_type === 'privada') {
                if ($profile->business_volume_usd !== null) {
                    $general['business_volume_usd'] = (float) $profile->business_volume_usd;
                    $preloadedFields[] = 'Volumen de negocio';
                } else {
                    $warnings[] = 'La organización no tiene volumen de negocio cargado. Debes completarlo manualmente en el paso 1.';
                }
            }

            if ($profile->entity_type === 'publica') {
                if ($profile->sbu_reference !== null) {
                    $general['sbu_reference'] = (float) $profile->sbu_reference;
                    $preloadedFields[] = 'SBU de referencia';
                } else {
                    $warnings[] = 'La organización no tiene SBU de referencia cargado. Debes completarlo manualmente en el paso 1.';
                }
            }
        } else {
            $warnings[] = 'No se encontró perfil regulatorio completo para la organización. Debes completar manualmente el tipo de entidad y la referencia económica.';
        }

        if ($incident->company_role && in_array($incident->company_role, ['responsable', 'encargado'], true)) {
            $general['company_role'] = $incident->company_role;
            $preloadedFields[] = 'Rol de la empresa';
        } else {
            $warnings[] = 'El incidente no tiene rol de la empresa definido. Debes completarlo manualmente en el paso 1.';
        }

        foreach ([
            'confidentiality_impact' => 'Confidencialidad',
            'integrity_impact' => 'Integridad',
            'availability_impact' => 'Disponibilidad',
        ] as $field => $label) {
            $mapped = $this->mapIncidentImpact((string) $incident->{$field});
            if ($mapped !== null) {
                $ndv[$field] = $mapped;
                $preloadedFields[] = $label;
            } elseif (!empty($incident->{$field})) {
                $warnings[] = 'El impacto de ' . Str::lower($label) . ' del incidente no coincide con el catálogo del asistente y debe revisarse manualmente.';
            }
        }

        if ($incident->data_subject_count !== null) {
            $ndv['data_subject_count'] = (int) $incident->data_subject_count;
            $preloadedFields[] = 'Cantidad de titulares afectados';
        }

        if ($incident->data_volume_amount !== null) {
            $ndv['data_volume_amount'] = (float) $incident->data_volume_amount;
            $preloadedFields[] = 'Volumen de datos afectados';
        }

        $mappedTypes = [];
        $unmappedTypes = [];
        foreach ((array) ($incident->affected_data_types ?? []) as $type) {
            $mapped = $this->mapIncidentDataType((string) $type);
            if ($mapped !== null) {
                $mappedTypes[] = $mapped;
                continue;
            }

            $unmappedTypes[] = $type;
        }

        $mappedTypes = array_values(array_unique($mappedTypes));
        if ($mappedTypes !== []) {
            $ndv['data_types'] = $mappedTypes;
            $preloadedFields[] = 'Tipos de datos afectados';
        }

        if ($unmappedTypes !== []) {
            $warnings[] = 'Algunos tipos de datos del incidente no pudieron mapearse automáticamente al asistente: ' . implode(', ', $unmappedTypes) . '.';
        }

        $ndv['vulnerable_groups'] = (bool) $incident->vulnerable_groups_flag;
        $preloadedFields[] = 'Grupos vulnerables';

        return [
            'general' => $general,
            'ndv' => $ndv,
            'meta' => [
                'current_step' => 1,
                'source' => 'incident',
                'incident_id' => $incident->incident_id,
                'incident_code' => $incident->incident_code,
                'incident_title' => $incident->title,
                'incident_label' => $incident->incident_code . ' - ' . $incident->title,
                'incident_org_id' => (int) $incident->org_id,
                'system_id' => $incident->system_id,
                'system_name' => $incident->system?->name,
                'pa_id' => $incident->pa_id,
                'processing_activity_name' => $incident->processingActivity?->name,
                'regulatory_profile_missing' => $profile === null,
                'preloaded_fields' => array_values(array_unique($preloadedFields)),
                'preload_warnings' => $warnings,
                'started_from_incident_at' => now()->toIso8601String(),
            ],
        ];
    }

    private function mapIncidentImpact(?string $value): ?string
    {
        if (!$value) {
            return null;
        }

        $normalized = $this->normalizeToken($value);
        $aliases = [
            'baja' => 'baja',
            'bajo' => 'baja',
            'media' => 'media',
            'medio' => 'media',
            'alta' => 'alta',
            'alto' => 'alta',
            'muy_alta' => 'muy_alta',
            'critica' => 'muy_alta',
            'critico' => 'muy_alta',
        ];

        $mapped = $aliases[$normalized] ?? null;
        $allowed = collect(SanctionWizardDefinition::impactLevels())->pluck('value')->all();

        return $mapped !== null && in_array($mapped, $allowed, true) ? $mapped : null;
    }

    private function mapIncidentDataType(?string $value): ?string
    {
        if (!$value) {
            return null;
        }

        $normalized = $this->normalizeToken($value);
        $aliases = [
            'identificativos' => 'identificativos',
            'identificativo' => 'identificativos',
            'identificacion' => 'identificativos',
            'identificacion_personal' => 'identificativos',
            'contacto' => 'contacto',
            'contactos' => 'contacto',
            'datos_de_contacto' => 'contacto',
            'financieros' => 'financieros',
            'financiero' => 'financieros',
            'sensibles' => 'sensibles',
            'sensible' => 'sensibles',
            'salud' => 'salud',
            'biometricos' => 'biometricos',
            'biometrico' => 'biometricos',
            'laborales' => 'laborales',
            'laboral' => 'laborales',
            'ubicacion' => 'ubicacion',
            'trazabilidad' => 'ubicacion',
        ];

        $mapped = $aliases[$normalized] ?? null;
        $allowed = collect(SanctionWizardDefinition::dataTypes())->pluck('value')->all();

        return $mapped !== null && in_array($mapped, $allowed, true) ? $mapped : null;
    }

    private function normalizeToken(string $value): string
    {
        return (string) Str::of($value)
            ->lower()
            ->ascii()
            ->replaceMatches('/[^a-z0-9]+/', '_')
            ->trim('_');
    }

    private function authorizeIncident(Incident $incident): void
    {
        $currentOrgId = $this->currentOrgId();

        if ($currentOrgId !== null && (int) $incident->org_id !== $currentOrgId) {
            throw new NotFoundHttpException();
        }
    }

    private function currentOrgId(): ?int
    {
        $orgId = session('org_id');

        return $orgId !== null ? (int) $orgId : null;
    }
}
