@extends('layouts.app')

@section('title', 'Cálculo de multas')
@section('active_key', 'sanctions')

@section('page_header')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
        <h2 class="text-xl font-bold">Cálculo de multas</h2>
    </div>

    <div class="flex gap-2">


        <a href="{{ route('risk.ui.sanctions.methodology') }}"
           class="bg-white hover:bg-gray-50 text-gray-700 border px-4 py-2 rounded flex items-center gap-2">
            <i class="bi bi-journal-text"></i>
            Metodología de cálculo
        </a>

        <a href="{{ route('risk.ui.sanctions.simulations.index') }}"
           class="bg-white hover:bg-gray-50 text-gray-700 border px-4 py-2 rounded flex items-center gap-2">
            <i class="bi bi-clock-history"></i>
            Historial de cálculos
        </a>
        <button type="button"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded flex items-center gap-2"
                onclick="window.__sanctionsUI?.openConfigModal?.()">
            <i class="bi bi-sliders"></i>
            Configurar coeficientes
        </button>
    </div>
</div>
@endsection

@section('content')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<div x-data="sanctionsPage()" x-init="init()" class="space-y-4">
    @if (session('status'))
        <div class="alert alert-success mb-0">
            {{ session('status') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="bg-white border rounded p-4">
            <div class="text-xs uppercase tracking-wide text-gray-500">Coeficientes</div>
            <div class="text-3xl font-bold text-gray-900 mt-2" x-text="stats.total"></div>
            <div class="text-sm text-gray-500 mt-1">Parámetros configurables cargados</div>
        </div>

        <div class="bg-white border rounded p-4">
            <div class="text-xs uppercase tracking-wide text-gray-500">Activos</div>
            <div class="text-3xl font-bold text-gray-900 mt-2" x-text="stats.active"></div>
            <div class="text-sm text-gray-500 mt-1">Coeficientes habilitados para el cálculo</div>
        </div>

        <div class="bg-white border rounded p-4">
            <div class="text-xs uppercase tracking-wide text-gray-500">Última actualización</div>
            <div class="text-lg font-semibold text-gray-900 mt-1" x-text="stats.lastUpdated"></div>
            <div class="text-sm text-gray-500 mt-1">Para los valores de coeficientes </div>
        </div>

        <div class="bg-white border rounded p-4">
            <div class="text-xs uppercase tracking-wide text-gray-500">Simulaciones guardadas</div>
            <div class="text-3xl font-bold text-gray-900 mt-2">{{ $simulationMetrics['count'] ?? 0 }}</div>
            <div class="text-sm text-gray-500 mt-1">
                @if (!empty($simulationMetrics['latestAt']))
                    Última: {{ optional($simulationMetrics['latestAt'])->format('d/m/Y H:i') }}
                @else
                    Aún no hay historial guardado
                @endif
            </div>
        </div>
    </div>

    <div class="bg-white border rounded p-5">
        <div class="flex items-start justify-between gap-3 flex-wrap">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Asistente de cálculo de multas</h3>
            </div>

            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('risk.ui.sanctions.wizard.show', ['step' => max(1, $wizardCurrentStep)]) }}"
                   class="btn btn-primary">
                    <i class="bi bi-magic me-1"></i>
                    {{ $hasWizardState ? 'Continuar asistente' : 'Iniciar asistente' }}
                </a>




                @if ($hasWizardState)
                    <form method="POST" action="{{ route('risk.ui.sanctions.wizard.reset') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>
                            Reiniciar asistente
                        </button>
                    </form>
                @endif

            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mt-4">
            <div class="border rounded p-4 bg-gray-50">
                <div class="text-sm font-semibold text-gray-900">Estado del asistente</div>
                <ul class="text-sm text-gray-600 mt-2 space-y-2 mb-0">
                    <li>{{ $hasWizardState ? 'Hay una captura en sesión lista para continuar.' : 'Aún no hay una captura iniciada en sesión.' }}</li>
                </ul>
            </div>

        </div>
    </div>
