<?php
$escape = static fn($value) => htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
$avatarActual = !empty($user['avatar']) ? ASSETS . '/img/avatars/' . rawurlencode($user['avatar']) : ASSETS . '/img/avatars/default.png';
$errores = $data['errors'] ?? [];
?>
<div class="user-form-wrapper">
    <div class="w-100" style="max-width:1100px">
        <div class="admin-page-header mb-3">
            <div>
                <h3 class="admin-page-title">Editar usuario</h3>
                <p class="admin-page-description">Actualiza sus datos de acceso, contacto y asignación.</p>
            </div>
            <a href="index.php?page=users" class="btn btn-light"><i class="fa-solid fa-arrow-left me-1"></i>Volver al listado</a>
        </div>

        <?php if ($errores): ?>
            <div class="alert alert-danger" role="alert">
                <strong>Revisa la información:</strong>
                <ul class="user-form-errors-list mb-0">
                    <?php foreach ($errores as $error): ?><li><?= $escape($error) ?></li><?php endforeach; ?>
                </ul>
            </div>
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
                            <label class="form-label" for="nombre">Nombre completo</label>
                            <input type="text" id="nombre" name="nombre" class="form-control" maxlength="150" required value="<?= $escape($user['nombre'] ?? '') ?>">
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="correo">Correo</label>
                                <input type="email" id="correo" name="correo" class="form-control" maxlength="150" value="<?= $escape($user['correo'] ?? '') ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="telefono">Teléfono</label>
                                <input type="text" id="telefono" name="telefono" class="form-control" maxlength="30" value="<?= $escape($user['telefono'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="usuario">Nombre de usuario</label>
                            <input type="text" id="usuario" name="usuario" class="form-control" maxlength="80" required value="<?= $escape($user['usuario']) ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="rol_id">Rol</label>
                            <select name="rol_id" id="rol_id" class="form-select" required>
                                <?php foreach (($roles ?? []) as $rol): ?>
                                    <option value="<?= (int) $rol['id'] ?>" <?= (int) $rol['id'] === (int) $user['rol_id'] ? 'selected' : '' ?>><?= $escape($rol['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3 <?= in_array((int) $user['rol_id'], [3,4], true) ? '' : 'd-none' ?>" id="boxLiga">
                            <label class="form-label" for="liga_id">Liga</label>
                            <select name="liga_id" id="liga_id" class="form-select">
                                <option value="">Seleccione liga</option>
                                <?php foreach (($ligas ?? []) as $liga): ?>
                                    <option value="<?= (int) $liga['id'] ?>" <?= (int) ($user['liga_id'] ?? 0) === (int) $liga['id'] ? 'selected' : '' ?>><?= $escape($liga['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3 <?= (int) $user['rol_id'] === 4 ? '' : 'd-none' ?>" id="boxEquipo">
                            <label class="form-label" for="equipo_id">Equipo</label>
                            <select name="equipo_id" id="equipo_id" class="form-select">
                                <option value="">Seleccione equipo</option>
                                <?php foreach (($equipos ?? []) as $equipo): ?>
                                    <option value="<?= (int) $equipo['id'] ?>" data-liga="<?= (int) ($equipo['liga_id'] ?? 0) ?>" <?= (int) ($user['equipo_id'] ?? 0) === (int) $equipo['id'] ? 'selected' : '' ?>><?= $escape($equipo['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="password">Nueva contraseña <small class="text-muted">(opcional)</small></label>
                            <input type="password" id="password" name="password" class="form-control" autocomplete="new-password" placeholder="Dejar vacío para conservar la actual">
                        </div>
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
<script>
(function () {
    const role = document.getElementById('rol_id');
    const leagueBox = document.getElementById('boxLiga');
    const teamBox = document.getElementById('boxEquipo');
    const league = document.getElementById('liga_id');
    const team = document.getElementById('equipo_id');
    if (role && leagueBox && teamBox) {
        const syncRole = () => {
            const value = Number(role.value);
            leagueBox.classList.toggle('d-none', ![3,4].includes(value));
            teamBox.classList.toggle('d-none', value !== 4);
            if ([1,2].includes(value)) { league.value = ''; team.value = ''; }
            if (value === 3) team.value = '';
        };
        role.addEventListener('change', syncRole);
        syncRole();
    }
    if (league && team) {
        const filterTeams = () => {
            const selectedLeague = league.value;
            Array.from(team.options).forEach(option => {
                if (!option.value) return;
                option.hidden = selectedLeague !== '' && option.dataset.liga !== selectedLeague;
            });
            if (team.selectedOptions[0]?.hidden) team.value = '';
        };
        league.addEventListener('change', filterTeams);
        filterTeams();
    }
})();
</script>
<?php endSection('scripts'); ?>
