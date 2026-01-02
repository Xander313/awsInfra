@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1>Detalle Acción Correctiva #{{ $action->ca_id }}</h1>

    <div class="card mb-3">
        <div class="card-body">
            <p><strong>Hallazgo:</strong> {{ $action->finding->description }}</p>
            <p><strong>Propietario:</strong> {{ $action->owner->full_name ?? 'N/A' }}</p>
            <p><strong>Fecha Límite:</strong> {{ $action->due_at }}</p>
            <p><strong>Estado:</strong> 
                <span class="badge 
                    {{ $action->status == 'open' ? 'bg-info' : ($action->status == 'in_progress' ? 'bg-warning' : 'bg-success') }}">
                    {{ ucfirst(str_replace('_', ' ', $action->status)) }}
                </span>
            </p>
            <p><strong>Fecha Cierre:</strong> {{ $action->closed_at }}</p>
            <p><strong>Resultado:</strong> {{ $action->outcome }}</p>
        </div>
    </div>

    <a href="{{ route('corrective_actions.index') }}" class="btn btn-secondary">Volver</a>
</div>
@endsection
