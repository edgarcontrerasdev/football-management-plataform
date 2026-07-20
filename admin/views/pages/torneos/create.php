<div class="card shadow">

    <div class="card-header bg-dark  text-white d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0">Crear Torneo</h5>
            <small>Paso <span id="stepActual">1</span> de 6</small>
        </div>
        <button class="btn btn-sm btn-danger" id="btnSalirWizard"> <i class="bi bi-x-lg"></i></button>
    </div>

    <div class="card-body">

         <!-- PASO 0 - BIENVENIDA -->
        <div class="wizard-step" data-step="0">

            <div class="col-md-12 mb-5">
                <h4 class="text-center mb-3"> Bienvenido al modulo de creación de torneos </h4>
                <p class="text-center text-muted">
                    Aquí definirás la estructura administrativa y deportiva de un torneo.<br>
                    <strong>No se crearán partidos ni calendarios en este paso.</strong>
                </p>
            </div>

            <div class="row">
                <div class="col-md-5">
                    <div class="text-center mb-4">
                        <img src="/../../afec/public/assets/img/torneos/torneos_welcome.png"
                        alt="Bienvenida Torneos" class="img-fluid rounded shadow" style="max-height:280px;">
                    </div>
                </div>

                <div class="col-md-5">
                    
                    <div class="row text-start">
                        <div class="mb-3">
                            <h6>✔ ¿Qué configurarás aquí?</h6>
                            <ul class="list-unstyled mt-2">
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Tipo de torneo (Liga, Copa, Pretemporada)</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Temporada y liga</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Costos y reglas generales</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Estructura deportiva</li>
                            </ul>
                        </div>

                        <div>
                            <h6>⏭ ¿Qué se hará después?</h6>
                            <ul class="list-unstyled mt-2">
                                <li><i class="bi bi-arrow-right-circle text-primary me-2"></i>Crear bloques de competencia</li>
                                <li><i class="bi bi-arrow-right-circle text-primary me-2"></i>Asignar equipos</li>
                                <li><i class="bi bi-arrow-right-circle text-primary me-2"></i>Generar partidos y fixture</li>
                            </ul>
                        </div>
                    </div>
                
                </div>

                <div class="col-md-1">
                    <div class="text-center mt-4">
                        <button class="btn btn-primary px-4" id="btnComenzar">
                            Comenzar configuración
                        </button>
                    </div>
                </div>
            </div>
            <div class="alert alert-info mt-3 text-center">
                El torneo se guardará inicialmente en estado <strong>PLANEADO</strong>.
                Podrás modificarlo antes de activarlo.
            </div>
        </div>

        <!-- PASO 1 -->
        <div class="wizard-step" data-step="1">
            <h6 class="mb-3">1. Contexto del Torneo</h6>
             <!-- <input type="hidden" id="liga" value="<?= $ligaActualId ?>">
             <input type="hidden" id="temp" value="<?= $temporadaActualId ?>"> -->

            <div class="mb-3">
                <label class="form-label">Nombre del torneo</label>
                <input type="text" class="form-control" id="nombre_torneo">
            </div>

            <div class="mb-3">
                <label class="form-label">Tipo de torneo</label>
                <select class="form-select" id="tipo_torneo">
                    <option value="liga">Liga</option>
                    <option value="copa">Copa</option>
                    <option value="pretemporada">Pretemporada</option>
                    <option value="amistoso">Amistoso</option>
                    <option value="especial">Nacional / Especial</option>
                </select>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <label class="form-label">Temporada</label>
                    <select id="temporada_id" class="form-select" disabled>
                        <option value="<?= $temporadaActiva['id']; ?>"> 
                            <?= $temporadaActiva['temporada']; ?>
                        </option>
                    </select>
                    <div class="form-check mt-1">
                        <input class="form-check-input" type="checkbox" id="sin_temporada">
                        <label class="form-check-label">No aplica</label>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Liga</label>
                    <select class="form-select" id="liga_id">
                        <option value="">Seleccione</option>
                        <?php
                        foreach($ligas as $liga): ?>
                            <option value="<?= $liga['id']; ?>"> 
                                <?= $liga['nombre']; ?>
                            </option>
                        <?php    
                        endforeach;
                        ?>
                    </select>
                    <div class="form-check mt-1">
                        <input class="form-check-input" type="checkbox" id="sin_liga">
                        <label class="form-check-label">No aplica</label>
                    </div>
                </div>
            </div>

        </div>

        <!-- PASO 2 -->
        <div class="wizard-step d-none" data-step="2">
            <h6 class="mb-3">2. Configuración Administrativa</h6>

            <div class="row">
                <div class="col-md-4">
                    <label class="form-label">Cuota inscripción</label>
                    <input type="number" class="form-control" id="inscripcion">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Fianza</label>
                    <input type="number" class="form-control" id="fianza">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Moneda</label>
                    <select class="form-select" id="moneda">
                        <option>MXN</option>
                    </select>
                </div>
            </div>

            <div class="form-check mt-3">
                <input class="form-check-input" type="checkbox" id="requiere_pagos">
                <label class="form-check-label">Requiere pago para participar</label>
            </div>

            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="permite_invitados">
                <label class="form-check-label">Permite equipos invitados</label>
            </div>
        </div>

        <!-- PASO 3 -->
        <div class="wizard-step d-none" data-step="3">
            <h6 class="mb-3">3. Formato Deportivo</h6>

            <label class="form-label">Modalidad</label>
            <select class="form-select mb-3" id="modalidad">
                <option>Todos contra todos</option>
                <option>Grupos</option>
                <option>Eliminación directa</option>
            </select>

            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="tiene_liguilla">
                <label class="form-check-label">Habrá liguilla</label>
            </div>

            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="segunda_vuelta_puntos">
                <label class="form-check-label">Segunda vuelta suma puntos</label>
            </div>

            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="permite_inconcluso">
                <label class="form-check-label">Puede quedar inconcluso</label>
            </div>
        </div>

        <!-- PASO 4 -->
        <div class="wizard-step d-none" data-step="4">
            <h6 class="mb-3">4. Bloques de Competencia</h6>

            <div class="form-check">
                <input class="form-check-input" type="radio" name="tipo_bloques" checked>
                <label class="form-check-label">Un solo bloque</label>
            </div>

            <div class="form-check">
                <input class="form-check-input" type="radio" name="tipo_bloques">
                <label class="form-check-label">Múltiples bloques</label>
            </div>

            <div class="form-check mt-2">
                <input class="form-check-input" type="checkbox" id="comparte_bloques">
                <label class="form-check-label">Compartirá bloques con otro torneo</label>
            </div>
        </div>

        <!-- PASO 5 -->
        <div class="wizard-step d-none" data-step="5">
            <h6 class="mb-3">5. Puntaje y Control</h6>

            <div class="form-check">
                <input class="form-check-input" type="checkbox" checked>
                <label class="form-check-label">Suma puntos</label>
            </div>

            <div class="form-check">
                <input class="form-check-input" type="checkbox" checked>
                <label class="form-check-label">Genera tabla general</label>
            </div>

            <label class="form-label mt-3">Criterios de desempate</label>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" checked>
                <label class="form-check-label">Diferencia de goles</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" checked>
                <label class="form-check-label">Goles a favor</label>
            </div>
        </div>

        <!-- PASO 6 -->
        <div class="wizard-step d-none" data-step="6">

            <h6 class="mb-3">6. Confirmación y Activación</h6>
            <p class="text-muted mb-4">
                Revisa cuidadosamente la información antes de activar el torneo.
            </p>

            <button class="btn btn-outline-primary mb-4" id="btnVerResumen">
                <i class="fas fa-eye"></i> Ver resumen del torneo
            </button>

            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="confirmar_activacion">
                <label class="form-check-label">
                    Entiendo y deseo activar el torneo
                </label>
            </div>

            <div class="alert alert-info mt-3 text-center">
                Al activar el torneo se guardará inicialmente en estado <strong>PLANEADO</strong>.
                <button class="btn btn-sm btn-success w-100" id="btnActivarTorneo" disabled>
                Activar Torneo
            </button>
            </div>

        </div>
   
    </div>

    <div class="card-footer d-flex justify-content-between">
        <button class="btn btn-secondary" id="btnAnterior">Anterior</button>
        <button class="btn btn-primary" id="btnSiguiente">Siguiente</button>
    </div>

