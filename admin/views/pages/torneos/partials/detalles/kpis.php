<?php
$estadoTorneo = strtoupper((string) ($torneo['estado'] ?? ''));
$kpisTorneo = [
    [
        'titulo' => 'Equipos inscritos',
        'valor'  => (int) ($totalEquiposInscritos ?? 0),
        'icono'  => 'fas fa-users',
        'tipo'   => 'info'
    ],
    [
        'titulo' => 'Bloques',
        'valor'  => !empty($progreso['bloques']) ? 'Configurados' : 'Pendientes',
        'icono'  => 'fas fa-layer-group',
        'tipo'   => !empty($progreso['bloques']) ? 'success' : 'warning'
    ],
    [
        'titulo' => 'Estado del torneo',
        'valor'  => $estadoTorneo !== '' ? ucfirst(strtolower($estadoTorneo)) : 'No definido',
        'icono'  => 'fas fa-flag-checkered',
        'tipo'   => 'primary'
    ],
];
?>
<div class="row g-3 mb-4">
    <?php foreach ($kpisTorneo as $kpi): ?>
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="kpi-card kpi-card--detail <?= htmlspecialchars($kpi['tipo'], ENT_QUOTES, 'UTF-8') ?>">
                <span class="kpi-title"><?= htmlspecialchars($kpi['titulo'], ENT_QUOTES, 'UTF-8') ?></span>
                <h3 class="kpi-value"><?= htmlspecialchars((string) $kpi['valor'], ENT_QUOTES, 'UTF-8') ?></h3>
                <i class="kpi-icon <?= htmlspecialchars($kpi['icono'], ENT_QUOTES, 'UTF-8') ?>"></i>
            </div>
        </div>
    <?php endforeach; ?>
</div>
