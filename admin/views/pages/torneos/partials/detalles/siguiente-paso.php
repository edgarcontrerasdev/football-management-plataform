<?php
$pasoNumero = 1;
$pasoTitulo = 'Configuración completa';
$pasoDescripcion = 'El torneo está listo para activarse.';
$pasoIcono = 'fas fa-check-circle';
$pasoAccion = null;

if (empty($progreso['equipos'])) {
    $pasoNumero = 2;
    $pasoTitulo = 'Registrar equipos participantes';
    $pasoDescripcion = 'Selecciona los equipos activos de la liga que participarán en este torneo.';
    $pasoIcono = 'fas fa-users';
    $pasoAccion = 'equipos';
} elseif (empty($progreso['bloques'])) {
    $pasoNumero = 3;
    $pasoTitulo = 'Configurar bloques del torneo';
    $pasoDescripcion = 'Define la estructura del torneo: temporada regular, liguilla, grupos u otras fases.';
    $pasoIcono = 'fas fa-layer-group';
    $pasoAccion = 'bloques';
} elseif (empty($progreso['fixture'])) {
    $pasoNumero = 4;
    $pasoTitulo = 'Generar calendario de partidos';
    $pasoDescripcion = 'Crea el fixture del torneo con base en los equipos y bloques configurados.';
    $pasoIcono = 'fas fa-calendar-alt';
    $pasoAccion = 'fixture';
}
?>

<div class="card mb-4 siguiente-paso-card">
    <div class="card-body d-flex justify-content-between align-items-center gap-3">

        <div class="d-flex align-items-center gap-3">
            <div class="siguiente-paso-icon">
                <i class="<?= $pasoIcono ?>"></i>
            </div>

            <div>
                <span class="siguiente-paso-label">
                    Paso <?= $pasoNumero ?> de 5
                </span>

                <h5 class="mb-1">
                    <?= htmlspecialchars($pasoTitulo) ?>
                </h5>

                <small class="text-muted">
                    <?= htmlspecialchars($pasoDescripcion) ?>
                </small>

            </div>

        </div>

        <div>
            <?php if ($pasoAccion === 'equipos'): ?>
                <button class="btn btn-primary btnAbrirEquipos" data-torneo="<?= $torneo['id'] ?>">
                    Inscribir equipos
                </button>

            <?php elseif ($pasoAccion === 'bloques'): ?>
                <a href="/admin/torneos/<?= $torneo['id'] ?>/bloques"
                   class="btn btn-primary">
                    Configurar bloques
                </a>

            <?php elseif ($pasoAccion === 'fixture'): ?>
                <a href="/admin/torneos/<?= $torneo['id'] ?>/fixture"
                   class="btn btn-primary">
                    Generar fixture
                </a>

            <?php else: ?>
                <button class="btn btn-success">
                    Activar torneo
                </button>
            <?php endif; ?>
        </div>

    </div>
</div>