</div>

<div class="modal fade" id="modalResumenTorneo" tabindex="-1">
    
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Resumen del Torneo</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body  bg-dark">

                <h6>Datos Generales</h6>
                <p><strong>Nombre:</strong> <span id="res_nombre"></span></p>
                <p><strong>Tipo:</strong> <span id="res_tipo"></span></p>
                <p><strong>Liga:</strong> <span id="res_liga"></span></p>
                <p><strong>Temporada:</strong> <span id="res_temporada"></span></p>

                <hr>

                <h6>Configuración Administrativa</h6>
                <p><strong>Inscripción:</strong> <span id="res_inscripcion"></span></p>
                <p><strong>Fianza:</strong> <span id="res_fianza"></span></p>
                <p><strong>Requiere pago:</strong> <span id="res_pago"></span></p>

                <hr>

                <h6>Formato Deportivo</h6>
                <p><strong>Modalidad:</strong> <span id="res_modalidad"></span></p>
                <p><strong>Liguilla:</strong> <span id="res_liguilla"></span></p>

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">
                    Cerrar
                </button>
            </div>

        </div>
    </div>
</div>

<style>
    /* Animación base */
    @keyframes fadeUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Paso animado */
    .wizard-animado {
        animation: fadeUp 1.2s ease-out;
    }
    
    .glass {
        background: rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);

        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 14px;

        box-shadow: 0 10px 30px rgba(0,0,0,0.25);
    }

    
