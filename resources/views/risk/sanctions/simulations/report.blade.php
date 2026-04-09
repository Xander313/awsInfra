@php
    $wizardState = $record['wizard_snapshot'] ?? [];
    $resultSummary = $record['result_snapshot'] ?? [];
    $documentationSnapshot = $record['documentation_snapshot'] ?? [];
    $general = $wizardState['general'] ?? [];
    $cdi = $wizardState['cdi'] ?? [];
    $ndv = $wizardState['ndv'] ?? [];
    $components = $resultSummary['components'] ?? [];
    $metrics = $resultSummary['metrics'] ?? [];
    $monteCarlo = $resultSummary['monte_carlo'] ?? [];
    $documentation = $documentationSnapshot['documentation'] ?? [];
    $assumptions = $documentationSnapshot['assumptions'] ?? [];
    $referenceMaps = $documentationSnapshot['reference_maps'] ?? [];
    $dataTypeLabels = $referenceMaps['data_types'] ?? [];
    $impactLevels = $referenceMaps['impact_levels'] ?? [];
    $generatedAt = $record['created_at'] ?? now()->toIso8601String();
    $autoExport = request()->boolean('export');
    $reportTitle = 'Informe de simulación de multas';
    $fileName = ($mode === 'saved' && isset($simulation))
        ? 'simulacion-' . $simulation->simulation_id . '-' . \Illuminate\Support\Str::slug($simulation->case_name ?: 'sin-etiqueta') . '.pdf'
        : 'simulacion-multas-' . \Illuminate\Support\Str::slug($general['case_name'] ?? 'caso-actual') . '.pdf';
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $reportTitle }}</title>
    <style>
        @page {
            size: A4;
            margin: 10mm;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            background: #f3f6fb;
            color: #1f2937;
            font-family: "Montserrat", "Segoe UI", Arial, sans-serif;
            font-size: 12px;
            line-height: 1.45;
        }

        body {
            padding: 12px;
        }

        .toolbar {
            max-width: 190mm;
            margin: 0 auto 12px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
        }

        .toolbar-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 9px 14px;
            border-radius: 10px;
            border: 1px solid #d1d5db;
            background: #ffffff;
            color: #111827;
            text-decoration: none;
            font-size: 12px;
            font-weight: 600;
        }

        .btn-primary {
            background: #2563eb;
            border-color: #2563eb;
            color: #ffffff;
        }

        .document {
            width: 190mm;
            margin: 0 auto;
        }

        .page {
            background: #ffffff;
            border: 1px solid #dbe3ee;
            border-radius: 16px;
            padding: 12mm;
            min-height: 276mm;
            display: flex;
            flex-direction: column;
            gap: 10px;
            page-break-after: always;
            break-after: page;
        }

        .page:last-child {
            page-break-after: auto;
            break-after: auto;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e5e7eb;
        }

        .eyebrow {
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-size: 10px;
            color: #64748b;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .title {
            font-size: 22px;
            font-weight: 700;
            margin: 0 0 4px;
        }

        .subtitle {
            color: #64748b;
            margin: 0;
        }

        .meta-list {
            min-width: 64mm;
            display: grid;
            gap: 4px;
        }

        .meta-row {
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }

        .meta-row strong {
            color: #111827;
        }

        .hero {
            display: grid;
            grid-template-columns: 1.15fr 0.85fr;
            gap: 10px;
        }

        .card,
        .metric-card,
        .info-box,
        .table-wrap {
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            background: #ffffff;
            padding: 10px 12px;
            break-inside: avoid;
            page-break-inside: avoid;
        }

        .card-muted {
            background: #f8fafc;
        }

        .section-title {
            font-size: 14px;
            font-weight: 700;
            margin: 0 0 8px;
        }

        .muted {
            color: #6b7280;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .summary-item {
            border: 1px solid #eef2f7;
            border-radius: 12px;
            padding: 8px 10px;
            background: #f8fafc;
            break-inside: avoid;
            page-break-inside: avoid;
        }

        .summary-item.wide {
            grid-column: span 2;
        }

        .label {
            color: #64748b;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 4px;
        }

        .value {
            font-weight: 700;
        }

        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 8px;
        }

        .metric-card {
            background: #f8fafc;
        }

        .metric-code {
            display: inline-block;
            border: 1px solid #dbe3ee;
            border-radius: 999px;
            padding: 2px 8px;
            font-size: 10px;
            font-weight: 700;
            color: #475569;
            margin-bottom: 6px;
        }

        .metric-value {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 10px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 8px 10px;
            border-bottom: 1px solid #edf2f7;
            vertical-align: top;
            text-align: left;
        }

        .table th {
            background: #f8fafc;
            font-size: 11px;
            color: #475569;
        }

        .table tr:last-child td {
            border-bottom: 0;
        }

        .two-col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .three-col {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
        }

        .list {
            margin: 0;
            padding-left: 18px;
        }

        .list li {
            margin-bottom: 6px;
        }

        .chart-box {
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            background: #ffffff;
            padding: 10px 12px;
            break-inside: avoid;
            page-break-inside: avoid;
        }

        .chart-stage {
            height: 260px;
            margin-top: 8px;
        }

        .page-note {
            margin-top: auto;
            padding-top: 8px;
            border-top: 1px solid #edf2f7;
            font-size: 10px;
            color: #64748b;
        }

        .page-break-before {
            page-break-before: always;
            break-before: page;
        }

        .avoid-break {
            break-inside: avoid;
            page-break-inside: avoid;
        }

        @media print {
            body {
                padding: 0;
                background: #ffffff;
            }

            .toolbar {
                display: none !important;
            }

            .document {
                width: 100%;
                margin: 0;
            }

            .page {
                width: 100%;
                min-height: auto;
                border: 0;
                border-radius: 0;
                padding: 0;
                gap: 8px;
            }
        }

    .btn {
        cursor: pointer;
    }
