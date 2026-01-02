\
@extends('layouts.app')

@section('title', 'DPIA')
@section('active_key', 'dpia')

@section('page_header')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
        <h2 class="text-xl font-bold">DPIA</h2>
        <p class="text-sm text-gray-500">Evaluación de impacto por actividad (<span class="font-mono">risk.dpia</span>)</p>
    </div>

    <div class="flex gap-2">
        <a href="{{ url('/risk/ui/risks') }}"
           class="bg-slate-900 hover:bg-slate-800 text-white px-4 py-2 rounded flex items-center gap-2">
            <i class="fa-solid fa-shield-halved"></i>
            Riesgos
        </a>

        <button type="button"
                onclick="window.__dpiaUI?.openCreate?.()"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded flex items-center gap-2">
            <i class="fa-solid fa-plus"></i>
            Nuevo
        </button>
    </div>
</div>
@endsection

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css"/>
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>

<div x-data="dpiaPage()" x-init="init()" class="space-y-4">

    {{-- Filtros --}}
    <div class="bg-white border rounded p-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
            <div>
                <label class="text-xs text-gray-500">Organización</label>
                <select x-model="orgId"
                        @change="onOrgChange()"
                        class="mt-1 w-full border rounded px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="all">Todas</option>
                    <template x-for="o in orgs" :key="o.org_id">
                        <option :value="String(o.org_id)" x-text="`(${o.org_id}) ${o.name}`"></option>
                    </template>
                </select>
            </div>

            <div class="md:col-span-2">
                <label class="text-xs text-gray-500">Actividad de tratamiento (pa_id)</label>
                <select x-model="paId"
                        class="mt-1 w-full border rounded px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Todas</option>
                    <template x-for="a in activities" :key="a.pa_id">
                        <option :value="String(a.pa_id)" x-text="`(${a.pa_id}) ${a.name}`"></option>
                    </template>
                </select>
                <div class="text-[11px] text-gray-400 mt-1">
                    Evita errores: selecciona la actividad desde el RAT.
                </div>
            </div>

            <div class="flex gap-2">
                <button type="button"
                        @click="loadDpias()"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
                    <i class="fa-solid fa-rotate"></i> Recargar
                </button>
                <button type="button"
                        @click="loadSummary()"
                        :disabled="!paId"
                        class="px-4 py-2 rounded text-sm border hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fa-solid fa-chart-column"></i> Resumen
                </button>
            </div>
        </div>
    </div>

    {{-- Resumen por actividad --}}
    <template x-if="summary">
        <div class="bg-white border rounded p-4">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <div class="text-sm text-gray-500">Resumen DPIA por actividad</div>
                    <div class="font-semibold" x-text="summary.activity?.name || '-'"></div>
                </div>
                <div class="text-xs text-gray-500">
                    DPIAs: <span class="font-semibold" x-text="summary.dpia_count ?? 0"></span>
                    • Riesgos asociados: <span class="font-semibold" x-text="summary.risk_count ?? 0"></span>
                </div>
            </div>

            <div class="mt-3 overflow-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="text-left px-3 py-2">DPIA</th>
                        <th class="text-left px-3 py-2">Estado</th>
                        <th class="text-left px-3 py-2">Riesgos</th>
                    </tr>
                    </thead>
                    <tbody>
                    <template x-for="d in summary.dpias || []" :key="d.dpia_id">
                        <tr class="border-t">
                            <td class="px-3 py-2 font-mono" x-text="d.dpia_id"></td>
                            <td class="px-3 py-2" x-text="d.status || '-'"></td>
                            <td class="px-3 py-2 text-xs text-gray-700">
                                <template x-if="(d.risks||[]).length===0">
                                    <span class="text-gray-400">Sin riesgos</span>
                                </template>
                                <template x-for="r in (d.risks||[])" :key="r.risk_id">
                                    <span class="inline-block mr-2 mb-1 px-2 py-1 rounded bg-gray-100">
                                        <span class="font-mono" x-text="r.risk_id"></span>:
                                        <span x-text="r.name || '-'"></span>
                                    </span>
                                </template>
                            </td>
                        </tr>
                    </template>
                    </tbody>
                </table>
            </div>
        </div>
    </template>

    {{-- Tabla DPIAs --}}
    <div class="bg-white border rounded">
        <div class="p-4 border-b flex items-center justify-between">
            <div class="text-sm text-gray-600">
                <span class="font-semibold" x-text="dpias.length"></span> DPIAs
            </div>
            <div class="text-xs text-gray-500" x-show="loading">Cargando...</div>
        </div>

        <div class="overflow-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="text-left px-4 py-3">ID</th>
                    <th class="text-left px-4 py-3">Actividad</th>
                    <th class="text-left px-4 py-3">Estado</th>
                    <th class="text-left px-4 py-3">Fecha</th>
                    <th class="text-left px-4 py-3">Resumen</th>
                    <th class="text-right px-4 py-3">Acciones</th>
                </tr>
                </thead>
                <tbody>
                <template x-if="dpias.length === 0">
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-gray-500">
                            No hay DPIAs para mostrar.
                        </td>
                    </tr>
                </template>

                <template x-for="d in dpias" :key="d.dpia_id">
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-4 py-3 font-mono" x-text="d.dpia_id"></td>
                        <td class="px-4 py-3">
                            <div class="text-xs text-gray-500 font-mono" x-text="`pa_id=${d.pa_id}`"></div>
                            <div x-text="activityName(d.pa_id)"></div>
                        </td>
                        <td class="px-4 py-3" x-text="d.status || '-'"></td>
                        <td class="px-4 py-3 text-xs text-gray-600" x-text="fmtDate(d.initiated_at)"></td>
                        <td class="px-4 py-3 text-xs text-gray-700">
                            <span x-text="(d.summary || '').slice(0, 120)"></span><span x-show="(d.summary||'').length>120">...</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex justify-end gap-2">
                                <button class="px-3 py-1.5 rounded border hover:bg-white text-xs"
                                        @click="openEdit(d)">
                                    <i class="fa-solid fa-pen"></i> Editar
                                </button>
                                <button class="px-3 py-1.5 rounded border hover:bg-white text-xs"
                                        @click="openRisks(d)">
                                    <i class="fa-solid fa-link"></i> Riesgos
                                </button>
                                <button class="px-3 py-1.5 rounded border border-red-200 text-red-700 hover:bg-red-50 text-xs"
                                        @click="confirmDelete(d)">
                                    <i class="fa-solid fa-trash"></i> Eliminar
                                </button>
                            </div>
                        </td>
                    </tr>
                </template>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Create/Edit DPIA --}}
    <div x-cloak x-show="modalOpen" class="fixed inset-0 z-50">
        <div class="absolute inset-0 bg-black/40" @click="closeModal()"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="bg-white w-full max-w-3xl rounded shadow-lg overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b">
                    <div class="font-semibold" x-text="mode === 'create' ? 'Nuevo DPIA' : 'Editar DPIA'"></div>
                    <button class="text-gray-500 hover:text-gray-800" @click="closeModal()">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="p-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs text-gray-500">Actividad (pa_id) *</label>
                            <select x-model="form.pa_id"
                                    class="mt-1 w-full border rounded px-3 py-2 text-sm bg-white">
                                <option value="">-- Selecciona --</option>
                                <template x-for="a in activities" :key="a.pa_id">
                                    <option :value="String(a.pa_id)" x-text="`(${a.pa_id}) ${a.name}`"></option>
                                </template>
                            </select>
                            <div class="text-[11px] text-gray-400 mt-1">
                                Se toma de <span class="font-mono">privacy.processing_activity</span>.
                            </div>
                        </div>

                        <div>
                            <label class="text-xs text-gray-500">Estado</label>
                            <select x-model="form.status"
                                    class="mt-1 w-full border rounded px-3 py-2 text-sm bg-white">
                                <template x-for="s in dpiaStatuses" :key="s">
                                    <option :value="s" x-text="s"></option>
                                </template>
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="text-xs text-gray-500">Resumen</label>
                            <textarea x-model="form.summary" rows="5"
                                      class="mt-1 w-full border rounded px-3 py-2 text-sm"
                                      placeholder="Resumen de la evaluación DPIA..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="px-5 py-4 border-t flex justify-end gap-2">
                    <button class="px-4 py-2 rounded border hover:bg-gray-50" @click="closeModal()">Cancelar</button>
                    <button class="px-4 py-2 rounded bg-blue-600 hover:bg-blue-700 text-white"
                            :disabled="saving"
                            @click="save()">
                        <span x-show="!saving">Guardar</span>
                        <span x-show="saving">Guardando...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal gestionar riesgos --}}
    <div x-cloak x-show="riskModalOpen" class="fixed inset-0 z-50">
        <div class="absolute inset-0 bg-black/40" @click="closeRiskModal()"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="bg-white w-full max-w-4xl rounded shadow-lg overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b">
                    <div class="font-semibold">
                        Gestionar riesgos de DPIA
                        <span class="text-xs text-gray-500 font-mono" x-text="selectedDpia ? `dpia_id=${selectedDpia.dpia_id}` : ''"></span>
                    </div>
                    <button class="text-gray-500 hover:text-gray-800" @click="closeRiskModal()">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="p-5 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
                        <div class="md:col-span-2">
                            <label class="text-xs text-gray-500">Agregar riesgo</label>
                            <select x-model="attach.risk_id"
                                    class="mt-1 w-full border rounded px-3 py-2 text-sm bg-white">
                                <option value="">-- Selecciona --</option>
                                <template x-for="r in risks" :key="r.risk_id">
                                    <option :value="String(r.risk_id)" x-text="`(${r.risk_id}) ${r.name || '(sin nombre)'} - ${r.status || ''}`"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <button class="w-full bg-slate-900 hover:bg-slate-800 text-white px-4 py-2 rounded text-sm"
                                    :disabled="saving || !attach.risk_id"
                                    @click="attachRisk()">
                                <i class="fa-solid fa-link"></i> Asociar
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="text-xs text-gray-500">Rationale (al asociar)</label>
                        <textarea x-model="attach.rationale" rows="2"
                                  class="mt-1 w-full border rounded px-3 py-2 text-sm"
                                  placeholder="Justificación de la relación DPIA - Riesgo..."></textarea>
                    </div>

                    <div class="border rounded overflow-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50 text-gray-600">
                            <tr>
                                <th class="text-left px-4 py-3">Riesgo</th>
                                <th class="text-left px-4 py-3">Tipo</th>
                                <th class="text-left px-4 py-3">Estado</th>
                                <th class="text-left px-4 py-3">Rationale</th>
                                <th class="text-right px-4 py-3">Acciones</th>
                            </tr>
                            </thead>
                            <tbody>
                            <template x-if="associatedRisks.length === 0">
                                <tr>
                                    <td colspan="5" class="px-4 py-10 text-center text-gray-500">
                                        Este DPIA no tiene riesgos asociados.
                                    </td>
                                </tr>
                            </template>

                            <template x-for="r in associatedRisks" :key="r.risk_id">
                                <tr class="border-t">
                                    <td class="px-4 py-3">
                                        <div class="font-mono text-xs text-gray-500" x-text="r.risk_id"></div>
                                        <div x-text="r.name || '-'"></div>
                                    </td>
                                    <td class="px-4 py-3" x-text="r.risk_type || '-'"></td>
                                    <td class="px-4 py-3" x-text="r.status || '-'"></td>
                                    <td class="px-4 py-3">
                                        <textarea class="w-full border rounded px-2 py-1 text-xs"
                                                  rows="2"
                                                  x-model="r.pivot.rationale"
                                                  placeholder="Rationale..."></textarea>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="flex justify-end gap-2">
                                            <button class="px-3 py-1.5 rounded border hover:bg-white text-xs"
                                                    :disabled="saving"
                                                    @click="saveRationale(r)">
                                                <i class="fa-solid fa-floppy-disk"></i> Guardar
                                            </button>
                                            <button class="px-3 py-1.5 rounded border border-red-200 text-red-700 hover:bg-red-50 text-xs"
                                                    :disabled="saving"
                                                    @click="detachRisk(r)">
                                                <i class="fa-solid fa-unlink"></i> Quitar
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="px-5 py-4 border-t flex justify-end gap-2">
                    <button class="px-4 py-2 rounded border hover:bg-gray-50" @click="closeRiskModal()">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
    function notify(type, message, title = '') {
    // Prefer existing global helper from layout (RAT)
    if (typeof window.toastify === 'function') {
        try {
            window.toastify(type, message, title || (type || 'info').toUpperCase());
            return;
        } catch (e) { /* fallthrough */ }
    }

    // Fallback: Toastify.js
    if (typeof Toastify === 'function') {
        const t = (title ? (title + ': ') : '') + (message || '');
        Toastify({
            text: t,
            duration: 3200,
            gravity: "top",
            position: "right",
            close: true
        }).showToast();
        return;
    }

    // Last resort
    alert((title ? (title + ': ') : '') + (message || ''));
}

