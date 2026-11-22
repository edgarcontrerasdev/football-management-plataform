<div class="temporadas-index-page">

    <h3 class="users-title">
        Listado de Temporadas
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

                    <span class='popover-subtitle'>
                        Flujo de la temporada
                    </span>

                    <div class='text-center mb-3'>

                        <div><strong>📝 Planeada</strong></div>

                        <div class='flow-arrow'>
                            <div class='flow-line'></div>
                            <i class='fa-solid fa-caret-down'></i>
                        </div>

                        <div><strong>🟢 Activa</strong></div>

                        <div class='flow-arrow'>
                            <div class='flow-line'></div>
                            <i class='fa-solid fa-caret-down'></i>
                        </div>

                        <div><strong>🟡 Finalizada</strong></div>

                        <div class='flow-arrow'>
                            <div class='flow-line'></div>
                            <i class='fa-solid fa-caret-down'></i>
                        </div>

                        <div><strong>🔴 Cerrada</strong></div>

                    </div>

                    <hr class='my-2'>

                    <span class='popover-subtitle'>
                        Descripción de los estados
                    </span>

                    <div class='mb-2'>
                        <strong>📝 Planeada</strong><br>
                        La temporada se encuentra en preparación y puede configurarse libremente.
                    </div>

                    <div class='mb-2'>
                        <strong>🟢 Activa</strong><br>
                        Es la temporada vigente donde se desarrollan los torneos y la actividad deportiva.
                    </div>

                    <div class='mb-2'>
                        <strong>🟡 Finalizada</strong><br>
                        La competencia concluyó, pero aún pueden realizarse procesos administrativos.
                    </div>

                    <div>
                        <strong>🔴 Cerrada</strong><br>
                        La temporada queda archivada y disponible únicamente para consulta.
                    </div>

                </div>
                <hr class='my-2'>

                <div class='text-muted small'>
                    <i class='fa-solid fa-lightbulb text-warning me-1'></i>

                    <strong>Importante:</strong><br>

                    Al activar una nueva temporada, la temporada activa anterior cambia automáticamente a
                    <strong>Finalizada</strong>. El estado
                    <strong>Cerrada</strong> debe establecerse únicamente cuando todos los procesos administrativos hayan concluido.
                </div>
                            
            ">
        </i>
    </h3>

    <!-- KPIS CON INFO RELEVANTE -->
    <div class="row g-3 mb-4 justify-content-start">

        <div class="col-md-3">
            <div class="kpi-card success">
                <span class="kpi-title">TEMPORADA ACTIVA: TEMPORADA 2025-2026</span>
                <h3></h3>
                <i class="kpi-icon bi bi-calendar"></i>
            </div>
        </div>

        <div class="col-md-3">
            <div class="kpi-card info">
                <span class="kpi-title">Cuerpo Tecnico Registrados</span>
                <h3 class="kpi-value"><?= $kpis['dt'] ?? 0 ?></h3>
                <i class="kpi-icon bi bi-person-badge"></i>
            </div>
        </div>

        <div class="col-md-3">
            <div class="kpi-card warning">
                <span class="kpi-title">CREDENCIAELES PENDIENTES</span>
                <h3 class="kpi-value"><?= $kpis['pendientes'] ?? 80 ?></h3>
                <i class="kpi-icon bi bi-clock"></i>
            </div>
        </div>

        <div class="col-md-3">
            <div class="kpi-card danger">
                <span class="kpi-title">AFILIACIONES RECHAZADAS</span>
                <h3 class="kpi-value"><?= $kpis['rechazados'] ?? 25 ?></h3>
                <i class="kpi-icon bi bi-x-circle"></i>
            </div>
        </div>

    </div>

    <?php include ROOT_PATH.'/admin/views/components/table-toolbox.php'; ?>

    <?php include ROOT_PATH. '/admin/views/components/table-content.php'; ?>



</div>

<?php startSection('scripts') ?>
<script src="<?= BASE_URL ?>/public/assets/js/admin/temporadas/temporada-list.js"></script>
<?php endSection('scripts') ?>

