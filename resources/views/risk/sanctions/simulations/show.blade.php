@extends('layouts.app')

@section('title', 'Detalle de simulación')
@section('active_key', 'sanctions')

@section('page_header')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
        <h2 class="text-xl font-bold">Detalle de simulación</h2>
    </div>

    <div class="flex gap-2 flex-wrap">
        @if ($simulation->incident_id)
            <a href="{{ route('risk.ui.incidents.show', $simulation->incident_id) }}"
               class="bg-white hover:bg-gray-50 text-gray-700 border px-4 py-2 rounded flex items-center gap-2">
                <i class="bi bi-arrow-return-left"></i>
                Volver al incidente
            </a>
        @endif


        <a href="{{ route('risk.ui.sanctions.simulations.report', $simulation) }}"
           target="_blank"
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded flex items-center gap-2">
            <i class="bi bi-file-earmark-text"></i>
            Ver informe 
        </a>
        
        <a href="{{ route('risk.ui.sanctions.simulations.index') }}"
           class="bg-white hover:bg-gray-50 text-gray-700 border px-4 py-2 rounded flex items-center gap-2">
            <i class="bi bi-arrow-left"></i>
            Volver al historial
        </a>


    </div>
</div>
@endsection

@section('content')
@php
    $general = $wizardState['general'] ?? [];
    $cdi = $wizardState['cdi'] ?? [];
    $ndv = $wizardState['ndv'] ?? [];
    $components = $resultSummary['components'] ?? [];
    $metrics = $resultSummary['metrics'] ?? [];
    $monteCarlo = $resultSummary['monte_carlo'] ?? [];
    $dataTypeLabels = data_get($referenceMaps, 'data_types', []);
    $impactLevels = data_get($referenceMaps, 'impact_levels', []);
    $documentationData = $documentation ?? [];
@endphp

