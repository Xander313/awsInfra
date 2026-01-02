@extends('layouts.app')

@section('content')
<div class="container mt-5">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="bi bi-mortarboard"></i> Cursos de Capacitación
        </h2>

        <a href="{{ route('training.courses.create') }}"
           class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Nuevo Curso
        </a>
    </div>

    {{-- Card --}}
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-0">

            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nombre</th>
                        <th class="text-center">Obligatorio</th>
                        <th class="text-center">Renovación (días)</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($courses as $course)
                        <tr>
                            <td class="fw-semibold">
                                {{ $course->name }}
                            </td>

                            <td class="text-center">
                                @if($course->mandatory_flag)
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle"></i> Sí
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        <i class="bi bi-dash-circle"></i> No
                                    </span>
                                @endif
                            </td>

                            <td class="text-center">
                                {{ $course->renewal_days ?? '—' }}
                            </td>

                            <td class="text-center">
                                <div class="btn-group btn-group-sm" role="group">

                                    {{-- Editar --}}
                                    <a href="{{ route('training.courses.edit', $course) }}"
                                       class="btn btn-outline-warning"
                                       title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    {{-- Eliminar --}}
                                    <form action="{{ route('training.courses.destroy', $course) }}"
                                          method="POST"
                                          onsubmit="return confirm('¿Eliminar este curso?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-outline-danger"
                                                title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4"
                                class="text-center py-4 text-muted">
                                No existen cursos registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>

        </div>
    </div>

</div>
@endsection