function csrf() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    }

    async function api(url, options = {}) {
        const headers = options.headers || {};
        headers['Accept'] = 'application/json';
        if (options.method && options.method !== 'GET') {
            headers['Content-Type'] = 'application/json';
            headers['X-CSRF-TOKEN'] = csrf();
        }
        const res = await fetch(url, { ...options, headers });
        const text = await res.text();
        let data = null;
        try { data = text ? JSON.parse(text) : null; } catch (e) { /* ignore */ }

        if (!res.ok) {
            const msg = (data && (data.message || data.error)) ? (data.message || data.error) : `HTTP ${res.status}`;
            throw new Error(msg);
        }
        return data;
    }

    function dpiaPage() {
        return {
            loading: false,
            saving: false,

            // filtros
            orgId: 'all',
            paId: '',

            // meta
            orgs: [],
            activities: [],
            dpiaStatuses: ['draft', 'in_review', 'approved', 'rejected', 'done'],

            // data
            dpias: [],
            summary: null,

            // modal create/edit
            modalOpen: false,
            mode: 'create',
            form: { dpia_id: null, pa_id: '', status: 'draft', summary: '' },

            // modal riesgos
            riskModalOpen: false,
            selectedDpia: null,
            risks: [],
            associatedRisks: [],
            attach: { risk_id: '', rationale: '' },

            async init() {
                window.__dpiaUI = this;

                await this.loadOrgs();
                await this.onOrgChange(); // carga activities + riesgos del org
                await this.loadDpias();
            },

            async loadOrgs() {
                try {
                    this.orgs = await api(`{{ url('/risk/meta/orgs') }}`);
                } catch (e) {
                    this.orgs = [];
                }
            },

            async onOrgChange() {
                // recarga actividades (y lista de riesgos para asociar) según org
                await Promise.all([
                    this.loadActivities(),
                    this.loadRisksList(),
                ]);

                // si paId ya no existe en la lista, resetea
                if (this.paId && !this.activities.some(a => String(a.pa_id) === String(this.paId))) {
                    this.paId = '';
                }
            },

            async loadActivities() {
                try {
                    let url = `{{ url('/risk/meta/processing-activities') }}`;
                    if (this.orgId && this.orgId !== 'all') {
                        url += `?org_id=${encodeURIComponent(this.orgId)}`;
                    }
                    this.activities = await api(url);
                } catch (e) {
                    this.activities = [];
                }
            },

            async loadRisksList() {
                try {
                    let url = `{{ url('/risk/risks') }}`;
                    if (this.orgId && this.orgId !== 'all') {
                        url += `?org_id=${encodeURIComponent(this.orgId)}`;
                    }
                    this.risks = await api(url);
                } catch (e) {
                    this.risks = [];
                }
            },

            activityName(pa_id) {
                const id = String(pa_id ?? '');
                const a = this.activities.find(x => String(x.pa_id) === id);
                return a ? a.name : (id ? `Actividad ${id}` : '-');
            },

            fmtDate(v) {
                if (!v) return '-';
                // si viene como string ISO / timestamp
                const d = new Date(v);
                if (isNaN(d.getTime())) return String(v);
                return d.toLocaleString();
            },

            async loadDpias() {
                this.loading = true;
                try {
                    this.summary = null;

                    let url = `{{ url('/risk/dpias') }}`;
                    if (this.paId) {
                        url += `?pa_id=${encodeURIComponent(this.paId)}`;
                    }
                    this.dpias = await api(url);
                } catch (e) {
                    notify('info', e.message, 'Error');
                } finally {
                    this.loading = false;
                }
            },

            async loadSummary() {
                if (!this.paId) return;
                try {
                    this.summary = await api(`{{ url('/risk/processing-activities') }}/${this.paId}/dpia-summary`);
                } catch (e) {
                    notify('info', e.message, 'Error');
                }
            },

            openCreate() {
                this.mode = 'create';
                this.form = { dpia_id: null, pa_id: this.paId ? String(this.paId) : '', status: 'draft', summary: '' };
                this.modalOpen = true;
            },

            openEdit(d) {
                this.mode = 'edit';
                this.form = { dpia_id: d.dpia_id, pa_id: String(d.pa_id ?? ''), status: d.status || 'draft', summary: d.summary || '' };
                this.modalOpen = true;
            },

            closeModal() {
                this.modalOpen = false;
            },

            async save() {
                if (!this.form.pa_id) {
                    notify('info', 'Debes seleccionar un pa_id (actividad).', 'Validación');
                    return;
                }

                this.saving = true;
                try {
                    const payload = {
                        pa_id: Number(this.form.pa_id),
                        status: this.form.status || null,
                        summary: this.form.summary || null
                    };

                    if (this.mode === 'create') {
                        await api(`{{ url('/risk/dpias') }}`, { method: 'POST', body: JSON.stringify(payload) });
                    } else {
                        // pa_id es editable: si quieres bloquearlo, lo quitamos del payload
                        await api(`{{ url('/risk/dpias') }}/${this.form.dpia_id}`, { method: 'PUT', body: JSON.stringify(payload) });
                    }

                    this.closeModal();
                    await this.loadDpias();
                    notify('info', 'Guardado correctamente.', 'OK');
                } catch (e) {
                    notify('info', e.message, 'Error');
                } finally {
                    this.saving = false;
                }
            },

            confirmDelete(d) {
                Swal.fire({
                    title: 'Eliminar DPIA',
                    text: '¿Seguro? Esto eliminará el registro y sus asociaciones dpia_risk.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then(async (result) => {
                    if (!result.isConfirmed) return;

                    try {
                        await api(`{{ url('/risk/dpias') }}/${d.dpia_id}`, { method: 'DELETE' });
                        await this.loadDpias();
                        notify('info', 'DPIA eliminado.', 'OK');
                    } catch (e) {
                        notify('info', e.message, 'Error');
                    }
                });
            },

            async openRisks(d) {
                this.selectedDpia = d;
                this.attach = { risk_id: '', rationale: '' };
                this.riskModalOpen = true;

                // refresca dpia con risks
                try {
                    const full = await api(`{{ url('/risk/dpias') }}/${d.dpia_id}`);
                    this.associatedRisks = full.risks || [];
                } catch (e) {
                    this.associatedRisks = [];
                    notify('info', e.message, 'Error');
                }
            },

            closeRiskModal() {
                this.riskModalOpen = false;
                this.selectedDpia = null;
                this.associatedRisks = [];
            },

            async attachRisk() {
                if (!this.selectedDpia) return;
                this.saving = true;
                try {
                    const payload = {
                        risk_id: Number(this.attach.risk_id),
                        rationale: this.attach.rationale || null
                    };

                    const res = await api(`{{ url('/risk/dpias') }}/${this.selectedDpia.dpia_id}/risks`, {
                        method: 'POST',
                        body: JSON.stringify(payload)
                    });

                    this.associatedRisks = res.risks || [];
                    this.attach = { risk_id: '', rationale: '' };
                    notify('info', 'Riesgo asociado.', 'OK');
                } catch (e) {
                    notify('info', e.message, 'Error');
                } finally {
                    this.saving = false;
                }
            },

            async saveRationale(r) {
                if (!this.selectedDpia) return;
                this.saving = true;
                try {
                    const payload = { rationale: r.pivot?.rationale || null };
                    const res = await api(`{{ url('/risk/dpias') }}/${this.selectedDpia.dpia_id}/risks/${r.risk_id}`, {
                        method: 'PUT',
                        body: JSON.stringify(payload)
                    });
                    this.associatedRisks = res.risks || [];
                    notify('info', 'Rationale actualizado.', 'OK');
                } catch (e) {
                    notify('info', e.message, 'Error');
                } finally {
                    this.saving = false;
                }
            },

            detachRisk(r) {
                if (!this.selectedDpia) return;

                Swal.fire({
                    title: 'Quitar relación',
                    text: '¿Desasociar este riesgo del DPIA?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Sí, quitar',
                    cancelButtonText: 'Cancelar'
                }).then(async (result) => {
                    if (!result.isConfirmed) return;

                    this.saving = true;
                    try {
                        const res = await api(`{{ url('/risk/dpias') }}/${this.selectedDpia.dpia_id}/risks/${r.risk_id}`, {
                            method: 'DELETE'
                        });
                        this.associatedRisks = res.risks || [];
                        notify('info', 'Relación eliminada.', 'OK');
                    } catch (e) {
                        notify('info', e.message, 'Error');
                    } finally {
                        this.saving = false;
                    }
                });
            }
        }
    }

    // Función para guardar/actualizar DPIA (ejemplo)
