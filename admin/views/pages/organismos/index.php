<div class="organismos-index-page">
    <div class="admin-page-header mb-4">
        <div>
            <h3 class="admin-page-title mb-1">Organismos</h3>
            <p class="admin-page-description mb-0">Catálogo y administración de organismos deportivos</p>
        </div>
    </div>

    <div class="row g-3 mb-4 justify-content-start">
        <?php foreach ($kpis as $kpi): ?>
            <div class="col-12 col-sm-6 col-lg-4">
                <?php require ROOT_PATH . '/admin/views/components/kpis.php'; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <?php include ROOT_PATH . '/admin/views/components/table-toolbox.php'; ?>
    <?php include ROOT_PATH . '/admin/views/components/table-content.php'; ?>
</div>
