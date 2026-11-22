<?php
$kpisTorneo = [
    [
        'titulo' => 'Equipos',
        'valor'  => '12 / 16',
        'icono'  => 'fas fa-users',
        'tipo'   => 'info'
    ],
    [
        'titulo' => 'Bloques',
        'valor'  => '2',
        'icono'  => 'fas fa-layer-group',
        'tipo'   => 'warning'
    ],
    [
        'titulo' => 'Partidos',
        'valor'  => '96',
        'icono'  => 'fas fa-futbol',
        'tipo'   => 'success'
    ],
    [
        'titulo' => 'Estado',
        'valor'  => 'Planeado',
        'icono'  => 'fas fa-flag-checkered',
        'tipo'   => 'primary'
    ],
];
?>

<div class="row g-3 mb-4">

    <?php foreach ($kpisTorneo as $kpi): ?>
        <div class="col-md-3">
            <div class="kpi-card kpi-card--detail <?= $kpi['tipo']; ?>">
                <span class="kpi-title">
                    <?= htmlspecialchars($kpi['titulo']); ?>
                </span>

                <h3 class="kpi-value">
                    <?= htmlspecialchars($kpi['valor']); ?>
                </h3>

                <i class="kpi-icon <?= htmlspecialchars($kpi['icono']); ?>"></i>
            </div>
        </div>
    <?php endforeach; ?>

</div>
