@extends('layouts.app')

@section('title', 'Destinatarios')
@section('active_key', 'recipients')


@section('content')
<div class="container">
    <h1 class="mb-4">Destinatarios</h1>

    {{-- 🔍 FILTROS --}}
    <form method="GET" action="{{ route('recipients.index') }}" class="card card-body mb-4">
        <div class="row g-3">

            <div class="col-md-4">
                <label class="form-label"><b>Organización</b></label>
                <select name="org_id" class="form-select">
                    <option value="">-- Organización --</option>
                    @foreach($orgs as $org)
                        <option value="{{ $org->org_id }}"
                            {{ request('org_id') == $org->org_id ? 'selected' : '' }}>
                            {{ $org->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label"><b>Nombre</b></label>
                <input type="text"
                       name="name"
                       class="form-control"
                       placeholder="Buscar por nombre"
                       value="{{ request('name') }}">
            </div>

            <div class="col-md-4 d-grid gap-2 mt-4">
                <button type="submit" class="btn btn-primary">
                    Filtrar
                </button>
                <a href="{{ route('recipients.index') }}" class="btn btn-secondary">
                    Limpiar
                </a>
            </div>

        </div>
    </form>

    <div class="mb-3">
        <a href="{{ route('recipients.create') }}" class="btn btn-primary">
            Crear Destinatario
        </a>
    </div>

    @if($recipients->count() > 0)
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Organización</th>
                    <th>Nombre</th>
                    <th>Tipo</th>
                    <th>Email</th>
                    <th>¿Tercero?</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recipients as $recipient)
                <tr>
                    <td>{{ $recipient->recipient_id }}</td>
                    <td>{{ $recipient->org->name ?? 'N/A' }}</td>
                    <td>{{ $recipient->name }}</td>
                    <td>{{ $recipient->recipient_type }}</td>
                    <td>{{ $recipient->contact_email }}</td>
                    <td>{{ $recipient->is_third_party ? 'Sí' : 'No' }}</td>
                    <td>
                        <a href="{{ route('recipients.edit', $recipient->recipient_id) }}"
                           class="btn btn-sm btn-warning">
                            Editar
                        </a>

                        <form action="{{ route('recipients.destroy', $recipient->recipient_id) }}"
                              method="POST"
                              class="form-eliminar"
                              style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">
                                Eliminar
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="alert alert-info">
            No hay destinatarios registrados.
        </div>
    @endif
</div>

{{-- CONFIRMACIÓN ELIMINAR --}}
<script>
$(document).ready(function () {
    $('.form-eliminar').on('submit', function (e) {
        e.preventDefault();
        const form = this;

        Swal.fire({
            title: '¿Estás seguro?',
            text: 'Este destinatario será eliminado',
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

{{-- MENSAJES DEL CONTROLADOR --}}
@if(session('alert'))
<script>
Swal.fire({
    icon: '{{ session('alert') === 'duplicate' ? 'error' : 'success' }}',
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
