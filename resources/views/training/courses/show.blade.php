@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center mt-5">
    <div class="col-md-7 col-lg-6">

        <div class="card shadow-lg border-0 rounded-4 overflow-hidden">

            {{-- Header --}}
            <div class="card-header bg-info text-white px-4 py-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-mortarboard fs-4"></i>
                    <h4 class="mb-0 fw-semibold">
                        Detalle del Curso
                    </h4>
                </div>
            </div>

            {{-- Body --}}
            <div class="card-body p-4">

                {{-- Nombre --}}
                <div class="mb-4">
                    <small class="text-muted text-uppercase">
                        Nombre
                    </small>
                    <h5 class="fw-bold mb-0">
                        {{ $course->name }}
                    </h5>
                </div>

                {{-- Obligatorio --}}
                <div class="mb-4">
                    <small class="text-muted text-uppercase">
                        Obligatorio
                    </small>
                    <p class="mb-0 fs-6">
                        @if($course->mandatory_flag)
                            <span class="badge bg-success">
                                <i class="bi bi-check-circle"></i> Sí
                            </span>
                        @else
                            <span class="badge bg-secondary">
                                <i class="bi bi-dash-circle"></i> No
                            </span>
                        @endif
                    </p>
                </div>

                {{-- Renovación --}}
                <div class="mb-4">
                    <small class="text-muted text-uppercase">
                        Renovación (días)
                    </small>
                    <p class="mb-0 fs-6">
                        {{ $course->renewal_days ?? '—' }}
                    </p>
                </div>

                <hr>

                {{-- Acciones --}}
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('training.courses.index') }}"
                       class="btn btn-outline-secondary rounded-pill px-4">
                        <i class="bi bi-arrow-left"></i> Volver
                    </a>

                    <a href="{{ route('training.courses.edit', $course) }}"
                       class="btn btn-warning rounded-pill px-4">
                        <i class="bi bi-pencil"></i> Editar
                    </a>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection
