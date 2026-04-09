@php
    $general = $wizardState['general'] ?? [];
    $entityType = old('entity_type', $general['entity_type'] ?? '');
    $defaultSbu = $pageData['defaultSbu'] ?? 470;
@endphp

<div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-4">
    <div>
        <h3 class="h5 mb-1">Paso 1. Datos generales</h3>
        <p class="text-muted mb-0">Define el tipo de entidad, el rol y la referencia económica que luego usará el cálculo.</p>
    </div>
    <span class="badge text-bg-light border">Paso {{ $currentStep }} de {{ count($steps) }}</span>
</div>

<form method="POST" action="{{ route('risk.ui.sanctions.wizard.store', ['step' => 1]) }}" x-data="{ entityType: '{{ $entityType }}' }">
    @csrf

    <div class="row g-4">
        <div class="col-12">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Tipo de entidad</label>
                    <select name="entity_type" x-model="entityType" class="form-select @error('entity_type') is-invalid @enderror" required>
                        <option value="">Selecciona una opción</option>
                        <option value="privada" {{ $entityType === 'privada' ? 'selected' : '' }}>Privada</option>
                        <option value="publica" {{ $entityType === 'publica' ? 'selected' : '' }}>Pública</option>
                    </select>
                    @error('entity_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Rol de la empresa</label>
                    <select name="company_role" class="form-select @error('company_role') is-invalid @enderror" required>
                        <option value="">Selecciona una opción</option>
                        <option value="responsable" {{ old('company_role', $general['company_role'] ?? '') === 'responsable' ? 'selected' : '' }}>Responsable</option>
                        <option value="encargado" {{ old('company_role', $general['company_role'] ?? '') === 'encargado' ? 'selected' : '' }}>Encargado</option>
                    </select>
                    @error('company_role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Nombre de referencia del caso</label>
                    <input type="text"
                           name="case_name"
                           class="form-control @error('case_name') is-invalid @enderror"
                           value="{{ old('case_name', $general['case_name'] ?? '') }}"
                           placeholder="Ejemplo: Revisión abril 2026 - cliente privado">
                    @error('case_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6" x-show="entityType === 'privada'">
                    <label class="form-label fw-semibold">Volumen de negocio USD</label>
                    <input type="text"
                           name="business_volume_usd"
                           inputmode="decimal"
                           x-bind:disabled="entityType !== 'privada'"
                           class="form-control @error('business_volume_usd') is-invalid @enderror"
                           value="{{ old('business_volume_usd', $general['business_volume_usd'] ?? '') }}"
                           placeholder="Ej. 50000 o 50,000">
                    @error('business_volume_usd') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6" x-show="entityType === 'publica'">
                    <label class="form-label fw-semibold">SBU de referencia</label>
                    <input type="text"
                           name="sbu_reference"
                           inputmode="decimal"
                           x-bind:disabled="entityType !== 'publica'"
                           class="form-control @error('sbu_reference') is-invalid @enderror"
                           value="{{ old('sbu_reference', $general['sbu_reference'] ?? $defaultSbu) }}"
                           placeholder="Ej. 470">
                    <div class="form-text">Precargado desde el coeficiente <code>sbu_default</code>. También acepta separadores humanos.</div>
                    @error('sbu_reference') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>


    </div>

    <div class="d-flex justify-content-end mt-4">
        <button type="submit" class="btn btn-primary">
            Siguiente
        </button>
    </div>
</form>
