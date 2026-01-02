@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-center align-items-center" style="min-height: 90vh;">
    <div class="card shadow-lg w-100" style="max-width: 700px;">
        <div class="card-header bg-primary text-white text-center">
            <h4 class="mb-0">Registrar Nuevo Sistema</h4>
        </div>

        <div class="card-body">
            <form id="formSistema" action="{{ route('systems.store') }}" method="POST">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label"><b>Organización</b></label>
                        <select name="org_id" class="form-select">
                            <option value="">-- Seleccione una organización --</option>
                            @foreach ($orgs as $org)
                                <option value="{{ $org->org_id }}" {{ old('org_id') == $org->org_id ? 'selected' : '' }}>
                                    {{ $org->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label"><b>Usuario Responsable</b></label>
                        <select name="owner_user_id" class="form-select">
                            <option value="">-- Ninguno --</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->user_id }}" {{ old('owner_user_id') == $user->user_id ? 'selected' : '' }}>
                                    {{ $user->full_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label"><b>Nombre del Sistema</b></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label"><b>Tipo</b></label>
                        <input type="text" name="type" class="form-control" value="{{ old('type') }}">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label"><b>Hosting</b></label>
                        <input type="text" name="hosting" class="form-control" value="{{ old('hosting') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label"><b>Criticidad</b></label>
                        <input type="text" name="criticality" class="form-control" value="{{ old('criticality') }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label"><b>Descripción</b></label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                </div>

                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <a href="{{ route('systems.index') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- VALIDACIÓN + CONFIRMACIÓN --}}
<script>
$(document).ready(function () {
    $('#formSistema').validate({
        rules: {
            org_id: { required: true },
            owner_user_id: { required: true },
            name: { required: true, minlength: 3 },
            type: { required: true, minlength: 3 },
            hosting: { required: true, minlength: 3 },
            criticality: { required: true },
            description: { required: true, minlength: 5 }
        },
        messages: {
            org_id: "Seleccione una organización.",
            owner_user_id: "Seleccione un usuario responsable.",
            name: "Ingrese el nombre del sistema.",
            type: "Ingrese el tipo de sistema.",
            hosting: "Ingrese el hosting.",
            criticality: "Ingrese la criticidad.",
            description: "Ingrese una descripción válida."
        },
        submitHandler: function(form) {
            Swal.fire({
                title: '¿Desea guardar este sistema?',
                icon: 'question',
                showCancelButton: true,
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

{{-- 🔔 SWEETALERT: SISTEMA DUPLICADO --}}
@if(session('alert') === 'duplicate')
<script>
Swal.fire({
    icon: 'error',
    title: 'Sistema duplicado',
    text: "{{ session('message') }}",
    confirmButtonColor: '#dc3545'
});
</script>
@endif
@endsection
