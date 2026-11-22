<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<div class="equipos-dashboard-page">

<!-- ENCABEZADO -->
<div class="shadow-sm border-0 mb-3">

    <div class="">
        <div class="row align-items-center">

            <!-- ESCUDO -->
            <div class="col-md-2 text-center">
                <img src="/afec/public/assets/img/equipos/default.png" 
                class="img-fluid rounded shadow-sm equipo-escudo-dashboard">
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
        <div class="kpi-card kpi-card--dashboard success">
            <span class="kpi-title">Total</span>
            <h3 class="kpi-value"><?= $metricas['total'] ?? 0 ?></h3>
            <i class="kpi-icon fas fa-trophy"></i>
        </div>
    </div>

    <div class="col-md-3">
        <div class="kpi-card kpi-card--dashboard success">
            <span class="kpi-title">Activos</span>
            <h3 class="kpi-value"><?= $metricas['activos'] ?? 0 ?></h3>
            <i class="kpi-icon fas fa-play-circle"></i>
        </div>
    </div>

    <div class="col-md-3">
        <div class="kpi-card kpi-card--dashboard warning">
            <span class="kpi-title">Planeados</span>
            <h3 class="kpi-value"><?= $metricas['planeados'] ?? 0 ?></h3>
            <i class="kpi-icon fas fa-clock"></i>
        </div>
    </div>

    <div class="col-md-3">
        <div class="kpi-card kpi-card--dashboard danger">
            <span class="kpi-title">Finalizados</span>
            <h3 class="kpi-value"><?= $metricas['finalizados'] ?? 0 ?></h3>
            <i class="kpi-icon fas fa-flag-checkered"></i>
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
<div class="progress equipo-progress-dashboard">
<div class="progress-bar bg-success equipo-progress-dashboard-bar"></div>
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
</div>
