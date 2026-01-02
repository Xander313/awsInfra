@extends('layouts.app')

@section('title', 'Editar DSAR')
@section('active_key', 'dsar')

@section('content')
<div class="bg-white border rounded p-5">
    <h2 class="text-lg font-bold mb-4">Editar Solicitud DSAR</h2>

    {{-- ERRORES BACKEND --}}
    @if ($errors->any())
        <div class="bg-red-100 border border-red-300 text-red-700 p-3 rounded mb-4">
            <strong>Hay errores en el formulario:</strong>
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div x-data="{ tab: 'details', showEvidence: false }">

        {{-- FORMULARIO DSAR --}}
        <form method="POST" action="{{ route('dsar.update', $dsar) }}">
            @csrf
            @method('PUT')

            {{-- TABS --}}
            <div class="flex border-b mb-4">
                <button type="button"
                        @click="tab='details'"
                        class="px-4 py-2"
                        :class="tab==='details' ? 'border-b-2 border-blue-600 font-bold' : ''">
                    Detalles
                </button>
                <button type="button"
                        @click="tab='status'"
                        class="px-4 py-2"
                        :class="tab==='status' ? 'border-b-2 border-blue-600 font-bold' : ''">
                    Estado
                </button>
                <button type="button"
                        @click="showEvidence = !showEvidence"
                        class="px-4 py-2 ml-auto bg-gray-200 rounded hover:bg-gray-300">
                    Evidencias
                </button>
            </div>

            {{-- DETALLES --}}
            <div x-show="tab==='details'" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium">Titular</label>
                    <input type="text"
                           value="{{ $dsar->subject->full_name ?? '' }}"
                           disabled
                           class="w-full border rounded p-2 bg-gray-100">
                </div>

                <div>
                    <label class="block text-sm font-medium">Tipo de solicitud</label>
                    <input type="text"
                           name="request_type"
                           value="{{ old('request_type', $dsar->request_type) }}"
                           class="w-full border rounded p-2"
                           required>
                </div>

                <div>
                    <label class="block text-sm font-medium">Canal</label>
                    <input type="text"
                           name="channel"
                           value="{{ old('channel', $dsar->channel) }}"
                           class="w-full border rounded p-2"
                           required>
                </div>

                <div>
                    <label class="block text-sm font-medium">Fecha límite</label>
                    <input type="date"
                           name="due_at"
                           value="{{ old('due_at', optional($dsar->due_at)->format('Y-m-d')) }}"
                           class="w-full border rounded p-2"
                           required>
                </div>
            </div>

            {{-- ESTADO --}}
            <div x-show="tab==='status'" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium">Estado</label>
                    <select name="status" class="w-full border rounded p-2" required>
                        <option value="PENDING" {{ $dsar->status === 'PENDING' ? 'selected' : '' }}>
                            Pendiente
                        </option>
                        <option value="IN_PROGRESS" {{ $dsar->status === 'IN_PROGRESS' ? 'selected' : '' }}>
                            En proceso
                        </option>
                        <option value="CLOSED" {{ $dsar->status === 'CLOSED' ? 'selected' : '' }}>
                            Cerrado
                        </option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium">Resumen de resolución</label>
                    <textarea name="resolution_summary"
                              rows="3"
                              class="w-full border rounded p-2">{{ old('resolution_summary', $dsar->resolution_summary) }}</textarea>
                </div>

                @if($dsar->status === 'CLOSED')
                <div>
                    <label class="block text-sm font-medium">Fecha de cierre</label>
                    <input type="date"
                           name="closed_at"
                           value="{{ optional($dsar->closed_at)->format('Y-m-d') }}"
                           class="w-full border rounded p-2">
                </div>
                @endif
            </div>

            {{-- BOTONES DSAR --}}
            <div class="flex justify-end gap-3 mt-6">
                <a href="{{ route('dsar.index') }}"
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                    Regresar
                </a>

                <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
                    Guardar cambios
                </button>
            </div>
        </form>

        {{-- SECCIÓN EVIDENCIAS --}}
        <div x-show="showEvidence" class="mt-6 p-4 border rounded bg-gray-50">
            <h3 class="font-semibold mb-3">Evidencias</h3>

            {{-- LISTADO DE EVIDENCIAS EXISTENTES --}}
            <ul class="mb-4">
                @forelse($dsar->evidences as $e)
                    <li class="flex justify-between items-center py-1 border-b">
                        <span>{{ $e->description }} (Documento ID: {{ $e->doc_ver_id }})</span>
                        <span class="text-gray-500 text-sm">{{ optional($e->attached_at)->format('Y-m-d H:i') }}</span>
                    </li>
                @empty
                    <li class="text-gray-500">No hay evidencias agregadas.</li>
                @endforelse
            </ul>

            {{-- FORMULARIO AGREGAR EVIDENCIA --}}
            <form method="POST" action="{{ route('dsar.evidence.store', $dsar->dsar_id) }}">
                @csrf
                <input type="hidden" name="dsar_id" value="{{ $dsar->dsar_id }}">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium">Documento</label>
                        <select name="doc_ver_id" class="w-full border rounded p-2" required>
                            <option value="">Seleccione un documento</option>
                            @foreach($documents as $doc)
                                <option value="{{ $doc->doc_ver_id }}">
                                    Documento {{ $doc->doc_ver_id }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Descripción</label>
                        <input type="text" name="description"
                               class="w-full border rounded p-2"
                               placeholder="Descripción de la evidencia">
                    </div>
                </div>

                <button type="submit"
                        class="mt-3 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                    + Agregar evidencia
                </button>
            </form>
        </div>
    </div>
</div>

{{-- FONT AWESOME --}}
<link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>

@endsection




