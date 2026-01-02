{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<head>

       <!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'SGPD COAC - Sistema de Gestión de Protección de Datos')</title>

    {{-- Tipografía global (elige una; aquí uso Montserrat por estética institucional) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- ✅ Opción A (simple): Tailwind por CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- ✅ Opción B (pro): Vite (descomenta cuando tengan Tailwind compilado) --}}
    {{--
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    --}}

    <style>
        [x-cloak] { display: none !important; }

        :root {
            --sgpd-font: "Montserrat", ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial, "Apple Color Emoji", "Segoe UI Emoji";
        }

        html, body { height: 100%; }

        body {
            font-family: var(--sgpd-font);
            font-size: 14px;
            line-height: 1.45;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Scrollbar ligera (opcional) */
        ::-webkit-scrollbar { width: 10px; height: 10px; }
        ::-webkit-scrollbar-thumb { background: rgba(100, 116, 139, .35); border-radius: 999px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(100, 116, 139, .55); }
    </style>

    @stack('styles')
</head>

<body class="bg-gray-50 text-gray-800">
@php
    // Navegación base (el equipo puede editar labels/urls sin tocar el layout)
    $nav = [
        [
            'label' => 'Dashboard',
            'items' => [
                ['label' => 'Inicio', 'href' => route('dashboard'), 'key' => 'dashboard'],
            ],
        ],
        [
            'label' => 'CORE',
            'items' => [
                ['label' => 'Organizaciones', 'href' => route('orgs.index'), 'key' => 'org'],
            ],
        ],
        [
            'label' => 'IAM',
            'items' => [
                ['label' => 'Usuarios', 'href' => route('users.index'), 'key' => 'users'],
                ['label' => 'Roles', 'href' => route('roles.index'), 'key' => 'roles'],
                ['label' => 'Permisos', 'href' => route('permissions.index'), 'key' => 'permissions'],
            ],
        ],
        [
            'label' => 'PRIVACY',
            'items' => [
                ['label' => 'Catálogos (Base)', 'href' => route('privacy.data_category.index'), 'key' => 'privacy_catalogs'],
                ['label' => 'Sistemas / Data Stores', 'href' => route('systems.index'), 'key' => 'systems'],
                ['label' => 'Destinatarios', 'href' => route('recipients.index'), 'key' => 'recipients'],
                ['label' => 'RAT: Actividades de Tratamiento', 'href' => route('rat.index'), 'key' => 'rat'],
                ['label' => 'Titulares / Consentimientos', 'href' => route('data-subjects.index'), 'key' => 'subjects'],
                ['label' => 'Documentos', 'href' => route('documents.index'), 'key' => 'documents'],
                ['label' => 'DSAR', 'href' => route('dsar.index'), 'key' => 'dsar'],
            ],
        ],
        [
            'label' => 'RISK & AUDIT',
            'items' => [
                ['label' => 'Riesgos', 'href' => url('/risk/ui/risks'), 'key' => 'risks'],
                ['label' => 'DPIA', 'href' => url('/risk/ui/dpias'), 'key' => 'dpia'],
                [
                    'label' => 'Auditoría',
                    'href' => "#",
                    'key' => 'audits',
                    'submenu' => [
                        ['label' => 'Auditorias', 'href' => route('audits.index'), 'key' => 'audits'],
                        ['label' => 'Controles', 'href' => route('controls.index'), 'key' => 'controls'],
                        ['label' => 'Hallazgos', 'href' => route('findings.index'), 'key' => 'findings'],
                        ['label' => 'Acciones Correctivas', 'href' => route('corrective_actions.index'), 'key' => 'corrective_actions'],
                    ],
                ],
            ],
        ],
        [
            'label' => 'TRAINING',
            'items' => [
                ['label' => 'Paises', 'href' => route('privacy.country.index'), 'key' => 'country'],
                ['label' => 'Asignaciones', 'href' => '/training/assignments', 'key' => 'assignments'],
                ['label' => 'Resultados',   'href' => '/training/results',    'key' => 'results'],

            ],
        ],
    ];
@endphp

<div x-data="sgpdLayout()" x-cloak @keydown.escape.window="closeAll()" class="min-h-screen">
    {{-- HEADER --}}
    <header class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm">
        <div class="flex items-center justify-between px-4 py-3">
            {{-- Toggle (junto al sidebar) --}}
            <div class="flex items-center gap-2">
                <button type="button"
                        @click="toggleSidebar()"
                        class="p-2 hover:bg-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        aria-label="Abrir/cerrar menú">
                    {{-- Icono hamburguesa / X --}}
                    <svg x-show="!sidebarOpen" class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg x-show="sidebarOpen" class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <div class="flex items-center space-x-2">
                    <h1 class="text-lg font-bold text-gray-900 tracking-tight">SGPD</h1>
                    <span class="text-xs px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-100">COAC</span>
                </div>
            </div>

            {{-- Acciones derecha --}}
            <div class="flex items-center space-x-2">
                {{-- Notificaciones --}}
                <div class="relative">
                    <button
                        type="button"
                        @click="showNotifications = !showNotifications; loadRealNotifications()"
                        class="relative p-2 hover:bg-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        aria-label="Notificaciones">
                        <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span x-show="notificationCount > 0" class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                        <span x-show="notificationCount > 0" class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center" x-text="notificationCount"></span>
                    </button>

                    {{-- Dropdown --}}
                    <div
                        x-show="showNotifications"
                        @click.away="showNotifications = false"
                        x-transition.opacity
                        class="absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-xl border border-gray-200 overflow-hidden z-50">
                        <div class="p-4 border-b border-gray-100">
                            <h3 class="font-semibold text-gray-900">Notificaciones del Sistema</h3>
                            <p class="text-xs text-gray-500 mt-0.5" x-text="`${notificationCount} alertas pendientes`"></p>
                        </div>
                        
                        <!-- CONTENEDOR DONDE SE MOSTRARÁN LAS NOTIFICACIONES REALES -->
                        <div id="realNotifications" class="divide-y divide-gray-100 max-h-80 overflow-y-auto">
                            <!-- Las notificaciones se cargarán aquí dinámicamente -->
                            <div x-show="loadingNotifications" class="p-6 text-center">
                                <div class="inline-block animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
                                <p class="text-sm text-gray-500 mt-2">Cargando notificaciones...</p>
                            </div>
                        </div>
                        
                        <div class="p-3 border-t border-gray-100 bg-gray-50">
                            <button @click="markAllAsRead()" class="text-xs text-blue-600 hover:text-blue-800">
                                Marcar todas como leídas
                            </button>
                            <a href="{{ route('dashboard') }}" class="text-xs text-gray-600 hover:text-gray-800 ml-3">
                                Ver todas en dashboard
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Ajustes --}}
                <a href="#" class="p-2 hover:bg-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" aria-label="Ajustes">
                    <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </a>

                {{-- Usuario (placeholder) --}}
                <div class="hidden sm:flex items-center gap-2 pl-2">
                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-600 to-blue-800 text-white flex items-center justify-center font-bold text-xs">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
                    </div>
                    <div class="leading-tight">
                        <div class="text-sm font-semibold text-gray-900 truncate max-w-[160px]">
                            {{ auth()->user()->name ?? 'Usuario' }}
                        </div>
                        <div class="text-xs text-gray-500 truncate max-w-[160px]">
                            {{ auth()->user()->email ?? 'sesión' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    {{-- SIDEBAR + OVERLAY (mobile) --}}
    <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-30 bg-black/50 md:hidden" @click="sidebarOpen = false"></div>

    {{--
        FIX DE DISPOSICIÓN (DESKTOP):
        - El MAIN usa md:ml-72 para dejar espacio al sidebar.
        - Por lo tanto, el SIDEBAR en desktop debe quedarse "fixed" (fuera del flujo),
          y solo bajarse con md:top-16 para no quedar debajo del header sticky.
        - Si lo pones md:static, empuja el contenido + además ml-72 => doble espacio (contenido al centro).
    --}}
    <aside
        class="fixed z-40 left-0 top-0 bottom-0 w-72 bg-white border-r border-gray-200 shadow-xl md:shadow-none flex flex-col md:top-16"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
        style="transition: transform 280ms ease-out;">

        {{-- Sidebar header --}}
        <div class="p-5 bg-gradient-to-r from-blue-600 to-blue-800">
            <div class="flex items-center space-x-3 text-white">
                <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-lg leading-tight">SGPD COAC</h2>
                    <p class="text-xs text-blue-100">Layout global (equipo)</p>
                </div>
            </div>
        </div>

        {{-- Sidebar nav --}}
        <nav class="p-4 overflow-y-auto flex-1 space-y-5">
            @foreach($nav as $section)
                <div>
                    <div class="text-[11px] uppercase tracking-wider text-gray-400 font-semibold px-2 mb-2">
                        {{ $section['label'] }}
                    </div>

                    <div class="space-y-1">
                        @foreach($section['items'] as $item)
                            <div x-data="{ open: false }">
                                {{-- Item principal --}}
                                <a href="{{ $item['href'] }}"
                                @click="if (isMobile()) sidebarOpen = false; open = !open"
                                class="flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium transition-colors hover:bg-gray-50 text-gray-700"
                                :class="activeKey === '{{ $item['key'] }}' ? 'bg-blue-50 text-blue-700 border border-blue-100' : ''">
                                    <span class="truncate">{{ $item['label'] }}</span>

                                    {{-- Icono de flecha solo si hay submenu --}}
                                    @if(isset($item['submenu']))
                                        <svg :class="{'rotate-90': open}" class="w-4 h-4 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    @endif
                                </a>

                                {{-- Submenu --}}
                                @if(isset($item['submenu']))
                                    <ul x-show="open" x-collapse class="pl-4 mt-1 space-y-1">
                                        @foreach($item['submenu'] as $sub)
                                            <li>
                                                <a href="{{ $sub['href'] }}"
                                                @click="if (isMobile()) sidebarOpen = false"
                                                class="block px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-gray-100 hover:text-gray-900"
                                                :class="activeKey === '{{ $sub['key'] }}' ? 'bg-blue-50 text-blue-700 border border-blue-100' : ''">
                                                    {{ $sub['label'] }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

            {{-- Zona libre para que cada módulo agregue accesos directos --}}
            @hasSection('sidebar_extra')
                <div class="pt-4 border-t border-gray-100">
                    @yield('sidebar_extra')
                </div>
            @endif
        </nav>

        {{-- Sidebar footer --}}
        <div class="p-4 border-t border-gray-200 bg-white">
            <div class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-50">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white font-bold text-xs">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900 truncate">{{ auth()->user()->name ?? 'Usuario' }}</p>
                    <p class="text-xs text-gray-600 truncate">Sesión activa</p>
                </div>
            </div>
        </div>
    </aside>

    {{-- MAIN --}}
    <div class="md:ml-72">
        {{-- Page container --}}
        <main class="px-4 sm:px-6 lg:px-8 py-6 pb-24">

            {{-- Flash messages --}}
            @if (session('success'))
                <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    {{ session('error') }}
                </div>
            @endif
            

            {{-- Encabezado de página (opcional) --}}
            @hasSection('page_header')
                <div class="mb-5">
                    @yield('page_header')
                </div>
            @else
                <div class="mb-5">
                    <h2 class="text-xl font-bold text-gray-900">@yield('h1', 'Panel')</h2>
                    <p class="text-sm text-gray-500">@yield('subtitle', 'Bienvenido al sistema')</p>
                </div>
            @endif

            {{-- Contenido de cada pantalla --}}
            @yield('content')
        </main>

        {{-- FOOTER --}}
        <footer class="border-t border-gray-200 bg-white">
            <div class="px-4 sm:px-6 lg:px-8 py-4 flex flex-col sm:flex-row items-center justify-between gap-2">
                <div class="text-xs text-gray-500">
                    © {{ date('Y') }} SGPD COAC — Todos los derechos reservados.
                </div>
                <div class="text-xs text-gray-500 flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded-full bg-gray-100 border border-gray-200">v1</span>
                    <a href="#" class="hover:text-gray-700">Soporte</a>
                    <span>•</span>
                    <a href="#" class="hover:text-gray-700">Políticas</a>
                </div>
            </div>
        </footer>
    </div>
</div>

{{-- Alpine.js (si no lo compilan por Vite) --}}
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<script>
    function sgpdLayout() {
    return {
        sidebarOpen: false,
        showNotifications: false,
        hoverKey: null,
        loadingNotifications: false,
        notificationCount: 0,

        activeKey: '{{ trim($__env->yieldContent('active_key')) ?: 'dashboard' }}',

        notifications: [], // Ahora vacío, se cargará dinámicamente

        toggleSidebar() {
            this.sidebarOpen = !this.sidebarOpen;
            this.showNotifications = false;
        },

        closeAll() {
            this.sidebarOpen = false;
            this.showNotifications = false;
        },

        isMobile() {
            return window.matchMedia('(max-width: 767px)').matches;
        },

        // Cargar notificaciones reales desde el dashboard
        async loadRealNotifications() {
            if (this.loadingNotifications) return;
            
            this.loadingNotifications = true;
            try {
                const response = await fetch('/api/dashboard/alerts');
                const alerts = await response.json();
                
                this.notifications = alerts.map(alert => ({
                    id: alert.id,
                    title: alert.title,
                    type: alert.type,
                    priority: alert.priority,
                    due_at: alert.due_at,
                    time: this.formatTimeAgo(alert.due_at)
                }));
                
                this.notificationCount = this.notifications.length;
                this.renderNotifications();
            } catch (error) {
                console.error('Error cargando notificaciones:', error);
                this.notifications = [];
                this.notificationCount = 0;
            } finally {
                this.loadingNotifications = false;
            }
        },

        // Formatear tiempo relativo
        formatTimeAgo(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diffMs = now - date;
            const diffMins = Math.floor(diffMs / 60000);
            const diffHours = Math.floor(diffMs / 3600000);
            const diffDays = Math.floor(diffMs / 86400000);

            if (diffMins < 1) return 'ahora mismo';
            if (diffMins < 60) return `hace ${diffMins} min`;
            if (diffHours < 24) return `hace ${diffHours} hora${diffHours > 1 ? 's' : ''}`;
            return `hace ${diffDays} día${diffDays > 1 ? 's' : ''}`;
        },

        // Renderizar notificaciones en el dropdown
        // Renderizar notificaciones en el dropdown
        renderNotifications() {
            const container = document.getElementById('realNotifications');
            if (!container) {
                console.error('No se encontró el contenedor de notificaciones');
                return;
            }

            // Limpiar contenido previo
            container.innerHTML = '';

            if (this.notifications.length === 0) {
                container.innerHTML = `
                    <div class="p-6 text-center text-sm text-gray-500">
                        <svg class="w-12 h-12 mx-auto text-green-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p>No hay notificaciones pendientes</p>
                        <p class="text-xs text-gray-400 mt-1">Las notificaciones se sincronizan con las alertas del dashboard</p>
                    </div>
                `;
                return;
            }

            let html = '';
            this.notifications.forEach(notif => {
                // Determinar color basado en prioridad
                let priorityColor, priorityText;
                if (notif.priority === 'high') {
                    priorityColor = 'red';
                    priorityText = 'Alta';
                } else if (notif.priority === 'medium') {
                    priorityColor = 'yellow';
                    priorityText = 'Media';
                } else {
                    priorityColor = 'blue';
                    priorityText = 'Baja';
                }
                
                html += `
                    <div class="p-4 hover:bg-gray-50 cursor-pointer border-l-4 border-${priorityColor}-500">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">${notif.title}</p>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-xs px-2 py-0.5 bg-${priorityColor}-100 text-${priorityColor}-800 rounded-full">
                                        ${notif.type}
                                    </span>
                                    <span class="text-xs px-2 py-0.5 bg-gray-100 text-gray-800 rounded-full">
                                        ${priorityText}
                                    </span>
                                    <span class="text-xs text-gray-500">${notif.time}</span>
                                </div>
                            </div>
                            <button class="text-gray-400 hover:text-gray-600 ml-2" 
                                    @click="removeNotification(${notif.id})"
                                    title="Eliminar notificación">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                `;
            });

            container.innerHTML = html;
        },

        // Marcar todas como leídas
        async markAllAsRead() {
            this.notifications = [];
            this.notificationCount = 0;
            this.renderNotifications();
            
            // Aquí podrías hacer una llamada API para marcar como leídas en el backend
            try {
                await fetch('/api/notifications/mark-read', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
            } catch (error) {
                console.error('Error marcando notificaciones:', error);
            }
        },

        // Eliminar una notificación
        removeNotification(id) {
            this.notifications = this.notifications.filter(n => n.id !== id);
            this.notificationCount = this.notifications.length;
            this.renderNotifications();
        }
    }
}
</script>
<script>
@if(session('success'))
    Swal.fire({
        toast: true,                     // Habilita modo "toast"
        position: 'bottom-start',        // Esquina inferior izquierda
        icon: 'success',                 // Icono: success, error, warning, info
        title: "{{ session('success') }}",
        showConfirmButton: false,        // Sin botón
        timer: 3000,                     // Duración en ms
        timerProgressBar: true
    });
@endif
    @if(session('error'))
        Swal.fire({
            toast: true,
            position: 'bottom-start',
            icon: 'error',
            title: "{{ session('error') }}",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    @endif
</script>
@stack('scripts')
</body>
</html>
