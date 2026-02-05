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
                           placeholder="ejemplo@dominio.com">
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
                           placeholder="Juan Pérez González">
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

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-gray-100">
                    <div>
                        <label for="provincia" class="block text-sm font-medium text-gray-700 mb-2">
                            Provincia <span class="text-red-500">*</span>
                        </label>
                        <select id="provincia" 
                                name="provincia" 
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-white">
                            @foreach(array_keys(config('ecuador.ubicaciones')) as $prov)
                                <option value="{{ $prov }}" {{ old('provincia', $user->provincia) == $prov ? 'selected' : '' }}>
                                    {{ $prov }}
                                </option>
                            @endforeach
                        </select>
                        @error('provincia')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="canton" class="block text-sm font-medium text-gray-700 mb-2">
                            Cantón <span class="text-red-500">*</span>
                        </label>
                        <select id="canton" 
                                name="canton" 
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-white">
                            </select>
                        @error('canton')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
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
                    Asignación de Rol <span class="text-red-500">*</span>
                </label>
                
                @php
                    // Obtener el ID del rol actual del usuario (si tiene)
                    $currentRoleId = $user->roles->first() ? $user->roles->first()->role_id : null;
                @endphp

                <select id="role_id" 
                        name="role_id" 
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-white">
                    <option value="">Seleccione un rol</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->role_id }}" {{ old('role_id', $currentRoleId) == $role->role_id ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
                
                @error('role_id')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
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
        
        function updateStatusColor() {
            const value = statusSelect.value;
            const colors = {
                'activo': 'border-green-300 bg-green-50 text-green-800',
                'suspendido': 'border-red-300 bg-red-50 text-red-800'
            };
            
            statusSelect.classList.remove(
                'border-green-300', 'bg-green-50', 'text-green-800',
                'border-gray-300', 'bg-gray-50', 'text-gray-800',
                'border-red-300', 'bg-red-50', 'text-red-800'
            );
            
            const [border, bg, text] = colors[value].split(' ');
            statusSelect.classList.add(border, bg, text);
        }
        
        updateStatusColor();
        statusSelect.addEventListener('change', updateStatusColor);
    });
</script>

<script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.js"></script>

<script>
    // Datos de configuración
    const ubicaciones = @json(config('ecuador.ubicaciones'));
    const oldCanton = "{{ old('canton') }}";
    const dbCanton = "{{ $user->canton }}"; // Valor en base de datos

    $(document).ready(function() {
        // 1. Lógica Provincia/Cantón
        const $provinciaSelect = $('#provincia');
        const $cantonSelect = $('#canton');

        function cargarCantones(provincia) {
            $cantonSelect.empty();
            
            if (provincia && ubicaciones[provincia]) {
                const cantones = ubicaciones[provincia];
                
                cantones.forEach(function(canton, index) {
                    // Seleccionar si:
                    // 1. Es el valor viejo (old) tras error de validación
                    // 2. Es el valor en DB (si no hay old)
                    // 3. Es el primero (index 0) si no hay ni old ni DB (default requerido)
                    let isSelected = false;

                    if (oldCanton) {
                        isSelected = (canton === oldCanton);
                    } else if (dbCanton) {
                        isSelected = (canton === dbCanton);
                    } else if (index === 0) {
                        isSelected = true;
                    }
                    
                    const option = new Option(canton, canton, isSelected, isSelected);
                    $cantonSelect.append(option);
                });
            }
        }

        // Evento cambio
        $provinciaSelect.on('change', function() {
            // Al cambiar manualmente, borramos referencia a DB/Old para que seleccione el primero de la nueva provincia
            // Pero si es la carga inicial, la lógica dentro de cargarCantones maneja la selección correcta.
            // Para simplificar: al cambiar provincia, el cantón seleccionado será el primero por defecto (lógica index 0)
            // a menos que volvamos a la provincia original donde dbCanton/oldCanton coincidan.
            cargarCantones($(this).val());
        });

        // Inicialización
        if ($provinciaSelect.val()) {
            cargarCantones($provinciaSelect.val());
        } else {
            // Forzar selección de primera provincia si viene vacío (aunque el HTML ya selecciona el primero)
            const primeraProvincia = $provinciaSelect.find('option:first').val();
            if(primeraProvincia) {
                $provinciaSelect.val(primeraProvincia).trigger('change');
            }
        }

        // 2. Validación
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
                },
                provincia: {
                    required: true
                },
                canton: {
                    required: true
                },
                role_id: {
                    required: true
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
                },
                provincia: {
                    required: "Seleccione una provincia"
                },
                canton: {
                    required: "Seleccione un cantón"
                },
                role_id: {
                    required: "Debe seleccionar un rol para el usuario"
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection