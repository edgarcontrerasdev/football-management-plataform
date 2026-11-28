<div class="temporada-show-page">
<div class="container mt-4">

    <h3><?= htmlspecialchars($temporada['temporada']) ?></h3>
    <p class="text-muted" class="temporada-detail-subtitle">Detalle de temporada</p>

    <table class="table table-bordered mt-3">
        <tr>
            <th>Propietario</th>
            <td>
                <?= htmlspecialchars($temporada['organismo_nombre'] ?? $temporada['liga_propietaria_nombre'] ?? 'No definido'); ?>
                <span class="text-muted small">
                    (<?= ($temporada['ambito_tipo'] ?? '') === 'organismo' ? 'Organismo' : 'Liga independiente'; ?>)
                </span>
            </td>
        </tr>
        <tr>
            <th>Estado</th>
            <td><?= ucfirst($temporada['estado']) ?></td>
        </tr>
        <tr>
            <th>Fecha inicio</th>
            <td><?= $temporada['fecha_inicio'] ?></td>
        </tr>
        <tr>
            <th>Fecha fin</th>
            <td><?= $temporada['fecha_fin'] ?></td>
        </tr>
        <tr>
            <th>Descripción</th>
            <td><?= $temporada['observaciones'] ?: '—' ?></td>
        </tr>
    </table>

    <?php if (($temporada['ambito_tipo'] ?? '') === 'organismo'): ?>
        <section class="card mt-4 mb-4">
            <div class="card-header">
                <h5 class="mb-0">Ligas participantes</h5>
            </div>
            <div class="card-body">
                <p class="text-muted small">
                    Desde aquí puedes autorizar o rechazar qué ligas gestionadas por este organismo participan en la temporada.
                </p>
                <?php if (empty($ligasParticipantes)): ?>
                    <div class="alert alert-light border mb-0">Este organismo todavía no tiene ligas gestoras vinculadas.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead><tr><th>Liga</th><th>Participación</th><?php if ($puedeGestionarParticipantes): ?><th>Acciones</th><?php endif; ?></tr></thead>
                            <tbody>
                            <?php foreach ($ligasParticipantes as $ligaParticipante): ?>
                                <?php
                                    $estadoParticipacion = $ligaParticipante['estado_participacion'];
                                    $claseEstado = $estadoParticipacion === 'activo' ? 'success' : ($estadoParticipacion === 'pendiente' ? 'warning' : ($estadoParticipacion === 'rechazado' ? 'danger' : 'secondary'));
                                    $etiquetaEstado = $estadoParticipacion === 'no_vinculada' ? 'Sin solicitud' : ucfirst($estadoParticipacion);
                                ?>
                                <tr>
                                    <td>
                                        <div class="fw-semibold"><?= htmlspecialchars($ligaParticipante['liga_nombre'], ENT_QUOTES, 'UTF-8') ?></div>
                                        <?php if (!empty($ligaParticipante['siglas'])): ?><small class="text-muted"><?= htmlspecialchars($ligaParticipante['siglas'], ENT_QUOTES, 'UTF-8') ?></small><?php endif; ?>
                                    </td>
                                    <td><span class="badge bg-<?= $claseEstado ?>"><?= htmlspecialchars($etiquetaEstado, ENT_QUOTES, 'UTF-8') ?></span></td>
                                    <?php if ($puedeGestionarParticipantes): ?>
                                        <td>
                                            <div class="d-flex flex-wrap gap-2">
                                                <?php if ($estadoParticipacion !== 'activo'): ?>
                                                    <form method="POST" action="index.php?page=temporadas&action=resolverParticipacionLiga">
                                                        <input type="hidden" name="temporada_id" value="<?= (int) $temporada['id'] ?>">
                                                        <input type="hidden" name="liga_id" value="<?= (int) $ligaParticipante['liga_id'] ?>">
                                                        <input type="hidden" name="decision" value="aprobar">
                                                        <button type="submit" class="btn btn-sm btn-success">Autorizar</button>
                                                    </form>
                                                <?php endif; ?>
                                                <?php if ($estadoParticipacion !== 'rechazado'): ?>
                                                    <form method="POST" action="index.php?page=temporadas&action=resolverParticipacionLiga">
                                                        <input type="hidden" name="temporada_id" value="<?= (int) $temporada['id'] ?>">
                                                        <input type="hidden" name="liga_id" value="<?= (int) $ligaParticipante['liga_id'] ?>">
                                                        <input type="hidden" name="decision" value="rechazar">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">Rechazar</button>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    <?php endif; ?>

    <div class="d-flex gap-2">

        <?php if ($puedeGestionarTemporada && $temporada['estado'] === 'borrador'): ?>

            <a href="index.php?page=temporadas&action=edit&id=<?= $temporada['id'] ?>"
               class="btn btn-warning">
               Editar
            </a>
            
            <a  href="index.php?page=temporadas&action=activar&id=<?= $temporada['id']; ?>"
                class="btn btn-sm btn-success"
                data-confirm="¿Confirmas que quieres activar esta temporada? Si aceptas se desactivara automaticamente la temporada ACTIVA">
                Activar
            </a>

            <a href="index.php?page=temporadas&action=delete&id=<?=  $temporada['id']; ?>"
                class="btn btn-sm btn-alert"
                data-confirm="Deseas eliminar esta temporada?">
                Eliminar
            </a>

        <?php elseif ($puedeGestionarTemporada && $temporada['estado'] === 'activa'): ?>
            <a  href="index.php?page=temporadas&action=cerrar&id=<?= $temporada['id']; ?>"
                class="btn btn-sm btn-outline-danger"
                data-confirm="¿Cerrar esta temporada?. Una vez cerrada no se podran asociar mas registros a este temporada">
                Cerrar
            </a>
        <?php endif; ?>

        <a href="index.php?page=temporadas" class="btn btn-light">
            Volver
        </a>

    </div>

</div>


<?php startSection('scripts') ?>
<script src="<?= BASE_URL ?>/public/assets/js/admin/temporadas/temporada-show.js"></script>
<?php endSection('scripts') ?>

</div>
