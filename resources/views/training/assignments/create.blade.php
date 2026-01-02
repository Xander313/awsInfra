@extends('layouts.app')

@section('title', 'Nueva Asignación')
@section('active_key', 'training_assignments')

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
                              d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h2 class="text-lg font-semibold text-gray-900">
                    Nueva Asignación
                </h2>
            </div>

            {{-- Body --}}
            <div class="p-6">
                <form id="frm_assignment"
                      method="POST"
                      action="{{ route('training.assignments.store') }}"
                      class="space-y-5">
                    @csrf

                    {{-- Usuario --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Usuario 
                        </label>
                        <select
                            name="user_id"
                            class="w-full rounded-lg border-gray-300
                                   focus:border-blue-500 focus:ring
                                   focus:ring-blue-200 text-sm">
                            <option value="">Seleccione usuario</option>
                            @foreach($users as $user)
                                <option value="{{ $user->user_id }}">
                                    {{ $user->full_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Curso --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Curso
                        </label>
                        <select
                            name="course_id"
                            class="w-full rounded-lg border-gray-300
                                   focus:border-blue-500 focus:ring
                                   focus:ring-blue-200 text-sm">
                            <option value="">Seleccione curso</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->course_id }}">
                                    {{ $course->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Fecha asignación --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Fecha de asignación 
                        </label>
                        <input
                            type="date"
                            name="assigned_at"
                            value="{{ now()->toDateString() }}"
                            class="w-full rounded-lg border-gray-300
                                   focus:border-blue-500 focus:ring
                                   focus:ring-blue-200 text-sm"
                        >
                    </div>

                    {{-- Fecha vencimiento --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Fecha de vencimiento
                        </label>
                        <input
                            type="date"
                            name="due_at"
                            class="w-full rounded-lg border-gray-300
                                   focus:border-blue-500 focus:ring
                                   focus:ring-blue-200 text-sm"
                        >
                    </div>

                    {{-- Acciones --}}
                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('training.assignments.index') }}"
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

    $("#frm_assignment").validate({
        rules: {
            user_id: {
                required: true
            },
            course_id: {
                required: true
            },
            assigned_at: {
                required: true,
                date: true
            },
            due_at: {
                date: true
            }
        },
        messages: {
            user_id: {
                required: "Debe seleccionar un usuario"
            },
            course_id: {
                required: "Debe seleccionar un curso"
            },
            assigned_at: {
                required: "La fecha de asignación es obligatoria",
                date: "Ingrese una fecha válida"
            },
            due_at: {
                date: "Ingrese una fecha válida"
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
