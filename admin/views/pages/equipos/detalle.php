<?php
$escudoNombre = !empty($equipo['escudo']) ? $equipo['escudo'] : 'default.png';
$escudoRuta = ASSETS . '/img/equipos/' . rawurlencode($escudoNombre);
?>

<div class="equipos-detalle-page">
    <div class="entity-detail-heading d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="users-title mb-1">Detalle del equipo</h3>
            <p class="text-muted mb-0">Información registrada de la entidad equipo.</p>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <a href="index.php?page=equipos&action=editar&id=<?= (int) ($equipo['id'] ?? 0) ?>" class="btn btn-primary">
                <i class="bi bi-pencil-square me-1"></i>Editar equipo
            </a>
            <a href="index.php?page=equipos" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Regresar al listado
            </a>
        </div>
    </div>

    <?php if (empty($equipo)): ?>
        <div class="alert alert-warning" role="alert">
            No se encontró el equipo solicitado.
        </div>
    <?php else: ?>
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="row align-items-center g-4">
                    <div class="col-auto">
                        <div class="border rounded-3 bg-white d-flex align-items-center justify-content-center p-3" style="width: 150px; height: 150px;">
                            <img
                                src="<?= htmlspecialchars($escudoRuta, ENT_QUOTES, 'UTF-8') ?>"
                                alt="Escudo de <?= htmlspecialchars($equipo['equipo'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                class="img-fluid"
                                style="max-width: 100%; max-height: 100%; object-fit: contain;"
                            >
                        </div>
                    </div>

                    <div class="col">
                        <h4 class="fw-bold mb-1">
                            <?= htmlspecialchars($equipo['equipo'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                        </h4>

                        <p class="text-muted mb-0">
                            <?= !empty($equipo['siglas'])
                                ? htmlspecialchars($equipo['siglas'], ENT_QUOTES, 'UTF-8')
                                : 'Sin siglas registradas' ?>
                        </p>
                    </div>
                </div>

                <hr class="my-4">

                <h5 class="mb-3">Datos generales</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <span class="text-muted small d-block">Nombre del equipo</span>
                        <span class="fw-semibold"><?= htmlspecialchars($equipo['equipo'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                    </div>

                    <div class="col-md-6">
                        <span class="text-muted small d-block">Siglas</span>
                        <span class="fw-semibold">
                            <?= !empty($equipo['siglas'])
                                ? htmlspecialchars($equipo['siglas'], ENT_QUOTES, 'UTF-8')
                                : 'No registradas' ?>
                        </span>
                    </div>

                    <div class="col-md-6">
                        <span class="text-muted small d-block">Fecha de registro</span>
                        <span class="fw-semibold">
                            <?= !empty($equipo['created_at'])
                                ? htmlspecialchars(date('d/m/Y H:i', strtotime($equipo['created_at'])), ENT_QUOTES, 'UTF-8')
                                : 'No disponible' ?>
                        </span>
                    </div>

                    <div class="col-md-6">
                        <span class="text-muted small d-block">Última actualización</span>
                        <span class="fw-semibold">
                            <?= !empty($equipo['updated_at'])
                                ? htmlspecialchars(date('d/m/Y H:i', strtotime($equipo['updated_at'])), ENT_QUOTES, 'UTF-8')
                                : 'No disponible' ?>
                        </span>
                    </div>

                    <div class="col-12">
                        <span class="text-muted small d-block">Observaciones</span>
                        <p class="mb-0">
                            <?= !empty($equipo['observaciones'])
                                ? nl2br(htmlspecialchars($equipo['observaciones'], ENT_QUOTES, 'UTF-8'))
                                : 'Sin observaciones registradas.' ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mt-1">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="mb-3">Clubes relacionados</h5>
                        <?php if (empty($clubes)): ?>
                            <p class="text-muted mb-0">Este equipo no tiene relaciones con clubes registradas.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-sm align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>Club</th>
                                            <th>Inicio</th>
                                            <th>Fin</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($clubes as $club): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($club['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                                                <td><?= !empty($club['fecha_inicio']) ? htmlspecialchars(date('d/m/Y', strtotime($club['fecha_inicio'])), ENT_QUOTES, 'UTF-8') : '-' ?></td>
                                                <td><?= !empty($club['fecha_fin']) ? htmlspecialchars(date('d/m/Y', strtotime($club['fecha_fin'])), ENT_QUOTES, 'UTF-8') : 'Actual' ?></td>
                                                <td><?= htmlspecialchars($club['estado'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="mb-3">Participación en ligas y temporadas</h5>
                        <?php if (empty($participaciones)): ?>
                            <p class="text-muted mb-0">Este equipo no tiene participaciones en ligas registradas.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-sm align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>Liga</th>
                                            <th>Temporada</th>
                                            <th>Participación</th>
                                            <th>Documentación</th>
                                            <th>Registro</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($participaciones as $participacion): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($participacion['liga'], ENT_QUOTES, 'UTF-8') ?></td>
                                                <td><?= htmlspecialchars($participacion['temporada'], ENT_QUOTES, 'UTF-8') ?></td>
                                                <td><?= htmlspecialchars($participacion['estado_participacion'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                                                <td><?= htmlspecialchars($participacion['estado_documentacion'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                                                <td><?= htmlspecialchars($participacion['numero_registro'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
