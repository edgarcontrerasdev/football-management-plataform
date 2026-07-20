
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

<style>

    :root{
        /* Radios */
        --radius-sm: 8px;
        --radius-md: 12px;
        --radius-lg: 16px;

        /* Sombras */
        --shadow-sm: 0 2px 6px rgba(0,0,0,.08);
        --shadow-md: 0 6px 18px rgba(0,0,0,.18);

        /* Superficies oscuras */
        --surface-dark: #0f172a;
        --surface-dark-2: #111827;

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

    .escudo{
        width: 30px;
        height: 30px;
    }

    .equipo-card {
        min-height: 80px;
        cursor: pointer;
        transition: all 0.2s ease;
        border-radius: 10px;
        border: 1px solid var(--border);
    }

    .equipo-card:hover {
        border-color: #1E40AF;
        background: #a1c9ee;
        transform:translateY(-2px);
        box-shadow: 0 4px 10px  rgba(0,0,0,0.08);
    }

    .equipo-card.seleccionado{
        background-color: #a1c9ee;
        border-color: #1E40AF;
    }

    /* Refuerzo visual del checkbox */
    .equipo-card.seleccionado input[type="checkbox"] {
        accent-color: #082f9c;
    }

    .equipo-card input[type="checkbox"] {
        transform: scale(1.2);
    }

    .equipo-card {
            transition: 
                background-color .3s ease,
                border-color .3s ease,
                transform .25s ease,
                box-shadow .25s ease;
        }

        .equipo-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 14px rgba(0,0,0,.08);
        }

        .equipo-card.pendiente-alta {
            background: rgba(255, 193, 7, .18);
            border: 1px solid #ffc107;
        }

        .equipo-card.pendiente-baja {
            background: rgba(220, 53, 69, .16);
            border: 1px solid #dc3545;
        }

        @keyframes popEstado {
        0% {
            transform: scale(1);
            box-shadow: 0 0 0 rbga(0, 0, 0, 0);
        }
        40% {
            transform: scale(1.06);
            box-shadow: 0 0px 18px rgba(0, 0, 0, 0.18);
        }
        100% {
            transform: scale(1);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        }
    }

    .estado-animado {
        animation: popEstado .35s ease;
    }

    .leyenda-estados {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 8px 12px;
        font-size: 13px;
    }

    /* Normalizar checkbox dentro del modal de equipos */
    #modalEquipos .form-check-input {
        width: 18px;
        height: 18px;
        min-width: 18px;
        min-height: 18px;
        margin-top: 0;
        cursor: pointer;
        flex-shrink: 0;
        appearance: auto;
        -webkit-appearance: auto;
    }

    .equipo-item.inscrito {
        opacity: 0.85;
    }

    .equipo-info {
        max-width: 140px;
    }

    .equipo-nombre {
        font-weight: 600;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .equipo-card.pendiente-alta {
        background: rgba(255, 193, 7, .15);
        border: 1px solid #ffc107;
    }

    .equipo-card.pendiente-baja {
        background: rgba(220, 53, 69, .12);
        border: 1px solid #dc3545;
    }

    .glass {
        background: rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);

        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 14px;

        box-shadow: 0 10px 30px rgba(0,0,0,0.25);
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
    .row.card{
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-md);
        overflow: hidden;
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

    .users-toolbar{
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: rgba(255,255,255,.04);
        backdrop-filter: blur(6px);
        padding: 10px 14px;
        border-radius: var(--radius-md);
        border: 1px solid rgba(255,255,255,.08);
        box-shadow: var(--shadow-sm);
        margin-bottom: 12px;
    }

    .search-box input,
    .users-toolbar select{
        background: rgba(255,255,255,.08);
        border: 1px solid rgba(255,255,255,.12);
        color: #e5e7eb;
        border-radius: var(--radius-sm);
    }

    .search-box input::placeholder{
        color: #9ca3af;
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

    .search-box input:focus{
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 2px rgba(59,130,246,.25);
    }
    .search-box i{
        position:absolute;
        left:12px;
        top:50%;
        transform: translateY(-50%);
        opacity: .6;
    }

    select{
        color-scheme: dark;
    }

    .select-dark{
        background-color: rgba(255,255,255,.08);
        color: #e5e7eb;
        border: 1px solid rgba(255,255,255,.12);
        border-radius: 8px;
        padding: 6px 32px 6px 10px;
        font-size: 14px;

        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;

        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='%23e5e7eb' viewBox='0 0 24 24'%3E%3Cpath d='M7 10l5 5 5-5z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 10px center;
        background-size: 18px;
    }

   .select-dark option{
        background: #111827;
        color: #e5e7eb;
    }

    .select-dark:focus{
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 2px rgba(59,130,246,.25);
    }

    .btn-create
    {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        box-shadow: var(--shadow-md);
    }

    #btnGuardarEquipos:disabled {
    opacity: .6;
    cursor: not-allowed;
    }
    .users-title{
        color: #f8fafc;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .badge-season{
        background: rgba(234,179,8,.15);
        color: #facc15;
        border: 1px solid rgba(234,179,8,.4);
        font-size: 12px;
        font-weight: 600;
        border-radius: 999px;
        padding: 4px 10px;
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

  
    .kpi-card
    {
        position: relative;
        background: linear-gradient(180deg, var(--surface-dark), var(--surface-dark-2));
        border-radius: var(--radius-md);
        padding: 14px 16px;
        border: 1px solid rgba(255,255,255,.06);
        box-shadow: var(--shadow-sm);
        color: #e5e7eb;
        min-height: 92px;
    }

    .kpi-title{
        font-size: 12px;
        letter-spacing: .5px;
        text-transform: uppercase;
        color: #9ca3af;
    }

    .kpi-card h3{
        margin: 6px 0 0;
        font-size: 30px;
        font-weight: 700;
        line-height: 1;
    }

    .kpi-card i{
        position: absolute;
        right: 14px;
        top: 14px;
        font-size: 26px;
        opacity: .25;
    }

    /* Acentos sutiles */
    .kpi-card.success{ border-left: 3px solid var(--success); }
    .kpi-card.warning{ border-left: 3px solid var(--warning); }
    .kpi-card.danger{  border-left: 3px solid var(--danger); }

</style>
