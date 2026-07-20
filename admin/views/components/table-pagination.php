<?php if(isset($pagination) && $pagination['pages'] > 1): ?>

<?php
$params = $_GET;

$current = $pagination['page'];
$total   = $pagination['pages'];

$range = 2;

$start = max(1, $current - $range);
$end   = min($total, $current + $range);
?>

<div id="paginacion">

    <!-- IZQUIERDA -->
    <div class="paginacion-left">
        <span class="info-registros">
            Página <?= $current ?> de <?= $total ?>
        </span>
    </div>

    <!-- CENTRO -->
    <div class="paginacion-center">

        <!-- PRIMERA -->
        <?php if($current > 1): ?>
            <?php $params['p'] = 1; ?>
            <a class="btn btn-nav" href="?<?= http_build_query($params) ?>">
                <i class="fas fa-angle-double-left fa-fw" > </i> Primera
            </a>
        <?php endif; ?>

        <!-- ANTERIOR -->
        <?php if($current > 1): ?>
            <?php $params['p'] = $current-1; ?>
            <a class="btn btn-nav" href="?<?= http_build_query($params) ?>">
                <i class="fas fa-angle-left fa-fw"></i> Atras
            </a>
        <?php endif; ?>

        <!-- PAGINAS -->
        <?php for($i=$start;$i<=$end;$i++): ?>

            <?php $params['p'] = $i; ?>

            <a class="btn btn-page <?= $current==$i ? 'active':'' ?>" href="?<?= http_build_query($params) ?>">
                <?= $i ?>
            </a>

        <?php endfor; ?>

        <!-- SIGUIENTE -->
        <?php if($current < $total): ?>
            <?php $params['p'] = $current+1; ?>
            <a class="btn btn-nav" href="?<?= http_build_query($params) ?>">
                Siguiente <i class="fas fa-angle-right fa-fw"></i>
            </a>
        <?php endif; ?>

        <!-- ULTIMA -->
        <?php if($current < $total): ?>
            <?php $params['p'] = $total; ?>
            <a class="btn btn-nav" href="?<?= http_build_query($params) ?>">
                Ultima <i class="fas fa-angle-double-right fa-fw"></i>
            </a>
        <?php endif; ?>

    </div>

    <div class="paginacion-right">
        Mostrar:
        <select name="" id=""><option value="10">10</option></select>
    </div>

</div>

<?php endif; ?>