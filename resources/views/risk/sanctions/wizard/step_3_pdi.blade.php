@php
    $answers = data_get($wizardState, 'pdi.answers', []);
@endphp

<div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-4">
    <div>
        <h3 class="h5 mb-1">Paso 3. Peso de la infracción</h3>
        <p class="text-muted mb-0">Registra el cuestionario de cumplimiento organizacional para el score PDI.</p>
    </div>
    <span class="badge text-bg-light border">Paso {{ $currentStep }} de {{ count($steps) }}</span>
</div>

<form method="POST" action="{{ route('risk.ui.sanctions.wizard.store', ['step' => 3]) }}">
    @csrf
<!--
    <div class="alert alert-light border mb-4">
        <div class="small">
            El PDI se deriva del total de respuestas negativas del cuestionario y luego se suaviza con el coeficiente <code>pert_weight_most_probable</code>.
            En esta etapa se capturan los 10 insumos requeridos por el modelo.
        </div>
    </div>
-->
    <div class="vstack gap-3">
        @foreach ($pageData['pdiQuestions'] as $question)
            <div class="border rounded p-3">
                <div class="d-flex flex-column gap-2">
                    <span class="badge text-bg-light border align-self-start">
                        Pregunta {{ $loop->iteration }}/{{ count($pageData['pdiQuestions']) }}
                    </span>

                    <div class="fw-semibold mb-2">
                        {{ $loop->iteration }}. {{ $question['label'] }}
                    </div>
                </div>
                <div class="text-muted small mb-3">{{ $question['help'] }}</div>
                <div class="d-flex gap-4">
                    @foreach ([1 => 'Sí', 0 => 'No'] as $value => $label)
                        <div class="form-check">
                            <input class="form-check-input @error('answers.' . $question['key']) is-invalid @enderror"
                                   type="radio"
                                   name="answers[{{ $question['key'] }}]"
                                   id="{{ $question['key'] }}_{{ $value }}"
                                   value="{{ $value }}"
                                   {{ (string) old('answers.' . $question['key'], data_get($answers, $question['key'])) === (string) $value ? 'checked' : '' }}>
                            <label class="form-check-label" for="{{ $question['key'] }}_{{ $value }}">{{ $label }}</label>
                        </div>
                    @endforeach
                </div>
                @error('answers.' . $question['key']) <div class="text-danger small mt-2">{{ $message }}</div> @enderror
            </div>
        @endforeach
    </div>

    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('risk.ui.sanctions.wizard.show', ['step' => 2]) }}" class="btn btn-outline-secondary">Anterior</a>
        <button type="submit" class="btn btn-primary">Siguiente</button>
    </div>
</form>
