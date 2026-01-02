@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white border-0 pt-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="bi bi-clipboard-check"></i> Registrar Consentimiento
                        </h4>
                        <a href="{{ route('data-subjects.show', $dataSubject) }}" 
                           class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Cancelar
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="alert alert-info mb-4">
                        <i class="bi bi-info-circle"></i>
                        <strong>Titular:</strong> {{ $dataSubject->full_name }}
                        <br>
                        <strong>Identificación:</strong> {{ $dataSubject->id_type }}: {{ $dataSubject->id_number }}
                    </div>

                    <form action="{{ route('data-subjects.consent.store', $dataSubject) }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="notice_ver_id" class="form-label">
                                ID de Aviso de Privacidad (opcional)
                            </label>
                            <input type="number" 
                                   name="notice_ver_id" 
                                   id="notice_ver_id"
                                   class="form-control"
                                   placeholder="Ej: 123"
                                   value="{{ old('notice_ver_id') }}">
                            <small class="text-muted">Referencia al documento de aviso de privacidad</small>
                            @error('notice_ver_id')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="purpose_id" class="form-label">
                                ID de Propósito (opcional)
                            </label>
                            <input type="number" 
                                   name="purpose_id" 
                                   id="purpose_id"
                                   class="form-control"
                                   placeholder="Ej: 456"
                                   value="{{ old('purpose_id') }}">
                            <small class="text-muted">Referencia al propósito específico del consentimiento</small>
                            @error('purpose_id')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="given_at" class="form-label">
                                Fecha y Hora de Otorgamiento
                            </label>
                            <input type="datetime-local" 
                                   name="given_at" 
                                   id="given_at"
                                   class="form-control"
                                   value="{{ old('given_at', date('Y-m-d\TH:i')) }}">
                            <small class="text-muted">Si se deja vacío, se usará la fecha y hora actual</small>
                            @error('given_at')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="confirm_consent" required>
                                <label class="form-check-label" for="confirm_consent">
                                    Confirmo que el titular ha otorgado su consentimiento de manera libre, específica, informada e inequívoca.
                                </label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between pt-3 border-top">
                            <a href="{{ route('data-subjects.show', $dataSubject) }}" 
                               class="btn btn-outline-secondary">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Registrar Consentimiento
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Set default datetime to now
    document.addEventListener('DOMContentLoaded', function() {
        if (!document.getElementById('given_at').value) {
            const now = new Date();
            const localDateTime = now.toISOString().slice(0, 16);
            document.getElementById('given_at').value = localDateTime;
        }
    });
</script>
@endpush
@endsection