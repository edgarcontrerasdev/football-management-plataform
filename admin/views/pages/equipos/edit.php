<?php
$old = $old ?? [];
$valor = static fn(string $campo, $default = ''): string =>
    htmlspecialchars((string) ($old[$campo] ?? $default), ENT_QUOTES, 'UTF-8');
$escudo = $equipo['escudo'] ?? 'default.png';
$escudoUrl = ASSETS . '/img/equipos/' . rawurlencode($escudo ?: 'default.png');
?>
<div class="equipos-edit-page">
    <div class="admin-page-header mb-4">
        <div>
            <h3 class="admin-page-title mb-1">Editar equipo</h3>
            <p class="admin-page-description mb-0">Actualiza los datos permanentes y el escudo de esta entidad equipo.</p>
        </div>
        <a href="index.php?page=equipos&action=detalles&id=<?= (int) $equipo['id'] ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver al equipo
        </a>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger" role="alert"><?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <div class="card border-0 equipo-create-card">
        <div class="card-body p-0">
            <form id="formEditEquipo" action="index.php?page=equipos&action=actualizar&id=<?= (int) $equipo['id'] ?>" method="POST" enctype="multipart/form-data">
                <div class="row g-0">
                    <div class="col-lg-8 p-4">
                        <section class="equipo-form-section">
                            <h6 class="text-uppercase fw-bold mb-3"><i class="fa-solid fa-circle-info me-2"></i>Identidad del equipo</h6>
                            <div class="mb-3">
                                <label class="form-label" for="nombre">Nombre del equipo <span class="text-danger">*</span></label>
                                <input type="text" id="nombre" name="nombre" class="form-control" maxlength="150" required value="<?= $valor('nombre', $equipo['equipo'] ?? '') ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="siglas">Siglas o clave corta</label>
                                <input type="text" id="siglas" name="siglas" class="form-control" maxlength="30" value="<?= $valor('siglas', $equipo['siglas'] ?? '') ?>">
                            </div>
                        </section>

                        <section class="equipo-form-section mt-4">
                            <h6 class="text-uppercase fw-bold mb-3"><i class="fa-solid fa-file-lines me-2"></i>Información adicional</h6>
                            <div class="mb-3">
                                <label class="form-label" for="observaciones">Observaciones</label>
                                <textarea id="observaciones" name="observaciones" class="form-control" rows="4"><?= $valor('observaciones', $equipo['observaciones'] ?? '') ?></textarea>
                            </div>
                        </section>
                    </div>

                    <aside class="col-lg-4 p-4 equipo-create-aside">
                        <section class="equipo-form-section">
                            <h6 class="text-uppercase fw-bold mb-3"><i class="fa-solid fa-image me-2"></i>Escudo del equipo</h6>
                            <?php
                            $imageUploadId = 'escudo';
                            $imageUploadName = 'escudo';
                            $imageUploadSrc = $escudoUrl;
                            $imageUploadLabel = 'Cambiar escudo';
                            $imageUploadAlt = 'Escudo actual del equipo';
                            $imageUploadHint = 'Opcional. Si no seleccionas otra imagen, se conservará la actual. JPG, PNG o WEBP; máximo 2 MB.';
                            include ROOT_PATH . '/admin/views/components/image-upload.php';
                            ?>
                            <p class="equipo-crest-note mt-3 mb-0">El escudo corresponde a la identidad permanente del equipo. Los cambios de imagen por temporada se administrarán por separado.</p>
                        </section>
                    </aside>
                </div>

                <div class="equipo-form-actions d-flex flex-wrap justify-content-end gap-2 p-3">
                    <a href="index.php?page=equipos&action=detalles&id=<?= (int) $equipo['id'] ?>" class="btn btn-light">Cancelar</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

