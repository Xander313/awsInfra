@extends('layouts.app')

@section('title', 'Crear País')

@section('content')

{{-- Font Awesome --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>

<style>
/* Estilos de validación */
.error {
    border-color: #dc3545 !important;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
}
label.error {
    color: #dc3545;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}
</style>

<div class="card p-4 bg-white rounded-lg shadow-sm">
    <h2 class="mb-4 font-bold text-lg">Crear País</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="countryForm" action="{{ route('privacy.country.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="iso_code" class="form-label">Código ISO</label>
            <input type="text" name="iso_code" id="iso_code" class="form-control" value="{{ old('iso_code') }}" required maxlength="3" minlength="2" placeholder="Ej: EC">
        </div>

        <div class="mb-3">
            <label for="name" class="form-label">Nombre del País</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required minlength="2" placeholder="Ej: Ecuador">
        </div>

        <button type="submit" class="btn btn-outline-success">
            <i class="fas fa-save"></i> Guardar
        </button>
        <a href="{{ route('privacy.country.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </form>
</div>

{{-- jQuery y jQuery Validation --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>

<script>
$(document).ready(function() {
    $('#countryForm').validate({
        errorClass: 'error',
        validClass: 'valid',
        highlight: function(element) {
            $(element).addClass('is-invalid').removeClass('is-valid');
        },
        unhighlight: function(element) {
            $(element).addClass('is-valid').removeClass('is-invalid');
        },
        errorPlacement: function(error, element) {
            error.addClass('invalid-feedback');
            error.insertAfter(element);
        },
        rules: {
            iso_code: {
                required: true,
                minlength: 2,
                maxlength: 3,
                pattern: /^[A-Z]{2,3}$/
            },
            name: {
                required: true,
                minlength: 2
            }
        },
        messages: {
            iso_code: {
                required: "El código ISO es obligatorio",
                minlength: "El código ISO debe tener al menos 2 caracteres",
                maxlength: "El código ISO no puede tener más de 3 caracteres",
                pattern: "El código ISO debe contener solo letras mayúsculas"
            },
            name: {
                required: "El nombre del país es obligatorio",
                minlength: "El nombre debe tener al menos 2 caracteres"
            }
        }
    });
});
</script>

@endsection
