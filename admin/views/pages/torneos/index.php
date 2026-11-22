<div class="torneos-page">
<h3 class="users-title"><i class="fas fa-trophy"></i> PANEL DE TORNEOS </h3>

<span class="badge-season">  <?=  $temporadaActiva['temporada'];?> </span>


<!-- kpis -->
<div class="row g-3 mb-4 justify-content-start">
    <?php foreach($kpis as $kpi): ?>
        <div class="col-12 col-sm-6 col-lg-3">
            <?php require 'views/components/kpis.php' ?>
        </div>

<?php startSection('scripts') ?>
<script src="<?= ASSETS ?>/js/admin/torneos/torneo-list.js"></script>
<?php endSection('scripts') ?>
    <?php endforeach; ?>
</div>

<?php include ROOT_PATH.'/admin/views/components/table-toolbox.php'; ?>

<?php include ROOT_PATH. '/admin/views/components/table-content.php'; ?>
</div>
