<?php

namespace App\Http\Requests\Risk;

use App\Models\Core\Org;
use App\Models\Document\DocumentVersion;
use App\Models\Privacy\ProcessingActivity;
use App\Models\Privacyfase4\System;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IncidentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $types = $this->input('affected_data_types');

        if (!is_array($types)) {
            $text = (string) $this->input('affected_data_types_text', '');
            $items = preg_split('/[\r\n,;]+/', $text) ?: [];
            $types = collect($items)
                ->map(fn ($item) => trim((string) $item))
                ->filter()
                ->unique()
                ->values()
                ->all();
        }

        $this->merge([
            'affected_data_types' => $types,
            'vulnerable_groups_flag' => $this->boolean('vulnerable_groups_flag'),
            'document_version_ids' => collect((array) $this->input('document_version_ids', []))
                ->filter()
                ->unique()
                ->values()
                ->all(),
        ]);
    }

    public function rules(): array
    {
        return [
            'org_id' => ['required', 'integer', Rule::exists(Org::class, 'org_id')],
            'incident_code' => ['required', 'string', 'max:100'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'incident_type' => ['nullable', 'string', 'max:100'],
            'status' => ['required', 'string', 'max:50'],
            'severity' => ['nullable', 'string', 'max:50'],
            'company_role' => ['nullable', Rule::in(['responsable', 'encargado'])],
            'system_id' => ['nullable', 'integer', Rule::exists(System::class, 'system_id')],
            'pa_id' => ['nullable', 'integer', Rule::exists(ProcessingActivity::class, 'pa_id')],
            'occurred_at' => ['nullable', 'date'],
            'detected_at' => ['nullable', 'date', 'after_or_equal:occurred_at'],
            'closed_at' => ['nullable', 'date', 'after_or_equal:detected_at'],
            'data_subject_count' => ['nullable', 'integer', 'min:1'],
            'data_volume_amount' => ['nullable', 'numeric', 'min:0'],
            'affected_data_types' => ['nullable', 'array'],
            'affected_data_types.*' => ['required', 'string', 'max:120'],
            'confidentiality_impact' => ['nullable', 'string', 'max:50'],
            'integrity_impact' => ['nullable', 'string', 'max:50'],
            'availability_impact' => ['nullable', 'string', 'max:50'],
            'vulnerable_groups_flag' => ['required', 'boolean'],
            'document_version_ids' => ['nullable', 'array'],
            'document_version_ids.*' => ['integer', Rule::exists(DocumentVersion::class, 'doc_ver_id')],
            'document_relation_type' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function attributes(): array
    {
        return [
            'org_id' => 'organización',
            'incident_code' => 'código del incidente',
            'title' => 'título',
            'incident_type' => 'tipo de incidente',
            'status' => 'estado',
            'severity' => 'severidad',
            'company_role' => 'rol de la empresa',
            'system_id' => 'sistema',
            'pa_id' => 'actividad de tratamiento',
            'occurred_at' => 'fecha de ocurrencia',
            'detected_at' => 'fecha de detección',
            'closed_at' => 'fecha de cierre',
            'data_subject_count' => 'cantidad de titulares',
            'data_volume_amount' => 'volumen de datos',
            'affected_data_types' => 'tipos de datos afectados',
            'document_version_ids' => 'documentos asociados',
        ];
    }
}
