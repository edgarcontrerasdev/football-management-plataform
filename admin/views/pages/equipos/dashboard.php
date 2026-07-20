<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- ENCABEZADO -->
<div class="shadow-sm border-0 mb-3">

    <div class="">
        <div class="row align-items-center">

            <!-- ESCUDO -->
            <div class="col-md-2 text-center">
                <img src="/afec/public/assets/img/equipos/default.png" 
                class="img-fluid rounded shadow-sm" style="max-height:90px">
            </div>

            <!-- DATOS DEL EQUIPO -->
            <div class="col-md-6">
                <h4 class="fw-bold mb-1"> <?= $equipo['nombre'] ?> </h4> 
                <span class="badge bg-success"> Activo </span>
                <div class="text-muted small">
                    <div>
                        <i class="bi bi-geo-alt"></i> Calle sin nombre       
                    </div>
                    <div>
                        <i class="bi bi-envelope"></i> equipo@gmail.com
                    </div>
                    <div>
                        <i class="bi bi-telephone"></i> 3121301033
                    </div>
                    <div>
                        <i class="bi bi-instagram"></i> @sanrafael
                    </div>
                </div>
            </div>

            <!-- ESTADO DEL EQUIPO -->
            <div class="col-md-4 text-end">
                    <small class="text-muted"> Liga: Liga de prueba </small>
            </div>


        </div>
    </div>

</div>

<div class="row g-3 mb-5 ">

    <div class="col-md-3">
        <div class="kpi-card success">
            <span class="kpi-title">Total</span>
            <h3><?= $metricas['total'] ?? 0 ?></h3>
            <i class="fas fa-trophy"></i>
        </div>
    </div>

    <div class="col-md-3">
        <div class="kpi-card success">
            <span class="kpi-title">Activos</span>
            <h3><?= $metricas['activos'] ?? 0 ?></h3>
            <i class="fas fa-play-circle"></i>
        </div>
    </div>

    <div class="col-md-3">
        <div class="kpi-card warning">
            <span class="kpi-title">Planeados</span>
            <h3><?= $metricas['planeados'] ?? 0 ?></h3>
            <i class="fas fa-clock"></i>
        </div>
    </div>

    <div class="col-md-3">
        <div class="kpi-card danger">
            <span class="kpi-title">Finalizados</span>
            <h3><?= $metricas['finalizados'] ?? 0 ?></h3>
            <i class="fas fa-flag-checkered"></i>
        </div>
    </div>
    
</div>

<!-- TABS -->
<div class="card shadow-sm border-0">

        <div class="card-header bg-white">
            <ul class="nav nav-tabs card-header-tabs" id="equipoTabs">

                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#info">
                       <i class="bi bi-info"></i> Información
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#plantilla">
                       <i class="bi bi-people"></i> Plantilla
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#afiliaciones">
                        <i class="bi bi-person-badge"> </i>Afiliaciones
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#documentos">
                        <i class="bi bi-folder"></i>Documentos
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#pagos">
                       <i class="bi bi-cash"></i> Pagos
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#deportivo">
                        <i class="bi bi-trophy"></i>Deportivo
                    </a>
                </li>

            </ul>
        </div>

        <div class="card-body">

            <div class="tab-content">

                <!-- INFORMACION -->
                <div class="tab-pane fade show active" id="info">
                    <?php require 'tabs/informacion.php'; ?>
                </div>

                <!-- PLANTILLA -->
                <div class="tab-pane fade" id="plantilla">
                    <?php require 'tabs/plantilla.php'; ?>
                </div>

                <!-- AFILIACIONES -->
                <div class="tab-pane fade" id="afiliaciones">
                    <?php require 'tabs/afiliaciones.php'; ?>
                </div>

                <!-- DOCUMENTOS -->
                <div class="tab-pane fade" id="documentos">
                    <?php require 'tabs/documentos.php'; ?>
                </div>

                <!-- PAGOS -->
                <div class="tab-pane fade" id="pagos">
                    <?php require 'tabs/pagos.php'; ?>
                </div>

                <!-- DEPORTIVO -->
                <div class="tab-pane fade" id="deportivo">
                    <?php require 'tabs/deportivo.php'; ?>
                </div>

            </div>

        </div>

</div>
<div class="row g-2 mb-3">