<div id="sanctionSavedReport" class="space-y-4">
    @if ($simulation->officialForIncident)
        <div class="alert alert-success mb-0">
            Esta simulación está marcada como referencia oficial del incidente
            <a href="{{ route('risk.ui.incidents.show', $simulation->officialForIncident) }}" class="alert-link ms-1">
                {{ $simulation->officialForIncident->incident_code }}
            </a>.
        </div>
    @endif

    @if ($simulation->incident_id)
        <div class="alert alert-info mb-0">
            Esta simulación nació desde el incidente
            <span class="fw-semibold">
                {{ data_get($simulation->incident_snapshot, 'incident_code') ?: ('#' . $simulation->incident_id) }}
                @if (data_get($simulation->incident_snapshot, 'title'))
                    - {{ data_get($simulation->incident_snapshot, 'title') }}
                @endif
            </span>.
            <a href="{{ route('risk.ui.incidents.show', $simulation->incident_id) }}" class="alert-link ms-1">
                Ver incidente origen
            </a>
        </div>
    @endif

    <div class="bg-white border rounded p-4">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="small text-muted">Registro</div>
                <div class="fw-semibold">#{{ $simulation->simulation_id }}</div>
            </div>
            <div class="col-md-3">
                <div class="small text-muted">Fecha</div>
                <div class="fw-semibold">{{ optional($simulation->created_at)->format('d/m/Y H:i') }}</div>
            </div>
            <div class="col-md-3">
                <div class="small text-muted">Usuario</div>
                <div class="fw-semibold">{{ $simulation->created_by_user_name ?: 'Usuario no disponible' }}</div>
            </div>
            <div class="col-md-3">
                <div class="small text-muted">Organización</div>
                <div class="fw-semibold">{{ $simulation->org_name ?: 'Sin organización asociada' }}</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="bg-white border rounded p-4 h-100">
                <div class="fw-semibold mb-3">Resumen del caso</div>
                <div class="row g-3 small">
                    <div class="col-md-6">
                        <div class="text-muted">Entidad</div>
                        <div class="fw-semibold text-capitalize">{{ $general['entity_type'] ?? 'No definido' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted">Rol</div>
                        <div class="fw-semibold text-capitalize">{{ $general['company_role'] ?? 'No definido' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted">Caso</div>
                        <div class="fw-semibold">{{ $general['case_name'] ?? 'Sin etiqueta' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted">Referencia económica</div>
                        <div class="fw-semibold">
                            @if (($general['entity_type'] ?? null) === 'privada')
                                USD {{ number_format((float) ($general['business_volume_usd'] ?? 0), 2, '.', ',') }}
                            @else
                                SBU {{ number_format((float) ($general['sbu_reference'] ?? 0), 2, '.', ',') }}
                            @endif
                        </div>
                    </div>
                    <div class="col-12"><hr class="my-1"></div>
                    <div class="col-12">
                        <div class="text-muted">Infracción seleccionada</div>
                        <div class="fw-semibold">{{ $cdi['label'] ?? 'No definido' }}</div>
                        <div class="small text-muted">{{ $cdi['severity_label'] ?? 'Sin categoría' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted">PDI</div>
                        <div class="fw-semibold">{{ data_get($components, 'pdi.no_count', 0) }}/{{ data_get($components, 'pdi.total_questions', 0) }} brechas</div>
                        <div class="small text-muted">{{ data_get($components, 'pdi.detail') }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted">INT</div>
                        <div class="fw-semibold">{{ data_get($components, 'int.label', 'No definido') }}</div>
                        <div class="small text-muted">{{ data_get($components, 'int.detail') }}</div>
                    </div>
                    <div class="col-12">
                        <div class="text-muted">Naturaleza de la vulneración</div>
                        <div class="small">Confidencialidad: {{ data_get($impactLevels, data_get($ndv, 'confidentiality_impact') . '.label', 'No definido') }}</div>
                        <div class="small">Integridad: {{ data_get($impactLevels, data_get($ndv, 'integrity_impact') . '.label', 'No definido') }}</div>
                        <div class="small">Disponibilidad: {{ data_get($impactLevels, data_get($ndv, 'availability_impact') . '.label', 'No definido') }}</div>
                        <div class="small">
                            Tipos de datos:
                            {{ collect($ndv['data_types'] ?? [])->map(fn ($value) => data_get($dataTypeLabels, $value . '.label', $value))->join(', ') ?: 'No definido' }}
                        </div>
                        <div class="small">Titulares afectados: {{ $ndv['data_subject_count'] ?? 'No definido' }}</div>
                        <div class="small">Volumen de datos afectados: {{ isset($ndv['data_volume_amount']) ? number_format((float) $ndv['data_volume_amount'], 2, '.', ',') : 'No definido' }}</div>
                        <div class="small">Grupos vulnerables: {{ data_get($ndv, 'vulnerable_groups') ? 'Sí' : 'No' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="bg-white border rounded p-4 h-100 bg-light">
                <div class="fw-semibold mb-3">Desglose del cálculo</div>
                <div class="vstack gap-3">
                    @foreach ($metrics as $metric)
                        <div class="border rounded bg-white p-3">
                            <div class="d-flex justify-content-between align-items-center gap-2">
                                <span class="badge text-bg-light border">{{ $metric['code'] }}</span>
                                <span class="small text-muted">{{ $metric['label'] }}</span>
                            </div>
                            <div class="fw-semibold mt-2">{{ $metric['value'] }}</div>
                            <div class="small text-muted mt-1">{{ $metric['detail'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="bg-white border rounded p-4">
                <div class="fw-semibold mb-3">Distribución probabilística</div>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="border rounded p-3 bg-light h-100">
                            <div class="small text-muted">Mínimo</div>
                            <div class="fw-semibold mt-1">{{ data_get($monteCarlo, 'summary.formatted_minimum', 'USD 0.00') }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3 bg-light h-100">
                            <div class="small text-muted">Media</div>
                            <div class="fw-semibold mt-1">{{ data_get($monteCarlo, 'summary.formatted_mean', 'USD 0.00') }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3 bg-light h-100">
                            <div class="small text-muted">Máximo</div>
                            <div class="fw-semibold mt-1">{{ data_get($monteCarlo, 'summary.formatted_maximum', 'USD 0.00') }}</div>
                        </div>
                    </div>
                </div>

                <div class="border rounded p-3 bg-light-subtle">
                    <div class="fw-semibold small mb-2">Histograma guardado</div>
                    <div style="height: 320px;">
                        <canvas id="savedMonteCarloHistogram"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="bg-white border rounded p-4 h-100">
                <div class="fw-semibold mb-3">Metodología congelada</div>
                <div class="small text-muted mb-3">{{ data_get($documentationData, 'source.summary') }}</div>
                <div class="small text-muted mb-3">
                    Este detalle conserva el resultado original y los supuestos visibles al momento del guardado. No se recalcula sobre coeficientes posteriores.
                </div>
                <div class="vstack gap-2 small text-muted">
                    @foreach ($assumptions as $assumption)
                        <div>{{ $assumption }}</div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    (function () {
        const canvas = document.getElementById('savedMonteCarloHistogram');
        const labels = @json(data_get($monteCarlo, 'histogram.labels', []));
        const values = @json(data_get($monteCarlo, 'histogram.frequencies', []));

        if (!canvas || typeof Chart === 'undefined' || labels.length === 0 || values.length === 0) {
            return;
        }

        Chart.defaults.font.family = "'Montserrat', ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial";
        Chart.defaults.color = '#6b7280';

        new Chart(canvas.getContext('2d'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: 'rgba(37, 99, 235, 0.72)',
                    borderColor: '#1d4ed8',
                    borderWidth: 1,
                    barPercentage: 1,
                    categoryPercentage: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        title: { display: true, text: 'Rango de multa en USD' },
                        ticks: { maxRotation: 0, autoSkip: true, maxTicksLimit: 6 },
                        grid: { display: false }
                    },
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'Frecuencia' },
                        ticks: { precision: 0 }
                    }
                }
            }
        });
    })();
</script>
@endpush
