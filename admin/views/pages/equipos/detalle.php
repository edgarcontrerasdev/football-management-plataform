<?php
    $direccionCompleta = !empty($equipo['calle']) 
        && !empty($equipo['colonia']) 
        && !empty($equipo['codigo_postal']);

    $direccionTexto = '';

    if ($direccionCompleta) {
        $direccionTexto = trim(
            $equipo['calle']            . ', ' .
            $equipo['colonia']          . ', ' .
            $equipo['codigo_postal']    . ', ' .
            $equipo['ciudad']           . ', ' .
            $equipo['estado_direccion']
        );
    }


    $totalDocumentos = isset($documentos) ? count($documentos) : 0;
    $expedienteCompleto = $totalDocumentos > 0;

    $escudo = $equipo['escudo'] ? '/afec/public/assets/img/equipos/'.$equipo['escudo']: '/afec/public/assets/img/equipos/default.png';

?>

    <!-- ===== HEADER DEL EQUIPO ===== -->
    <div class="card header-dark shadow-sm border-0 mb-3 p-3">

        <div class="row align-items-center">

            <!-- 🔷 ESCUDO -->
            <div class="col-md-2 text-stretch">

                <div class="escudo-wrapper mx-auto">

                    <img id="escudoPreview" src="<?= $escudo ?>" class="escudo-img">

                    <div class="escudo-overlay">
                        <i class="fas fa-camera"></i>
                    </div>

                    <div class="escudo-actions d-none">
                        <button class="btn btn-success btn-sm" id="btnConfirmarEscudo">
                            <i class="fas fa-check"></i>
                        </button>
                        <button class="btn btn-danger btn-sm" id="btnCancelarEscudo">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <input type="file" id="inputEscudo" hidden>

            </div>

            <!-- 🔷 INFO -->
            <div class="col-md-6">

                <h3 class="fw-bold mb-1 text-white">
                    <?= $equipo['equipo'] ?>
                </h3>

                <div class="mb-2">

                    <span class="badge bg-success">
                        <?= $equipo['estado']; ?>
                    </span>

                    <span class="text-light small ms-2">
                        Liga: <?= $equipo['liga'] ?? 'Sin asignar' ?>
                    </span>

                </div>

                <div class="header-info small">

                    <?php if(!empty($direccionTexto)): ?>
                    <div>
                        <i class="bi bi-geo-alt"></i>
                        <span id="headerDir"><?= $direccionTexto ?> </span>
                    </div>
                    <?php endif; ?>

                    <?php if(!empty($equipo['contacto_email'])): ?>
                    <div>
                        <i class="bi bi-envelope"></i>
                        <?= $equipo['contacto_email'] ?>
                    </div>
                    <?php endif; ?>

                    <?php if(!empty($equipo['contacto_telefono'])): ?>
                    <div>
                        <i class="bi bi-telephone"></i>
                        <?= $equipo['contacto_telefono'] ?>
                    </div>
                    <?php endif; ?>

                </div>

            </div>

            <!-- 🔷 ACCIONES -->
            <div class="col-md-4 d-flex flex-column justify-content-between">

                <!-- 💰 SALDO (PRIORIDAD) -->
                <div class="saldo-box mb-3">

                    <div class="saldo-main">
                        <span class="saldo-label">Disponible</span>
                        <h3 class="saldo-cantidad">
                            $<?= number_format($saldo['disponible'] ?? 0, 2) ?>
                        </h3>
                    </div>

                    <div class="saldo-extra">

                        <div class="saldo-item">
                            <span>Total</span>
                            <strong>$<?= number_format($saldo['total'] ?? 0, 2) ?></strong>
                        </div>

                        <div class="saldo-item text-warning">
                            <span>Comprometido</span>
                            <strong>$<?= number_format($saldo['comprometido'] ?? 0, 2) ?></strong>
                        </div>

                    </div>

                </div>

                <!-- 🔘 BOTONES -->
                <div class="d-flex justify-content-end gap-2">

                    <button class="btn btn-outline-light btn-sm">
                        <i class="bi bi-pencil"></i> Editar
                    </button>

                    <a href="index.php?page=equipos&action=index"
                    class="btn btn-light btn-sm">
                        <i class="bi bi-arrow-left"></i> Regresar
                    </a>

                </div>

            </div>
        </div>

    </div>
              
    <!-- KPIS CON INFO RELEVANTE -->
    <div class="row g-3 mb-4">

        <div class="col-md-3">
            <div class="kpi-card success">
                <span class="kpi-title">Jugadores</span>
                <h3><?= $kpis['jugadores'] ?? 0 ?></h3>
                <i class="bi bi-people"></i>
            </div>
        </div>

        <div class="col-md-3">
            <div class="kpi-card info">
                <span class="kpi-title">Cuerpo Técnico</span>
                <h3><?= $kpis['dt'] ?? 0 ?></h3>
                <i class="bi bi-person-badge"></i>
            </div>
        </div>

        <div class="col-md-3">
            <div class="kpi-card warning">
                <span class="kpi-title">Pendientes</span>
                <h3><?= $kpis['pendientes'] ?? 0 ?></h3>
                <i class="bi bi-clock"></i>
            </div>
        </div>

        <div class="col-md-3">
            <div class="kpi-card danger">
                <span class="kpi-title">Rechazados</span>
                <h3><?= $kpis['rechazados'] ?? 0 ?></h3>
                <i class="bi bi-x-circle"></i>
            </div>
        </div>

    </div>

    <!-- TABS CON INFO DEL EQUIPO -->
    <div class="card shadow-sm border-0">

        <div class="card-header bg-white">
            <ul class="nav nav-tabs card-header-tabs">

                <li class="nav-item">
                    <button class="nav-link active"
                            data-bs-toggle="tab"
                            data-bs-target="#info">
                        <i class="bi bi-info"></i> Información
                    </button>
                </li>

                <li class="nav-item">
                    <button class="nav-link"
                            data-bs-toggle="tab"
                            data-bs-target="#plantilla">
                        <i class="bi bi-people"></i> Plantilla
                    </button>
                </li>

                <li class="nav-item">
                    <button class="nav-link"
                            data-bs-toggle="tab"
                            data-bs-target="#afiliaciones">
                        <i class="bi bi-person-badge"></i> Afiliaciones
                    </button>
                </li>

                <li class="nav-item">
                    <button class="nav-link"
                            data-bs-toggle="tab"
                            data-bs-target="#documentos">
                        <i class="bi bi-folder"></i> Documentos
                    </button>
                </li>

                <li class="nav-item">
                    <button class="nav-link"
                            data-bs-toggle="tab"
                            data-bs-target="#pagos">
                        <i class="bi bi-cash"></i> Pagos
                    </button>
                </li>

                <li class="nav-item">
                    <button class="nav-link"
                            data-bs-toggle="tab"
                            data-bs-target="#deportivo">
                        <i class="bi bi-trophy"></i> Deportivo
                    </button>
                </li>

            </ul>
        </div>

        <div class="card-body">

            <div class="tab-content">

                <div class="tab-pane fade show active" id="info">
                    <?php require_once ROOT_PATH.'/admin/views/pages/equipos/tabs/informacion.php'; ?>
                </div>

                <div class="tab-pane fade" id="plantilla">
                    <?php require_once ROOT_PATH.'/admin/views/pages/equipos/tabs/plantilla.php'; ?>
                </div>

                <div class="tab-pane fade" id="afiliaciones">
                    <?php require_once ROOT_PATH.'/admin/views/pages/equipos/tabs/afiliaciones.php'; ?>
                </div>

                <div class="tab-pane fade" id="documentos">
                    <?= 'Modulo de doucumentos del equipo' ?>
                </div>

                <div class="tab-pane fade" id="pagos">
                    <?=  "Estado de cuenta del equipo"; ?> 
                </div>

                <div class="tab-pane fade" id="deportivo">
                    <?php require_once ROOT_PATH.'/admin/views/pages/equipos/tabs/deportivo.php'; ?>
                </div>

            </div>

        </div>

    </div>

    <!-- AREA DE MODALES -->

    <?php 
    //modal informacion general
    require_once 'modals/editarGenerales.php';                   

    //modal editar contacto
    require_once 'modals/editarContacto.php';

    // modal editar direccion
    require_once 'modals/editarDireccion.php';

    //Modal buscar usuario
    require_once 'modals/buscarAfiliado.php'; 
    ?>

    <!-- FIN AREA DE MODALES -->


