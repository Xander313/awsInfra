@extends('layouts.app')

@section('title', 'Historial de simulaciones')
@section('active_key', 'sanctions')

@section('page_header')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
        <h2 class="text-xl font-bold">Historial de simulaciones</h2>
    </div>

    <div class="flex gap-2">


        <a href="{{ route('risk.ui.sanctions.wizard.show', ['step' => 7]) }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded flex items-center gap-2">
            <i class="bi bi-magic"></i>
            Ir al resultado actual
        </a>

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
        <div class="row g-3">
            <div class="col-md-6">
                <div class="border rounded p-3 bg-light h-100">
                    <div class="small text-muted">Registros visibles</div>
                    <div class="h4 mb-0 mt-1">{{ $simulations->total() }}</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="border rounded p-3 bg-light h-100">
                    <div class="small text-muted">Ámbito actual</div>
                    <div class="fw-semibold mt-1">
                        {{ $currentOrgId !== null ? 'Organización activa ' . $currentOrgId : 'Sin organización seleccionada' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white border rounded overflow-hidden">
        <div class="p-4 border-b">
            <div class="fw-semibold">Simulaciones guardadas</div>
        </div>

        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Fecha</th>
                        <th>Caso</th>
                        <th>Entidad</th>
                        <th>Rol</th>
                        <th>Usuario</th>
                        <th>Multa final</th>
                        <th>Monte Carlo</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($simulations as $simulation)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ optional($simulation->created_at)->format('d/m/Y') }}</div>
                                <div class="small text-muted">{{ optional($simulation->created_at)->format('H:i') }}</div>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $simulation->case_name ?: 'Simulación sin etiqueta' }}</div>
                                <div class="small text-muted">
                                    @if ($simulation->org_name)
                                        {{ $simulation->org_name }}
                                    @else
                                        Sin organización asociada
                                    @endif
                                </div>
                            </td>
                            <td class="text-capitalize">{{ $simulation->entity_type }}</td>
                            <td class="text-capitalize">{{ $simulation->company_role }}</td>
                            <td>{{ $simulation->created_by_user_name ?: 'Usuario no disponible' }}</td>
                            <td class="fw-semibold">USD {{ number_format((float) $simulation->deterministic_fine_usd, 2, '.', ',') }}</td>
                            <td>
                                <div class="small text-muted">Mín: USD {{ number_format((float) ($simulation->monte_carlo_min_usd ?? 0), 2, '.', ',') }}</div>
                                <div class="small text-muted">Med: USD {{ number_format((float) ($simulation->monte_carlo_mean_usd ?? 0), 2, '.', ',') }}</div>
                                <div class="small text-muted">Máx: USD {{ number_format((float) ($simulation->monte_carlo_max_usd ?? 0), 2, '.', ',') }}</div>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('risk.ui.sanctions.simulations.show', $simulation) }}" class="btn btn-sm btn-outline-primary">
                                    Ver detalle
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                Todavía no hay simulaciones guardadas para el ámbito actual.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($simulations->hasPages())
            <div class="p-4 border-top">
                {{ $simulations->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
