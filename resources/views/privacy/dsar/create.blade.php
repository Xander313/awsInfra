@extends('layouts.app')

@section('title', 'Nuevo DSAR')
@section('active_key', 'dsar')

@section('content')
<div class="bg-white border rounded p-5">
    <h2 class="text-lg font-bold mb-4">Nueva Solicitud DSAR</h2>

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

    {{-- FORMULARIO DSAR --}}
    <form method="POST" action="{{ route('dsar.store') }}" id="dsarForm">
        @csrf
        <input type="hidden" name="status" value="PENDING">

        <div x-data="{ tab: 'details' }">
            {{-- TABS --}}
            <div class="flex border-b mb-4">
                <button type="button"
                        @click="tab='details'"
                        class="px-4 py-2"
                        :class="tab==='details' ? 'border-b-2 border-blue-500 font-bold' : ''">
                    Detalles
                </button>
                <button type="button"
                        @click="tab='assign'"
                        class="px-4 py-2"
                        :class="tab==='assign' ? 'border-b-2 border-blue-500 font-bold' : ''">
                    Asignación
                </button>
            </div>

            {{-- DETALLES --}}
            <div x-show="tab==='details'" class="space-y-4">
                <div class="field-wrapper">
                    <label class="block text-sm font-medium">Titular (Data Subject)</label>
                    <select name="subject_id" class="w-full border rounded p-2" required>
                        <option value="">Seleccione</option>
                        @foreach($subjects as $s)
                            <option value="{{ $s->subject_id }}"
                                {{ old('subject_id') == $s->subject_id ? 'selected' : '' }}>
                                {{ $s->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="field-wrapper">
                    <label class="block text-sm font-medium">Tipo de solicitud</label>
                    <input type="text"
                           name="request_type"
                           value="{{ old('request_type') }}"
                           placeholder="Acceso / Rectificación / Eliminación"
                           class="w-full border rounded p-2"
                           required>
                </div>

                <div class="field-wrapper">
                    <label class="block text-sm font-medium">Canal</label>
                    <input type="text"
                           name="channel"
                           value="{{ old('channel') }}"
                           placeholder="Email / Web / Presencial"
                           class="w-full border rounded p-2"
                           required>
                </div>
            </div>

            {{-- ASIGNACIÓN --}}
            <div x-show="tab==='assign'" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="field-wrapper">
                    <label class="block text-sm font-medium">Fecha de recepción</label>
                    <input type="date"
                           name="received_at"
                           value="{{ old('received_at') }}"
                           class="border p-2 rounded w-full"
                           required>
                </div>

                <div class="field-wrapper">
                    <label class="block text-sm font-medium">Fecha límite</label>
                    <input type="date"
                           name="due_at"
                           value="{{ old('due_at') }}"
                           class="border p-2 rounded w-full"
                           required>
                </div>

                <div class="field-wrapper md:col-span-2">
                    <label class="block text-sm font-medium">Asignado a</label>
                    <select name="assigned_to_user_id"
                            class="border p-2 rounded w-full">
                        <option value="">Sin asignar</option>
                        @foreach($users as $u)
                            <option value="{{ $u->user_id }}"
                                {{ old('assigned_to_user_id') == $u->user_id ? 'selected' : '' }}>
                                {{ $u->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- BOTONES --}}
            <div class="flex justify-end gap-3 mt-6">
                <a href="{{ route('dsar.index') }}"
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                    <i class="fa-solid fa-arrow-left"></i> Regresar
                </a>

                <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
                    Guardar <i class="fa-solid fa-save"></i>
                </button>
            </div>

        </div>
    </form>
</div>

{{-- FONT AWESOME --}}
<link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>

@endsection