</style>

<script>
    
    let paso = 0;
    window.wizardTieneTorneo = false;

    document.getElementById('btnSalirWizard').onclick = () => {
        let titulo = "¿Deseas salir del asistente?";
        let texto = '';
        let icono = 'warning';
        if (paso<=1){
            if(window.wizardTieneTorneo){
                texto = "Si sales ahora el torneo se conservará en estado planeado y podrás retomarlo mas tarde o eliminarlo.";
            }else
              texto = "Saldrás del asistente. Aún no se iniciado ningún torneo."
        }else{
            texto = "Si sales ahora el torneo se conservará en estado planeado y podrás retomarlo mas tarde o eliminarlo.";
        }
        Swal.fire({
            title: titulo,
            text: texto,
            icon: icono,
            showCancelButton: true,
            confirmButtonText: 'Sí, salir',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'index.php?page=torneos';
            }
        });
    };

    document.getElementById('btnVerResumen').addEventListener('click', () => {

        // 🔹 Tomamos valores YA capturados
        document.getElementById('res_nombre').innerText =
        document.getElementById('nombre_torneo').value;

        document.getElementById('res_tipo').innerText =
        document.getElementById('tipo_torneo').value;

        document.getElementById('res_liga').innerText =
        document.getElementById('liga_id').selectedOptions[0]?.text || '—';

        document.getElementById('res_temporada').innerText =
        document.getElementById('temporada_id').selectedOptions[0]?.text || '—';

        document.getElementById('res_inscripcion').innerText =
        document.getElementById('inscripcion').value || '0';

        document.getElementById('res_fianza').innerText =
        document.getElementById('fianza').value || '0';

        document.getElementById('res_pago').innerText =
        document.getElementById('requiere_pagos').checked ? 'Sí' : 'No';

        document.getElementById('res_modalidad').innerText =
        document.getElementById('modalidad').value;

        document.getElementById('res_liguilla').innerText =
        document.getElementById('tiene_liguilla').checked ? 'Sí' : 'No';

        // 🔥 MOSTRAR MODAL
        new bootstrap.Modal(
            document.getElementById('modalResumenTorneo')
        ).show();
    });


    function mostrarPaso() {
        const steps = document.querySelectorAll('.wizard-step');

        steps.forEach(div => {
            div.classList.add('d-none');
            div.classList.remove('wizard-animado');
        });

        const pasoActual = document.querySelector(`.wizard-step[data-step="${paso}"]`);
        pasoActual.classList.remove('d-none');
        

        // Header step (ignora paso 0)
        const stepHeader = paso === 0 ? 0 : paso;
        document.getElementById('stepActual').innerText = stepHeader;

        // 🔥 Animación SOLO paso 0
        if (paso === 0) {
            setTimeout(() => {
                pasoActual.classList.add('wizard-animado');
            }, 50);
        }

        // Botones
        document.getElementById('btnAnterior').style.display = paso <= 1 ? 'none' : 'inline-block';
        document.getElementById('btnSiguiente').style.display = paso === 0 ? 'none' : 'inline-block';
        document.getElementById('btnSiguiente').innerText = paso === 6 ? 'Guardar' : 'Siguiente';
    }

    function guardarStep1() {

        const data = new FormData();
        data.append('nombre_torneo', document.getElementById('nombre_torneo').value);
        data.append('tipo_torneo', document.getElementById('tipo_torneo').value);
        data.append('liga_id', document.getElementById('liga_id').value);
        data.append('temporada_id', document.getElementById('temporada_id').value);

        fetch('index.php?page=torneos&action=guardarStep1', {
            method: 'POST',
            body: data
        })
        .then(res => res.json())
        .then(res => {
            if (res.success) {
                paso = 2;
                window.wizardTieneTorneo = true;
                mostrarPaso();
            } else {
                alert('Error al guardar torneo');
            }
        })
        .catch(() => alert('Error de conexión'));
    }

    function guardarStep2(){
        const data = new FormData();
        const requierePagos = document.getElementById('requiere_pagos');
        const permiteInvitados = document.getElementById('permiteInvitados');

        data.append('inscripcion', document.getElementById('inscripcion').value);
        data.append('fianza', document.getElementById('fianza').value);
        data.append('moneda', document.getElementById('moneda').value);
        
        if (requierePagos && requierePagos.checked) {
            data.append('requiere_pagos', 1);
        }

        if (permiteInvitados && permiteInvitados.checked) {
            data.append('permite_invitados', 1);
        }

        fetch('index.php?page=torneos&action=guardarStep2', {
            method: 'POST',
            body: data
        })
        .then(res => res.json())
        .then(res => {
            console.log('RESPUESTA STEP 2:', res);
            if (res.success) {
                paso = 3;
                mostrarPaso();
            } else {
                alert('Error al guardar el paso 2');
            }
        })
        .catch(err => {
                console.log(err);
                alert('Error de conexión');
        });    
    }

   function guardarStep3() 
    {
        const data = new FormData();
        data.append('modalidad', document.getElementById('modalidad').value);
        if(document.getElementById('tiene_liguilla').checked){
            data.append('tiene_liguilla', 1);
        }
        if(document.getElementById('segunda_vuelta_puntos').checked){
            data.append('segunda_vuelta_puntos', 1);
        }
        if(document.getElementById('permite_inconcluso').checked){
            data.append('permite_inconcluso',1);
        }
        fetch('index.php?page=torneos&action=guardarStep3', {
            method: 'POST',
            body: data
        })
        .then(res => res.json())
        .then(res => {
            if (res.success) {
                paso = 4;
                mostrarPaso();
            } else {
                alert(res.message || 'Error al guardar formato deportivo');
            }
        })
        .catch(() => alert('Error de conexión'));
    }

    function guardarStep4() 
    {
        const tipoBloques = document.querySelector(
            'input[name="tipo_bloques"]:checked'
        )?.value;

        const data = new FormData();
        data.append('tipo_bloques', tipoBloques);
        data.append(
            'comparte_bloques',
            document.getElementById('comparte_bloques').checked ? 1 : 0
        );

        fetch('index.php?page=torneos&action=guardarStep4', {
            method: 'POST',
            body: data
        })
        .then(res => res.json())
        .then(res => {
            if (res.success) {
                paso = 5;
                mostrarPaso();
            } else {
                alert(res.message || 'Error al guardar Step 4');
            }
        })
        .catch(() => alert('Error de conexión'));
    }

    document.getElementById('btnSiguiente').onclick = () => {
        if (paso === 1) {
            guardarStep1();
            return; // esperamos respuesta
        }

        if(paso === 2){
            guardarStep2();
            return;
        }

        if(paso == 3){
            guardarStep3();
            return;
        }
        
        if (paso < 6) paso++;
        mostrarPaso();
    };

    document.getElementById('btnAnterior').onclick = () => {
    if (paso > 1) paso--;
        mostrarPaso();
    };

    document.getElementById('btnComenzar')?.addEventListener('click', () => {
        paso = 1;
        mostrarPaso();
    });

    mostrarPaso();
    
</script>
