@extends('layouts.app')

@section('title', 'Editar Resultado')

@section('content')
<div class="flex justify-center mt-10">
    <div class="w-full max-w-xl">

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm">

            {{-- Header --}}
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">
                    Editar Resultado
                </h2>
            </div>

            {{-- Body --}}
            <div class="p-6">
                <form id="frm_result"
                      method="POST"
                      action="{{ route('training.results.update', $result) }}"
                      class="space-y-5">
                    @csrf
                    @method('PUT')

                    {{-- Info --}}
                    <div class="bg-gray-50 p-4 rounded-lg text-sm">
                        <p><strong>Usuario:</strong> {{ $result->assignment->user->full_name }}</p>
                        <p><strong>Curso:</strong> {{ $result->assignment->course->name }}</p>
                    </div>

                    {{-- Fecha --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Fecha de finalización <span class="text-red-500">*</span>
                        </label>
                        <input type="date"
                               name="completed_at"
                               value="{{ $result->completed_at }}"
                               class="w-full rounded-lg border-gray-300
                                      focus:border-blue-500 focus:ring
                                      focus:ring-blue-200 text-sm">
                    </div>

                    {{-- Puntaje --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Puntaje
                        </label>
                        <input type="number"
                               name="score"
                               value="{{ $result->score }}"
                               class="w-full rounded-lg border-gray-300
                                      focus:border-blue-500 focus:ring
                                      focus:ring-blue-200 text-sm">
                    </div>

                    {{-- Acciones --}}
                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('training.results.index') }}"
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
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>

<script>
$(function () {

    $("#frm_result").validate({
        rules: {
            completed_at: {
                required: true,
                date: true
            },
            score: {
                number: true,
                min: 0,
                max: 100
            }
        },
        messages: {
            completed_at: {
                required: "La fecha es obligatoria"
            },
            score: {
                number: "Ingrese un número válido",
                min: "Debe ser mayor o igual a 0",
                max: "No puede superar 100"
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
