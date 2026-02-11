@extends('layouts.app')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

@php
    $estadoLabel = [
        'open' => 'Abierto',
        'in_progress' => 'En progreso',
        'closed' => 'Cerrado',
    ];

    $estadoBtnClass = [
        'open' => 'btn-info',
        'in_progress' => 'btn-warning',
        'closed' => 'btn-success',
    ];
@endphp

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Hallazgos</h1>
        <a href="{{ route('findings.create') }}" class="btn btn-success">Nuevo Hallazgo</a>
    </div>

    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Auditoría</th>
                <th>Control</th>
                <th>Severidad</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($findings as $finding)
            <tr>
                <td>{{ $finding->finding_id }}</td>
                <td>{{ $finding->audit->audit_type }}</td>
                <td>{{ $finding->control->name ?? 'N/A' }}</td>
                <td>{{ $finding->severity }}</td>
                <td>
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm {{ $estadoBtnClass[$finding->status] ?? 'btn-secondary' }}">
                            {{ $estadoLabel[$finding->status] ?? $finding->status }}
                        </button>

                        <button type="button" class="btn btn-sm btn-secondary dropdown-toggle dropdown-toggle-split"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="visually-hidden">Cambiar estado</span>
                        </button>

                        <ul class="dropdown-menu">
                            @foreach(['open','in_progress','closed'] as $status)
                                @if($status !== $finding->status)
                                <li>
                                    <a class="dropdown-item change-status"
                                       href="#"
                                       data-id="{{ $finding->finding_id }}"
                                       data-status="{{ $status }}">
                                        {{ $estadoLabel[$status] ?? $status }}
                                    </a>
                                </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </td>
                <td>
                    <a href="{{ route('findings.show', $finding->finding_id) }}" class="btn btn-sm btn-primary">Ver</a>
                    <a href="{{ route('findings.edit', $finding->finding_id) }}" class="btn btn-sm btn-warning text-white">Editar</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
document.querySelectorAll('.change-status').forEach(function(el) {
    el.addEventListener('click', function(e) {
        e.preventDefault();

        const findingId = this.dataset.id;
        const status = this.dataset.status; // ✅ open/in_progress/closed

        fetch(`/auditorias/hallazgos/${findingId}/cambiar-estado`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ status })
        })
        .then(res => res.json())
        .then(res => {
            if (res.success) {
                const estados = {
                    open: 'Abierto',
                    in_progress: 'En progreso',
                    closed: 'Cerrado'
                };

                const btn = this.closest('.btn-group').querySelector('button:first-child');
                btn.textContent = estados[res.status] ?? res.status;

                btn.classList.remove('btn-info', 'btn-warning', 'btn-success', 'btn-secondary');
                if (res.status === 'open') btn.classList.add('btn-info');
                else if (res.status === 'in_progress') btn.classList.add('btn-warning');
                else if (res.status === 'closed') btn.classList.add('btn-success');
                else btn.classList.add('btn-secondary');
            } else {
                alert('Error al cambiar el estado');
            }
        })
        .catch(() => alert('Error al cambiar el estado'));
    });
});
</script>
@endsection
