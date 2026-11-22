<?php
$escudoNombre = !empty($equipo['escudo']) ? $equipo['escudo'] : 'default.png';
$escudoRuta = ASSETS . '/img/equipos/' . rawurlencode($escudoNombre);
?>

<div class="equipos-detalle-page">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="users-title mb-1">Detalle del equipo</h3>
            <p class="text-muted mb-0">Información registrada de la entidad equipo.</p>
        </div>

        <a href="index.php?page=equipos" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Regresar al listado
        </a>
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
    <?php endif; ?>
</div>
