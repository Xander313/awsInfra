@extends('layouts.app')

@section('title', 'Detalle de incidente')
@section('active_key', 'incidents')

@section('page_header')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
        <h2 class="text-xl font-bold">Detalle de incidente</h2>
        <p class="text-sm text-gray-500">Expediente operativo base del incidente dentro del dominio de riesgo.</p>
    </div>

    <div class="flex gap-2 flex-wrap">
        <a href="{{ route('risk.ui.incidents.index') }}"
           class="bg-white hover:bg-gray-50 text-gray-700 border px-4 py-2 rounded">
            Volver al listado
        </a>

        <a href="{{ route('risk.ui.incidents.edit', $incident) }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
            Editar
        </a>
    </div>
</div>
@endsection

@section('content')
@php
    $profile = $incident->org?->regulatoryProfile;
    $affectedTypes = $incident->affected_data_types ?? [];
    $simulationCount = $simulationSummary['count'] ?? 0;
    $latestSimulationLabel = data_get($latestSimulation?->wizard_snapshot ?? [], 'cdi.severity_label')
        ?: data_get($latestSimulation?->wizard_snapshot ?? [], 'cdi.label')
        ?: '—';
    $officialSimulationLabel = data_get($officialSimulation?->wizard_snapshot ?? [], 'cdi.severity_label')
        ?: data_get($officialSimulation?->wizard_snapshot ?? [], 'cdi.label')
        ?: '—';
@endphp

