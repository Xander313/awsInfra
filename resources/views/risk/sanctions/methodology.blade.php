@extends('layouts.app')

@section('title', 'Metodología del cálculo')
@section('active_key', 'sanctions')

@section('page_header')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
        <h2 class="text-xl font-bold">Metodología del cálculo</h2>
        <p class="text-sm text-gray-500">Trazabilidad metodológica y explicación funcional del módulo de cálculo de multas.</p>
    </div>

    <div class="flex gap-2">

        <a href="{{ route('risk.ui.sanctions') }}"
           class="bg-white hover:bg-gray-50 text-gray-700 border px-4 py-2 rounded flex items-center gap-2">
            <i class="bi bi-arrow-left"></i>
            Volver al módulo
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-4">
    <div class="bg-white border rounded p-4">
        <div class="fw-semibold mb-2">{{ data_get($documentation, 'source.title') }}</div>
        <p class="text-muted mb-3">{{ data_get($documentation, 'source.summary') }}</p>
        <ul class="mb-0 text-muted">
            @foreach (data_get($documentation, 'source.items', []) as $item)
                <li>{{ $item }}</li>
            @endforeach
        </ul>
    </div>

    <div class="bg-white border rounded p-4">
        <div class="fw-semibold mb-3">Metodología del cálculo</div>
        <div class="row g-3">
            @foreach (data_get($documentation, 'methodology', []) as $item)
                <div class="col-lg-6">
                    <div class="border rounded p-3 h-100">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge text-bg-light border">{{ $item['code'] }}</span>
                            <span class="fw-semibold">{{ $item['title'] }}</span>
                        </div>
                        <div class="small text-muted">{{ $item['description'] }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
<!--
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="bg-white border rounded p-4 h-100">
                <div class="fw-semibold mb-3">Parámetros normativos fijos</div>
                <ul class="mb-0 text-muted">
                    @foreach (data_get($documentation, 'normative_fixed_parameters', []) as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="bg-white border rounded p-4 h-100">
                <div class="fw-semibold mb-3">Parámetros configurables del modelo</div>
                <ul class="mb-0 text-muted">
                    @foreach (data_get($documentation, 'configurable_parameters', []) as $item)
                        <li><code>{{ $item }}</code></li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
-->
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="bg-white border rounded p-4 h-100">
                <div class="fw-semibold mb-3">Mapeos metodológicos internos</div>
                <div class="vstack gap-2 text-muted small">
                    @foreach (config('sanctions.assumptions', []) as $assumption)
                        <div>{{ $assumption }}</div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="bg-white border rounded p-4 h-100">
                <div class="fw-semibold mb-3">{{ data_get($documentation, 'orientation.title') }}</div>
                <ul class="mb-3 text-muted">
                    @foreach (data_get($documentation, 'orientation.items', []) as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>

                <div class="border rounded p-3 bg-light">
                    <div class="fw-semibold mb-2">{{ data_get($documentation, 'monte_carlo.title') }}</div>
                    <div class="small text-muted">{{ data_get($documentation, 'monte_carlo.summary') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
