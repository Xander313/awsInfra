@extends('layouts.app')

@section('title', 'Gestión de Roles - SGPD COAC')
@section('active_key', 'roles')

@section('content')
<div class="container-fluid px-0">
    <!-- Encabezado -->
    <div class="bg-white rounded-lg border border-gray-200 mb-6 p-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Gestión de Roles</h1>
                <p class="text-gray-600 mt-1">Administra los roles del sistema SGPD COAC</p>
            </div>
            <a href="{{ route('roles.create') }}" 
               class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-4 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nuevo Rol
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

    <!-- Agregar este código para mostrar errores -->
    @if(session('error'))
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
                {{ session('error') }}
            </div>
        </div>
    @endif

    <!-- Tarjeta principal -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <!-- Encabezado tabla -->
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <h2 class="text-lg font-semibold text-gray-900">Listado de Roles</h2>
            </div>
        </div>

        <!-- Tabla -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="myTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Permisos</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($roles as $indice => $rol)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $indice + 1 }}</td>
                        <td class="px-6 py-4">
                            <div class="font-bold">{{ $rol->name }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-gray-700 truncate max-w-md group relative cursor-help" 
                                title="{{ $rol->description ?? 'Sin descripción' }}">
                                {{ Str::limit($rol->description ?? 'Sin descripción', 80) }}
                                @if(strlen($rol->description ?? '') > 80)
                                    <span class="inline-block ml-1 text-blue-500 text-xs">...</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full border bg-blue-100 text-blue-800 border-blue-200 whitespace-nowrap">
                                {{ $rol->permissions->count() }} permiso(s)
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center gap-2">
                                <!-- Botón Detalles (Modal) -->
        <button type="button" 
                class="inline-flex items-center gap-1 text-gray-600 hover:text-gray-900 btn-detalles"
                data-id="{{ $rol->role_id }}"
                data-position="{{  $indice + 1  }}"
                data-name="{{ $rol->name }}"
                data-description="{{ $rol->description ?? 'Sin descripción' }}"
                data-permissions="{{ $rol->permissions->pluck('code')->implode(', ') }}"> 
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Detalles
        </button>

        <!-- Separador -->
        <span class="text-gray-300">|</span>

                                <!-- Botón Editar -->
                                <a href="{{ route('roles.edit', $rol->role_id) }}" 
                                   class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-900">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Editar
                                </a>

                                <!-- Separador -->
                                <span class="text-gray-300">|</span>

                                <!-- Botón Eliminar -->
                                <form action="{{ route('roles.destroy', $rol->role_id) }}" method="POST" class="inline form-eliminar">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" 
                                            class="inline-flex items-center gap-1 text-red-600 hover:text-red-900 btn-eliminar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Eliminar
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- Modal para Detalles del Rol -->
<div id="detallesModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-lg w-full max-h-[80vh] overflow-hidden">
        <!-- Encabezado del modal -->
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900" id="modalTitle">Detalles del Rol</h3>
            <button type="button" id="closeModal" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        
        <!-- Contenido del modal -->
        <div class="px-6 py-4 overflow-y-auto max-h-[calc(80vh-8rem)]">
            <!-- Nombre del Rol -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Rol</label>
                <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                    <p class="text-gray-900 font-medium" id="modalName"></p>
                </div>
            </div>
            
            
            <!-- Descripción -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="text-gray-700 whitespace-pre-line max-h-60 overflow-y-auto" id="modalDescription"></div>
                </div>
            </div>
            
            <!-- Permisos asignados -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Permisos asignados</label>
                <div class="p-3 bg-gray-50 rounded-lg border border-gray-200 max-h-40 overflow-y-auto">
                    <div id="modalPermissionsList" class="text-gray-700 text-sm space-y-1">
                        <!-- Los permisos se cargarán aquí con JavaScript -->
                    </div>
                    <div id="noPermissionsMessage" class="hidden text-gray-500 text-sm italic">
                        Este rol no tiene permisos asignados.
                    </div>
                </div>
            </div>

            <!-- Información adicional -->
            <div class="mt-6 pt-4 border-t border-gray-200">
                <h4 class="text-sm font-medium text-gray-900 mb-2">Información del sistema</h4>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class=" text-black">N° del Rol</p>
                        <p class="text-sm font-medium text-gray-900" id="modalPosition"></p>
                    </div>
                    <div>
                        <p class=" text-black">Total de Permisos</p>
                        <p class="text-sm font-medium text-gray-900" id="modalPermissionsCount"></p>
                    </div>
                </div>
            </div>
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
            smart: true
        },
        columnDefs: [
            {
                // Columna # (índice 0) - NO buscable
                targets: 0,
                searchable: false
            },
            {
                // Columna Nombre (índice 1) - SÍ buscable
                targets: 1,
                searchable: true
            },
            {
                // Columna Descripción (índice 2) - SÍ buscable
                targets: 2,
                searchable: true
            },
            {
                // Columna Permisos (índice 3) - NO buscable
                targets: 3,
                searchable: false
            },
            {
                // Columna Acciones (índice 4) - NO buscable
                targets: 4,
                searchable: false
            }
        ]
    });
