<?php
$errors = $errors ?? [];
$old = $old ?? [];
$value = static fn(string $key, string $default = ''): string =>
    htmlspecialchars((string) ($old[$key] ?? $default), ENT_QUOTES, 'UTF-8');
?>
<div class="liga-create-page">
    <div class="admin-page-header mb-4">
        <div>
            <h3 class="admin-page-title mb-1">Nueva liga</h3>
            <p class="admin-page-description mb-0">Registra la identidad, contacto y ubicación de una liga. Podrás vincular posteriormente su organismo gestor y sus temporadas.</p>
        </div>
        <a href="index.php?page=ligas" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver al listado
        </a>
    </div>

    <?php if ($errors): ?>
        <div class="alert alert-danger" role="alert">
            <ul class="mb-0"><?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8') ?></li>
            <?php endforeach; ?></ul>
        </div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="index.php?page=ligas&action=store" method="POST" enctype="multipart/form-data">
                <section class="mb-4">
                    <h5 class="form-section-title mb-3"><i class="bi bi-flag me-2"></i>Identidad de la liga</h5>
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label" for="nombre">Nombre de la liga <span class="text-danger">*</span></label>
                            <input class="form-control" type="text" id="nombre" name="nombre" maxlength="150" required autofocus value="<?= $value('nombre') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="siglas">Siglas / clave corta</label>
                            <input class="form-control" type="text" id="siglas" name="siglas" maxlength="30" value="<?= $value('siglas') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="estado">Estado</label>
                            <select class="form-select" id="estado" name="estado">
                                <option value="activo" <?= $value('estado', 'activo') === 'activo' ? 'selected' : '' ?>>Activa</option>
                                <option value="inactivo" <?= $value('estado') === 'inactivo' ? 'selected' : '' ?>>Inactiva</option>
                                <option value="suspendido" <?= $value('estado') === 'suspendido' ? 'selected' : '' ?>>Suspendida</option>
                            </select>
                        </div>
                    </div>
                </section>

                <hr class="my-4">
                <section class="mb-4">
                    <h5 class="form-section-title mb-3"><i class="bi bi-image me-2"></i>Logotipo</h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <?php
                            $imageUploadId = 'logo';
                            $imageUploadName = 'logo';
                            $imageUploadSrc = ASSETS . '/img/ligas/default.png';
                            $imageUploadLabel = 'Cambiar logotipo';
                            $imageUploadAlt = 'Logotipo de la liga';
                            $imageUploadHint = 'Opcional. JPG, PNG o WEBP. Máximo 2 MB.';
                            include ROOT_PATH . '/admin/views/components/image-upload.php';
                            ?>
                        </div>
                    </div>
                </section>

                <hr class="my-4">
                <section class="mb-4">
                    <h5 class="form-section-title mb-3"><i class="bi bi-person-lines-fill me-2"></i>Datos de contacto</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="correo">Correo electrónico</label>
                            <input class="form-control" type="email" id="correo" name="correo" maxlength="150" value="<?= $value('correo') ?>" placeholder="contacto@liga.org">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="telefono">Teléfono</label>
                            <input class="form-control" type="text" id="telefono" name="telefono" maxlength="30" value="<?= $value('telefono') ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="redes_sociales">Redes sociales</label>
                            <textarea class="form-control" id="redes_sociales" name="redes_sociales" rows="2" placeholder="Sitio web o perfiles oficiales"><?= $value('redes_sociales') ?></textarea>
                        </div>
                    </div>
                </section>

                <hr class="my-4">
                <section class="mb-4">
                    <h5 class="form-section-title mb-3"><i class="bi bi-geo-alt me-2"></i>Ubicación</h5>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label" for="direccion">Dirección</label>
                            <input class="form-control" id="direccion" name="direccion" maxlength="255" value="<?= $value('direccion') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="colonia">Colonia</label>
                            <input class="form-control" id="colonia" name="colonia" maxlength="100" value="<?= $value('colonia') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="municipio">Municipio</label>
                            <input class="form-control" id="municipio" name="municipio" maxlength="100" value="<?= $value('municipio') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="estado_republica">Estado / entidad federativa</label>
                            <input class="form-control" id="estado_republica" name="estado_republica" maxlength="100" value="<?= $value('estado_republica') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="codigo_postal">Código postal</label>
                            <input class="form-control" id="codigo_postal" name="codigo_postal" maxlength="10" value="<?= $value('codigo_postal') ?>">
                        </div>
                    </div>
                </section>

                <hr class="my-4">
                <section>
                    <h5 class="form-section-title mb-3"><i class="bi bi-card-text me-2"></i>Observaciones</h5>
                    <label class="form-label" for="notas">Notas internas</label>
                    <textarea class="form-control" id="notas" name="notas" rows="3"><?= $value('notas') ?></textarea>
                </section>

                <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                    <a href="index.php?page=ligas" class="btn btn-light">Cancelar</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Guardar liga</button>
                </div>
            </form>
        </div>
    </div>
</div>

