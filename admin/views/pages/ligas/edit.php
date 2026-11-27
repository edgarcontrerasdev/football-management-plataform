<?php
$old = $old ?? [];
$errors = $errors ?? [];
$valor = static fn(string $campo, $default = ''): string =>
    htmlspecialchars((string) ($old[$campo] ?? ($liga[$campo] ?? $default)), ENT_QUOTES, 'UTF-8');
?>
<div class="liga-edit-page">
    <div class="entity-detail-heading d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="page-title mb-1">Editar liga</h3>
            <p class="page-details mb-0">Actualiza la información general sin modificar sus temporadas, equipos ni organismos vinculados.</p>
        </div>
        <a href="index.php?page=ligas&action=detalle&id=<?= (int) $liga['id'] ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Volver al detalle
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
            <form action="index.php?page=ligas&action=actualizar&id=<?= (int) $liga['id'] ?>" method="POST" enctype="multipart/form-data">
                <h5 class="mb-3">Identidad de la liga</h5>
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label" for="nombre">Nombre de la liga <span class="text-danger">*</span></label>
                        <input class="form-control" id="nombre" name="nombre" maxlength="150" required value="<?= $valor('nombre') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="siglas">Siglas</label>
                        <input class="form-control" id="siglas" name="siglas" maxlength="30" value="<?= $valor('siglas') ?>">
                    </div>
                    <?php if (Auth::role() !== 'LIGA'): ?>
                    <div class="col-md-4">
                        <label class="form-label" for="estado">Estado</label>
                        <select class="form-select" id="estado" name="estado">
                            <option value="activo" <?= $valor('estado') === 'activo' ? 'selected' : '' ?>>Activa</option>
                            <option value="inactivo" <?= $valor('estado') === 'inactivo' ? 'selected' : '' ?>>Inactiva</option>
                            <option value="suspendido" <?= $valor('estado') === 'suspendido' ? 'selected' : '' ?>>Suspendida</option>
                        </select>
                    </div>
                    <?php else: ?>
                    <div class="col-md-4">
                        <label class="form-label">Estado</label>
                        <input class="form-control" value="<?= htmlspecialchars(ucfirst((string) ($liga['estado'] ?? 'activo')), ENT_QUOTES, 'UTF-8') ?>" disabled>
                    </div>
                    <?php endif; ?>
                </div>

                <hr class="my-4">
                <h5 class="mb-3">Logotipo de la liga</h5>
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <?php
                        $imageUploadId = 'logo';
                        $imageUploadName = 'logo';
                        $imageUploadSrc = !empty($liga['logo']) ? ASSETS . '/img/ligas/' . rawurlencode($liga['logo']) : ASSETS . '/img/ligas/default.png';
                        $imageUploadLabel = 'Cambiar logotipo';
                        $imageUploadAlt = 'Logotipo actual de la liga';
                        $imageUploadHint = 'Opcional. Si no seleccionas otra imagen, se conservará la actual. JPG, PNG o WEBP; máximo 2 MB.';
                        include ROOT_PATH . '/admin/views/components/image-upload.php';
                        ?>
                    </div>
                </div>

                <hr class="my-4">
                <h5 class="mb-3">Contacto</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="correo">Correo electrónico</label>
                        <input type="email" class="form-control" id="correo" name="correo" maxlength="150" value="<?= $valor('correo') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="telefono">Teléfono</label>
                        <input class="form-control" id="telefono" name="telefono" maxlength="30" value="<?= $valor('telefono') ?>">
                    </div>
                </div>

                <hr class="my-4">
                <h5 class="mb-3">Ubicación</h5>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label" for="direccion">Dirección</label>
                        <input class="form-control" id="direccion" name="direccion" maxlength="255" value="<?= $valor('direccion') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="colonia">Colonia</label>
                        <input class="form-control" id="colonia" name="colonia" maxlength="100" value="<?= $valor('colonia') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="municipio">Municipio</label>
                        <input class="form-control" id="municipio" name="municipio" maxlength="100" value="<?= $valor('municipio') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="estado_republica">Estado</label>
                        <input class="form-control" id="estado_republica" name="estado_republica" maxlength="100" value="<?= $valor('estado_republica') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="codigo_postal">Código postal</label>
                        <input class="form-control" id="codigo_postal" name="codigo_postal" maxlength="10" value="<?= $valor('codigo_postal') ?>">
                    </div>
                </div>

                <hr class="my-4">
                <h5 class="mb-3">Información adicional</h5>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label" for="redes_sociales">Redes sociales</label>
                        <textarea class="form-control" id="redes_sociales" name="redes_sociales" rows="2"><?= $valor('redes_sociales') ?></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="notas">Observaciones</label>
                        <textarea class="form-control" id="notas" name="notas" rows="3"><?= $valor('notas') ?></textarea>
                    </div>
                </div>

                <div class="form-actions d-flex flex-wrap justify-content-end gap-2 mt-4 pt-3">
                    <a href="index.php?page=ligas&action=detalle&id=<?= (int) $liga['id'] ?>" class="btn btn-light">Cancelar</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>
