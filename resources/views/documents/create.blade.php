@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="mb-0">
                    <i class="bi bi-file-earmark-plus"></i> Nuevo Documento
                </h2>
            </div>

            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">

                    <p class="text-muted mb-4">
                        Los campos marcados con <span class="text-danger">*</span> son obligatorios.
                    </p>

                    <form id="form-document" action="{{ route('documents.store') }}" method="POST"
                          enctype="multipart/form-data" novalidate>
                        @csrf

                        {{-- Organización --}}
                        <div class="mb-3">
                            <label class="form-label">Organización <span class="text-danger">*</span></label>
                            <select name="org_id" class="form-select">
                                <option value="">Seleccione una organización</option>
                                @foreach($orgs as $org)
                                    <option value="{{ $org->org_id }}"
                                        {{ old('org_id', session('org_id')) == $org->org_id ? 'selected' : '' }}>
                                        {{ $org->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('org_id') <small class="text-danger d-block">{{ $message }}</small> @enderror
                        </div>

                        {{-- Título --}}
                        <div class="mb-3">
                            <label class="form-label">Título <span class="text-danger">*</span></label>
                            <input type="text"
                                   name="title"
                                   class="form-control"
                                   value="{{ old('title') }}">
                            @error('title') <small class="text-danger d-block">{{ $message }}</small> @enderror
                        </div>

                        {{-- Tipo y Clasificación en dos columnas --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tipo</label>
                                <input type="text"
                                       name="doc_type"
                                       class="form-control"
                                       value="{{ old('doc_type') }}">
                                @error('doc_type') <small class="text-danger d-block">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Clasificación</label>
                                <input type="text"
                                       name="classification"
                                       class="form-control"
                                       value="{{ old('classification') }}">
                                @error('classification') <small class="text-danger d-block">{{ $message }}</small> @enderror
                            </div>
                        </div>

                        {{-- Archivo --}}
                        <div class="mb-3">
                            <label class="form-label">Archivo (primera versión) <span class="text-danger">*</span></label>
                            <input type="file" name="file" class="form-control">
                            <small class="text-muted">
                                Formatos sugeridos: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX. Tamaño máximo 10 MB.
                            </small>
                            @error('file') <small class="text-danger d-block">{{ $message }}</small> @enderror
                        </div>

                        {{-- Botones --}}
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('documents.index') }}" class="btn btn-outline-secondary">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                Guardar documento
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
        $('#form-document').validate({
            rules: {
                org_id: {
                    required: true
                },
                title: {
                    required: true,
                    minlength: 3,
                    maxlength: 255
                },
                doc_type: {
                    maxlength: 100
                },
                classification: {
                    maxlength: 50
                },
                file: {
                    required: true,
                    extension: "pdf|doc|docx|xls|xlsx|ppt|pptx"
                }
            },
            messages: {
                org_id: {
                    required: "Seleccione una organización."
                },
                title: {
                    required: "El título es obligatorio.",
                    minlength: "El título debe tener al menos 3 caracteres.",
                    maxlength: "El título no puede superar los 255 caracteres."
                },
                doc_type: {
                    maxlength: "El tipo no puede superar los 100 caracteres."
                },
                classification: {
                    maxlength: "La clasificación no puede superar los 50 caracteres."
                },
                file: {
                    required: "Debe adjuntar un archivo.",
                    extension: "Formato no permitido. Use pdf, doc, docx, xls, xlsx, ppt o pptx."
                }
            },
            errorElement: 'small',
            errorClass: 'text-danger',
            highlight: function (element) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function (element) {
                $(element).removeClass('is-invalid');
            },
            errorPlacement: function (error, element) {
                if (element.parent('.input-group').length) {
                    error.insertAfter(element.parent());
                } else {
                    error.insertAfter(element);
                }
            }
        });
    });
</script>
@endpush
