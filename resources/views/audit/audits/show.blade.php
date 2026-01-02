@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1>Auditoría #{{ $audit->audit_id }}</h1>

    <div class="card mb-3">
        <div class="card-body">
            <p><strong>Organización:</strong> {{ $audit->org->name }}</p>
            <p><strong>Tipo:</strong> {{ $audit->audit_type }}</p>
            <p><strong>Alcance:</strong> {{ $audit->scope }}</p>
            <p><strong>Auditor:</strong> {{ $audit->auditor->full_name ?? 'N/A' }}</p>
            <p><strong>Fecha Planeada:</strong> {{ $audit->planned_at }}</p>
            <p><strong>Fecha Ejecutada:</strong> {{ $audit->executed_at }}</p>
            <p><strong>Estado:</strong> 
                <span class="badge 
                    {{ $audit->status == 'planned' ? 'bg-info' : ($audit->status == 'executed' ? 'bg-success' : 'bg-danger') }}">
                    {{ ucfirst($audit->status) }}
                </span>
            </p>
        </div>
    </div>

    <h3>Hallazgos</h3>
    <ul class="list-group">
        @foreach($audit->findings as $finding)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                {{ $finding->description }}
                <span class="badge {{ $finding->status == 'open' ? 'bg-warning' : 'bg-success' }}">
                    {{ ucfirst($finding->status) }}
                </span>
            </li>
        @endforeach
    </ul>

    <a href="{{ route('audits.index') }}" class="btn btn-secondary mt-3">Volver</a>
</div>
@endsection
