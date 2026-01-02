@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center mt-5">
    <div class="col-md-7 col-lg-6">

        <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
            {{-- Header --}}
            <div class="card-header bg-info text-white px-4 py-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-building fs-4"></i>
                    <h4 class="mb-0 fw-semibold">Detalle de la Organización</h4>
                </div>
            </div>

            {{-- Body --}}
            <div class="card-body p-4">

                <div class="mb-4">
                    <small class="text-muted text-uppercase">Nombre</small>
                    <h5 class="fw-bold mb-0">{{ $org->name }}</h5>
                </div>

                <div class="mb-4">
                    <small class="text-muted text-uppercase">RUC</small>
                    <p class="mb-0 fs-6">
                        {{ $org->ruc ?? '—' }}
                    </p>
                </div>

                <div class="mb-4">
                    <small class="text-muted text-uppercase">Industria</small>
                    <p class="mb-0 fs-6">
                        {{ $org->industry ?? '—' }}
                    </p>
                </div>
                <div class="mb-4">
                    <small class="text-muted text-uppercase">Fecha de Creación</small>
                    <p class="mb-0 fs-6">
                        {{ $org->created_at ? $org->created_at->format('d/m/Y H:i') : '—' }}
                    </p>
                </div>


                <hr>

                {{-- Acciones --}}
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('orgs.index') }}"
                       class="btn btn-outline-secondary rounded-pill px-4">
                        <i class="bi bi-arrow-left"></i> Volver
                    </a>

                    @if(session('org_id') != $org->org_id)
                        <a href="{{ route('orgs.select', $org->org_id) }}"
                           class="btn btn-success rounded-pill px-4">
                            <i class="bi bi-check-circle"></i> Activar
                        </a>
                    @else
                        <span class="badge bg-success rounded-pill px-4 py-2 fs-6">
                            <i class="bi bi-check-circle"></i> Organización Activa
                        </span>
                    @endif
                </div>

            </div>
        </div>

    </div>
</div>
@endsection
