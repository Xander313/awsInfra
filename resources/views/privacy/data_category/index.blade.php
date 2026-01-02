{{-- resources/views/privacy/data_category/index.blade.php --}}
@extends('layouts.app')
@section('active_key', 'privacy_catalogs')


@section('title', 'Categorías de Datos')

@section('content')
<div class="mb-4">
    <a href="{{ route('privacy.data_category.create') }}" class="btn btn-success">Nuevo</a>
</div>

@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

<table class="table table-striped table-hover">
    <thead>
        <tr>
            <th>ID</th>
            <th>Código</th>
            <th>Nombre</th>
            <th>Sensible</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach($dataCategories as $dc)
        <tr>
            <td>{{ $dc->data_cat_id }}</td>
            <td>{{ $dc->code }}</td>
            <td>{{ $dc->name }}</td>
            <td>{{ $dc->is_sensitive ? 'Sí' : 'No' }}</td>
            <td>
                <a href="{{ route('privacy.data_category.edit', $dc->data_cat_id) }}" class="btn btn-primary btn-sm">Editar</a>
                <form action="{{ route('privacy.data_category.destroy', $dc->data_cat_id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que deseas eliminar?')">Eliminar</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
