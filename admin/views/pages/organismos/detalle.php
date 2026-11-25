<?php
$e = static fn($v) => htmlspecialchars((string) ($v ?? ''), ENT_QUOTES, 'UTF-8');
?>
<div class="organismo-detalle-page">
    <div class="entity-detail-heading d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div><h3 class="page-title mb-1">Detalle del organismo</h3><p class="page-details mb-0">Información y ligas relacionadas.</p></div>
        <div class="d-flex gap-2">
            <?php if (!empty($organismo)): ?><a href="index.php?page=organismos&action=editar&id=<?= (int) $organismo['id'] ?>" class="btn btn-primary"><i class="fa-solid fa-pen me-1"></i>Editar</a><?php endif; ?>
            <a href="index.php?page=organismos" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i>Regresar</a>
        </div>
    </div>
    <?php if (empty($organismo)): ?>
        <div class="alert alert-warning">No se encontró el organismo solicitado.</div>
    <?php else: ?>
        <div class="card border-0 shadow-sm mb-4"><div class="card-body p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="organismo-logo-detail d-flex align-items-center justify-content-center rounded-3 bg-light border p-2" style="width:104px;height:104px;flex:0 0 104px;">
                        <img src="<?= $e(!empty($organismo['logo']) ? ASSETS . '/img/organismos/' . rawurlencode($organismo['logo']) : ASSETS . '/img/ligas/default.png') ?>" alt="Logotipo de <?= $e($organismo['nombre']) ?>" style="width:100%;height:100%;object-fit:contain;">
                    </div>
                    <div><h4 class="fw-bold mb-1"><?= $e($organismo['nombre']) ?></h4><div class="text-muted"><?= $e($organismo['siglas'] ?: 'Sin siglas') ?></div></div>
                </div>
                <div><span class="badge bg-<?= $organismo['estado'] === 'activo' ? 'success' : 'secondary' ?>"><?= $e(ucfirst($organismo['estado'])) ?></span></div>
            </div>
            <hr><div class="row g-3">
                <div class="col-md-4"><small class="text-muted d-block">Tipo</small><strong><?= $e(ucfirst($organismo['tipo'])) ?></strong></div>
                <div class="col-md-4"><small class="text-muted d-block">Alcance territorial</small><strong><?= $e($organismo['alcance_territorial'] ?: 'No especificado') ?></strong></div>
                <div class="col-md-4"><small class="text-muted d-block">Fecha de creación</small><strong><?= $e($organismo['fecha_creacion'] ? date('d/m/Y', strtotime($organismo['fecha_creacion'])) : 'No registrada') ?></strong></div>
                <div class="col-md-4"><small class="text-muted d-block">Correo</small><strong><?= $e($organismo['correo'] ?: 'No registrado') ?></strong></div>
                <div class="col-md-4"><small class="text-muted d-block">Teléfono</small><strong><?= $e($organismo['telefono'] ?: 'No registrado') ?></strong></div>
                <div class="col-md-4"><small class="text-muted d-block">Sitio web</small><?php if (!empty($organismo['sitio_web'])): ?><a href="<?= $e($organismo['sitio_web']) ?>" target="_blank" rel="noopener noreferrer"><?= $e($organismo['sitio_web']) ?></a><?php else: ?><strong>No registrado</strong><?php endif; ?></div>
                <div class="col-12"><small class="text-muted d-block">Descripción</small><p class="mb-0"><?= $organismo['descripcion'] ? nl2br($e($organismo['descripcion'])) : 'Sin descripción.' ?></p></div>
            </div>
        </div></div>
        <div class="card border-0 shadow-sm mb-4"><div class="card-body p-4">
            <h5 class="mb-1">Vincular una liga</h5>
            <p class="text-muted small mb-3">Una liga puede relacionarse con varios organismos, pero solo tendrá un organismo gestor vigente.</p>
            <?php if (!empty($errorRelacion)): ?><div class="alert alert-danger"><?= $e($errorRelacion) ?></div><?php endif; ?>
            <?php if (Auth::role() === 'ORGANISMO' && ($organismo['estado'] ?? '') === 'activo' && !empty($ligasDisponibles)): ?>
            <form action="index.php?page=organismos&action=vincularLiga&id=<?= (int) $organismo['id'] ?>" method="POST">
                <div class="row g-3">
                    <div class="col-lg-4"><label class="form-label" for="liga_id">Liga *</label><select class="form-select" name="liga_id" id="liga_id" required><option value="">Selecciona una liga</option><?php foreach ($ligasDisponibles as $disponible): ?><option value="<?= (int) $disponible['id'] ?>"><?= $e($disponible['nombre']) ?><?= !empty($disponible['siglas']) ? ' (' . $e($disponible['siglas']) . ')' : '' ?></option><?php endforeach; ?></select></div>
                    <div class="col-lg-3"><label class="form-label" for="tipo_relacion">Tipo de relación *</label><select class="form-select" name="tipo_relacion" id="tipo_relacion" required><option value="gestion">Gestión</option><option value="afiliacion">Afiliación</option><option value="colaboracion">Colaboración</option><option value="otro">Otro</option></select></div>
                    <div class="col-lg-3"><label class="form-label" for="fecha_inicio">Fecha de inicio *</label><input class="form-control" type="date" name="fecha_inicio" id="fecha_inicio" value="<?= date('Y-m-d') ?>" required></div>
                    <div class="col-lg-2 d-flex align-items-end"><div class="form-check mb-2"><input class="form-check-input" type="checkbox" name="es_gestor" id="es_gestor" value="1"><label class="form-check-label" for="es_gestor">Es gestor</label></div></div>
                    <div class="col-md-4"><label class="form-label" for="referencia">Referencia</label><input class="form-control" name="referencia" id="referencia" maxlength="100" placeholder="Opcional"></div>
                    <div class="col-md-8"><label class="form-label" for="observaciones_relacion">Observaciones</label><input class="form-control" name="observaciones" id="observaciones_relacion" placeholder="Opcional"></div>
                </div><div class="d-flex justify-content-end mt-3"><button class="btn btn-primary" type="submit"><i class="fa-solid fa-link me-1"></i>Vincular liga</button></div>
            </form>
            <?php elseif (Auth::role() !== 'ORGANISMO'): ?><div class="alert alert-info mb-0">Las solicitudes de vinculación se revisan en este apartado. El administrador global y las ligas no pueden vincularse directamente; deben enviar una solicitud para aprobación del organismo.</div>
            <?php elseif (($organismo['estado'] ?? '') !== 'activo'): ?><div class="alert alert-secondary mb-0">El organismo está inactivo y no puede recibir nuevas ligas.</div>
            <?php else: ?><p class="text-muted mb-0">No hay ligas activas disponibles para vincular.</p><?php endif; ?>
        </div></div>
        <div class="card border-0 shadow-sm mb-4" id="solicitudes-vinculacion"><div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-3"><div><h5 class="mb-1">Solicitudes de vinculación</h5><p class="text-muted small mb-0">Solo los administradores autorizados de este organismo pueden resolverlas.</p></div><span class="badge bg-warning text-dark"><?= count($solicitudesVinculacion ?? []) ?> pendientes</span></div>
            <?php if (!empty($solicitudesVinculacion)): ?>
                <?php foreach ($solicitudesVinculacion as $solicitud): ?>
                    <div class="border rounded-3 p-3 mb-3">
                        <div class="d-flex flex-wrap justify-content-between gap-2 mb-2"><div><strong><?= $e($solicitud['liga_nombre']) ?></strong> <span class="text-muted"><?= $e($solicitud['siglas'] ?? '') ?></span><div class="small text-muted">Solicitó: <?= $e($solicitud['solicitante'] ?? 'Usuario') ?> (<?= $e($solicitud['solicitante_rol']) ?>) · <?= $e(date('d/m/Y H:i', strtotime($solicitud['fecha_solicitud']))) ?></div></div><span class="badge bg-warning text-dark">Pendiente</span></div>
                        <?php if (Auth::role() === 'ORGANISMO'): ?>
                        <form method="POST" action="index.php?page=organismos&action=resolverSolicitudVinculacion&id=<?= (int) $organismo['id'] ?>">
                            <input type="hidden" name="solicitud_id" value="<?= (int) $solicitud['id'] ?>">
                            <div class="row g-2 align-items-end">
                                <div class="col-md-4"><label class="form-label small">Tipo de relación</label><select class="form-select form-select-sm" name="tipo_relacion"><option value="afiliacion">Afiliación</option><option value="gestion">Gestión</option><option value="colaboracion">Colaboración</option><option value="otro">Otro</option></select></div>
                                <div class="col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" name="es_gestor" id="gestor_solicitud_<?= (int) $solicitud['id'] ?>" value="1"><label class="form-check-label small" for="gestor_solicitud_<?= (int) $solicitud['id'] ?>">Será organismo gestor</label></div></div>
                                <div class="col-md-5"><label class="form-label small">Respuesta (opcional)</label><input class="form-control form-control-sm" name="respuesta" maxlength="255"></div>
                            </div>
                            <div class="d-flex justify-content-end gap-2 mt-3"><button class="btn btn-sm btn-outline-danger" name="decision" value="rechazar" type="submit">Rechazar</button><button class="btn btn-sm btn-primary" name="decision" value="aprobar" type="submit">Aprobar y vincular</button></div>
                        </form>
                        <?php else: ?><p class="small text-muted mb-0">Solo el administrador autorizado del organismo puede resolver esta solicitud.</p><?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?><p class="text-muted mb-0">No hay solicitudes pendientes de revisión.</p><?php endif; ?>
        </div></div>
        <div class="card border-0 shadow-sm"><div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-3"><div><h5 class="mb-1">Ligas relacionadas</h5><p class="text-muted small mb-0">Relaciones vigentes e históricas de este organismo.</p></div><span class="badge bg-light text-dark"><?= count($ligas ?? []) ?> relaciones</span></div>
            <?php if (empty($ligas)): ?><p class="text-muted mb-0">Este organismo todavía no tiene ligas relacionadas.</p><?php else: ?>
            <div class="table-responsive"><table class="table table-sm align-middle mb-0"><thead><tr><th>Liga</th><th>Relación</th><th>Gestor</th><th>Inicio</th><th>Fin</th><th>Estado</th><th class="text-end">Acción</th></tr></thead><tbody>
            <?php foreach ($ligas as $liga): ?><tr><td><a href="index.php?page=ligas&action=detalle&id=<?= (int) $liga['id'] ?>"><?= $e($liga['nombre']) ?></a></td><td><?= $e($liga['tipo_relacion']) ?></td><td><?= (int) $liga['es_gestor'] === 1 ? '<span class="badge bg-primary">Sí</span>' : 'No' ?></td><td><?= $e($liga['fecha_inicio'] ? date('d/m/Y', strtotime($liga['fecha_inicio'])) : '-') ?></td><td><?= $e($liga['fecha_fin'] ? date('d/m/Y', strtotime($liga['fecha_fin'])) : '-') ?></td><td><?= $e($liga['estado_relacion']) ?></td><td class="text-end"><?php if ($liga['estado_relacion'] === 'activo'): ?><form action="index.php?page=organismos&action=finalizarRelacion&id=<?= (int) $organismo['id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('¿Finalizar esta relación? Se conservará en el historial.');"><input type="hidden" name="relacion_id" value="<?= (int) $liga['relacion_id'] ?>"><input class="form-control form-control-sm d-inline-block me-1" style="width:145px" type="date" name="fecha_fin" value="<?= date('Y-m-d') ?>" required aria-label="Fecha de finalización"><button class="btn btn-sm btn-outline-danger" type="submit"><i class="fa-solid fa-link-slash me-1"></i>Finalizar</button></form><?php else: ?><span class="text-muted small">Histórica</span><?php endif; ?></td></tr><?php endforeach; ?>
            </tbody></table></div><?php endif; ?>
        </div></div>
    <?php endif; ?>
</div>
