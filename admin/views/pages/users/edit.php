<?php
$escape = static fn($value) => htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
$avatarActual = !empty($user['avatar']) ? ASSETS . '/img/avatars/' . rawurlencode($user['avatar']) : ASSETS . '/img/avatars/default.png';
$errores = $data['errors'] ?? [];
$rolActual = null;
foreach (($roles ?? []) as $rol) {
    if ((int) $rol['id'] === (int) ($user['rol_id'] ?? 0)) { $rolActual = strtoupper($rol['clave']); break; }
}
?>
<div class="user-form-wrapper">
    <div class="w-100" style="max-width:1100px">
        <div class="admin-page-header mb-3">
            <div><h3 class="admin-page-title">Editar usuario</h3><p class="admin-page-description">Actualiza la cuenta, el rol y sus alcances de acceso.</p></div>
            <a href="index.php?page=users" class="btn btn-light"><i class="fa-solid fa-arrow-left me-1"></i>Volver al listado</a>
        </div>
        <?php if ($errores): ?>
            <div class="alert alert-danger" role="alert"><ul class="mb-0"><?php foreach ($errores as $error): ?><li><?= $escape($error) ?></li><?php endforeach; ?></ul></div>
        <?php endif; ?>
        <div class="user-form-card">
            <form id="formEditUser" action="index.php?page=users&action=update&id=<?= (int) $user['id'] ?>" method="POST" enctype="multipart/form-data">
                <div class="user-form-grid">
                    <aside class="avatar-column">
                        <?php
                        $imageUploadId = 'avatar';
                        $imageUploadName = 'avatar';
                        $imageUploadSrc = $avatarActual;
                        $imageUploadLabel = 'Cambiar avatar';
                        $imageUploadAlt = 'Avatar actual del usuario';
                        $imageUploadHint = 'Opcional. Si no seleccionas otra imagen, se conservará la actual. JPG, PNG o WEBP; máximo 2 MB.';
                        $imageUploadShape = 'circle';
                        include ROOT_PATH . '/admin/views/components/image-upload.php';
                        ?>
                    </aside>
                    <div class="form-column">
                        <div class="mb-3">
                            <label class="form-label" for="usuario">Nombre de usuario</label>
                            <input type="text" id="usuario" name="usuario" class="form-control" maxlength="100" required value="<?= $escape($user['usuario']) ?>">
                        </div>
                        <?php if (Auth::role() === 'ADMIN'): ?>
                            <div class="mb-3 <?= in_array($rolActual, ['ORGANISMO','LIGA','EQUIPO'], true) ? '' : 'd-none' ?>" id="boxOrganismo">
                                <label class="form-label" for="organismo_id">Organismo responsable</label>
                                <select name="organismo_id" id="organismo_id" class="form-select">
                                    <option value="">Selecciona un organismo</option>
                                    <?php foreach (($organismos ?? []) as $organismo): ?>
                                        <option value="<?= (int) $organismo['id'] ?>" <?= (int) ($user['organismo_id'] ?? 0) === (int) $organismo['id'] ? 'selected' : '' ?>><?= $escape($organismo['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php else: ?>
                            <input type="hidden" name="organismo_id" id="organismo_id" value="<?= (int) ($organismoId ?? 0) ?>">
                        <?php endif; ?>
                        <div class="mb-3">
                            <label class="form-label" for="rol_id">Rol de acceso</label>
                            <select name="rol_id" id="rol_id" class="form-select" required>
                                <?php foreach (($roles ?? []) as $rol): ?>
                                    <?php if (($rol['estado'] ?? 'activo') === 'activo'): ?>
                                        <option value="<?= (int) $rol['id'] ?>" data-role-key="<?= $escape(strtoupper($rol['clave'])) ?>" <?= (int) $rol['id'] === (int) $user['rol_id'] ? 'selected' : '' ?>><?= $escape($rol['nombre']) ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3 <?= in_array($rolActual, ['LIGA','EQUIPO'], true) ? '' : 'd-none' ?>" id="boxLiga">
                            <label class="form-label" for="liga_id">Liga asignada</label>
                            <select name="liga_id" id="liga_id" class="form-select">
                                <option value="">Selecciona una liga</option>
                                <?php foreach (($ligas ?? []) as $liga): ?>
                                    <option value="<?= (int) $liga['id'] ?>" data-organismo="<?= (int) ($liga['organismo_id'] ?? 0) ?>" <?= (int) ($user['liga_id'] ?? 0) === (int) $liga['id'] ? 'selected' : '' ?>><?= $escape($liga['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3 <?= $rolActual === 'EQUIPO' ? '' : 'd-none' ?>" id="boxEquipo">
                            <label class="form-label" for="equipo_id">Equipo asignado</label>
                            <select name="equipo_id" id="equipo_id" class="form-select">
                                <option value="">Selecciona un equipo</option>
                                <?php foreach (($equipos ?? []) as $equipo): ?>
                                    <option value="<?= (int) $equipo['id'] ?>" data-liga="<?= (int) $equipo['liga_id'] ?>" <?= (int) ($user['equipo_id'] ?? 0) === (int) $equipo['id'] ? 'selected' : '' ?>><?= $escape($equipo['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="password">Nueva contraseña <small class="text-muted">(opcional)</small></label>
                            <input type="password" id="password" name="password" class="form-control" autocomplete="new-password" placeholder="Dejar vacío para conservar la actual">
                        </div>
                        <p class="form-text">Los datos personales pertenecen al expediente de afiliación; aquí se administran únicamente la cuenta y sus alcances.</p>
                        <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                            <a href="index.php?page=users" class="btn btn-light">Cancelar</a>
                            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i>Guardar cambios</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php startSection('scripts'); ?>
<script src="<?= BASE_URL ?>/public/assets/js/admin/users/user-edit.js"></script>
<?php endSection('scripts'); ?>
