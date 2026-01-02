{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'SGPD COAC - Sistema de Gestión de Protección de Datos')</title>

    {{-- Librerías (mantengo las que ya usas) --}}
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Bootstrap CSS + Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    {{-- Tipografía global --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- Tailwind por CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Opción B (pro): Vite (descomenta cuando tengan Tailwind compilado) --}}
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
    // Nota: si una ruta aún no existe en el proyecto, Laravel lanzará excepción.
    // Si prefieres "no romper" mientras desarrollan módulos, dime y te lo dejo con Route::has().
    $nav = [
        [
            'key' => 'dashboard',
            'label' => 'Dashboard',
            'items' => [
                ['label' => 'Inicio', 'href' => route('dashboard'), 'key' => 'dashboard'],
            ],
        ],
        [
            'key' => 'core',
            'label' => 'CORE',
            'items' => [
                ['label' => 'Organizaciones', 'href' => route('orgs.index'), 'key' => 'org'],
            ],
        ],
        [
            'key' => 'iam',
            'label' => 'IAM',
            'items' => [
                ['label' => 'Usuarios', 'href' => route('users.index'), 'key' => 'users'],
                ['label' => 'Roles', 'href' => route('roles.index'), 'key' => 'roles'],
                ['label' => 'Permisos', 'href' => route('permissions.index'), 'key' => 'permissions'],
            ],
        ],
        [
            'key' => 'privacy',
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
            'key' => 'risk_audit',
            'label' => 'RISK & AUDIT',
            'items' => [
                ['label' => 'Riesgos', 'href' => url('/risk/ui/risks'), 'key' => 'risks'],
                ['label' => 'DPIA', 'href' => url('/risk/ui/dpias'), 'key' => 'dpia'],
                [
                    'label' => 'Auditoría',
                    'href' => '#',
                    'key' => 'audits',
                    'submenu' => [
                        ['label' => 'Auditorías', 'href' => route('audits.index'), 'key' => 'audits'],
                        ['label' => 'Controles', 'href' => route('controls.index'), 'key' => 'controls'],
                        ['label' => 'Hallazgos', 'href' => route('findings.index'), 'key' => 'findings'],
                        ['label' => 'Acciones Correctivas', 'href' => route('corrective_actions.index'), 'key' => 'corrective_actions'],
                    ],
                ],
            ],
        ],
        [
            'key' => 'training',
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
                        class="md:hidden p-2 hover:bg-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
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
                        @click="showNotifications = !showNotifications; if(showNotifications){ loadRealNotifications() }"
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

                        {{-- Contenedor donde se muestran las notificaciones reales --}}
                        <div id="realNotifications" class="divide-y divide-gray-100 max-h-80 overflow-y-auto">
                            <div x-show="loadingNotifications" class="p-6 text-center">
                                <div class="inline-block animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
                                <p class="text-sm text-gray-500 mt-2">Cargando notificaciones...</p>
                            </div>
                        </div>

                        <div class="p-3 border-t border-gray-100 bg-gray-50">
                            <button type="button" @click="markAllAsRead()" class="text-xs text-blue-600 hover:text-blue-800">
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
    <div x-show="sidebarOpen && isMobile()" x-transition.opacity class="fixed inset-0 z-30 bg-black/50 md:hidden" @click="sidebarOpen = false"></div>

    {{-- SIDEBAR --}}
    <aside
        class="fixed z-40 left-0 top-16 w-72 bg-white border-r border-gray-200 shadow-xl md:shadow-none flex flex-col h-[calc(100vh-64px)]"
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

        {{-- Sidebar nav (secciones colapsables) --}}
        <nav class="p-3 overflow-y-auto flex-1">
            <div class="space-y-2">
                @foreach($nav as $section)
                    <div class="rounded-xl border border-gray-200 bg-white overflow-hidden">
                        {{-- Encabezado sección (dropdown) --}}
                        <button type="button"
                                @click="toggleSection('{{ $section['key'] }}')"
                                class="w-full flex items-center justify-between px-3 py-2 text-left hover:bg-gray-50">
                            <span class="text-[11px] uppercase tracking-wider text-gray-500 font-semibold">
                                {{ $section['label'] }}
                            </span>

                            <svg class="w-4 h-4 text-gray-400 transition-transform"
                                 :class="isSectionOpen('{{ $section['key'] }}') ? 'rotate-90' : ''"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>

                        {{-- Items --}}
                        <div x-show="isSectionOpen('{{ $section['key'] }}')" x-transition class="px-2 pb-2">
                            <div class="space-y-1">
                                @foreach($section['items'] as $item)
                                    {{-- Item con submenu --}}
                                    @if(isset($item['submenu']))
                                        <div class="rounded-lg border border-gray-200 bg-gray-50/60 overflow-hidden">
                                            <button type="button"
                                                    @click="toggleSubmenu('{{ $item['key'] }}')"
                                                    class="w-full flex items-center justify-between px-3 py-2 text-sm font-semibold text-gray-800 hover:bg-gray-50">
                                                <span class="truncate">{{ $item['label'] }}</span>
                                                <svg class="w-4 h-4 text-gray-400 transition-transform"
                                                     :class="isSubmenuOpen('{{ $item['key'] }}') ? 'rotate-90' : ''"
                                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                </svg>
                                            </button>

                                            <div x-show="isSubmenuOpen('{{ $item['key'] }}')" x-transition class="px-2 pb-2">
                                                <div class="space-y-1">
                                                    @foreach($item['submenu'] as $sub)
                                                        <a href="{{ $sub['href'] }}"
                                                           @click="if (isMobile()) sidebarOpen = false"
                                                           class="block px-3 py-2 rounded-lg text-sm text-gray-700 hover:bg-gray-100"
                                                           :class="activeKey === '{{ $sub['key'] }}' ? 'bg-blue-50 text-blue-700 border border-blue-100' : ''">
                                                            {{ $sub['label'] }}
                                                        </a>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        {{-- Item normal --}}
                                        <a href="{{ $item['href'] }}"
                                           @click="if (isMobile()) sidebarOpen = false"
                                           class="flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium transition-colors hover:bg-gray-50 text-gray-700"
                                           :class="activeKey === '{{ $item['key'] }}' ? 'bg-blue-50 text-blue-700 border border-blue-100' : ''">
                                            <span class="truncate">{{ $item['label'] }}</span>
                                            <svg x-show="activeKey === '{{ $item['key'] }}'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach

                {{-- Zona libre para que cada módulo agregue accesos directos --}}
                @hasSection('sidebar_extra')
                    <div class="pt-3">
                        @yield('sidebar_extra')
                    </div>
                @endif
            </div>
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
        <main class="px-4 sm:px-6 lg:px-8 py-6 pb-24">

            {{-- Flash messages (bloques clásicos) --}}
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

    {{-- Safelist mínimo para Tailwind Play CDN (clases usadas en strings JS) --}}
    <div class="hidden border-red-500 border-yellow-500 border-blue-500 bg-red-100 bg-yellow-100 bg-blue-100 text-red-800 text-yellow-800 text-blue-800"></div>
</div>

{{-- Alpine.js + (opcional) collapse plugin --}}
<script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<script>
    function sgpdLayout() {
        return {
            sidebarOpen: false,
            showNotifications: false,
            hoverKey: null,

            loadingNotifications: false,
            notificationCount: 0,
            notifications: [],

            // Sección activa de menú (cada vista setea: @section('active_key','rat'))
            activeKey: '{{ trim($__env->yieldContent('active_key')) ?: 'dashboard' }}',

            // Secciones / submenus abiertos
            openSections: {},
            openSubmenus: {},

            init() {
                // Abrir por defecto la sección que contenga el activeKey
                const sectionKey = this.findSectionForActiveKey(this.activeKey);
                if (sectionKey) this.openSections[sectionKey] = true;

                // Si activeKey pertenece a un submenu (ej. controls), abrir submenu padre
                const submenuKey = this.findSubmenuForActiveKey(this.activeKey);
                if (submenuKey) this.openSubmenus[submenuKey] = true;

                // Puedes cargar contador inicial si quieres (opcional):
                // this.loadRealNotifications();
            },

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

            // ------- Sidebar: secciones colapsables -------
            toggleSection(key) {
                this.openSections[key] = !this.openSections[key];
            },

            isSectionOpen(key) {
                return !!this.openSections[key];
            },

            // ------- Sidebar: submenus (Auditoría) -------
            toggleSubmenu(key) {
                this.openSubmenus[key] = !this.openSubmenus[key];
            },

            isSubmenuOpen(key) {
                return !!this.openSubmenus[key];
            },

            findSectionForActiveKey(activeKey) {
                const nav = @json($nav);
                for (const section of nav) {
                    for (const item of (section.items || [])) {
                        if (item.key === activeKey) return section.key;
                        if (item.submenu) {
                            for (const sub of item.submenu) {
                                if (sub.key === activeKey) return section.key;
                            }
                        }
                    }
                }
                return null;
            },

            findSubmenuForActiveKey(activeKey) {
                const nav = @json($nav);
                for (const section of nav) {
                    for (const item of (section.items || [])) {
                        if (item.submenu) {
                            for (const sub of item.submenu) {
                                if (sub.key === activeKey) return item.key; // key del submenu padre
                            }
                        }
                    }
                }
                return null;
            },

            // ------- Notificaciones -------
            async loadRealNotifications() {
                if (this.loadingNotifications) return;

                this.loadingNotifications = true;
                try {
                    const response = await fetch('/api/dashboard/alerts', { headers: { 'Accept': 'application/json' } });
                    const alerts = await response.json();

                    this.notifications = (alerts || []).map(alert => ({
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
                    this.renderNotifications();
                } finally {
                    this.loadingNotifications = false;
                }
            },

            formatTimeAgo(dateString) {
                if (!dateString) return '';
                const date = new Date(dateString);
                if (isNaN(date.getTime())) return '';
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

            renderNotifications() {
                const container = document.getElementById('realNotifications');
                if (!container) return;

                container.innerHTML = '';

                if (this.loadingNotifications) {
                    container.innerHTML = `
                        <div class="p-6 text-center">
                            <div class="inline-block animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
                            <p class="text-sm text-gray-500 mt-2">Cargando notificaciones...</p>
                        </div>
                    `;
                    return;
                }

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
                    let priorityColor = 'blue';
                    let priorityText = 'Baja';

                    if (notif.priority === 'high') { priorityColor = 'red'; priorityText = 'Alta'; }
                    else if (notif.priority === 'medium') { priorityColor = 'yellow'; priorityText = 'Media'; }

                    html += `
                        <div class="p-4 hover:bg-gray-50 cursor-pointer border-l-4 border-${priorityColor}-500">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">${notif.title ?? ''}</p>
                                    <div class="flex items-center gap-2 mt-1 flex-wrap">
                                        <span class="text-xs px-2 py-0.5 bg-${priorityColor}-100 text-${priorityColor}-800 rounded-full">
                                            ${notif.type ?? ''}
                                        </span>
                                        <span class="text-xs px-2 py-0.5 bg-gray-100 text-gray-800 rounded-full">
                                            ${priorityText}
                                        </span>
                                        <span class="text-xs text-gray-500">${notif.time ?? ''}</span>
                                    </div>
                                </div>
                                <button type="button" class="text-gray-400 hover:text-gray-600 ml-2" data-notif-id="${notif.id}" title="Eliminar notificación">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    `;
                });

                container.innerHTML = html;

                // Bind de botones eliminar (porque el HTML se inyecta)
                container.querySelectorAll('button[data-notif-id]').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        const id = btn.getAttribute('data-notif-id');
                        this.removeNotification(Number(id));
                    });
                });
            },

            async markAllAsRead() {
                this.notifications = [];
                this.notificationCount = 0;
                this.renderNotifications();

                // Opcional: backend
                try {
                    await fetch('/api/notifications/mark-read', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        }
                    });
                } catch (error) {
                    console.error('Error marcando notificaciones:', error);
                }
            },

            removeNotification(id) {
                this.notifications = this.notifications.filter(n => n.id !== id);
                this.notificationCount = this.notifications.length;
                this.renderNotifications();
            }
        }
    }
</script>

{{-- Toasts (SweetAlert) --}}
<script>
@if(session('success'))
    Swal.fire({
        toast: true,
        position: 'bottom-start',
        icon: 'success',
        title: @json(session('success')),
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });
@endif

@if(session('error'))
    Swal.fire({
        toast: true,
        position: 'bottom-start',
        icon: 'error',
        title: @json(session('error')),
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });
@endif
</script>

@stack('scripts')
</body>
</html>
