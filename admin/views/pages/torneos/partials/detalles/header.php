<?php
$txt = '-';
switch ($torneo['estado']) {
    case 'activo': $badge = 'success'; $txt = 'ACTIVO'; break;
    case 'planeado': $badge = 'secondary'; $txt = 'PLANEADO'; break;
    case 'finalizado': $badge = 'secondary'; $txt = 'FINALIZADO'; break;
    default: $badge = 'light'; break;
}
?>
<div class="entity-detail-heading torneo-detail-heading d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
            <h3 class="mb-0"><i class="fas fa-trophy me-2"></i><?= htmlspecialchars($torneo['nombre'], ENT_QUOTES, 'UTF-8') ?></h3>
            <span class="badge bg-<?= $badge ?> p-2"><?= htmlspecialchars(ucfirst($txt), ENT_QUOTES, 'UTF-8') ?></span>
            <span class="badge-season p-2"><?= htmlspecialchars($temporadaActiva['temporada'] ?? 'Sin temporada', ENT_QUOTES, 'UTF-8') ?></span>
        </div>
        <p class="mb-0 text-muted"><?= htmlspecialchars($torneo['liga'] ?? 'Sin liga', ENT_QUOTES, 'UTF-8') ?></p>
    </div>
</div>
