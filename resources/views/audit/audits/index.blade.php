@extends('layouts.app')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

@php
    $estadoLabel = [
        'PLANNED' => 'Planificada',
        'IN_PROGRESS' => 'En progreso',
        'COMPLETED' => 'Completada',
        'CLOSED' => 'Cerrada',
    ];

    $estadoBtnClass = [
        'PLANNED' => 'btn-info',
        'IN_PROGRESS' => 'btn-warning',
        'COMPLETED' => 'btn-success',
        'CLOSED' => 'btn-danger',
    ];
@endphp

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Auditorías</h1>
        <a href="{{ route('audits.create') }}" class="btn btn-success">Nueva Auditoría</a>
    </div>

    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Organización</th>
                <th>Tipo</th>
                <th>Auditor</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($audits as $audit)
            <tr>
                <td>{{ $audit->audit_id }}</td>
                <td>{{ $audit->org->name }}</td>
                <td>{{ $audit->audit_type }}</td>
                <td>{{ $audit->auditor->full_name ?? 'N/A' }}</td>
                <td>
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm {{ $estadoBtnClass[$audit->status] ?? 'btn-secondary' }}">
                            {{ $estadoLabel[$audit->status] ?? $audit->status }}
                        </button>

                        <button type="button" class="btn btn-sm btn-secondary dropdown-toggle dropdown-toggle-split"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="visually-hidden">Cambiar estado</span>
                        </button>

                        <ul class="dropdown-menu">
                            @foreach(['PLANNED','IN_PROGRESS','COMPLETED','CLOSED'] as $status)
                                @if($status !== $audit->status)
                                <li>
                                    <a class="dropdown-item change-status"
                                       href="#"
                                       data-id="{{ $audit->audit_id }}"
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
                    <a href="{{ route('audits.show', $audit->audit_id) }}" class="btn btn-sm btn-primary">Ver</a>
                    <a href="{{ route('audits.edit', $audit->audit_id) }}" class="btn btn-sm btn-warning text-white">Editar</a>
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

        const auditId = this.dataset.id;
        const status = this.dataset.status; // ✅ SIEMPRE inglés

        fetch(`/auditorias/${auditId}/cambiar-estado`, {
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
                    'PLANNED': 'Planificada',
                    'IN_PROGRESS': 'En progreso',
                    'COMPLETED': 'Completada',
                    'CLOSED': 'Cerrada'
                };

                const btn = this.closest('.btn-group').querySelector('button:first-child');
                btn.textContent = estados[res.status] ?? res.status;

                btn.classList.remove('btn-info', 'btn-warning', 'btn-success', 'btn-danger', 'btn-secondary');
                if (res.status === 'PLANNED') btn.classList.add('btn-info');
                else if (res.status === 'IN_PROGRESS') btn.classList.add('btn-warning');
                else if (res.status === 'COMPLETED') btn.classList.add('btn-success');
                else if (res.status === 'CLOSED') btn.classList.add('btn-danger');
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
