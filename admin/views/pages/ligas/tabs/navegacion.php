<?php
$tabsLiga = [
    ['id' => 'info', 'icon' => 'bi-info-circle', 'label' => 'Información'],
    ['id' => 'temporada', 'icon' => 'bi-calendar3', 'label' => 'Temporada'],
    ['id' => 'equipos', 'icon' => 'bi-people', 'label' => 'Equipos'],
    ['id' => 'documentos', 'icon' => 'bi-folder', 'label' => 'Documentos'],
    ['id' => 'pagos', 'icon' => 'bi-cash-coin', 'label' => 'Pagos'],
    ['id' => 'deportivo', 'icon' => 'bi-trophy', 'label' => 'Deportivo'],
    ['id' => 'historial', 'icon' => 'bi-clock-history', 'label' => 'Historial'],
];
?>
<ul class="nav nav-tabs card-header-tabs flex-wrap">
    <?php foreach ($tabsLiga as $indice => $tab): ?>
        <li class="nav-item">
            <button
                class="nav-link <?= $indice === 0 ? 'active' : '' ?>"
                type="button"
                data-bs-toggle="tab"
                data-bs-target="#<?= htmlspecialchars($tab['id'], ENT_QUOTES, 'UTF-8') ?>"
                aria-controls="<?= htmlspecialchars($tab['id'], ENT_QUOTES, 'UTF-8') ?>"
                aria-selected="<?= $indice === 0 ? 'true' : 'false' ?>">
                <i class="bi <?= htmlspecialchars($tab['icon'], ENT_QUOTES, 'UTF-8') ?>"></i>
                <?= htmlspecialchars($tab['label'], ENT_QUOTES, 'UTF-8') ?>
            </button>
        </li>
    <?php endforeach; ?>
</ul>