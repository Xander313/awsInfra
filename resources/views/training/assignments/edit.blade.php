@extends('layouts.app')

@section('active_key', 'training_assignments')

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
                        @php
                            $dueAtValue = old('due_at', optional($assignment->due_at)->format('Y-m-d'));
                        @endphp
                        <input type="date"
                               name="due_at"
                               class="form-control"
                               value="{{ $dueAtValue }}">
                        <label>Fecha de vencimiento</label>
                    </div>

                    {{-- Estado --}}
                    <div class="form-floating mb-4">
                        <select name="status"
                                class="form-select">
                            <option value="PENDING"
                                @selected(strtoupper((string) $assignment->status) === 'PENDING')>
                                Pendiente
                            </option>
                            <option value="COMPLETED"
                                @selected(strtoupper((string) $assignment->status) === 'COMPLETED')>
                                Completado
                            </option>
                            <option value="EXPIRED"
                                @selected(strtoupper((string) $assignment->status) === 'EXPIRED')>
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
