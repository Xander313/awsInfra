@php
    $general = $wizardState['general'] ?? [];
    $cdi = $wizardState['cdi'] ?? [];
    $ndv = $wizardState['ndv'] ?? [];
    $components = $resultSummary['components'] ?? [];
    $dataTypeLabels = $pageData['dataTypesMap'] ?? [];
    $impactLevels = $pageData['impactLevelsMap'] ?? [];
    $documentation = $pageData['documentation'] ?? [];
    $monteCarlo = $resultSummary['monte_carlo'] ?? [];
@endphp

<div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-4">
    <div>
        <h3 class="h5 mb-1">Paso 7. Resultado final</h3>
        <p class="text-muted mb-0">Resultado determinista del caso con desglose de componentes, multa estimada en USD y distribución probabilística complementaria.</p>
    </div>
    <span class="badge text-bg-light border">Paso {{ $currentStep }} de {{ count($steps) }}</span>
</div>

@if (!empty($calculationError))
    <div class="alert alert-danger">
        {{ $calculationError }}
    </div>
@endif

<div class="d-flex justify-content-end gap-2 flex-wrap mb-4">


    @if (empty($calculationError))
        <form method="POST" action="{{ route('risk.ui.sanctions.simulations.store') }}">
            @csrf
            <button type="submit" class="btn btn-outline-primary">
                <i class="bi bi-save me-1"></i>
                Guardar simulación
            </button>
        </form>

        <a href="{{ route('risk.ui.sanctions.simulations.current-report') }}"
           target="_blank"
           class="btn btn-outline-secondary">
            <i class="bi bi-file-earmark-text me-1"></i>
            Ver informe
        </a>

    @endif
    @if (($wizardContext['source'] ?? null) === 'incident' && !empty($wizardContext['incident_id']))
        <a href="{{ route('risk.ui.incidents.show', $wizardContext['incident_id']) }}"
           class="btn btn-outline-secondary">
            <i class="bi bi-arrow-return-left me-1"></i>
            Volver al incidente
        </a>
    @endif

</div>

@if (($wizardContext['source'] ?? null) === 'incident')
    <div class="alert alert-light border">
        <div class="fw-semibold mb-1">Trazabilidad del origen</div>
        <div class="small">
            Este cálculo fue iniciado desde el incidente <span class="fw-semibold">{{ $wizardContext['incident_label'] ?? 'sin referencia' }}</span>.
            Si guardas la simulación, quedará vinculada a ese incidente junto con un snapshot del expediente usado como base.
        </div>
    </div>
@endif

