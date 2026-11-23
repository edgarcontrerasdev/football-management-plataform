<div class="torneos-page">
    <div class="admin-page-header mb-4">
        <div>
            <h3 class="admin-page-title mb-1"><i class="fas fa-trophy me-2"></i>Panel de torneos</h3>
            <p class="admin-page-description mb-0">Administración de competencias y seguimiento de torneos</p>
        </div>
        <span class="badge-season"><?= htmlspecialchars($temporadaActiva['temporada'] ?? 'Sin temporada activa'); ?></span>
    </div>

    <div class="row g-3 mb-4 justify-content-start">
        <?php foreach($kpis as $kpi): ?>
            <div class="col-12 col-sm-6 col-lg-3">
                <?php require 'views/components/kpis.php' ?>
            </div>
        <?php endforeach; ?>
    </div>

    <?php include ROOT_PATH.'/admin/views/components/table-toolbox.php'; ?>
    <?php include ROOT_PATH. '/admin/views/components/table-content.php'; ?>
</div>

<?php startSection('scripts') ?>
<script src="<?= ASSETS ?>/js/admin/torneos/torneo-list.js"></script>
<?php endSection('scripts') ?>