<div class="space-y-4">
    @if (session('success'))
        <div class="alert alert-success mb-0">{{ session('success') }}</div>
    @endif

    @if (!$hasRegulatoryProfile)
        <div class="alert alert-warning mb-0">
            La organización asociada no tiene perfil regulatorio/económico cargado. El incidente puede gestionarse, pero todavía no quedará listo para alimentar el cálculo sancionatorio en la siguiente fase.
        </div>
    @endif

    @if ($simulationCount > 0 && !$officialSimulation)
        <div class="alert alert-warning mb-0">
            El expediente ya tiene {{ $simulationCount }} simulación{{ $simulationCount === 1 ? '' : 'es' }}, pero ninguna quedó marcada como referencia oficial. Define una para mejorar la trazabilidad del caso.
        </div>
    @endif

    <div class="bg-white border rounded p-4">
        <div class="flex items-center justify-between gap-3 mb-3">
            <div>
                <div class="text-sm font-semibold text-gray-900">Resumen sancionatorio</div>
                <div class="text-sm text-gray-500">Vista rápida del expediente vinculado a este incidente.</div>
            </div>

            <a href="{{ route('risk.ui.sanctions.from-incident', $incident) }}"
               class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded">
                {{ $simulationCount > 0 ? 'Iniciar nuevo cálculo' : 'Calcular multa' }}
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            <div class="border rounded p-3 bg-gray-50">
                <div class="text-xs uppercase tracking-wide text-gray-500">Total simulaciones</div>
                <div class="text-2xl font-bold mt-1">{{ $simulationCount }}</div>
            </div>
            <div class="border rounded p-3 bg-gray-50">
                <div class="text-xs uppercase tracking-wide text-gray-500">Última simulación</div>
                <div class="font-semibold mt-1">
                    {{ $latestSimulation ? ('#' . $latestSimulation->simulation_id) : 'Sin registros' }}
                </div>
            </div>
            <div class="border rounded p-3 bg-gray-50">
                <div class="text-xs uppercase tracking-wide text-gray-500">Última multa calculada</div>
                <div class="font-semibold mt-1">
                    {{ $latestSimulation ? 'USD ' . number_format((float) $simulationSummary['latest_fine_usd'], 2, '.', ',') : '—' }}
                </div>
            </div>
            <div class="border rounded p-3 bg-gray-50">
                <div class="text-xs uppercase tracking-wide text-gray-500">Fecha última simulación</div>
                <div class="font-semibold mt-1">
                    {{ $latestSimulation ? optional($simulationSummary['latest_at'])->format('d/m/Y H:i') : '—' }}
                </div>
            </div>
            <div class="border rounded p-3 {{ $officialSimulation ? 'bg-emerald-50 border-emerald-200' : 'bg-gray-50' }}">
                <div class="text-xs uppercase tracking-wide text-gray-500">Simulación oficial</div>
                <div class="font-semibold mt-1">
                    {{ $officialSimulation ? ('#' . $officialSimulation->simulation_id) : 'Sin referencia oficial' }}
                </div>
                @if ($officialSimulation)
                    <div class="text-xs text-gray-500 mt-1">{{ $officialSimulationLabel }}</div>
                @endif
            </div>
            <div class="border rounded p-3 {{ $officialSimulation ? 'bg-emerald-50 border-emerald-200' : 'bg-gray-50' }}">
                <div class="text-xs uppercase tracking-wide text-gray-500">Multa oficial actual</div>
                <div class="font-semibold mt-1">
                    {{ $officialSimulation ? 'USD ' . number_format((float) $simulationSummary['official_fine_usd'], 2, '.', ',') : '—' }}
                </div>
            </div>
            <div class="border rounded p-3 {{ $officialSimulation ? 'bg-emerald-50 border-emerald-200' : 'bg-gray-50' }}">
                <div class="text-xs uppercase tracking-wide text-gray-500">Fecha simulación oficial</div>
                <div class="font-semibold mt-1">
                    {{ $officialSimulation ? optional($simulationSummary['official_at'])->format('d/m/Y H:i') : '—' }}
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white border rounded p-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <div class="text-xs uppercase tracking-wide text-gray-500">Código</div>
                <div class="font-semibold mt-1">{{ $incident->incident_code }}</div>
            </div>
            <div>
                <div class="text-xs uppercase tracking-wide text-gray-500">Estado</div>
                <div class="font-semibold mt-1">{{ $incident->status ?: '—' }}</div>
            </div>
            <div>
                <div class="text-xs uppercase tracking-wide text-gray-500">Severidad</div>
                <div class="font-semibold mt-1">{{ $incident->severity ?: '—' }}</div>
            </div>
            <div>
                <div class="text-xs uppercase tracking-wide text-gray-500">Rol</div>
                <div class="font-semibold mt-1">{{ $incident->company_role ? ucfirst($incident->company_role) : '—' }}</div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-white border rounded p-4">
                <div class="text-sm font-semibold text-gray-900 mb-3">Resumen general</div>
                <div class="space-y-3">
                    <div>
                        <div class="text-xs uppercase tracking-wide text-gray-500">Título</div>
                        <div class="font-semibold mt-1">{{ $incident->title }}</div>
                    </div>
                    <div>
                        <div class="text-xs uppercase tracking-wide text-gray-500">Descripción</div>
                        <div class="text-sm text-gray-700 mt-1 whitespace-pre-line">{{ $incident->description ?: 'Sin descripción' }}</div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div>
                            <div class="text-xs uppercase tracking-wide text-gray-500">Tipo</div>
                            <div class="text-sm mt-1">{{ $incident->incident_type ?: '—' }}</div>
                        </div>
                        <div>
                            <div class="text-xs uppercase tracking-wide text-gray-500">Ocurrencia</div>
                            <div class="text-sm mt-1">{{ optional($incident->occurred_at)->format('d/m/Y H:i') ?: '—' }}</div>
                        </div>
                        <div>
                            <div class="text-xs uppercase tracking-wide text-gray-500">Detección</div>
                            <div class="text-sm mt-1">{{ optional($incident->detected_at)->format('d/m/Y H:i') ?: '—' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white border rounded p-4">
                <div class="text-sm font-semibold text-gray-900 mb-3">Relaciones operativas</div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                    <div>
                        <div class="text-xs uppercase tracking-wide text-gray-500">Sistema</div>
                        <div class="mt-1">{{ $incident->system?->name ?: 'No asociado' }}</div>
                    </div>
                    <div>
                        <div class="text-xs uppercase tracking-wide text-gray-500">Actividad de tratamiento</div>
                        <div class="mt-1">{{ $incident->processingActivity?->name ?: 'No asociada' }}</div>
                    </div>
                    <div>
                        <div class="text-xs uppercase tracking-wide text-gray-500">Creado por</div>
                        <div class="mt-1">{{ $incident->creator?->full_name ?: 'No disponible' }}</div>
                    </div>
                    <div>
                        <div class="text-xs uppercase tracking-wide text-gray-500">Última actualización</div>
                        <div class="mt-1">{{ optional($incident->updated_at)->format('d/m/Y H:i') ?: '—' }}</div>
                    </div>
                </div>
            </div>

            <div class="bg-white border rounded p-4">
                <div class="text-sm font-semibold text-gray-900 mb-3">Datos de afectación</div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                    <div>
                        <div class="text-xs uppercase tracking-wide text-gray-500">Titulares afectados</div>
                        <div class="mt-1">{{ $incident->data_subject_count ?: '—' }}</div>
                    </div>
                    <div>
                        <div class="text-xs uppercase tracking-wide text-gray-500">Volumen de datos</div>
                        <div class="mt-1">{{ $incident->data_volume_amount !== null ? number_format((float) $incident->data_volume_amount, 2, '.', ',') : '—' }}</div>
                    </div>
                    <div>
                        <div class="text-xs uppercase tracking-wide text-gray-500">Confidencialidad</div>
                        <div class="mt-1">{{ $incident->confidentiality_impact ?: '—' }}</div>
                    </div>
                    <div>
                        <div class="text-xs uppercase tracking-wide text-gray-500">Integridad</div>
                        <div class="mt-1">{{ $incident->integrity_impact ?: '—' }}</div>
                    </div>
                    <div>
                        <div class="text-xs uppercase tracking-wide text-gray-500">Disponibilidad</div>
                        <div class="mt-1">{{ $incident->availability_impact ?: '—' }}</div>
                    </div>
                    <div>
                        <div class="text-xs uppercase tracking-wide text-gray-500">Grupos vulnerables</div>
                        <div class="mt-1">{{ $incident->vulnerable_groups_flag ? 'Sí' : 'No' }}</div>
                    </div>
                </div>

                <div class="mt-4">
                    <div class="text-xs uppercase tracking-wide text-gray-500">Tipos de datos afectados</div>
                    @if ($affectedTypes !== [])
                        <div class="mt-2 flex gap-2 flex-wrap">
                            @foreach ($affectedTypes as $type)
                                <span class="px-2 py-1 rounded-full border text-xs bg-gray-50">{{ $type }}</span>
                            @endforeach
                        </div>
                    @else
                        <div class="text-sm text-gray-500 mt-1">No registrados.</div>
                    @endif
                </div>
            </div>

            <div class="bg-white border rounded p-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-3">
                    <div>
                        <div class="text-sm font-semibold text-gray-900">Expediente sancionatorio</div>
                        <div class="text-sm text-gray-500">Simulaciones de multa relacionadas con este incidente, ordenadas de más reciente a más antigua.</div>
                    </div>

                </div>

                @if ($sanctionSimulations->isEmpty())
                    <div class="border border-dashed rounded p-4 bg-gray-50">
                        <div class="font-semibold text-gray-900">Todavía no hay simulaciones sancionatorias relacionadas.</div>
                        <div class="text-sm text-gray-500 mt-1">
                            Este incidente ya puede funcionar como expediente base. Inicia un cálculo de multa para dejar trazabilidad histórica desde aquí.
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('risk.ui.sanctions.from-incident', $incident) }}"
                               class="inline-flex bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded">
                                Calcular multa
                            </a>
                        </div>
                    </div>
                @else
                    <div class="overflow-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50 text-gray-600">
                                <tr>
                                    <th class="text-left px-3 py-2">Fecha</th>
                                    <th class="text-left px-3 py-2">Usuario</th>
                                    <th class="text-left px-3 py-2">Multa final</th>
                                    <th class="text-left px-3 py-2">Monte Carlo</th>
                                    <th class="text-left px-3 py-2">Entidad</th>
                                    <th class="text-left px-3 py-2">Rol</th>
                                    <th class="text-left px-3 py-2">Etiqueta</th>
                                    <th class="text-left px-3 py-2">Indicadores</th>
                                    <th class="text-right px-3 py-2">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sanctionSimulations as $simulation)
                                    @php
                                        $simulationLabel = data_get($simulation->wizard_snapshot ?? [], 'cdi.severity_label')
                                            ?: data_get($simulation->wizard_snapshot ?? [], 'cdi.label')
                                            ?: '—';
                                        $isOfficial = $officialSimulation && $officialSimulation->simulation_id === $simulation->simulation_id;
                                        $isLatest = $latestSimulation && $latestSimulation->simulation_id === $simulation->simulation_id;
                                    @endphp
                                    <tr class="border-t">
                                        <td class="px-3 py-2">
                                            <div class="font-semibold">{{ optional($simulation->created_at)->format('d/m/Y') ?: '—' }}</div>
                                            <div class="text-xs text-gray-500">{{ optional($simulation->created_at)->format('H:i') ?: '—' }}</div>
                                        </td>
                                        <td class="px-3 py-2">{{ $simulation->created_by_user_name ?: 'Usuario no disponible' }}</td>
                                        <td class="px-3 py-2 font-semibold">USD {{ number_format((float) $simulation->deterministic_fine_usd, 2, '.', ',') }}</td>
                                        <td class="px-3 py-2">
                                            <div class="text-xs text-gray-600">Min: USD {{ number_format((float) ($simulation->monte_carlo_min_usd ?? 0), 2, '.', ',') }}</div>
                                            <div class="text-xs text-gray-600">Med: USD {{ number_format((float) ($simulation->monte_carlo_mean_usd ?? 0), 2, '.', ',') }}</div>
                                            <div class="text-xs text-gray-600">Máx: USD {{ number_format((float) ($simulation->monte_carlo_max_usd ?? 0), 2, '.', ',') }}</div>
                                        </td>
                                        <td class="px-3 py-2 capitalize">{{ $simulation->entity_type ?: '—' }}</td>
                                        <td class="px-3 py-2 capitalize">{{ $simulation->company_role ?: '—' }}</td>
                                        <td class="px-3 py-2">
                                            <div>{{ $simulationLabel }}</div>
                                            <div class="text-xs text-gray-500 mt-1">{{ $simulation->case_name ?: 'Sin etiqueta de caso' }}</div>
                                        </td>
                                        <td class="px-3 py-2">
                                            <div class="flex flex-wrap gap-1">
                                                @if ($isOfficial)
                                                    <span class="px-2 py-1 rounded-full text-xs bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                        Oficial
                                                    </span>
                                                @endif
                                                @if ($isLatest)
                                                    <span class="px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800 border border-blue-200">
                                                        Última
                                                    </span>
                                                @endif
                                                <span class="px-2 py-1 rounded-full text-xs bg-rose-100 text-rose-800 border border-rose-200">
                                                    PDF
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-3 py-2">
                                            <div class="flex flex-wrap justify-end gap-2">
                                                <a href="{{ route('risk.ui.sanctions.simulations.show', $simulation) }}"
                                                   class="text-blue-600 hover:text-blue-800">
                                                    Ver simulación
                                                </a>
                                                <a href="{{ route('risk.ui.sanctions.simulations.report', ['simulation' => $simulation, 'export' => 1]) }}"
                                                   target="_blank"
                                                   class="text-rose-600 hover:text-rose-800">
                                                    Descargar PDF
                                                </a>
                                                @if (!$isOfficial)
                                                    <form action="{{ route('risk.ui.incidents.official-simulation', ['incident' => $incident, 'simulation' => $simulation]) }}"
                                                          method="POST">
                                                        @csrf
                                                        <button type="submit"
                                                                class="text-emerald-700 hover:text-emerald-900">
                                                            Marcar oficial
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <div class="bg-white border rounded p-4">
                <div class="text-sm font-semibold text-gray-900 mb-3">Documentos y evidencias</div>
                @if ($incident->incidentDocuments->isEmpty())
                    <div class="text-sm text-gray-500">
                        No hay documentos asociados todavía.
                    </div>
                @else
                    <div class="overflow-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50 text-gray-600">
                                <tr>
                                    <th class="text-left px-3 py-2">Documento</th>
                                    <th class="text-left px-3 py-2">Versión</th>
                                    <th class="text-left px-3 py-2">Tipo de vínculo</th>
                                    <th class="text-left px-3 py-2">Adjuntado</th>
                                    <th class="text-right px-3 py-2">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($incident->incidentDocuments as $link)
                                    <tr class="border-t">
                                        <td class="px-3 py-2">
                                            {{ $link->documentVersion?->document?->title ?? 'Documento no disponible' }}
                                        </td>
                                        <td class="px-3 py-2">
                                            v{{ $link->documentVersion?->version_no ?? '—' }}
                                        </td>
                                        <td class="px-3 py-2">{{ $link->relation_type ?: 'evidence' }}</td>
                                        <td class="px-3 py-2">{{ optional($link->attached_at)->format('d/m/Y H:i') ?: '—' }}</td>
                                        <td class="px-3 py-2 text-right">
                                            @if ($link->documentVersion?->document)
                                                <a href="{{ route('documents.versions.download', ['document' => $link->documentVersion->document, 'version' => $link->documentVersion]) }}"
                                                   class="text-blue-600 hover:text-blue-800">
                                                    Descargar
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <div class="space-y-4">
            <div class="bg-white border rounded p-4">
                <div class="text-sm font-semibold text-gray-900 mb-3">Organización</div>
                <div class="text-sm">
                    <div class="font-semibold">{{ $incident->org?->name ?? 'N/A' }}</div>
                    <div class="text-gray-500 mt-1">RUC: {{ $incident->org?->ruc ?? '—' }}</div>
                    <div class="text-gray-500">Industria: {{ $incident->org?->industry ?? '—' }}</div>
                </div>
            </div>

            <div class="bg-white border rounded p-4">
                <div class="text-sm font-semibold text-gray-900 mb-3">Perfil regulatorio</div>
                @if ($profile)
                    <div class="space-y-3 text-sm">
                        <div>
                            <div class="text-xs uppercase tracking-wide text-gray-500">Tipo de entidad</div>
                            <div class="mt-1">{{ ucfirst($profile->entity_type) }}</div>
                        </div>
                        <div>
                            <div class="text-xs uppercase tracking-wide text-gray-500">Volumen de negocio</div>
                            <div class="mt-1">{{ $profile->business_volume_usd !== null ? 'USD ' . number_format((float) $profile->business_volume_usd, 2, '.', ',') : '—' }}</div>
                        </div>
                        <div>
                            <div class="text-xs uppercase tracking-wide text-gray-500">SBU de referencia</div>
                            <div class="mt-1">{{ $profile->sbu_reference !== null ? number_format((float) $profile->sbu_reference, 2, '.', ',') : '—' }}</div>
                        </div>
                        <div>
                            <div class="text-xs uppercase tracking-wide text-gray-500">Año de referencia</div>
                            <div class="mt-1">{{ $profile->reference_year ?: '—' }}</div>
                        </div>
                    </div>
                @else
                    <div class="text-sm text-amber-700 bg-amber-50 border border-amber-200 rounded p-3">
                        Falta el perfil regulatorio/económico de la organización. El expediente puede seguir creciendo, pero parte del cálculo sancionatorio deberá completarse manualmente hasta que este perfil exista.
                    </div>
                @endif
            </div>

            @if ($latestSimulation)
                <div class="bg-white border rounded p-4">
                    <div class="flex items-center justify-between gap-3 mb-3">
                        <div class="text-sm font-semibold text-gray-900">Última simulación</div>
                        @if ($officialSimulation && $officialSimulation->simulation_id === $latestSimulation->simulation_id)
                            <span class="px-2 py-1 rounded-full text-xs bg-emerald-100 text-emerald-800 border border-emerald-200">
                                También es la oficial
                            </span>
                        @endif
                    </div>
                    <div class="space-y-3 text-sm">
                        <div>
                            <div class="text-xs uppercase tracking-wide text-gray-500">Registro</div>
                            <div class="mt-1 font-semibold">#{{ $latestSimulation->simulation_id }}</div>
                        </div>
                        <div>
                            <div class="text-xs uppercase tracking-wide text-gray-500">Fecha</div>
                            <div class="mt-1">{{ optional($latestSimulation->created_at)->format('d/m/Y H:i') ?: '—' }}</div>
                        </div>
                        <div>
                            <div class="text-xs uppercase tracking-wide text-gray-500">Resultado</div>
                            <div class="mt-1">{{ $latestSimulationLabel }}</div>
                        </div>
                        <div>
                            <div class="text-xs uppercase tracking-wide text-gray-500">Multa determinista</div>
                            <div class="mt-1 font-semibold">USD {{ number_format((float) $latestSimulation->deterministic_fine_usd, 2, '.', ',') }}</div>
                        </div>
                    </div>
                </div>
            @endif

            @if ($officialSimulation)
                <div class="bg-white border rounded p-4 border-emerald-200">
                    <div class="text-sm font-semibold text-gray-900 mb-3">Simulación oficial vigente</div>
                    <div class="space-y-3 text-sm">
                        <div>
                            <div class="text-xs uppercase tracking-wide text-gray-500">Registro</div>
                            <div class="mt-1 font-semibold">#{{ $officialSimulation->simulation_id }}</div>
                        </div>
                        <div>
                            <div class="text-xs uppercase tracking-wide text-gray-500">Fecha</div>
                            <div class="mt-1">{{ optional($officialSimulation->created_at)->format('d/m/Y H:i') ?: '—' }}</div>
                        </div>
                        <div>
                            <div class="text-xs uppercase tracking-wide text-gray-500">Resultado</div>
                            <div class="mt-1">{{ $officialSimulationLabel }}</div>
                        </div>
                        <div>
                            <div class="text-xs uppercase tracking-wide text-gray-500">Multa determinista</div>
                            <div class="mt-1 font-semibold">USD {{ number_format((float) $officialSimulation->deterministic_fine_usd, 2, '.', ',') }}</div>
                        </div>
                        <div class="pt-1">
                            <a href="{{ route('risk.ui.sanctions.simulations.show', $officialSimulation) }}"
                               class="text-emerald-700 hover:text-emerald-900">
                                Ver simulación oficial
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
