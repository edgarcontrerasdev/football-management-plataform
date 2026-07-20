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

<style>
    /* ==========================================
   TIMELINE TORNEO V2
========================================== */

.torneo-timeline-card{
    border-left:4px solid var(--primary);
        padding:20px;

}

.torneo-timeline-card .card-body{
}

.torneo-timeline{
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    width:100%;
    margin-top:15px;
}

.timeline-step{
    flex:1;
    text-align:center;
    position:relative;
}

.timeline-circle{
    width:54px;
    height:54px;
    border-radius:50%;
    margin:0 auto 15px;

    display:flex;
    align-items:center;
    justify-content:center;

    font-size:18px;
    font-weight:700;

    border:3px solid #cbd5e1;

    background:#f8fafc;
    color:#64748b;
}

.timeline-text strong{
    display:block;
    font-size:14px;
    font-weight:600;
}

.timeline-text span{
    display:block;
    font-size:12px;
    color:#6b7280;
}

.timeline-line{
    flex:1;
    height:5px;

    background:#dbe2ea;

    margin-top:24px;
    margin-left:10px;
    margin-right:10px;

    border-radius:20px;
}

/* COMPLETADO */

.timeline-step.completo .timeline-circle{
    background:#198754;
    border-color:#198754;
    color:#fff;
}

/* ACTUAL */

.timeline-step.actual .timeline-circle{
    background:#1E40AF;
    border-color:#1E40AF;
    color:#fff;

    box-shadow:
        0 0 0 6px rgba(30,64,175,.15);
}

.timeline-step.actual .timeline-text strong{
    color:#1E40AF;
}

/* PENDIENTE */

.timeline-step.pendiente .timeline-circle{
    background:#fff;
    border-color:#f59e0b;
    color:#f59e0b;
}

/* BLOQUEADO */

.timeline-step.bloqueado .timeline-circle{
    background:#6c757d;
    border-color:#6c757d;
    color:#fff;
}
.timeline-status {
    display: inline-block;
    margin-top: 3px;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .4px;
}

.timeline-status.success {
    color: var(--success);
}

.timeline-status.current {
    color: var(--primary);
}

.timeline-status.pending {
    color: #f59e0b;
}

.timeline-status.blocked {
    color: #6c757d;
}

</style>