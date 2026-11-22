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

    <div class="equipos-detalle-page">

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
</div>
