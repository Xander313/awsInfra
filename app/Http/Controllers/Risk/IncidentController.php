<?php

namespace App\Http\Controllers\Risk;

use App\Http\Controllers\Controller;
use App\Http\Requests\Risk\IncidentRequest;
use App\Models\Core\Org;
use App\Models\Document\DocumentVersion;
use App\Models\Risk\Incident;
use App\Models\Risk\SanctionSimulation;
use App\Models\Privacy\ProcessingActivity;
use App\Models\Privacyfase4\System;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class IncidentController extends Controller
{
    public function index(): View
    {
        $currentOrgId = $this->currentOrgId();

        $query = Incident::query()
            ->with(['org', 'system', 'processingActivity'])
            ->orderByDesc('detected_at')
            ->orderByDesc('created_at');

        if ($currentOrgId !== null) {
            $query->where('org_id', $currentOrgId);
        }

        return view('risk.incidents.index', [
            'incidents' => $query->paginate(15),
            'currentOrgId' => $currentOrgId,
        ]);
    }

    public function create(): View
    {
        $formData = $this->buildFormData();

        return view('risk.incidents.create', $formData + [
            'incident' => new Incident([
                'org_id' => $formData['selectedOrgId'],
                'status' => 'abierto',
                'vulnerable_groups_flag' => false,
            ]),
            'selectedDocumentVersionIds' => [],
        ]);
    }

    public function store(IncidentRequest $request): RedirectResponse
    {
        $orgId = $this->resolveOrgId((int) $request->integer('org_id'));
        $data = $request->validated();
        $data['org_id'] = $orgId;

        $this->ensureUniqueIncidentCode($orgId, $data['incident_code']);
        $this->assertRelationsBelongToOrg(
            $orgId,
            $data['system_id'] ?? null,
            $data['pa_id'] ?? null,
            $data['document_version_ids'] ?? []
        );

        $incident = Incident::create($this->incidentPayload($data) + [
            'created_by_user_id' => optional(auth()->user())->user_id,
        ]);
        $this->syncDocuments(
            $incident,
            $data['document_version_ids'] ?? [],
            $data['document_relation_type'] ?? 'evidence'
        );

        return redirect()
            ->route('risk.ui.incidents.show', $incident)
            ->with('success', 'Incidente creado correctamente.');
    }

    public function show(Incident $incident): View
    {
        $this->authorizeIncident($incident);

        $incident->load([
            'org.regulatoryProfile.updatedBy',
            'officialSimulation',
            'system',
            'processingActivity',
            'creator',
            'incidentDocuments.documentVersion.document.org',
        ]);
        $sanctionSimulations = $incident->sanctionSimulations()
            ->orderByDesc('created_at')
            ->get();
        $latestSimulation = $sanctionSimulations->first();
        $officialSimulation = $incident->officialSimulation;

        return view('risk.incidents.show', [
            'incident' => $incident,
            'hasRegulatoryProfile' => $incident->org?->regulatoryProfile !== null,
            'sanctionSimulations' => $sanctionSimulations,
            'latestSimulation' => $latestSimulation,
            'officialSimulation' => $officialSimulation,
            'simulationSummary' => [
                'count' => $sanctionSimulations->count(),
                'latest_at' => $latestSimulation?->created_at,
                'latest_fine_usd' => $latestSimulation?->deterministic_fine_usd,
                'official_at' => $officialSimulation?->created_at,
                'official_fine_usd' => $officialSimulation?->deterministic_fine_usd,
            ],
        ]);
    }

    public function markOfficialSimulation(Incident $incident, SanctionSimulation $simulation): RedirectResponse
    {
        $this->authorizeIncident($incident);

        if ((int) $simulation->incident_id !== (int) $incident->incident_id) {
            abort(404);
        }

        $incident->update([
            'official_simulation_id' => $simulation->simulation_id,
        ]);

        return redirect()
            ->route('risk.ui.incidents.show', $incident)
            ->with('success', 'La simulación #' . $simulation->simulation_id . ' quedó marcada como referencia oficial del expediente.');
    }

    public function edit(Incident $incident): View
    {
        $this->authorizeIncident($incident);
        $incident->load('documentVersions');

        return view('risk.incidents.edit', $this->buildFormData((int) $incident->org_id) + [
            'incident' => $incident,
            'selectedDocumentVersionIds' => $incident->documentVersions->pluck('doc_ver_id')->all(),
        ]);
    }

    public function update(IncidentRequest $request, Incident $incident): RedirectResponse
    {
        $this->authorizeIncident($incident);

        $orgId = $this->resolveOrgId((int) $request->integer('org_id'));
        $data = $request->validated();
        $data['org_id'] = $orgId;

        $this->ensureUniqueIncidentCode($orgId, $data['incident_code'], $incident->incident_id);
        $this->assertRelationsBelongToOrg(
            $orgId,
            $data['system_id'] ?? null,
            $data['pa_id'] ?? null,
            $data['document_version_ids'] ?? []
        );

        $incident->update($this->incidentPayload($data));
        $this->syncDocuments(
            $incident,
            $data['document_version_ids'] ?? [],
            $data['document_relation_type'] ?? 'evidence'
        );

        return redirect()
            ->route('risk.ui.incidents.show', $incident)
            ->with('success', 'Incidente actualizado correctamente.');
    }

    private function buildFormData(?int $selectedOrgId = null): array
    {
        $currentOrgId = $this->currentOrgId();
        $resolvedOrgId = $selectedOrgId ?? $currentOrgId;
        $orgs = $this->availableOrgs();

        $systems = System::query()
            ->with('organization')
            ->when($currentOrgId !== null, fn ($query) => $query->where('org_id', $currentOrgId))
            ->orderBy('name')
            ->get();

        $processingActivities = ProcessingActivity::query()
            ->when($currentOrgId !== null, fn ($query) => $query->where('org_id', $currentOrgId))
            ->orderBy('name')
            ->get();

        $documentVersions = DocumentVersion::query()
            ->with(['document.org'])
            ->where('active_flag', true)
            ->whereHas('document', function ($query) use ($currentOrgId) {
                if ($currentOrgId !== null) {
                    $query->where('org_id', $currentOrgId);
                }
            })
            ->orderByDesc('doc_ver_id')
            ->get();

        return [
            'currentOrgId' => $currentOrgId,
            'selectedOrgId' => $resolvedOrgId,
            'orgs' => $orgs,
            'systems' => $systems,
            'processingActivities' => $processingActivities,
            'documentVersions' => $documentVersions,
            'statusOptions' => $this->statusOptions(),
            'severityOptions' => $this->severityOptions(),
            'companyRoleOptions' => $this->companyRoleOptions(),
            'impactOptions' => $this->impactOptions(),
            'documentRelationTypeOptions' => $this->documentRelationTypeOptions(),
            'orgProfileAvailability' => $orgs
                ->mapWithKeys(fn (Org $org) => [$org->org_id => $org->relationLoaded('regulatoryProfile')
                    ? $org->regulatoryProfile !== null
                    : $org->regulatoryProfile()->exists()])
                ->all(),
        ];
    }

    private function availableOrgs(): Collection
    {
        $currentOrgId = $this->currentOrgId();

        return Org::query()
            ->with('regulatoryProfile')
            ->when($currentOrgId !== null, fn ($query) => $query->where('org_id', $currentOrgId))
            ->orderBy('name')
            ->get();
    }

    private function resolveOrgId(int $requestedOrgId): int
    {
        return $this->currentOrgId() ?? $requestedOrgId;
    }

    private function currentOrgId(): ?int
    {
        $orgId = session('org_id');

        return $orgId !== null ? (int) $orgId : null;
    }

    private function authorizeIncident(Incident $incident): void
    {
        $currentOrgId = $this->currentOrgId();

        if ($currentOrgId !== null && (int) $incident->org_id !== $currentOrgId) {
            abort(404);
        }
    }

    private function incidentPayload(array $data): array
    {
        return [
            'org_id' => $data['org_id'],
            'incident_code' => $data['incident_code'],
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'incident_type' => $data['incident_type'] ?? null,
            'status' => $data['status'],
            'severity' => $data['severity'] ?? null,
            'company_role' => $data['company_role'] ?? null,
            'system_id' => $data['system_id'] ?? null,
            'pa_id' => $data['pa_id'] ?? null,
            'occurred_at' => $data['occurred_at'] ?? null,
            'detected_at' => $data['detected_at'] ?? null,
            'closed_at' => $data['closed_at'] ?? null,
            'data_subject_count' => $data['data_subject_count'] ?? null,
            'data_volume_amount' => $data['data_volume_amount'] ?? null,
            'affected_data_types' => $data['affected_data_types'] ?? [],
            'confidentiality_impact' => $data['confidentiality_impact'] ?? null,
            'integrity_impact' => $data['integrity_impact'] ?? null,
            'availability_impact' => $data['availability_impact'] ?? null,
            'vulnerable_groups_flag' => (bool) ($data['vulnerable_groups_flag'] ?? false),
        ];
    }

    private function assertRelationsBelongToOrg(int $orgId, mixed $systemId, mixed $paId, array $documentVersionIds): void
    {
        if ($systemId !== null && !System::query()->where('system_id', $systemId)->where('org_id', $orgId)->exists()) {
            throw ValidationException::withMessages([
                'system_id' => 'El sistema seleccionado no pertenece a la organización del incidente.',
            ]);
        }

        if ($paId !== null && !ProcessingActivity::query()->where('pa_id', $paId)->where('org_id', $orgId)->exists()) {
            throw ValidationException::withMessages([
                'pa_id' => 'La actividad de tratamiento seleccionada no pertenece a la organización del incidente.',
            ]);
        }

        if ($documentVersionIds === []) {
            return;
        }

        $allowedCount = DocumentVersion::query()
            ->whereIn('doc_ver_id', $documentVersionIds)
            ->whereHas('document', fn ($query) => $query->where('org_id', $orgId))
            ->count();

        if ($allowedCount !== count($documentVersionIds)) {
            throw ValidationException::withMessages([
                'document_version_ids' => 'Uno o más documentos seleccionados no pertenecen a la organización del incidente.',
            ]);
        }
    }

    private function syncDocuments(Incident $incident, array $documentVersionIds, string $relationType): void
    {
        $payload = collect($documentVersionIds)
            ->mapWithKeys(fn ($docVerId) => [
                (int) $docVerId => [
                    'relation_type' => $relationType ?: 'evidence',
                    'description' => null,
                    'attached_at' => now(),
                ],
            ])
            ->all();

        $incident->documentVersions()->sync($payload);
    }

    private function ensureUniqueIncidentCode(int $orgId, string $incidentCode, ?int $ignoreIncidentId = null): void
    {
        $query = Incident::query()
            ->where('org_id', $orgId)
            ->where('incident_code', $incidentCode);

        if ($ignoreIncidentId !== null) {
            $query->where('incident_id', '!=', $ignoreIncidentId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'incident_code' => 'Ya existe un incidente con ese código dentro de la organización seleccionada.',
            ]);
        }
    }

    private function statusOptions(): array
    {
        return [
            'abierto' => 'Abierto',
            'en_analisis' => 'En análisis',
            'contenido' => 'Contenido',
            'cerrado' => 'Cerrado',
        ];
    }

    private function severityOptions(): array
    {
        return [
            'baja' => 'Baja',
            'media' => 'Media',
            'alta' => 'Alta',
            'critica' => 'Crítica',
        ];
    }

    private function companyRoleOptions(): array
    {
        return [
            'responsable' => 'Responsable',
            'encargado' => 'Encargado',
        ];
    }

    private function impactOptions(): array
    {
        return [
            'bajo' => 'Bajo',
            'medio' => 'Medio',
            'alto' => 'Alto',
            'critico' => 'Crítico',
        ];
    }

    private function documentRelationTypeOptions(): array
    {
        return [
            'evidence' => 'Evidencia',
            'report' => 'Reporte',
            'support' => 'Soporte',
        ];
    }
}
