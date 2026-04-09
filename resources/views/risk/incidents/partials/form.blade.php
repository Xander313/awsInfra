@php
    $selectedTypesText = old('affected_data_types_text', collect(old('affected_data_types', $incident->affected_data_types ?? []))->implode(', '));
    $selectedDocIds = old('document_version_ids', $selectedDocumentVersionIds ?? []);
    $selectedOrg = (string) old('org_id', $incident->org_id ?? $selectedOrgId);
@endphp

<div x-data="incidentForm({
        selectedOrgId: @js($selectedOrg),
        currentOrgId: @js($currentOrgId),
        orgProfiles: @js($orgProfileAvailability),
    })"
    class="space-y-4">

    @if ($errors->any())
        <div class="alert alert-danger mb-0">
            Verifica los campos del formulario. Hay errores de validación pendientes.
        </div>
    @endif

    <div x-show="showProfileWarning()" class="alert alert-warning mb-0">
        La organización seleccionada no tiene perfil regulatorio/económico. Puedes registrar el incidente, pero todavía no podrá alimentar el cálculo sancionatorio en la siguiente fase.
    </div>

    <form action="{{ $action }}" method="POST" class="space-y-4">
        @csrf
        @if ($method !== 'POST')
            @method($method)
        @endif

        <div class="bg-white border rounded p-4">
            <div class="text-sm font-semibold text-gray-900 mb-4">Datos generales</div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-xs text-gray-500">Organización *</label>
                    @if ($currentOrgId)
                        <input type="hidden" name="org_id" value="{{ $currentOrgId }}">
                        <div class="mt-1 w-full border rounded px-3 py-2 text-sm bg-gray-50">
                            {{ $orgs->first()?->name ?? 'Organización activa' }}
                        </div>
                    @else
                        <select name="org_id"
                                x-model="selectedOrgId"
                                class="mt-1 w-full border rounded px-3 py-2 text-sm bg-white">
                            <option value="">-- Seleccione --</option>
                            @foreach ($orgs as $org)
                                <option value="{{ $org->org_id }}">{{ $org->name }}</option>
                            @endforeach
                        </select>
                    @endif
                    @error('org_id') <div class="text-danger text-sm mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="text-xs text-gray-500">Código *</label>
                    <input type="text" name="incident_code"
                           value="{{ old('incident_code', $incident->incident_code) }}"
                           class="mt-1 w-full border rounded px-3 py-2 text-sm"
                           placeholder="Ej: INC-2026-001">
                    @error('incident_code') <div class="text-danger text-sm mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="text-xs text-gray-500">Título *</label>
                    <input type="text" name="title"
                           value="{{ old('title', $incident->title) }}"
                           class="mt-1 w-full border rounded px-3 py-2 text-sm"
                           placeholder="Resumen corto del incidente">
                    @error('title') <div class="text-danger text-sm mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="text-xs text-gray-500">Descripción</label>
                    <textarea name="description" rows="4"
                              class="mt-1 w-full border rounded px-3 py-2 text-sm"
                              placeholder="Describe el incidente, su contexto y hallazgos operativos.">{{ old('description', $incident->description) }}</textarea>
                    @error('description') <div class="text-danger text-sm mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="text-xs text-gray-500">Tipo de incidente</label>
                    <input type="text" name="incident_type"
                           value="{{ old('incident_type', $incident->incident_type) }}"
                           class="mt-1 w-full border rounded px-3 py-2 text-sm"
                           placeholder="Ej: fuga, acceso indebido, indisponibilidad">
                    @error('incident_type') <div class="text-danger text-sm mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="text-xs text-gray-500">Estado *</label>
                    <select name="status" class="mt-1 w-full border rounded px-3 py-2 text-sm bg-white">
                        @foreach ($statusOptions as $value => $label)
                            <option value="{{ $value }}" @selected(old('status', $incident->status) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status') <div class="text-danger text-sm mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="text-xs text-gray-500">Severidad</label>
                    <select name="severity" class="mt-1 w-full border rounded px-3 py-2 text-sm bg-white">
                        <option value="">-- Seleccione --</option>
                        @foreach ($severityOptions as $value => $label)
                            <option value="{{ $value }}" @selected(old('severity', $incident->severity) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-xs text-gray-500">Rol de la empresa</label>
                    <select name="company_role" class="mt-1 w-full border rounded px-3 py-2 text-sm bg-white">
                        <option value="">-- Seleccione --</option>
                        @foreach ($companyRoleOptions as $value => $label)
                            <option value="{{ $value }}" @selected(old('company_role', $incident->company_role) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-xs text-gray-500">Sistema</label>
                    <select name="system_id" class="mt-1 w-full border rounded px-3 py-2 text-sm bg-white">
                        <option value="">-- Ninguno --</option>
                        @foreach ($systems as $system)
                            <option value="{{ $system->system_id }}"
                                    data-org-id="{{ $system->org_id }}"
                                    x-show="matchesOrg('{{ $system->org_id }}')"
                                    @selected((string) old('system_id', $incident->system_id) === (string) $system->system_id)>
                                {{ $system->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('system_id') <div class="text-danger text-sm mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="text-xs text-gray-500">Actividad de tratamiento</label>
                    <select name="pa_id" class="mt-1 w-full border rounded px-3 py-2 text-sm bg-white">
                        <option value="">-- Ninguna --</option>
                        @foreach ($processingActivities as $activity)
                            <option value="{{ $activity->pa_id }}"
                                    x-show="matchesOrg('{{ $activity->org_id }}')"
                                    @selected((string) old('pa_id', $incident->pa_id) === (string) $activity->pa_id)>
                                {{ $activity->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('pa_id') <div class="text-danger text-sm mt-1">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        <div class="bg-white border rounded p-4">
            <div class="text-sm font-semibold text-gray-900 mb-4">Fechas e indicadores</div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="text-xs text-gray-500">Ocurrencia</label>
                    <input type="datetime-local" name="occurred_at"
                           value="{{ old('occurred_at', optional($incident->occurred_at)->format('Y-m-d\\TH:i')) }}"
                           class="mt-1 w-full border rounded px-3 py-2 text-sm">
                    @error('occurred_at') <div class="text-danger text-sm mt-1">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="text-xs text-gray-500">Detección</label>
                    <input type="datetime-local" name="detected_at"
                           value="{{ old('detected_at', optional($incident->detected_at)->format('Y-m-d\\TH:i')) }}"
                           class="mt-1 w-full border rounded px-3 py-2 text-sm">
                    @error('detected_at') <div class="text-danger text-sm mt-1">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="text-xs text-gray-500">Cierre</label>
                    <input type="datetime-local" name="closed_at"
                           value="{{ old('closed_at', optional($incident->closed_at)->format('Y-m-d\\TH:i')) }}"
                           class="mt-1 w-full border rounded px-3 py-2 text-sm">
                    @error('closed_at') <div class="text-danger text-sm mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="text-xs text-gray-500">Cantidad de titulares</label>
                    <input type="number" min="1" name="data_subject_count"
                           value="{{ old('data_subject_count', $incident->data_subject_count) }}"
                           class="mt-1 w-full border rounded px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="text-xs text-gray-500">Volumen de datos</label>
                    <input type="number" min="0" step="0.01" name="data_volume_amount"
                           value="{{ old('data_volume_amount', $incident->data_volume_amount) }}"
                           class="mt-1 w-full border rounded px-3 py-2 text-sm">
                </div>
                <div class="flex items-end">
                    <label class="inline-flex items-center gap-2 text-sm">
                        <input type="checkbox" name="vulnerable_groups_flag" value="1"
                               @checked(old('vulnerable_groups_flag', $incident->vulnerable_groups_flag))>
                        Afecta grupos vulnerables
                    </label>
                </div>
            </div>
        </div>

        <div class="bg-white border rounded p-4">
            <div class="text-sm font-semibold text-gray-900 mb-4">Afectación</div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="text-xs text-gray-500">Confidencialidad</label>
                    <select name="confidentiality_impact" class="mt-1 w-full border rounded px-3 py-2 text-sm bg-white">
                        <option value="">-- Seleccione --</option>
                        @foreach ($impactOptions as $value => $label)
                            <option value="{{ $value }}" @selected(old('confidentiality_impact', $incident->confidentiality_impact) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs text-gray-500">Integridad</label>
                    <select name="integrity_impact" class="mt-1 w-full border rounded px-3 py-2 text-sm bg-white">
                        <option value="">-- Seleccione --</option>
                        @foreach ($impactOptions as $value => $label)
                            <option value="{{ $value }}" @selected(old('integrity_impact', $incident->integrity_impact) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs text-gray-500">Disponibilidad</label>
                    <select name="availability_impact" class="mt-1 w-full border rounded px-3 py-2 text-sm bg-white">
                        <option value="">-- Seleccione --</option>
                        @foreach ($impactOptions as $value => $label)
                            <option value="{{ $value }}" @selected(old('availability_impact', $incident->availability_impact) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-3">
                    <label class="text-xs text-gray-500">Tipos de datos afectados</label>
                    <textarea name="affected_data_types_text" rows="3"
                              class="mt-1 w-full border rounded px-3 py-2 text-sm"
                              placeholder="Ej: identificación, contacto, financieros">{{ $selectedTypesText }}</textarea>
                    <div class="text-[11px] text-gray-400 mt-1">
                        Ingresa valores separados por coma o por línea.
                    </div>
                    @error('affected_data_types') <div class="text-danger text-sm mt-1">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        <div class="bg-white border rounded p-4">
            <div class="text-sm font-semibold text-gray-900 mb-4">Documentos y evidencias</div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-xs text-gray-500">Tipo de vínculo</label>
                    <select name="document_relation_type" class="mt-1 w-full border rounded px-3 py-2 text-sm bg-white">
                        @foreach ($documentRelationTypeOptions as $value => $label)
                            <option value="{{ $value }}" @selected(old('document_relation_type', 'evidence') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

            </div>

            <div class="mt-4 space-y-2 max-h-72 overflow-auto">
                @forelse ($documentVersions as $version)
                    <label class="flex items-start gap-3 border rounded p-3" x-show="matchesOrg('{{ $version->document?->org_id }}')">
                        <input type="checkbox"
                               name="document_version_ids[]"
                               value="{{ $version->doc_ver_id }}"
                               @checked(in_array($version->doc_ver_id, $selectedDocIds))>
                        <div class="text-sm">
                            <div class="font-semibold">{{ $version->document?->title ?? 'Documento sin título' }}</div>
                            <div class="text-gray-500">
                                v{{ $version->version_no }} · {{ $version->document?->org?->name ?? 'Sin organización' }}
                            </div>
                        </div>
                    </label>
                @empty
                    <div class="text-sm text-gray-500">
                        No hay versiones activas de documentos disponibles para asociar.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="flex justify-end gap-2">
            <a href="{{ route('risk.ui.incidents.index') }}"
               class="px-4 py-2 rounded border hover:bg-gray-50">
                Cancelar
            </a>
            <button type="submit"
                    class="px-4 py-2 rounded bg-blue-600 hover:bg-blue-700 text-white">
                {{ $mode === 'create' ? 'Guardar incidente' : 'Actualizar incidente' }}
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function incidentForm(config) {
        return {
            selectedOrgId: config.selectedOrgId || '',
            currentOrgId: config.currentOrgId,
            orgProfiles: config.orgProfiles || {},
            matchesOrg(orgId) {
                if (!this.selectedOrgId) return true;
                return String(orgId) === String(this.selectedOrgId);
            },
            showProfileWarning() {
                if (!this.selectedOrgId) return false;
                return !Boolean(this.orgProfiles[this.selectedOrgId]);
            },
        };
    }
</script>
@endpush
