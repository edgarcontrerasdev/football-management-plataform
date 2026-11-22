<?php
    // Asegurarnos de que $stats está definido
    $stats = $stats ?? [
        'equipos' => 0,
        'torneos' => 0,
        'jugadores' => 0,
        'partidos' => 0
    ];
?>
<div class="dashboard-page">
<div class="grid charts-grid">

    <div class="card">
        <h6>Equipos por liga</h6>
        <canvas id="chartEquiposLiga"></canvas>
    </div>

    <div class="card">
        <h6>Afiliaciones por rol</h6>
        <canvas id="chartAfiliaciones"></canvas>
    </div>

    <div class="card full">
        <h6>Partidos disputados por mes</h6>
        <canvas id="chartPartidosMes"></canvas>
    </div>

</div>
</div>





<?php startSection('scripts') ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="<?= BASE_URL ?>/public/assets/js/admin/dashboard/dashboard.js"></script>

<?php endSection('scripts') ?>
