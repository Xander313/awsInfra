@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center mt-5">
    <div class="col-md-7 col-lg-6">

        <div class="card shadow-lg border-0 rounded-4">
            <div class="card-header bg-warning text-dark rounded-top-4">
                <h4 class="mb-0">
                    <i class="bi bi-pencil-square"></i> Editar Organización
                </h4>
            </div>

            <div class="card-body p-4">
                <form method="POST" action="{{ route('orgs.update', $org) }}">
                    @csrf
                    @method('PUT')

                    {{-- Nombre --}}
                    <div class="form-floating mb-3">
                        <input
                            type="text"
                            name="name"
                            class="form-control"
                            id="name"
                            placeholder="Nombre de la organización"
                            value="{{ $org->name }}"
                            required
                        >
                        <label for="name">Nombre <span class="text-danger">*</span></label>
                    </div>

                    {{-- RUC --}}
                    <div class="form-floating mb-3">
                        <input
                            type="text"
                            name="ruc"
                            class="form-control"
                            id="ruc"
                            placeholder="RUC"
                            value="{{ $org->ruc }}"
                        >
                        <label for="ruc">RUC</label>
                    </div>

                    {{-- Industria --}}
                    <div class="form-floating mb-4">
                        <input
                            type="text"
                            name="industry"
                            class="form-control"
                            id="industry"
                            placeholder="Industria"
                            value="{{ $org->industry }}"
                        >
                        <label for="industry">Industria</label>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('orgs.index') }}" class="btn btn-outline-secondary">
                            Cancelar
                        </a>

                        <button type="submit" class="btn btn-warning px-4">
                            <i class="bi bi-check-circle"></i> Actualizar
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
