
<div id="table-wrapper">

    <div class="row card shadow-sm">
        <div class="card-body p-0">
            <div class="mb-3 mt-3">
                <i class="fas fa-list"></i>
                <span>Listado de equipos</span>
            </div>
            
            <?php include ROOT_PATH.'/admin/views/components/table.php'; ?>

        </div>
    </div>

    <?php if(isset($pagination)): ?>
        <div>
            
            <?php 
            $url_pagination = ROOT_PATH.'/admin/views/components/table-pagination.php';
            if(!file_exists($url_pagination)){
                echo '<p>Error al cargar componente de paginacion</p>';
                exit;
            }
            include $url_pagination; 
            ?>
        </div>
    <?php endif; ?>

</div>