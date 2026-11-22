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



<script>

    document.addEventListener('DOMContentLoaded',function(e){
        const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');

        popoverTriggerList.forEach(function (el) {
            new bootstrap.Popover(el);
        });
    });

    document.addEventListener('click', function(e){
        const btn = e.target.closest('.btn-cerrar');
        
        if(!btn) return;

        e.preventDefault();

        const url = btn.getAttribute('href');

        Swal.fire({
            icon: 'warning',
            title: '¿Deseas cerrar definitivamente la temporada?',
            html: `
                    <p class="mb-3">
                        La temporada cambiará al estado
                        <strong>Cerrada</strong>.
                    </p>
                    <div class="alert alert-warning text-start mb-0">
                        <i class="fa-solid fa-circle-exclamation me-2"></i>
                        Antes de continuar, confirma que:
                        <ul class="mt-2 mb-0">
                            <li>Todos los torneos han concluido.</li>
                            <li>No existen partidos pendientes.</li>
                            <li>Las estadísticas y sanciones están actualizadas.</li>
                            <li>La temporada está lista para archivarse.</li>
                        </ul>
                    </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Sí, finalizar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#0033ff'
        }).then((result)=>{
            if(result.isConfirmed){
                window.location.href = url;
            }
        });

    });


    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-activar');

        if (!btn) return;

        e.preventDefault();

        const url = btn.getAttribute('href');

        Swal.fire({
            icon: 'warning',
            title: '¿Activar temporada?',
            html: 'Al activar esta temporada, la temporada activa actual pasará a estado <b>Finalizada</b>.',
            showCancelButton: true,
            confirmButtonText: 'Sí, activar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#0033ff'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    });

    
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-delete');

        if (!btn) return;

        e.preventDefault();

        const url = btn.getAttribute('href');

        Swal.fire({
            icon: 'warning',
            title: '¿Eliminar temporada?',
            html: 'Al eliminar se perderan los datos previamente guardados.',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#0033ff'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    });

    document.addEventListener('click', function(e){
        const btn = e.target.closest('.btn-finalizar');

        if(!btn) return;
        e.preventDefault();
        const url = btn.getAttribute('href');
        Swal.fire({
            icon:               'warning',
            title:              '¿Finalizar temporada?',
            html:               'Al finalizar la temporada pasara de <b>ACTIVA</b> </br> a estado <b>FINALIZADO</> lo cual permitirá concluir procesos pendientes pero ya no permitirá </br> crear nuevos torneos ni afiliar a esta temporada.',
            showCancelbutton:   'true',
            confirmButtonText:  'Sí, finalizar',
            cancelButtonText:   'Cancelar',
            confirmButtonColor: '#0033ff'
        }).then((result) =>{
            if(result.isConfirmed){
                window.location.href = url;
            }
        });
    });

</div>

</script>