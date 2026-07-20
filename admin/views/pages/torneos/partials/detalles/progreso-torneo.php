<?php
$totalPasos = 5;

$pasosCompletados = 0;
$pasosCompletados += !empty($progreso['datos']) ? 1 : 0;
$pasosCompletados += !empty($progreso['costos']) ? 1 : 0;
$pasosCompletados += !empty($progreso['equipos']) ? 1 : 0;
$pasosCompletados += !empty($progreso['bloques']) ? 1 : 0;
$pasosCompletados += !empty($progreso['fixture']) ? 1 : 0;

$porcentaje = round(($pasosCompletados / $totalPasos) * 100);

$pasos = [
    'Datos generales'     => !empty($progreso['datos']),
    'Costos y reglas'     => !empty($progreso['costos']),
    'Equipos inscritos'   => !empty($progreso['equipos']),
    'Bloques del torneo'  => !empty($progreso['bloques']),
    'Fixture'             => !empty($progreso['fixture']),
];
?>

<div class="card mb-4 config-progress-card">
    <div class="card-body">

        <div class="d-flex justify-content-between align-items-center mb-2">
            <div>
                <h6 class="mb-0">Estado de configuración <i class="fas fa-gear"></i></h6>
                <small class="text-muted">
                    <?= $pasosCompletados ?> de <?= $totalPasos ?> pasos completados
                </small>
            </div>

            <span class="badge bg-primary">
                <?= $porcentaje ?>%
            </span>
        </div>

        <div class="progress mb-3 config-progress-bar">
            <div class="progress-bar bg-primary" style="width: <?= $porcentaje ?>%;"></div>
        </div>

        <ul class="list-group list-group-flush config-step-list">
            <?php foreach ($pasos as $nombre => $completo): ?>
                <li class="list-group-item config-step-item">
                    <span>
                        <?= $completo ? '✅' : '⏳' ?>
                        <?= $nombre ?>
                    </span>

                    <?php if ($completo): ?>
                        <span class="badge bg-success">Completado</span>
                    <?php else: ?>
                        <span class="badge bg-warning text-dark">Pendiente</span>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>

            <li class="list-group-item config-step-item">
                <span>
                    <?= !empty($progreso['activado']) ? '🚀' : '⛔' ?>
                    Activación
                </span>

                <?php if (!empty($progreso['activado'])): ?>
                    <span class="badge bg-success">Activo</span>
                <?php else: ?>
                    <span class="badge bg-secondary">Bloqueado</span>
                <?php endif; ?>
            </li>
        </ul>

    </div>
</div>

<style>
    .config-progress-card .card-body {
    padding: 14px 16px;
}

.config-progress-bar {
    height: 7px;
}

.config-step-list .config-step-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 7px 10px;
    font-size: 13px;
}

.config-step-item .badge {
    min-width: 90px;
    font-size: 11px;
    padding: 5px 8px;
}
</style>