<!--
    <div class="bg-white border rounded">
        <div class="p-4 border-b flex items-center justify-between">
            <div>
                <div class="text-sm font-semibold text-gray-900">Vista previa de coeficientes</div>
                <div class="text-xs text-gray-500">Resumen actual del set cargado en base de datos.</div>
            </div>
            <button type="button" class="btn btn-outline-primary btn-sm" @click="openConfigModal()">
                Editar
            </button>
        </div>

        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Grupo</th>
                        <th>Nombre</th>
                        <th>Clave</th>
                        <th>Valor</th>
                        <th>Tipo</th>
                        <th>Clase</th>
                        <th>Edición</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody id="previewTableBody">
                    @foreach ($coefficients as $coefficient)
                        <tr>
                            <td><span class="font-mono small">{{ $coefficient->group_name }}</span></td>
                            <td>
                                <div class="fw-semibold">{{ $coefficient->display_name }}</div>
                                <div class="text-muted small">{{ $coefficient->description }}</div>
                            </td>
                            <td><code>{{ $coefficient->coefficient_key }}</code></td>
                            <td>{{ number_format((float) $coefficient->value_numeric, 6, '.', '') }}</td>
                            <td><span class="badge text-bg-light border">{{ $coefficient->value_type }}</span></td>
                            <td>
                                @if ($coefficient->coefficient_class === 'normative_fixed')
                                    <span class="badge text-bg-dark">Normativo fijo</span>
                                @elseif ($coefficient->coefficient_class === 'optional_conditional')
                                    <span class="badge text-bg-warning">Opcional / condicional</span>
                                @else
                                    <span class="badge text-bg-primary">Configurable del modelo</span>
                                @endif
                            </td>
                            <td>
                                @if ($coefficient->value_editable)
                                    <span class="badge text-bg-info">Editable</span>
                                @else
                                    <span class="badge text-bg-secondary">Solo lectura</span>
                                @endif
                            </td>
                            <td>
                                @if ($coefficient->coefficient_class === 'normative_fixed')
                                    <span class="badge text-bg-success">Activo fijo</span>
                                @elseif ($coefficient->coefficient_class === 'optional_conditional' && !$coefficient->active_flag)
                                    <span class="badge text-bg-secondary">Inactivo</span>
                                @elseif ($coefficient->active_flag)
                                    <span class="badge text-bg-success">Activo</span>
                                @else
                                    <span class="badge text-bg-secondary">Inactivo</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    -->

    @include('risk.sanctions.partials.coefficients_modal')
</div>