</script>

<script>
// Delegación de eventos para manejar botones dinámicos de DataTables
document.addEventListener('click', function(event) {
    // 1. DETECTAR BOTÓN "ELIMINAR" (Roles)
    if (event.target.closest('.btn-eliminar')) {
        event.preventDefault();
        const form = event.target.closest('form');
        const roleName = event.target.closest('tr').querySelector('.font-bold').textContent;
        const permissionsCount = parseInt(event.target.closest('tr').querySelector('.bg-blue-100').textContent);
        // Verificar si tiene permisos asignados
        if (permissionsCount > 0) {
            Swal.fire({
                title: 'No se puede eliminar',
                html: `El rol <strong>"${roleName}"</strong> tiene ${permissionsCount} permiso(s) asignado(s).`,
                icon: 'error',
                confirmButtonColor: '#3b82f6',
                confirmButtonText: 'Entendido'
            });
            return; // No proceder con la eliminación
        }
        
        Swal.fire({
            title: '¿Eliminar rol?',
            html: `¿Estás seguro de eliminar el rol <strong>"${roleName}"</strong>?<br>Esta acción no se puede deshacer.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    }
    
    // 2. DETECTAR BOTÓN "DETALLES" (Roles - Modal)
    if (event.target.closest('.btn-detalles')) {
        event.preventDefault();
        
        const button = event.target.closest('.btn-detalles');
        const roleId = button.getAttribute('data-id');
        const rolePosition = button.getAttribute('data-position');
        const roleName = button.getAttribute('data-name');
        const roleDescription = button.getAttribute('data-description');
        const permissionsString = button.getAttribute('data-permissions');
        
        // Encontrar la fila para obtener el número de permisos
        const row = button.closest('tr');
        const permissionsCount = row.querySelector('.bg-blue-100').textContent.trim();
        
        // Llenar el modal (tu código actual de detalles)
        document.getElementById('modalTitle').textContent = `Detalles: ${roleName}`;
        document.getElementById('modalName').textContent = roleName;
        document.getElementById('modalDescription').textContent = roleDescription;
        document.getElementById('modalPosition').textContent = rolePosition;
        document.getElementById('modalPermissionsCount').textContent = permissionsCount;
        
        // Mostrar/Ocultar lista de permisos
        const permissionsList = document.getElementById('modalPermissionsList');
        const noPermissionsMsg = document.getElementById('noPermissionsMessage');
        
        if (permissionsString && permissionsString.trim() !== '') {
            const permissionsArray = permissionsString.split(', ');
            let html = '';
            
            permissionsArray.forEach(permission => {
                html += `<div class="flex items-start">
                    <svg class="w-4 h-4 text-green-500 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span class="text-gray-700">${permission}</span>
                </div>`;
            });
            
            permissionsList.innerHTML = html;
            permissionsList.classList.remove('hidden');
            noPermissionsMsg.classList.add('hidden');
        } else {
            permissionsList.innerHTML = '';
            permissionsList.classList.add('hidden');
            noPermissionsMsg.classList.remove('hidden');
        }
        
        // Mostrar modal
        document.getElementById('detallesModal').classList.remove('hidden');
        document.getElementById('detallesModal').classList.add('flex');
    }
});
</script>

<script>
// Cerrar modal
function closeModal() {
    document.getElementById('detallesModal').classList.remove('flex');
    document.getElementById('detallesModal').classList.add('hidden');
}

// Asignar eventos para cerrar
document.getElementById('closeModal').addEventListener('click', closeModal);

// Cerrar con tecla ESC
document.addEventListener('keydown', function(event) {
    const modal = document.getElementById('detallesModal');
    const isModalVisible = !modal.classList.contains('hidden');
    
    if (isModalVisible && event.key === 'Escape') {
        closeModal();
    }
});
</script>

@endsection