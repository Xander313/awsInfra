@extends('layouts.app')

@section('title', 'Editar Rol - SGPD COAC')
@section('active_key', 'roles')

@section('content')
<div class="container-fluid px-0">
    <!-- Encabezado -->
    <div class="bg-white rounded-lg border border-gray-200 mb-6 p-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Editar Rol</h1>
                <p class="text-gray-600 mt-1">Modificar los datos del rol: {{ $role->name }}</p>
            </div>
            <a href="{{ route('roles.index') }}" 
               class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 font-medium py-2.5 px-4 rounded-lg transition-colors border border-gray-300 hover:border-gray-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver al listado
            </a>
        </div>
    </div>

    <!-- Formulario -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <form action="{{ route('roles.update', $role->role_id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="p-6 space-y-6">
                <!-- Campo Nombre -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nombre del Rol <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name', $role->name) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                           placeholder="Ej: Administrador, Auditor, Usuario"
                           >
                    @error('name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Campo Descripción -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Descripción
                    </label>
                    <textarea 
                        id="description" 
                        name="description" 
                        rows="5"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                        placeholder="Describe las funciones y responsabilidades de este rol...">{{ old('description', $role->description) }}</textarea>
                    @error('description')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Información del rol (solo lectura) -->
                <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-gray-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 w-full">
                            <div>
                                <h4 class="text-sm font-medium text-gray-900">Información del sistema</h4>
                                <ul class="mt-2 text-sm text-gray-700 space-y-1">
                                    <li><strong>N° de Rol:</strong> {{ $position }}</li>
                                    <li><strong>Permisos asignados:</strong> 
                                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                            {{ $role->permissions->count() }} permiso(s)
                                        </span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                            <!-- Sección Permisos -->
            <div class="px-6 pb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">
                    Permisos asignados
                </label>
                
                <!-- Contenedor de checkboxes con scroll -->
                <div class="border border-gray-300 rounded-lg p-4 max-h-72 overflow-y-auto bg-gray-50">
                    <!-- Botones de selección rápida -->
                    <div class="flex gap-2 mb-3 pb-3 border-b border-gray-200">
                        <button type="button" 
                                onclick="selectAllPermissions()"
                                class=" px-3 py-1 bg-blue-100 text-blue-700 hover:bg-blue-200 rounded transition-colors">
                            Seleccionar todos
                        </button>
                        <button type="button" 
                                onclick="deselectAllPermissions()"
                                class=" px-3 py-1 bg-gray-100 text-gray-700 hover:bg-gray-200 rounded transition-colors">
                            Quitar selección
                        </button>
                    </div>
                    
                    <!-- Lista de permisos -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @forelse($permissions as $perm)
                            <div class="flex items-start p-2 hover:bg-white rounded transition-colors">
                                <input type="checkbox" 
                                    id="perm_{{ $perm->perm_id }}" 
                                    name="permissions[]" 
                                    value="{{ $perm->perm_id }}"
                                    class="mt-1 h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                    @if(is_array(old('permissions')) && in_array($perm->perm_id, old('permissions'))) 
                                        checked 
                                    @elseif(!old('permissions') && $role->permissions->contains('perm_id', $perm->perm_id))
                                        checked
                                    @endif>
                                <label for="perm_{{ $perm->perm_id }}" class="ml-3 text-sm text-gray-700 flex-1">
                                    <div class="font-medium text-gray-900">{{ $perm->code }}</div>
                                    @if($perm->description)
                                        <div class="text-gray-500 text-xs mt-1">{{ Str::limit($perm->description, 70) }}</div>
                                    @endif
                                </label>
                            </div>
                        @empty
                            <div class="col-span-2 text-center py-4 text-gray-500">
                                No hay permisos registrados en el sistema.
                            </div>
                        @endforelse
                    </div>
                </div>
                
                @error('permissions')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
                
                <!-- Contador de permisos seleccionados -->
                <div class="mt-2 flex justify-between items-center">
                    <p class="text-sm text-gray-500">
                        Permisos seleccionados: <span id="selectedCount" class="font-medium">0</span>
                    </p>
                    <p class="text-xs text-gray-400">
                        Total: {{ $permissions->count() }} permisos
                    </p>
                </div>
            </div>
            </div>

            <!-- Botones de acción -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex flex-col sm:flex-row gap-3">
                <button type="submit" 
                        class="inline-flex justify-center items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-lg transition-colors flex-1 sm:flex-none">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Actualizar Rol
                </button>
                
                <a href="{{ route('roles.index') }}" 
                   class="inline-flex justify-center items-center gap-2 bg-white hover:bg-gray-50 text-gray-700 font-medium py-3 px-6 rounded-lg transition-colors border border-gray-300 flex-1 sm:flex-none">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<!-- JQuery Validation -->
<script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.js"></script>

<script>
    $(document).ready(function() {
        $("form").validate({
            rules: {
                name: {
                    required: true,
                    minlength: 3,
                    maxlength: 100
                },
                description: {
                    required: true,
                    minlength: 3,
                    maxlength: 500
                }
            },
            messages: {
                name: {
                    required: "Por favor ingrese el nombre del rol",
                    minlength: "El nombre debe tener al menos 1 caracteres",
                    maxlength: "El nombre debe tener máximo 100 caracteres"
                },
                description: {
                    required: "Por favor ingrese la actividad que realiza el rol",
                    minlength: "La descripción debe tener al menos 1 palabra",
                    maxlength: "La descripción no puede exceder los 500 caracteres"
                }
            },
        });
    });
</script>
<style>
    .error{
        color: red;
    }
</style>
<script>
// Funciones para selección rápida
function selectAllPermissions() {
    document.querySelectorAll('input[name="permissions[]"]').forEach(checkbox => {
        checkbox.checked = true;
    });
    updateSelectedCount();
}

function deselectAllPermissions() {
    document.querySelectorAll('input[name="permissions[]"]').forEach(checkbox => {
        checkbox.checked = false;
    });
    updateSelectedCount();
}

// Actualizar contador
function updateSelectedCount() {
    const checked = document.querySelectorAll('input[name="permissions[]"]:checked').length;
    document.getElementById('selectedCount').textContent = checked;
}

// Inicializar contador y eventos
document.addEventListener('DOMContentLoaded', function() {
    // Contador inicial
    updateSelectedCount();
    
    // Actualizar contador al cambiar checkboxes
    document.querySelectorAll('input[name="permissions[]"]').forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedCount);
    });
});
</script>
<!-- SweetAlert2 CDN (ya incluido en layout, pero por si acaso) -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection