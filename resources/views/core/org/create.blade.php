@extends('layouts.app')

@section('title', 'Nueva Organización')
@section('active_key', 'org')

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
                              d="M3 21h18M5 21V7l7-4 7 4v14M9 21V9h6v12"/>
                    </svg>
                </div>
                <h2 class="text-lg font-semibold text-gray-900">
                    Nueva Organización
                </h2>
            </div>

            {{-- Body --}}
            <div class="p-6">
                <form id="frm_org"
                      method="POST"
                      action="{{ route('orgs.store') }}"
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
                            placeholder="Nombre de la organización"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 text-sm"
                        >
                    </div>

                    {{-- RUC --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            RUC
                        </label>
                        <input
                            type="text"
                            name="ruc"
                            placeholder="RUC (opcional)"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 text-sm"
                        >
                    </div>

                    {{-- Industria --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Industria
                        </label>
                        <input
                            type="text"
                            name="industry"
                            placeholder="Industria o sector"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 text-sm"
                        >
                    </div>

                    {{-- Acciones --}}
                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('orgs.index') }}"
                           class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 text-sm hover:bg-gray-50">
                            Cancelar
                        </a>

                        <button type="submit"
                                class="px-5 py-2 rounded-lg bg-blue-600 text-white text-sm font-medium hover:bg-blue-700">
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

        $("#frm_org").validate({
            rules: {
                name: {
                    required: true,
                    maxlength: 255
                },
                ruc: {
                    required: true,
                    digits: true,
                    minlength: 13,
                    maxlength: 13,
                    remote: {
                        url: "{{ route('orgs.check-ruc') }}",
                        type: "post",
                        data: {
                            _token: "{{ csrf_token() }}",
                            ruc: function () {
                                return $("input[name='ruc']").val();
                            }
                        }
                    }
                },
                industry: {
                    required: true,
                    maxlength: 100
                }
            },
            messages: {
                name: {
                    required: "El nombre de la organización es obligatorio",
                    maxlength: "El nombre no puede superar los 255 caracteres"
                },
                ruc: {
                    required: "El RUC es obligatorio",
                    digits: "El RUC debe contener solo números",
                    minlength: "El RUC debe tener exactamente 13 dígitos",
                    maxlength: "El RUC debe tener exactamente 13 dígitos",
                    remote: "Este RUC ya está registrado"
                },
                industry: {
                    required: "La industria es obligatoria",
                    maxlength: "La industria no puede superar los 100 caracteres"
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
