<?php
    $old = $_SESSION['club_old'] ?? [];
    unset($_SESSION['club_old']);
?>

<h3 class="users-title mb-1">NUEVO CLUB</h3>

<div class="context-bar mb-3">
    <div class="context-icon">
        <i class="fa-solid fa-shield"></i>
    </div>
    <div>
        <strong>Registro de entidad club</strong>
        <p class="mb-0">Registra la identidad permanente del club. Su relación con los equipos se administrará por separado.</p>
    </div>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger" role="alert">
        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
    </div>
<?php endif; ?>

<div class="card shadow-sm border-0 club-create-card entity-form-card">
    <div class="card-body p-0">
        <form id="formCreateClub" action="index.php?page=clubes&action=store" method="POST" enctype="multipart/form-data">
            <div class="row g-0 entity-form-grid">
                <div class="col-lg-8 p-4 entity-form-main">
                    <section class="club-form-section entity-form-section">
                        <h6 class="text-uppercase fw-bold mb-3">
                            <i class="fa-solid fa-circle-info me-2"></i>Identidad del club
                        </h6>

                        <div class="mb-3">
                            <label class="form-label" for="nombre">Nombre del club <span class="text-danger">*</span></label>
                            <input type="text" id="nombre" name="nombre" class="form-control" maxlength="150"
                                value="<?= htmlspecialchars($old['nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                placeholder="Ej. Club Deportivo" required autofocus>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="siglas">Siglas o clave corta</label>
                            <input type="text" id="siglas" name="siglas" class="form-control" maxlength="30"
                                value="<?= htmlspecialchars($old['siglas'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                placeholder="Ej. CD">
                        </div>
                    </section>

                    <section class="club-form-section entity-form-section mt-4">
                        <h6 class="text-uppercase fw-bold mb-3">
                            <i class="fa-solid fa-file-lines me-2"></i>Información adicional
                        </h6>
                        <div class="mb-3">
                            <label class="form-label" for="observaciones">Observaciones</label>
                            <textarea id="observaciones" name="observaciones" class="form-control" rows="4"
                                placeholder="Notas internas u observaciones generales"><?= htmlspecialchars($old['observaciones'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                        </div>
                    </section>
                </div>

                <aside class="col-lg-4 p-4 club-create-aside entity-form-aside">
                    <section class="club-form-section">
                        <h6 class="text-uppercase fw-bold mb-3">
                            <i class="fa-solid fa-image me-2"></i>Logo del club
                        </h6>

                        <?php
                        $imageUploadId = 'logo';
                        $imageUploadName = 'logo';
                        $imageUploadSrc = ASSETS . '/img/ligas/default.png';
                        $imageUploadLabel = 'Cambiar logotipo';
                        $imageUploadAlt = 'Logotipo del club';
                        $imageUploadHint = 'Opcional. JPG, PNG o WEBP. Máximo 2 MB.';
                        include ROOT_PATH . '/admin/views/components/image-upload.php';
                        ?>
                    </section>
                </aside>
            </div>

            <div class="club-form-actions entity-form-actions d-flex justify-content-end gap-2 p-3">
                <a href="index.php?page=clubes" class="btn btn-light">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-floppy-disk me-1"></i>Guardar club
                </button>
            </div>
        </form>
    </div>
</div>

<?php startSection('scripts'); ?>
<script src="<?= ASSETS ?>/js/admin/clubes/club-create.js"></script>
<?php endSection('scripts'); ?>
