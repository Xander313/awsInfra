@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-center align-items-center" style="min-height: 90vh;">
    <div class="card shadow-lg w-100" style="max-width: 700px;">
        <div class="card-header bg-primary text-white text-center">
            <h4 class="mb-0">Registrar Almacén de Datos</h4>
        </div>

        <div class="card-body">
            <form id="formDataStore" action="{{ route('data-stores.store') }}" method="POST">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label"><b>Sistema</b></label>

                        @if(request('system_id'))
                            {{-- Campo bloqueado y se envía como hidden --}}
                            <select class="form-select" disabled>
                                @foreach ($systems as $sys)
                                    <option value="{{ $sys->system_id }}"
                                        {{ request('system_id') == $sys->system_id ? 'selected' : '' }}>
                                        {{ $sys->name }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="system_id" value="{{ request('system_id') }}">
                        @else
                            <select name="system_id" class="form-select">
                                <option value="">-- Seleccione un sistema --</option>
                                @foreach ($systems as $sys)
                                    <option value="{{ $sys->system_id }}"
                                        {{ old('system_id') == $sys->system_id ? 'selected' : '' }}>
                                        {{ $sys->name }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                    </div>

                    <div class="col-md-6">
                        <label class="form-label"><b>Tipo de Almacén</b></label>
                        <select name="store_type" class="form-select">
                            <option value="">-- Seleccione --</option>
                            <option value="Físico" {{ old('store_type') == 'Físico' ? 'selected' : '' }}>Físico</option>
                            <option value="Digital" {{ old('store_type') == 'Digital' ? 'selected' : '' }}>Digital</option>
                            <option value="Nube" {{ old('store_type') == 'Nube' ? 'selected' : '' }}>Nube</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label"><b>Nombre del Almacén</b></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label"><b>Ubicación</b></label>
                        <input type="text" name="location" class="form-control" value="{{ old('location') }}">
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label"><b>Cifrado</b></label>
                        <select name="encryption_flag" class="form-select">
                            <option value="">-- Seleccione --</option>
                            <option value="1" {{ old('encryption_flag') == '1' ? 'selected' : '' }}>Sí</option>
                            <option value="0" {{ old('encryption_flag') == '0' ? 'selected' : '' }}>No</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label"><b>Respaldo</b></label>
                        <select name="backup_flag" class="form-select">
                            <option value="">-- Seleccione --</option>
                            <option value="1" {{ old('backup_flag') == '1' ? 'selected' : '' }}>Sí</option>
                            <option value="0" {{ old('backup_flag') == '0' ? 'selected' : '' }}>No</option>
                        </select>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <a href="{{ request('system_id') ? route('data-stores.index', ['system_id' => request('system_id')]) : route('data-stores.index') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- VALIDACIÓN + CONFIRMACIÓN --}}
<script>
$(document).ready(function () {
    $('#formDataStore').validate({
        rules: {
            system_id: { required: true },
            store_type: { required: true },
            name: { required: true, minlength: 3 },
            location: { required: true, minlength: 3 },
            encryption_flag: { required: true },
            backup_flag: { required: true }
        },
        messages: {
            system_id: "Seleccione un sistema.",
            store_type: "Seleccione el tipo de almacén.",
            name: { required: "Ingrese el nombre del almacén.", minlength: "Mínimo 3 caracteres." },
            location: { required: "Ingrese la ubicación.", minlength: "Mínimo 3 caracteres." },
            encryption_flag: "Seleccione una opción.",
            backup_flag: "Seleccione una opción."
        },
        errorClass: 'is-invalid',
        validClass: 'is-valid',
        errorPlacement: function(error, element) {
            error.addClass('invalid-feedback');
            element.closest('.col-md-6').append(error);
        },
        submitHandler: function(form) {
            Swal.fire({
                title: '¿Desea guardar este almacén de datos?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#198754',
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

{{-- SWEETALERT: ERROR DUPLICADO --}}
@if(session('alert') === 'duplicate')
<script>
Swal.fire({
    icon: 'error',
    title: 'Registro duplicado',
    text: "{{ session('message') }}",
    confirmButtonColor: '#dc3545',
    confirmButtonText: 'Aceptar'
});
</script>
@endif
@endsection
