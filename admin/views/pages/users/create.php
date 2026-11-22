<?php if (!empty($data['errors'])): ?>
    <div class="alert alert-danger">
        <strong>Ocurrieron errores:</strong>
        <ul class="user-form-errors-list">
            <?php foreach ($data['errors'] as $error): ?>
                <li><?= htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="user-form-wrapper">

    <div class="user-form-card">

        <h3 class="form-title">Registro de usuario</h3>

        <form id="formNewUser" action="index.php?page=users&action=save" method="POST" enctype="multipart/form-data">
           
            <div class="user-form-grid">

                <!-- Avatar -->
                <div class="avatar-column">
                    <label for="avatar" class="avatar-box">
                        <img id="avatarPreview"
                             src="/afec/public/assets/img/avatars/default.png">
                        <div class="avatar-overlay">
                            <span>📷</span>
                            <small>Cambiar avatar</small>
                        </div>
                    </label>

                    <input type="file" name="avatar" id="avatar" accept="image/png,image/jpeg" hidden>
                    <p>
                        Imagen de perfil del usuario.<br>
                        <strong>PNG, JPG</strong><br>
                        Máx. recomendado <strong>2MB</strong>
                    </p>
                </div>
        
            <!-- 🟩 Columna formulario -->
            <div class="form-column">

                <div class="mb-3">
                    <label class="form-label">Nombre completo</label>
                    <input type="text" name="nombre" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label" >Correo</label>
                    <input type="text" name="correo" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label" >Telefono</label>
                    <input type="text" name="telefono" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">¿Qué rol tendrá el usuario? </label>
                    <select name="rol_id" id="rol_id" class="form-control" required>
                        <option value="0">***selecciona un rol para el usuario ***</option>
                        <?php
                        foreach($data['roles'] as $rol):?>
                        <option value="<?= $rol['id']; ?>"> <?= $rol['nombre']; ?> </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3 d-none" id="boxLiga">
                    <label for="liga_id">Liga </label>
                    <select name="liga_id" id="liga_id" class="form-select">
                        <option value="">Seleccione liga</option>
                    </select>
                </div>

                <div class="mb-3 d-none" id="boxEquipo" class="d-none">
                    <label for="equipo_id">Equipo</label>
                    <select name="equipo_id" id="equipo_id" class="form-select">
                        <option value="">Seleccione equipo</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Usuario</label>
                    <input type="text" name="usuario" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Contraseña</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <!-- botones -->
                <button type="submit" class="btn btn-primary">Crear Usuario</button>
                <a href="index.php?page=users" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
        
    </div>

</div>

<?php startSection('scripts'); ?>
<script src="<?= BASE_URL ?>/public/assets/js/admin/users/user-create.js"></script>
<?php endSection('scripts'); ?>
