@extends('layouts.app')

@section('content')

@php
    $statusLabels = [
        'open' => 'Abierto',
        'in_progress' => 'En progreso',
        'closed' => 'Cerrado',
    ];

    $statusBadgeClass = [
        'open' => 'bg-info',
        'in_progress' => 'bg-warning',
        'closed' => 'bg-success',
    ];
@endphp

<div class="container mt-5">
    <h1>Detalle de Acción Correctiva #{{ $action->ca_id }}</h1>

    <div class="card mb-3">
        <div class="card-body">
            <p><strong>Hallazgo:</strong> {{ $action->finding->description }}</p>
            <p><strong>Responsable:</strong> {{ $action->owner->full_name ?? 'No asignado' }}</p>
            <p><strong>Fecha Límite:</strong> {{ $action->due_at }}</p>

            <p><strong>Estado:</strong>
                <span class="badge {{ $statusBadgeClass[$action->status] ?? 'bg-secondary' }}">
                    {{ $statusLabels[$action->status] ?? $action->status }}
                </span>
            </p>

            <p><strong>Fecha de Cierre:</strong> {{ $action->closed_at }}</p>
            <p><strong>Resultado:</strong> {{ $action->outcome }}</p>
        </div>
    </div>

    <a href="{{ route('corrective_actions.index') }}" class="btn btn-secondary">
        Volver
    </a>
</div>
@endsection
