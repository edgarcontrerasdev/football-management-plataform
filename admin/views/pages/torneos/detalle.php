<div class="torneo-detalle-page" data-torneo-id="<?= (int) $torneo['id'] ?>">

<div class="container-fluid py-4">

    <!-- HEADER -->
    <?php require __DIR__ . '/partials/detalles/header.php'; ?>

    <!-- kpis -->
    <?php require __DIR__ . '/partials/detalles/kpis.php'; ?>


    <!-- SIGUIENTE PASO RECOMENDADO -->
    <?php require __DIR__ . '/partials/detalles/siguiente-paso.php'; ?>

    <?php require __DIR__ . '/partials/detalles/time-line.php'; ?>

    <!-- PROGRESO -->

    <!-- MODULOS -->
    <?php require __DIR__ . '/partials/detalles/modulos.php'; ?>
</div>

<!-- modal para agregar equipos al torneo -->
<?php require __DIR__ . '/partials/detalles/modal-equipos.php'; ?>
<!-- modal para administrar bloques -->
<?php require __DIR__ . '/partials/detalles/modal-bloques.php'; ?>

<script src="<?= ASSETS ?>/js/admin/torneos/torneo-detail.js"></script>
</div>
