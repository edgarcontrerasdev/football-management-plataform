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
            <div class="kpi-card <?= $kpi['tipo']; ?>">
                <span class="kpi-title">
                    <?= htmlspecialchars($kpi['titulo']); ?>
                </span>

                <h3>
                    <?= htmlspecialchars($kpi['valor']); ?>
                </h3>

                <i class="<?= htmlspecialchars($kpi['icono']); ?>"></i>
            </div>
        </div>
    <?php endforeach; ?>

</div>

<style>

    .kpi-card.primary {
    border-left: 3px solid var(--primary);
}

.kpi-card.info {
    border-left: 3px solid var(--info);
}
</style>