<div class="alert alert-light border">
    <div class="d-flex align-items-start gap-2">
        <i class="bi bi-info-circle text-primary"></i>
        <div class="small">
            <div class="fw-semibold mb-1">Nota metodológica</div>
            <div>{{ data_get($documentation, 'source.summary') }}</div>
            <div class="mt-1">{{ data_get($documentation, 'ui_notice') }}</div>
            <div class="mt-2">
                <a href="{{ route('risk.ui.sanctions.methodology') }}" class="link-primary text-decoration-none">
                    Ver metodología completa del cálculo
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="border rounded p-4 h-100">
            <div class="fw-semibold mb-3">Resumen del caso evaluado</div>

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
                    <div class="text-muted">PDI utilizado</div>
                    <div class="fw-semibold">
                        {{ $components['pdi']['no_count'] ?? 0 }}/{{ $components['pdi']['total_questions'] ?? 0 }}
                        brechas detectadas
                    </div>
                    <div class="small text-muted">{{ $components['pdi']['detail'] ?? '' }}</div>
                </div>

                <div class="col-md-6">
                    <div class="text-muted">INT</div>
                    <div class="fw-semibold">{{ $components['int']['label'] ?? 'No definido' }}</div>
                    <div class="small text-muted">{{ $components['int']['detail'] ?? '' }}</div>
                </div>

                <div class="col-12">
                    <div class="text-muted">Entradas NDV / IED</div>
                    <div class="small">
                        Confidencialidad: {{ data_get($impactLevels, data_get($ndv, 'confidentiality_impact') . '.label', 'No definido') }}
                    </div>
                    <div class="small">
                        Integridad: {{ data_get($impactLevels, data_get($ndv, 'integrity_impact') . '.label', 'No definido') }}
                    </div>
                    <div class="small">
                        Disponibilidad: {{ data_get($impactLevels, data_get($ndv, 'availability_impact') . '.label', 'No definido') }}
                    </div>
                    <div class="small">
                        Tipos de datos:
                        {{ collect($ndv['data_types'] ?? [])->map(fn ($value) => data_get($dataTypeLabels, $value . '.label', $value))->join(', ') ?: 'No definido' }}
                    </div>
                    <div class="small">Titulares afectados: {{ $ndv['data_subject_count'] ?? 'No definido' }}</div>
                    <div class="small">Volumen de datos afectados: {{ isset($ndv['data_volume_amount']) ? number_format((float) $ndv['data_volume_amount'], 2, '.', ',') : 'No definido' }}</div>
                    <div class="small">Grupos vulnerables: {{ data_get($ndv, 'vulnerable_groups') ? 'Sí' : 'No' }}</div>
                </div>

                <div class="col-md-6">
                    <div class="text-muted">RER</div>
                    <div class="fw-semibold">{{ data_get($components, 'rer.applies') ? 'Aplica' : 'No aplica' }}</div>
                    <div class="small text-muted">{{ $components['rer']['detail'] ?? '' }}</div>
                </div>
                <div class="col-md-6">
                    <div class="text-muted">CDI calculado</div>
                    <div class="fw-semibold">{{ $components['cdi']['detail'] ?? 'No disponible' }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="border rounded p-4 h-100 bg-light">
            <div class="fw-semibold mb-3">Desglose del cálculo</div>

            <div class="vstack gap-3">
                @foreach (($resultSummary['metrics'] ?? []) as $metric)
                    <div class="border rounded bg-white p-3">
                        <div class="d-flex align-items-center justify-content-between gap-2">
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

<div class="row g-4 mt-1">
    <div class="col-lg-8">
        <div class="border rounded p-4">
            <div class="fw-semibold mb-3">Componentes numéricos del modelo</div>
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Componente</th>
                            <th>Valor</th>
                            <th>Detalle</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>TDP</td>
                            <td>{{ number_format((float) data_get($components, 'ied.tdp_score', 0), 4, '.', '') }}</td>
                            <td>Mayor sensibilidad entre los tipos de datos afectados.</td>
                        </tr>
                        <tr>
                            <td>TAV</td>
                            <td>{{ number_format((float) data_get($components, 'ied.tav_score', 0), 4, '.', '') }}</td>
                            <td>Promedio entre la banda de titulares y la banda del volumen numérico de datos.</td>
                        </tr>
                        <tr>
                            <td>Escala titulares</td>
                            <td>{{ number_format((float) data_get($components, 'ied.subject_count_score', 0), 4, '.', '') }}</td>
                            <td>Banda aplicada al número de titulares afectados.</td>
                        </tr>
                        <tr>
                            <td>Escala volumen</td>
                            <td>{{ number_format((float) data_get($components, 'ied.data_volume_score', 0), 4, '.', '') }}</td>
                            <td>Banda aplicada al volumen de datos personales afectados.</td>
                        </tr>
                        <tr>
                            <td>NDV</td>
                            <td>{{ number_format((float) data_get($components, 'ied.ndv_score', 0), 4, '.', '') }}</td>
                            <td>Promedio de confidencialidad, integridad y disponibilidad.</td>
                        </tr>
                        <tr>
                            <td>TEV</td>
                            <td>{{ number_format((float) data_get($components, 'ied.tev_score', 0), 4, '.', '') }}</td>
                            <td>Impacto adicional por grupos especialmente vulnerables.</td>
                        </tr>
                        <tr>
                            <td>IED</td>
                            <td>{{ number_format((float) data_get($components, 'ied.score', 0), 4, '.', '') }}</td>
                            <td>{{ data_get($components, 'ied.detail') }}</td>
                        </tr>
                        <tr>
                            <td>INT</td>
                            <td>{{ number_format((float) data_get($components, 'int.score', 0), 4, '.', '') }}</td>
                            <td>{{ data_get($components, 'int.detail') }}</td>
                        </tr>
                        <tr>
                            <td>RER</td>
                            <td>{{ number_format((float) data_get($components, 'rer.score', 0), 4, '.', '') }}</td>
                            <td>{{ data_get($components, 'rer.detail') }}</td>
                        </tr>
                        <tr>
                            <td>SDI</td>
                            <td>{{ number_format((float) data_get($components, 'sdi.score', 0), 4, '.', '') }}</td>
                            <td>{{ data_get($components, 'sdi.detail') }}</td>
                        </tr>
                        <tr>
                            <td>CDI</td>
                            <td>{{ data_get($components, 'cdi.base_amount_usd') !== null ? 'USD ' . number_format((float) data_get($components, 'cdi.base_amount_usd', 0), 2, '.', ',') : 'N/D' }}</td>
                            <td>{{ data_get($components, 'cdi.detail') }}</td>
                        </tr>
                        <tr class="table-primary">
                            <td class="fw-semibold">Multa estimada</td>
                            <td class="fw-semibold">{{ data_get($components, 'fine.formatted_amount_usd', 'USD 0.00') }}</td>
                            <td>{{ data_get($components, 'fine.formula') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        @if (!empty($monteCarlo))
            <div class="border rounded p-4 mt-4 bg-white">
                <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-3">
                    <div>
                        <div class="fw-semibold">Análisis de Monte Carlo</div>
                        <div class="small text-muted">
                            Capa probabilística complementaria basada en {{ number_format((int) data_get($monteCarlo, 'iterations', 0), 0, '.', ',') }} simulaciones.
                            El cálculo determinista sigue siendo el resultado principal del caso.
                        </div>
                    </div>
                    <span class="badge text-bg-light border">Distribución de riesgo</span>
                </div>

                <div class="alert alert-light border small">
                    Monte Carlo modela la incertidumbre sobre los factores estimativos del daño. Para este caso se simulan
                    <strong>NDV</strong>, <strong>TDP</strong>, <strong>TAV</strong> y <strong>TEV</strong>, y en cada iteración se recalculan
                    <strong>IED</strong>, <strong>SDI</strong> y la <strong>multa final</strong>.
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-3 col-sm-6">
                        <div class="border rounded p-3 bg-light h-100">
                            <div class="small text-muted">Determinista</div>
                            <div class="fw-semibold mt-1">{{ data_get($components, 'fine.formatted_amount_usd', 'USD 0.00') }}</div>
                            <div class="small text-muted mt-1">Estimación puntual base del modelo.</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="border rounded p-3 bg-light h-100">
                            <div class="small text-muted">Mínimo</div>
                            <div class="fw-semibold mt-1">{{ data_get($monteCarlo, 'summary.formatted_minimum', 'USD 0.00') }}</div>
                            <div class="small text-muted mt-1">Escenario inferior observado en la simulación.</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="border rounded p-3 bg-light h-100">
                            <div class="small text-muted">Media</div>
                            <div class="fw-semibold mt-1">{{ data_get($monteCarlo, 'summary.formatted_mean', 'USD 0.00') }}</div>
                            <div class="small text-muted mt-1">Promedio de multas simuladas.</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="border rounded p-3 bg-light h-100">
                            <div class="small text-muted">Máximo</div>
                            <div class="fw-semibold mt-1">{{ data_get($monteCarlo, 'summary.formatted_maximum', 'USD 0.00') }}</div>
                            <div class="small text-muted mt-1">Escenario superior observado en la simulación.</div>
                        </div>
                    </div>
                </div>

                <div class="border rounded p-3 bg-light-subtle">
                    <div class="fw-semibold small mb-2">Histograma de distribución</div>
                    <div class="small text-muted mb-3">
                        Eje X: rango de multa en USD. Eje Y: frecuencia de resultados dentro del rango.
                    </div>
                    <div style="height: 320px;">
                        <canvas id="monteCarloHistogram"></canvas>
                    </div>
                </div>

                <div class="row g-3 mt-2 small">
                    <div class="col-md-6">
                        <div class="border rounded p-3 h-100">
                            <div class="fw-semibold mb-2">Variables sometidas a simulación</div>
                            <div class="vstack gap-2 text-muted">
                                @foreach (data_get($monteCarlo, 'simulated_components', []) as $code => $description)
                                    <div><strong>{{ strtoupper($code) }}</strong>: {{ $description }}</div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded p-3 h-100">
                            <div class="fw-semibold mb-2">Lectura del resultado probabilístico</div>
                            <div class="text-muted">{{ data_get($monteCarlo, 'detail') }}</div>
                            <div class="text-muted mt-2">
                                El histograma no reemplaza la multa determinista. Sirve para visualizar la dispersión plausible del resultado cuando existen componentes con incertidumbre metodológica.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="col-lg-4">
        <div class="border rounded p-4 bg-white h-100">
            <div class="fw-semibold mb-3">Supuestos del modelo</div>
            <div class="vstack gap-3 small">
                <div>
                    <div class="fw-semibold text-dark">1. Fuente normativa</div>
                    <div class="text-muted">{{ data_get($documentation, 'source.summary') }}</div>
                </div>

                <div>
                    <div class="fw-semibold text-dark">2. Parámetros normativos fijos</div>
                    <ul class="mb-0 text-muted ps-3">
                        @foreach (data_get($documentation, 'normative_fixed_parameters', []) as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                </div>

                <div>
                    <div class="fw-semibold text-dark">3. Parámetros configurables del modelo</div>
                    <ul class="mb-0 text-muted ps-3">
                        @foreach (data_get($documentation, 'configurable_parameters', []) as $item)
                            <li><code>{{ $item }}</code></li>
                        @endforeach
                    </ul>
                </div>

                <div>
                    <div class="fw-semibold text-dark">4. Mapeos metodológicos internos</div>
                    <div class="vstack gap-1 text-muted">
                        @foreach (($resultSummary['assumptions'] ?? []) as $assumption)
                            <div>{{ $assumption }}</div>
                        @endforeach
                    </div>
                </div>

                <div>
                    <div class="fw-semibold text-dark">5. Naturaleza orientativa del resultado</div>
                    <ul class="mb-0 text-muted ps-3">
                        @foreach (data_get($documentation, 'orientation.items', []) as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                </div>

                <div class="border rounded p-3 bg-light">
                    <div class="fw-semibold text-dark mb-1">{{ data_get($documentation, 'monte_carlo.title') }}</div>
                    <div class="text-muted">{{ data_get($documentation, 'monte_carlo.summary') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-between mt-4 gap-2 flex-wrap">
    <a href="{{ route('risk.ui.sanctions.wizard.show', ['step' => 6]) }}" class="btn btn-outline-secondary">Anterior</a>

    <div class="d-flex gap-2">
        <form method="POST" action="{{ route('risk.ui.sanctions.wizard.reset') }}">
            @csrf
            <button type="submit" class="btn btn-outline-secondary">Reiniciar asistente</button>
        </form>
        <a href="{{ route('risk.ui.sanctions') }}" class="btn btn-primary">Volver al módulo</a>
    </div>
</div>
@if (!empty(data_get($monteCarlo, 'histogram.labels', [])) && !empty(data_get($monteCarlo, 'histogram.frequencies', [])))
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            (function () {
                const canvas = document.getElementById('monteCarloHistogram');
                if (!canvas || typeof Chart === 'undefined') {
                    return;
                }

                const labels = @json(data_get($monteCarlo, 'histogram.labels', []));
                const values = @json(data_get($monteCarlo, 'histogram.frequencies', []));

                if (!Array.isArray(labels) || !Array.isArray(values) || labels.length === 0 || values.length === 0) {
                    return;
                }

                if (window.monteCarloHistogramChart) {
                    window.monteCarloHistogramChart.destroy();
                }

                Chart.defaults.font.family = "'Montserrat', ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial";
                Chart.defaults.color = '#6b7280';

                window.monteCarloHistogramChart = new Chart(canvas.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Frecuencia',
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
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    title(items) {
                                        return items[0]?.label ?? '';
                                    },
                                    label(context) {
                                        return `Frecuencia: ${context.parsed.y}`;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: 'Rango de multa USD'
                                },
                                ticks: {
                                    maxRotation: 0,
                                    autoSkip: true,
                                    maxTicksLimit: 6
                                },
                                grid: {
                                    display: false
                                }
                            },
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Frecuencia'
                                },
                                ticks: {
                                    precision: 0
                                }
                            }
                        }
                    }
                });
            })();
        </script>
    @endpush
@endif
