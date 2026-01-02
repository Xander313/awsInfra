@extends('layouts.app')

@section('title', 'RISK & DPIA')
@section('active_key', 'risks')

@section('page_header')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
        <h2 class="text-xl font-bold">RISK & DPIA</h2>
        <p class="text-sm text-gray-500">Gestión de riesgos y evaluación DPIA (Fase 9)</p>
    </div>

    <div class="flex gap-2">
        <a href="{{ url('/risk/ui/risks') }}"
           class="bg-slate-900 hover:bg-slate-800 text-white px-4 py-2 rounded flex items-center gap-2">
            <i class="fa-solid fa-shield-halved"></i>
            Riesgos
        </a>
        <a href="{{ url('/risk/ui/dpias') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded flex items-center gap-2">
            <i class="fa-solid fa-clipboard-check"></i>
            DPIA
        </a>
    </div>
</div>
@endsection

@section('content')
<link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>

<div class="bg-white border rounded p-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <a href="{{ url('/risk/ui/risks') }}" class="border rounded-lg p-4 hover:bg-gray-50">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-lg bg-slate-900 text-white flex items-center justify-center">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <div>
                    <div class="font-semibold">Riesgos</div>
                    <div class="text-sm text-gray-500">CRUD de risk.risk (tipo, estado, descripción)</div>
                </div>
            </div>
        </a>

        <a href="{{ url('/risk/ui/dpias') }}" class="border rounded-lg p-4 hover:bg-gray-50">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-lg bg-blue-600 text-white flex items-center justify-center">
                    <i class="fa-solid fa-clipboard-check"></i>
                </div>
                <div>
                    <div class="font-semibold">DPIA</div>
                    <div class="text-sm text-gray-500">CRUD de risk.dpia por actividad (pa_id) + N:M con riesgos</div>
                </div>
            </div>
        </a>
    </div>

    <div class="mt-4 text-sm text-gray-600">
        <div class="font-semibold text-gray-800 mb-1">Nota de integración</div>
        <p>
            Estas vistas están pensadas para trabajar con los endpoints ya creados:
            <code class="px-1 py-0.5 bg-gray-100 border rounded">/risk/risks</code>,
            <code class="px-1 py-0.5 bg-gray-100 border rounded">/risk/dpias</code>, etc.
        </p>
        <p class="mt-1">
            Si aún no creaste rutas UI, agrega (ejemplo) en <code class="px-1 py-0.5 bg-gray-100 border rounded">routes/web.php</code>:
        </p>
        <pre class="mt-2 text-xs bg-gray-900 text-gray-100 rounded p-3 overflow-x-auto"><code>Route::view('/risk/ui', 'risk.index');
Route::view('/risk/ui/risks', 'risk.risks.index');
Route::view('/risk/ui/dpias', 'risk.dpias.index');</code></pre>
    </div>
</div>
@endsection
