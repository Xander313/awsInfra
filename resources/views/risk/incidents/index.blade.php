@extends('layouts.app')

@section('title', 'Incidentes')
@section('active_key', 'incidents')

@section('page_header')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
        <h2 class="text-xl font-bold">Incidentes</h2>
        <p class="text-sm text-gray-500">Registro operativo de incidentes dentro del dominio de riesgo.</p>
    </div>

    <div class="flex gap-2 flex-wrap">

        <a href="{{ route('risk.ui.incidents.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded flex items-center gap-2">
            Nuevo incidente
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-4">
    @if (session('success'))
        <div class="alert alert-success mb-0">{{ session('success') }}</div>
    @endif

    <div class="bg-white border rounded p-4">
        <div class="flex items-center justify-between gap-3 flex-wrap">
            <div class="text-sm text-gray-600">
                @if ($currentOrgId)
                    Mostrando incidentes de la organización activa <span class="font-mono">#{{ $currentOrgId }}</span>.
                @else
                    Mostrando incidentes de todas las organizaciones.
                @endif
            </div>
            <div class="text-sm text-gray-500">
                {{ $incidents->total() }} registros
            </div>
        </div>
    </div>

    <div class="bg-white border rounded overflow-hidden">
        <div class="overflow-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="text-left px-4 py-3">Código</th>
                        <th class="text-left px-4 py-3">Título</th>
                        <th class="text-left px-4 py-3">Organización</th>
                        <th class="text-left px-4 py-3">Estado</th>
                        <th class="text-left px-4 py-3">Severidad</th>
                        <th class="text-left px-4 py-3">Rol</th>
                        <th class="text-left px-4 py-3">Detección</th>
                        <th class="text-right px-4 py-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($incidents as $incident)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="px-4 py-3 font-mono">{{ $incident->incident_code }}</td>
                            <td class="px-4 py-3">
                                <div class="font-semibold text-gray-900">{{ $incident->title }}</div>
                                <div class="text-xs text-gray-500">{{ \Illuminate\Support\Str::limit($incident->description, 80) }}</div>
                            </td>
                            <td class="px-4 py-3">{{ $incident->org?->name ?? 'N/A' }}</td>
                            <td class="px-4 py-3">{{ $incident->status ?: '—' }}</td>
                            <td class="px-4 py-3">{{ $incident->severity ?: '—' }}</td>
                            <td class="px-4 py-3">{{ $incident->company_role ? ucfirst($incident->company_role) : '—' }}</td>
                            <td class="px-4 py-3 text-xs text-gray-600">
                                {{ optional($incident->detected_at)->format('d/m/Y H:i') ?: '—' }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('risk.ui.incidents.show', $incident) }}"
                                       class="px-3 py-1.5 rounded border hover:bg-white text-xs">
                                        Ver
                                    </a>
                                    <a href="{{ route('risk.ui.incidents.edit', $incident) }}"
                                       class="px-3 py-1.5 rounded border hover:bg-white text-xs">
                                        Editar
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-10 text-center text-gray-500">
                                No hay incidentes registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>
        {{ $incidents->links() }}
    </div>
</div>
@endsection
