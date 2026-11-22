<?php
$nombreClub = (string) ($club['nombre'] ?? '');
$logoNombre = !empty($club['logo']) ? $club['logo'] : 'default.png';
$logoRuta = !empty($club['logo'])
    ? ASSETS . '/img/clubes/' . rawurlencode($logoNombre)
    : ASSETS . '/img/ligas/default.png';
$estadoClub = strtolower((string) ($club['estado'] ?? ''));
$claseEstado = $estadoClub === 'activo' ? 'success' : 'secondary';
?>

<div class="clubes-detalle-page">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="page-title mb-1">Detalle del club</h3>
            <p class="text-muted mb-0">Información general de la entidad club.</p>
        </div>

        <div class="d-flex gap-2 flex-wrap">
        <?php if (!empty($club)): ?>
        <a href="index.php?page=clubes&action=editar&id=<?= (int) $club['id'] ?>" class="btn btn-primary"><i class="fa-solid fa-pen me-1"></i>Editar club</a>
        <?php endif; ?>
        <a href="index.php?page=clubes" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Regresar al listado
        </a>
        </div>
    </div>

    <?php if (empty($club)): ?>
        <div class="alert alert-warning" role="alert">
            No se encontró el club solicitado.
        </div>
    <?php else: ?>
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex flex-wrap align-items-center gap-4">
                    <div class="border rounded-3 bg-white d-flex align-items-center justify-content-center p-3"
                         style="width: 140px; height: 140px;">
                        <img
                            src="<?= htmlspecialchars($logoRuta, ENT_QUOTES, 'UTF-8') ?>"
                            alt="Escudo de <?= htmlspecialchars($nombreClub, ENT_QUOTES, 'UTF-8') ?>"
                            class="img-fluid"
                            style="max-width: 100%; max-height: 100%; object-fit: contain;"
                        >
                    </div>

                    <div>
                        <h4 class="fw-bold mb-1"><?= htmlspecialchars($nombreClub, ENT_QUOTES, 'UTF-8') ?></h4>
                        <p class="text-muted mb-2">
                            <?= !empty($club['siglas'])
                                ? htmlspecialchars($club['siglas'], ENT_QUOTES, 'UTF-8')
                                : 'Sin siglas registradas' ?>
                        </p>
                        <span class="badge bg-<?= $claseEstado ?>">
                            <?= htmlspecialchars($club['estado'] ?? 'Sin estado', ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </div>
                </div>

                <hr class="my-4">

                <h5 class="mb-3">Datos generales</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <span class="text-muted small d-block">Nombre del club</span>
                        <span class="fw-semibold"><?= htmlspecialchars($nombreClub, ENT_QUOTES, 'UTF-8') ?></span>
                    </div>

                    <div class="col-md-6">
                        <span class="text-muted small d-block">Siglas</span>
                        <span class="fw-semibold">
                            <?= !empty($club['siglas'])
                                ? htmlspecialchars($club['siglas'], ENT_QUOTES, 'UTF-8')
                                : 'No registradas' ?>
                        </span>
                    </div>

                    <div class="col-md-6">
                        <span class="text-muted small d-block">Fecha de registro</span>
                        <span class="fw-semibold">
                            <?= !empty($club['created_at'])
                                ? htmlspecialchars(date('d/m/Y H:i', strtotime($club['created_at'])), ENT_QUOTES, 'UTF-8')
                                : 'No disponible' ?>
                        </span>
                    </div>

                    <div class="col-md-6">
                        <span class="text-muted small d-block">Última actualización</span>
                        <span class="fw-semibold">
                            <?= !empty($club['updated_at'])
                                ? htmlspecialchars(date('d/m/Y H:i', strtotime($club['updated_at'])), ENT_QUOTES, 'UTF-8')
                                : 'No disponible' ?>
                        </span>
                    </div>

                    <div class="col-12">
                        <span class="text-muted small d-block">Observaciones</span>
                        <p class="mb-0">
                            <?= !empty($club['observaciones'])
                                ? nl2br(htmlspecialchars($club['observaciones'], ENT_QUOTES, 'UTF-8'))
                                : 'Sin observaciones registradas.' ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mt-4">
            <div class="card-body p-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                    <div>
                        <h5 class="mb-1">Equipos del club</h5>
                        <p class="text-muted small mb-0">Equipos vinculados a este club y su historial de pertenencia.</p>
                    </div>
                    <span class="badge bg-light text-dark"><?= count($equipos ?? []) ?> equipos</span>
                </div>

                <?php if (!empty($errorAsignacion)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?= htmlspecialchars($errorAsignacion, ENT_QUOTES, 'UTF-8') ?>
                    </div>
                <?php endif; ?>

                <?php if ($estadoClub === 'activo'): ?>
                    <div class="border rounded-3 p-3 mb-4">
                        <h6 class="fw-bold mb-3">
                            <i class="fa-solid fa-link me-2"></i>Vincular un equipo
                        </h6>

                        <?php if (!empty($equiposDisponibles)): ?>
                            <form action="index.php?page=clubes&action=asignarEquipo&id=<?= (int) $club['id'] ?>" method="POST">
                                <div class="row g-3">
                                    <div class="col-lg-5">
                                        <label for="equipo_id" class="form-label">Equipo <span class="text-danger">*</span></label>
                                        <select name="equipo_id" id="equipo_id" class="form-select" required>
                                            <option value="">Selecciona un equipo</option>
                                            <?php foreach ($equiposDisponibles as $equipoDisponible): ?>
                                                <option value="<?= (int) $equipoDisponible['id'] ?>">
                                                    <?= htmlspecialchars($equipoDisponible['nombre'], ENT_QUOTES, 'UTF-8') ?>
                                                    <?= !empty($equipoDisponible['siglas']) ? ' (' . htmlspecialchars($equipoDisponible['siglas'], ENT_QUOTES, 'UTF-8') . ')' : '' ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-lg-3">
                                        <label for="fecha_inicio" class="form-label">Fecha de inicio <span class="text-danger">*</span></label>
                                        <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" value="<?= htmlspecialchars(date('Y-m-d'), ENT_QUOTES, 'UTF-8') ?>" required>
                                    </div>
                                    <div class="col-lg-4">
                                        <label for="observaciones_asignacion" class="form-label">Observaciones</label>
                                        <input type="text" name="observaciones" id="observaciones_asignacion" class="form-control" maxlength="500" placeholder="Opcional">
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end mt-3">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa-solid fa-plus me-1"></i>Vincular equipo
                                    </button>
                                </div>
                            </form>
                        <?php else: ?>
                            <p class="text-muted small mb-0">No hay equipos disponibles para vincular. Los equipos que ya pertenecen a otro club no aparecen en esta lista.</p>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-secondary">
                        Este club está inactivo. No es posible vincular nuevos equipos.
                    </div>
                <?php endif; ?>

                <?php if (empty($equipos)): ?>
                    <p class="text-muted mb-0">Este club todavía no tiene equipos relacionados.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Equipo</th>
                                    <th>Inicio</th>
                                    <th>Fin</th>
                                    <th>Estado</th>
                                    <th class="text-end">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($equipos as $equipo): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-semibold"><?= htmlspecialchars($equipo['nombre'], ENT_QUOTES, 'UTF-8') ?></div>
                                            <?php if (!empty($equipo['siglas'])): ?>
                                                <small class="text-muted"><?= htmlspecialchars($equipo['siglas'], ENT_QUOTES, 'UTF-8') ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= !empty($equipo['fecha_inicio']) ? htmlspecialchars(date('d/m/Y', strtotime($equipo['fecha_inicio'])), ENT_QUOTES, 'UTF-8') : '-' ?></td>
                                        <td><?= !empty($equipo['fecha_fin']) ? htmlspecialchars(date('d/m/Y', strtotime($equipo['fecha_fin'])), ENT_QUOTES, 'UTF-8') : 'Actual' ?></td>
                                        <td><?= htmlspecialchars($equipo['estado_relacion'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                                        <td class="text-end">
                                            <a href="index.php?page=equipos&action=detalles&id=<?= (int) $equipo['id'] ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fa-solid fa-eye me-1"></i> Ver equipo
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
