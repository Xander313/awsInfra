@extends('layouts.app')

@section('title', 'Nuevo incidente')
@section('active_key', 'incidents')

@section('page_header')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
        <h2 class="text-xl font-bold">Nuevo incidente</h2>
        <p class="text-sm text-gray-500">Captura base del expediente del incidente para su gestión dentro del dominio de riesgo.</p>
    </div>

    <div class="flex gap-2">
        <a href="{{ route('risk.ui.incidents.index') }}"
           class="bg-white hover:bg-gray-50 text-gray-700 border px-4 py-2 rounded">
            Volver al listado
        </a>
    </div>
</div>
@endsection

@section('content')
    @include('risk.incidents.partials.form', [
        'mode' => 'create',
        'action' => route('risk.ui.incidents.store'),
        'method' => 'POST',
    ])
@endsection
