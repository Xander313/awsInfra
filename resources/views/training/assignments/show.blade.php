@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center mt-5">
    <div class="col-md-7 col-lg-6">

        <div class="card shadow-lg border-0 rounded-4 overflow-hidden">

            <div class="card-header bg-info text-white px-4 py-3">
                <h4 class="mb-0">
                    <i class="bi bi-person-check"></i> Detalle de Asignación
                </h4>
            </div>

            <div class="card-body p-4">

                <div class="mb-3">
                    <small class="text-muted text-uppercase">Usuario</small>
                    <h5 class="fw-bold">{{ $assignment->user->full_name }}</h5>
                </div>

                <div class="mb-3">
                    <small class="text-muted text-uppercase">Curso</small>
                    <p class="fs-6">{{ $assignment->course->name }}</p>
                </div>

                <div class="mb-3">
                    <small class="text-muted text-uppercase">Estado</small>
                    <p class="fs-6">{{ ucfirst($assignment->status) }}</p>
                </div>

                <div class="mb-4">
                    <small class="text-muted text-uppercase">Vence</small>
                    <p class="fs-6">{{ $assignment->due_at ?? '—' }}</p>
                </div>

                <hr>

                <a href="{{ route('training.assignments.index') }}"
                   class="btn btn-outline-secondary rounded-pill px-4">
                    <i class="bi bi-arrow-left"></i> Volver
                </a>

            </div>
        </div>

    </div>
</div>
@endsection
