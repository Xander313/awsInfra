<div class="modal fade" id="coefficientsModal" tabindex="-1" aria-labelledby="coefficientsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title fw-bold" id="coefficientsModalLabel">Configurar coeficientes</h5>
                    <p class="text-muted small mb-0">Edición masiva de parámetros base del cálculo de multas.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body">
                <div class="alert alert-light border mb-3">
                    <div class="d-flex align-items-start gap-2">
                        <i class="bi bi-info-circle text-primary"></i>
                        <div class="small">
                            <div>Los parámetros normativos fijos provienen de la LOPDP y no pueden editarse ni desactivarse.</div>
                            <div>Los parámetros configurables del modelo pueden cambiar su valor, pero no desactivarse.</div>
                            <div>Los parámetros opcionales/condicionales pueden cambiar su valor y activarse o desactivarse.</div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 14rem;">Grupo</th>
                                <th style="width: 18rem;">Parámetro</th>
                                <th style="width: 10rem;">Valor</th>
                                <th style="width: 12rem;">Clase</th>
                                <th style="width: 10rem;">Edición</th>
                                <th style="width: 10rem;">Estado</th>
                            </tr>
                        </thead>
                        <tbody id="coefficientsTableBody">
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">Cargando coeficientes...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" @click="saveCoefficients()" :disabled="saving">
                    <span x-show="!saving">Guardar cambios</span>
                    <span x-show="saving">Guardando...</span>
                </button>
            </div>
        </div>
    </div>
</div>
