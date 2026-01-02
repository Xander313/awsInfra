<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SGPD COAC - Sistema de Gestión de Protección de Datos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        [x-cloak] { display: none !important; }
        .slide-enter {
            transform: translateX(-100%);
        }
        .slide-enter-active {
            transform: translateX(0);
            transition: transform 300ms ease-out;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div x-data="sgpdApp()" x-cloak class="min-h-screen">
        <!-- Header Superior -->
        <header class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm">
            <div class="flex items-center justify-between px-4 py-3">
                <button @click="menuOpen = !menuOpen" class="p-2 hover:bg-gray-100 rounded-lg">
                    <svg x-show="!menuOpen" class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg x-show="menuOpen" class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                
                <div class="flex items-center space-x-2">
                    <h1 class="text-lg font-bold text-gray-900">SGPD</h1>
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>

                <div class="flex items-center space-x-2">
                    <button @click="showNotifications = !showNotifications" class="relative p-2 hover:bg-gray-100 rounded-lg">
                        <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                    </button>
                    <button class="p-2 hover:bg-gray-100 rounded-lg">
                        <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Notificaciones Dropdown -->
            <div x-show="showNotifications" @click.away="showNotifications = false" 
                 class="absolute right-0 top-16 w-80 bg-white rounded-lg shadow-xl border border-gray-200 mx-4">
                <div class="p-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900">Notificaciones</h3>
                </div>
                <div class="divide-y divide-gray-100 max-h-80 overflow-y-auto">
                    <template x-for="activity in recentActivities" :key="activity.id">
                        <div class="p-4 hover:bg-gray-50">
                            <p class="text-sm font-medium text-gray-900" x-text="activity.desc"></p>
                            <p class="text-xs text-gray-500 mt-1" x-text="activity.time"></p>
                        </div>
                    </template>
                </div>
            </div>
        </header>

        <!-- Menú Lateral Deslizable -->
        <div x-show="menuOpen" @click="menuOpen = false"
             class="fixed inset-0 bg-black bg-opacity-50 z-40 transition-opacity">
            <div @click.stop class="fixed left-0 top-0 bottom-0 w-72 bg-white shadow-2xl"
                 :class="menuOpen ? 'translate-x-0' : '-translate-x-full'"
                 style="transition: transform 300ms ease-out;">
                <div class="p-6 bg-gradient-to-r from-blue-600 to-blue-800">
                    <div class="flex items-center space-x-3 text-white">
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-bold text-lg">SGPD COAC</h2>
                            <p class="text-xs text-blue-100">Sistema de Gestión</p>
                        </div>
                    </div>
                </div>

                <nav class="p-4 space-y-2">
                    <template x-for="module in modules" :key="module.id">
                        <button @click="activeModule = module.id; menuOpen = false"
                                class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors"
                                :class="activeModule === module.id ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50'">
                            <span x-html="module.icon"></span>
                            <span class="font-medium" x-text="module.name"></span>
                            <svg x-show="activeModule === module.id" class="w-5 h-5 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </template>
                </nav>

                <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-200">
                    <div class="flex items-center space-x-3 px-4 py-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white font-bold">
                            AG
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 truncate">Ana García</p>
                            <p class="text-xs text-gray-600">DPO Principal</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenido Principal -->
        <main class="pb-20 px-4 pt-6">
            <!-- Dashboard -->
            <div x-show="activeModule === 'dashboard'" class="space-y-4">
                <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-xl p-6 text-white">
                    <h2 class="text-2xl font-bold mb-2">SGPD COAC</h2>
                    <p class="text-blue-100">Sistema de Gestión de Protección de Datos</p>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-100">
                        <div class="flex items-center justify-between mb-2">
                            <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            <span class="text-2xl font-bold text-gray-800" x-text="stats.processingActivities"></span>
                        </div>
                        <p class="text-xs text-gray-600">Actividades de Procesamiento</p>
                    </div>

                    <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-100">
                        <div class="flex items-center justify-between mb-2">
                            <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span class="text-2xl font-bold text-gray-800" x-text="stats.dsarRequests"></span>
                        </div>
                        <p class="text-xs text-gray-600">Solicitudes DSAR</p>
                    </div>

                    <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-100">
                        <div class="flex items-center justify-between mb-2">
                            <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <span class="text-2xl font-bold text-gray-800" x-text="stats.risks"></span>
                        </div>
                        <p class="text-xs text-gray-600">Riesgos Activos</p>
                    </div>

                    <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-100">
                        <div class="flex items-center justify-between mb-2">
                            <svg class="w-8 h-8 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            <span class="text-2xl font-bold text-gray-800" x-text="stats.trainings"></span>
                        </div>
                        <p class="text-xs text-gray-600">Capacitaciones</p>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                    <div class="p-4 border-b border-gray-100">
                        <h3 class="font-semibold text-gray-800">Actividad Reciente</h3>
                    </div>
                    <div class="divide-y divide-gray-100">
                        <template x-for="activity in recentActivities" :key="activity.id">
                            <div class="p-4 flex items-start space-x-3">
                                <div class="w-2 h-2 rounded-full mt-2"
                                     :class="{
                                         'bg-yellow-500': activity.status === 'pending',
                                         'bg-red-500': activity.status === 'alert',
                                         'bg-green-500': activity.status === 'success',
                                         'bg-blue-500': activity.status === 'info'
                                     }"></div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900" x-text="activity.desc"></p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        <span x-text="activity.type"></span> • <span x-text="activity.time"></span>
                                    </p>
                                </div>
                                <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Módulo Privacidad -->
            <div x-show="activeModule === 'privacy'" class="space-y-4">
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
                    <div class="flex border-b border-gray-200">
                        <button @click="privacyTab = 'processing'"
                                class="flex-1 py-3 px-4 text-sm font-medium"
                                :class="privacyTab === 'processing' ? 'bg-blue-50 text-blue-600 border-b-2 border-blue-600' : 'text-gray-600'">
                            Actividades
                        </button>
                        <button @click="privacyTab = 'dsar'"
                                class="flex-1 py-3 px-4 text-sm font-medium"
                                :class="privacyTab === 'dsar' ? 'bg-blue-50 text-blue-600 border-b-2 border-blue-600' : 'text-gray-600'">
                            DSAR
                        </button>
                        <button @click="privacyTab = 'consent'"
                                class="flex-1 py-3 px-4 text-sm font-medium"
                                :class="privacyTab === 'consent' ? 'bg-blue-50 text-blue-600 border-b-2 border-blue-600' : 'text-gray-600'">
                            Consentimientos
                        </button>
                    </div>

                    <!-- Tab Actividades -->
                    <div x-show="privacyTab === 'processing'" class="p-4 space-y-3">
                        <template x-for="activity in processingActivities" :key="activity.id">
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-start justify-between mb-2">
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-900 text-sm" x-text="activity.name"></h4>
                                        <p class="text-xs text-gray-600 mt-1" x-text="activity.purpose"></p>
                                    </div>
                                    <span class="px-2 py-1 rounded-full text-xs font-medium"
                                          :class="{
                                              'bg-red-100 text-red-700': activity.risk === 'high',
                                              'bg-yellow-100 text-yellow-700': activity.risk === 'medium',
                                              'bg-green-100 text-green-700': activity.risk === 'low'
                                          }"
                                          x-text="activity.risk === 'high' ? 'Alto' : activity.risk === 'medium' ? 'Medio' : 'Bajo'">
                                    </span>
                                </div>
                                <div class="flex items-center space-x-2 mt-3">
                                    <button class="flex-1 flex items-center justify-center space-x-1 px-3 py-2 bg-blue-50 text-blue-600 rounded-lg text-xs font-medium">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        <span>Ver</span>
                                    </button>
                                    <button class="flex-1 flex items-center justify-center space-x-1 px-3 py-2 bg-gray-50 text-gray-600 rounded-lg text-xs font-medium">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        <span>Editar</span>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Tab DSAR -->
                    <div x-show="privacyTab === 'dsar'" class="p-4 space-y-3">
                        <template x-for="request in dsarRequests" :key="request.id">
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-start justify-between mb-2">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2">
                                            <h4 class="font-semibold text-gray-900 text-sm" x-text="request.type"></h4>
                                            <span class="px-2 py-0.5 rounded-full text-xs font-medium"
                                                  :class="{
                                                      'bg-green-100 text-green-700': request.status === 'completed',
                                                      'bg-blue-100 text-blue-700': request.status === 'inProgress',
                                                      'bg-yellow-100 text-yellow-700': request.status === 'pending'
                                                  }"
                                                  x-text="request.status === 'completed' ? 'Completado' : request.status === 'inProgress' ? 'En Proceso' : 'Pendiente'">
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-600 mt-1" x-text="request.requester"></p>
                                        <p class="text-xs text-gray-500 mt-1">Fecha: <span x-text="request.date"></span></p>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-100">
                                    <span class="text-xs text-gray-600">
                                        <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span x-text="request.deadline"></span>
                                    </span>
                                    <button class="px-3 py-1 bg-blue-600 text-white rounded-lg text-xs font-medium">
                                        Gestionar
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Tab Consentimientos -->
                    <div x-show="privacyTab === 'consent'" class="p-4">
                        <div class="text-center py-8">
                            <svg class="w-16 h-16 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            <p class="text-sm text-gray-600">Gestión de consentimientos</p>
                            <button class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium">
                                Registrar Consentimiento
                            </button>
                        </div>
                    </div>
                </div>

                <button class="w-full flex items-center justify-center space-x-2 px-4 py-3 bg-blue-600 text-white rounded-lg font-medium shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Nueva Actividad</span>
                </button>
            </div>

            <!-- Módulo Riesgos -->
            <div x-show="activeModule === 'risks'" class="space-y-4">
                <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-lg p-4 text-white">
                    <h3 class="text-lg font-bold mb-1">Gestión de Riesgos</h3>
                    <p class="text-sm text-red-100">8 riesgos activos requieren atención</p>
                </div>

                <div class="grid grid-cols-3 gap-2">
                    <div class="bg-white rounded-lg p-3 text-center border border-red-100">
                        <div class="text-2xl font-bold text-red-600">2</div>
                        <div class="text-xs text-gray-600 mt-1">Críticos</div>
                    </div>
                    <div class="bg-white rounded-lg p-3 text-center border border-orange-100">
                        <div class="text-2xl font-bold text-orange-600">4</div>
                        <div class="text-xs text-gray-600 mt-1">Altos</div>
                    </div>
                    <div class="bg-white rounded-lg p-3 text-center border border-yellow-100">
                        <div class="text-2xl font-bold text-yellow-600">2</div>
                        <div class="text-xs text-gray-600 mt-1">Medios</div>
                    </div>
                </div>

                <div class="space-y-3">
                    <template x-for="risk in risks" :key="risk.id">
                        <div class="bg-white rounded-lg border border-gray-200 p-4">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-900 text-sm mb-1" x-text="risk.name"></h4>
                                    <div class="flex items-center space-x-2 mt-2">
                                        <span class="px-2 py-0.5 rounded text-xs font-medium"
                                              :class="{
                                                  'bg-red-100 text-red-700': risk.severity === 'critical',
                                                  'bg-orange-100 text-orange-700': risk.severity === 'high',
                                                  'bg-yellow-100 text-yellow-700': risk.severity === 'medium'
                                              }"
                                              x-text="risk.severity === 'critical' ? 'Crítico' : risk.severity === 'high' ? 'Alto' : 'Medio'">
                                        </span>
                                        <span class="px-2 py-0.5 rounded text-xs font-medium"
                                              :class="{
                                                  'bg-red-50 text-red-600': risk.status === 'open',
                                                  'bg-blue-50 text-blue-600': risk.status === 'mitigating',
                                                  'bg-green-50 text-green-600': risk.status === 'monitoring'
                                              }"
                                              x-text="risk.status === 'open' ? 'Abierto' : risk.status === 'mitigating' ? 'Mitigando' : 'Monitoreando'">
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-xs">
                                <div class="bg-gray-50 rounded p-2">
                                    <span class="text-gray-600">Probabilidad:</span>
                                    <span class="font-medium text-gray-900 ml-1" x-text="risk.probability === 'high' ? 'Alta' : risk.probability === 'medium' ? 'Media' : 'Baja'"></span>
                                </div>
                                <div class="bg-gray-50 rounded p-2">
                                    <span class="text-gray-600">Impacto:</span>
                                    <span class="font-medium text-gray-900 ml-1" x-text="risk.impact === 'high' ? 'Alto' : risk.impact === 'medium' ? 'Medio' : 'Bajo'"></span>
                                </div>
                            </div>
                            <button class="w-full mt-3 py-2 bg-blue-50 text-blue-600 rounded-lg text-sm font-medium">
                                Ver Detalles y Controles
                            </button>
                        </div>
                    </template>
                </div>

                <button class="w-full flex items-center justify-center space-x-2 px-4 py-3 bg-red-600 text-white rounded-lg font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Registrar Nuevo Riesgo</span>
                </button>
            </div>

            <!-- Módulo Usuarios IAM -->
            <div x-show="activeModule === 'iam'" class="space-y-4">
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4">
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" placeholder="Buscar usuario..." 
                               class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="space-y-3">
                    <template x-for="user in users" :key="user.id">
                        <div class="bg-white rounded-lg border border-gray-200 p-4">
                            <div class="flex items-start space-x-3">
                                <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-sm"
                                     x-text="user.name.split(' ').map(n => n[0]).join('')">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <h4 class="font-semibold text-gray-900 text-sm" x-text="user.name"></h4>
                                        <span class="px-2 py-0.5 rounded-full text-xs font-medium"
                                              :class="user.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'"
                                              x-text="user.status === 'active' ? 'Activo' : 'Inactivo'">
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-600 mt-1" x-text="user.role"></p>
                                    <p class="text-xs text-gray-500 mt-1">Último acceso: <span x-text="user.lastLogin"></span></p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2 mt-3">
                                <button class="flex-1 px-3 py-2 bg-blue-50 text-blue-600 rounded-lg text-xs font-medium">
                                    Ver Permisos
                                </button>
                                <button class="flex-1 px-3 py-2 bg-gray-50 text-gray-600 rounded-lg text-xs font-medium">
                                    Editar
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                <button class="w-full flex items-center justify-center space-x-2 px-4 py-3 bg-purple-600 text-white rounded-lg font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Agregar Usuario</span>
                </button>
            </div>

            <!-- Módulo Documentos -->
            <div x-show="activeModule === 'documents'" class="space-y-4">
                <div class="flex items-center space-x-2">
                    <button class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium flex items-center justify-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <span>Subir</span>
                    </button>
                    <button class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                    </button>
                </div>

                <div class="space-y-3">
                    <template x-for="doc in documents" :key="doc.id">
                        <div class="bg-white rounded-lg border border-gray-200 p-4">
                            <div class="flex items-start space-x-3">
                                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-semibold text-gray-900 text-sm truncate" x-text="doc.name"></h4>
                                    <div class="flex items-center space-x-2 mt-1">
                                        <span class="text-xs text-gray-600" x-text="doc.type"></span>
                                        <span class="text-xs text-gray-400">•</span>
                                        <span class="text-xs text-gray-600" x-text="doc.size"></span>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1" x-text="doc.date"></p>
                                </div>
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium flex-shrink-0"
                                      :class="{
                                          'bg-green-100 text-green-700': doc.status === 'approved',
                                          'bg-yellow-100 text-yellow-700': doc.status === 'review',
                                          'bg-gray-100 text-gray-700': doc.status === 'draft'
                                      }"
                                      x-text="doc.status === 'approved' ? 'Aprobado' : doc.status === 'review' ? 'Revisión' : 'Borrador'">
                                </span>
                            </div>
                            <div class="flex items-center space-x-2 mt-3">
                                <button class="flex-1 px-3 py-2 bg-blue-50 text-blue-600 rounded-lg text-xs font-medium flex items-center justify-center space-x-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    <span>Descargar</span>
                                </button>
                                <button class="flex-1 px-3 py-2 bg-gray-50 text-gray-600 rounded-lg text-xs font-medium flex items-center justify-center space-x-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <span>Ver</span>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Módulo Capacitación -->
            <div x-show="activeModule === 'training'" class="space-y-4">
                <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 rounded-lg p-4 text-white">
                    <h3 class="text-lg font-bold mb-1">Capacitación</h3>
                    <p class="text-sm text-indigo-100">2 de 3 cursos completados</p>
                </div>

                <div class="space-y-3">
                    <template x-for="course in courses" :key="course.id">
                        <div class="bg-white rounded-lg border border-gray-200 p-4">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-900 text-sm" x-text="course.name"></h4>
                                    <p class="text-xs text-gray-600 mt-1">Duración: <span x-text="course.duration"></span></p>
                                </div>
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium"
                                      :class="{
                                          'bg-green-100 text-green-700': course.status === 'completed',
                                          'bg-blue-100 text-blue-700': course.status === 'inProgress',
                                          'bg-gray-100 text-gray-700': course.status === 'notStarted'
                                      }"
                                      x-text="course.status === 'completed' ? 'Completado' : course.status === 'inProgress' ? 'En Progreso' : 'No Iniciado'">
                                </span>
                            </div>
                            
                            <div class="space-y-2">
                                <div class="flex items-center justify-between text-xs text-gray-600">
                                    <span>Progreso</span>
                                    <span class="font-medium" x-text="course.progress + '%'"></span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="h-2 rounded-full transition-all"
                                         :class="course.progress === 100 ? 'bg-green-500' : 'bg-blue-500'"
                                         :style="`width: ${course.progress}%`">
                                    </div>
                                </div>
                            </div>

                            <button class="w-full mt-3 py-2 rounded-lg text-sm font-medium"
                                    :class="course.status === 'completed' ? 'bg-green-50 text-green-600' : 'bg-blue-600 text-white'"
                                    x-text="course.status === 'completed' ? 'Revisar Certificado' : course.status === 'inProgress' ? 'Continuar' : 'Iniciar Curso'">
                            </button>
                        </div>
                    </template>
                </div>
            </div>
        </main>

        <!-- Barra de Navegación Inferior -->
        <nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-lg">
            <div class="flex items-center justify-around px-2 py-2">
                <template x-for="module in modules.slice(0, 5)" :key="module.id">
                    <button @click="activeModule = module.id"
                            class="flex flex-col items-center space-y-1 px-3 py-2 rounded-lg transition-colors"
                            :class="activeModule === module.id ? 'text-blue-600' : 'text-gray-500'">
                        <div x-html="module.icon" 
                             :class="activeModule === module.id ? 'scale-110' : ''"
                             class="transition-transform"></div>
                        <span class="text-xs font-medium" x-text="module.name.split(' ')[0]"></span>
                    </button>
                </template>
            </div>
        </nav>
    </div>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <script>
        function sgpdApp() {
            return {
                activeModule: 'dashboard',
                menuOpen: false,
                showNotifications: false,
                privacyTab: 'processing',
                
                stats: {
                    processingActivities: 45,
                    dsarRequests: 12,
                    risks: 8,
                    audits: 3,
                    documents: 156,
                    trainings: 24
                },
                
                modules: [
                    { 
                        id: 'dashboard', 
                        name: 'Dashboard', 
                        icon: '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>'
                    },
                    { 
                        id: 'privacy', 
                        name: 'Privacidad', 
                        icon: '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>'
                    },
                    { 
                        id: 'iam', 
                        name: 'Usuarios', 
                        icon: '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>'
                    },
                    { 
                        id: 'documents', 
                        name: 'Documentos', 
                        icon: '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>'
                    },
                    { 
                        id: 'risks', 
                        name: 'Riesgos', 
                        icon: '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>'
                    },
                    { 
                        id: 'training', 
                        name: 'Capacitación', 
                        icon: '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>'
                    }
                ],
                
                recentActivities: [
                    { id: 1, type: 'DSAR', desc: 'Nueva solicitud de acceso', time: '5 min', status: 'pending' },
                    { id: 2, type: 'Risk', desc: 'Riesgo evaluado - Alto', time: '1 hora', status: 'alert' },
                    { id: 3, type: 'Audit', desc: 'Auditoría completada', time: '3 horas', status: 'success' },
                    { id: 4, type: 'Training', desc: 'Capacitación asignada', time: '1 día', status: 'info' }
                ],
                
                processingActivities: [
                    { id: 1, name: 'Gestión de Socios', purpose: 'Administración de membresía', status: 'active', risk: 'medium' },
                    { id: 2, name: 'Procesamiento de Préstamos', purpose: 'Evaluación crediticia', status: 'active', risk: 'high' },
                    { id: 3, name: 'Marketing Digital', purpose: 'Comunicaciones promocionales', status: 'review', risk: 'low' }
                ],
                
                dsarRequests: [
                    { id: 1, type: 'Acceso', requester: 'Juan Pérez', date: '2024-12-15', status: 'pending', deadline: '15 días' },
                    { id: 2, type: 'Portabilidad', requester: 'María López', date: '2024-12-10', status: 'inProgress', deadline: '10 días' },
                    { id: 3, type: 'Rectificación', requester: 'Carlos Ruiz', date: '2024-12-05', status: 'completed', deadline: 'Completado' }
                ],
                
                risks: [
                    { id: 1, name: 'Fuga de datos financieros', severity: 'critical', probability: 'high', impact: 'high', status: 'open' },
                    { id: 2, name: 'Acceso no autorizado', severity: 'high', probability: 'medium', impact: 'high', status: 'mitigating' },
                    { id: 3, name: 'Pérdida de respaldo', severity: 'medium', probability: 'low', impact: 'medium', status: 'monitoring' }
                ],
                
                users: [
                    { id: 1, name: 'Ana García', role: 'DPO', status: 'active', lastLogin: '2 horas' },
                    { id: 2, name: 'Luis Martínez', role: 'Auditor', status: 'active', lastLogin: '5 horas' },
                    { id: 3, name: 'Carmen Silva', role: 'Operador', status: 'inactive', lastLogin: '3 días' }
                ],
                
                documents: [
                    { id: 1, name: 'Política de Privacidad', type: 'PDF', size: '2.4 MB', date: '2024-12-01', status: 'approved' },
                    { id: 2, name: 'Manual de Procedimientos', type: 'DOCX', size: '1.8 MB', date: '2024-11-28', status: 'review' },
                    { id: 3, name: 'Registro de Actividades', type: 'XLSX', size: '856 KB', date: '2024-12-15', status: 'draft' }
                ],
                
                courses: [
                    { id: 1, name: 'Fundamentos GDPR', progress: 75, status: 'inProgress', duration: '2 horas' },
                    { id: 2, name: 'Seguridad de Datos', progress: 100, status: 'completed', duration: '1.5 horas' },
                    { id: 3, name: 'Gestión de Incidentes', progress: 0, status: 'notStarted', duration: '3 horas' }
                ]
            }
        }
    </script>
</body>
</html>