@push('scripts')
<script>
    function sanctionsPage() {
        return {
            loading: false,
            saving: false,
            coefficients: @json($coefficients->values()),
            modalInstance: null,
            coefficientsIndexUrl: @json(route('risk.sanctions.coefficients.index', [], false)),
            coefficientsUpdateUrl: @json(route('risk.sanctions.coefficients.update', [], false)),
            stats: {
                total: {{ $totalCoefficients }},
                active: {{ $activeCoefficients }},
                lastUpdated: @json(optional($lastUpdatedAt)->format('d/m/Y H:i') ?? 'Sin cambios'),
            },

            init() {
                window.__sanctionsUI = this;
                const modalElement = document.getElementById('coefficientsModal');
                this.modalInstance = modalElement ? new bootstrap.Modal(modalElement) : null;
                this.renderPreview();
            },

            csrf() {
                return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            },

            async api(url, options = {}) {
                const headers = options.headers || {};
                headers['Accept'] = 'application/json';

                if (options.method && options.method !== 'GET') {
                    headers['Content-Type'] = 'application/json';
                    headers['X-CSRF-TOKEN'] = this.csrf();
                }

                const response = await fetch(url, { ...options, headers });
                const text = await response.text();
                let data = null;

                try {
                    data = text ? JSON.parse(text) : null;
                } catch (e) {
                    data = null;
                }

                if (!response.ok) {
                    let message = data?.message || `HTTP ${response.status}`;
                    const errors = data?.errors || {};
                    const firstError = Object.values(errors)[0];

                    if (Array.isArray(firstError) && firstError.length > 0) {
                        message = firstError[0];
                    }

                    throw new Error(message);
                }

                return data;
            },

            async loadCoefficients() {
                this.loading = true;

                try {
                    const payload = await this.api(this.coefficientsIndexUrl);
                    this.coefficients = payload.data || [];
                    this.refreshStats();
                    this.renderPreview();
                    this.renderModalRows();
                } finally {
                    this.loading = false;
                }
            },

            openConfigModal() {
                this.loadCoefficients()
                    .then(() => this.modalInstance?.show())
                    .catch((error) => {
                        Swal.fire({
                            icon: 'error',
                            title: 'No se pudieron cargar los coeficientes',
                            text: error.message,
                        });
                    });
            },

            renderPreview() {
                const body = document.getElementById('previewTableBody');
                if (!body) return;

                if (!this.coefficients.length) {
                    body.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-4">No hay coeficientes configurados.</td></tr>';
                    return;
                }

                body.innerHTML = this.coefficients.map((item) => `
                    <tr>
                        <td><span class="font-mono small">${this.escapeHtml(item.group_name)}</span></td>
                        <td>
                            <div class="fw-semibold">${this.escapeHtml(item.display_name)}</div>
                            <div class="text-muted small">${this.escapeHtml(item.description || '')}</div>
                        </td>
                        <td><code>${this.escapeHtml(item.coefficient_key)}</code></td>
                        <td>${this.formatNumber(item.value_numeric)}</td>
                        <td><span class="badge text-bg-light border">${this.escapeHtml(item.value_type)}</span></td>
                        <td>${this.classBadge(item)}</td>
                        <td>${this.editBadge(item)}</td>
                        <td>${this.staticStatusBadge(item)}</td>
                    </tr>
                `).join('');
            },

            renderModalRows() {
                const body = document.getElementById('coefficientsTableBody');
                if (!body) return;

                if (!this.coefficients.length) {
                    body.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-4">No hay coeficientes configurados.</td></tr>';
                    return;
                }

                let currentGroup = null;
                const rows = [];

                this.coefficients.forEach((item) => {
                    if (currentGroup !== item.group_name) {
                        currentGroup = item.group_name;
                        rows.push(`
                            <tr class="table-secondary">
                                <td colspan="8" class="fw-semibold">${this.escapeHtml(currentGroup)}</td>
                            </tr>
                        `);
                    }

                    rows.push(`
                        <tr>
                            <td class="font-mono small text-muted">${this.escapeHtml(item.group_name)}</td>
                            <td>
                                <div class="fw-semibold">${this.escapeHtml(item.display_name)}</div>
                                <div class="text-muted small">${this.escapeHtml(item.description || '')}</div>
                            </td>
                            <td>
                                <input
                                    type="number"
                                    class="form-control form-control-sm js-coefficient-value"
                                    data-id="${item.coefficient_id}"
                                    step="${this.inputStep(item.value_type)}"
                                    value="${this.formatNumber(item.value_numeric)}"
                                    ${item.value_editable ? '' : 'readonly'}
                                    ${item.value_editable ? '' : 'aria-readonly="true"'}
                                    ${item.value_editable ? '' : 'style="background-color:#f8f9fa;"'}
                                    required
                                >
                                ${item.coefficient_class === 'normative_fixed' ? '<div class="form-text text-warning-emphasis">Este parámetro proviene del rango sancionatorio establecido por la LOPDP y su edición o desactivación no está permitida.</div>' : ''}
                            </td>
                            <td>
                                ${this.classBadge(item)}
                            </td>
                            <td>
                                ${this.editBadge(item)}
                            </td>
                            <td>
                                ${this.statusCell(item)}
                            </td>
                        </tr>
                    `);
                });

                body.innerHTML = rows.join('');
            },

            async saveCoefficients() {
                const valueInputs = Array.from(document.querySelectorAll('.js-coefficient-value'));
                const activeInputs = new Map(
                    Array.from(document.querySelectorAll('.js-coefficient-active')).map((input) => [input.dataset.id, input])
                );

                const coefficients = [];

                for (const input of valueInputs) {
                    const trimmed = String(input.value ?? '').trim();
                    const parsed = Number(trimmed);

                    if (trimmed === '' || !Number.isFinite(parsed)) {
                        input.focus();
                        input.classList.add('is-invalid');

                        Swal.fire({
                            icon: 'warning',
                            title: 'Valor inválido',
                            text: 'Todos los coeficientes deben tener un valor numérico válido.',
                        });
                        return;
                    }

                    input.classList.remove('is-invalid');

                    coefficients.push({
                        coefficient_id: Number(input.dataset.id),
                        value_numeric: parsed,
                        active_flag: activeInputs.has(input.dataset.id) ? (activeInputs.get(input.dataset.id)?.checked ? 1 : 0) : 1,
                    });
                }

                this.saving = true;

                try {
                    const payload = await this.api(this.coefficientsUpdateUrl, {
                        method: 'PUT',
                        body: JSON.stringify({ coefficients }),
                    });

                    this.coefficients = payload.data || [];
                    this.refreshStats();
                    this.renderPreview();
                    this.renderModalRows();
                    this.modalInstance?.hide();

                    Swal.fire({
                        icon: 'success',
                        title: 'Coeficientes actualizados',
                        text: payload.message || 'Los cambios se guardaron correctamente.',
                        timer: 1800,
                        showConfirmButton: false,
                    });
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'No se pudo guardar',
                        text: error.message,
                    });
                } finally {
                    this.saving = false;
                }
            },

            refreshStats() {
                this.stats.total = this.coefficients.length;
                this.stats.active = this.coefficients.filter((item) => item.active_flag).length;
                this.stats.lastUpdated = new Date().toLocaleString('es-EC', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                });
            },

            inputStep(type) {
                return ['integer'].includes(type) ? '1' : '0.000001';
            },

            formatNumber(value) {
                const parsed = Number(value);
                return Number.isFinite(parsed) ? parsed.toFixed(6) : '0.000000';
            },

            classBadge(item) {
                if (item.coefficient_class === 'normative_fixed') {
                    return '<span class="badge text-bg-dark">Normativo fijo</span>';
                }

                if (item.coefficient_class === 'optional_conditional') {
                    return '<span class="badge text-bg-warning">Opcional / condicional</span>';
                }

                return '<span class="badge text-bg-primary">Configurable del modelo</span>';
            },

            editBadge(item) {
                if (item.value_editable) {
                    return '<span class="badge text-bg-info">Editable</span>';
                }

                return '<span class="badge text-bg-secondary">Solo lectura</span>';
            },

            staticStatusBadge(item) {
                if (item.coefficient_class === 'normative_fixed') {
                    return '<span class="badge text-bg-success">Activo fijo</span>';
                }

                if (item.coefficient_class === 'model_configurable') {
                    return '<span class="badge text-bg-success">Activo</span>';
                }

                return item.active_flag
                    ? '<span class="badge text-bg-success">Activo</span>'
                    : '<span class="badge text-bg-secondary">Inactivo</span>';
            },

            statusCell(item) {
                if (item.coefficient_class === 'normative_fixed') {
                    return '<span class="badge text-bg-success">Activo fijo</span>';
                }

                if (!item.toggle_allowed) {
                    return '<span class="badge text-bg-success">Activo</span>';
                }

                return `
                    <div class="form-check form-switch">
                        <input
                            class="form-check-input js-coefficient-active"
                            type="checkbox"
                            data-id="${item.coefficient_id}"
                            ${item.active_flag ? 'checked' : ''}
                        >
                    </div>
                `;
            },

            escapeHtml(value) {
                return String(value ?? '')
                    .replaceAll('&', '&amp;')
                    .replaceAll('<', '&lt;')
                    .replaceAll('>', '&gt;')
                    .replaceAll('"', '&quot;')
                    .replaceAll("'", '&#039;');
            },
        };
    }
</script>
@endpush
@endsection
