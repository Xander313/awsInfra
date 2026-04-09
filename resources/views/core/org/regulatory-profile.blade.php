@extends('layouts.app')

@section('title', 'Perfil regulatorio')
@section('active_key', 'org')

@php
    $profileExists = $profile !== null;
    $entityType = old('entity_type', $profile?->entity_type);
    $currentYear = (int) date('Y');
    $businessVolumeValue = old('business_volume_usd', $profile?->business_volume_usd !== null ? number_format((float) $profile->business_volume_usd, 2, '.', '') : null);
    $sbuReferenceValue = old('sbu_reference', $profile?->sbu_reference !== null ? number_format((float) $profile->sbu_reference, 2, '.', '') : null);
@endphp

@section('content')
<div class="flex justify-center mt-10">
    <div class="w-full max-w-3xl">
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200 flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">
                        {{ $profileExists ? 'Editar perfil regulatorio/económico' : 'Configurar perfil regulatorio/económico' }}
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Organización: <span class="font-medium text-gray-700">{{ $org->name }}</span>
                    </p>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-medium {{ $profileExists ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                    {{ $profileExists ? 'Configurado' : 'Pendiente' }}
                </span>
            </div>

            <div class="p-6 space-y-5">
                <div class="rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-900">
                    Este perfil permitirá alimentar el cálculo sancionatorio desde incidentes sin depender de carga manual posterior.
                </div>

                @if ($errors->any())
                    <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                        Corrige los campos marcados antes de guardar el perfil.
                    </div>
                @endif

                <form method="POST" action="{{ route('orgs.regulatory-profile.update', $org) }}" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Tipo de entidad <span class="text-red-500">*</span>
                            </label>
                            <select
                                name="entity_type"
                                id="entity_type"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 text-sm @error('entity_type') border-red-500 @enderror"
                            >
                                <option value="">Selecciona una opción</option>
                                <option value="privada" @selected($entityType === 'privada')>Privada</option>
                                <option value="publica" @selected($entityType === 'publica')>Pública</option>
                            </select>
                            @error('entity_type') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Año de referencia
                            </label>
                            <input
                                type="number"
                                name="reference_year"
                                min="{{ $currentYear }}"
                                max="{{ $currentYear + 1 }}"
                                value="{{ old('reference_year', $profile?->reference_year) }}"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 text-sm @error('reference_year') border-red-500 @enderror"
                            >
                            <p class="text-xs text-gray-500 mt-1">Debe ser el año actual o el siguiente.</p>
                            @error('reference_year') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Volumen de negocio USD
                            </label>
                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                max="10000000000"
                                name="business_volume_usd"
                                value="{{ $businessVolumeValue }}"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 text-sm @error('business_volume_usd') border-red-500 @enderror"
                                placeholder="Ej. 1500000.00"
                            >
                            <p class="text-xs text-gray-500 mt-1">Obligatorio cuando la entidad es privada. Máximo permitido: 10,000,000,000.</p>
                            @error('business_volume_usd') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                SBU de referencia
                            </label>
                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                name="sbu_reference"
                                value="{{ $sbuReferenceValue }}"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 text-sm @error('sbu_reference') border-red-500 @enderror"
                                placeholder="Ej. 460.00"
                            >
                            <p class="text-xs text-gray-500 mt-1">Obligatorio cuando la entidad es pública.</p>
                            @error('sbu_reference') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Notas
                        </label>
                        <textarea
                            name="notes"
                            rows="4"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 text-sm @error('notes') border-red-500 @enderror"
                            placeholder="Contexto regulatorio, fuente del dato económico o criterios internos relevantes."
                        >{{ old('notes', $profile?->notes) }}</textarea>
                        @error('notes') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex justify-between items-center gap-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('orgs.show', $org) }}"
                           class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 text-sm hover:bg-gray-50">
                            Volver al detalle
                        </a>

                        <button type="submit"
                                class="px-5 py-2 rounded-lg bg-blue-600 text-white text-sm font-medium hover:bg-blue-700">
                            {{ $profileExists ? 'Actualizar perfil' : 'Guardar perfil' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
