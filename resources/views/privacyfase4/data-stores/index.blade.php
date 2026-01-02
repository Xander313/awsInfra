@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">
        @isset($system)
            Almacenes de Datos del Sistema: {{ $system->name }}
        @else
            Almacenes de Datos
        @endisset
    </h1>
    <div class="mb-3">
        <a href="{{ route('systems.index') }}" class="btn btn-secondary">
            Regresar a Sistemas
        </a>
    </div>

    {{-- 🔍 FILTROS --}}
    <form id="formFiltro" method="GET" action="{{ route('data-stores.index') }}" class="card card-body mb-4">
        <div class="row g-3">

            {{-- Solo mostrar select de sistemas si no viene del subrecurso --}}
            @unless(isset($system))
            <div class="col-md-4">
                <label class="form-label"><b>Sistema</b></label>
                <select name="system_id" class="form-select">
                    <option value="">-- Seleccione un sistema --</option>
                    @foreach($systems as $sys)
                        <option value="{{ $sys->system_id }}"
                            {{ request('system_id') == $sys->system_id ? 'selected' : '' }}>
                            {{ $sys->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endunless

            <div class="col-md-2 d-grid gap-2">
                <button class="btn btn-primary">Filtrar</button>
                <a href="{{ route('data-stores.index') }}" class="btn btn-secondary">Limpiar</a>
            </div>
        </div>
    </form>

            {{--
        <div class="mb-3">
            <a href="{{ isset($system) ? route('data-stores.create', ['system_id' => $system->system_id]) : route('data-stores.create') }}" 
            class="btn btn-primary">
                Crear Almacén de Datos
            </a>
        </div>
        --}}



    @if($dataStores->count() > 0)
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Sistema</th>
                    <th>Nombre</th>
                    <th>Tipo</th>
                    <th>Ubicación</th>
                    <th>Cifrado</th>
                    <th>Respaldo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dataStores as $dataStore)
                <tr>
                    <td>{{ $dataStore->store_id }}</td>
                    <td>{{ $dataStore->system->name ?? 'N/A' }}</td>
                    <td>{{ $dataStore->name }}</td>
                    <td>{{ $dataStore->store_type }}</td>
                    <td>{{ $dataStore->location }}</td>
                    <td>{{ $dataStore->encryption_flag ? 'Sí' : 'No' }}</td>
                    <td>{{ $dataStore->backup_flag ? 'Sí' : 'No' }}</td>
                    <td>
                        <a href="{{ route('data-stores.edit', $dataStore->store_id) }}" class="btn btn-sm btn-warning">Editar</a>

                        <form action="{{ route('data-stores.destroy', $dataStore->store_id) }}" method="POST" class="form-eliminar d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="alert alert-info">No hay almacenes de datos registrados.</div>
    @endif
</div>

{{-- SWEETALERT: MENSAJES CRUD --}}
@if(session('alert'))
<script>
Swal.fire({
    icon: '{{ session('alert') === 'deleted' ? 'success' : 'success' }}',
    title: '{{ session('alert') === 'created' ? 'Registro exitoso' : (session('alert') === 'updated' ? 'Actualización exitosa' : 'Eliminado') }}',
    text: "{{ session('message') }}",
    confirmButtonColor: '#198754',
    confirmButtonText: 'Aceptar'
});
</script>
@endif

{{-- SWEETALERT: CONFIRMACIÓN ELIMINAR --}}
<script>
$(document).ready(function () {
    $('.form-eliminar').on('submit', function (e) {
        e.preventDefault();
        const form = this;

        Swal.fire({
            title: '¿Está seguro?',
            text: 'Este almacén de datos será eliminado.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
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

{{-- VALIDACIÓN DE FILTROS --}}
<script>
$(document).ready(function () {
    $('#formFiltro').on('submit', function (e) {
        const systemId = $('select[name="system_id"]').val();

        if (!systemId) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Filtros vacíos',
                text: 'Debe ingresar al menos un criterio para filtrar.',
                confirmButtonColor: '#198754',
                confirmButtonText: 'Aceptar'
            });
        }
    });
});
</script>
@endsection
