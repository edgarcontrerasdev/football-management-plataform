<?php
$pasosTimeline = [
    [
        'numero' => 1,
        'titulo' => 'Datos',
        'descripcion' => 'Generales',
        'completo' => !empty($progreso['datos']),
        'bloqueado' => false,
    ],
    [
        'numero' => 2,
        'titulo' => 'Costos',
        'descripcion' => 'Reglas',
        'completo' => !empty($progreso['costos']),
        'bloqueado' => false,
    ],
    [
        'numero' => 3,
        'titulo' => 'Equipos',
        'descripcion' => 'Inscritos',
        'completo' => !empty($progreso['equipos']),
        'bloqueado' => false,
    ],
    [
        'numero' => 4,
        'titulo' => 'Bloques',
        'descripcion' => 'Estructura',
        'completo' => !empty($progreso['bloques']),
        'bloqueado' => false,
    ],
    [
        'numero' => 5,
        'titulo' => 'Fixture',
        'descripcion' => 'Calendario',
        'completo' => !empty($progreso['fixture']),
        'bloqueado' => empty($progreso['bloques']),
    ],
    [
        'numero' => 6,
        'titulo' => 'Activar',
        'descripcion' => 'Torneo',
        'completo' => !empty($progreso['activado']),
        'bloqueado' => empty($progreso['datos']) ||
                      empty($progreso['costos']) ||
                      empty($progreso['equipos']) ||
                      empty($progreso['bloques']) ||
                      empty($progreso['fixture']),
    ],
];

$pasoActual = null;

foreach ($pasosTimeline as $paso) {
    if (!$paso['completo'] && !$paso['bloqueado']) {
        $pasoActual = $paso['numero'];
        break;
    }
}
?>

<div class="card mb-4 torneo-timeline-card">
    <div class="card-body">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h6 class="mb-0">
                    Ruta de configuración
                </h6>
                <small class="text-muted">
                    Sigue los pasos para dejar listo el torneo
                </small>
            </div>

            <span class="badge bg-primary">
                Paso <?= $pasoActual ?? 6 ?> de 6
            </span>
        </div>

        <div class="torneo-timeline">

            <?php foreach ($pasosTimeline as $index => $paso): ?>

                <?php
                    $clase = 'pendiente';

                    if ($paso['completo']) {
                        $clase = 'completo';
                    } elseif ($paso['bloqueado']) {
                        $clase = 'bloqueado';
                    } elseif ($pasoActual === $paso['numero']) {
                        $clase = 'actual';
                    }
                ?>

                <div class="timeline-step <?= $clase ?>">

                    <div class="timeline-circle">
                        <?php if ($paso['completo']): ?>
                            <i class="fas fa-check"></i>
                        <?php elseif ($paso['bloqueado']): ?>
                            <i class="fas fa-lock"></i>
                        <?php else: ?>
                            <?= $paso['numero'] ?>
                        <?php endif; ?>
                    </div>

                    <div class="timeline-text">
                        <strong><?= htmlspecialchars($paso['titulo']) ?></strong>

                        <?php if ($paso['completo']): ?>
                            <span class="timeline-status success">Completado</span>
                        <?php elseif ($paso['bloqueado']): ?>
                            <span class="timeline-status blocked">Bloqueado</span>
                        <?php elseif ($pasoActual === $paso['numero']): ?>
                            <span class="timeline-status current">Paso actual</span>
                        <?php else: ?>
                            <span class="timeline-status pending">Pendiente</span>
                        <?php endif; ?>
                    </div>

                </div>

                <?php if ($index < count($pasosTimeline) - 1): ?>
                    <div class="timeline-line"></div>
                <?php endif; ?>

            <?php endforeach; ?>

        </div>

    </div>
</div>

