@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center mt-5">
    <div class="col-md-7 col-lg-6">

        <div class="card shadow-lg border-0 rounded-4">

            {{-- Header --}}
            <div class="card-header bg-warning text-dark rounded-top-4">
                <h4 class="mb-0">
                    <i class="bi bi-pencil-square"></i> Editar Curso
                </h4>
            </div>

            {{-- Body --}}
            <div class="card-body p-4">
                <form method="POST"
                      action="{{ route('training.courses.update', $course) }}">
                    @csrf
                    @method('PUT')

                    {{-- Nombre --}}
                    <div class="form-floating mb-3">
                        <input
                            type="text"
                            name="name"
                            class="form-control"
                            id="name"
                            placeholder="Nombre del curso"
                            value="{{ old('name', $course->name) }}"
                            required
                        >
                        <label for="name">
                            Nombre <span class="text-danger">*</span>
                        </label>
                    </div>

                    {{-- Renovación --}}
                    <div class="form-floating mb-3">
                        <input
                            type="number"
                            name="renewal_days"
                            class="form-control"
                            id="renewal_days"
                            placeholder="Renovación"
                            value="{{ old('renewal_days', $course->renewal_days) }}"
                            min="1"
                        >
                        <label for="renewal_days">
                            Renovación (días)
                        </label>
                    </div>

                    {{-- Obligatorio --}}
                    <div class="form-check mb-4">
                        <input
                            type="checkbox"
                            name="mandatory_flag"
                            value="1"
                            class="form-check-input"
                            id="mandatory_flag"
                            {{ old('mandatory_flag', $course->mandatory_flag) ? 'checked' : '' }}
                        >
                        <label class="form-check-label fw-semibold"
                               for="mandatory_flag">
                            Curso obligatorio
                        </label>
                    </div>

                    {{-- Acciones --}}
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('training.courses.index') }}"
                           class="btn btn-outline-secondary">
                            Cancelar
                        </a>

                        <button type="submit"
                                class="btn btn-warning px-4">
                            <i class="bi bi-check-circle"></i> Actualizar
                        </button>
                    </div>

                </form>
            </div>
        </div>

    </div>
</div>
@endsection
