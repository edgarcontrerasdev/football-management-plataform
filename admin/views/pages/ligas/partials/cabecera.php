<?php
$nombreLiga = htmlspecialchars((string) ($liga['nombre'] ?? 'Liga sin nombre'), ENT_QUOTES, 'UTF-8');
$logoNombre = !empty($liga['logo']) ? $liga['logo'] : 'default.png';
$logoUrl = ASSETS . '/img/ligas/' . rawurlencode($logoNombre);
$estado = strtolower((string) ($liga['estado'] ?? ''));
$estadoTexto = $estadoTexto ?? match ($estado) {
    'activo' => 'Activa',
    'inactivo' => 'Inactiva',
    'suspendido' => 'Suspendida',
    default => 'Sin estado'
};
$estadoClase = $estadoClase ?? match ($estado) {
    'activo' => 'success',
    'inactivo' => 'secondary',
    'suspendido' => 'warning',
    default => 'secondary'
};
?>
<div class="liga-detail-page">
    <div class="entity-detail-heading d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="admin-page-title mb-1">Detalle de la liga</h3>
            <p class="admin-page-description mb-0">Información general y administración de la liga.</p>
        </div>
        <a href="index.php?page=ligas" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Regresar al listado
        </a>
    </div>

    <div class="card border-0 shadow-sm liga-identity-card mb-4">
        <div class="card-body p-4">
            <div class="row align-items-center g-4">
                <div class="col-auto">
                    <div class="liga-identity-logo">
                        <img src="<?= htmlspecialchars($logoUrl, ENT_QUOTES, 'UTF-8') ?>" alt="Logo de <?= $nombreLiga ?>">
                    </div>
                </div>
                <div class="col">
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                        <h4 class="fw-bold mb-0 liga-identity-name"><?= $nombreLiga ?></h4>
                        <span class="badge bg-<?= htmlspecialchars((string) $estadoClase, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string) $estadoTexto, ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <div class="text-muted mb-3">
                        <?= !empty($liga['siglas']) ? 'Siglas: ' . htmlspecialchars((string) $liga['siglas'], ENT_QUOTES, 'UTF-8') : 'Sin siglas registradas' ?>
                    </div>
                    <div class="liga-identity-meta">
                        <?php if (!empty($direccionTexto)): ?>
                            <span><i class="bi bi-geo-alt"></i><?= htmlspecialchars((string) $direccionTexto, ENT_QUOTES, 'UTF-8') ?></span>
                        <?php endif; ?>
                        <?php if (!empty($liga['correo'])): ?>
                            <span><i class="bi bi-envelope"></i><?= htmlspecialchars((string) $liga['correo'], ENT_QUOTES, 'UTF-8') ?></span>
                        <?php endif; ?>
                        <?php if (!empty($liga['telefono'])): ?>
                            <span><i class="bi bi-telephone"></i><?= htmlspecialchars((string) $liga['telefono'], ENT_QUOTES, 'UTF-8') ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-12 col-lg-auto">
                    <a href="index.php?page=ligas&action=editar&id=<?= (int) ($liga['id'] ?? 0) ?>" class="btn btn-primary">
                        <i class="bi bi-pencil-square me-1"></i>Editar liga
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
