
<?php

    $logo = !empty($liga['logo']) ? '/afec/public/assets/img/ligas/' . $liga['logo'] : '/afec/public/assets/img/ligas/default.png';

    $direccionPartes = [];

    if (!empty($liga['direccion'])) {
        $direccionPartes[] = $liga['direccion'];
    }

    if (!empty($liga['colonia'])) {
        $direccionPartes[] = $liga['colonia'];
    }

    if (!empty($liga['codigo_postal'])) {
        $direccionPartes[] = $liga['codigo_postal'];
    }

    if (!empty($liga['municipio'])) {
        $direccionPartes[] = $liga['municipio'];
    }

    if (!empty($liga['estado_republica'])) {
        $direccionPartes[] = $liga['estado_republica'];
    }

    $direccionTexto = implode(', ', $direccionPartes);
    $estado = strtolower((string) ($liga['estado'] ?? ''));
    $estadoTexto = match ($estado) {
        'activo'   => 'Activa',
        'inactivo' => 'Inactiva',
        default    => ucfirst($estado ?: 'Sin estado')
    };

    $estadoClase = match ($estado) {
        'activo'   => 'success',
        'inactivo' => 'secondary',
        default    => 'secondary'
    };
?>

<?php
    require ROOT_PATH . '/admin/views/pages/ligas/partials/cabecera.php';
    require ROOT_PATH . '/admin/views/pages/ligas/partials/temporada-actual.php';
?>

<?php if (!empty($kpis)): ?>

    <div class="row g-3 mb-4">
        <?php foreach ($kpis as $kpi): ?>
            <div class="col-12 col-sm-6 col-lg-3">
                <?php require ROOT_PATH . '/admin/views/components/kpis.php'; ?>
            </div>
        <?php endforeach; ?>
    </div>

<?php endif; ?>

    <div class="card liga-tabs-card shadow-sm border-0">
        <div class="card-header bg-white">
            <?php require ROOT_PATH . '/admin/views/pages/ligas/tabs/navegacion.php'; ?>
        </div>

        <div class="card-body">
            <div class="tab-content">
                <div class="tab-pane fade show active" id="info">
                    <?php require ROOT_PATH . '/admin/views/pages/ligas/tabs/informacion.php'; ?>
                </div>

                <div class="tab-pane fade" id="temporada">
                    <?php require ROOT_PATH . '/admin/views/pages/ligas/tabs/temporada.php'; ?>
                </div>

                <div class="tab-pane fade" id="equipos">
                    <?php require ROOT_PATH . '/admin/views/pages/ligas/tabs/equipos.php'; ?>
                </div>

                <div class="tab-pane fade" id="documentos">
                    <?php require ROOT_PATH . '/admin/views/pages/ligas/tabs/documentos.php'; ?>
                </div>

                <div class="tab-pane fade" id="pagos">
                    <?php require ROOT_PATH . '/admin/views/pages/ligas/tabs/pagos.php'; ?>
                </div>

                <div class="tab-pane fade" id="deportivo">
                    <?php require ROOT_PATH . '/admin/views/pages/ligas/tabs/deportivo.php'; ?>
                </div>
            </div>
        </div>
        
    </div>
