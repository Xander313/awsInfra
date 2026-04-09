@extends('layouts.app')

@section('title', 'Editar incidente')
@section('active_key', 'incidents')

@section('page_header')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
        <h2 class="text-xl font-bold">Editar incidente</h2>
        <p class="text-sm text-gray-500">Actualiza el registro operativo del incidente sin alterar aún el módulo de cálculo sancionatorio.</p>
    </div>

    <div class="flex gap-2">
        <a href="{{ route('risk.ui.incidents.show', $incident) }}"
           class="bg-white hover:bg-gray-50 text-gray-700 border px-4 py-2 rounded">
            Ver detalle
        </a>
    </div>
</div>
@endsection

@section('content')
    @include('risk.incidents.partials.form', [
        'mode' => 'edit',
        'action' => route('risk.ui.incidents.update', $incident),
        'method' => 'PUT',
    ])
@endsection
