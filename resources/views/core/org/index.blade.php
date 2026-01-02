@extends('layouts.app')

@section('content')
<div class="container mt-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="bi bi-buildings"></i> Organizaciones
        </h2>

        <a href="{{ route('orgs.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Nueva Organización
        </a>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-0">

            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nombre</th>
                        <th>RUC</th>
                        <th>Industria</th>
                        <th class="text-center">Activa</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($orgs as $org)
                        <tr>
                            <td class="fw-semibold">
                                {{ $org->name }}
                            </td>

                            <td>
                                {{ $org->ruc ?? '—' }}
                            </td>

                            <td>
                                {{ $org->industry ?? '—' }}
                            </td>

                            <td class="text-center">
                                @if(session('org_id') == $org->org_id)
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle"></i> Activa
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        —
                                    </span>
                                @endif
                            </td>

                            <td class="text-center">
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('orgs.show', $org) }}"
                                       class="btn btn-outline-info">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    <a href="{{ route('orgs.edit', $org) }}"
                                       class="btn btn-outline-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('orgs.destroy', $org) }}" method="POST">
                                    @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger"
                                                onclick="return confirm('¿Eliminar esta organización?')">
                                                <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    @if(session('org_id') != $org->org_id)
                                        <a href="{{ route('orgs.select', $org->org_id) }}"
                                           class="btn btn-outline-success">
                                            <i class="bi bi-check2-circle"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                No existen organizaciones registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </div>
    </div>
</div>
@endsection
