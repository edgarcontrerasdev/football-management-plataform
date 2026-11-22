<?php
    $old = $_SESSION['equipo_old'] ?? [];
    unset($_SESSION['equipo_old']);
?>

<h3 class="users-title mb-1">NUEVO EQUIPO</h3>

<div class="context-bar mb-3">
    <div class="context-icon">
        <i class="fa-solid fa-shield-halved"></i>
    </div>
    <div>
        <strong>Registro de entidad equipo</strong>
        <p class="mb-0">Registra los datos permanentes del equipo. Su club, identidad por temporada e inscripción en una liga se administrarán por separado.</p>
    </div>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger" role="alert">
        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
    </div>
<?php endif; ?>

<div class="card shadow-sm border-0 equipo-create-card">
    <div class="card-body p-0">
        <form id="formCreateEquipo" action="index.php?page=equipos&action=store" method="POST" enctype="multipart/form-data">
            <div class="row g-0">
                <div class="col-lg-8 p-4">
                    <section class="equipo-form-section">
                        <h6 class="text-uppercase fw-bold mb-3">
                            <i class="fa-solid fa-circle-info me-2"></i>Identidad del equipo
                        </h6>

                        <div class="mb-3">
                            <label class="form-label" for="nombre">Nombre del equipo <span class="text-danger">*</span></label>
                            <input
                                type="text"
                                id="nombre"
                                name="nombre"
                                class="form-control"
                                maxlength="150"
                                value="<?= htmlspecialchars($old['nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                placeholder="Ej. Deportivo San Rafael"
                                required
                                autofocus
                            >
                            <small class="form-text text-muted">Nombre permanente con el que se identificará esta entidad en el sistema.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="siglas">Siglas o clave corta</label>
                            <input
                                type="text"
                                id="siglas"
                                name="siglas"
                                class="form-control"
                                maxlength="30"
                                value="<?= htmlspecialchars($old['siglas'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                placeholder="Ej. SR"
                            >
                        </div>
                    </section>

                    <section class="equipo-form-section mt-4">
                        <h6 class="text-uppercase fw-bold mb-3">
                            <i class="fa-solid fa-file-lines me-2"></i>Información adicional
                        </h6>
                        <div class="mb-3">
                            <label class="form-label" for="observaciones">Observaciones</label>
                            <textarea
                                id="observaciones"
                                name="observaciones"
                                class="form-control"
                                rows="4"
                                placeholder="Notas internas u observaciones generales"
                            ><?= htmlspecialchars($old['observaciones'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                        </div>
                    </section>
                </div>

                <aside class="col-lg-4 p-4 equipo-create-aside">
                    <section class="equipo-form-section">
                        <h6 class="text-uppercase fw-bold mb-3">
                            <i class="fa-solid fa-image me-2"></i>Escudo del equipo
                        </h6>

                        <label for="escudo" class="equipo-crest-picker" title="Cambiar escudo">
                            <img
                                id="equipoCrestPreview"
                                src="<?= ASSETS ?>/img/equipos/default.png"
                                alt="Vista previa del escudo del equipo"
                            >
                            <span class="equipo-crest-overlay">
                                <i class="fa-solid fa-camera"></i>
                                <small>Cambiar escudo</small>
                            </span>
                        </label>
                        <input
                            type="file"
                            id="escudo"
                            name="escudo"
                            accept="image/png,image/jpeg,image/webp"
                            hidden
                        >
                        <small class="form-text text-muted d-block mt-2">
                            Opcional. Formatos PNG, JPG o WEBP. Tamaño máximo: 2 MB.
                        </small>
                        <p class="equipo-crest-note mt-3 mb-0">
                            El escudo registrado aquí corresponde a la entidad equipo. Si su imagen cambia por temporada, se administrará posteriormente en la identidad de temporada.
                        </p>
                    </section>
                </aside>
            </div>

            <div class="equipo-form-actions d-flex justify-content-end gap-2 p-3">
                <a href="index.php?page=equipos" class="btn btn-light">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-floppy-disk me-1"></i>Guardar equipo
                </button>
            </div>
        </form>
    </div>
</div>

<?php startSection('scripts'); ?>
<script src="<?= ASSETS ?>/js/admin/equipos/equipo-create.js"></script>
<?php endSection('scripts'); ?>
