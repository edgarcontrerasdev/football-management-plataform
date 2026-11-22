<?php
$e = static fn($v) => htmlspecialchars((string) ($v ?? ''), ENT_QUOTES, 'UTF-8');
?>
<div class="organismo-detalle-page">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
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
            <div class="d-flex flex-wrap justify-content-between gap-3">
                <div><h4 class="fw-bold mb-1"><?= $e($organismo['nombre']) ?></h4><div class="text-muted"><?= $e($organismo['siglas'] ?: 'Sin siglas') ?></div></div>
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
        <div class="card border-0 shadow-sm"><div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-3"><div><h5 class="mb-1">Ligas relacionadas</h5><p class="text-muted small mb-0">Relaciones vigentes e históricas de este organismo.</p></div><span class="badge bg-light text-dark"><?= count($ligas ?? []) ?> relaciones</span></div>
            <?php if (empty($ligas)): ?><p class="text-muted mb-0">Este organismo todavía no tiene ligas relacionadas.</p><?php else: ?>
            <div class="table-responsive"><table class="table table-sm align-middle mb-0"><thead><tr><th>Liga</th><th>Relación</th><th>Gestor</th><th>Inicio</th><th>Fin</th><th>Estado</th></tr></thead><tbody>
            <?php foreach ($ligas as $liga): ?><tr><td><a href="index.php?page=ligas&action=detalles&id=<?= (int) $liga['id'] ?>"><?= $e($liga['nombre']) ?></a></td><td><?= $e($liga['tipo_relacion']) ?></td><td><?= (int) $liga['es_gestor'] === 1 ? '<span class="badge bg-primary">Sí</span>' : 'No' ?></td><td><?= $e($liga['fecha_inicio'] ? date('d/m/Y', strtotime($liga['fecha_inicio'])) : '-') ?></td><td><?= $e($liga['fecha_fin'] ? date('d/m/Y', strtotime($liga['fecha_fin'])) : '-') ?></td><td><?= $e($liga['estado_relacion']) ?></td></tr><?php endforeach; ?>
            </tbody></table></div><?php endif; ?>
        </div></div>
    <?php endif; ?>
</div>
