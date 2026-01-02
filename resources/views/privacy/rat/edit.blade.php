@extends('layouts.app')

@section('title', 'Editar RAT')
@section('active_key', 'rat')

@section('content')
<div class="bg-white border rounded p-5">
    <h2 class="text-lg font-bold mb-4">Editar Actividad de Tratamiento</h2>

    <form method="POST"
          action="{{ route('rat.update', $activity->pa_id) }}"
          id="ratForm">
        @csrf
        @method('PUT')

        <div x-data="{ tab: 'details' }">

            {{-- TABS --}}
            <div class="flex border-b mb-4">
                <button type="button"
                        @click="tab='details'"
                        data-tab="details"
                        class="tab-btn px-4 py-2"
                        :class="tab==='details' ? 'border-b-2 border-blue-500 font-bold' : ''">
                    Detalles
                </button>

                <button type="button"
                        @click="tab='categories'"
                        data-tab="categories"
                        class="tab-btn px-4 py-2"
                        :class="tab==='categories' ? 'border-b-2 border-blue-500 font-bold' : ''">
                    Categorías
                </button>

                <button type="button"
                        @click="tab='retention'"
                        data-tab="retention"
                        class="tab-btn px-4 py-2"
                        :class="tab==='retention' ? 'border-b-2 border-blue-500 font-bold' : ''">
                    Retención
                </button>

                <button type="button"
                        @click="tab='transfers'"
                        data-tab="transfers"
                        class="tab-btn px-4 py-2"
                        :class="tab==='transfers' ? 'border-b-2 border-blue-500 font-bold' : ''">
                    Transferencias
                </button>
            </div>

            {{-- DETALLES --}}
            <div x-show="tab==='details'">
                <div class="mb-4 field-wrapper">
                    <label class="block text-sm font-medium">Nombre</label>
                    <input type="text"
                           name="name"
                           value="{{ old('name', $activity->name) }}"
                           class="w-full border rounded p-2">
                </div>
            </div>

            {{-- CATEGORÍAS --}}
            <div x-show="tab==='categories'">
                <div class="field-wrapper" id="categories-wrapper">
                    @foreach($categories as $cat)
                        @php
                            $checked = in_array($cat->data_cat_id, $selectedCategories);
                            $source  = $categoryPivot[$cat->data_cat_id] ?? '';
                        @endphp

                        <div class="mb-2 border p-2 rounded">
                            <label class="flex items-center gap-2">
                                <input type="checkbox"
                                       class="category-checkbox"
                                       name="data_categories[{{ $cat->data_cat_id }}][checked]"
                                       value="1"
                                       {{ $checked ? 'checked' : '' }}>
                                {{ $cat->name }}
                            </label>

                            <input type="text"
                                   name="data_categories[{{ $cat->data_cat_id }}][collection_source]"
                                   value="{{ $source }}"
                                   placeholder="Fuente de colección"
                                   class="border p-1 mt-1 w-full rounded">
                        </div>
                    @endforeach
                </div>

                <input type="hidden" name="categories_check" id="categories_check"
                       value="{{ count($selectedCategories) > 0 ? 1 : '' }}">
            </div>

            {{-- RETENCIÓN --}}
            <div x-show="tab==='retention'">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                    <div class="field-wrapper">
                        <input type="number"
                               name="retention_rules[0][retention_period_days]"
                               value="{{ old('retention_rules.0.retention_period_days', $retention->retention_period_days ?? '') }}"
                               placeholder="Días (1 a 365)"
                               class="border p-2 rounded w-full">
                    </div>

                    <div class="field-wrapper">
                        <input type="text"
                               name="retention_rules[0][trigger_event]"
                               value="{{ old('retention_rules.0.trigger_event', $retention->trigger_event ?? '') }}"
                               placeholder="Evento"
                               class="border p-2 rounded w-full">
                    </div>

                    <div class="field-wrapper">
                        <input type="text"
                               name="retention_rules[0][disposal_method]"
                               value="{{ old('retention_rules.0.disposal_method', $retention->disposal_method ?? '') }}"
                               placeholder="Método de disposición"
                               class="border p-2 rounded w-full">
                    </div>
                </div>
            </div>

            {{-- TRANSFERENCIAS --}}
            <div x-show="tab==='transfers'">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                    <div class="field-wrapper">
                        <select name="transfers[0][recipient_id]" class="border p-2 rounded w-full">
                            <option value="">Seleccione destinatario</option>
                            @foreach($recipients as $recipient)
                                <option value="{{ $recipient->recipient_id }}"
                                    {{ optional($transfer)->recipient_id == $recipient->recipient_id ? 'selected' : '' }}>
                                    {{ $recipient->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="field-wrapper">
                        <select name="transfers[0][country_id]" class="border p-2 rounded w-full">
                            <option value="">Seleccione país</option>
                            @foreach($countries as $country)
                                <option value="{{ $country->country_id }}"
                                    {{ optional($transfer)->country_id == $country->country_id ? 'selected' : '' }}>
                                    {{ $country->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="field-wrapper">
                        <input type="text"
                               name="transfers[0][transfer_type]"
                               value="{{ optional($transfer)->transfer_type }}"
                               placeholder="Tipo de transferencia"
                               class="border p-2 rounded w-full">
                    </div>

                    <div class="field-wrapper">
                        <input type="text"
                               name="transfers[0][safeguard]"
                               value="{{ optional($transfer)->safeguard }}"
                               placeholder="Salvaguarda"
                               class="border p-2 rounded w-full">
                    </div>

                    <div class="field-wrapper">
                        <input type="text"
                               name="transfers[0][legal_basis_text]"
                               value="{{ optional($transfer)->legal_basis_text }}"
                               placeholder="Base legal"
                               class="border p-2 rounded w-full">
                    </div>
                </div>
            </div>

            {{-- BOTONES --}}
            <div class="flex justify-end gap-3 mt-6">
                <button type="button"
                        onclick="history.back()"
                        class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                        <i class="fa-solid fa-arrow-left"></i> &nbsp;
                    Regresar
                </button>

                <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
                    Guardar &nbsp; <i class="fa-solid fa-save"></i>
                </button>
            </div>

        </div>
    </form>
</div>


<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>

<link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>


<script>
$(function () {

    // CAMPOS POR TAB
    const tabFields = {
        details: ['name'],
        categories: ['categories_check'],
        retention: [
            'retention_rules[0][retention_period_days]',
            'retention_rules[0][trigger_event]',
            'retention_rules[0][disposal_method]'
        ],
        transfers: [
            'transfers[0][recipient_id]',
            'transfers[0][country_id]',
            'transfers[0][transfer_type]',
            'transfers[0][safeguard]',
            'transfers[0][legal_basis_text]'
        ]
    };

    // DESHABILITAR TODOS LOS INPUTS DE FUENTE AL INICIO
    $('input[name*="[collection_source]"]').prop('disabled', true);

    // HABILITAR FUENTE PARA CATEGORÍAS YA MARCADAS (EDICIÓN)
    $('.category-checkbox:checked').each(function () {
        const container = $(this).closest('div');
        container.find('input[name*="[collection_source]"]').prop('disabled', false);
    });

    // HABILITAR/DESHABILITAR CHECKBOX DE CATEGORÍAS
    $('.category-checkbox').on('change', function () {

        const container = $(this).closest('div');
        const sourceInput = container.find('input[name*="[collection_source]"]');

        if ($(this).is(':checked')) {
            sourceInput.prop('disabled', false);
        } else {
            sourceInput.prop('disabled', true);
            sourceInput.val('');
        }

        if ($('.category-checkbox:checked').length > 0) {
            $('#categories_check').val('1');
        } else {
            $('#categories_check').val('');
        }

        $('#categories_check').valid();
    });

    // VALIDACIÓN PERSONALIZADA
    $.validator.addMethod("minOneCategory", function () {
        return $('.category-checkbox:checked').length > 0;
    });

    const validator = $("#ratForm").validate({
        ignore: [],

        rules: {
            name: {
                required: true,
                minlength: 3,
                maxlength: 150
            },

            "retention_rules[0][retention_period_days]": {
                required: true,
                digits: true,
                min: 1,
                max: 365
            },
            "retention_rules[0][trigger_event]": {
                required: true,
                minlength: 3,
                maxlength: 255
            },
            "retention_rules[0][disposal_method]": {
                required: true,
                minlength: 3,
                maxlength: 255
            },

            "transfers[0][recipient_id]": { required: true },
            "transfers[0][country_id]": { required: true },

            "transfers[0][transfer_type]": {
                required: true,
                minlength: 3,
                maxlength: 150
            },
            "transfers[0][safeguard]": {
                required: true,
                minlength: 3,
                maxlength: 150
            },
            "transfers[0][legal_basis_text]": {
                required: true,
                minlength: 3,
                maxlength: 150
            },

            categories_check: {
                minOneCategory: true
            }
        },

        messages: {
            name: {
                required: "El nombre es obligatorio",
                minlength: "El nombre debe tener al menos 3 caracteres",
                maxlength: "El nombre no puede superar los 150 caracteres"
            },

            "retention_rules[0][retention_period_days]": {
                required: "Ingrese los días",
                digits: "Solo números enteros",
                min: "Debe ser mayor a 0",
                max: "Máximo 365 días"
            },

            "retention_rules[0][trigger_event]": {
                required: "Ingrese el evento",
                minlength: "Mínimo 3 caracteres"
            },

            "retention_rules[0][disposal_method]": {
                required: "Ingrese el método",
                minlength: "Mínimo 3 caracteres"
            },

            "transfers[0][recipient_id]": "Seleccione un destinatario",
            "transfers[0][country_id]": "Seleccione un país",

            "transfers[0][transfer_type]": {
                required: "Campo obligatorio",
                minlength: "Mínimo 3 caracteres"
            },

            "transfers[0][safeguard]": {
                required: "Campo obligatorio",
                minlength: "Mínimo 3 caracteres"
            },

            "transfers[0][legal_basis_text]": {
                required: "Campo obligatorio",
                minlength: "Mínimo 3 caracteres"
            },

            categories_check: {
                minOneCategory: "Debe seleccionar al menos una categoría"
            }
        },

        errorElement: "span",
        errorClass: "text-red-600 text-sm mt-1",

        errorPlacement: function (error, element) {
            if (element.attr("name") === "categories_check") {
                error.appendTo("#categories-wrapper");
            } else {
                error.appendTo(element.closest(".field-wrapper"));
            }
        },

        highlight: function (element) {
            $(element).addClass("border-red-500 bg-red-50");
            actualizarTabs();
        },

        unhighlight: function (element) {
            $(element).removeClass("border-red-500 bg-red-50");
            actualizarTabs();
        },

        invalidHandler: function () {
            actualizarTabs();
            irPrimerTabConError();
        }
    });

    // TABS CON ERRORES
    function actualizarTabs() {

        $.each(tabFields, function (tab, fields) {

            let errores = 0;

            fields.forEach(function (field) {
                if (validator.invalid[field]) {
                    errores++;
                }
            });

            const btn = $('.tab-btn[data-tab="' + tab + '"]');

            btn.find('.error-count').remove();

            if (errores > 0) {
                btn.addClass('text-red-600 font-bold');
                btn.append('<span class="error-count ml-1 text-xs">(' + errores + ')</span>');
            } else {
                btn.removeClass('text-red-600 font-bold');
            }
        });
    }

    function irPrimerTabConError() {
        for (const tab in tabFields) {
            const btn = $('.tab-btn[data-tab="' + tab + '"]');
            if (btn.find('.error-count').length > 0) {
                btn.trigger('click');
                break;
            }
        }
    }

});
</script>
@endsection
