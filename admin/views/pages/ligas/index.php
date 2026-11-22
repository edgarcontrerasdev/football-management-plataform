
<div class="ligas-index-page">
     <!-- CABEZERA DE LA VISTA --> 

    <div class="page-header mb-4">
        <div>
            <h3 class="page-title mb-1">Ligas</h3>
            <p class="page-details mb-0">
                Catálogo y administración de ligas
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


