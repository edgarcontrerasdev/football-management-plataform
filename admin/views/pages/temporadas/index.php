<div class="temporadas-index-page">

    <div class="admin-page-header mb-4">
        <div>
            <h3 class="admin-page-title mb-1">
                Listado de temporadas
                <i class="fa-solid fa-circle-info text-primary ms-2"
                        role="button"
                        tabindex="0"
                        data-bs-toggle="popover"
                        data-bs-trigger="focus"
                        data-bs-placement="right"
                        data-bs-html="true"
                        data-bs-title="Ciclo de vida de una temporada"
                        data-bs-content="
                        <div>
                            <span class='popover-subtitle'>Flujo de la temporada</span>
                            <div class='text-center mb-3'>
                                <div><strong>📝 Planeada</strong></div>
                                <div class='flow-arrow'><div class='flow-line'></div><i class='fa-solid fa-caret-down'></i></div>
                                <div><strong>🟢 Activa</strong></div>
                                <div class='flow-arrow'><div class='flow-line'></div><i class='fa-solid fa-caret-down'></i></div>
                                <div><strong>🟡 Finalizada</strong></div>
                                <div class='flow-arrow'><div class='flow-line'></div><i class='fa-solid fa-caret-down'></i></div>
                                <div><strong>🔴 Cerrada</strong></div>
                            </div>
                            <hr class='my-2'>
                            <span class='popover-subtitle'>Descripción de los estados</span>
                            <div class='mb-2'><strong>📝 Planeada</strong><br>La temporada se encuentra en preparación y puede configurarse libremente.</div>
                            <div class='mb-2'><strong>🟢 Activa</strong><br>Es la temporada vigente donde se desarrollan los torneos y la actividad deportiva.</div>
                            <div class='mb-2'><strong>🟡 Finalizada</strong><br>La competencia concluyó, pero aún pueden realizarse procesos administrativos.</div>
                            <div><strong>🔴 Cerrada</strong><br>La temporada queda archivada y disponible únicamente para consulta.</div>
                        </div>
                        <hr class='my-2'>
                        <div class='text-muted small'>
                            <i class='fa-solid fa-lightbulb text-warning me-1'></i>
                            <strong>Importante:</strong><br>
                            Al activar una nueva temporada, la temporada activa anterior cambia automáticamente a <strong>Finalizada</strong>. El estado <strong>Cerrada</strong> debe establecerse únicamente cuando todos los procesos administrativos hayan concluido.
                        </div>
                        ">
            </i>
            </h3>
            <p class="admin-page-description mb-0">Control de ciclos deportivos y estados de temporada</p>
        </div>
    </div>

    <?php include ROOT_PATH.'/admin/views/components/table-toolbox.php'; ?>
    <?php include ROOT_PATH. '/admin/views/components/table-content.php'; ?>
</div>

<?php startSection('scripts') ?>
<script src="<?= BASE_URL ?>/public/assets/js/admin/temporadas/temporada-list.js"></script>
<?php endSection('scripts') ?>
