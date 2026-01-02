@extends('layouts.app')

@section('title', 'Nuevo Permiso - SGPD COAC')
@section('active_key', 'permissions')

@section('content')
<div class="container-fluid px-0">
    <!-- Encabezado -->
    <div class="bg-white rounded-lg border border-gray-200 mb-6 p-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Nuevo Permiso</h1>
                <p class="text-gray-600 mt-1">Registrar un nuevo permiso en el sistema SGPD COAC</p>
            </div>
            <a href="{{ route('permissions.index') }}" 
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
        <form action="{{ route('permissions.store') }}" method="POST">
            @csrf
            
            <div class="p-6 space-y-6">
                <!-- Campo Código -->
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                        Código del Permiso <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="code" 
                           name="code" 
                           value="{{ old('code') }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                           placeholder="Ej: privacy.dsar.manage, audit.view, user.create"
                           >
                    @error('code')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-sm text-gray-500">
                        Código único para identificar el permiso en el sistema (formato recomendado: modulo.accion).
                    </p>
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
                        placeholder="Describe el propósito y alcance de este permiso...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-sm text-gray-500">
                        Información adicional sobre qué permite hacer este permiso en el sistema.
                    </p>
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex flex-col sm:flex-row gap-3">
                <button type="submit" 
                        class="inline-flex justify-center items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-lg transition-colors flex-1 sm:flex-none">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Guardar Permiso
                </button>
                
                <a href="{{ route('permissions.index') }}" 
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
                code: {
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
                code: {
                    required: "Por favor ingrese el código del permiso",
                    minlength: "El código debe tener al menos 3 caracteres",
                    maxlength: "El código debe tener máximo 100 caracteres"
                },
                description: {
                    required: "Por favor ingrese la descripción del permiso",
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

<!-- SweetAlert2 CDN (ya incluido en layout, pero por si acaso) -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection