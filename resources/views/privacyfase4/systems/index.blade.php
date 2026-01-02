@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Sistemas</h2>

    {{-- 🔍 FILTROS (DISEÑO TIPO CARD) --}}
    <form method="GET" action="{{ route('systems.index') }}" class="card card-body mb-4">
        <div class="row g-3">

            <div class="col-md-4">
                <label class="form-label"><b>Nombre</b></label>
                <input type="text"
                       name="name"
                       class="form-control"
                       placeholder="Buscar por nombre"
                       value="{{ request('name') }}">
            </div>

            <div class="col-md-4">
                <label class="form-label"><b>Tipo</b></label>
                <input type="text"
                       name="type"
                       class="form-control"
                       placeholder="Buscar por tipo"
                       value="{{ request('type') }}">
            </div>

            <div class="col-md-3">
                <label class="form-label"><b>Criticidad</b></label>
                <select name="criticality" class="form-select">
                    <option value="">-- Criticidad --</option>
                    <option value="Alta" {{ request('criticality') == 'Alta' ? 'selected' : '' }}>Alta</option>
                    <option value="Media" {{ request('criticality') == 'Media' ? 'selected' : '' }}>Media</option>
                    <option value="Baja" {{ request('criticality') == 'Baja' ? 'selected' : '' }}>Baja</option>
                </select>
            </div>

            <div class="col-md-1 d-grid gap-2 mt-4">
                <button type="submit" class="btn btn-primary">
                    Filtrar
                </button>
                <a href="{{ route('systems.index') }}" class="btn btn-secondary">
                    Limpiar
                </a>
            </div>

        </div>
    </form>

    <a href="{{ route('systems.create') }}" class="btn btn-primary mb-3">
        Nuevo Sistema
    </a>
    <a href="{{ route('data-stores.index') }}" class="btn btn-sm btn-success">
        Datos del Almacén
    </a>

    @if($systems->count() > 0)
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Organización</th>
                    <th>Nombre</th>
                    <th>Tipo</th>
                    <th>Hosting</th>
                    <th>Responsable</th>
                    <th>Criticidad</th>
                    <th>Descripción</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($systems as $system)
                <tr>
                    <td>{{ $system->system_id }}</td>
                    <td>{{ $system->organization->name ?? 'Sin organización' }}</td>
                    <td>{{ $system->name }}</td>
                    <td>{{ $system->type }}</td>
                    <td>{{ $system->hosting }}</td>
                    <td>{{ $system->owner->full_name ?? 'Sin responsable' }}</td>
                    <td>{{ $system->criticality }}</td>
                    <td>{{ $system->description }}</td>
                    <td>
                        <a href="{{ route('systems.edit', $system->system_id) }}"
                           class="btn btn-sm btn-warning">
                            Editar
                        </a>

                        <form action="{{ route('systems.destroy', $system->system_id) }}"
                                method="POST"
                                class="form-eliminar d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">
                                Eliminar
                            </button>
                            <a href="{{ route('data-stores.create', ['system_id' => $system->system_id]) }}" class="btn btn-sm btn-success">
                                Agregar Almacén
                            </a>


                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="alert alert-info">
            No hay sistemas registrados.
        </div>
    @endif
</div>

{{-- CONFIRMACIÓN DE ELIMINAR --}}
<script>
$(document).ready(function () {
    $('.form-eliminar').on('submit', function (e) {
        e.preventDefault();
        const form = this;

        Swal.fire({
            title: '¿Estás seguro?',
            text: 'Este sistema será eliminado',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>

{{-- MENSAJES DEL CONTROLADOR --}}
@if(session('alert'))
<script>
Swal.fire({
    icon: '{{ session('alert') === 'error' ? 'error' : 'success' }}',
    title: @json(
        session('alert') === 'created' ? 'Registro exitoso' :
        (session('alert') === 'updated' ? 'Actualización exitosa' :
        (session('alert') === 'deleted' ? 'Eliminado' :
        'Acción no permitida'))
    ),
    text: @json(session('message')),
    confirmButtonColor: '#198754',
    confirmButtonText: 'Aceptar'
});
</script>
@endif

@endsection