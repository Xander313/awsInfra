@php
    $int = $wizardState['int'] ?? [];
@endphp

<div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-4">
    <div>
        <h3 class="h5 mb-1">Paso 5. Intencionalidad</h3>
        <p class="text-muted mb-0">Selecciona el nivel de negligencia o intencionalidad que mejor describe la conducta.</p>
    </div>
    <span class="badge text-bg-light border">Paso {{ $currentStep }} de {{ count($steps) }}</span>
</div>

<form method="POST" action="{{ route('risk.ui.sanctions.wizard.store', ['step' => 5]) }}">
    @csrf

    <div class="vstack gap-3">
        @foreach ($pageData['intentionalityOptions'] as $option)
            <label class="border rounded p-3 d-block {{ old('level', $int['level'] ?? '') === $option['value'] ? 'border-primary bg-light shadow-sm' : '' }}">
                <div class="form-check">
                    <input class="form-check-input @error('level') is-invalid @enderror"
                           type="radio"
                           name="level"
                           value="{{ $option['value'] }}"
                           {{ old('level', $int['level'] ?? '') === $option['value'] ? 'checked' : '' }}>
                    <span class="form-check-label">
                        <span class="d-block fw-semibold">{{ $option['label'] }}</span>
                        <span class="d-block text-muted small">{{ $option['description'] }}</span>
                    </span>
                </div>
            </label>
        @endforeach
        @error('level') <div class="text-danger small">{{ $message }}</div> @enderror
    </div>

    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('risk.ui.sanctions.wizard.show', ['step' => 4]) }}" class="btn btn-outline-secondary">Anterior</a>
        <button type="submit" class="btn btn-primary">Siguiente</button>
    </div>
</form>
