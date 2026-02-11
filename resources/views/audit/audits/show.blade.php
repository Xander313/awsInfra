@extends('layouts.app')

@section('content')
@php
    // Traducción visual de estados
    $estadoLabel = [
        'PLANNED' => 'Planificada',
        'IN_PROGRESS' => 'En progreso',
        'COMPLETED' => 'Completada',
        'CLOSED' => 'Cerrada',
    ];

    // Colores Bootstrap
    $estadoBadgeClass = [
        'PLANNED' => 'bg-info',
        'IN_PROGRESS' => 'bg-warning',
        'COMPLETED' => 'bg-success',
        'CLOSED' => 'bg-danger',
    ];

    // Estados de hallazgos (ajusta si usas otros valores)
    $estadoHallazgoLabel = [
        'open' => 'Abierto',
        'in_progress' => 'En progreso',
        'closed' => 'Cerrado',
    ];

    $estadoHallazgoClass = [
        'open' => 'bg-warning',
        'in_progress' => 'bg-info',
        'closed' => 'bg-success',
    ];
@endphp

<div class="container mt-5">
    <h1>Auditoría #{{ $audit->audit_id }}</h1>

    <div class="card shadow-sm mb-4">
        <div class="card-body">

            <p><strong>Organización:</strong> {{ $audit->org->name }}</p>

            <p><strong>Tipo:</strong> {{ $audit->audit_type }}</p>

            <p><strong>Alcance:</strong> {{ $audit->scope ?? 'No especificado' }}</p>

            <p><strong>Auditor:</strong> {{ $audit->auditor->full_name ?? 'N/A' }}</p>

            <p><strong>Fecha Planeada:</strong> {{ $audit->planned_at ?? 'No definida' }}</p>

            <p><strong>Fecha Ejecutada:</strong> {{ $audit->executed_at ?? 'No ejecutada' }}</p>

            <p><strong>Estado:</strong>
                <span class="badge {{ $estadoBadgeClass[$audit->status] ?? 'bg-secondary' }}">
                    {{ $estadoLabel[$audit->status] ?? $audit->status }}
                </span>
            </p>

        </div>
    </div>

    <h4>Hallazgos</h4>

    @if($audit->findings->count())
        <ul class="list-group mb-4">
            @foreach($audit->findings as $finding)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    {{ $finding->description }}

                    <span class="badge {{ $estadoHallazgoClass[$finding->status] ?? 'bg-secondary' }}">
                        {{ $estadoHallazgoLabel[$finding->status] ?? $finding->status }}
                    </span>
                </li>
            @endforeach
        </ul>
    @else
        <div class="alert alert-info">
            No existen hallazgos registrados para esta auditoría.
        </div>
    @endif

    <a href="{{ route('audits.index') }}" class="btn btn-secondary">
        Volver
    </a>

    <a href="{{ route('audits.edit', $audit->audit_id) }}" class="btn btn-warning text-white ms-2">
        Editar
    </a>

</div>
@endsection
