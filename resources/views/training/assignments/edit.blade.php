@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center mt-5">
    <div class="col-md-7 col-lg-6">

        <div class="card shadow-lg border-0 rounded-4">

            <div class="card-header bg-warning text-dark rounded-top-4">
                <h4 class="mb-0">
                    <i class="bi bi-pencil-square"></i> Editar Asignación
                </h4>
            </div>

            <div class="card-body p-4">
                <form method="POST"
                      action="{{ route('training.assignments.update', $assignment) }}">
                    @csrf
                    @method('PUT')

                    {{-- Usuario (solo lectura) --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Usuario</label>
                        <input type="text"
                               class="form-control"
                               value="{{ $assignment->user->full_name }}"
                               disabled>
                    </div>

                    {{-- Curso (solo lectura) --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Curso</label>
                        <input type="text"
                               class="form-control"
                               value="{{ $assignment->course->name }}"
                               disabled>
                    </div>

                    {{-- Fecha vencimiento --}}
                    <div class="form-floating mb-3">
                        <input type="date"
                               name="due_at"
                               class="form-control"
                               value="{{ $assignment->due_at }}">
                        <label>Fecha de vencimiento</label>
                    </div>

                    {{-- Estado --}}
                    <div class="form-floating mb-4">
                        <select name="status"
                                class="form-select">
                            <option value="pending"
                                @selected($assignment->status === 'pending')>
                                Pendiente
                            </option>
                            <option value="completed"
                                @selected($assignment->status === 'completed')>
                                Completado
                            </option>
                            <option value="expired"
                                @selected($assignment->status === 'expired')>
                                Vencido
                            </option>
                        </select>
                        <label>Estado</label>
                    </div>

                    {{-- Acciones --}}
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('training.assignments.index') }}"
                           class="btn btn-outline-secondary">
                            Cancelar
                        </a>

                        <button class="btn btn-warning px-4">
                            <i class="bi bi-save"></i> Actualizar
                        </button>
                    </div>

                </form>
            </div>

        </div>

    </div>
</div>
@endsection
