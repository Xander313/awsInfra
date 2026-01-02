@extends('layouts.app')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Bootstrap JS (Popper incluido) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
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
                        <button type="button" class="btn btn-sm
                            {{ $finding->status == 'open' ? 'btn-info' : ($finding->status == 'in_progress' ? 'btn-warning' : 'btn-success') }}">
                            {{ ucfirst(str_replace('_', ' ', $finding->status)) }}
                        </button>
                        <button type="button" class="btn btn-sm btn-secondary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="visually-hidden">Cambiar estado</span>
                        </button>
                        <ul class="dropdown-menu">
                            @foreach(['open','in_progress','closed'] as $status)
                                @if($status !== $finding->status)
                                <li>
                                    <a class="dropdown-item change-status" href="#" data-id="{{ $finding->finding_id }}" data-status="{{ $status }}">
                                        {{ ucfirst(str_replace('_', ' ', $status)) }}
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
        const status = this.dataset.status;

        fetch(`/audit/findings/${findingId}/change-status`, {
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
                const btn = this.closest('.btn-group').querySelector('button:first-child');
                btn.textContent = res.status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
                btn.classList.remove('btn-info', 'btn-warning', 'btn-success');
                if (res.status === 'open') btn.classList.add('btn-info');
                else if (res.status === 'in_progress') btn.classList.add('btn-warning');
                else if (res.status === 'closed') btn.classList.add('btn-success');
            } else {
                alert('Error al cambiar el estado');
            }
        });
    });
});
</script>
@endsection
