@php
    use Illuminate\Support\Str;
@endphp

@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            {{-- Título + volver --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-0">
                        <i class="bi bi-file-earmark-text"></i> Detalle del Documento
                    </h2>
                    <small class="text-muted">Información general y versiones registradas</small>
                </div>

                <a href="{{ route('documents.index') }}"
                   class="btn btn-light border rounded-pill px-4 shadow-sm">
                    Volver al listado
                </a>
            </div>

            {{-- Información del documento --}}
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-white border-0 rounded-top-4 px-4 py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">
                            <i class="bi bi-info-circle text-primary me-2"></i>
                            Información del documento
                        </h5>
                        <small class="text-muted">Datos principales y metadatos del registro</small>
                    </div>
                    <span class="badge bg-light text-muted border">
                        ID #{{ $document->doc_id }}
                    </span>
                </div>

                <div class="card-body px-0 pb-3 pt-0">
                    <ul class="list-group list-group-flush">

                        {{-- Título --}}
                        <li class="list-group-item px-4 py-3">
                            <span class="text-muted d-block small">Título</span>
                            <span class="fw-semibold h5">{{ $document->title }}</span>
                        </li>

                        {{-- Tipo --}}
                        <li class="list-group-item px-4 py-3">
                            <span class="text-muted d-block small">Tipo</span>
                            <span>{{ $document->doc_type ?? '—' }}</span>
                        </li>

                        {{-- Clasificación --}}
                        @if($document->classification)
                            <li class="list-group-item px-4 py-3">
                                <span class="text-muted d-block small">Clasificación</span>
                                <span class="badge bg-primary-subtle text-primary-emphasis px-2 py-1">
                                    {{ $document->classification }}
                                </span>
                            </li>
                        @endif

                        {{-- Organización --}}
                        <li class="list-group-item px-4 py-3">
                            <span class="text-muted d-block small">Organización</span>
                            <span class="fw-semibold">{{ $document->org?->name ?? 'N/A' }}</span>
                        </li>

                        {{-- Creador --}}
                        <li class="list-group-item px-4 py-3">
                            <span class="text-muted d-block small">Creador</span>
                            <span>{{ $document->creator?->full_name ?? 'N/A' }}</span>
                        </li>

                        {{-- Fecha de Creación --}}
                        <li class="list-group-item px-4 py-3">
                            <span class="text-muted d-block small">Creado en</span>
                            <span>{{ $document->created_at }}</span>
                        </li>

                    </ul>

                </div>
            </div>


            {{-- Encabezado versiones --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">
                    <i class="bi bi-layers"></i> Versiones del documento
                </h4>

                <a href="{{ route('documents.versions.create', $document->doc_id) }}"
                   class="btn btn-success rounded-pill px-4">
                    <i class="bi bi-plus-circle me-1"></i> Nueva versión
                </a>
            </div>

            {{-- Tabla de versiones --}}
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID Versión</th>
                                <th>N° Versión</th>
                                <th>Checksum</th>
                                <th class="text-center">Estado</th>
                                <th>Fecha creación</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($document->versions as $ver)
                            <tr>
                                <td class="fw-semibold">{{ $ver->doc_ver_id }}</td>
                                <td>v{{ $ver->version_no }}</td>
                                <td><code>{{ Str::limit($ver->checksum, 20) }}</code></td>
                                <td class="text-center">
                                    @if($ver->active_flag)
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle me-1"></i> Activa
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="bi bi-circle me-1"></i> Inactiva
                                        </span>
                                    @endif
                                </td>
                                <td>{{ $ver->created_at }}</td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">

                                        {{-- Descargar --}}
                                        <a href="{{ route('documents.versions.download', [$document->doc_id, $ver->doc_ver_id]) }}"
                                           class="btn btn-light btn-sm border rounded-pill px-3 shadow-sm"
                                           title="Descargar">
                                            <i class="bi bi-download text-primary"></i>
                                        </a>

                                        {{-- Marcar activa --}}
                                        @if(!$ver->active_flag)
                                            <form action="{{ route('documents.versions.activate', [$document->doc_id, $ver->doc_ver_id]) }}"
                                                  method="POST" style="display:inline-block">
                                                @csrf
                                                <button class="btn btn-light btn-sm border rounded-pill px-3 shadow-sm"
                                                        title="Marcar como activa">
                                                    <i class="bi bi-star-fill text-warning"></i>
                                                </button>
                                            </form>
                                        @endif

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    No hay versiones registradas.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
