@extends('layouts.app')

@section('title', 'Editar Usuario - SGPD COAC')
@section('active_key', 'users')
@php
    $targetIsAdmin = $user->roles->contains('name', 'ADMIN_SISTEMA');
@endphp

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
                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                    @error('email') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Sección de Seguridad (Opcional) --}}
                <div class="pt-6 border-t border-gray-100">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Seguridad (Opcional)</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                Nueva Contraseña
                            </label>
                            <div class="relative">
                                <input type="password" id="password" name="password" 
                                    class="w-full pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                    placeholder="Dejar en blanco para mantener la actual">
                                <button type="button" onclick="togglePassword('password', this)" 
                                        class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-blue-600 focus:outline-none">
                                    <svg class="w-5 h-5 icon-show" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg class="w-5 h-5 icon-hide hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                    </svg>
                                </button>
                            </div>
                            @error('password') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                Confirmar Nueva Contraseña
                            </label>
                            <div class="relative">
                                <input type="password" id="password_confirmation" name="password_confirmation" 
                                    class="w-full pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                <button type="button" onclick="togglePassword('password_confirmation', this)" 
                                        class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-blue-600 focus:outline-none">
                                    <svg class="w-5 h-5 icon-show" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg class="w-5 h-5 icon-hide hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    <p class="mt-2 text-xs text-gray-500 italic">Solo llene estos campos si desea cambiar la contraseña del usuario. Si los deja vacíos, se mantendrá la clave actual.</p>
                </div>

                {{-- Nombre Completo --}}
                <div>
                    <label for="full_name" class="block text-sm font-medium text-gray-700 mb-2">Nombre Completo <span class="text-red-500">*</span></label>
                    <input type="text" id="full_name" name="full_name" value="{{ old('full_name', $user->full_name) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                    @error('full_name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Estado --}}
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Estado <span class="text-red-500">*</span></label>
                    <select id="status" name="status" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        <option value="activo" {{ old('status', $user->status) == 'activo' ? 'selected' : '' }}>Activo</option>
                        <option value="suspendido" {{ old('status', $user->status) == 'suspendido' ? 'selected' : '' }}>Suspendido</option>
                    </select>
                </div>

                {{-- ID Unidad --}}
                <div>
                    <label for="unit_id" class="block text-sm font-medium text-gray-700 mb-2">ID de Unidad</label>
                    <input type="number" id="unit_id" name="unit_id" value="{{ old('unit_id', $user->unit_id) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                    @error('unit_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Ubicación --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-gray-100">
                    <div>
                        <label for="provincia" class="block text-sm font-medium text-gray-700 mb-2">Provincia <span class="text-red-500">*</span></label>
                        <select id="provincia" name="provincia" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                            @foreach(array_keys(config('ecuador.ubicaciones')) as $prov)
                                <option value="{{ $prov }}" {{ old('provincia', $user->provincia) == $prov ? 'selected' : '' }}>{{ $prov }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="canton" class="block text-sm font-medium text-gray-700 mb-2">Cantón <span class="text-red-500">*</span></label>
                        <select id="canton" name="canton" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white"></select>
                    </div>
                </div>

                {{-- Roles --}}
                <div class="pt-6 border-t border-gray-100">
                    <label for="role_id" class="block text-sm font-medium text-gray-700 mb-3">Asignación de Rol <span class="text-red-500">*</span></label>
                    
                    @php $currentRoleId = $user->roles->first() ? $user->roles->first()->role_id : ''; @endphp

                    <select id="role_id" name="role_id" 
                            class="w-full px-4 py-2.5 border {{ $errors->has('role_id') ? 'border-red-500' : 'border-gray-300' }} rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-white">
                        <option value="" {{ old('role_id', $currentRoleId) == '' ? 'selected' : '' }}>Sin Rol</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->role_id }}" {{ old('role_id', $currentRoleId) == $role->role_id ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('role_id')
                        <p class="mt-2 text-sm text-red-600 font-semibold">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500 italic">Nota: Si selecciona "Sin Rol", el usuario no tendrá permisos asignados.</p>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex flex-col sm:flex-row gap-3">
                <button type="submit" class="inline-flex justify-center items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-lg transition-colors flex-1 sm:flex-none">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Actualizar Usuario
                </button>
                <a href="{{ route('users.index') }}" class="inline-flex justify-center items-center gap-2 bg-white hover:bg-gray-50 text-gray-700 font-medium py-3 px-6 rounded-lg transition-colors border border-gray-300 flex-1 sm:flex-none">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.js"></script>

<script>
    const ubicaciones = @json(config('ecuador.ubicaciones'));
    const oldCanton = "{{ old('canton') }}";
    const dbCanton = "{{ $user->canton }}";

    $(document).ready(function() {
        const $provinciaSelect = $('#provincia');
        const $cantonSelect = $('#canton');

        // Variable pasada desde Blade
        const targetIsAdmin = @json($targetIsAdmin);
        const statusSelect = document.getElementById('status');

        if (statusSelect) {
            statusSelect.addEventListener('change', function() {
                if (this.value === 'suspendido' && targetIsAdmin) {
                    Swal.fire({
                        title: 'Acción no permitida',
                        text: 'No se puede cambiar el estado a "Suspendido" porque este usuario es ADMIN_SISTEMA.',
                        icon: 'error',
                        confirmButtonColor: '#3b82f6'
                    });
                    this.value = 'activo'; // Revertir selección automáticamente
                }
            });
        }

        function cargarCantones(provincia) {
            $cantonSelect.empty();
            if (provincia && ubicaciones[provincia]) {
                ubicaciones[provincia].forEach(function(canton) {
                    let isSelected = oldCanton ? (canton === oldCanton) : (canton === dbCanton);
                    $cantonSelect.append(new Option(canton, canton, isSelected, isSelected));
                });
            }
        }

        $provinciaSelect.on('change', function() { cargarCantones($(this).val()); });
        if ($provinciaSelect.val()) cargarCantones($provinciaSelect.val());

        $("form").validate({
            rules: {
                email: { required: true, email: true },
                full_name: { required: true, minlength: 3 },
                unit_id: { required: true, number: true }
                // role_id ya no es estrictamente requerido por jQuery si permitimos "Sin Rol" (vacío)
            },
            errorClass: "text-red-500 text-sm mt-1"
        });
    });
</script>
<script>
    function togglePassword(fieldId, button) {
        const input = document.getElementById(fieldId);
        const iconShow = button.querySelector('.icon-show');
        const iconHide = button.querySelector('.icon-hide');
        
        if (input.type === 'password') {
            input.type = 'text';
            iconShow.classList.add('hidden');
            iconHide.classList.remove('hidden');
        } else {
            input.type = 'password';
            iconShow.classList.remove('hidden');
            iconHide.classList.add('hidden');
        }
    }
</script>
@endsection