async function saveDpia() {
    try {
        // Tu lógica actual de guardado...
        const formData = new FormData(document.getElementById('dpiaForm'));
        const response = await fetch('/risk/dpias', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const result = await response.json();
        
        if (response.ok) {
            // ✅ REGISTRO GUARDADO EXITOSAMENTE
            
            // 1. Notificar al dashboard que hay cambios
            if (window.dashboardNotifyUpdate) {
                window.dashboardNotifyUpdate();
            }
            
            // 2. Si las notificaciones están abiertas, recargarlas
            if (window.sgpdLayout && window.sgpdLayout.showNotifications) {
                await window.sgpdLayout.loadRealNotifications();
            }
            
            // 3. Mostrar mensaje de éxito (sin alert, mejor con toast)
            showToast('success', 'DPIA guardado exitosamente. Dashboard actualizado.');
            
            // 4. Cerrar modal/limpiar formulario
            closeModal();
            
        } else {
            showToast('error', result.message || 'Error al guardar');
        }
        
    } catch (error) {
        console.error('Error:', error);
        showToast('error', 'Error de conexión');
    }
}

// Función helper para mostrar toast
function showToast(type, message) {
    // Usar la librería que tengas (Toastify, SweetAlert, etc.)
    if (typeof Toastify === 'function') {
        Toastify({
            text: message,
            duration: 3000,
            gravity: "top",
            position: "right",
            backgroundColor: type === 'success' ? "#10b981" : "#ef4444"
        }).showToast();
    } else {
        alert(message); // Fallback
    }
}
</script>
@endpush

@endsection
