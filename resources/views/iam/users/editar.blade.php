@extends('layouts.app')

@section('title', 'Editar Usuario - SGPD COAC')
@section('active_key', 'users')

@section('content')
<div class="container-fluid px-0">
    <div class="bg-white rounded-lg border border-gray-200 mb-6 p-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Editar Usuario</h1>
                <p class="text-gray-600 mt-1">Modificar los datos del usuario: {{ $user->full_name }}</p>
            </div>
            <a href="{{ route('users.index') }}" 
               class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 font-medium py-2.5 px-4 rounded-lg transition-colors border border-gray-300 hover:border-gray-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver al listado
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <form action="{{ route('users.update', $user->user_id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="p-6 space-y-6">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="{{ old('email', $user->email) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                           placeholder="ejemplo@dominio.com"
                           >
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="full_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nombre Completo <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="full_name" 
                           name="full_name" 
                           value="{{ old('full_name', $user->full_name) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                           placeholder="Juan Pérez González"
                           >
                    @error('full_name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        Estado <span class="text-red-500">*</span>
                    </label>
                    <select id="status" 
                            name="status" 
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                            required>
                        <option value="activo" {{ old('status', $user->status) == 'activo' ? 'selected' : '' }} class="text-green-700">
                            Activo
                        </option>
                        <option value="suspendido" {{ old('status', $user->status) == 'suspendido' ? 'selected' : '' }} class="text-red-700">
                            Suspendido
                        </option>
                    </select>
                    @error('status')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    
                    <div class="mt-3 space-y-2">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-green-500"></div>
                            <span class="text-sm text-gray-600"><strong>Activo:</strong> Usuario puede acceder al sistema</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-red-500"></div>
                            <span class="text-sm text-gray-600"><strong>Suspendido:</strong> Usuario bloqueado temporalmente</span>
                        </div>
                    </div>
                </div>

                <div>
                    <label for="unit_id" class="block text-sm font-medium text-gray-700 mb-2">
                        ID de Unidad
                    </label>
                    <input type="number" 
                           id="unit_id" 
                           name="unit_id" 
                           value="{{ old('unit_id', $user->unit_id) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                           placeholder="Ej: 123"
                           min="1">
                    @error('unit_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-sm text-gray-500">
                        Identificador de la unidad/organización a la que pertenece el usuario.
                    </p>
                </div>

                <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-gray-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 w-full">
                            <div>
                                <h4 class="text-sm font-medium text-gray-900">Información del sistema</h4>
                                <ul class="mt-2 text-sm text-gray-700 space-y-1">
                                    <li><strong>User ID:</strong> {{ $user->user_id }}</li>
                                    <li><strong>Creado:</strong> {{ $user->created_at->format('d/m/Y H:i') }}</li>
                                    <li><strong>Último login:</strong> 
                                        @if($user->last_login_at)
                                            {{ $user->last_login_at->format('d/m/Y H:i') }}
                                        @else
                                            <span class="text-gray-500">Nunca</span>
                                        @endif
                                    </li>
                                </ul>
                            </div>
                            <div class="flex items-center">
                                <div class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-bold text-xl">
                                    {{ strtoupper(substr($user->full_name, 0, 2)) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-6 pb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">
                    Asignación de Roles
                </label>
                
                <div class="border border-gray-300 rounded-lg p-4 max-h-72 overflow-y-auto bg-gray-50">
                    <div class="flex gap-2 mb-3 pb-3 border-b border-gray-200">
                        <button type="button" 
                                onclick="selectAllRoles()" 
                                class="px-3 py-1 bg-blue-100 text-blue-700 hover:bg-blue-200 rounded text-sm transition-colors">
                            Seleccionar todos
                        </button>
                        <button type="button" 
                                onclick="deselectAllRoles()" 
                                class="px-3 py-1 bg-gray-100 text-gray-700 hover:bg-gray-200 rounded text-sm transition-colors">
                            Quitar selección
                        </button>
                    </div>

                    @if($roles->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach($roles as $role)
                                @php
                                    // Verificar si el usuario tiene este rol (desde BD o desde old input si falló validación)
                                    $isChecked = $user->roles->contains('role_id', $role->role_id);
                                    if(old('roles')) {
                                        $isChecked = in_array($role->role_id, old('roles'));
                                    }
                                @endphp
                            
                            <div class="flex items-start p-2 hover:bg-white rounded transition-colors">
                                <input type="checkbox" 
                                    id="role_{{ $role->role_id }}"
                                    name="roles[]" 
                                    value="{{ $role->role_id }}"
                                    class="mt-1 h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                    {{ $isChecked ? 'checked' : '' }}>
                                <label for="role_{{ $role->role_id }}" class="ml-3 text-sm text-gray-700 flex-1 cursor-pointer">
                                    <div class="font-medium text-gray-900">{{ $role->name }}</div>
                                    @if($role->description)
                                        <div class="text-gray-500 text-xs mt-1">{{ Str::limit($role->description, 50) }}</div>
                                    @endif
                                </label>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4 text-sm text-gray-500 italic">No hay roles disponibles.</div>
                    @endif
                </div>
                
                @error('roles')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror

                <div class="mt-2 flex justify-between items-center">
                    <p class="text-sm text-gray-500">
                        Roles seleccionados: <span id="selectedCount" class="font-medium">0</span>
                    </p>
                    <p class="text-xs text-gray-400">
                        Total: {{ $roles->count() }} roles
                    </p>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex flex-col sm:flex-row gap-3">
                <button type="submit" 
                        class="inline-flex justify-center items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-lg transition-colors flex-1 sm:flex-none">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Actualizar Usuario
                </button>
                
                <a href="{{ route('users.index') }}" 
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const statusSelect = document.getElementById('status');
        
        // Colorear el select según estado seleccionado
        function updateStatusColor() {
            const value = statusSelect.value;
            const colors = {
                'activo': 'border-green-300 bg-green-50 text-green-800',
                'suspendido': 'border-red-300 bg-red-50 text-red-800'
            };
            
            // Remover todas las clases de color
            statusSelect.classList.remove(
                'border-green-300', 'bg-green-50', 'text-green-800',
                'border-gray-300', 'bg-gray-50', 'text-gray-800',
                'border-red-300', 'bg-red-50', 'text-red-800'
            );
            
            // Agregar clases según valor
            const [border, bg, text] = colors[value].split(' ');
            statusSelect.classList.add(border, bg, text);
        }
        
        // Inicializar color
        updateStatusColor();
        
        // Actualizar color cuando cambie
        statusSelect.addEventListener('change', updateStatusColor);
    });
</script>

<script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.js"></script>

<script>
    $(document).ready(function() {
        $("form").validate({
            rules: {
                email: {
                    required: true,
                    email: true
                },
                full_name: {
                    required: true,
                    minlength: 3,
                    maxlength: 255
                },
                status: {
                    required: true
                },
                unit_id: {
                    required: true,
                    number: true,
                    min: 1
                }
            },
            messages: {
                email: {
                    required: "Por favor ingrese el email del usuario",
                    email: "Ingrese un email válido (ejemplo@dominio.com)"
                },
                full_name: {
                    required: "Por favor ingrese el nombre completo",
                    minlength: "El nombre debe tener al menos 3 caracteres",
                    maxlength: "El nombre debe tener máximo 255 caracteres"
                },
                status: {
                    required: "Por favor seleccione el estado del usuario"
                },
                unit_id: {
                    required: "Por favor ingrese el ID de unidad",
                    number: "El ID de unidad debe ser un número",
                    min: "El ID de unidad debe ser mayor a 0"
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
    // Funciones para selección rápida y conteo
    function selectAllRoles() {
        document.querySelectorAll('input[name="roles[]"]').forEach(checkbox => {
            checkbox.checked = true;
        });
        updateSelectedCount();
    }

    function deselectAllRoles() {
        document.querySelectorAll('input[name="roles[]"]').forEach(checkbox => {
            checkbox.checked = false;
        });
        updateSelectedCount();
    }

    // Actualizar contador
    function updateSelectedCount() {
        const checked = document.querySelectorAll('input[name="roles[]"]:checked').length;
        document.getElementById('selectedCount').textContent = checked;
    }

    // Inicializar contador y eventos al cargar
    document.addEventListener('DOMContentLoaded', function() {
        // Contador inicial
        updateSelectedCount();
        
        // Actualizar contador al cambiar checkboxes
        document.querySelectorAll('input[name="roles[]"]').forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectedCount);
        });
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection