@php
    $ndv = $wizardState['ndv'] ?? [];
@endphp

<div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-4">
    <div>
        <h3 class="h5 mb-1">Paso 4. Naturaleza de la vulneración</h3>
        <p class="text-muted mb-0">Captura niveles de afectación y alcance para derivar TDP, TAV, NDV, TEV e IED.</p>
    </div>
    <span class="badge text-bg-light border">Paso {{ $currentStep }} de {{ count($steps) }}</span>
</div>

<form method="POST" action="{{ route('risk.ui.sanctions.wizard.store', ['step' => 4]) }}">
    @csrf

    <div class="col g-4">
        <div class="col-lg-12">
            <div class="border rounded p-3 h-100">
                <div class="fw-semibold mb-3">Afectaciones CIA</div>

                @foreach ([
                    'confidentiality_impact' => 'Afectación a confidencialidad',
                    'integrity_impact' => 'Afectación a integridad',
                    'availability_impact' => 'Afectación a disponibilidad',
                ] as $field => $label)
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ $label }}</label>
                        <select name="{{ $field }}" class="form-select @error($field) is-invalid @enderror">
                            <option value="">Selecciona un nivel</option>
                            @foreach ($pageData['impactLevels'] as $level)
                                <option value="{{ $level['value'] }}" {{ old($field, data_get($ndv, $field)) === $level['value'] ? 'selected' : '' }}>
                                    {{ $level['label'] }}: {{ $level['description'] }}
                                </option>
                            @endforeach
                        </select>
                        @error($field) <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>
                @endforeach

            </div>
        </div>
        <br>
        <div class="col-lg-12">
            <div class="border rounded p-3 h-100">
                <div class="fw-semibold mb-3">Alcance de los datos</div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Tipos de datos personales afectados</label>
                    <div class="row g-2">
                        @foreach ($pageData['dataTypes'] as $type)
                            <div class="col-md-6">
                                <label class="border rounded p-2 d-block">
                                    <div class="form-check">
                                        <input class="form-check-input"
                                               type="checkbox"
                                               name="data_types[]"
                                               value="{{ $type['value'] }}"
                                               {{ in_array($type['value'], old('data_types', $ndv['data_types'] ?? []), true) ? 'checked' : '' }}>
                                        <span class="form-check-label">
                                            <span class="d-block fw-semibold">{{ $type['label'] }}</span>
                                            <span class="d-block text-muted small">{{ $type['description'] }}</span>
                                        </span>
                                    </div>
                                </label>
                            </div>
                        @endforeach
                    </div>
                    @error('data_types') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    @error('data_types.*') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Número de titulares afectados</label>
                    <input type="number"
                           min="1"
                           name="data_subject_count"
                           class="form-control @error('data_subject_count') is-invalid @enderror"
                           value="{{ old('data_subject_count', $ndv['data_subject_count'] ?? '') }}">
                    @error('data_subject_count') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Volumen de datos personales afectados (ej. número de registros, gigabytes)</label>
                    <input type="number"
                           name="data_volume_amount"
                           inputmode="decimal"
                           class="form-control @error('data_volume_amount') is-invalid @enderror"
                           value="{{ old('data_volume_amount', $ndv['data_volume_amount'] ?? '') }}"
                           placeholder="Ej. 1200">

                    @error('data_volume_amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-0">
                    <label class="form-label fw-semibold">¿Hay grupos especialmente vulnerables?</label>
                    <div class="d-flex gap-4">
                        @foreach ([1 => 'Sí', 0 => 'No'] as $value => $label)
                            <div class="form-check">
                                <input class="form-check-input @error('vulnerable_groups') is-invalid @enderror"
                                       type="radio"
                                       name="vulnerable_groups"
                                       value="{{ $value }}"
                                       {{ (string) old('vulnerable_groups', data_get($ndv, 'vulnerable_groups')) === (string) $value ? 'checked' : '' }}>
                                <label class="form-check-label">{{ $label }}</label>
                            </div>
                        @endforeach
                    </div>
                    @error('vulnerable_groups') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('risk.ui.sanctions.wizard.show', ['step' => 3]) }}" class="btn btn-outline-secondary">Anterior</a>
        <button type="submit" class="btn btn-primary">Siguiente</button>
    </div>
</form>
