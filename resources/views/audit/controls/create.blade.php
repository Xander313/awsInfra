@extends('layouts.app')
@section('title', 'Controles')
@section('active_key', 'controls')
@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/additional-methods.min.js"></script>

@php
    $isEdit = !is_null($control);
@endphp

<div class="container mt-5">
    <h1 class="mb-4">{{ $isEdit ? 'Editar Control' : 'Nuevo Control' }}</h1>

    {{-- Errores backend --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="frm_control"
          action="{{ $isEdit ? route('controls.update', $control->control_id) : route('controls.store') }}"
          method="POST">
        @csrf
        @if($isEdit) @method('PUT') @endif

        <div class="mb-3">
            <label class="form-label">Organización activa</label>
            <input type="text" class="form-control" value="{{ session('org_id') }}" disabled>
        </div>

        <div class="mb-3">
            <label class="form-label">Código</label>
            <input type="text" class="form-control" name="code"
                   value="{{ old('code', $control->code ?? '') }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input type="text" class="form-control" name="name"
                   value="{{ old('name', $control->name ?? '') }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Tipo</label>
            <input type="text" class="form-control" name="control_type"
                   value="{{ old('control_type', $control->control_type ?? '') }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Descripción</label>
            <textarea class="form-control" name="description" rows="3">{{ old('description', $control->description ?? '') }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Propietario</label>
            <select class="form-select" name="owner_user_id">
                <option value="">-- Seleccione --</option>
                @foreach($users as $user)
                    <option value="{{ $user->user_id }}"
                        {{ (string) old('owner_user_id', $control->owner_user_id ?? '') === (string) $user->user_id ? 'selected' : '' }}>
                        {{ $user->full_name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- ✅ Para que UPDATE no falle (porque tu update exige status) --}}
        @if($isEdit)
        <div class="mb-3">
            <label class="form-label">Estado</label>
            <select class="form-select" name="status" required>
                <option value="Activo" {{ old('status', $control->status ?? 'Activo') === 'Activo' ? 'selected' : '' }}>Activo</option>
                <option value="Inactivo" {{ old('status', $control->status ?? '') === 'Inactivo' ? 'selected' : '' }}>Inactivo</option>
            </select>
        </div>
        @endif

        <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Actualizar' : 'Guardar' }}</button>
        <a href="{{ route('controls.index') }}" class="btn btn-secondary ms-2">Cancelar</a>
    </form>
</div>

<script>
$(function() {
    $("#frm_control").validate({
        rules: {
            code: { required: true, minlength: 3, maxlength: 50 },
            name: { required: true, minlength: 3, maxlength: 255 },
            control_type: { required: true, minlength: 3, maxlength: 100 },
            description: { required: true, minlength: 10, maxlength: 1000 },
            owner_user_id: { required: true }
        },
        messages: {
            code: { required: "Obligatorio", minlength: "Mínimo 3 caracteres", maxlength: "Máximo 50 caracteres" },
            name: { required: "Obligatorio", minlength: "Mínimo 3 caracteres", maxlength: "Máximo 255 caracteres" },
            control_type: { required: "Obligatorio", minlength: "Mínimo 3 caracteres", maxlength: "Máximo 100 caracteres" },
            description: { required: "Obligatorio", minlength: "Mínimo 10 caracteres", maxlength: "Máximo 1000 caracteres" },
            owner_user_id: { required: "Seleccione un propietario" }
        },
        errorElement: "span",
        errorClass: "text-danger",
        highlight: function(element){ $(element).addClass("border-danger"); },
        unhighlight: function(element){ $(element).removeClass("border-danger"); }
    });
});
</script>
@endsection