<div class="col">
<div class="kpi-mini success text-center">
<h5><?= $kpis['aprobados'] ?></h5>
<small>Aprobados</small>
</div>
</div>

<div class="col">
<div class="kpi-mini warning text-center">
<h5><?= $kpis['pendientes'] ?></h5>
<small>Pendientes</small>
</div>
</div>

<div class="col">
<div class="kpi-mini danger text-center">
<h5><?= $kpis['rechazados'] ?></h5>
<small>Rechazados</small>
</div>
</div>

<div class="col">
<div class="kpi-mini info text-center">
<h5><?= $kpis['proceso'] ?></h5>
<small>En proceso</small>
</div>
</div>

</div>

<div class="card border-0 shadow-sm">
<div class="card-body p-0">

<table class="table table-hover align-middle mb-0">

<thead class="table-light">
<tr>
<th>Jugador</th>
<th>Tipo</th>
<th>Seguro</th>
<th>Estado</th>
<th>Fecha</th>
<th class="text-end">Acciones</th>
</tr>
</thead>

<tbody>

<?php foreach($afiliaciones as $a): ?>

<tr>

<td>
<div class="d-flex align-items-center">

<img src="/uploads/fotos/<?= $a['foto'] ?>"
     class="rounded-circle me-2"
     width="40" height="40">

<div>
<strong><?= $a['nombre'] ?></strong><br>
<small class="text-muted"><?= $a['curp'] ?></small>
</div>

</div>
</td>

<td><?= $a['tipo'] ?></td>

<td><?= $a['seguro'] ?></td>

<td>
<?= estadoBadge($a['estado']) ?>
</td>

<td>
<small class="text-muted">
<?= date('d/m/Y', strtotime($a['created_at'])) ?>
</small>
</td>

<td class="text-end">

<button class="btn btn-sm btn-outline-primary">
<i class="fas fa-eye"></i>
</button>

<button class="btn btn-sm btn-outline-warning">
<i class="fas fa-pencil"></i>
</button>

<?php if($a['estado'] == 'rechazada'): ?>
<button class="btn btn-sm btn-outline-danger">
<i class="fas fa-exclamation-circle"></i>
</button>
<?php endif; ?>

</td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

</div>
</div>

<td>
<div class="progress" style="height:6px;">
<div class="progress-bar bg-success" style="width: 80%"></div>
</div>
<small class="text-muted">En proceso</small>
</td>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var triggerTabList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tab"]'))

    triggerTabList.forEach(function (triggerEl) {
        triggerEl.addEventListener('click', function (event) {
            event.preventDefault()
            var tab = new bootstrap.Tab(triggerEl)
            tab.show()
        })
    })
});
</script>

<script>
    <?php
function estadoBadge($estado)
{
    switch($estado)
    {
        case 'pendiente':
            return '<span class="badge bg-warning text-dark">Pendiente</span>';

        case 'en_proceso':
            return '<span class="badge bg-info">En proceso</span>';

        case 'aprobada':
            return '<span class="badge bg-success">Aprobada</span>';

        case 'rechazada':
            return '<span class="badge bg-danger">Rechazada</span>';

        case 'impresa':
            return '<span class="badge bg-primary">Impresa</span>';

        case 'entregada':
            return '<span class="badge bg-dark">Entregada</span>';
    }
}
?>
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

    /* estilos para filas vacias */
    .empty-state
    {
        display:flex;
        flex-direction:column;
        align-items:center;
        justify-content:center;
        padding: 40px 20px;
        text-align:center;
        color: var(--text-muted);
    }

    .empty-icon{
        width:72px;
        height:72px;
        border-radius:50%;
        background: rgba(59,130,246,.12);
        display:flex;
        align-items:center;
        justify-content:center;
        margin-bottom:16px;
    }

    .empty-icon i{
        font-size:32px;
        color: var(--primary);
    }

    .empty-state h5{
        margin-bottom:6px;
        color: var(--text);
        font-weight:600;
    }

    .empty-state p{
        max-width:420px;
        font-size:14px;
    }

    .empty-state{
        animation: fadeIn .35s ease;
    }

    @keyframes fadeIn{
        from{ opacity:0; transform:translateY(10px); }
        to{ opacity:1; transform:none; }
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
