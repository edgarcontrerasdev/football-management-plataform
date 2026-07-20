<div class="modal fade" id="modalBloque" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form class="modal-content" id="formBloque">

            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">
                    Crear bloque del torneo
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <input type="hidden" name="torneo_id" value="<?= $torneo['id'] ?>">

                <div class="mb-3">
                    <label class="form-label">Nombre del bloque</label>
                    <input type="text" name="nombre" class="form-control" placeholder="Ej. Temporada regular" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tipo de bloque</label>
                        <select name="tipo_bloque" class="form-select">
                            <option value="oficial">Oficial</option>
                            <option value="amistoso">Amistoso</option>
                            <option value="pretemporada">Pretemporada</option>
                            <option value="entrenamiento">Entrenamiento</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Formato</label>
                        <select name="formato" class="form-select">
                            <option value="round_robin">Todos contra todos</option>
                            <option value="grupos">Grupos</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Vueltas planeadas</label>
                    <input type="number" name="vueltas_planeadas" class="form-control" min="0.5" step="0.5" value="1">
                </div>

                <hr>

                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" name="impacta_estadisticas" checked>
                    <label class="form-check-label">
                        Impacta estadísticas
                    </label>
                </div>

                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" name="impacta_suspensiones" checked>
                    <label class="form-check-label">
                        Impacta suspensiones
                    </label>
                </div>

                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" name="impacta_puntos" checked>
                    <label class="form-check-label">
                        Impacta puntos de tabla
                    </label>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-dark" data-bs-dismiss="modal">
                    Cancelar
                </button>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Guardar bloque
                </button>
            </div>

        </form>
    </div>
</div>