<?php
$puedeResolverTemporadas = $puedeResolverTemporadas ?? false;
$temporadasDisponibles = $temporadasDisponibles ?? [];
$temporadasVinculadas = $temporadasVinculadas ?? [];
?>

<div class="mb-3">
    <h5 class="mb-1">Temporadas de la liga</h5>
    <p class="text-muted small mb-0">
        Las ligas con organismo gestor utilizan temporadas de ese organismo. Las ligas independientes utilizan sus propias temporadas.
    </p>
</div>

<?php if ($temporadasDisponibles): ?>
    <form method="POST" action="index.php?page=ligas&action=vincularTemporada" class="card card-body border mb-4">
        <input type="hidden" name="liga_id" value="<?= (int) $liga['id'] ?>">
        <label for="temporada_id" class="form-label fw-semibold">Vincular una temporada disponible</label>
        <div class="d-flex flex-wrap gap-2">
            <select class="form-select" id="temporada_id" name="temporada_id" required>
                <option value="">Selecciona una temporada</option>
                <?php foreach ($temporadasDisponibles as $temporada): ?>
                    <option value="<?= (int) $temporada['id'] ?>">
                        <?= htmlspecialchars($temporada['temporada'], ENT_QUOTES, 'UTF-8') ?>
                        <?= !empty($temporada['es_actual']) ? ' · Actual' : '' ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-primary">Solicitar vinculación</button>
        </div>
        <small class="text-muted mt-2">La solicitud se registra con estado pendiente.</small>
    </form>
<?php else: ?>
    <div class="alert alert-light border mb-4">
        No hay temporadas disponibles para vincular en el ámbito correspondiente.
    </div>
<?php endif; ?>

<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead>
            <tr><th>Temporada</th><th>Periodo</th><th>Estado de temporada</th><th>Vinculación</th><?php if ($puedeResolverTemporadas): ?><th>Acciones</th><?php endif; ?></tr>
        </thead>
        <tbody>
            <?php if (!$temporadasVinculadas): ?>
                <tr><td colspan="<?= $puedeResolverTemporadas ? 5 : 4 ?>" class="text-center text-muted py-4">Esta liga todavía no tiene temporadas vinculadas.</td></tr>
            <?php else: ?>
                <?php foreach ($temporadasVinculadas as $item): ?>
                    <tr>
                        <td>
                            <div class="fw-semibold"><?= htmlspecialchars($item['temporada'], ENT_QUOTES, 'UTF-8') ?></div>
                            <?php if (!empty($item['es_actual'])): ?><span class="badge bg-primary">Temporada actual</span><?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars((string) $item['fecha_inicio'], ENT_QUOTES, 'UTF-8') ?> – <?= htmlspecialchars((string) $item['fecha_fin'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars(ucfirst((string) $item['estado_temporada']), ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <?php
                            $estadoVinculacion = (string) $item['estado_vinculacion'];
                            $claseVinculacion = $estadoVinculacion === 'activo' ? 'success' : ($estadoVinculacion === 'pendiente' ? 'warning' : 'danger');
                            ?>
                            <span class="badge bg-<?= $claseVinculacion ?>"><?= htmlspecialchars(ucfirst($estadoVinculacion), ENT_QUOTES, 'UTF-8') ?></span>
                        </td>
                        <?php if ($puedeResolverTemporadas): ?>
                            <td>
                                <?php if ($estadoVinculacion === 'pendiente'): ?>
                                    <div class="d-flex flex-wrap gap-1">
                                        <form method="POST" action="index.php?page=ligas&action=resolverVinculacionTemporada">
                                            <input type="hidden" name="liga_id" value="<?= (int) $liga['id'] ?>">
                                            <input type="hidden" name="liga_temporada_id" value="<?= (int) $item['liga_temporada_id'] ?>">
                                            <input type="hidden" name="decision" value="aprobar">
                                            <button type="submit" class="btn btn-sm btn-success">Aprobar</button>
                                        </form>
                                        <form method="POST" action="index.php?page=ligas&action=resolverVinculacionTemporada">
                                            <input type="hidden" name="liga_id" value="<?= (int) $liga['id'] ?>">
                                            <input type="hidden" name="liga_temporada_id" value="<?= (int) $item['liga_temporada_id'] ?>">
                                            <input type="hidden" name="decision" value="rechazar">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Rechazar</button>
                                        </form>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted small">Sin acciones</span>
                                <?php endif; ?>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
