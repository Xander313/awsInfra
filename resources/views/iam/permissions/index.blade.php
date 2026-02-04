@extends('layouts.app')

@section('title', 'Gestión de Permisos - SGPD COAC')
@section('active_key', 'permissions')

@section('content')
<div class="container-fluid px-0">
    <div class="bg-white rounded-lg border border-gray-200 mb-6 p-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Gestión de Permisos</h1>
                <p class="text-gray-600 mt-1">Administra los permisos del sistema SGPD COAC</p>
            </div>
            <a href="{{ route('permissions.create') }}" 
               class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-4 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nuevo Permiso
            </a>
        </div>
    </div>

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

    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <h2 class="text-lg font-semibold text-gray-900">Listado de Permisos</h2>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="myTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Roles Asignados</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($permissions as $indice => $permiso)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $indice + 1 }}</td>
                        <td class="px-6 py-4">
                            <div class="font-bold">{{ $permiso->code }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-gray-700 truncate max-w-md group relative cursor-help" 
                                title="{{ $permiso->description ?? 'Sin descripción' }}">
                                {{ Str::limit($permiso->description ?? 'Sin descripción', 80) }}
                                @if(strlen($permiso->description ?? '') > 80)
                                    <span class="inline-block ml-1 text-blue-500 text-xs">...</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $estadoColor = [
                                    'activo' => 'bg-green-100 text-green-800 border-green-200',
                                    'inactivo' => 'bg-red-100 text-red-800 border-red-200'
                                ][$permiso->status] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                            @endphp
                            <span class="px-3 py-1 text-xs font-medium rounded-full border {{ $estadoColor }}">
                                {{ ucfirst($permiso->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full border bg-blue-100 text-blue-800 border-blue-200 whitespace-nowrap">
                                {{ $permiso->roles->count() }} rol(es)
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center gap-2">
                                <button type="button" 
                                        class="inline-flex items-center gap-1 text-gray-600 hover:text-gray-900 btn-detalles"
                                        data-id="{{ $permiso->perm_id }}"
                                        data-position="{{ $indice + 1 }}"
                                        data-code="{{ $permiso->code }}"
                                        data-description="{{ $permiso->description ?? 'Sin descripción' }}"
                                        data-roles="{{ $permiso->roles->pluck('name')->implode(', ') }}"> 
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Detalles
                                </button>

                                <span class="text-gray-300">|</span>

                                <a href="{{ route('permissions.edit', $permiso->perm_id) }}" 
                                   class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-900">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Editar
                                </a>

                                <span class="text-gray-300">|</span>

                                @if($permiso->status == 'activo')
                                <form action="{{ route('permissions.destroy', $permiso->perm_id) }}" method="POST" class="inline form-inactivar">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" 
                                            class="inline-flex items-center gap-1 text-amber-600 hover:text-amber-900 btn-inactivar"
                                            data-code="{{ $permiso->code }}"
                                            data-roles-count="{{ $permiso->roles->count() }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                        Inactivar
                                    </button>
                                </form>
                                @else
                                <form action="{{ route('permissions.destroy', $permiso->perm_id) }}" method="POST" class="inline form-activar">
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

<div id="detallesModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-lg w-full max-h-[80vh] overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900" id="modalTitle">Detalles del Permiso</h3>
            <button type="button" id="closeModal" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        
        <div class="px-6 py-4 overflow-y-auto max-h-[calc(80vh-8rem)]">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Código del Permiso</label>
                <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                    <p class="text-gray-900 font-medium" id="modalCode"></p>
                </div>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="text-gray-700 whitespace-pre-line max-h-60 overflow-y-auto" id="modalDescription"></div>
                </div>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Roles asignados</label>
                <div class="p-3 bg-gray-50 rounded-lg border border-gray-200 max-h-40 overflow-y-auto">
                    <div id="modalRolesList" class="text-gray-700 text-sm space-y-1"></div>
                    <div id="noRolesMessage" class="hidden text-gray-500 text-sm italic">
                        Este permiso no está asignado a ningún rol.
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-gray-200">
                <h4 class="text-sm font-medium text-gray-900 mb-2">Información del sistema</h4>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500">N° del Permiso</p>
                        <p class="text-sm font-medium text-gray-900" id="modalPosition"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Roles Asignados</p>
                        <p class="text-sm font-medium text-gray-900" id="modalRolesCount"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.min.css">
<script src="https://cdn.datatables.net/2.3.2/js/dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    let table = new DataTable('#myTable', {
        language: { url: 'https://cdn.datatables.net/plug-ins/2.3.6/i18n/es-ES.json' },
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        search: { smart: true },
        columnDefs: [
            { targets: 0, searchable: false },
            { targets: 3, searchable: false },
            { targets: 4, searchable: false },
            { targets: 5, searchable: false }
        ]
    });
</script>

<script>
document.addEventListener('click', function(event) {
    // 1. INACTIVAR
    if (event.target.closest('.btn-inactivar')) {
        event.preventDefault();
        const button = event.target.closest('.btn-inactivar');
        const form = button.closest('form');
        const permCode = button.getAttribute('data-code');
        const rolesCount = parseInt(button.getAttribute('data-roles-count'));

        if (rolesCount > 0) {
            Swal.fire({
                title: 'No se puede inactivar',
                html: `El permiso <strong>"${permCode}"</strong> está asignado a ${rolesCount} rol(es).`,
                icon: 'error',
                confirmButtonColor: '#3b82f6',
                confirmButtonText: 'Entendido'
            });
            return;
        }
        
        Swal.fire({
            title: '¿Inactivar permiso?',
            html: `El permiso <strong>"${permCode}"</strong> pasará a estado inactivo.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f59e0b',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Sí, inactivar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    }
    
    // 2. ACTIVAR
    if (event.target.closest('.btn-activar')) {
        event.preventDefault();
        const form = event.target.closest('form');
        
        Swal.fire({
            title: '¿Activar permiso?',
            text: 'El permiso volverá a estar disponible.',
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

    // 3. DETALLES
    if (event.target.closest('.btn-detalles')) {
        event.preventDefault();
        const button = event.target.closest('.btn-detalles');
        const permCode = button.getAttribute('data-code');
        const permDesc = button.getAttribute('data-description');
        const rolesStr = button.getAttribute('data-roles');
        const pos = button.getAttribute('data-position');
        const row = button.closest('tr');
        const count = row.querySelector('.bg-blue-100').textContent.trim();
        
        document.getElementById('modalTitle').textContent = `Detalles: ${permCode}`;
        document.getElementById('modalCode').textContent = permCode;
        document.getElementById('modalDescription').textContent = permDesc;
        document.getElementById('modalPosition').textContent = pos;
        document.getElementById('modalRolesCount').textContent = count;
        
        const list = document.getElementById('modalRolesList');
        const msg = document.getElementById('noRolesMessage');
        
        if (rolesStr && rolesStr.trim() !== '') {
            list.innerHTML = rolesStr.split(', ').map(r => `
                <div class="flex items-start">
                    <svg class="w-4 h-4 text-green-500 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span class="text-gray-700">${r}</span>
                </div>
            `).join('');
            list.classList.remove('hidden');
            msg.classList.add('hidden');
        } else {
            list.innerHTML = '';
            list.classList.add('hidden');
            msg.classList.remove('hidden');
        }
        
        document.getElementById('detallesModal').classList.remove('hidden');
        document.getElementById('detallesModal').classList.add('flex');
    }
});

function closeModal() {
    document.getElementById('detallesModal').classList.remove('flex');
    document.getElementById('detallesModal').classList.add('hidden');
}

document.getElementById('closeModal').addEventListener('click', closeModal);
document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeModal(); });
</script>
@endsection