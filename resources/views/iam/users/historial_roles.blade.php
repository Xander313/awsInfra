@extends('layouts.app')

@section('title', 'Historial de Roles - SGPD COAC')
@section('active_key', 'users')

@section('content')
<div class="container-fluid px-0">
    <div class="bg-white rounded-lg border border-gray-200 mb-6 p-5">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Historial de Roles</h1>
                <p class="text-gray-600 mt-1 flex items-center gap-2">
                    <span class="font-semibold text-blue-600">{{ $user->full_name }}</span>
                    <span class="text-gray-300">|</span>
                    <span class="text-sm">{{ $user->email }}</span>
                </p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex flex-col sm:flex-row justify-between items-center gap-4">
            <h2 class="text-lg font-semibold text-gray-900">Registro de Cambios</h2>
            
            <form action="{{ url()->current() }}" method="GET" id="perPageForm" class="flex items-center gap-2">
                <label for="per_page" class="text-sm text-gray-600 font-medium">Mostrar:</label>
                <select name="per_page" id="per_page" onchange="document.getElementById('perPageForm').submit()" 
                    class="text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 py-1 pl-2 pr-8">
                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 registros</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 registros</option>
                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 registros</option>
                </select>
            </form>
        </div>

        @if($history->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Fecha y Hora</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Estado / Acción</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Rol</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Gestionado por</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 text-sm">
                        @foreach($history as $record)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-gray-900 font-medium">
                                {{ $record->created_at->format('d/m/Y H:i:s') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    // Determinar si es el registro actual (el primero de la lista)
                                    $isActual = $history->onFirstPage() && $loop->first;
                                    $hasNoRole = is_null($record->role_id);
                                @endphp

                                @if($isActual && $hasNoRole)
                                    {{-- SOLO AMARILLO SI ES EL ACTUAL Y NO TIENE ROL --}}
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800 border border-yellow-200 shadow-sm">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                        Pendiente
                                    </span>
                                @elseif($isActual)
                                    {{-- VERDE SI ES EL ACTUAL CON ROL --}}
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800 border border-green-200">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                        Asignado
                                    </span>
                                @else
                                    {{-- GRIS PARA TODO LO QUE YA PASÓ (INCLUIDOS ANTIGUOS "SIN ROL") --}}
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500 border border-gray-200">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Anterior
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if(is_null($record->role_id))
                                    {{-- Texto de Rol: Amarillo solo si es el actual, si no gris --}}
                                    <span class="px-2 py-1 {{ $isActual ? 'bg-yellow-50 text-yellow-700 border-yellow-200 font-bold' : 'bg-gray-50 text-gray-400 border-gray-200 font-medium' }} rounded text-xs border uppercase tracking-tight">
                                        Sin Rol
                                    </span>
                                @else
                                    <span class="px-2 py-1 {{ $isActual ? 'bg-blue-100 text-blue-800 border-blue-200 font-bold' : 'bg-gray-50 text-gray-600 border-gray-200 font-semibold' }} rounded text-xs border">
                                        {{ $record->role->name ?? 'N/A' }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                                <div class="flex items-center">
                                    <div class="h-7 w-7 rounded-full bg-gray-200 flex items-center justify-center text-[10px] font-bold text-gray-600 mr-2 border border-gray-300">
                                        {{ substr($record->assignedBy->full_name ?? 'S', 0, 1) }}
                                    </div>
                                    {{ $record->assignedBy->full_name ?? 'Sistema' }}
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                {{ $history->links() }}
            </div>
        @else
            <div class="px-6 py-12 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                    <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-sm font-medium text-gray-900">Sin historial</h3>
                <p class="mt-1 text-sm text-gray-500">No se encontraron registros de cambios de roles para este usuario.</p>
            </div>
        @endif
    </div>
</div>
@endsection