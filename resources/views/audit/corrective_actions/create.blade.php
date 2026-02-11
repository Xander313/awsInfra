
@extends('layouts.app')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/additional-methods.min.js"></script>

<div class="container mt-5">
    <h1 class="mb-4">{{ isset($action) ? 'Editar Acción Correctiva' : 'Nueva Acción Correctiva' }}</h1>

    <form id="frm_action"
          action="{{ isset($action) ? route('corrective_actions.update', $action->ca_id) : route('corrective_actions.store') }}"
          method="POST">
        @csrf
        @if(isset($action)) @method('PUT') @endif

        {{-- Hallazgo --}}
        <div class="mb-3">
            <label class="form-label">Hallazgo</label>
            <select class="form-select" name="finding_id">
                <option value="">-- Seleccione --</option>
                @foreach($findings as $finding)
                    <option value="{{ $finding->finding_id }}"
                        {{ isset($action) && $action->finding_id == $finding->finding_id ? 'selected' : '' }}>
                        {{ $finding->description }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Propietario --}}
        <div class="mb-3">
            <label class="form-label">Propietario</label>
            <select class="form-select" name="owner_user_id">
                <option value="">-- Seleccione --</option>
                @foreach($users as $user)
                    <option value="{{ $user->user_id }}"
                        {{ isset($action) && $action->owner_user_id == $user->user_id ? 'selected' : '' }}>
                        {{ $user->full_name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Fecha Límite --}}
        <div class="mb-3">
            <label class="form-label">Fecha Límite</label>
            <input type="datetime-local"
                   class="form-control"
                   name="due_at"
                   value="{{ isset($action) && $action->due_at ? date('Y-m-d\TH:i', strtotime($action->due_at)) : '' }}">
        </div>

        {{-- Estado --}}
        <div class="mb-3">
            <label class="form-label">Estado</label>
            <select class="form-select" name="status">
                <option value="">-- Seleccione --</option>
                <option value="open" {{ isset($action) && $action->status == 'open' ? 'selected' : '' }}>Abierto</option>
                <option value="in_progress" {{ isset($action) && $action->status == 'in_progress' ? 'selected' : '' }}>En Progreso</option>
                <option value="closed" {{ isset($action) && $action->status == 'closed' ? 'selected' : '' }}>Cerrado</option>
            </select>
        </div>

        {{-- Fecha de Cierre (AUTOMÁTICA) --}}
        <div class="mb-3">
            <label class="form-label">Fecha de Cierre</label>
            <input type="datetime-local"
                   class="form-control"
                   readonly
                   disabled
                   value="{{ isset($action) && $action->closed_at ? date('Y-m-d\TH:i', strtotime($action->closed_at)) : '' }}">
            <small class="text-muted">
                La fecha de cierre se genera automáticamente cuando la acción es marcada como cerrada.
            </small>
        </div>

        {{-- Resultado --}}
        <div class="mb-3">
            <label class="form-label">Resultado</label>
            <textarea class="form-control"
                      name="outcome"
                      rows="3">{{ old('outcome', $action->outcome ?? '') }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">
            {{ isset($action) ? 'Actualizar' : 'Guardar' }}
        </button>
        <a href="{{ route('corrective_actions.index') }}" class="btn btn-secondary ms-2">Cancelar</a>
    </form>
</div>

<script>
$(function() {

    // 🔹 Fecha límite no puede ser pasada
    $.validator.addMethod("notPastDueDate", function(value) {
        if (!value) return true;
        return new Date(value) >= new Date();
    }, "La fecha límite no puede ser anterior a la fecha actual.");

    // 🔹 Fecha límite máximo 1 año
    $.validator.addMethod("maxOneYearDue", function(value) {
        if (!value) return true;
        let max = new Date();
        max.setFullYear(max.getFullYear() + 1);
        return new Date(value) <= max;
    }, "La fecha límite no puede exceder un año a partir de la fecha actual.");

    $("#frm_action").validate({
        rules: {
            finding_id: { required: true },
            owner_user_id: { required: true },
            due_at: {
                required: true,
                notPastDueDate: true,
                maxOneYearDue: true
            },
            status: { required: true },
            outcome: {
                required: true,
                minlength: 5,
                maxlength: 1000
            }
        },
        messages: {
            finding_id: { required: "Seleccione un hallazgo" },
            owner_user_id: { required: "Seleccione un propietario" },
            due_at: {
                required: "Ingrese la fecha límite",
                notPastDueDate: "No se permite una fecha límite anterior a la actual.",
                maxOneYearDue: "La fecha límite no puede superar el rango permitido de un año."
            },
            status: { required: "Seleccione un estado" },
            outcome: {
                required: "Ingrese el resultado",
                minlength: "Mínimo 5 caracteres",
                maxlength: "Máximo 1000 caracteres"
            }
        },
        errorElement: "span",
        errorClass: "text-danger",
        highlight: function(el){ $(el).addClass("border-danger"); },
        unhighlight: function(el){ $(el).removeClass("border-danger"); }
    });
});
</script>
@endsection