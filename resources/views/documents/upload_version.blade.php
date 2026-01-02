@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            {{-- Título + volver --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-0">
                        <i class="bi bi-file-earmark-arrow-up"></i> Nueva versión
                    </h2>
                    <small class="text-muted">
                        Documento: <span class="fw-semibold">{{ $document->title }}</span>
                    </small>
                </div>
            </div>

            {{-- Card principal --}}
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white border-0 rounded-top-4 px-4 py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">
                            <i class="bi bi-upload text-primary me-2"></i>
                            Subir nueva versión
                        </h5>
                        <small class="text-muted">
                            Selecciona el archivo que reemplazará a la versión actual
                        </small>
                    </div>

                    @if($document->activeVersion)
                        <span class="badge bg-success-subtle text-success-emphasis border">
                            Versión activa: v{{ $document->activeVersion->version_no }}
                        </span>
                    @endif
                </div>

                <div class="card-body px-4 pb-4 pt-3">
                    <form id="form-new-version"
                          action="{{ route('documents.versions.store', $document->doc_id) }}"
                          method="POST" enctype="multipart/form-data">
                        @csrf

                        {{-- Archivo --}}
                        <div class="mb-3">
                            <label class="form-label">Archivo *</label>
                            <input type="file"
                                   name="file"
                                   class="form-control @error('file') is-invalid @enderror"
                                   required>

                            <small class="text-muted d-block mt-1">
                                Tamaño máximo 10MB. Sube el documento en su formato oficial (PDF, DOCX, etc.).
                            </small>

                            @error('file')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Botones --}}
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('documents.show', $document->doc_id) }}"
                               class="btn btn-light border rounded-pill px-4">
                                Cancelar
                            </a>

                            <button type="submit"
                                    class="btn btn-success rounded-pill px-4">
                                <i class="bi bi-cloud-arrow-up me-1"></i>
                                Subir versión
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
<script>
$(function () {

    // Regla personalizada para tamaño máximo (10MB)
    $.validator.addMethod("filesize", function (value, element, param) {
        return this.optional(element) || (element.files[0].size <= param);
    }, "El archivo supera el tamaño permitido.");

    $('#form-new-version').validate({
        rules: {
            file: {
                required: true,
                extension: "pdf|doc|docx|xls|xlsx|ppt|pptx",
                filesize: 10485760 // 10MB
            }
        },
        messages: {
            file: {
                required: "Debe seleccionar un archivo.",
                extension: "Formato no válido. Solo se permiten: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX.",
                filesize: "El archivo debe ser máximo de 10 MB."
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

