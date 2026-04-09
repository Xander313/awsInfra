@extends('layouts.app')

@section('title', 'Detalle de organización')
@section('active_key', 'org')

@php
    $profile = $org->regulatoryProfile;
    $hasProfile = $profile !== null;
@endphp

@section('content')
<div class="container d-flex justify-content-center mt-5">
    <div class="col-lg-9">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="card shadow-lg border-0 rounded-4 overflow-hidden mb-4">
            <div class="card-header bg-info text-white px-4 py-3">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-building fs-4"></i>
                        <h4 class="mb-0 fw-semibold">Detalle de la Organización</h4>
                    </div>

                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('orgs.edit', $org) }}" class="btn btn-light btn-sm">
                            <i class="bi bi-pencil"></i> Editar organización
                        </a>
                        <a href="{{ route('orgs.regulatory-profile.edit', $org) }}" class="btn btn-warning btn-sm">
                            <i class="bi bi-shield-check"></i>
                            {{ $hasProfile ? 'Editar perfil regulatorio' : 'Configurar perfil regulatorio' }}
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body p-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <small class="text-muted text-uppercase">Nombre</small>
                        <h5 class="fw-bold mb-0">{{ $org->name }}</h5>
                    </div>

                    <div class="col-md-6">
                        <small class="text-muted text-uppercase">RUC</small>
                        <p class="mb-0 fs-6">{{ $org->ruc ?? '—' }}</p>
                    </div>

                    <div class="col-md-6">
                        <small class="text-muted text-uppercase">Industria</small>
                        <p class="mb-0 fs-6">{{ $org->industry ?? '—' }}</p>
                    </div>

                    <div class="col-md-6">
                        <small class="text-muted text-uppercase">Fecha de creación</small>
                        <p class="mb-0 fs-6">{{ $org->created_at ? $org->created_at->format('d/m/Y H:i') : '—' }}</p>
                    </div>
                </div>

                <hr class="my-4">

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

        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-light px-4 py-3 d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h5 class="mb-1 fw-semibold">Perfil regulatorio/económico</h5>
                    <p class="text-muted mb-0 small">
                        Este perfil permitirá alimentar el cálculo sancionatorio desde incidentes.
                    </p>
                </div>

                <span class="badge {{ $hasProfile ? 'bg-success' : 'bg-warning text-dark' }} rounded-pill px-3 py-2">
                    {{ $hasProfile ? 'Configurado' : 'No configurado' }}
                </span>
            </div>

            <div class="card-body p-4">
                @if (!$hasProfile)
                    <div class="alert alert-warning mb-4">
                        Falta configurar el perfil regulatorio/económico de esta organización. Sin este prerequisito, los incidentes no podrán alimentar correctamente el cálculo sancionatorio.
                    </div>

                    <div class="border rounded-4 p-4 bg-light">
                        <h6 class="fw-semibold mb-2">Estado actual</h6>
                        <p class="text-muted mb-3">
                            No existe todavía un perfil regulatorio registrado para esta organización.
                        </p>

                        <a href="{{ route('orgs.regulatory-profile.edit', $org) }}" class="btn btn-warning">
                            <i class="bi bi-plus-circle"></i> Configurar perfil regulatorio
                        </a>
                    </div>
                @else
                    <div class="row g-4">
                        <div class="col-md-6">
                            <small class="text-muted text-uppercase">Tipo de entidad</small>
                            <p class="mb-0 fs-6">{{ ucfirst($profile->entity_type) }}</p>
                        </div>

                        <div class="col-md-6">
                            <small class="text-muted text-uppercase">Año de referencia</small>
                            <p class="mb-0 fs-6">{{ $profile->reference_year ?: '—' }}</p>
                        </div>

                        <div class="col-md-6">
                            <small class="text-muted text-uppercase">Volumen de negocio USD</small>
                            <p class="mb-0 fs-6">
                                {{ $profile->business_volume_usd !== null ? 'USD ' . number_format((float) $profile->business_volume_usd, 2, '.', ',') : '—' }}
                            </p>
                        </div>

                        <div class="col-md-6">
                            <small class="text-muted text-uppercase">SBU de referencia</small>
                            <p class="mb-0 fs-6">
                                {{ $profile->sbu_reference !== null ? number_format((float) $profile->sbu_reference, 2, '.', ',') : '—' }}
                            </p>
                        </div>

                        <div class="col-12">
                            <small class="text-muted text-uppercase">Notas</small>
                            <p class="mb-0 fs-6">{{ $profile->notes ?: 'Sin notas registradas.' }}</p>
                        </div>

                        <div class="col-md-6">
                            <small class="text-muted text-uppercase">Última actualización</small>
                            <p class="mb-0 fs-6">{{ optional($profile->updated_at)->format('d/m/Y H:i') ?: '—' }}</p>
                        </div>

                        <div class="col-md-6">
                            <small class="text-muted text-uppercase">Actualizado por</small>
                            <p class="mb-0 fs-6">{{ $profile->updatedBy?->full_name ?: 'No disponible' }}</p>
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('orgs.regulatory-profile.edit', $org) }}" class="btn btn-outline-primary">
                            <i class="bi bi-pencil-square"></i> Editar perfil regulatorio
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
