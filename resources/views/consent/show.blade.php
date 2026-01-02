@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row">
        <!-- Información del Titular -->
        <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-white border-0 pt-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="bi bi-person-vcard"></i> Detalle del Titular
                        </h4>
                        <div class="btn-group">
                            <a href="{{ route('data-subjects.edit', $dataSubject) }}" 
                               class="btn btn-outline-warning">
                                <i class="bi bi-pencil"></i> Editar
                            </a>
                            <a href="{{ route('data-subjects.index') }}" 
                               class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Volver
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Identificación</h6>
                            <p class="fs-5">
                                <strong>{{ $dataSubject->id_type }}:</strong> {{ $dataSubject->id_number }}
                            </p>
                        </div>
                        
                        <div class="col-md-6">
                            <h6 class="text-muted">Verificación</h6>
                            @if($dataSubject->verified_level > 0)
                                <span class="badge bg-info fs-6">
                                    Nivel {{ $dataSubject->verified_level }}
                                </span>
                            @else
                                <span class="badge bg-secondary">Sin verificar</span>
                            @endif
                        </div>

                        <div class="col-12 mt-3">
                            <h6 class="text-muted">Nombre Completo</h6>
                            <p class="fs-4 fw-bold">{{ $dataSubject->full_name }}</p>
                        </div>

                        <div class="col-md-6 mt-3">
                            <h6 class="text-muted">Contacto</h6>
                            @if($dataSubject->email)
                                <p><i class="bi bi-envelope"></i> {{ $dataSubject->email }}</p>
                            @endif
                            @if($dataSubject->phone)
                                <p><i class="bi bi-telephone"></i> {{ $dataSubject->phone }}</p>
                            @endif
                        </div>

                        <div class="col-md-6 mt-3">
                            <h6 class="text-muted">Dirección</h6>
                            <p>{{ $dataSubject->address ?? 'No registrada' }}</p>
                        </div>

                        <div class="col-md-6 mt-3">
                            <h6 class="text-muted">Organización</h6>
                            <p>{{ $dataSubject->org->name ?? 'No disponible' }}</p>
                        </div>

                        <div class="col-md-6 mt-3">
                            <h6 class="text-muted">Fecha de Registro</h6>
                            <p>{{ $dataSubject->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Consentimientos -->
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-clipboard-check"></i> Historial de Consentimientos
                        </h5>
                        @if($dataSubject->activeConsent())
                            <span class="badge bg-success">
                                <i class="bi bi-check-circle"></i> Consentimiento Activo
                            </span>
                        @else
                            <a href="{{ route('data-subjects.consent.create', $dataSubject) }}" 
                               class="btn btn-sm btn-primary">
                                <i class="bi bi-plus-circle"></i> Registrar Consentimiento
                            </a>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    @if($consents->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Fecha Otorgado</th>
                                        <th>Fecha Revocado</th>
                                        <th>Aviso ID</th>
                                        <th>Propósito ID</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($consents as $consent)
                                        <tr>
                                            <td>{{ $consent->given_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                @if($consent->revoked_at)
                                                    {{ $consent->revoked_at->format('d/m/Y H:i') }}
                                                @else
                                                    —
                                                @endif
                                            </td>
                                            <td>{{ $consent->notice_ver_id ?? '—' }}</td>
                                            <td>{{ $consent->purpose_id ?? '—' }}</td>
                                            <td>
                                                @if($consent->revoked_at)
                                                    <span class="badge bg-danger">Revocado</span>
                                                @else
                                                    <span class="badge bg-success">Activo</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if(!$consent->revoked_at)
                                                    <form action="{{ route('data-subjects.consent.revoke', $consent) }}" 
                                                          method="POST"
                                                          class="d-inline"
                                                          onsubmit="return confirm('¿Estás seguro de revocar este consentimiento?')">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                                            <i class="bi bi-x-circle"></i> Revocar
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-clipboard-x display-4 text-muted"></i>
                            <p class="text-muted mt-2">No hay consentimientos registrados</p>
                            <a href="{{ route('data-subjects.consent.create', $dataSubject) }}" 
                               class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Registrar primer consentimiento
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar de Estadísticas -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-white border-0">
                    <h6 class="mb-0">
                        <i class="bi bi-graph-up"></i> Estadísticas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">Total Consentimientos</small>
                        <h4>{{ $consents->count() }}</h4>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Activos</small>
                        <h4 class="text-success">{{ $consents->whereNull('revoked_at')->count() }}</h4>
                    </div>
                    <div>
                        <small class="text-muted">Revocados</small>
                        <h4 class="text-danger">{{ $consents->whereNotNull('revoked_at')->count() }}</h4>
                    </div>
                </div>
            </div>

            <!-- Acciones Rápidas -->
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white border-0">
                    <h6 class="mb-0">
                        <i class="bi bi-lightning-charge"></i> Acciones Rápidas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if(!$dataSubject->activeConsent())
                            <a href="{{ route('data-subjects.consent.create', $dataSubject) }}" 
                               class="btn btn-outline-primary">
                                <i class="bi bi-clipboard-check"></i> Registrar Consentimiento
                            </a>
                        @endif
                        <a href="{{ route('data-subjects.edit', $dataSubject) }}" 
                           class="btn btn-outline-warning">
                            <i class="bi bi-pencil"></i> Editar Titular
                        </a>
                        @if($dataSubject->consents->count() == 0)
                            <form action="{{ route('data-subjects.destroy', $dataSubject) }}" 
                                  method="POST"
                                  onsubmit="return confirm('¿Eliminar este titular permanentemente?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger w-100">
                                    <i class="bi bi-trash"></i> Eliminar Titular
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Cualquier script adicional que necesites
</script>
@endpush