<?php startSection('scripts') ?>
    
    <script src="<?= ASSETS ?>/js/core/formHandler.js"  > </script>
    <script src="<?= ASSETS ?>/js/core/modalManager.js" > </script>
    <script src="<?= ASSETS ?>/js/equipos/direccion.js" > </script>
    <script src="<?= ASSETS ?>/js/equipos/contacto.js"  > </script>
    <script src="<?= ASSETS ?>/js/equipos/generales.js" > </script>
        
    <script>
        let escudoOriginal = document.getElementById('escudoPreview').src;
        let archivoEscudo = null;
        let bloqueado = false;

        document.querySelector('.escudo-overlay').addEventListener('click', () => {
            if (bloqueado) return;
            document.getElementById('inputEscudo').click();
        });

        document.getElementById('inputEscudo').addEventListener('change', e => {
            const file = e.target.files[0];
            if (!file) return;

            bloqueado = true;
            archivoEscudo = file;

            const reader = new FileReader();
            reader.onload = e => {
                const img = document.getElementById('escudoPreview');
                img.src = e.target.result;
                img.classList.add('animate__animated', 'animate__pulse');

                document.querySelector('.escudo-actions').classList.remove('d-none');
            };
            reader.readAsDataURL(file);
        });

        document.getElementById('btnCancelarEscudo').addEventListener('click', () => {
            document.getElementById('escudoPreview').src = escudoOriginal;
            document.querySelector('.escudo-actions').classList.add('d-none');
            document.getElementById('inputEscudo').value = '';
            archivoEscudo = null;
            bloqueado = false;
        });


    
    </script>

    <script>
        document.getElementById('btnConfirmarEscudo').addEventListener('click', () => {
            if (!archivoEscudo) return;

            Swal.fire({
                title: '¿Guardar nuevo escudo?',
                text: 'El escudo del equipo sera actualizado',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, guardar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#0d6efd'
            }).then(result => {

                if (!result.isConfirmed) return;

                const data = new FormData();
                data.append('equipo_id', <?= $equipo['id'] ?>);
                data.append('escudo', archivoEscudo);

                Swal.fire({
                    title: 'Guardando escudo...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch('index.php?page=equipos&action=guardarEscudo', {
                    method: 'POST',
                    body: data
                })
                .then(res => res.json())
                .then(res => {

                    if (!res.success) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: res.message || 'No se pudo guardar el escudo'
                        });
                        return;
                    }

                    escudoOriginal = document.getElementById('escudoPreview').src;
                    archivoEscudo = null;
                    bloqueado = false;

                    document.querySelector('.escudo-actions').classList.add('d-none');

                    Swal.fire({
                        icon: 'success',
                        title: 'Escudo actualizado',
                        text: 'El escudo se guardó correctamente',
                        timer: 1800,
                        showConfirmButton: false
                    });
                })
                .catch(() => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de comunicación',
                        text: 'No se pudo contactar al servidor'
                    });
                });
            });
        });
    </script>

    <script>
        document.getElementById('btnNuevaAfiliacion').addEventListener('click', () => {
            new bootstrap.Modal(document.getElementById('modalBuscarAfiliado')).show();
        });

        document.getElementById('inputBusqueda').addEventListener('keyup', function() {

            let valor = this.value;

            if(valor.length < 5) return;

            fetch(`index.php?page=afiliaciones&action=buscar&term=${valor}`)
            .then(res => res.json())
            .then(res => {

                let contenedor = document.getElementById('resultadoBusqueda');

                if(!res.encontrado){
                    contenedor.innerHTML = `
                        <div class="alert alert-warning">
                            No existe afiliado
                            <br>
                            <button class="btn btn-sm btn-primary mt-2" onclick="crearNuevo('${valor}')">
                                Crear nuevo
                            </button>
                        </div>
                        `;
                        return;
                }

                if(res.ya_afiliado){
                    contenedor.innerHTML = `
                        <div class="alert alert-danger">
                            Ya está afiliado en esta temporada
                        </div>
                        `;
                        return;
                }

                contenedor.innerHTML = `
                    <div class="card p-2">
                        <strong>${res.nombre}</strong>
                        <br>
                        <small>${res.curp}</small>

                        <button class="btn btn-success btn-sm mt-2"
                            onclick="afiliar(${res.id})">
                            Afiliar a equipo
                        </button>
                    </div>
                    `;

            });

        });
    </script>

