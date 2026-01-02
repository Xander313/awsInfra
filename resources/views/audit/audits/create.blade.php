@extends('layouts.app')

@section('content')
<!-- CSS Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<!-- jQuery Validation -->
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/additional-methods.min.js"></script>

<div class="container mt-5">
    <h1 class="mb-4">{{ isset($audit) ? 'Editar Auditoría' : 'Nueva Auditoría' }}</h1>

    <form id="frm_audit" action="{{ isset($audit) ? route('audits.update', $audit->audit_id) : route('audits.store') }}" method="POST">
        @csrf
        @if(isset($audit)) @method('PUT') @endif

        {{-- Tipo de Auditoría --}}
        <div class="mb-3">
            <label class="form-label">Tipo de Auditoría</label>
            <input type="text" class="form-control" name="audit_type" value="{{ old('audit_type', $audit->audit_type ?? '') }}">
        </div>

        {{-- Alcance --}}
        <div class="mb-3">
            <label class="form-label">Alcance</label>
            <textarea class="form-control" name="scope" rows="3">{{ old('scope', $audit->scope ?? '') }}</textarea>
        </div>

        {{-- Auditor --}}
        <div class="mb-3">
            <label class="form-label">Auditor</label>
            <select class="form-select" name="auditor_user_id">
                <option value="">-- Seleccione --</option>
                @foreach($users as $user)
                    <option value="{{ $user->user_id }}" {{ old('auditor_user_id', $audit->auditor_user_id ?? '') == $user->user_id ? 'selected' : '' }}>
                        {{ $user->full_name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Fecha planeada --}}
        <div class="mb-3">
            <label class="form-label">Fecha Planeada</label>
            <input type="datetime-local" class="form-control" name="planned_at" value="{{ isset($audit) && $audit->planned_at ? date('Y-m-d\TH:i', strtotime($audit->planned_at)) : '' }}">
        </div>

        {{-- Estado --}}
        <div class="mb-3">
            <label class="form-label">Estado</label>
            <select class="form-select" name="status">
                <option value="">-- Seleccione --</option>
                <option value="PLANNED" {{ (isset($audit) && $audit->status === 'PLANNED') ? 'selected' : '' }}>Planeada</option>
                <option value="IN_PROGRESS" {{ (isset($audit) && $audit->status === 'IN_PROGRESS') ? 'selected' : '' }}>En ejecución</option>
                <option value="COMPLETED" {{ (isset($audit) && $audit->status === 'COMPLETED') ? 'selected' : '' }}>Completada</option>
                <option value="CLOSED" {{ (isset($audit) && $audit->status === 'CLOSED') ? 'selected' : '' }}>Cerrada</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">{{ isset($audit) ? 'Actualizar' : 'Guardar' }}</button>
        <a href="{{ route('audits.index') }}" class="btn btn-secondary ms-2">Cancelar</a>
    </form>
</div>

<script>
$(function() {
    // Método pattern personalizado
    $.validator.addMethod("pattern", function(value, element, param) {
        return this.optional(element) || param.test(value);
    }, "Formato inválido");

    $("#frm_audit").validate({
        rules: {
            audit_type: { required: true, maxlength:30, pattern: /^[a-zA-Z0-9\s\-]+$/ },
            scope: { required: true, minlength: 10, maxlength: 1000 },
            auditor_user_id: { required: true },
            planned_at: { required: true },
            status: { required: true }
        },
        messages: {
            audit_type: { required: "Obligatorio", minlength:10, maxlength: "Máximo 30 caracteres", pattern: "Solo letras, números y guiones" },
            scope: { required: "Obligatorio", minlength: "Mínimo 10 caracteres", maxlength: "Máximo 1000 caracteres" },
            auditor_user_id: { required: "Seleccione un auditor" },
            planned_at: { required: "Ingrese fecha planeada" },
            status: { required: "Seleccione un estado" }
        },

        errorElement: "span",
        errorClass: "text-danger",
        highlight: function(element){ $(element).addClass("border-danger"); },
        unhighlight: function(element){ $(element).removeClass("border-danger"); }
    });
});
</script>
@endsection
