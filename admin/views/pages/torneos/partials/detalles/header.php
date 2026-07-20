<?php
$txt = '-';

switch ($torneo['estado']) {
    case 'activo':
        $badge = 'success';
        $txt = 'ACTIVO';
        break;

    case 'planeado':
        $badge = 'secondary';
        $txt = 'PLANEADO';
        break;

    case 'finalizado':
        $badge = 'cerrado';
        $txt = 'FINALIZADO';
        break;

    default:
        $badge = 'light';
        break;
}
?>

<div>
    <h3 class="users-title">
        <i class="fas fa-trophy"></i>

        <?= htmlspecialchars($torneo['nombre']) ?> -

        <small>
            <span class="text-muted">
                <?= htmlspecialchars($torneo['liga'] ?? 'Sin liga') ?>
            </span>
        </small>

        <span class="badge bg-<?= $badge ?> p-2">
            <?= ucfirst($txt); ?>
        </span>

        <span class="badge-season p-2 me-3">
            <small>
                <?= htmlspecialchars($temporadaActiva['temporada'] ?? 'Sin temporada') ?>
            </small>
        </span>
    </h3>
</div>