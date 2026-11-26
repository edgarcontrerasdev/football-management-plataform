
<div class="ligas-index-page">
     <!-- CABEZERA DE LA VISTA --> 

    <div class="admin-page-header mb-4">
        <div>
            <h3 class="admin-page-title mb-1"><?= Auth::role() === 'ORGANISMO' ? 'Ligas que administra' : 'Ligas' ?></h3>
            <p class="admin-page-description mb-0">
                <?= Auth::role() === 'ORGANISMO' ? 'Gestión de las ligas bajo la administración de su organismo' : 'Catálogo y administración de ligas' ?>
            </p>
        </div>
    </div>
    
    <!-- KPIS CON INFO RELEVANTE -->

    <div class="row g-3 mb-4 justify-content-start">
        <?php foreach ($kpis as $kpi): ?>
            <div class="col-12 col-sm-6 col-lg-4">
                <?php require 'views/components/kpis.php'; ?>
            </div>
        <?php endforeach; ?>
    </div>


    <?php include ROOT_PATH.'/admin/views/components/table-toolbox.php'; ?>


    <?php include ROOT_PATH. '/admin/views/components/table-content.php'; ?>

</div>


