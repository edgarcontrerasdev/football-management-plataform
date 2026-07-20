
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
                <i class="bi bi-calendar"></i>
            </div>
        </div>

        <div class="col-md-3">
            <div class="kpi-card info">
                <span class="kpi-title">Cuerpo Tecnico Registrados</span>
                <h3><?= $kpis['dt'] ?? 0 ?></h3>
                <i class="bi bi-person-badge"></i>
            </div>
        </div>

        <div class="col-md-3">
            <div class="kpi-card warning">
                <span class="kpi-title">CREDENCIAELES PENDIENTES</span>
                <h3><?= $kpis['pendientes'] ?? 80 ?></h3>
                <i class="bi bi-clock"></i>
            </div>
        </div>

        <div class="col-md-3">
            <div class="kpi-card danger">
                <span class="kpi-title">AFILIACIONES RECHAZADAS</span>
                <h3><?= $kpis['rechazados'] ?? 25 ?></h3>
                <i class="bi bi-x-circle"></i>
            </div>
        </div>

    </div>

    <?php include ROOT_PATH.'/admin/views/components/table-toolbox.php'; ?>

    <?php include ROOT_PATH. '/admin/views/components/table-content.php'; ?>

<style>

    :root{
        /* 🎨 Colores principales */
        --primary: #1e40af;        /* Azul royal */
        --primary-dark: #1e3a8a;
        --primary-light: #3b82f6;

        /* Estados */
        --success: #16a34a;
        --danger:  #dc2626;
        --warning: #f59e0b;
        --info:    #0ea5e9;

        /* 🧱 Fondo y superficies */
        --bg-white:      #f8fafc;        /* Fondo general del sistema */
        --card:    #ffffff;        /* Cards, tablas, modales */
        --border:  #e5e7eb;

        /* 📝 Texto */
        --text:        #1f2937;
        --text-muted: #6b7280;
    }

    body{
        background-color: var(--bg);
        color:var(--text);
    }
    
    .btn{
        padding: 6px 14px;
        border-radius: 6px;
        font-size: 14px;
        border: none;
        cursor: pointer;
    }

    .btn-primary{
        background: var(--primary);
        color: #fff;
    }

    .btn-secondary{
        background: var(--secondary);
        color: #fff;
    }

    .btn-danger{
        background: var(--danger);
        color: #fff;
    }

    .btn-warning{
        background: var(--warning);
        color: #000;
    }

    .switch{
        position: relative;
        display: inline-block;
        width: 44px;
        height: 24px;
    }

    .switch input{
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider{
        position: absolute;
        cursor: pointer;
        inset: 0;
        background: #ccc;
        border-radius: 34px;
        transition: .3s;
    }

    .slider::before{
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background: white;
        border-radius: 50%;
        transition: .3s;
    }

    input:checked + .slider{
        background: #2ecc71;
    }

    input:checked + .slider::before{
        transform: translateX(20px);
    }

    table{
        table-layout: fixed;
        width:100%;
    }

    .status-label {
        font-weight: bold;
        padding: 2px 8px;
        border-radius: 12px;
        color: white;
        font-size: 0.9em;
    }

    .user-status{
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 90px;           /* 🔑 MISMO TAMAÑO */
        height: 28px;
        font-size: 12px;
        font-weight: 600;
        border-radius: 20px;
        color: #fff;
        letter-spacing: .5px;
        transition: all .25s ease;
    }

    .user-status.active{
        background: var(--success);
        box-shadow: 0 0 0 1px rgba(46,204,113,.5);
    }

    .user-status.inactive{
        background: var(--danger);
        box-shadow: 0 0 0 1px rgba(231,76,60,.5);
    }

    .btn-icon{
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        border: 1px solid transparent;
        transition: all .2s ease;
    }

    .btn-icon svg{
        width: 16px;
        height: 16px;
    }

    /* Edit */
    .btn-icon.edit{
        background: #f1f3f5;
        color: #2c3e50;
    }

    .btn-icon.edit:hover{
        background: var(--primary-dark);
        color: #fff;
    }

    .users-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: var(--bg-white);
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 10px;
        border: 1px solid var(--border-color);
    }

    .users-filters{
        display:flex;
        gap: 12px;;
    }

    .search-box{
        position:relative;
    }

    .search-box input{
        padding:8px;
        padding-left: 36px;
        border-radius: 10px;
        border:2px solid #6b7280;
        width: 400px;
    }

    .search-box i{
        position:absolute;
        left:12px;
        top:50%;
        transform: translateY(-50%);
        opacity: .6;
    }

    .btn-create{
        background: #0d6efd;
        color: #fff;
        border-radius: 8px;
        padding: 8px 14px;
        display: flex;
        gap: 6px;
        align-items: center;
    }


    .users-title{
        color: var(--warning);
        margin-bottom: 10px;
    }
        
    .user-cell {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .user-avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #e5e5e5;
    }

    .user-info {
        display: flex;
        flex-direction: column;
        line-height: 1.2;
    }

    .user-name {
        font-weight: 600;
        font-size: 14px;
        color: #212529;
    }

    .user-alias {
        font-size: 14px;
        color: #6c757d;
    }


    /* ================= PAGINACIÓN MODERNA ================= */

    #paginacion {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 15px;
        padding: 12px 16px;
        background: linear-gradient(135deg, #0f172a, #1f2937);
        border-radius: 14px;
        color: #e5e7eb;
        font-size: 14px;
    }

    /* Contenedor central */
    #paginacion .paginacion-center {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* ===== BOTÓN BASE ===== */
    #paginacion .btn {
        height: 38px;
        padding: 0 16px;
        border-radius: 999px; /* 🔥 PILL */
        border: none;
        background: rgba(255,255,255,0.08);
        color: #e5e7eb;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all .25s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* ===== BOTONES DE NAVEGACIÓN (‹ ›) ===== */
    #paginacion .btn-nav {
        min-width: 42px;
        font-size: 18px;
        font-weight: 600;
    }

    /* ===== BOTONES NUMÉRICOS ===== */
    #paginacion .btn-page {
        min-width: 38px;
    }

    /* ===== HOVER CON GRADIENTE ===== */
    #paginacion .btn:hover {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: #fff;
        box-shadow: 0 6px 16px rgba(59,130,246,.45);
        transform: translateY(-1px);
    }

    /* ===== ACTIVO ===== */
    #paginacion .btn.active {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        color: #fff;
        font-weight: 600;
        box-shadow: 0 6px 20px rgba(37,99,235,.55);
        transform: none;
    }

    /* ===== DESHABILITADO ===== */
    #paginacion .btn:disabled {
        background: rgba(255,255,255,0.04);
        color: #9ca3af;
        cursor: not-allowed;
        box-shadow: none;
        transform: none;
    }

    /* Texto informativo */
    #paginacion .info-registros {
        color: #cbd5e1;
    }

    /* Selector */
    #paginacion select {
        padding: 6px 10px;
        border-radius: 10px;
        border: none;
        background: #f8fafc;
        color: #1e40af;
        font-weight: 500;
    }

    .btn.disabled {
        opacity: 0.45;
        pointer-events: none;
        cursor: not-allowed;
    }

    /* Spinner */
    .spinner {
        border: 4px solid #f3f3f3;
        border-top: 4px solid #3498db;
        border-radius: 50%;
        width: 36px;
        height: 36px;
        animation: girar 1s linear infinite;
        margin: auto;
    }
    @keyframes girar { 0% { transform: rotate(0deg);} 100% { transform: rotate(360deg);} }

    .skeleton {
        display: inline-block;
        height: 16px;
        width: 100%;
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: shimmer 1.5s infinite;
    }
    
    @keyframes shimmer { 0% { background-position: -200% 0;} 100% { background-position: 200% 0;} }


   .popover{
    max-width:360px;
}

.popover-header{
    background:#0b3ea6;
    color:#fff;
    font-weight:600;
}

.popover-body{
    font-size:.90rem;
    line-height:1.5;
}

.popover-subtitle{
    display:block;
    font-size:.75rem;
    font-weight:700;
    text-transform:uppercase;
    color:#6c757d;
    letter-spacing:.5px;
    margin-bottom:8px;
}

.flow-arrow{
    display:flex;
    flex-direction:column;
    align-items:center;
    margin:3px 0;
}

.flow-line{
    width:2px;
    height:10px;
    background:#adb5bd;
}

.flow-arrow i{
    color:#6c757d;
    font-size:12px;
    margin-top:-2px;
}

</style>

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

</script>