<?php endSection('scripts') ?>

<style>

    /* Contenedor del escudo */
    .escudo-wrapper {
        position: relative;
        width: 160px;
        height: 160px;
        cursor: pointer;

        display: flex;
        align-items: center;
        justify-content: center;

        padding: 14px;

        background: #f4f6f4;
        border-radius: 12px;

        box-shadow:
            inset 0 0 0 1px rgba(0,0,0,.05),
            0 4px 10px rgba(0,0,0,.08);

        transition: transform .2s ease, box-shadow .2s ease;
    }

    /* Imagen del escudo */
    .escudo-img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        background: transparent;
        pointer-events: none;
    }

    /* Overlay cámara */
    .escudo-overlay {
        position: absolute;
        inset: 0;

        background: rgba(0,0,0,0.45);
        border-radius: 12px;

        display: flex;
        align-items: center;
        justify-content: center;

        color: #fff;
        font-size: 18px;

        opacity: 0;
        transition: opacity .2s ease;
    }

    /* Hover */
    .escudo-wrapper:hover {
        transform: translateY(-2px);
        box-shadow:
            inset 0 0 0 1px rgba(0,0,0,.05),
            0 8px 16px rgba(0,0,0,.12);
    }

    .escudo-wrapper:hover .escudo-overlay {
        opacity: 1;
    }

    /* Acciones */
    .escudo-actions {
        position: absolute;
        bottom: -18px;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        gap: 6px;
        animation: fadeUp .25s ease;
    }

    .escudo-actions button {
        border-radius: 50%;
    }

    /* Nombre más protagonista */
    h3.fw-bold {
        font-size: 26px;
    }

    /* Datos del equipo */
    .text-muted.small div {
        margin-bottom: 4px;
    }

    /* Escudo más limpio en header */
    .escudo-wrapper {
        width: 110px;
        height: 110px;
    }

    /* 🔥 HEADER DARK PRO */
    .header-dark {
        background: linear-gradient(135deg, #0f172a, #1e293b);
        border-radius: 14px;
        color: #e5e7eb;
    }

    /* Nombre */
    .header-dark h3 {
        font-size: 26px;
        letter-spacing: .3px;
    }

    /* Info */
    .header-info div {
        opacity: 0.85;
        margin-bottom: 4px;
    }

    /* Íconos más suaves */
    .header-info i {
        margin-right: 6px;
        opacity: 0.7;
        color:var(--accent);
    }

    /* Botones */
    .header-dark .btn-outline-light {
        border: 1px solid rgba(255,255,255,.25);
    }

    .header-dark .btn-outline-light:hover {
        background: rgba(255,255,255,.1);
    }

    /* Escudo ajuste */
    .header-dark .escudo-wrapper {
        width: 110px;
        height: 110px;
    }

    /* ===== SALDO PRO ===== */
    .saldo-box{
        background: linear-gradient(135deg, #0f172a, #1e293b);
        border-radius: 14px;
        padding: 14px 18px;
        min-width: 220px;

        border: 1px solid rgba(255,255,255,.06);
        box-shadow: 0 6px 18px rgba(0,0,0,.25);

        color: #e5e7eb;
    }

    /* Principal */
    .saldo-main{
        margin-bottom: 8px;
    }

    .saldo-label{
        font-size: 12px;
        text-transform: uppercase;
        color: #9ca3af;
        letter-spacing: .5px;
    }

    .saldo-cantidad{
        margin: 2px 0 0;
        font-size: 26px;
        font-weight: 700;
        color: #22c55e; /* verde dinero */
    }

    /* Extra */
    .saldo-extra{
        border-top: 1px solid rgba(255,255,255,.08);
        padding-top: 8px;

        display: flex;
        justify-content: space-between;
        font-size: 13px;
    }

    .saldo-item{
        display: flex;
        flex-direction: column;
    }

    .saldo-item span{
        color: #9ca3af;
        font-size: 11px;
    }

    .saldo-item strong{
        font-weight: 600;
    }

    /* Hover elegante */
    .saldo-box:hover{
        transform: translateY(-2px);
        transition: .2s ease;
        box-shadow: 0 10px 22px rgba(0,0,0,.35);
    }

    /* Animación */
    @keyframes fadeUp {
        from {
            opacity: 0;
            transform: translate(-50%, 10px);
        }
        to {
            opacity: 1;
            transform: translate(-50%, 0);
        }
    }


    /* DIRECCION */
    .direccion-update {
        animation: direccionFlash 1s ease;
    }

    @keyframes direccionFlash {
        0% {
            background-color: #fff3cd;
            transform: translateY(5px);
            opacity: 0.5;
        }
        50% {
            background-color: #d1e7dd;
            transform: translateY(0px);
            opacity: 1;
        }
        100% {
            background-color: transparent;
        }
    }

</style>