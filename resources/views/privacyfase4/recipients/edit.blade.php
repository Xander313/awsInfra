@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-center align-items-center" style="min-height: 90vh;">
    <div class="card shadow-lg w-100" style="max-width: 700px;">
        <div class="card-header bg-warning text-dark text-center">
            <h4 class="mb-0">Editar Destinatario</h4>
        </div>

        <div class="card-body">
            <form id="formRecipientEdit"
                  action="{{ route('recipients.update', $recipient->recipient_id) }}"
                  method="POST" novalidate>
                @csrf
                @method('PUT')

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label"><b>Organización</b></label>
                        <select name="org_id" class="form-select" required>
                            <option value="">-- Seleccione una organización --</option>
                            @foreach ($orgs as $org)
                                <option value="{{ $org->org_id }}"
                                    {{ old('org_id', $recipient->org_id) == $org->org_id ? 'selected' : '' }}>
                                    {{ $org->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label"><b>Tipo de Destinatario</b></label>
                        <input type="text"
                               name="recipient_type"
                               class="form-control"
                               value="{{ old('recipient_type', $recipient->recipient_type) }}"
                               required
                               minlength="3"
                               maxlength="100">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label"><b>Nombre del Destinatario</b></label>
                        <input type="text"
                               name="name"
                               class="form-control"
                               value="{{ old('name', $recipient->name) }}"
                               required
                               minlength="3"
                               maxlength="150">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label"><b>Correo de Contacto</b></label>
                        <input type="email"
                               name="contact_email"
                               class="form-control"
                               value="{{ old('contact_email', $recipient->contact_email) }}"
                               placeholder="correo@ejemplo.com"
                               required
                               maxlength="150">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label"><b>¿Es Tercero?</b></label>
                        <select name="is_third_party" class="form-select" required>
                            <option value="">-- Seleccione --</option>
                            <option value="0" {{ old('is_third_party', $recipient->is_third_party) == '0' ? 'selected' : '' }}>No</option>
                            <option value="1" {{ old('is_third_party', $recipient->is_third_party) == '1' ? 'selected' : '' }}>Sí</option>
                        </select>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-warning">Actualizar</button>
                    <a href="{{ route('recipients.index') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- VALIDACIÓN + SWEETALERT --}}
<script>
$(document).ready(function () {
    $('#formRecipientEdit').validate({
        rules: {
            org_id: { required: true },
            recipient_type: {
                required: true,
                minlength: 3,
                maxlength: 100
            },
            name: {
                required: true,
                minlength: 3,
                maxlength: 150
            },
            contact_email: {
                required: true,
                email: true,
                maxlength: 150
            },
            is_third_party: { required: true }
        },
        messages: {
            org_id: "Seleccione una organización.",
            recipient_type: {
                required: "Ingrese el tipo de destinatario.",
                minlength: "Mínimo 3 caracteres.",
                maxlength: "Máximo 100 caracteres."
            },
            name: {
                required: "Ingrese el nombre del destinatario.",
                minlength: "Mínimo 3 caracteres.",
                maxlength: "Máximo 150 caracteres."
            },
            contact_email: {
                required: "Ingrese el correo de contacto.",
                email: "Ingrese un correo válido.",
                maxlength: "Máximo 150 caracteres."
            },
            is_third_party: "Seleccione una opción."
        },
        errorElement: 'div',
        errorClass: 'is-invalid',
        validClass: 'is-valid',
        errorPlacement: function(error, element) {
            error.addClass('invalid-feedback');
            element.closest('.col-md-6, .mb-3').append(error);
        },
        submitHandler: function(form) {
            Swal.fire({
                title: '¿Desea actualizar este destinatario?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, actualizar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
    });
});
</script>
{{-- MENSAJE DE DUPLICADO AL EDITAR --}}
@if(session('alert') === 'duplicate')
<script>
Swal.fire({
    icon: 'error',
    title: 'No se puede actualizar',
    text: @json(session('message')),
    confirmButtonColor: '#dc3545',
    confirmButtonText: 'Aceptar'
});
</script>
@endif

@endsection
