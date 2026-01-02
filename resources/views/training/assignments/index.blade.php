@extends('layouts.app')

@section('content')
<div class="container mt-5">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="bi bi-person-check"></i> Asignaciones de Capacitación
        </h2>

        {{-- BOTÓN CORREGIDO --}}
        <a href="{{ route('training.assignments.create') }}"
           class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Nueva asignación
        </a>
    </div>

    {{-- Card --}}
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-0">

            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Usuario</th>
                        <th>Curso</th>
                        <th class="text-center">Asignado</th>
                        <th class="text-center">Vence</th>
                        <th class="text-center">Estado</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($assignments as $assignment)
                        <tr>
                            <td class="fw-semibold">
                                {{ $assignment->user->full_name ?? '—' }}
                            </td>

                            <td>
                                {{ $assignment->course->name ?? '—' }}
                            </td>

                            <td class="text-center">
                                {{ $assignment->assigned_at ?? '—' }}
                            </td>

                            <td class="text-center">
                                {{ $assignment->due_at ?? '—' }}
                            </td>

                            <td class="text-center">
                                <span class="badge bg-warning text-dark">
                                    Pendiente
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5"
                                class="text-center py-4 text-muted">
                                No existen asignaciones registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>

        </div>
    </div>

</div>
@endsection
