<?php
$title = htmlspecialchars($kpi['title'] ?? '', ENT_QUOTES, 'UTF-8');
$value = htmlspecialchars($kpi['value'] ?? 0, ENT_QUOTES, 'UTF-8');
$icon  = htmlspecialchars($kpi['icon'] ?? 'fa-solid fa-chart-line', ENT_QUOTES, 'UTF-8');
$class = htmlspecialchars($kpi['class'] ?? '', ENT_QUOTES, 'UTF-8');
?>

<div class="kpi-card <?= $class ?>">
    <div>
        <span class="kpi-title"><?= $title ?></span>
        <h3><?= $value ?></h3>
    </div>

    <i class="<?= $icon ?>"></i>
</div>