@extends('layouts.app')

@section('content')
@php
    $estadoLabel = [
        'open' => 'Abierto',
        'in_progress' => 'En progreso',
        'closed' => 'Cerrado',
    ];

    $estadoBadgeClass = [
        'open' => 'bg-info',
        'in_progress' => 'bg-warning',
        'closed' => 'bg-success',
    ];
@endphp

<div class="container mt-5">
    <h1>Detalle Hallazgo #{{ $finding->finding_id }}</h1>

    <div class="card mb-3">
        <div class="card-body">
            <p><strong>Auditoría:</strong> {{ $finding->audit->audit_type }}</p>
            <p><strong>Control:</strong> {{ $finding->control->name ?? 'N/A' }}</p>
            <p><strong>Severidad:</strong> {{ $finding->severity }}</p>
            <p><strong>Estado:</strong>
                <span class="badge {{ $estadoBadgeClass[$finding->status] ?? 'bg-secondary' }}">
                    {{ $estadoLabel[$finding->status] ?? $finding->status }}
                </span>
            </p>
            <p><strong>Descripción:</strong> {{ $finding->description }}</p>
        </div>
    </div>

    <h3>Acciones Correctivas</h3>
    <ul class="list-group mb-3">
        @foreach($finding->correctiveActions as $action)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                {{ $action->outcome ?? 'Sin resultado' }}
                <span class="badge {{ $estadoBadgeClass[$action->status] ?? 'bg-secondary' }}">
                    {{ $estadoLabel[$action->status] ?? $action->status }}
                </span>
            </li>
        @endforeach
    </ul>

    <a href="{{ route('findings.index') }}" class="btn btn-secondary">Volver</a>
    <a href="{{ route('findings.edit', $finding->finding_id) }}" class="btn btn-warning text-white ms-2">Editar</a>
</div>
@endsection
