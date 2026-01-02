@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Controles</h1>
        <a href="{{ route('controls.create') }}" class="btn btn-success">Nuevo Control</a>
    </div>

    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Organización</th>
                <th>Código</th>
                <th>Nombre</th>
                <th>Tipo</th>
                <th>Propietario</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($controls as $control)
            <tr>
                <td>{{ $control->control_id }}</td>
                <td>{{ $control->org->name }}</td>
                <td>{{ $control->code }}</td>
                <td>{{ $control->name }}</td>
                <td>{{ $control->control_type }}</td>
                <td>{{ $control->owner->full_name ?? 'N/A' }}</td>
                <td>
                    <a href="{{ route('controls.show', $control->control_id) }}" class="btn btn-sm btn-primary">Ver</a>
                    <a href="{{ route('controls.edit', $control->control_id) }}" class="btn btn-sm btn-warning text-white">Editar</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
