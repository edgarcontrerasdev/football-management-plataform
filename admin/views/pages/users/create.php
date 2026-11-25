<?php
$escape = static fn($value) => htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
?>
<div class="user-form-wrapper">
    <div class="user-form-card">
        <h3 class="form-title">Crear cuenta de usuario</h3>
        <?php if (!empty($data['errors'])): ?>
            <div class="alert alert-danger"><ul class="mb-0"><?php foreach ($data['errors'] as $error): ?><li><?= $escape($error) ?></li><?php endforeach; ?></ul></div>
        <?php endif; ?>
        <form id="formNewUser" action="index.php?page=users&action=save" method="POST" enctype="multipart/form-data">
            <div class="user-form-grid">
                <aside class="avatar-column">
                    <?php
                    $imageUploadId = 'avatar';
                    $imageUploadName = 'avatar';
                    $imageUploadSrc = ASSETS . '/img/avatars/default.png';
                    $imageUploadLabel = 'Agregar avatar';
                    $imageUploadAlt = 'Avatar del usuario';
                    $imageUploadHint = 'Opcional. JPG, PNG o WEBP. Máximo 2 MB.';
                    $imageUploadShape = 'circle';
                    include ROOT_PATH . '/admin/views/components/image-upload.php';
                    ?>
                </aside>
                <div class="form-column">
                    <div class="mb-3">
                        <label class="form-label" for="usuario">Nombre de usuario</label>
                        <input type="text" id="usuario" name="usuario" class="form-control" maxlength="100" autocomplete="username" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="password">Contraseña</label>
                        <input type="password" id="password" name="password" class="form-control" autocomplete="new-password" required>
                    </div>
                    <?php if (Auth::role() === 'ADMIN'): ?>
                        <div class="mb-3 d-none" id="boxOrganismo">
                            <label class="form-label" for="organismo_id">Organismo responsable</label>
                            <select name="organismo_id" id="organismo_id" class="form-select">
                                <option value="">Selecciona un organismo</option>
                                <option value="0">Liga independiente (sin organismo)</option>
                                <?php foreach (($organismos ?? []) as $organismo): ?>
                                    <option value="<?= (int) $organismo['id'] ?>"><?= $escape($organismo['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <small class="form-text">El administrador de organismo solo podrá gestionar este ámbito.</small>
                        </div>
                    <?php else: ?>
                        <input type="hidden" name="organismo_id" id="organismo_id" value="<?= (int) ($organismoId ?? 0) ?>">
                    <?php endif; ?>
                    <div class="mb-3">
                        <label class="form-label" for="rol_id">Rol de acceso</label>
                        <select name="rol_id" id="rol_id" class="form-select" required>
                            <option value="">Selecciona un rol</option>
                            <?php foreach (($roles ?? []) as $rol): ?>
                                <?php if (($rol['estado'] ?? 'activo') === 'activo'): ?>
                                    <option value="<?= (int) $rol['id'] ?>" data-role-key="<?= $escape(strtoupper($rol['clave'])) ?>"><?= $escape($rol['nombre']) ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3 d-none" id="boxLiga">
                        <label class="form-label" for="liga_id">Liga asignada</label>
                        <select name="liga_id" id="liga_id" class="form-select">
                            <option value="">Selecciona una liga</option>
                        </select>
                    </div>
                    <div class="mb-3 d-none" id="boxEquipo">
                        <label class="form-label" for="equipo_id">Equipo asignado</label>
                        <select name="equipo_id" id="equipo_id" class="form-select">
                            <option value="">Selecciona un equipo</option>
                        </select>
                    </div>
                    <p class="form-text">Los datos personales y documentos se administran desde el expediente de afiliación. Esta pantalla crea únicamente la cuenta y sus permisos de acceso.</p>
                    <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                        <a href="index.php?page=users" class="btn btn-light">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Crear usuario</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<?php startSection('scripts'); ?>
<script src="<?= BASE_URL ?>/public/assets/js/admin/users/user-create.js"></script>
<?php endSection('scripts'); ?>
