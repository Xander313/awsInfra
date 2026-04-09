@extends('layouts.app')

@section('title', 'Asistente de cálculo de multas')
@section('active_key', 'sanctions')

@section('page_header')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
        <h2 class="text-xl font-bold">Cálculo de multas</h2>
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
    @include('risk.sanctions.partials.wizard_stepper', ['steps' => $steps, 'currentStep' => $currentStep, 'completedSteps' => $completedSteps])

    <div class="bg-white border rounded p-4">
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        @if (($wizardContext['source'] ?? null) === 'incident')
            <div class="alert alert-info">
                <div class="fw-semibold mb-1">Cálculo iniciado desde incidente</div>
                <div>
                    Este asistente fue iniciado desde el incidente
                    <span class="fw-semibold">{{ $wizardContext['incident_label'] ?? 'sin referencia' }}</span>.
                    @if (!empty($wizardContext['incident_id']))
                        <a href="{{ route('risk.ui.incidents.show', $wizardContext['incident_id']) }}" class="alert-link ms-1">
                            Volver al incidente
                        </a>
                    @endif
                </div>
                @if (!empty($wizardContext['system_name']) || !empty($wizardContext['processing_activity_name']))
                    <div class="small mt-2">
                        @if (!empty($wizardContext['system_name']))
                            Sistema: <span class="fw-semibold">{{ $wizardContext['system_name'] }}</span>.
                        @endif
                        @if (!empty($wizardContext['processing_activity_name']))
                            Actividad: <span class="fw-semibold">{{ $wizardContext['processing_activity_name'] }}</span>.
                        @endif
                    </div>
                @endif
                @if (!empty($wizardContext['preloaded_fields']))
                    <div class="small mt-2">
                        Datos precargados automáticamente: {{ implode(', ', $wizardContext['preloaded_fields']) }}.
                    </div>
                @endif
            </div>
        @endif

        @if (!empty($wizardContext['preload_warnings']))
            <div class="alert alert-warning">
                <div class="fw-semibold mb-1">Datos que requieren revisión manual</div>
                <ul class="mb-0">
                    @foreach ($wizardContext['preload_warnings'] as $warning)
                        <li>{{ $warning }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @include($stepView, ['pageData' => $pageData, 'wizardState' => $wizardState, 'wizardContext' => $wizardContext, 'currentStep' => $currentStep, 'steps' => $steps, 'resultSummary' => $resultSummary])
    </div>
</div>
@endsection
