@extends('layouts.app')

@section('title', 'Gestión de Usuarios - SGPD COAC')
@section('active_key', 'users')

@section('content')
<div class="container-fluid px-0">
    <!-- Encabezado -->
    <div class="bg-white rounded-lg border border-gray-200 mb-6 p-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Gestión de Usuarios</h1>
                <p class="text-gray-600 mt-1">Administra los usuarios del sistema SGPD COAC</p>
            </div>
            <a href="{{ route('users.create') }}" 
               class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-4 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nuevo Usuario
            </a>
        </div>
    </div>

    <!-- Mensajes de sesión -->
    @if(session('message'))
        <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ session('message') }}
            </div>
        </div>
    @endif

    <!-- Tarjeta principal -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <!-- Encabezado tabla -->
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <h2 class="text-lg font-semibold text-gray-900">Listado de Usuarios</h2>
            </div>
        </div>

        <!-- Tabla -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="myTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Creado</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($users as $indice => $usuario)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $indice + 1 }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-10 h-10">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-bold text-sm">
                                        {{ strtoupper(substr($usuario->full_name, 0, 2)) }}
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="font-bold">{{ $usuario->full_name }}</div>
                                    <div class="text-black">{{ $usuario->email }}</div>
                                    @if($usuario->unit_id)
                                    <div class="text-black">Unidad ID: {{ $usuario->unit_id }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $estadoColor = [
                                    'activo' => 'bg-green-100 text-green-800 border-green-200',
                                    'suspendido' => 'bg-red-100 text-red-800 border-red-200'
                                ][$usuario->status] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                            @endphp
                            <span class="px-3 py-1 text-xs font-medium rounded-full border {{ $estadoColor }}">
                                {{ ucfirst($usuario->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $usuario->created_at->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center gap-2">
                                <!-- Botón Editar -->
                                <a href="{{ route('users.edit', $usuario->user_id) }}" 
                                   class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-900">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Editar
                                </a>

                                <!-- Separador -->
                                <span class="text-gray-300">|</span>

                                <!-- Botón Suspender/Activar -->
                                @if($usuario->status == 'activo')
                                <form action="{{ route('users.destroy', $usuario->user_id) }}" method="POST" class="inline form-suspender">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" 
                                            class="inline-flex items-center gap-1 text-amber-600 hover:text-amber-900 btn-suspender">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                        Suspender
                                    </button>
                                </form>
                                @else
                                <form action="{{ route('users.destroy', $usuario->user_id) }}" method="POST" class="inline form-activar">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" 
                                            class="inline-flex items-center gap-1 text-green-600 hover:text-green-900 btn-activar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Activar
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        
    </div>
</div>

<!-- JQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>

<!-- DataTables CSS y JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.min.css">
<script src="https://cdn.datatables.net/2.3.2/js/dataTables.min.js"></script>

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Inicializar DataTables
let table = new DataTable('#myTable', {
    language: {
        url: 'https://cdn.datatables.net/plug-ins/2.3.6/i18n/es-ES.json'
    },
    pageLength: 10,
    lengthMenu: [10, 25, 50, 100],
    search: {
        // Búsqueda normal (input general)
        smart: true
    },
    columnDefs: [
        {
            // Columna # (índice 0) - NO buscable
            targets: 0,
            searchable: false
        },
        {
            // Columna Usuario (índice 1) - SÍ buscable
            targets: 1,
            searchable: true
        },
        {
            // Columna Estado (índice 2) - NO buscable
            targets: 2,
            searchable: false
        },
        {
            // Columna Creado (índice 3) - NO buscable
            targets: 3,
            searchable: false
        },
        {
            // Columna Creado (índice 4) - NO buscable
            targets: 4,
            searchable: false
        }
    ]
}); 
</script>

<script>
// Delegación de eventos para manejar botones dinámicos de DataTables
document.addEventListener('click', function(event) {
    // Detectar clic en botón "Suspender"
    if (event.target.closest('.btn-suspender')) {
        event.preventDefault();
        const form = event.target.closest('form');
        
        Swal.fire({
            title: '¿Suspender usuario?',
            text: 'El usuario pasará a estado "suspendido" y no podrá acceder al sistema.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f59e0b',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Sí, suspender',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    }
    
    // Detectar clic en botón "Activar"
    if (event.target.closest('.btn-activar')) {
        event.preventDefault();
        const form = event.target.closest('form');
        
        Swal.fire({
            title: '¿Activar usuario?',
            text: 'El usuario volverá a estado "activo" y podrá acceder al sistema.',
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Sí, activar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    }
});
</script>


@endsection