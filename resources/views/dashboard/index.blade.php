@extends('layouts.app')

@section('title', 'Dashboard - SGPD')
@section('active_key', 'dashboard')
@section('h1', 'Dashboard Ejecutivo')
@section('subtitle', 'Panel de control y métricas del sistema')

@section('content')
<div class="space-y-8">
    
    <!-- Header con acciones mejorado -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="space-y-1">
            <div class="flex items-center gap-2">
                <h2 class="text-2xl font-bold text-gray-900 bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
                    Centro de estadísticas
                </h2>
                <span class="px-2 py-1 text-xs font-semibold bg-gradient-to-r from-blue-500 to-indigo-500 text-white rounded-full">
                    COAC
                </span>
            </div>
            <p class="text-sm text-gray-600 flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                Panel de control ejecutivo en tiempo real
            </p>
        </div>
        <div class="flex gap-3">
            <button onclick="window.print()" 
                    class="px-4 py-2 border border-gray-300 rounded-xl hover:bg-gray-50 flex items-center gap-2 transition-all duration-200 hover:scale-[1.02] active:scale-95 shadow-sm hover:shadow">
                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Exportar
            </button>
            <button id="refreshDashboard" 
                    class="px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl flex items-center gap-2 transition-all duration-200 hover:scale-[1.02] active:scale-95 hover:shadow-lg hover:from-blue-600 hover:to-blue-700">
                <svg id="refreshIcon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                <span>Actualizar</span>
            </button>
        </div>
    </div>

    <!-- KPIs Grid mejorado -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5">
        <!-- Actividades - Tarjeta mejorada -->
        <div class="group bg-white rounded-2xl border border-gray-200 p-6 shadow-sm hover:shadow-xl transition-all duration-300 hover:border-blue-200 hover:-translate-y-1 cursor-pointer" onclick="showModal('activities')">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-sm text-gray-500 font-medium mb-1">Actividades</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($kpis['processing_activities'] ?? 0) }}</p>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
            </div>
            <div class="text-xs text-blue-600 font-medium flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"/>
                </svg>
                Registros activos
            </div>
        </div>

        <!-- DSARs - Tarjeta mejorada -->
        <div class="group bg-white rounded-2xl border border-gray-200 p-6 shadow-sm hover:shadow-xl transition-all duration-300 hover:border-yellow-200 hover:-translate-y-1 cursor-pointer" onclick="showModal('dsar')">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-sm text-gray-500 font-medium mb-1">DSARs</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $kpis['dsar_requests']['open'] ?? 0 }}</p>
                    @if(($kpis['dsar_requests']['overdue'] ?? 0) > 0)
                    <div class="flex items-center gap-2 mt-2">
                        <div class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></div>
                        <p class="text-xs font-semibold text-red-600">{{ $kpis['dsar_requests']['overdue'] }} vencidos</p>
                    </div>
                    @endif
                </div>
                <div class="w-14 h-14 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-7 h-7 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
            <div class="text-xs text-yellow-600 font-medium flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Solicitudes abiertas
            </div>
        </div>

        <!-- Riesgos - Tarjeta mejorada -->
        <div class="group bg-white rounded-2xl border border-gray-200 p-6 shadow-sm hover:shadow-xl transition-all duration-300 hover:border-red-200 hover:-translate-y-1 cursor-pointer" onclick="showModal('risks')">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-sm text-gray-500 font-medium mb-1">Riesgos Altos</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $kpis['risks']['HIGH'] ?? 0 }}</p>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br from-red-50 to-red-100 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
            </div>
            <div class="text-xs text-red-600 font-medium flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.618 5.984A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                Requieren atención
            </div>
        </div>

        <!-- Auditorías - Tarjeta mejorada -->
        <div class="group bg-white rounded-2xl border border-gray-200 p-6 shadow-sm hover:shadow-xl transition-all duration-300 hover:border-indigo-200 hover:-translate-y-1 cursor-pointer" onclick="showModal('audits')">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-sm text-gray-500 font-medium mb-1">Auditorías</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $kpis['audits'] ?? 0 }}</p>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="text-xs text-indigo-600 font-medium flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                En progreso
            </div>
        </div>

        <!-- Capacitaciones - Tarjeta mejorada -->
        <div class="group bg-white rounded-2xl border border-gray-200 p-6 shadow-sm hover:shadow-xl transition-all duration-300 hover:border-green-200 hover:-translate-y-1 cursor-pointer" onclick="showModal('trainings')">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-sm text-gray-500 font-medium mb-1">Capacitaciones</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $kpis['trainings'] ?? 0 }}</p>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br from-green-50 to-green-100 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14v6l9-5M12 20l-9-5"/>
                    </svg>
                </div>
            </div>
            <div class="text-xs text-green-600 font-medium flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Pendientes
            </div>
        </div>
    </div>

    <!-- Gráficos y Métricas Mejoradas -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Gráficos principales -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Tendencia DSAR - Mejorado -->
            <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <span class="w-3 h-3 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full"></span>
                            Tendencia DSAR
                        </h3>
                        <p class="text-sm text-gray-500">Últimos 6 meses • Actividad por mes</p>
                    </div>
                    <div class="flex gap-2">
                        <select id="trendPeriod" class="text-sm border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 px-3 py-2 bg-gray-50">
                            <option value="6">6 meses</option>
                            <option value="3">3 meses</option>
                            <option value="12">1 año</option>
                        </select>
                        <button onclick="downloadChart('dsarTrendChart', 'tendencia-dsar.png')" 
                                class="p-2 hover:bg-gray-100 rounded-xl border border-gray-200 transition-colors">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="h-72">
                    <canvas id="dsarTrendChart"></canvas>
                </div>
            </div>

            <!-- Mini gráficos mejorados -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Distribución de Riesgos - Mejorado -->
                <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                                <span class="w-3 h-3 bg-gradient-to-r from-red-500 to-red-600 rounded-full"></span>
                                Distribución de Riesgos
                            </h3>
                            <p class="text-sm text-gray-500">Por nivel de severidad</p>
                        </div>
                        <div class="text-sm text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                            Total: {{ array_sum($kpis['risks'] ?? []) }}
                        </div>
                    </div>
                    <div class="h-56">
                        <canvas id="riskDistributionChart"></canvas>
                    </div>
                </div>

                <!-- Estado de Auditorías - Mejorado -->
                <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                                <span class="w-3 h-3 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-full"></span>
                                Estado de Auditorías
                            </h3>
                            <p class="text-sm text-gray-500">Por estado de avance</p>
                        </div>
                        <div class="flex gap-1">
                            @foreach(['PLANNED' => 'indigo', 'IN_PROGRESS' => 'blue', 'COMPLETED' => 'green'] as $status => $color)
                            <div class="w-2 h-2 bg-{{ $color }}-500 rounded-full"></div>
                            @endforeach
                        </div>
                    </div>
                    <div class="h-56">
                        <canvas id="auditStatusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar - Alertas y Actividad Mejorada (CON SCROLL) -->
        <div class="space-y-6">
            <!-- Alertas - Tarjeta mejorada CON SCROLL -->
            <div class="bg-gradient-to-br from-yellow-50 via-white to-yellow-50 rounded-2xl border border-yellow-200 shadow-sm flex flex-col h-[500px]">
                <div class="p-6 border-b border-yellow-100 flex-shrink-0">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-yellow-400 to-yellow-500 rounded-xl flex items-center justify-center shadow-sm">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">Alertas del Sistema</h3>
                                <p class="text-sm text-gray-500">{{ count($alerts) }} pendientes</p>
                            </div>
                        </div>
                        <button onclick="markAllAlertsAsRead()" class="text-xs text-yellow-700 hover:text-yellow-800 font-medium px-3 py-1.5 bg-yellow-100 hover:bg-yellow-200 rounded-lg transition-colors">
                            Marcar todas
                        </button>
                    </div>
                </div>
                
                <!-- CONTENEDOR CON SCROLL -->
                <div class="flex-1 overflow-hidden">
                    <div class="h-full overflow-y-auto px-4 py-2 custom-scrollbar">
                        <div class="space-y-3 pb-2">
                            @forelse($alerts as $alert)
                            <div class="group p-4 bg-white rounded-xl border border-gray-200 hover:bg-yellow-50 hover:border-yellow-300 transition-all duration-200 cursor-pointer alert-item" data-id="{{ $alert->id }}" onclick="showAlertDetail({{ $alert->id }})">
                                <div class="flex justify-between items-start">
                                    <div class="flex items-start gap-3">
                                        <div class="mt-0.5">
                                            @if($alert->priority === 'high')
                                                <div class="w-3 h-3 bg-gradient-to-r from-red-500 to-red-600 rounded-full animate-pulse"></div>
                                            @elseif($alert->priority === 'medium')
                                                <div class="w-3 h-3 bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-full"></div>
                                            @else
                                                <div class="w-3 h-3 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full"></div>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-semibold text-gray-900 group-hover:text-yellow-900 truncate">{{ $alert->title }}</p>
                                            <div class="flex items-center gap-2 mt-1 flex-wrap">
                                                <span class="text-xs px-2 py-0.5 bg-gray-100 text-gray-800 rounded-full">
                                                    {{ $alert->type }}
                                                </span>
                                                <span class="text-xs text-gray-500 whitespace-nowrap">
                                                    {{ \Carbon\Carbon::parse($alert->due_at)->diffForHumans() }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <button onclick="markSingleAlertAsRead(event, {{ $alert->id }})" 
                                            class="text-gray-300 hover:text-gray-500 group-hover:text-yellow-500 opacity-0 group-hover:opacity-100 transition-opacity p-1 hover:bg-yellow-100 rounded">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-8">
                                <div class="w-16 h-16 bg-gradient-to-br from-green-100 to-green-200 rounded-full flex items-center justify-center mx-auto mb-4 shadow-inner">
                                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <p class="text-sm font-medium text-gray-700">¡Excelente!</p>
                                <p class="text-xs text-gray-500 mt-1">No hay alertas pendientes</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
                
                @if(count($alerts) > 0)
                <div class="p-4 border-t border-yellow-100 bg-gradient-to-r from-yellow-50/50 to-white flex-shrink-0">
                    <button onclick="showAllAlertsModal()" class="w-full text-center text-sm text-yellow-700 hover:text-yellow-800 font-medium py-2.5 rounded-lg hover:bg-yellow-100 transition-colors flex items-center justify-center gap-2">
                        <span>Gestionar todas las alertas</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </button>
                </div>
                @endif
            </div>

            <!-- Actividad Reciente - Tarjeta mejorada CON SCROLL -->
            <div class="bg-gradient-to-br from-blue-50 via-white to-blue-50 rounded-2xl border border-blue-200 shadow-sm flex flex-col h-[500px]">
                <div class="p-6 border-b border-blue-100 flex-shrink-0">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-sm">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">Actividad Reciente</h3>
                                <p class="text-sm text-gray-500">Últimas actividades del sistema</p>
                            </div>
                        </div>
                        <span class="text-xs px-3 py-1 bg-blue-100 text-blue-800 rounded-full">{{ count($recentActivity) }}</span>
                    </div>
                </div>
                
                <!-- CONTENEDOR CON SCROLL -->
                <div class="flex-1 overflow-hidden">
                    <div class="h-full overflow-y-auto px-4 py-2 custom-scrollbar">
                        <div class="space-y-3 pb-2">
                            @forelse($recentActivity as $activity)
                            <div class="group p-4 bg-white rounded-xl border border-gray-200 hover:bg-blue-50 hover:border-blue-300 transition-all duration-200 cursor-pointer" onclick="showActivityDetail('{{ $activity->type }}', {{ $activity->id }})">
                                <div class="flex items-start gap-3">
                                    <div class="flex-shrink-0">
                                        @if($activity->type === 'Actividad de Tratamiento')
                                        <div class="w-10 h-10 bg-gradient-to-br from-blue-100 to-blue-200 rounded-xl flex items-center justify-center border border-blue-200 group-hover:scale-105 transition-transform">
                                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            </svg>
                                        </div>
                                        @elseif($activity->type === 'DSAR')
                                        <div class="w-10 h-10 bg-gradient-to-br from-yellow-100 to-yellow-200 rounded-xl flex items-center justify-center border border-yellow-200 group-hover:scale-105 transition-transform">
                                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                        @else
                                        <div class="w-10 h-10 bg-gradient-to-br from-indigo-100 to-purple-200 rounded-xl flex items-center justify-center border border-indigo-200 group-hover:scale-105 transition-transform">
                                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $activity->name }}</p>
                                        <div class="flex items-center gap-2 mt-1 flex-wrap">
                                            <span class="text-xs px-2 py-0.5 bg-gray-100 text-gray-800 rounded-full">
                                                {{ $activity->type }}
                                            </span>
                                            <span class="text-xs text-gray-500 whitespace-nowrap">
                                                {{ \Carbon\Carbon::parse($activity->created_at)->diffForHumans() }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-8">
                                <div class="w-16 h-16 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-200">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <p class="text-sm font-medium text-gray-700">Sin actividad reciente</p>
                                <p class="text-xs text-gray-500 mt-1">Se mostrará aquí la actividad nueva</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
                
                @if(count($recentActivity) > 0)
                <div class="p-4 border-t border-blue-100 bg-gradient-to-r from-blue-50/50 to-white flex-shrink-0">
                    <button onclick="showAllActivityModal()" class="w-full text-center text-sm text-blue-700 hover:text-blue-800 font-medium py-2.5 rounded-lg hover:bg-blue-100 transition-colors flex items-center justify-center gap-2">
                        <span>Ver historial completo</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Indicadores de Performance Mejorados -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl border border-blue-200 p-8 shadow-sm">
        <div class="flex justify-between items-center mb-8">
            <div class="space-y-1">
                <h3 class="text-lg font-semibold text-gray-900">Indicadores de Performance</h3>
                <p class="text-sm text-gray-600">Métricas clave de cumplimiento y eficiencia</p>
            </div>
            <div class="text-xs text-gray-500 bg-white px-3 py-1 rounded-full border border-blue-100">
                Actualizado ahora
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <!-- Resolución DSAR - Mejorado -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200 hover:shadow-md transition-shadow cursor-pointer" onclick="showModal('dsar-performance')">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-sm">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span class="text-xs font-semibold px-3 py-1 bg-blue-50 text-blue-700 rounded-full border border-blue-100">DSAR</span>
                </div>
                <div class="mb-3">
                    <div class="flex items-end gap-1">
                        <p class="text-3xl font-bold text-gray-900">{{ $performance['dsar_resolution_rate'] ?? 0 }}%</p>
                        <div class="flex items-center mb-1">
                            @if(($performance['dsar_resolution_rate'] ?? 0) >= 80)
                            <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd"/>
                            </svg>
                            @else
                            <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 100-2 1 1 0 000 2zm7-1a1 1 0 11-2 0 1 1 0 012 0zm-.464 5.535a1 1 0 10-1.415-1.414 3 3 0 01-4.242 0 1 1 0 00-1.415 1.414 5 5 0 007.072 0z" clip-rule="evenodd"/>
                            </svg>
                            @endif
                        </div>
                    </div>
                    <p class="text-sm text-gray-600 mt-1">Tasa de Resolución</p>
                </div>
                <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-blue-500 to-blue-600 rounded-full" 
                         style="width: {{ min($performance['dsar_resolution_rate'] ?? 0, 100) }}%"></div>
                </div>
            </div>

            <!-- Completitud Auditorías - Mejorado -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200 hover:shadow-md transition-shadow cursor-pointer" onclick="showModal('audits-performance')">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-sm">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span class="text-xs font-semibold px-3 py-1 bg-green-50 text-green-700 rounded-full border border-green-100">Auditorías</span>
                </div>
                <div class="mb-3">
                    <div class="flex items-end gap-1">
                        <p class="text-3xl font-bold text-gray-900">{{ $performance['audit_completion_rate'] ?? 0 }}%</p>
                        <div class="flex items-center mb-1">
                            @if(($performance['audit_completion_rate'] ?? 0) >= 75)
                            <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd"/>
                            </svg>
                            @else
                            <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 100-2 1 1 0 000 2zm7-1a1 1 0 11-2 0 1 1 0 012 0zm-.464 5.535a1 1 0 10-1.415-1.414 3 3 0 01-4.242 0 1 1 0 00-1.415 1.414 5 5 0 007.072 0z" clip-rule="evenodd"/>
                            </svg>
                            @endif
                        </div>
                    </div>
                    <p class="text-sm text-gray-600 mt-1">Completitud</p>
                </div>
                <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-green-500 to-green-600 rounded-full" 
                         style="width: {{ min($performance['audit_completion_rate'] ?? 0, 100) }}%"></div>
                </div>
            </div>

            <!-- Completitud Capacitaciones - Mejorado -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200 hover:shadow-md transition-shadow cursor-pointer" onclick="showModal('trainings-performance')">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shadow-sm">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                        </svg>
                    </div>
                    <span class="text-xs font-semibold px-3 py-1 bg-indigo-50 text-indigo-700 rounded-full border border-indigo-100">Capacitación</span>
                </div>
                <div class="mb-3">
                    <div class="flex items-end gap-1">
                        <p class="text-3xl font-bold text-gray-900">{{ $performance['training_completion_rate'] ?? 0 }}%</p>
                        <div class="flex items-center mb-1">
                            @if(($performance['training_completion_rate'] ?? 0) >= 90)
                            <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd"/>
                            </svg>
                            @else
                            <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 100-2 1 1 0 000 2zm7-1a1 1 0 11-2 0 1 1 0 012 0zm-.464 5.535a1 1 0 10-1.415-1.414 3 3 0 01-4.242 0 1 1 0 00-1.415 1.414 5 5 0 007.072 0z" clip-rule="evenodd"/>
                            </svg>
                            @endif
                        </div>
                    </div>
                    <p class="text-sm text-gray-600 mt-1">Completitud</p>
                </div>
                <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-indigo-500 to-purple-600 rounded-full" 
                         style="width: {{ min($performance['training_completion_rate'] ?? 0, 100) }}%"></div>
                </div>
            </div>

            <!-- Cobertura de Riesgos - Mejorado -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200 hover:shadow-md transition-shadow cursor-pointer" onclick="showModal('risks-coverage')">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl flex items-center justify-center shadow-sm">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.618 5.984A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <span class="text-xs font-semibold px-3 py-1 bg-amber-50 text-amber-700 rounded-full border border-amber-100">Riesgos</span>
                </div>
                <div class="mb-3">
                    <div class="flex items-end gap-1">
                        <p class="text-3xl font-bold text-gray-900">{{ $performance['risk_coverage'] ?? 0 }}%</p>
                        <div class="flex items-center mb-1">
                            @if(($performance['risk_coverage'] ?? 0) >= 60)
                            <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd"/>
                            </svg>
                            @else
                            <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 100-2 1 1 0 000 2zm7-1a1 1 0 11-2 0 1 1 0 012 0zm-.464 5.535a1 1 0 10-1.415-1.414 3 3 0 01-4.242 0 1 1 0 00-1.415 1.414 5 5 0 007.072 0z" clip-rule="evenodd"/>
                            </svg>
                            @endif
                        </div>
                    </div>
                    <p class="text-sm text-gray-600 mt-1">Cobertura DPIA</p>
                </div>
                <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-amber-500 to-orange-600 rounded-full" 
                         style="width: {{ min($performance['risk_coverage'] ?? 0, 100) }}%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Container -->
<div id="modalContainer" class="fixed inset-0 z-[9999] hidden">
    <!-- Overlay -->
    <div id="modalOverlay" class="absolute inset-0 bg-black bg-opacity-50 backdrop-blur-sm transition-opacity duration-300"></div>
    
    <!-- Modal Content -->
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div id="modalContent" class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden transform transition-all duration-300 scale-95 opacity-0">
            <!-- Modal Header -->
            <div id="modalHeader" class="px-8 py-6 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-white">
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div id="modalIcon" class="w-12 h-12 rounded-xl flex items-center justify-center"></div>
                        <div>
                            <h2 id="modalTitle" class="text-xl font-bold text-gray-900"></h2>
                            <p id="modalSubtitle" class="text-sm text-gray-600"></p>
                        </div>
                    </div>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 p-2 hover:bg-gray-100 rounded-xl transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Modal Body -->
            <div id="modalBody" class="p-8 overflow-y-auto max-h-[60vh] custom-scrollbar">
                <!-- Content will be loaded here -->
            </div>
            
            <!-- Modal Footer -->
            <div id="modalFooter" class="px-8 py-6 border-t border-gray-200 bg-gray-50">
                <div class="flex justify-end gap-3">
                    <button onclick="closeModal()" class="px-6 py-2.5 text-gray-700 hover:text-gray-900 font-medium bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors">
                        Cerrar
                    </button>
                    <button id="modalActionButton" class="px-6 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-medium rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all">
                        <span id="modalActionText">Ver más</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .chart-tooltip {
        background: white !important;
        border-radius: 8px !important;
        border: 1px solid #e5e7eb !important;
        padding: 12px !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
    }
    
    .chart-tooltip .title {
        font-weight: 600 !important;
        color: #374151 !important;
        margin-bottom: 4px !important;
    }
    
    .chart-tooltip .value {
        color: #1f2937 !important;
        font-size: 14px !important;
    }
    
    /* Scrollbar personalizada */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 10px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
    
    /* Animaciones personalizadas */
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-5px); }
    }
    
    .floating {
        animation: float 3s ease-in-out infinite;
    }
    
    /* Efectos de hover mejorados */
    .hover-lift {
        transition: all 0.3s ease;
    }
    
    .hover-lift:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
    }
    
    /* Animación para eliminar alerta */
    @keyframes slideOut {
        0% { transform: translateX(0); opacity: 1; }
        100% { transform: translateX(100%); opacity: 0; }
    }
    
    .slide-out {
        animation: slideOut 0.3s ease-out forwards;
    }
    
    /* Animaciones del modal */
    .modal-enter {
        animation: modalEnter 0.3s ease-out forwards;
    }
    
    .modal-exit {
        animation: modalExit 0.2s ease-in forwards;
    }
    
    @keyframes modalEnter {
        from {
            transform: scale(0.95);
            opacity: 0;
        }
        to {
            transform: scale(1);
            opacity: 1;
        }
    }
    
    @keyframes modalExit {
        from {
            transform: scale(1);
            opacity: 1;
        }
        to {
            transform: scale(0.95);
            opacity: 0;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
<script>
    // Configuración global de Chart.js
    Chart.defaults.font.family = "'Montserrat', ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial";
    Chart.defaults.color = '#6b7280';
    
    // Colores actualizados
    const colors = {
        primary: { gradient: ['#3b82f6', '#2563eb'], solid: '#3b82f6' },
        success: { gradient: ['#10b981', '#059669'], solid: '#10b981' },
        info: { gradient: ['#06b6d4', '#0891b2'], solid: '#06b6d4' },
        warning: { gradient: ['#f59e0b', '#d97706'], solid: '#f59e0b' },
        danger: { gradient: ['#ef4444', '#dc2626'], solid: '#ef4444' },
        purple: { gradient: ['#8b5cf6', '#7c3aed'], solid: '#8b5cf6' }
    };

    // Helper para crear gradientes
    function createGradient(ctx, colorStops, vertical = false) {
        const gradient = vertical 
            ? ctx.createLinearGradient(0, 0, 0, 400)
            : ctx.createLinearGradient(0, 0, 400, 0);
        
        colorStops.forEach((stop, i) => {
            gradient.addColorStop(i / (colorStops.length - 1), stop);
        });
        
        return gradient;
    }

    // Formatear meses
    function formatMonth(dateStr) {
        try {
            const date = new Date(dateStr);
            return date.toLocaleDateString('es-ES', {month: 'short', year: 'numeric'});
        } catch(e) {
            return dateStr;
        }
    }

    // ============================================
    // SISTEMA DE MODALES ELEGANTE
    // ============================================

    let currentModalType = null;
    let isModalOpen = false;

    // Mostrar modal genérico
    function showModal(type) {
        currentModalType = type;
        isModalOpen = true;
        
        const modalContainer = document.getElementById('modalContainer');
        const modalContent = document.getElementById('modalContent');
        const modalOverlay = document.getElementById('modalOverlay');
        
        modalContainer.classList.remove('hidden');
        modalContainer.style.display = 'block';
        
        setTimeout(() => {
            modalOverlay.classList.remove('opacity-0');
            modalContent.classList.remove('scale-95', 'opacity-0');
            modalContent.classList.add('modal-enter');
        }, 10);
        
        loadModalContent(type);
    }

    // Cerrar modal
    function closeModal() {
        const modalContainer = document.getElementById('modalContainer');
        const modalContent = document.getElementById('modalContent');
        const modalOverlay = document.getElementById('modalOverlay');
        
        modalContent.classList.remove('modal-enter');
        modalContent.classList.add('modal-exit');
        modalOverlay.classList.add('opacity-0');
        
        setTimeout(() => {
            modalContainer.classList.add('hidden');
            modalContainer.style.display = 'none';
            modalContent.classList.remove('modal-exit');
            currentModalType = null;
            isModalOpen = false;
        }, 200);
    }

    // Cargar contenido del modal basado en el tipo
    function loadModalContent(type) {
        const modalTitle = document.getElementById('modalTitle');
        const modalSubtitle = document.getElementById('modalSubtitle');
        const modalIcon = document.getElementById('modalIcon');
        const modalBody = document.getElementById('modalBody');
        const modalActionButton = document.getElementById('modalActionButton');
        const modalActionText = document.getElementById('modalActionText');
        
        // Configuración inicial
        modalActionButton.onclick = null;
        modalActionButton.classList.remove('hidden');
        
        switch(type) {
            case 'activities':
                setupActivitiesModal(modalTitle, modalSubtitle, modalIcon, modalBody, modalActionButton, modalActionText);
                break;
            case 'dsar':
                setupDsarModal(modalTitle, modalSubtitle, modalIcon, modalBody, modalActionButton, modalActionText);
                break;
            case 'risks':
                setupRisksModal(modalTitle, modalSubtitle, modalIcon, modalBody, modalActionButton, modalActionText);
                break;
            case 'audits':
                setupAuditsModal(modalTitle, modalSubtitle, modalIcon, modalBody, modalActionButton, modalActionText);
                break;
            case 'trainings':
                setupTrainingsModal(modalTitle, modalSubtitle, modalIcon, modalBody, modalActionButton, modalActionText);
                break;
            case 'dsar-performance':
                setupDsarPerformanceModal(modalTitle, modalSubtitle, modalIcon, modalBody, modalActionButton, modalActionText);
                break;
            case 'audits-performance':
                setupAuditsPerformanceModal(modalTitle, modalSubtitle, modalIcon, modalBody, modalActionButton, modalActionText);
                break;
            case 'trainings-performance':
                setupTrainingsPerformanceModal(modalTitle, modalSubtitle, modalIcon, modalBody, modalActionButton, modalActionText);
                break;
            case 'risks-coverage':
                setupRisksCoverageModal(modalTitle, modalSubtitle, modalIcon, modalBody, modalActionButton, modalActionText);
                break;
            case 'all-alerts':
                setupAllAlertsModal(modalTitle, modalSubtitle, modalIcon, modalBody, modalActionButton, modalActionText);
                break;
            case 'all-activity':
                setupAllActivityModal(modalTitle, modalSubtitle, modalIcon, modalBody, modalActionButton, modalActionText);
                break;
            default:
                setupDefaultModal(modalTitle, modalSubtitle, modalIcon, modalBody, modalActionButton, modalActionText);
        }
    }

// ============================================
// CONFIGURACIONES DE MODALES ESPECÍFICOS (ACTUALIZADO CON API)
// ============================================

async function loadModalData(type) {
    try {
        const response = await fetch(`/api/dashboard/modal-data/${type}`);
        if (!response.ok) throw new Error('Error al cargar datos');
        return await response.json();
    } catch (error) {
        console.error('Error cargando datos del modal:', error);
        return null;
    }
}

async function setupActivitiesModal(title, subtitle, icon, body, actionBtn, actionText) {
    title.textContent = 'Actividades de Tratamiento';
    subtitle.textContent = 'Resumen de actividades registradas en el sistema';
    icon.innerHTML = `<svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
    </svg>`;
    icon.className = 'w-12 h-12 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl flex items-center justify-center';
    
    // Mostrar loading
    body.innerHTML = `
        <div class="flex justify-center items-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
        </div>
    `;
    
    // Cargar datos desde API
    const data = await loadModalData('activities');
    
    if (data && data.activities) {
        const activitiesCount = data.activities.total || 0;
        const dpiaCompleted = data.activities.dpia_completed || 0;
        const categories = data.activities.by_category || [];
        
        body.innerHTML = `
            <div class="space-y-6">
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-blue-50 p-4 rounded-xl">
                        <p class="text-sm text-blue-600 font-medium">Total de Actividades</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">${activitiesCount}</p>
                    </div>
                    <div class="bg-green-50 p-4 rounded-xl">
                        <p class="text-sm text-green-600 font-medium">Con DPIA completado</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">${dpiaCompleted}</p>
                    </div>
                </div>
                
                ${categories.length > 0 ? `
                    <div class="space-y-4">
                        <h3 class="font-semibold text-gray-900">Distribución por Categoría</h3>
                        <div class="space-y-3">
                            ${categories.map(cat => `
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                            </svg>
                                        </div>
                                        <span class="font-medium text-gray-700">${cat.category || 'Sin categoría'}</span>
                                    </div>
                                    <span class="font-bold text-gray-900">${cat.count || 0}</span>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                ` : `
                    <div class="text-center py-8">
                        <p class="text-gray-500">No hay datos de categorías disponibles</p>
                    </div>
                `}
            </div>
        `;
    } else {
        body.innerHTML = `
            <div class="text-center py-8">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Error al cargar datos</h3>
                <p class="text-gray-600">No se pudieron cargar los datos de actividades.</p>
            </div>
        `;
    }
    
    actionText.textContent = 'Ver todas las actividades';
    actionBtn.onclick = () => {
    closeModal();
    showNotification('Redirigiendo a actividades de tratamiento...', 'info');
    window.location.href = '/rat'; // Cambiar a la ruta correcta
};

}

async function setupDsarModal(title, subtitle, icon, body, actionBtn, actionText) {
    title.textContent = 'Solicitudes DSAR';
    subtitle.textContent = 'Estado de solicitudes de derechos de los titulares';
    icon.innerHTML = `<svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
    </svg>`;
    icon.className = 'w-12 h-12 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl flex items-center justify-center';
    
    // Mostrar loading
    body.innerHTML = `
        <div class="flex justify-center items-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-yellow-600"></div>
        </div>
    `;
    
    // Cargar datos desde API
    const data = await loadModalData('dsar');
    
    if (data && data.dsar) {
        const openDsars = data.dsar.stats?.open || 0;
        const overdueDsars = data.dsar.stats?.overdue || 0;
        const resolvedDsars = data.dsar.stats?.resolved || 0;
        const dsarByType = data.dsar.by_type || {};
        const recentDsars = data.dsar.recent || [];
        
        body.innerHTML = `
            <div class="space-y-6">
                <div class="grid grid-cols-3 gap-4">
                    <div class="bg-yellow-50 p-4 rounded-xl">
                        <p class="text-sm text-yellow-600 font-medium">Abiertas</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">${openDsars}</p>
                    </div>
                    <div class="bg-red-50 p-4 rounded-xl">
                        <p class="text-sm text-red-600 font-medium">Vencidas</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">${overdueDsars}</p>
                    </div>
                    <div class="bg-green-50 p-4 rounded-xl">
                        <p class="text-sm text-green-600 font-medium">Resueltas</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">${resolvedDsars}</p>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <h3 class="font-semibold text-gray-900">Solicitudes por Tipo</h3>
                    <div class="space-y-3">
                        ${Object.entries(dsarByType).map(([type, count]) => {
                            let iconSvg = '';
                            let colorClass = '';
                            let label = '';
                            
                            switch(type) {
                                case 'ACCESS':
                                    iconSvg = `<svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>`;
                                    colorClass = 'bg-blue-100';
                                    label = 'Acceso a datos';
                                    break;
                                case 'RECTIFICATION':
                                    iconSvg = `<svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>`;
                                    colorClass = 'bg-red-100';
                                    label = 'Rectificación';
                                    break;
                                case 'DELETION':
                                    iconSvg = `<svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>`;
                                    colorClass = 'bg-purple-100';
                                    label = 'Cancelación';
                                    break;
                                default:
                                    iconSvg = `<svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`;
                                    colorClass = 'bg-gray-100';
                                    label = type;
                            }
                            
                            return `
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 ${colorClass} rounded-lg flex items-center justify-center">
                                            ${iconSvg}
                                        </div>
                                        <span class="font-medium text-gray-700">${label}</span>
                                    </div>
                                    <span class="font-bold text-gray-900">${count}</span>
                                </div>
                            `;
                        }).join('')}
                    </div>
                </div>
                
                ${recentDsars.length > 0 ? `
                    <div class="space-y-4">
                        <h3 class="font-semibold text-gray-900">Solicitudes Recientes</h3>
                        <div class="space-y-2">
                            ${recentDsars.slice(0, 3).map(dsar => `
                                <div class="p-3 bg-gray-50 rounded-lg">
                                    <div class="flex justify-between items-center">
                                        <span class="font-medium text-gray-700">${dsar.request_type || 'DSAR'}</span>
                                        <span class="text-xs text-gray-500">${new Date(dsar.created_at).toLocaleDateString('es-ES')}</span>
                                    </div>
                                    <p class="text-sm text-gray-600 mt-1">${dsar.status || 'Estado desconocido'}</p>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                ` : ''}
            </div>
        `;
    } else {
        body.innerHTML = `
            <div class="text-center py-8">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Error al cargar datos</h3>
                <p class="text-gray-600">No se pudieron cargar los datos de DSAR.</p>
            </div>
        `;
    }


}

async function setupRisksModal(title, subtitle, icon, body, actionBtn, actionText) {
    title.textContent = 'Gestión de Riesgos';
    subtitle.textContent = 'Riesgos identificados y su nivel de severidad';
    icon.innerHTML = `<svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
    </svg>`;
    icon.className = 'w-12 h-12 bg-gradient-to-br from-red-50 to-red-100 rounded-xl flex items-center justify-center';
    
    // Mostrar loading
    body.innerHTML = `
        <div class="flex justify-center items-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-red-600"></div>
        </div>
    `;
    
    // Cargar datos desde API
    const data = await loadModalData('risks');
    
    if (data && data.risks) {
        const highRisks = data.risks.by_severity?.HIGH || 0;
        const mediumRisks = data.risks.by_severity?.MEDIUM || 0;
        const lowRisks = data.risks.by_severity?.LOW || 0;
        const totalRisks = highRisks + mediumRisks + lowRisks;
        const criticalRisks = data.risks.critical_risks || [];
        
        body.innerHTML = `
            <div class="space-y-6">
                <div class="grid grid-cols-3 gap-4">
                    <div class="bg-red-50 p-4 rounded-xl">
                        <p class="text-sm text-red-600 font-medium">Alto</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">${highRisks}</p>
                        <div class="text-xs text-red-500 mt-1">
                            ${totalRisks > 0 ? Math.round((highRisks/totalRisks)*100) : 0}% del total
                        </div>
                    </div>
                    <div class="bg-yellow-50 p-4 rounded-xl">
                        <p class="text-sm text-yellow-600 font-medium">Medio</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">${mediumRisks}</p>
                        <div class="text-xs text-yellow-500 mt-1">
                            ${totalRisks > 0 ? Math.round((mediumRisks/totalRisks)*100) : 0}% del total
                        </div>
                    </div>
                    <div class="bg-blue-50 p-4 rounded-xl">
                        <p class="text-sm text-blue-600 font-medium">Bajo</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">${lowRisks}</p>
                        <div class="text-xs text-blue-500 mt-1">
                            ${totalRisks > 0 ? Math.round((lowRisks/totalRisks)*100) : 0}% del total
                        </div>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <h3 class="font-semibold text-gray-900">${highRisks > 0 ? 'Riesgos Críticos' : 'Estado de Riesgos'}</h3>
                    <div class="space-y-3">
                        ${highRisks > 0 ? `
                            <div class="p-4 bg-red-50 border border-red-200 rounded-xl">
                                <div class="flex items-center gap-3">
                                    <div class="w-3 h-3 bg-red-500 rounded-full animate-pulse"></div>
                                    <p class="font-medium text-gray-900">${highRisks} riesgos críticos identificados</p>
                                </div>
                                <p class="text-sm text-gray-600 mt-2">Estos riesgos requieren atención inmediata y deben ser tratados en los próximos 7 días.</p>
                                
                                ${criticalRisks.length > 0 ? `
                                    <div class="mt-4 space-y-2">
                                        ${criticalRisks.slice(0, 3).map(risk => `
                                            <div class="p-3 bg-white rounded-lg border border-red-100">
                                                <p class="font-medium text-gray-900">${risk.name || 'Riesgo sin nombre'}</p>
                                                <p class="text-sm text-gray-600 mt-1 truncate">${risk.description || 'Sin descripción'}</p>
                                            </div>
                                        `).join('')}
                                    </div>
                                ` : ''}
                            </div>
                        ` : `
                            <div class="p-4 bg-green-50 border border-green-200 rounded-xl">
                                <div class="flex items-center gap-3">
                                    <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                    <p class="font-medium text-gray-900">No hay riesgos críticos identificados</p>
                                </div>
                                <p class="text-sm text-gray-600 mt-2">Excelente gestión de riesgos en el sistema. Se recomienda mantener revisiones periódicas.</p>
                            </div>
                        `}
                    </div>
                </div>
            </div>
        `;
    } else {
        body.innerHTML = `
            <div class="text-center py-8">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Error al cargar datos</h3>
                <p class="text-gray-600">No se pudieron cargar los datos de riesgos.</p>
            </div>
        `;
    }
    
    actionText.textContent = 'Ver gestión de riesgos';
    actionBtn.onclick = () => {
    closeModal();
    showNotification('Redirigiendo a gestión de riesgos...', 'info');
    window.location.href = '/risk/ui'; // Cambiar a la ruta correcta
};
}


async function setupTrainingsModal(title, subtitle, icon, body, actionBtn, actionText) {
    title.textContent = 'Capacitaciones';
    subtitle.textContent = 'Estado de capacitaciones del personal';
    icon.innerHTML = `<svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14v6l9-5M12 20l-9-5"/>
    </svg>`;
    icon.className = 'w-12 h-12 bg-gradient-to-br from-green-50 to-green-100 rounded-xl flex items-center justify-center';
    
    // Mostrar loading
    body.innerHTML = `
        <div class="flex justify-center items-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600"></div>
        </div>
    `;
    
    // Cargar datos desde API
    const data = await loadModalData('trainings');
    
    if (data && data.trainings) {
        const pendingTrainings = data.trainings.pending || 0;
        const completionRate = data.trainings.completion_rate || 0;
        const overdueTrainings = data.trainings.overdue || 0;
        
        body.innerHTML = `
            <div class="space-y-6">
                <div class="grid grid-cols-3 gap-4">
                    <div class="bg-green-50 p-4 rounded-xl">
                        <p class="text-sm text-green-600 font-medium">Pendientes</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">${pendingTrainings}</p>
                    </div>
                    <div class="bg-blue-50 p-4 rounded-xl">
                        <p class="text-sm text-blue-600 font-medium">Completitud</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">${completionRate}%</p>
                    </div>
                    <div class="bg-red-50 p-4 rounded-xl">
                        <p class="text-sm text-red-600 font-medium">Vencidas</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">${overdueTrainings}</p>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <h3 class="font-semibold text-gray-900">Recomendaciones</h3>
                    <div class="space-y-3">
                        ${overdueTrainings > 0 ? `
                            <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-xl">
                                <div class="flex items-center gap-3">
                                    <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                                    <p class="font-medium text-gray-900">${overdueTrainings} capacitaciones vencidas</p>
                                </div>
                                <p class="text-sm text-gray-600 mt-2">Es necesario reprogramar estas capacitaciones urgentemente.</p>
                            </div>
                        ` : ''}
                        
                        ${completionRate < 90 ? `
                            <div class="p-4 bg-blue-50 border border-blue-200 rounded-xl">
                                <div class="flex items-center gap-3">
                                    <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                                    <p class="font-medium text-gray-900">Tasa de completitud baja</p>
                                </div>
                                <p class="text-sm text-gray-600 mt-2">La tasa de completitud de capacitaciones (${completionRate}%) está por debajo del objetivo del 90%.</p>
                            </div>
                        ` : `
                            <div class="p-4 bg-green-50 border border-green-200 rounded-xl">
                                <div class="flex items-center gap-3">
                                    <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                    <p class="font-medium text-gray-900">Excelente cumplimiento</p>
                                </div>
                                <p class="text-sm text-gray-600 mt-2">La tasa de completitud de capacitaciones (${completionRate}%) cumple con los objetivos establecidos.</p>
                            </div>
                        `}
                    </div>
                </div>
            </div>
        `;
    } else {
        body.innerHTML = `
            <div class="text-center py-8">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Error al cargar datos</h3>
                <p class="text-gray-600">No se pudieron cargar los datos de capacitaciones.</p>
            </div>
        `;
    }
    
    actionText.textContent = 'Gestionar capacitaciones aqui es';
    actionBtn.onclick = () => {
    closeModal();
    showNotification('Redirigiendo a capacitaciones...', 'info');
    window.location.href = '/audit/audits'; // Cambiar a la ruta correcta (o donde estén las capacitaciones)
};
}

// Actualiza la función loadModalContent para que sea async
async function loadModalContent(type) {
    const modalTitle = document.getElementById('modalTitle');
    const modalSubtitle = document.getElementById('modalSubtitle');
    const modalIcon = document.getElementById('modalIcon');
    const modalBody = document.getElementById('modalBody');
    const modalActionButton = document.getElementById('modalActionButton');
    const modalActionText = document.getElementById('modalActionText');
    
    modalActionButton.onclick = null;
    modalActionButton.classList.remove('hidden');
    
    switch(type) {
        case 'activities':
            await setupActivitiesModal(modalTitle, modalSubtitle, modalIcon, modalBody, modalActionButton, modalActionText);
            break;
        case 'dsar':
            await setupDsarModal(modalTitle, modalSubtitle, modalIcon, modalBody, modalActionButton, modalActionText);
            break;
        case 'risks':
            await setupRisksModal(modalTitle, modalSubtitle, modalIcon, modalBody, modalActionButton, modalActionText);
            break;
        case 'audits':
            await setupAuditsModal(modalTitle, modalSubtitle, modalIcon, modalBody, modalActionButton, modalActionText);
            break;
        case 'trainings':
            await setupTrainingsModal(modalTitle, modalSubtitle, modalIcon, modalBody, modalActionButton, modalActionText);
            break;
        case 'all-alerts':
            await setupAllAlertsModal(modalTitle, modalSubtitle, modalIcon, modalBody, modalActionButton, modalActionText);
            break;
        case 'all-activity':
            await setupAllActivityModal(modalTitle, modalSubtitle, modalIcon, modalBody, modalActionButton, modalActionText);
            break;
        default:
            setupDefaultModal(modalTitle, modalSubtitle, modalIcon, modalBody, modalActionButton, modalActionText);
    }
}

function setupDsarModal(title, subtitle, icon, body, actionBtn, actionText) {
    title.textContent = 'Solicitudes DSAR';
    subtitle.textContent = 'Estado de solicitudes de derechos de los titulares';
    icon.innerHTML = `<svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
    </svg>`;
    icon.className = 'w-12 h-12 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl flex items-center justify-center';
    
    // Usar datos reales del backend
    const openDsars = {{ $kpis['dsar_requests']['open'] ?? 0 }};
    const overdueDsars = {{ $kpis['dsar_requests']['overdue'] ?? 0 }};
    const resolvedDsars = {{ $kpis['dsar_requests']['resolved'] ?? 0 }};
    const dsarByType = @json($kpis['dsar_by_type'] ?? []);
    
    body.innerHTML = `
        <div class="space-y-6">
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-yellow-50 p-4 rounded-xl">
                    <p class="text-sm text-yellow-600 font-medium">Abiertas</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">${openDsars}</p>
                </div>
                <div class="bg-red-50 p-4 rounded-xl">
                    <p class="text-sm text-red-600 font-medium">Vencidas</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">${overdueDsars}</p>
                </div>
                <div class="bg-green-50 p-4 rounded-xl">
                    <p class="text-sm text-green-600 font-medium">Resueltas</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">${resolvedDsars}</p>
                </div>
            </div>
            
            <div class="space-y-4">
                <h3 class="font-semibold text-gray-900">Solicitudes por Tipo</h3>
                <div class="space-y-3">
                    ${Object.entries(dsarByType).map(([type, count]) => {
                        let iconSvg = '';
                        let colorClass = '';
                        let label = '';
                        
                        switch(type) {
                            case 'ACCESS':
                                iconSvg = `<svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>`;
                                colorClass = 'bg-blue-100';
                                label = 'Acceso a datos';
                                break;
                            case 'RECTIFICATION':
                                iconSvg = `<svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>`;
                                colorClass = 'bg-red-100';
                                label = 'Rectificación';
                                break;
                            case 'DELETION':
                                iconSvg = `<svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>`;
                                colorClass = 'bg-purple-100';
                                label = 'Cancelación';
                                break;
                            default:
                                iconSvg = `<svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`;
                                colorClass = 'bg-gray-100';
                                label = type;
                        }
                        
                        return `
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 ${colorClass} rounded-lg flex items-center justify-center">
                                        ${iconSvg}
                                    </div>
                                    <span class="font-medium text-gray-700">${label}</span>
                                </div>
                                <span class="font-bold text-gray-900">${count}</span>
                            </div>
                        `;
                    }).join('')}
                </div>
            </div>
        </div>
    `;
    

}



function setupAuditsModal(title, subtitle, icon, body, actionBtn, actionText) {
    title.textContent = 'Auditorías del Sistema aqui es';
    subtitle.textContent = 'Estado y seguimiento de auditorías';
    icon.innerHTML = `<svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>`;
    icon.className = 'w-12 h-12 bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-xl flex items-center justify-center';
    
    // Usar datos reales del backend
    const auditsInProgress = {{ $kpis['audits'] ?? 0 }};
    const completionRate = {{ $performance['audit_completion_rate'] ?? 0 }};
    const auditStatus = @json($charts['audit_status'] ?? []);
    
    body.innerHTML = `
        <div class="space-y-6">
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-indigo-50 p-4 rounded-xl">
                    <p class="text-sm text-indigo-600 font-medium">En progreso</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">${auditsInProgress}</p>
                </div>
                <div class="bg-green-50 p-4 rounded-xl">
                    <p class="text-sm text-green-600 font-medium">Tasa de completitud</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">${completionRate}%</p>
                </div>
            </div>
            
            <div class="space-y-4">
                <h3 class="font-semibold text-gray-900">Distribución por Estado</h3>
                <div class="space-y-3">
                    ${auditStatus.length > 0 ? auditStatus.map(status => {
                        let colorClass = '';
                        let iconSvg = '';
                        let label = '';
                        
                        switch(status.status) {
                            case 'PLANNED':
                                colorClass = 'bg-indigo-100';
                                iconSvg = `<svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>`;
                                label = 'Planificada';
                                break;
                            case 'IN_PROGRESS':
                                colorClass = 'bg-blue-100';
                                iconSvg = `<svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`;
                                label = 'En progreso';
                                break;
                            case 'COMPLETED':
                                colorClass = 'bg-green-100';
                                iconSvg = `<svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`;
                                label = 'Completada';
                                break;
                            default:
                                colorClass = 'bg-gray-100';
                                iconSvg = `<svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`;
                                label = status.status;
                        }
                        
                        return `
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 ${colorClass} rounded-lg flex items-center justify-center">
                                        ${iconSvg}
                                    </div>
                                    <span class="font-medium text-gray-700">${label}</span>
                                </div>
                                <span class="font-bold text-gray-900">${status.count}</span>
                            </div>
                        `;
                    }).join('') : '<p class="text-gray-500 text-center py-4">No hay datos de auditorías disponibles</p>'}
                </div>
            </div>
        </div>
    `;
    
    actionText.textContent = 'Ver auditorías';
    actionBtn.onclick = () => {
        closeModal();
        showNotification('Redirigiendo a auditorías...', 'info');
        window.location.href = '/audit/audits';
    };
}


    function setupDsarModal(title, subtitle, icon, body, actionBtn, actionText) {
        title.textContent = 'Solicitudes DSAR este es';
        subtitle.textContent = 'Estado de solicitudes de derechos de los titulares';
        icon.innerHTML = `<svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
        </svg>`;
        icon.className = 'w-12 h-12 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl flex items-center justify-center';
        
        const openDsars = {{ $kpis['dsar_requests']['open'] ?? 0 }};
        const overdueDsars = {{ $kpis['dsar_requests']['overdue'] ?? 0 }};
        const resolvedDsars = {{ $kpis['dsar_requests']['resolved'] ?? 0 }};
        
        body.innerHTML = `
            <div class="space-y-6">
                <div class="grid grid-cols-3 gap-4">
                    <div class="bg-yellow-50 p-4 rounded-xl">
                        <p class="text-sm text-yellow-600 font-medium">Abiertas</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">${openDsars}</p>
                    </div>
                    <div class="bg-red-50 p-4 rounded-xl">
                        <p class="text-sm text-red-600 font-medium">Vencidas</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">${overdueDsars}</p>
                    </div>
                    <div class="bg-green-50 p-4 rounded-xl">
                        <p class="text-sm text-green-600 font-medium">Resueltas</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">${resolvedDsars}</p>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <h3 class="font-semibold text-gray-900">Solicitudes por Tipo</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                                <span class="font-medium text-gray-700">Acceso a datos</span>
                            </div>
                            <span class="font-bold text-gray-900">{{ $kpis['dsar_by_type']['ACCESS'] ?? 0 }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </div>
                                <span class="font-medium text-gray-700">Rectificación</span>
                            </div>
                            <span class="font-bold text-gray-900">{{ $kpis['dsar_by_type']['RECTIFICATION'] ?? 0 }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </div>
                                <span class="font-medium text-gray-700">Cancelación</span>
                            </div>
                            <span class="font-bold text-gray-900">{{ $kpis['dsar_by_type']['DELETION'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        actionText.textContent = 'Gestionar DSARs';
        actionBtn.onclick = () => {
            closeModal();
            window.location.href = '/dsar';
        };
    }

    function setupRisksModal(title, subtitle, icon, body, actionBtn, actionText) {
        title.textContent = 'Gestión de Riesgos';
        subtitle.textContent = 'Riesgos identificados y su nivel de severidad';
        icon.innerHTML = `<svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
        </svg>`;
        icon.className = 'w-12 h-12 bg-gradient-to-br from-red-50 to-red-100 rounded-xl flex items-center justify-center';
        
        const highRisks = {{ $kpis['risks']['HIGH'] ?? 0 }};
        const mediumRisks = {{ $kpis['risks']['MEDIUM'] ?? 0 }};
        const lowRisks = {{ $kpis['risks']['LOW'] ?? 0 }};
        const totalRisks = highRisks + mediumRisks + lowRisks;
        
        body.innerHTML = `
            <div class="space-y-6">
                <div class="grid grid-cols-3 gap-4">
                    <div class="bg-red-50 p-4 rounded-xl">
                        <p class="text-sm text-red-600 font-medium">Alto</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">${highRisks}</p>
                        <div class="text-xs text-red-500 mt-1">
                            ${totalRisks > 0 ? Math.round((highRisks/totalRisks)*100) : 0}% del total
                        </div>
                    </div>
                    <div class="bg-yellow-50 p-4 rounded-xl">
                        <p class="text-sm text-yellow-600 font-medium">Medio</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">${mediumRisks}</p>
                        <div class="text-xs text-yellow-500 mt-1">
                            ${totalRisks > 0 ? Math.round((mediumRisks/totalRisks)*100) : 0}% del total
                        </div>
                    </div>
                    <div class="bg-blue-50 p-4 rounded-xl">
                        <p class="text-sm text-blue-600 font-medium">Bajo</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">${lowRisks}</p>
                        <div class="text-xs text-blue-500 mt-1">
                            ${totalRisks > 0 ? Math.round((lowRisks/totalRisks)*100) : 0}% del total
                        </div>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <h3 class="font-semibold text-gray-900">Riesgos que requieren atención inmediata</h3>
                    <div class="space-y-3">
                        ${highRisks > 0 ? `
                            <div class="p-4 bg-red-50 border border-red-200 rounded-xl">
                                <div class="flex items-center gap-3">
                                    <div class="w-3 h-3 bg-red-500 rounded-full animate-pulse"></div>
                                    <p class="font-medium text-gray-900">${highRisks} riesgos críticos</p>
                                </div>
                                <p class="text-sm text-gray-600 mt-2">Estos riesgos requieren atención inmediata y deben ser tratados en los próximos 7 días.</p>
                            </div>
                        ` : `
                            <div class="p-4 bg-green-50 border border-green-200 rounded-xl">
                                <div class="flex items-center gap-3">
                                    <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                    <p class="font-medium text-gray-900">No hay riesgos críticos</p>
                                </div>
                                <p class="text-sm text-gray-600 mt-2">Excelente gestión de riesgos en el sistema.</p>
                            </div>
                        `}
                    </div>
                </div>
            </div>
        `;
        
        actionText.textContent = 'Ver gestión de riesgos';
        actionBtn.onclick = () => {
            closeModal();
            window.location.href = '/risk/ui/risks';
        };
    }

    function setupAllAlertsModal(title, subtitle, icon, body, actionBtn, actionText) {
        title.textContent = 'Todas las Alertas';
        subtitle.textContent = 'Lista completa de alertas del sistema';
        icon.innerHTML = `<svg class="w-6 h-6 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
        </svg>`;
        icon.className = 'w-12 h-12 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl flex items-center justify-center';
        
        // Simular datos de todas las alertas
        const alerts = {!! json_encode($alerts) !!};
        
        body.innerHTML = `
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <h3 class="font-semibold text-gray-900">${alerts.length} alertas encontradas</h3>
                    <button onclick="markAllAlertsAsRead()" class="text-sm text-yellow-700 hover:text-yellow-800 font-medium px-3 py-1.5 bg-yellow-100 hover:bg-yellow-200 rounded-lg transition-colors">
                        Marcar todas como leídas
                    </button>
                </div>
                
                <div class="space-y-3 max-h-[400px] overflow-y-auto custom-scrollbar">
                    ${alerts.map((alert, index) => `
                        <div class="group p-4 bg-white rounded-xl border ${alert.priority === 'high' ? 'border-red-200 bg-red-50/50' : alert.priority === 'medium' ? 'border-yellow-200 bg-yellow-50/50' : 'border-blue-200 bg-blue-50/50'} hover:shadow-sm transition-all duration-200 cursor-pointer alert-item" data-id="${alert.id}">
                            <div class="flex justify-between items-start">
                                <div class="flex items-start gap-3">
                                    <div class="mt-0.5">
                                        ${alert.priority === 'high' ? 
                                            '<div class="w-3 h-3 bg-gradient-to-r from-red-500 to-red-600 rounded-full animate-pulse"></div>' : 
                                            alert.priority === 'medium' ? 
                                            '<div class="w-3 h-3 bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-full"></div>' : 
                                            '<div class="w-3 h-3 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full"></div>'}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-gray-900 group-hover:text-yellow-900">${alert.title}</p>
                                        <p class="text-sm text-gray-600 mt-1">${alert.description || 'Sin descripción'}</p>
                                        <div class="flex items-center gap-2 mt-2 flex-wrap">
                                            <span class="text-xs px-2 py-0.5 ${alert.priority === 'high' ? 'bg-red-100 text-red-800' : alert.priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800'} rounded-full">
                                                ${alert.type}
                                            </span>
                                            <span class="text-xs text-gray-500 whitespace-nowrap">
                                                ${new Date(alert.due_at).toLocaleDateString('es-ES')}
                                            </span>
                                            <span class="text-xs text-gray-500">
                                                ${new Date(alert.due_at).diffForHumans()}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <button onclick="markSingleAlertAsRead(event, ${alert.id})" 
                                        class="text-gray-300 hover:text-gray-500 group-hover:text-yellow-500 opacity-0 group-hover:opacity-100 transition-opacity p-1 hover:bg-yellow-100 rounded">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
        
        actionText.textContent = 'Configurar alertas';
        actionBtn.onclick = () => {
            closeModal();
            window.location.href = '/alerts/settings';
        };
    }

    function setupAllActivityModal(title, subtitle, icon, body, actionBtn, actionText) {
        title.textContent = 'Historial de Actividad';
        subtitle.textContent = 'Registro completo de actividades del sistema';
        icon.innerHTML = `<svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>`;
        icon.className = 'w-12 h-12 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl flex items-center justify-center';
        
        // Simular datos de actividad completa
        const activities = {!! json_encode($recentActivity) !!};
        
        body.innerHTML = `
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <h3 class="font-semibold text-gray-900">${activities.length} actividades registradas</h3>
                    <div class="flex gap-2">
                        <select class="text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 px-3 py-1.5 bg-gray-50">
                            <option>Últimas 24h</option>
                            <option>Última semana</option>
                            <option>Último mes</option>
                        </select>
                    </div>
                </div>
                
                <div class="space-y-3 max-h-[400px] overflow-y-auto custom-scrollbar">
                    ${activities.map((activity, index) => `
                        <div class="group p-4 bg-white rounded-xl border border-gray-200 hover:bg-blue-50 hover:border-blue-300 transition-all duration-200">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0">
                                    ${activity.type === 'Actividad de Tratamiento' ? 
                                        '<div class="w-10 h-10 bg-gradient-to-br from-blue-100 to-blue-200 rounded-xl flex items-center justify-center border border-blue-200"><svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg></div>' : 
                                        activity.type === 'DSAR' ? 
                                        '<div class="w-10 h-10 bg-gradient-to-br from-yellow-100 to-yellow-200 rounded-xl flex items-center justify-center border border-yellow-200"><svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg></div>' : 
                                        '<div class="w-10 h-10 bg-gradient-to-br from-indigo-100 to-purple-200 rounded-xl flex items-center justify-center border border-indigo-200"><svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>'}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900">${activity.name}</p>
                                    <p class="text-xs text-gray-500 mt-1">${activity.description || 'Sin descripción adicional'}</p>
                                    <div class="flex items-center gap-2 mt-2 flex-wrap">
                                        <span class="text-xs px-2 py-0.5 bg-gray-100 text-gray-800 rounded-full">
                                            ${activity.type}
                                        </span>
                                        <span class="text-xs text-gray-500">
                                            Por: ${activity.user_name || 'Sistema'}
                                        </span>
                                        <span class="text-xs text-gray-500 whitespace-nowrap">
                                            ${new Date(activity.created_at).toLocaleDateString('es-ES', {hour: '2-digit', minute: '2-digit'})}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
        
        actionText.textContent = 'Exportar historial';
        actionBtn.onclick = () => {
            showNotification('Historial exportado correctamente', 'success');
        };
    }

    function setupDefaultModal(title, subtitle, icon, body, actionBtn, actionText) {
        title.textContent = 'Detalles';
        subtitle.textContent = 'Información detallada';
        icon.innerHTML = `<svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>`;
        icon.className = 'w-12 h-12 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl flex items-center justify-center';
        
        body.innerHTML = `
            <div class="text-center py-8">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Información no disponible</h3>
                <p class="text-gray-600">Los detalles para esta sección no están disponibles en este momento.</p>
            </div>
        `;
        
        actionBtn.classList.add('hidden');
    }

    // ============================================
    // FUNCIONES DE ALERTAS (actualizadas para usar modales)
    // ============================================

    let readAlerts = JSON.parse(localStorage.getItem('sgpd_read_alerts') || '[]');

    function showAlertDetail(alertId) {
        // En una implementación real, aquí cargarías los detalles de la alerta desde una API
        const alert = {!! json_encode($alerts->first()) !!}; // Solo para demostración
        
        showModal('all-alerts');
    }

    function showAllAlertsModal() {
        showModal('all-alerts');
    }

    function showAllActivityModal() {
        showModal('all-activity');
    }

    function markSingleAlertAsRead(event, alertId) {
    event.stopPropagation();
    
    const alertElement = event.target.closest('.alert-item');
    if (!alertElement) return;
    
    // Guardar en localStorage que esta alerta fue leída
    if (!readAlerts.includes(alertId)) {
        readAlerts.push(alertId);
        localStorage.setItem('sgpd_read_alerts', JSON.stringify(readAlerts));
    }
    
    // También guardar en el servidor (opcional, pero recomendado)
    saveAlertAsReadToServer(alertId);
    
    // Animación de desaparición
    alertElement.classList.add('slide-out');
    
    setTimeout(() => {
        alertElement.remove();
        updateAlertCount();
        showNotification('Alerta marcada como leída', 'success');
    }, 300);
}

// Función para guardar en el servidor
async function saveAlertAsReadToServer(alertId) {
    try {
        await fetch(`/api/alerts/${alertId}/mark-as-read`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
    } catch (error) {
        console.error('Error al guardar en servidor:', error);
    }
}

    function markAllAlertsAsRead() {
        const alertElements = document.querySelectorAll('.alert-item');
        if (alertElements.length === 0) return;
        
        alertElements.forEach((alert, index) => {
            const alertId = alert.dataset.id;
            if (alertId && !readAlerts.includes(alertId)) {
                readAlerts.push(alertId);
            }
            
            setTimeout(() => {
                alert.classList.add('slide-out');
                setTimeout(() => {
                    alert.remove();
                    if (index === alertElements.length - 1) {
                        updateAlertCount();
                        localStorage.setItem('sgpd_read_alerts', JSON.stringify(readAlerts));
                        showNotification('Todas las alertas marcadas como leídas', 'success');
                    }
                }, 300);
            }, index * 100);
        });
    }

    function updateAlertCount() {
        const remainingAlerts = document.querySelectorAll('.alert-item').length;
        const alertCountElements = document.querySelectorAll('.text-sm.text-gray-500');
        
        alertCountElements.forEach(el => {
            if (el.textContent.includes('pendientes')) {
                el.textContent = `${remainingAlerts} pendientes`;
            }
        });
        
        if (remainingAlerts === 0) {
            const alertsContainer = document.querySelector('.space-y-3.pb-2');
            if (alertsContainer) {
                alertsContainer.innerHTML = `
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-gradient-to-br from-green-100 to-green-200 rounded-full flex items-center justify-center mx-auto mb-4 shadow-inner">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-gray-700">¡Excelente!</p>
                        <p class="text-xs text-gray-500 mt-1">No hay alertas pendientes</p>
                    </div>
                `;
            }
        }
    }

    function filterReadAlertsOnLoad() {
        document.querySelectorAll('.alert-item').forEach(alert => {
            const alertId = alert.dataset.id;
            if (readAlerts.includes(parseInt(alertId))) {
                alert.remove();
            }
        });
        updateAlertCount();
    }

    function showActivityDetail(type, activityId) {
        // En una implementación real, aquí cargarías los detalles de la actividad
        showModal('all-activity');
    }

    // ============================================
    // NOTIFICACIONES ELEGANTES
    // ============================================

    function showNotification(message, type = 'success') {
        const toast = document.createElement('div');
        const icons = {
            success: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>`,
            info: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>`,
            warning: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
            </svg>`
        };
        
        const bgColors = {
            success: 'from-green-500 to-emerald-600',
            info: 'from-blue-500 to-blue-600',
            warning: 'from-amber-500 to-orange-600'
        };
        
        toast.className = `fixed top-4 right-4 bg-gradient-to-r ${bgColors[type]} text-white px-6 py-4 rounded-2xl shadow-2xl transform transition-all duration-500 z-[10000] flex items-center gap-3 max-w-md`;
        toast.style.transform = 'translateX(400px)';
        toast.innerHTML = `
            <div class="flex-shrink-0">${icons[type]}</div>
            <div class="flex-1">
                <p class="font-semibold">${message}</p>
                <p class="text-xs opacity-90 mt-1">${new Date().toLocaleTimeString('es-ES', {hour: '2-digit', minute:'2-digit'})}</p>
            </div>
            <button onclick="this.parentElement.remove()" class="flex-shrink-0 text-white/70 hover:text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        `;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.style.transform = 'translateX(0)';
        }, 10);
        
        setTimeout(() => {
            toast.style.transform = 'translateX(400px)';
            setTimeout(() => {
                if (toast.parentElement) {
                    document.body.removeChild(toast);
                }
            }, 500);
        }, 4000);
    }

    // Descargar gráfico como imagen
    function downloadChart(canvasId, filename) {
        const canvas = document.getElementById(canvasId);
        const link = document.createElement('a');
        link.download = filename;
        link.href = canvas.toDataURL('image/png');
        link.click();
        showNotification('Gráfico descargado correctamente', 'success');
    }

    // ============================================
    // GRÁFICOS (igual que antes)
    // ============================================

    // Gráfico de tendencia DSAR mejorado
    let dsarChart;
    const dsarCtx = document.getElementById('dsarTrendChart');
    if (dsarCtx) {
        const dsarMonths = {!! json_encode($charts['dsar_trend']->pluck('month')->toArray()) !!};
        const dsarCounts = {!! json_encode($charts['dsar_trend']->pluck('count')->toArray()) !!};
        const dsarLabels = dsarMonths.map(m => formatMonth(m));
        
        const gradient = createGradient(dsarCtx.getContext('2d'), ['rgba(59, 130, 246, 0.3)', 'rgba(59, 130, 246, 0.1)'], true);
        
        dsarChart = new Chart(dsarCtx.getContext('2d'), {
            type: 'line',
            data: {
                labels: dsarLabels,
                datasets: [{
                    label: 'DSARs',
                    data: dsarCounts,
                    borderColor: colors.primary.solid,
                    backgroundColor: gradient,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: colors.primary.solid,
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { 
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'white',
                        titleColor: '#1f2937',
                        bodyColor: '#4b5563',
                        borderColor: '#e5e7eb',
                        borderWidth: 1,
                        cornerRadius: 8,
                        padding: 12,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return `${context.parsed.y} solicitudes`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            callback: function(value) {
                                return Number.isInteger(value) ? value : '';
                            }
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
    }

    // Gráfico de distribución de riesgos mejorado
    let riskChart;
    const riskCtx = document.getElementById('riskDistributionChart');
    if (riskCtx) {
        const riskLabels = {!! json_encode($charts['risk_distribution']->pluck('status')->toArray()) !!};
        const riskData = {!! json_encode($charts['risk_distribution']->pluck('count')->toArray()) !!};
        
        const backgroundColors = [
            'rgba(239, 68, 68, 0.8)',
            'rgba(245, 158, 11, 0.8)',
            'rgba(59, 130, 246, 0.8)',
            'rgba(139, 92, 246, 0.8)'
        ];
        
        const hoverColors = [
            'rgb(239, 68, 68)',
            'rgb(245, 158, 11)',
            'rgb(59, 130, 246)',
            'rgb(139, 92, 246)'
        ];
        
        riskChart = new Chart(riskCtx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: riskLabels,
                datasets: [{
                    data: riskData,
                    backgroundColor: backgroundColors,
                    hoverBackgroundColor: hoverColors,
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: { 
                    legend: { 
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            font: {
                                size: 11
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((context.parsed / total) * 100);
                                return `${context.label}: ${context.parsed} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    // Gráfico de estado de auditorías mejorado
    let auditChart;
    const auditCtx = document.getElementById('auditStatusChart');
    if (auditCtx) {
        const auditLabels = {!! json_encode($charts['audit_status']->pluck('status')->toArray()) !!};
        const auditData = {!! json_encode($charts['audit_status']->pluck('count')->toArray()) !!};
        
        const gradient = createGradient(auditCtx.getContext('2d'), ['#06b6d4', '#0891b2']);
        
        auditChart = new Chart(auditCtx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: auditLabels,
                datasets: [{
                    label: 'Auditorías',
                    data: auditData,
                    backgroundColor: gradient,
                    borderColor: colors.info.solid,
                    borderWidth: 1,
                    borderRadius: 8,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { 
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.parsed.y} auditorías`;
                            }
                        }
                    }
                },
                scales: {
                    y: { 
                        beginAtZero: true, 
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: { 
                            stepSize: 1,
                            precision: 0
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }

    // ============================================
    // INICIALIZACIÓN
    // ============================================

    document.addEventListener('DOMContentLoaded', () => {
        // Filtrar alertas ya leídas
        filterReadAlertsOnLoad();
        
        // Añadir clase de hover-lift a tarjetas
        document.querySelectorAll('.group.bg-white.rounded-2xl').forEach(card => {
            card.classList.add('hover-lift');
        });
        
        // Event listener para cerrar modal con ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && isModalOpen) {
                closeModal();
            }
        });
        
        // Event listener para cerrar modal al hacer clic fuera
        document.getElementById('modalOverlay').addEventListener('click', (e) => {
            if (e.target.id === 'modalOverlay') {
                closeModal();
            }
        });
        
        // Botón de refrescar
        document.getElementById('refreshDashboard').addEventListener('click', () => {
            const icon = document.getElementById('refreshIcon');
            icon.classList.add('animate-spin');
            showNotification('Actualizando datos del dashboard...', 'info');
            
            setTimeout(() => {
                icon.classList.remove('animate-spin');
                showNotification('Dashboard actualizado correctamente', 'success');
            }, 1000);
        });
        
        // Limpiar alertas leídas después de 24 horas
        const lastCleanup = localStorage.getItem('sgpd_last_cleanup');
        const now = new Date().getTime();
        const oneDay = 24 * 60 * 60 * 1000;
        
        if (!lastCleanup || (now - parseInt(lastCleanup)) > oneDay) {
            localStorage.removeItem('sgpd_read_alerts');
            localStorage.setItem('sgpd_last_cleanup', now.toString());
            readAlerts = [];
        }
    });
</script>
@endpush
@endsection