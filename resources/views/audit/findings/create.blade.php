@extends('layouts.app')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/additional-methods.min.js"></script>

<div class="container mt-5">
    <h1 class="mb-4">{{ isset($finding) ? 'Editar Hallazgo' : 'Nuevo Hallazgo' }}</h1>

    <form id="frm_finding" action="{{ isset($finding) ? route('findings.update', $finding->finding_id) : route('findings.store') }}" method="POST">
        @csrf
        @if(isset($finding)) @method('PUT') @endif

        <div class="mb-3">
            <label class="form-label">Auditoría</label>
            <select class="form-select" name="audit_id">
                <option value="">-- Seleccione --</option>
                @foreach($audits as $audit)
                    <option value="{{ $audit->audit_id }}" {{ isset($finding) && $finding->audit_id == $audit->audit_id ? 'selected' : '' }}>
                        {{ $audit->audit_type }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Control</label>
            <select class="form-select" name="control_id">
                <option value="">-- Ninguno --</option>
                @foreach($controls as $control)
                    <option value="{{ $control->control_id }}" {{ isset($finding) && $finding->control_id == $control->control_id ? 'selected' : '' }}>
                        {{ $control->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Severidad</label>
            <input type="text" class="form-control" name="severity" value="{{ old('severity', $finding->severity ?? '') }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Estado</label>
            <select class="form-select" name="status">
                <option value="">-- Seleccione --</option>
                <option value="open" {{ isset($finding) && $finding->status == 'open' ? 'selected' : '' }}>Abierto</option>
                <option value="in_progress" {{ isset($finding) && $finding->status == 'in_progress' ? 'selected' : '' }}>En Progreso</option>
                <option value="closed" {{ isset($finding) && $finding->status == 'closed' ? 'selected' : '' }}>Cerrado</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Descripción</label>
            <textarea class="form-control" name="description" rows="3">{{ old('description', $finding->description ?? '') }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">{{ isset($finding) ? 'Actualizar' : 'Guardar' }}</button>
        <a href="{{ route('findings.index') }}" class="btn btn-secondary ms-2">Cancelar</a>
    </form>
</div>

<script>
$(function() {
    $("#frm_finding").validate({
        rules: {
            audit_id: { required: true },
            control_id: { required: true }, 
            severity: { required: true, minlength: 3, maxlength: 50 },
            status: { required: true },
            description: { required: true, minlength: 10, maxlength: 1000 }
        },
        messages: {
            audit_id: { required: "Seleccione una auditoría" },
            control_id: { required: "Seleccione un control" }, 
            severity: { required: "Ingrese la severidad", minlength: "Mínimo 3 caracteres", maxlength: "Máximo 50 caracteres" },
            status: { required: "Seleccione un estado" },
            description: { required: "Ingrese la descripción", minlength: "Mínimo 10 caracteres", maxlength: "Máximo 1000 caracteres" }
        },
        errorElement: "span",
        errorClass: "text-danger",
        highlight: function(element){ $(element).addClass("border-danger"); },
        unhighlight: function(element){ $(element).removeClass("border-danger"); }
    });
});
</script>
@endsection
