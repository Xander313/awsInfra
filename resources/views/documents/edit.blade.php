@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            {{-- Título + volver --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-0">
                        <i class="bi bi-file-earmark-text"></i> Editar documento
                    </h2>
                    <small class="text-muted">Actualiza los datos básicos del documento</small>
                </div>
            </div>

            {{-- Card principal --}}
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <form id="form-edit-document"
                          action="{{ route('documents.update', $document->doc_id) }}"
                          method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Título --}}
                        <div class="mb-3">
                            <label class="form-label">Título *</label>
                            <input type="text"
                                   name="title"
                                   class="form-control @error('title') is-invalid @enderror"
                                   value="{{ old('title', $document->title) }}"
                                   placeholder="Ej. Política de privacidad interna"
                                   required>
                            @error('title')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Tipo --}}
                        <div class="mb-3">
                            <label class="form-label">Tipo</label>
                            <input type="text"
                                   name="doc_type"
                                   class="form-control @error('doc_type') is-invalid @enderror"
                                   value="{{ old('doc_type', $document->doc_type) }}"
                                   placeholder="Ej. Procedimiento, Política, Manual">
                            @error('doc_type')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Clasificación --}}
                        <div class="mb-3">
                            <label class="form-label">Clasificación</label>
                            <input type="text"
                                   name="classification"
                                   class="form-control @error('classification') is-invalid @enderror"
                                   value="{{ old('classification', $document->classification) }}"
                                   placeholder="Ej. Confidencial, Pública, Interna">
                            @error('classification')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Botones --}}
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('documents.index') }}"
                               class="btn btn-light border">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                 Guardar cambios
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function () {
    $('#form-edit-document').validate({
        rules: {
            title: {
                required: true,
                maxlength: 255
            },
            doc_type: {
                maxlength: 100
            },
            classification: {
                maxlength: 50
            }
        },
        messages: {
            title: {
                required: 'El título es obligatorio.',
                maxlength: 'Máximo 255 caracteres.'
            },
            doc_type: {
                maxlength: 'Máximo 100 caracteres.'
            },
            classification: {
                maxlength: 'Máximo 50 caracteres.'
            }
        },
        errorElement: 'span',
        errorClass: 'invalid-feedback',
        highlight: function (element) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function (element) {
            $(element).removeClass('is-invalid');
        },
        errorPlacement: function (error, element) {
            if (element.closest('.mb-3').length) {
                element.closest('.mb-3').append(error);
            } else {
                error.insertAfter(element);
            }
        }
    });
});
</script>
@endpush
