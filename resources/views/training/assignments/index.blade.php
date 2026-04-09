@extends('layouts.app')

@section('active_key', 'training_assignments')

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
                        <th class="text-center">Acciones</th>
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
                                {{ $assignment->assigned_at?->format('d/m/Y') ?? '—' }}
                            </td>

                            <td class="text-center">
                                {{ $assignment->due_at?->format('d/m/Y') ?? '—' }}
                            </td>

                            <td class="text-center">
                                @php
                                    $status = strtoupper((string) ($assignment->status ?? 'PENDING'));
                                    $statusClasses = [
                                        'PENDING' => 'bg-warning text-dark',
                                        'COMPLETED' => 'bg-success',
                                        'EXPIRED' => 'bg-danger',
                                    ][$status] ?? 'bg-secondary';

                                    $statusLabels = [
                                        'PENDING' => 'Pendiente',
                                        'COMPLETED' => 'Completado',
                                        'EXPIRED' => 'Vencido',
                                    ];
                                @endphp

                                <span class="badge {{ $statusClasses }}">
                                    {{ $statusLabels[$status] ?? ucfirst($assignment->status ?? 'Pendiente') }}
                                </span>
                            </td>

                            <td class="text-center">
                                <div class="d-inline-flex gap-2">
                                    <a href="{{ route('training.assignments.show', $assignment) }}"
                                       class="btn btn-sm btn-outline-secondary">
                                        Ver
                                    </a>
                                    <a href="{{ route('training.assignments.edit', $assignment) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        Editar
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6"
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
