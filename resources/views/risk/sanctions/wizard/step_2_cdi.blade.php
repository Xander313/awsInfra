@php
    $cdi = $wizardState['cdi'] ?? [];
    $role = $pageData['role'] ?? null;
    $catalog = $pageData['cdiCatalog'] ?? [];
@endphp

<div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-4">
    <div>
        <h3 class="h5 mb-1">Paso 2. Categoría de infracción</h3>
        <p class="text-muted mb-0">Selecciona la infracción aplicable según el rol elegido en el paso anterior.</p>
    </div>
    <span class="badge text-bg-light border">Paso {{ $currentStep }} de {{ count($steps) }}</span>
</div>

<form method="POST" action="{{ route('risk.ui.sanctions.wizard.store', ['step' => 2]) }}">
    @csrf

    <div class="alert alert-light border">
        <div class="small">
            Rol activo para este caso: <span class="fw-semibold text-capitalize">{{ $role ?? 'no definido' }}</span>.
            Si necesitas cambiarlo, vuelve al paso 1.
        </div>
    </div>

    @error('severity') <div class="alert alert-danger py-2">{{ $message }}</div> @enderror
    @error('infraction_code') <div class="alert alert-danger py-2">{{ $message }}</div> @enderror

    <input type="hidden" name="severity" id="selectedSeverity" value="{{ old('severity', $cdi['severity'] ?? '') }}">

    @if (empty($catalog))
        <div class="alert alert-warning">
            No hay catálogo disponible para el rol seleccionado. Vuelve al paso 1 y verifica la configuración del caso.
        </div>
    @endif

    <div class="row g-4">
        @foreach ($catalog as $severity => $items)
            <div class="col-lg-6">
                <div class="border rounded h-100">
                    <div class="p-3 border-bottom {{ $severity === 'grave' ? 'bg-red-50' : 'bg-yellow-50' }}">
                        <div class="fw-semibold">{{ $severity === 'grave' ? 'Infracciones graves' : 'Infracciones leves' }}</div>
                        <div class="text-muted small">Catálogo aplicable al rol {{ $role }}.</div>
                    </div>
                    <div class="p-3">
                        <div class="vstack gap-3">
                            @foreach ($items as $item)
                                @php
                                    $checked = old('infraction_code', $cdi['code'] ?? '') === $item['code'];
                                @endphp
                                <label class="border rounded p-3 d-block {{ $checked ? 'border-primary bg-light shadow-sm' : '' }}">
                                    <div class="form-check">
                                        <input class="form-check-input"
                                               type="radio"
                                               name="infraction_code"
                                               value="{{ $item['code'] }}"
                                               data-severity="{{ $severity }}"
                                               {{ $checked ? 'checked' : '' }}
                                               onclick="document.getElementById('selectedSeverity').value = this.dataset.severity">
                                        <span class="form-check-label">
                                            <span class="d-block fw-semibold">{{ $item['title'] }}</span>
                                            <span class="d-block text-muted small">{{ $item['description'] }}</span>
                                        </span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('risk.ui.sanctions.wizard.show', ['step' => 1]) }}" class="btn btn-outline-secondary">Anterior</a>
        <button type="submit" class="btn btn-primary" {{ empty($catalog) ? 'disabled' : '' }}>Siguiente</button>
    </div>
</form>
