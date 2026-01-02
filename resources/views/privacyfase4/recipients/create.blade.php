@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-center align-items-center" style="min-height: 90vh;">
    <div class="card shadow-lg w-100" style="max-width: 700px;">
        <div class="card-header bg-primary text-white text-center">
            <h4 class="mb-0">Registrar Destinatario</h4>
        </div>

        <div class="card-body">
            <form id="formRecipient" action="{{ route('recipients.store') }}" method="POST">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label"><b>Organización</b></label>
                        <select name="org_id" class="form-select">
                            <option value="">-- Seleccione una organización --</option>
                            @foreach ($orgs as $org)
                                <option value="{{ $org->org_id }}"
                                    {{ old('org_id') == $org->org_id ? 'selected' : '' }}>
                                    {{ $org->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label"><b>Tipo de Destinatario</b></label>
                        <input type="text" name="recipient_type" class="form-control"
                               value="{{ old('recipient_type') }}">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label"><b>Nombre del Destinatario</b></label>
                        <input type="text" name="name" class="form-control"
                               value="{{ old('name') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label"><b>Correo de Contacto</b></label>
                        <input type="email" name="contact_email" class="form-control"
                               value="{{ old('contact_email') }}"
                               placeholder="correo@ejemplo.com">
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label"><b>¿Es Tercero?</b></label>
                        <select name="is_third_party" class="form-select">
                            <option value="">-- Seleccione --</option>
                            <option value="0" {{ old('is_third_party') === '0' ? 'selected' : '' }}>No</option>
                            <option value="1" {{ old('is_third_party') === '1' ? 'selected' : '' }}>Sí</option>
                        </select>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <a href="{{ route('recipients.index') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- VALIDACIÓN COMPLETA + CONFIRMACIÓN --}}
<script>
$(document).ready(function () {
    $('#formRecipient').validate({
        rules: {
            org_id: { required: true },
            recipient_type: { required: true, minlength: 3 },
            name: { required: true, minlength: 3 },
            contact_email: { required: true, email: true },
            is_third_party: { required: true }
        },
        messages: {
            org_id: "Seleccione una organización.",
            recipient_type: {
                required: "Ingrese el tipo de destinatario.",
                minlength: "Mínimo 3 caracteres."
            },
            name: {
                required: "Ingrese el nombre del destinatario.",
                minlength: "Mínimo 3 caracteres."
            },
            contact_email: {
                required: "Ingrese un correo de contacto.",
                email: "Ingrese un correo válido."
            },
            is_third_party: "Seleccione una opción."
        },
        errorClass: 'is-invalid',
        validClass: 'is-valid',
        errorPlacement: function(error, element) {
            error.addClass('invalid-feedback');
            element.closest('.col-md-6').append(error);
        },
        submitHandler: function(form) {
            Swal.fire({
                title: '¿Desea guardar este destinatario?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0d6efd',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, guardar',
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

{{-- MENSAJE DE DUPLICADO --}}
@if(session('alert') === 'duplicate')
<script>
Swal.fire({
    icon: 'error',
    title: 'Registro duplicado',
    text: @json(session('message')),
    confirmButtonColor: '#dc3545',
    confirmButtonText: 'Aceptar'
});
</script>
@endif


@endsection
