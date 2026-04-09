@php
    $rer = $wizardState['rer'] ?? [];
@endphp

<div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-4">
    <div>
        <h3 class="h5 mb-1">Paso 6. Reiteración y reincidencia</h3>
        <p class="text-muted mb-0">Este componente es opcional/condicional y se deja preparado para el servicio de cálculo.</p>
    </div>
    <span class="badge text-bg-light border">Paso {{ $currentStep }} de {{ count($steps) }}</span>
</div>

<form method="POST" action="{{ route('risk.ui.sanctions.wizard.store', ['step' => 6]) }}">
    @csrf

    <div class="border rounded p-4 bg-light">
        <div class="fw-semibold mb-2">¿Aplica reiteración o reincidencia al caso?</div>
        <div class="text-muted small mb-3">La selección se almacenará para activar o excluir el componente RER del cálculo determinista.</div>

        <div class="d-flex gap-4">
            @foreach ([1 => 'Sí, aplica', 0 => 'No aplica'] as $value => $label)
                <div class="form-check">
                    <input class="form-check-input @error('applies') is-invalid @enderror"
                           id="applies_{{ $value }}"
                           type="radio"
                           name="applies"
                           value="{{ $value }}"
                           {{ (string) old('applies', data_get($rer, 'applies')) === (string) $value ? 'checked' : '' }}>
                    <label class="form-check-label" for="applies_{{ $value }}">{{ $label }}</label>
                </div>
            @endforeach
        </div>

        @error('applies') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
    </div>

    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('risk.ui.sanctions.wizard.show', ['step' => 5]) }}" class="btn btn-outline-secondary">Anterior</a>
        <button type="submit" class="btn btn-primary">Ver resultado</button>
    </div>
</form>
