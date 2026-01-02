\
@extends('layouts.app')

@section('title', 'Riesgos')
@section('active_key', 'risks')

@section('page_header')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
        <h2 class="text-xl font-bold">Riesgos</h2>
        <p class="text-sm text-gray-500">CRUD de <span class="font-mono">risk.risk</span></p>
    </div>

    <div class="flex gap-2">
        <a href="{{ url('/risk/ui/dpias') }}"
           class="bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-100 px-4 py-2 rounded flex items-center gap-2">
            <i class="fa-solid fa-clipboard-check"></i>
            Ir a DPIA
        </a>

        <button type="button"
                onclick="window.__riskUI?.openCreate?.()"
                class="bg-slate-900 hover:bg-slate-800 text-white px-4 py-2 rounded flex items-center gap-2">
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

<div x-data="riskPage()" x-init="init()" class="space-y-4">
    {{-- Filtros --}}
    <div class="bg-white border rounded p-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
            <div>
                <label class="text-xs text-gray-500">Buscar</label>
                <input type="text"
                       x-model="search"
                       placeholder="Nombre / tipo / estado..."
                       class="mt-1 w-full border rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>

            <div>
                <label class="text-xs text-gray-500">Organización</label>
                <select x-model="orgId"
                        class="mt-1 w-full border rounded px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="all">Todas</option>
                    <template x-for="o in orgs" :key="o.org_id">
                        <option :value="String(o.org_id)" x-text="`(${o.org_id}) ${o.name}`"></option>
                    </template>
                </select>
                <div class="text-[11px] text-gray-400 mt-1">
                    El backend usa <span class="font-mono">org_id=1</span> por defecto al crear si lo dejas vacío.
                </div>
            </div>

            <div class="flex gap-2">
                <button type="button"
                        @click="loadRisks()"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
                    <i class="fa-solid fa-rotate"></i>
                    Recargar
                </button>
                <button type="button"
                        @click="resetFilters()"
                        class="px-4 py-2 rounded text-sm border hover:bg-gray-50">
                    Limpiar
                </button>
            </div>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="bg-white border rounded">
        <div class="p-4 border-b flex items-center justify-between">
            <div class="text-sm text-gray-600">
                <span class="font-semibold" x-text="filteredRisks.length"></span> riesgos
            </div>
            <div class="text-xs text-gray-500" x-show="loading">
                Cargando...
            </div>
        </div>

        <div class="overflow-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="text-left px-4 py-3">ID</th>
                    <th class="text-left px-4 py-3">Nombre</th>
                    <th class="text-left px-4 py-3">Tipo</th>
                    <th class="text-left px-4 py-3">Estado</th>
                    <th class="text-left px-4 py-3">Org</th>
                    <th class="text-right px-4 py-3">Acciones</th>
                </tr>
                </thead>
                <tbody>
                <template x-if="filteredRisks.length === 0">
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-gray-500">
                            No hay riesgos para mostrar.
                        </td>
                    </tr>
                </template>

                <template x-for="r in filteredRisks" :key="r.risk_id">
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-4 py-3 font-mono" x-text="r.risk_id"></td>
                        <td class="px-4 py-3" x-text="r.name || '-'"></td>
                        <td class="px-4 py-3" x-text="r.risk_type || '-'"></td>
                        <td class="px-4 py-3" x-text="r.status || '-'"></td>
                        <td class="px-4 py-3" x-text="orgName(r.org_id)"></td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex justify-end gap-2">
                                <button class="px-3 py-1.5 rounded border hover:bg-white text-xs"
                                        @click="openEdit(r)">
                                    <i class="fa-solid fa-pen"></i> Editar
                                </button>
                                <button class="px-3 py-1.5 rounded border border-red-200 text-red-700 hover:bg-red-50 text-xs"
                                        @click="confirmDelete(r)">
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

    {{-- Modal Create/Edit --}}
    <div x-cloak x-show="modalOpen" class="fixed inset-0 z-50">
        <div class="absolute inset-0 bg-black/40" @click="closeModal()"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="bg-white w-full max-w-3xl rounded shadow-lg overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b">
                    <div class="font-semibold" x-text="mode === 'create' ? 'Nuevo riesgo' : 'Editar riesgo'"></div>
                    <button class="text-gray-500 hover:text-gray-800" @click="closeModal()">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="p-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs text-gray-500">Nombre</label>
                            <input type="text" x-model="form.name"
                                   class="mt-1 w-full border rounded px-3 py-2 text-sm"
                                   placeholder="Ej: Fuga de información por configuración..." />
                        </div>

                        <div>
                            <label class="text-xs text-gray-500">Organización</label>
                            <select x-model="form.org_id"
                                    class="mt-1 w-full border rounded px-3 py-2 text-sm bg-white">
                                <option value="">(usar 1 por defecto)</option>
                                <template x-for="o in orgs" :key="o.org_id">
                                    <option :value="String(o.org_id)" x-text="`(${o.org_id}) ${o.name}`"></option>
                                </template>
                            </select>
                        </div>

                        <div>
                            <label class="text-xs text-gray-500">Tipo (risk_type)</label>
                            <select x-model="form.risk_type"
                                    class="mt-1 w-full border rounded px-3 py-2 text-sm bg-white">
                                <template x-for="t in riskTypes" :key="t">
                                    <option :value="t" x-text="t"></option>
                                </template>
                            </select>
                        </div>

                        <div>
                            <label class="text-xs text-gray-500">Estado (status)</label>
                            <select x-model="form.status"
                                    class="mt-1 w-full border rounded px-3 py-2 text-sm bg-white">
                                <template x-for="s in riskStatuses" :key="s">
                                    <option :value="s" x-text="s"></option>
                                </template>
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="text-xs text-gray-500">Descripción</label>
                            <textarea x-model="form.description" rows="4"
                                      class="mt-1 w-full border rounded px-3 py-2 text-sm"
                                      placeholder="Describe el riesgo..."></textarea>
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

    function riskPage() {
        return {
            loading: false,
            saving: false,

            // filtros
            search: '',
            orgId: 'all',

            // meta
            orgs: [],
            riskTypes: ['SECURITY', 'PRIVACY', 'OPERATIONAL', 'LEGAL', 'COMPLIANCE', 'REPUTATIONAL', 'FINANCIAL', 'TECHNOLOGY'],
            riskStatuses: ['OPEN', 'IN_REVIEW', 'MITIGATED', 'ACCEPTED', 'CLOSED'],

            // data
            risks: [],

            // modal
            modalOpen: false,
            mode: 'create',
            form: { risk_id: null, name: '', org_id: '', risk_type: 'SECURITY', status: 'OPEN', description: '' },

            async init() {
                window.__riskUI = this;

                await this.loadOrgs();
                await this.loadRisks();
            },

            resetFilters() {
                this.search = '';
                this.orgId = 'all';
                this.loadRisks();
            },

            orgName(org_id) {
                const id = String(org_id ?? '');
                const o = this.orgs.find(x => String(x.org_id) === id);
                return o ? o.name : (id ? `Org ${id}` : '-');
            },

            get filteredRisks() {
                const q = (this.search || '').trim().toLowerCase();
                if (!q) return this.risks;

                return this.risks.filter(r => {
                    const orgLabel = this.orgName(r.org_id).toLowerCase();
                    return [
                        r.risk_id,
                        r.name,
                        r.description,
                        r.risk_type,
                        r.status,
                        orgLabel
                    ].some(v => String(v ?? '').toLowerCase().includes(q));
                });
            },

            async loadOrgs() {
                try {
                    this.orgs = await api(`{{ url('/risk/meta/orgs') }}`);
                } catch (e) {
                    // si falla, no bloquea la UI
                    this.orgs = [];
                }
            },

            async loadRisks() {
                this.loading = true;
                try {
                    let url = `{{ url('/risk/risks') }}`;
                    if (this.orgId && this.orgId !== 'all') {
                        url += `?org_id=${encodeURIComponent(this.orgId)}`;
                    }
                    this.risks = await api(url);
                } catch (e) {
                    notify('info', e.message, 'Error');
                } finally {
                    this.loading = false;
                }
            },

            openCreate() {
                this.mode = 'create';
                this.form = {
                    risk_id: null,
                    name: '',
                    org_id: (this.orgId && this.orgId !== 'all') ? this.orgId : '',
                    risk_type: 'SECURITY',
                    status: 'OPEN',
                    description: ''
                };
                this.modalOpen = true;
            },

            openEdit(r) {
                this.mode = 'edit';
                this.form = {
                    risk_id: r.risk_id,
                    name: r.name || '',
                    org_id: r.org_id ? String(r.org_id) : '',
                    risk_type: r.risk_type || 'SECURITY',
                    status: r.status || 'OPEN',
                    description: r.description || ''
                };
                this.modalOpen = true;
            },

            closeModal() {
                this.modalOpen = false;
            },

            async save() {
                this.saving = true;
                try {
                    const payload = {
                        name: this.form.name || null,
                        org_id: this.form.org_id !== '' ? Number(this.form.org_id) : undefined,
                        risk_type: this.form.risk_type || null,
                        status: this.form.status || null,
                        description: this.form.description || null
                    };

                    if (this.mode === 'create') {
                        await api(`{{ url('/risk/risks') }}`, { method: 'POST', body: JSON.stringify(payload) });
                    } else {
                        await api(`{{ url('/risk/risks') }}/${this.form.risk_id}`, { method: 'PUT', body: JSON.stringify(payload) });
                    }

                    this.closeModal();
                    await this.loadRisks();
                    notify('info', 'Guardado correctamente.', 'OK');
                } catch (e) {
                    notify('info', e.message, 'Error');
                } finally {
                    this.saving = false;
                }
            },

            confirmDelete(r) {
                Swal.fire({
                    title: 'Eliminar riesgo',
                    text: '¿Seguro? Si está asociado a un DPIA, el backend lo bloqueará.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then(async (result) => {
                    if (!result.isConfirmed) return;

                    try {
                        await api(`{{ url('/risk/risks') }}/${r.risk_id}`, { method: 'DELETE' });
                        await this.loadRisks();
                        notify('info', 'Riesgo eliminado.', 'OK');
                    } catch (e) {
                        notify('info', e.message, 'Error');
                    }
                });
            }
        }
    }
</script>
@endpush
@endsection
