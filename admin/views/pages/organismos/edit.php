<?php
$old = $old ?? [];
$valor = static fn(string $campo, $default = '') => $old[$campo] ?? ($organismo[$campo] ?? $default);
?>
<div class="organismo-edit-page"><div class="entity-detail-heading mb-4"><div><h3 class="page-title mb-1">Editar organismo</h3>
<p class="page-details mb-0">Actualiza los datos generales sin alterar sus relaciones con ligas.</p></div></div>
<?php if (!empty($error)): ?><div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
<div class="card border-0 shadow-sm"><div class="card-body p-4">
<form action="index.php?page=organismos&action=actualizar&id=<?= (int) $organismo['id'] ?>" method="POST" enctype="multipart/form-data">
<div class="row g-3">
<div class="col-md-8"><label class="form-label" for="nombre">Nombre *</label><input class="form-control" id="nombre" name="nombre" maxlength="150" required value="<?= htmlspecialchars((string) $valor('nombre'), ENT_QUOTES, 'UTF-8') ?>"></div>
<div class="col-md-4"><label class="form-label" for="siglas">Siglas</label><input class="form-control" id="siglas" name="siglas" maxlength="30" value="<?= htmlspecialchars((string) $valor('siglas'), ENT_QUOTES, 'UTF-8') ?>"></div>
<div class="col-md-4"><label class="form-label" for="tipo">Tipo *</label><select class="form-select" id="tipo" name="tipo" required><?php foreach (['asociacion'=>'Asociación','federacion'=>'Federación','comite'=>'Comité','otro'=>'Otro'] as $v=>$label): ?><option value="<?= $v ?>" <?= $valor('tipo') === $v ? 'selected' : '' ?>><?= $label ?></option><?php endforeach; ?></select></div>
<div class="col-md-4"><label class="form-label" for="alcance_territorial">Alcance territorial</label><select class="form-select" id="alcance_territorial" name="alcance_territorial"><option value="">Sin especificar</option><?php foreach (['local'=>'Local','municipal'=>'Municipal','estatal'=>'Estatal','regional'=>'Regional','nacional'=>'Nacional','internacional'=>'Internacional'] as $v=>$label): ?><option value="<?= $v ?>" <?= $valor('alcance_territorial') === $v ? 'selected' : '' ?>><?= $label ?></option><?php endforeach; ?></select></div>
<div class="col-md-4"><label class="form-label" for="estado">Estado</label><select class="form-select" id="estado" name="estado"><option value="activo" <?= $valor('estado') === 'activo' ? 'selected' : '' ?>>Activo</option><option value="inactivo" <?= $valor('estado') === 'inactivo' ? 'selected' : '' ?>>Inactivo</option></select></div>
<div class="col-md-4"><label class="form-label" for="fecha_creacion">Fecha de creación</label><input type="date" class="form-control" id="fecha_creacion" name="fecha_creacion" value="<?= htmlspecialchars((string) $valor('fecha_creacion'), ENT_QUOTES, 'UTF-8') ?>"></div>
<div class="col-md-4"><label class="form-label" for="correo">Correo</label><input type="email" class="form-control" id="correo" name="correo" maxlength="150" value="<?= htmlspecialchars((string) $valor('correo'), ENT_QUOTES, 'UTF-8') ?>"></div>
<div class="col-md-4"><label class="form-label" for="telefono">Teléfono</label><input class="form-control" id="telefono" name="telefono" maxlength="30" value="<?= htmlspecialchars((string) $valor('telefono'), ENT_QUOTES, 'UTF-8') ?>"></div>
<div class="col-md-4"><label class="form-label" for="sitio_web">Sitio web</label><input type="url" class="form-control" id="sitio_web" name="sitio_web" maxlength="255" value="<?= htmlspecialchars((string) $valor('sitio_web'), ENT_QUOTES, 'UTF-8') ?>"></div>
<div class="col-12"><label class="form-label" for="descripcion">Descripción</label><textarea class="form-control" id="descripcion" name="descripcion" rows="3"><?= htmlspecialchars((string) $valor('descripcion'), ENT_QUOTES, 'UTF-8') ?></textarea></div>
</div>
<div class="col-12 mt-3">
    <h5 class="form-section-title mb-3"><i class="fa-solid fa-image me-2"></i>Logotipo del organismo</h5>
    <?php
    $imageUploadId = 'logo';
    $imageUploadName = 'logo';
    $imageUploadSrc = !empty($organismo['logo']) ? ASSETS . '/img/organismos/' . rawurlencode($organismo['logo']) : ASSETS . '/img/organismos/default.png';
    $imageUploadLabel = 'Cambiar logotipo';
    $imageUploadAlt = 'Logotipo del organismo';
    $imageUploadHint = 'Opcional. JPG, PNG o WEBP. Máximo 2 MB.';
    include ROOT_PATH . '/admin/views/components/image-upload.php';
    ?>
</div>
<div class="d-flex justify-content-end gap-2 mt-4"><a href="index.php?page=organismos&action=detalles&id=<?= (int) $organismo['id'] ?>" class="btn btn-light">Cancelar</a><button class="btn btn-primary" type="submit"><i class="fas fa-save me-1"></i>Guardar cambios</button></div>
</form></div></div>

</div>
