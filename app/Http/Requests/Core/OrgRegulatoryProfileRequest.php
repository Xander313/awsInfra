<?php

namespace App\Http\Requests\Core;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrgRegulatoryProfileRequest extends FormRequest
{
    private const MAX_REFERENCE_AMOUNT = 10000000000;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'entity_type' => $this->input('entity_type') !== null ? trim((string) $this->input('entity_type')) : null,
            'business_volume_usd' => $this->normalizeDecimal($this->input('business_volume_usd')),
            'sbu_reference' => $this->normalizeDecimal($this->input('sbu_reference')),
            'reference_year' => $this->input('reference_year') !== null && $this->input('reference_year') !== ''
                ? (int) $this->input('reference_year')
                : null,
            'notes' => $this->input('notes') !== null ? trim((string) $this->input('notes')) : null,
        ]);
    }

    public function rules(): array
    {
        $currentYear = (int) date('Y');

        return [
            'entity_type' => ['required', Rule::in(['privada', 'publica'])],
            'business_volume_usd' => ['nullable', 'numeric', 'gte:0', 'max:' . self::MAX_REFERENCE_AMOUNT],
            'sbu_reference' => ['nullable', 'numeric', 'gte:0'],
            'reference_year' => ['nullable', 'integer', 'digits:4', 'min:' . $currentYear, 'max:' . ($currentYear + 1)],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $entityType = $this->input('entity_type');

            if ($entityType === 'privada' && !$this->filled('business_volume_usd')) {
                $validator->errors()->add('business_volume_usd', 'El volumen de negocio USD es obligatorio para entidades privadas.');
            }

            if ($entityType === 'publica' && !$this->filled('sbu_reference')) {
                $validator->errors()->add('sbu_reference', 'La SBU de referencia es obligatoria para entidades públicas.');
            }
        });
    }

    public function attributes(): array
    {
        return [
            'entity_type' => 'tipo de entidad',
            'business_volume_usd' => 'volumen de negocio USD',
            'sbu_reference' => 'SBU de referencia',
            'reference_year' => 'año de referencia',
            'notes' => 'notas',
        ];
    }

    public function messages(): array
    {
        return [
            'business_volume_usd.gte' => 'El volumen de negocio USD no puede ser negativo.',
            'business_volume_usd.max' => 'El volumen de negocio USD no puede superar los 10,000,000,000.',
            'sbu_reference.gte' => 'La SBU de referencia no puede ser negativa.',
            'reference_year.min' => 'El año de referencia no puede ser menor al año actual.',
            'reference_year.max' => 'El año de referencia no puede ser mayor al siguiente año calendario.',
        ];
    }

    private function normalizeDecimal(mixed $value): mixed
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_string($value)) {
            $value = trim($value);

            if (str_contains($value, ',') && str_contains($value, '.')) {
                $value = str_replace(',', '', $value);
            } elseif (str_contains($value, ',')) {
                $value = str_replace(',', '.', $value);
            }
        }

        return $value;
    }
}
