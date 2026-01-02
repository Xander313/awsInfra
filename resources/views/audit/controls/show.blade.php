@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1>Control #{{ $control->control_id }}</h1>

    <div class="card mb-3">
        <div class="card-body">
            <p><strong>Organización:</strong> {{ $control->org->name }}</p>
            <p><strong>Código:</strong> {{ $control->code }}</p>
            <p><strong>Nombre:</strong> {{ $control->name }}</p>
            <p><strong>Tipo:</strong> {{ $control->control_type }}</p>
            <p><strong>Propietario:</strong> {{ $control->owner->full_name ?? 'N/A' }}</p>
            <p><strong>Descripción:</strong> {{ $control->description }}</p>
        </div>
    </div>

    <h3>Hallazgos asociados</h3>
    <ul class="list-group mb-3">
        @foreach($control->findings as $finding)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                {{ $finding->description }}
                <span class="badge {{ $finding->status == 'open' ? 'bg-warning' : 'bg-success' }}">
                    {{ ucfirst($finding->status) }}
                </span>
            </li>
        @endforeach
    </ul>

    <a href="{{ route('controls.index') }}" class="btn btn-secondary">Volver</a>
</div>
@endsection
