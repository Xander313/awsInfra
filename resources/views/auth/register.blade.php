@extends('layouts.auth')

@section('title', 'Crear Cuenta - SGPD COAC')

@section('content')
<div class="min-h-screen flex bg-white">
    
    <div class="hidden lg:block relative w-0 flex-1">
        <img class="absolute inset-0 h-full w-full object-cover" src="https://images.unsplash.com/photo-1497215728101-856f4ea42174?ixlib=rb-1.2.1&auto=format&fit=crop&w=1920&q=80" alt="Oficina Moderna">
        <div class="absolute inset-0 bg-blue-900 bg-opacity-50 mix-blend-multiply"></div>
        <div class="absolute inset-0 flex flex-col justify-center px-12 text-white">
            <h2 class="text-4xl font-extrabold tracking-tight mb-4">Únete a SGPD</h2>
            <p class="text-lg text-blue-100 max-w-md">Gestiona la privacidad y seguridad de los datos de manera integral y profesional.</p>
        </div>
    </div>

    <div class="flex-1 flex flex-col justify-center py-12 px-4 sm:px-6 lg:flex-none lg:px-20 xl:px-24 bg-gray-50 overflow-y-auto">
        <div class="mx-auto w-full max-w-sm lg:w-[32rem]"> <div class="text-center lg:text-left">
                <div class="flex justify-center lg:justify-start items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-blue-800 rounded-lg shadow-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                    </div>
                    <span class="text-2xl font-bold text-gray-900 tracking-tight">Registro</span>
                </div>
                <h2 class="mt-6 text-3xl font-extrabold text-gray-900">Crea tu cuenta</h2>
                <p class="mt-2 text-sm text-gray-600">Completa tus datos para acceder al sistema.</p>
            </div>

            <div class="mt-8">
                @if($errors->any())
                    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-md shadow-sm">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <ul class="list-disc list-inside text-sm text-red-700">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('register.post') }}" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div class="col-span-1 md:col-span-2">
                            <label for="name" class="block text-sm font-medium text-gray-700">Nombre Completo</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                </div>
                                <input id="name" name="name" type="text" autocomplete="name" required class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150" placeholder="Juan Pérez" value="{{ old('name') }}">
                            </div>
                        </div>

                        <div class="col-span-1 md:col-span-2">
                            <label for="email" class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" /></svg>
                                </div>
                                <input id="email" name="email" type="email" autocomplete="email" required class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150" placeholder="usuario@empresa.com" value="{{ old('email') }}">
                            </div>
                        </div>
                    </div>

                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                        <h3 class="text-sm font-semibold text-blue-800 mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Ubicación
                        </h3>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label for="provincia" class="block text-xs font-medium text-gray-600 uppercase tracking-wide mb-1">Provincia</label>
                                <div class="relative">
                                    <select id="provincia" name="provincia" class="block w-full pl-3 pr-8 py-2.5 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 sm:text-sm appearance-none bg-white">
                                        @foreach(array_keys(config('ecuador.ubicaciones')) as $prov)
                                            <option value="{{ $prov }}" {{ old('provincia') == $prov ? 'selected' : '' }}>{{ $prov }}</option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label for="canton" class="block text-xs font-medium text-gray-600 uppercase tracking-wide mb-1">Cantón</label>
                                <div class="relative">
                                    <select id="canton" name="canton" class="block w-full pl-3 pr-8 py-2.5 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 sm:text-sm appearance-none bg-white">
                                        </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">Contraseña</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input id="password" name="password" type="password" autocomplete="new-password" required class="block w-full pl-3 pr-10 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Mínimo 8 caracteres">
                                <button type="button" onclick="togglePasswordVisibility('password', 'password-eye-icon')" class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer focus:outline-none">
                                    <svg id="password-eye-icon" class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L6.59 6.59m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmar</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required class="block w-full pl-3 pr-10 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Repite la contraseña">
                                <button type="button" onclick="togglePasswordVisibility('password_confirmation', 'password-confirm-eye-icon')" class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer focus:outline-none">
                                    <svg id="password-confirm-eye-icon" class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L6.59 6.59m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div>
                        <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-blue-700 to-blue-900 hover:from-blue-800 hover:to-blue-950 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transform transition hover:-translate-y-0.5 duration-150">
                            Registrar Cuenta
                        </button>
                    </div>
                </form>

                <div class="mt-6">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-3 bg-gray-50 text-gray-500">¿Ya tienes cuenta?</span>
                        </div>
                    </div>

                    <div class="mt-6">
                        <a href="{{ route('login') }}" class="w-full flex justify-center items-center px-4 py-3 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150">
                            Iniciar Sesión
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Datos de configuración inyectados desde PHP
    const ubicaciones = @json(config('ecuador.ubicaciones'));
    const oldCanton = "{{ old('canton') }}";

    document.addEventListener('DOMContentLoaded', function() {
        const provinciaSelect = document.getElementById('provincia');
        const cantonSelect = document.getElementById('canton');

        function cargarCantones(provincia) {
            cantonSelect.innerHTML = '';
            
            if (provincia && ubicaciones[provincia]) {
                const cantones = ubicaciones[provincia];
                
                cantones.forEach(function(canton, index) {
                    let isSelected = false;

                    // Lógica de selección por defecto (Posición 1 o valor old)
                    if (oldCanton) {
                        if (canton === oldCanton) isSelected = true;
                    } else {
                        // Si no hay old, seleccionar el primero (índice 0)
                        if (index === 0) isSelected = true;
                    }
                    
                    const option = new Option(canton, canton, isSelected, isSelected);
                    cantonSelect.add(option);
                });
            }
        }

        // Evento cambio
        provinciaSelect.addEventListener('change', function() {
            cargarCantones(this.value);
        });

        // Inicialización
        if (provinciaSelect.value) {
            cargarCantones(provinciaSelect.value);
        } else {
            // Asegurar que la primera provincia esté seleccionada si no hay nada
            if (provinciaSelect.options.length > 0) {
                provinciaSelect.selectedIndex = 0;
                cargarCantones(provinciaSelect.value);
            }
        }
    });
</script>
@endsection