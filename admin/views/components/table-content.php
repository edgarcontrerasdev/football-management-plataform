<div id="table-wrapper">
    <div class="table-panel">
        <div class="table-panel-heading">
            <i class="fas fa-list" aria-hidden="true"></i>
            <span><?= htmlspecialchars($tableTitle ?? 'Listado', ENT_QUOTES, 'UTF-8') ?></span>
        </div>
        <?php include ROOT_PATH.'/admin/views/components/table.php'; ?>
    </div>

    <?php if(isset($pagination)): ?>
        <?php
        $url_pagination = ROOT_PATH.'/admin/views/components/table-pagination.php';
        if(!file_exists($url_pagination)){
            echo '<p>Error al cargar componente de paginacion</p>';
            exit;
        }
        include $url_pagination;
        ?>
    <?php endif; ?>
</div>