</style>
    </style>
</head>
<body>
    <div class="toolbar">
        <div>
            <h1 class="title" style="font-size:20px; margin-bottom:2px;">{{ $reportTitle }}</h1>
        </div>
        <div class="toolbar-actions">
            <button type="button" class="btn btn-primary" onclick="exportPdfReport()">Exportar PDF</button>
            <button type="button" class="btn" onclick="window.print()">Imprimir</button>
        </div>
    </div>

    <div class="document" id="sanctionPdfDocument">
        <section class="page">
            <header class="page-header avoid-break">
                <div>
                    <h2 class="title">{{ $reportTitle }}</h2>
                    <p class="subtitle">Informe técnico funcional del resultado obtenido.</p>
                </div>
                <div class="meta-list small">
                    <div class="meta-row"><strong>Fecha:</strong> <span>{{ \Carbon\Carbon::parse($generatedAt)->format('d/m/Y H:i') }}</span></div>
                    <div class="meta-row"><strong>Organización:</strong> <span>{{ $record['org_name'] ?? 'Sin organización asociada' }}</span></div>
                    <div class="meta-row"><strong>Usuario:</strong> <span>{{ $record['created_by_user_name'] ?? 'No disponible' }}</span></div>
                </div>
            </header>

            <div class="info-box card-muted avoid-break">
                <div class="section-title">Nota metodológica</div>
                <div class="muted">{{ data_get($documentation, 'source.summary') }}</div>
                <div class="muted" style="margin-top:6px;">{{ data_get($documentation, 'ui_notice') }}</div>
            </div>

            <div class="hero">
                <div class="card avoid-break">
                    <div class="section-title">Resumen del caso</div>
                    <div class="summary-grid">
                        <div class="summary-item">
                            <div class="label">Caso</div>
                            <div class="value">{{ $general['case_name'] ?? 'Sin etiqueta' }}</div>
                        </div>
                        <div class="summary-item">
                            <div class="label">Entidad</div>
                            <div class="value">{{ ucfirst($general['entity_type'] ?? 'No definido') }}</div>
                        </div>
                        <div class="summary-item">
                            <div class="label">Rol</div>
                            <div class="value">{{ ucfirst($general['company_role'] ?? 'No definido') }}</div>
                        </div>
                        <div class="summary-item">
                            <div class="label">Referencia económica</div>
                            <div class="value">
                                @if (($general['entity_type'] ?? null) === 'privada')
                                    USD {{ number_format((float) ($general['business_volume_usd'] ?? 0), 2, '.', ',') }}
                                @else
                                    SBU {{ number_format((float) ($general['sbu_reference'] ?? 0), 2, '.', ',') }}
                                @endif
                            </div>
                        </div>
                        <div class="summary-item wide">
                            <div class="label">Infracción</div>
                            <div class="value">{{ $cdi['label'] ?? 'No definida' }}</div>
                            <div class="muted">{{ $cdi['severity_label'] ?? 'Sin categoría' }}</div>
                        </div>
                    </div>
                </div>

                <div class="card card-muted avoid-break">
                    <div class="section-title">Resultado principal</div>
                    <div class="summary-grid" style="grid-template-columns:1fr;">
                        <div class="summary-item">
                            <div class="label">Multa determinista</div>
                            <div class="value">{{ data_get($components, 'fine.formatted_amount_usd', 'USD 0.00') }}</div>
                        </div>
                        <div class="summary-item">
                            <div class="label">CDI</div>
                            <div class="value">{{ data_get($components, 'metrics.cdi', data_get($components, 'cdi.detail', 'No disponible')) }}</div>
                            <div class="muted">{{ data_get($components, 'cdi.detail') }}</div>
                        </div>
                        <div class="summary-item">
                            <div class="label">Lectura Monte Carlo</div>
                            <div class="muted">{{ data_get($monteCarlo, 'detail', 'No disponible') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-wrap avoid-break">
                <div class="section-title">Desglose del cálculo</div>
                <div class="metrics-grid">
                    @foreach ($metrics as $metric)
                        <div class="metric-card">
                            <div class="metric-code">{{ $metric['code'] }}</div>
                            <div class="muted">{{ $metric['label'] }}</div>
                            <div class="metric-value">{{ $metric['value'] }}</div>
                            <div class="muted">{{ $metric['detail'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="page-note">
                Página 1 de 3. Resumen ejecutivo del caso, resultado determinista y contexto metodológico base.
            </div>
        </section>

        <section class="page page-break-before">
            <header class="page-header avoid-break">
                <div>
                    <div class="eyebrow">Página 2</div>
                    <h2 class="title" style="font-size:18px;">Componentes numéricos y soporte metodológico</h2>
                    <p class="subtitle">Detalle estructurado de entradas, componentes del modelo y supuestos visibles al momento del cálculo.</p>
                </div>
            </header>

            <div class="two-col">
                <div class="table-wrap avoid-break">
                    <div class="section-title">Entradas relevantes</div>
                    <table class="table">
                        <tbody>
                            <tr>
                                <th>Confidencialidad</th>
                                <td>{{ data_get($impactLevels, data_get($ndv, 'confidentiality_impact') . '.label', 'No definido') }}</td>
                            </tr>
                            <tr>
                                <th>Integridad</th>
                                <td>{{ data_get($impactLevels, data_get($ndv, 'integrity_impact') . '.label', 'No definido') }}</td>
                            </tr>
                            <tr>
                                <th>Disponibilidad</th>
                                <td>{{ data_get($impactLevels, data_get($ndv, 'availability_impact') . '.label', 'No definido') }}</td>
                            </tr>
                            <tr>
                                <th>Tipos de datos</th>
                                <td>{{ collect($ndv['data_types'] ?? [])->map(fn ($value) => data_get($dataTypeLabels, $value . '.label', $value))->join(', ') ?: 'No definido' }}</td>
                            </tr>
                            <tr>
                                <th>Titulares afectados</th>
                                <td>{{ $ndv['data_subject_count'] ?? 'No definido' }}</td>
                            </tr>
                            <tr>
                                <th>Volumen de datos afectados</th>
                                <td>{{ isset($ndv['data_volume_amount']) ? number_format((float) $ndv['data_volume_amount'], 2, '.', ',') : 'No definido' }}</td>
                            </tr>
                            <tr>
                                <th>Grupos vulnerables</th>
                                <td>{{ data_get($ndv, 'vulnerable_groups') ? 'Sí' : 'No' }}</td>
                            </tr>
                            <tr>
                                <th>RER</th>
                                <td>{{ data_get($components, 'rer.applies') ? 'Aplica' : 'No aplica' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="table-wrap avoid-break">
                    <div class="section-title">Componentes numéricos</div>
                    <table class="table">
                        <thead>
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
                                <td>Promedio entre la escala de titulares y la escala de volumen.</td>
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
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="two-col">
                <div class="card avoid-break">
                    <div class="section-title">Supuestos del modelo</div>
                    <ul class="list muted">
                        @foreach ($assumptions as $assumption)
                            <li>{{ $assumption }}</li>
                        @endforeach
                    </ul>
                </div>

                <div class="card avoid-break">
                    <div class="section-title">Naturaleza orientativa del resultado</div>
                    <ul class="list muted">
                        @foreach (data_get($documentation, 'orientation.items', []) as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="page-note">
                Página 2 de 3. Componentes del modelo y supuestos visibles usados para interpretar el resultado.
            </div>
        </section>

        <section class="page page-break-before">
            <header class="page-header avoid-break">
                <div>
                    <div class="eyebrow">Página 3</div>
                    <h2 class="title" style="font-size:18px;">Análisis de Monte Carlo</h2>
                    <p class="subtitle">Lectura probabilística complementaria de la multa estimada bajo incertidumbre metodológica.</p>
                </div>
            </header>

            <div class="three-col avoid-break">
                <div class="summary-item">
                    <div class="label">Mínimo</div>
                    <div class="value">{{ data_get($monteCarlo, 'summary.formatted_minimum', 'USD 0.00') }}</div>
                </div>
                <div class="summary-item">
                    <div class="label">Media</div>
                    <div class="value">{{ data_get($monteCarlo, 'summary.formatted_mean', 'USD 0.00') }}</div>
                </div>
                <div class="summary-item">
                    <div class="label">Máximo</div>
                    <div class="value">{{ data_get($monteCarlo, 'summary.formatted_maximum', 'USD 0.00') }}</div>
                </div>
            </div>

            <div class="chart-box avoid-break">
                <div class="section-title">Histograma de distribución</div>
                <div class="chart-stage">
                    <canvas id="pdfMonteCarloChart"></canvas>
                </div>
            </div>

            <div class="two-col">
                <div class="card avoid-break">
                    <div class="section-title">Variables simuladas</div>
                    <ul class="list muted">
                        @foreach (data_get($monteCarlo, 'simulated_components', []) as $code => $description)
                            <li><strong>{{ strtoupper($code) }}:</strong> {{ $description }}</li>
                        @endforeach
                    </ul>
                </div>

                <div class="card avoid-break">
                    <div class="section-title">Lectura probabilística</div>
                    <div class="muted">{{ data_get($monteCarlo, 'detail', 'No disponible') }}</div>
                    <div class="muted" style="margin-top:8px;">
                        Esta capa probabilística complementa la estimación puntual del modelo. No sustituye la lectura determinista base ni una evaluación jurídica o técnica definitiva.
                    </div>
                </div>
            </div>

            <div class="page-note">
                Página 3 de 3. Distribución de riesgo y lectura probabilística del resultado.
            </div>
        </section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        function buildPdfChart() {
            const canvas = document.getElementById('pdfMonteCarloChart');
            const labels = @json(data_get($monteCarlo, 'histogram.labels', []));
            const values = @json(data_get($monteCarlo, 'histogram.frequencies', []));

            if (!canvas || typeof Chart === 'undefined' || !Array.isArray(labels) || !Array.isArray(values) || labels.length === 0 || values.length === 0) {
                return null;
            }

            Chart.defaults.font.family = "'Montserrat', 'Segoe UI', Arial, sans-serif";
            Chart.defaults.color = '#6b7280';

            return new Chart(canvas.getContext('2d'), {
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
                    animation: false,
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
        }

        async function exportPdfReport() {
            const container = document.getElementById('sanctionPdfDocument');

            if (!container || typeof html2pdf === 'undefined') {
                window.print();
                return;
            }

            await new Promise(resolve => setTimeout(resolve, 250));

            await html2pdf().set({
                margin: [0, 0, 0, 0],
                filename: @json($fileName),
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: {
                    scale: 2,
                    useCORS: true,
                    backgroundColor: '#f3f6fb',
                    scrollY: 0
                },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' },
                pagebreak: {
                    mode: ['css', 'legacy'],
                    before: '.page-break-before',
                    avoid: '.avoid-break, .card, .summary-item, .metric-card, .table-wrap, .chart-box'
                }
            }).from(container).save();
        }

        window.addEventListener('load', async function () {
            buildPdfChart();

            if (@json($autoExport)) {
                await exportPdfReport();
            }
        });
    </script>
</body>
</html>
