@extends('layouts.app')

@section('title', 'Nuevo Curso')
@section('active_key', 'courses')

@section('content')
<div class="flex justify-center mt-10">
    <div class="w-full max-w-xl">

        {{-- Card --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm">

            {{-- Header --}}
            <div class="px-6 py-4 border-b border-gray-200 flex items-center gap-2">
                <div class="w-9 h-9 rounded-lg bg-blue-600 text-white flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 14l9-5-9-5-9 5 9 5z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.955 11.955 0 0112 21
                                 a11.955 11.955 0 01-6.824-3.943
                                 a12.083 12.083 0 01.665-6.479L12 14z"/>
                    </svg>
                </div>
                <h2 class="text-lg font-semibold text-gray-900">
                    Nuevo Curso
                </h2>
            </div>

            {{-- Body --}}
            <div class="p-6">
                <form id="frm_course"
                      method="POST"
                      action="{{ route('training.courses.store') }}"
                      class="space-y-5">
                    @csrf

                    {{-- Nombre --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Nombre <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="name"
                            placeholder="Nombre del curso"
                            class="w-full rounded-lg border-gray-300
                                   focus:border-blue-500 focus:ring
                                   focus:ring-blue-200 text-sm"
                        >
                    </div>

                    {{-- Renovación --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Renovación (días)
                        </label>
                        <input
                            type="text"
                            name="renewal_days"
                            placeholder="Ej: 365"
                            class="w-full rounded-lg border-gray-300
                                   focus:border-blue-500 focus:ring
                                   focus:ring-blue-200 text-sm"
                        >
                    </div>

                    {{-- Obligatorio --}}
                    <div class="flex items-start gap-3">
                        <input
                            type="checkbox"
                            name="mandatory_flag"
                            value="1"
                            class="h-4 w-4 mt-1 text-blue-600
                                   border-gray-300 rounded"
                        >
                        <div>
                            <span class="text-sm text-gray-700 font-medium">
                                Curso obligatorio
                            </span>
                            <p class="text-xs text-gray-500">
                                Marque esta opción si el curso es obligatorio.
                            </p>
                        </div>
                    </div>

                    {{-- Acciones --}}
                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('training.courses.index') }}"
                           class="px-4 py-2 rounded-lg border border-gray-300
                                  text-gray-700 text-sm hover:bg-gray-50">
                            Cancelar
                        </a>

                        <button type="submit"
                                class="px-5 py-2 rounded-lg bg-blue-600
                                       text-white text-sm font-medium hover:bg-blue-700">
                            Guardar
                        </button>
                    </div>

                </form>
            </div>
        </div>

    </div>
</div>
@endsection
@push('scripts')
{{-- jQuery --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

{{-- jQuery Validate --}}
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>

<script>
$(function () {

    $("#frm_course").validate({
        rules: {
            name: {
                required: true,
                maxlength: 255
            },
            renewal_days: {
                digits: true,
                min: 1,
                maxlength: 5
            }
        },
        messages: {
            name: {
                required: "El nombre del curso es obligatorio",
                maxlength: "El nombre no puede superar los 255 caracteres"
            },
            renewal_days: {
                digits: "La renovación debe contener solo números",
                min: "Debe ser mayor a 0",
                maxlength: "El valor es demasiado grande"
            }
        },
        errorElement: "span",
        errorClass: "text-red-500 text-sm",
        highlight: function (element) {
            $(element).addClass("border-red-500");
        },
        unhighlight: function (element) {
            $(element).removeClass("border-red-500");
        }
    });

});
</script>
@endpush
