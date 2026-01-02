@extends('layouts.app')

@section('title', 'Crear Categoría de Datos')

@section('content')
<div class="card p-4 bg-white rounded-lg shadow-sm">
    <h2 class="mb-4 font-bold text-lg">Crear Categoría de Datos</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('privacy.data_category.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Código</label>
            <input type="text" name="code" class="form-control" value="{{ old('code') }}" required>
        </div>
        <div class="mb-3">
            <label>Nombre</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
        </div>
        <div class="mb-3">
            <label>Sensible</label>
            <select name="is_sensitive" class="form-control">
                <option value="0" {{ old('is_sensitive') == 0 ? 'selected' : '' }}>No</option>
                <option value="1" {{ old('is_sensitive') == 1 ? 'selected' : '' }}>Sí</option>
            </select>
        </div>
        <div class="mb-3">
            <label>Descripción</label>
            <textarea name="description" class="form-control">{{ old('description') }}</textarea>
        </div>
        <button type="submit" class="btn btn-success">Guardar</button>
        <a href="{{ route('privacy.data_category.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
