<div class="torneo-detalle-page">

<div class="container-fluid py-4">

    <!-- HEADER -->
    <?php require __DIR__ . '/partials/detalles/header.php'; ?>

    <!-- kpis -->
    <?php require __DIR__ . '/partials/detalles/kpis.php'; ?>


    <!-- SIGUIENTE PASO RECOMENDADO -->
    <?php require __DIR__ . '/partials/detalles/siguiente-paso.php'; ?>

    <?php require __DIR__ . '/partials/detalles/time-line.php'; ?>

    <!-- PROGRESO -->

    <!-- MODULOS -->
    <?php require __DIR__ . '/partials/detalles/modulos.php'; ?>
</div>

<!-- modal para agregar equipos al torneo -->
<?php require __DIR__ . '/partials/detalles/modal-equipos.php'; ?>
<!-- modal para administrar bloques -->
<?php require __DIR__ . '/partials/detalles/modal-bloques.php'; ?>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        let cerrarForzado = false;

        const modal = document.getElementById('modalEquipos');
        const btnsAbrirEquipos = document.querySelectorAll('.btnAbrirEquipos');
        const checks = document.querySelectorAll('.equipo-check');
        const contador = document.getElementById('contadorSeleccionados');
        const btnGuardar = document.getElementById('btnGuardarEquipos');
        const badgeCambios = document.getElementById('badgeCambios');
        const btnRestaurar = document.getElementById('btnRestaurarCambios');
        const btnToggle = document.getElementById('btnToggleEquipos');
        const buscador = document.getElementById('buscadorEquipos');

        if(modal){
            modal.addEventListener('hide.bs.modal', function(e){
                if(!hayCambios() || cerrarForzado){
                    cerrarForzado = false;
                    return;
                }
                e.preventDefault();
                Swal.fire({
                    icon:'warning',
                    title:'Cambios sin guardar',
                    text:'Si sales en este momento, los cambios realizados no se guardaran',
                    showCancelButton:true,
                    cancelButtonText: 'Regresar y seguir editando',
                    confirmButtonText:'Salir sin guardar',
                    confirmButtonColor:'#1E40AF'
                }).then(result =>{
                    if(result.isConfirmed){
                        restaurarEstadoOriginal();
                        cerrarForzado = true;
                        const instancia =  bootstrap.Modal.getInstance(modal);
                        instancia.hide();
                    }
                });
            });
        }

        if (btnsAbrirEquipos.length > 0 && modal) {
            btnsAbrirEquipos.forEach(btn =>{
                btn.addEventListener('click', function () {
                    const instancia = bootstrap.Modal.getOrCreateInstance(modal);
                    instancia.show();
                    refrescarTodo();
                });
            });
        }

        checks.forEach(check => {
            check.addEventListener('change', function () {
                actualizarEstadoVisual(this);
                refrescarTodo();
            });

            actualizarEstadoVisual(check);
        });

        if (btnToggle) {
            btnToggle.addEventListener('click', function () {
                const total = checks.length;
                const seleccionados = [...checks].filter(c => c.checked).length;
                const marcar = seleccionados < total;

                checks.forEach(check => {
                    check.checked = marcar;
                    actualizarEstadoVisual(check);
                });

                refrescarTodo();
            });
        }

        if (btnRestaurar) {
            btnRestaurar.addEventListener('click', restaurarEstadoOriginal);

        }

        if (buscador) {
            buscador.addEventListener('keyup', function () {
                const texto = this.value.toLowerCase();

                document.querySelectorAll('.equipo-item').forEach(item => {
                    item.style.display = item.innerText.toLowerCase().includes(texto)
                        ? ''
                        : 'none';
                });
            });
        }

        if (btnGuardar) {
            btnGuardar.addEventListener('click', function () {
                const equipos = [];

                document.querySelectorAll('.equipo-check:checked').forEach(check => {
                    equipos.push(check.value);
                });

                if (equipos.length === 0) {
                    Swal.fire('Atención', 'Selecciona al menos un equipo.', 'warning');
                    return;
                }

                const data = new FormData();
                data.append('torneo_id', <?= $torneo['id']; ?>);

                equipos.forEach(id => {
                    data.append('equipos[]', id);
                });

                btnGuardar.disabled = true;
                btnGuardar.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';

                fetch('index.php?page=torneos&action=asignarEquipos', {
                    method: 'POST',
                    body: data
                })
                .then(r => r.json())
                .then(res => {
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Equipos guardados',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => location.reload());
                    } else {
                        Swal.fire('Error', res.msg ?? 'Error al guardar', 'error');
                    }
                })
                .catch(() => {
                    Swal.fire('Error', 'Error de conexión', 'error');
                })
                .finally(() => {
                    btnGuardar.innerHTML = '<i class="fas fa-save"></i> Guardar cambios';
                    refrescarTodo();
                });
            });
        }

       
    document.getElementById('formBloque')?.addEventListener('submit', function(e) {
        e.preventDefault();

        const form = this;
        const data = new FormData(form);
        const btn = form.querySelector('button[type="submit"]');

        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';

        fetch('index.php?page=torneos&action=guardarBloque', {
            method: 'POST',
            body: data
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Bloque guardado',
                    timer: 1300,
                    showConfirmButton: false
                }).then(() => location.reload());
            } else {
                Swal.fire('Error', res.msg ?? 'No se pudo guardar', 'error');
            }
        })
        .catch(() => {
            Swal.fire('Error', 'Error de conexión', 'error');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save"></i> Guardar bloque';
        });
    });


        function refrescarTodo() {
            actualizarContador();
            actualizarBotonGuardar();
            actualizarTextoToggle();
        }

        function actualizarContador() {
            if (!contador) return;

            const seleccionados = document.querySelectorAll('.equipo-check:checked').length;
            contador.innerText = seleccionados;
        }

        function hayCambios() {
            return [...checks].some(check => {
                const original = check.dataset.original === '1';
                return check.checked !== original;
            });
        }

        function actualizarBotonGuardar() {
            const cambios = hayCambios();

            if (badgeCambios) {
                badgeCambios.classList.toggle('d-none', !cambios);
            }

            if (btnRestaurar) {
                btnRestaurar.classList.toggle('d-none', !cambios);
            }

            if (btnGuardar) {
                btnGuardar.disabled = !cambios;
            }
        }

        function actualizarTextoToggle() {
            if (!btnToggle) return;

            const total = checks.length;
            const seleccionados = [...checks].filter(c => c.checked).length;

            btnToggle.innerText = seleccionados === total
                ? 'Deseleccionar todos'
                : 'Seleccionar todos';
        }

        function actualizarEstadoVisual(check) {
            const card = check.closest('.equipo-card');
            const item = check.closest('.equipo-item');
            const badge = item.querySelector('.estado-equipo');

            if (!card || !item || !badge) return;

            const original = check.dataset.original === '1';
            const actual = check.checked;

            card.classList.remove('seleccionado', 'pendiente-alta', 'pendiente-baja');
            item.classList.remove('inscrito');

            if (!original && !actual) {
                badge.className = 'badge estado-equipo bg-secondary';
                badge.innerText = 'Disponible';
            }

            if (original && actual) {
                card.classList.add('seleccionado');
                item.classList.add('inscrito');
                badge.className = 'badge estado-equipo bg-success';
                badge.innerText = 'Inscrito';
            }

            if (!original && actual) {
                card.classList.add('pendiente-alta');
                badge.className = 'badge estado-equipo bg-warning text-dark';
                badge.innerText = 'Se inscribirá';
            }

            if (original && !actual) {
                card.classList.add('pendiente-baja');
                badge.className = 'badge estado-equipo bg-danger';
                badge.innerText = 'Se eliminará';
            }
        }

        function restaurarEstadoOriginal() {
            checks.forEach(check => {
                check.checked = check.dataset.original === '1';
                actualizarEstadoVisual(check);
            });

            refrescarTodo();
        }

        refrescarTodo();
    });



</script>
</div>
