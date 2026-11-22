<?php
$old = $old ?? [];
$valor = static fn(string $campo, $predeterminado = '') => $old[$campo] ?? ($club[$campo] ?? $predeterminado);
$logoActual = !empty($club['logo']) ? ASSETS . '/img/clubes/' . rawurlencode($club['logo']) : ASSETS . '/img/ligas/default.png';
?>
<h3 class="users-title mb-1">EDITAR CLUB</h3>
<div class="context-bar mb-3"><div class="context-icon"><i class="fa-solid fa-shield"></i></div><div><strong>Actualizar información del club</strong><p class="mb-0">Modifica los datos generales. Las relaciones con sus equipos no se modificarán.</p></div></div>
<?php if (!empty($error)): ?><div class="alert alert-danger" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
<div class="card shadow-sm border-0 club-create-card"><div class="card-body p-0">
<form id="formEditClub" action="index.php?page=clubes&action=actualizar&id=<?= (int) $club['id'] ?>" method="POST" enctype="multipart/form-data">
<div class="row g-0"><div class="col-lg-8 p-4"><section class="club-form-section">
<h6 class="text-uppercase fw-bold mb-3"><i class="fa-solid fa-circle-info me-2"></i>Identidad del club</h6>
<div class="mb-3"><label class="form-label" for="nombre">Nombre del club <span class="text-danger">*</span></label><input type="text" id="nombre" name="nombre" class="form-control" maxlength="150" value="<?= htmlspecialchars((string) $valor('nombre'), ENT_QUOTES, 'UTF-8') ?>" required autofocus></div>
<div class="mb-3"><label class="form-label" for="siglas">Siglas o clave corta</label><input type="text" id="siglas" name="siglas" class="form-control" maxlength="30" value="<?= htmlspecialchars((string) $valor('siglas'), ENT_QUOTES, 'UTF-8') ?>"></div>
<div class="mb-3"><label class="form-label" for="estado">Estado</label><select id="estado" name="estado" class="form-select" required><option value="activo" <?= $valor('estado') === 'activo' ? 'selected' : '' ?>>Activo</option><option value="inactivo" <?= $valor('estado') === 'inactivo' ? 'selected' : '' ?>>Inactivo</option></select></div>
</section><section class="club-form-section mt-4"><h6 class="text-uppercase fw-bold mb-3"><i class="fa-solid fa-file-lines me-2"></i>Información adicional</h6><div class="mb-3"><label class="form-label" for="observaciones">Observaciones</label><textarea id="observaciones" name="observaciones" class="form-control" rows="4"><?= htmlspecialchars((string) $valor('observaciones'), ENT_QUOTES, 'UTF-8') ?></textarea></div></section></div>
<aside class="col-lg-4 p-4 club-create-aside"><section class="club-form-section"><h6 class="text-uppercase fw-bold mb-3"><i class="fa-solid fa-image me-2"></i>Escudo del club</h6>
<label for="logo" class="club-logo-picker" title="Cambiar escudo"><img id="clubLogoPreview" src="<?= htmlspecialchars($logoActual, ENT_QUOTES, 'UTF-8') ?>" alt="Escudo actual del club"><span class="club-logo-overlay"><i class="fa-solid fa-camera"></i><small>Cambiar escudo</small></span></label>
<input type="file" id="logo" name="logo" accept="image/png,image/jpeg,image/webp" hidden><small class="form-text text-muted d-block mt-2">Opcional. Si no seleccionas otro archivo, se conservará el escudo actual. JPG, PNG o WEBP; máximo 2 MB.</small></section></aside></div>
<div class="club-form-actions d-flex justify-content-end gap-2 p-3"><a href="index.php?page=clubes&action=detalles&id=<?= (int) $club['id'] ?>" class="btn btn-light">Cancelar</a><button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i>Guardar cambios</button></div>
</form></div></div>
<?php startSection('scripts'); ?><script src="<?= ASSETS ?>/js/admin/clubes/club-edit.js"></script><?php endSection('scripts'); ?>
