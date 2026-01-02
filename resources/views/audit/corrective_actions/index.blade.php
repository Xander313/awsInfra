@extends('layouts.app')

@section('content')
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Bootstrap JS (Popper incluido) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Acciones Correctivas</h1>
        <a href="{{ route('corrective_actions.create') }}" class="btn btn-success">Nueva Acción</a>
    </div>

    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Hallazgo</th>
                <th>Propietario</th>
                <th>Fecha Límite</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($actions as $action)
            <tr>
                <td>{{ $action->ca_id }}</td>
                <td>{{ $action->finding->description }}</td>
                <td>{{ $action->owner->full_name ?? 'N/A' }}</td>
                <td>{{ $action->due_at }}</td>
                <td>
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm
                            {{ $action->status == 'open' ? 'btn-info' : ($action->status == 'in_progress' ? 'btn-warning' : 'btn-success') }}">
                            {{ ucfirst(str_replace('_', ' ', $action->status)) }}
                        </button>
                        <button type="button" class="btn btn-sm btn-secondary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="visually-hidden">Cambiar estado</span>
                        </button>
                        <ul class="dropdown-menu">
                            @foreach(['open','in_progress','closed'] as $status)
                                @if($status !== $action->status)
                                <li>
                                    <a class="dropdown-item change-status" href="#" 
                                       data-id="{{ $action->ca_id }}" data-status="{{ $status }}">
                                        {{ ucfirst(str_replace('_', ' ', $status)) }}
                                    </a>
                                </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </td>
                <td>
                    <a href="{{ route('corrective_actions.show', $action->ca_id) }}" class="btn btn-sm btn-primary">Ver</a>
                    <a href="{{ route('corrective_actions.edit', $action->ca_id) }}" class="btn btn-sm btn-warning text-white">Editar</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Script AJAX para cambiar estado -->
<script>
document.querySelectorAll('.change-status').forEach(function(el) {
    el.addEventListener('click', function(e) {
        e.preventDefault();
        const actionId = this.dataset.id;
        const status = this.dataset.status;

        fetch(`/audit/corrective_actions/${actionId}/change-status`, {
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
                const btn = this.closest('tr').querySelector('td:nth-child(5) .btn:first-child');
                btn.textContent = res.status.replace('_', ' ').charAt(0).toUpperCase() + res.status.slice(1);
                btn.classList.remove('btn-info','btn-warning','btn-success');
                if(res.status === 'open') btn.classList.add('btn-info');
                else if(res.status === 'in_progress') btn.classList.add('btn-warning');
                else if(res.status === 'closed') btn.classList.add('btn-success');
            } else {
                alert('Error al cambiar el estado');
            }
        });
    });
});
</script>
@endsection
