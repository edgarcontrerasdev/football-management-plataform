<?php if (!empty($data['errors'])): ?>
    <div class="alert alert-danger">
        <strong>Ocurrieron errores:</strong>
        <ul style="margin:0;">
            <?php foreach ($data['errors'] as $error): ?>
                <li><?= htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="user-form-wrapper">
    <div class="user-form-card">
        <h3 class="form-title">Editar usuario</h3>
        <form id="formEditUser"
              action="index.php?page=users&action=update&id=<?= $user['id'] ?>"
              method="POST"
              enctype="multipart/form-data">

            <div class="user-form-grid">

                <!-- AVATAR -->
                <div class="avatar-column">
                    <label for="avatar" class="avatar-box">
                        <img id="avatarPreview"
                             src="/afec/public/assets/img/avatars/<?= $user['avatar'] ?: 'default.png' ?>">
                        <div class="avatar-overlay">
                            <span>📷</span>
                            <small>Cambiar avatar</small>
                        </div>
                    </label>

                    <input type="file" name="avatar" id="avatar"
                           accept="image/png,image/jpeg" hidden>

                    <p>
                        Imagen de perfil del usuario.<br>
                        <strong>PNG, JPG</strong><br>
                        Máx. recomendado <strong>2MB</strong>
                    </p>
                </div>

                <!-- FORM -->
                <div class="form-column">

                    <div class="mb-3">
                        <label class="form-label">Nombre completo</label>
                        <input type="text" name="nombre"
                               class="form-control"
                               value="<?= htmlspecialchars($user['nombre']) ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Correo</label>
                        <input type="text" name="correo"
                               class="form-control"
                               value="<?= htmlspecialchars($user['correo']) ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="telefono"
                               class="form-control"
                               value="<?= htmlspecialchars($user['telefono']) ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Rol</label>
                        <select name="rol_id" id="rol_id"
                                class="form-control" required>
                            <?php foreach($roles as $rol): ?>
                                <option value="<?= $rol['id'] ?>"
                                    <?= $rol['id'] == $user['rol_id'] ? 'selected' : '' ?>>
                                    <?= $rol['nombre'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3 <?= !$user['liga_id'] ? 'd-none' : '' ?>" id="boxLiga">
                        <label>Liga</label>
                        <select name="liga_id" id="liga_id" class="form-select">
                            <option value="">Seleccione liga</option>
                        </select>
                    </div>

                    <div class="mb-3 <?= !$user['equipo_id'] ? 'd-none' : '' ?>" id="boxEquipo">
                        <label>Equipo</label>
                        <select name="equipo_id" id="equipo_id" class="form-select">
                            <option value="">Seleccione equipo</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Usuario</label>
                        <input type="text" name="usuario"
                               class="form-control"
                               value="<?= htmlspecialchars($user['usuario']) ?>"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            Nueva contraseña <small>(opcional)</small>
                        </label>
                        <input type="password" name="password"
                               class="form-control"
                               placeholder="Dejar vacío para no cambiar">
                    </div>

                    <button type="submit" class="btn btn-primary">
                        Actualizar Usuario
                    </button>
                    <a href="index.php?page=users" class="btn btn-secondary">
                        Cancelar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
