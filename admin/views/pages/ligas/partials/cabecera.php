<?php
$nombreLiga = htmlspecialchars((string) ($liga['nombre'] ?? 'Liga sin nombre'), ENT_QUOTES, 'UTF-8');
$logoUrl = htmlspecialchars((string) ($logo ?? '/afec/public/assets/img/ligas/default.png'), ENT_QUOTES, 'UTF-8');
$estadoBadge = $estadoClase ?? 'secondary';
$estadoLabel = htmlspecialchars((string) ($estadoTexto ?? 'Sin estado'), ENT_QUOTES, 'UTF-8');
$direccion = htmlspecialchars((string) ($direccionTexto ?? ''), ENT_QUOTES, 'UTF-8');
$correo = htmlspecialchars((string) ($liga['correo'] ?? ''), ENT_QUOTES, 'UTF-8');
$telefono = htmlspecialchars((string) ($liga['telefono'] ?? ''), ENT_QUOTES, 'UTF-8');
?>

<div class="card header-dark shadow-sm border-0 mb-3 p-3">
    <div class="row align-items-center g-3">
        <div class="col-12 col-md-2 text-center">
            <div class="escudo-wrapper mx-auto">
                <img src="<?= $logoUrl ?>" alt="Escudo de <?= $nombreLiga ?>" class="escudo-img">
            </div>
        </div>

        <div class="col-12 col-md-6">
            <h3 class="fw-bold mb-2 text-white"><?= $nombreLiga ?></h3>
            <div class="mb-2">
                <span class="badge bg-<?= htmlspecialchars((string) $estadoBadge, ENT_QUOTES, 'UTF-8') ?>"><?= $estadoLabel ?></span>
                <?php if (!empty($liga['siglas'])): ?>
                    <span class="text-light small ms-2"><?= htmlspecialchars((string) $liga['siglas'], ENT_QUOTES, 'UTF-8') ?></span>
                <?php endif; ?>
            </div>

            <div class="header-info small">
                <?php if ($direccion !== ''): ?>
                    <div><i class="bi bi-geo-alt"></i> <?= $direccion ?></div>
                <?php endif; ?>
                <?php if ($correo !== ''): ?>
                    <div><i class="bi bi-envelope"></i> <?= $correo ?></div>
                <?php endif; ?>
                <?php if ($telefono !== ''): ?>
                    <div><i class="bi bi-telephone"></i> <?= $telefono ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-12 col-md-4 d-flex justify-content-md-end align-items-center">
            <a href="index.php?page=ligas&action=index" class="btn btn-light btn-sm">
                <i class="bi bi-arrow-left"></i> Regresar
            </a>
        </div>
    </div>
</div>