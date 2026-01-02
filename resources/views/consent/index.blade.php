@extends('layouts.app')

@section('content')
<div class="container mt-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="bi bi-person-vcard"></i> Titulares de Datos
        </h2>

        <a href="{{ route('data-subjects.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Nuevo Titular
        </a>
    </div>

    <!-- Buscador -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body">
            <form action="{{ route('data-subjects.index') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-8">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" 
                                   name="search" 
                                   class="form-control" 
                                   placeholder="Buscar por ID, Nombre o Correo..."
                                   value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-primary w-100">
                            Buscar
                        </button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('data-subjects.index') }}" class="btn btn-outline-secondary w-100">
                            Limpiar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Listado -->
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-0">

            @if($dataSubjects->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Identificación</th>
                                <th>Nombre Completo</th>
                                <th>Contacto</th>
                                <th>Consentimiento</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($dataSubjects as $subject)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $subject->id_type }}</div>
                                        <div class="text-muted small">{{ $subject->id_number }}</div>
                                    </td>

                                    <td>
                                        <div class="fw-semibold">{{ $subject->full_name }}</div>
                                        @if($subject->verified_level > 0)
                                            <span class="badge bg-info">
                                                Verif. Nvl {{ $subject->verified_level }}
                                            </span>
                                        @endif
                                    </td>

                                    <td>
                                        @if($subject->email)
                                            <div class="small">
                                                <i class="bi bi-envelope"></i> {{ $subject->email }}
                                            </div>
                                        @endif
                                        @if($subject->phone)
                                            <div class="small">
                                                <i class="bi bi-telephone"></i> {{ $subject->phone }}
                                            </div>
                                        @endif
                                    </td>

                                    <td>
                                        @php
                                            $activeConsent = $subject->activeConsent();
                                        @endphp
                                        @if($activeConsent)
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle"></i> Activo
                                            </span>
                                            <div class="small text-muted">
                                                {{ $activeConsent->given_at->format('d/m/Y') }}
                                            </div>
                                        @elseif($subject->consents->count() > 0)
                                            <span class="badge bg-danger">
                                                <i class="bi bi-x-circle"></i> Revocado
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                Sin registro
                                            </span>
                                        @endif
                                    </td>

                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('data-subjects.show', $subject) }}"
                                               class="btn btn-outline-info"
                                               title="Ver detalle">
                                                <i class="bi bi-eye"></i>
                                            </a>

                                            <a href="{{ route('data-subjects.edit', $subject) }}"
                                               class="btn btn-outline-warning"
                                               title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </a>

                                            @if($subject->consents->count() == 0)
                                                <form action="{{ route('data-subjects.destroy', $subject) }}" 
                                                      method="POST"
                                                      class="d-inline"
                                                      onsubmit="return confirm('¿Eliminar este titular?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-outline-danger"
                                                            title="Eliminar">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="card-footer">
                    {{ $dataSubjects->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-person-slash display-4 text-muted"></i>
                    <h4 class="mt-3 text-muted">No hay titulares registrados</h4>
                    <p class="text-muted">Comienza registrando un nuevo titular</p>
                    <a href="{{ route('data-subjects.create') }}" class="btn btn-primary mt-2">
                        <i class="bi bi-plus-circle"></i> Crear primer titular
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection