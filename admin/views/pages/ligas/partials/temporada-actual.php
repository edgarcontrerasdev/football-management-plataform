<?php
$nombreTemporada = $temporadaActual['nombre']
    ?? $temporadaActual['temporada']
    ?? null;
?>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <div class="text-muted small text-uppercase fw-semibold">Temporada actual</div>
            <div class="fw-semibold">
                <?= $nombreTemporada
                    ? htmlspecialchars((string) $nombreTemporada, ENT_QUOTES, 'UTF-8')
                    : 'No hay una temporada activa configurada' ?>
            </div>
        </div>
        <?php if (!empty($temporadaActual['estado'])): ?>
            <span class="badge bg-primary">
                <?= htmlspecialchars((string) $temporadaActual['estado'], ENT_QUOTES, 'UTF-8') ?>
            </span>
        <?php endif; ?>
    </div>
</div>