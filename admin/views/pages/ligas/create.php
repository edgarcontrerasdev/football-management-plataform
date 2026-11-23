<?php
$errors = $errors ?? [];
$old = $old ?? [];
$value = static fn(string $key, string $default = ''): string =>
    htmlspecialchars((string) ($old[$key] ?? $default), ENT_QUOTES, 'UTF-8');
?>

<div class="container mt-4">
    <div class="mb-3">
        <h3>Nueva liga</h3>
        <p class="text-helper mb-1">
            <i class="fas fa-circle-info"></i>
            Captura la información básica de la liga.
        </p>
        <p class="text-helper mb-0">
            Podrás completar posteriormente su dirección, directivos y documentos.
        </p>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger" role="alert">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8') ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="index.php?page=ligas&action=store" method="POST">
                <div class="mb-3">
                    <label class="form-label" for="nombre">
                        Nombre de la liga <span class="text-danger">*</span>
                    </label>
                    <input type="text" id="nombre" name="nombre" class="form-control"
                           maxlength="150" value="<?= $value('nombre') ?>" required autofocus>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="siglas">Siglas / Clave corta</label>
                    <input type="text" id="siglas" name="siglas" class="form-control"
                           maxlength="30" value="<?= $value('siglas') ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label" for="estado">Estado</label>
                    <select id="estado" name="estado" class="form-select">
                        <option value="activo" <?= $value('estado', 'activo') === 'activo' ? 'selected' : '' ?>>Activa</option>
                        <option value="inactivo" <?= $value('estado') === 'inactivo' ? 'selected' : '' ?>>Inactiva</option>
                        <option value="suspendido" <?= $value('estado') === 'suspendido' ? 'selected' : '' ?>>Suspendida</option>
                    </select>
                </div>

                <hr>

                <div class="mb-3">
                    <label class="form-label" for="contacto_email">Correo de contacto</label>
                    <input type="email" id="contacto_email" name="contacto_email" class="form-control"
                           maxlength="150" placeholder="correo@liga.com" value="<?= $value('contacto_email') ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label" for="contacto_telefono">Teléfono de contacto</label>
                    <input type="text" id="contacto_telefono" name="contacto_telefono" class="form-control"
                           maxlength="30" placeholder="Opcional" value="<?= $value('contacto_telefono') ?>">
                </div>

                <hr>

                <div class="mb-3">
                    <label class="form-label" for="notas">Observaciones</label>
                    <textarea id="notas" name="notas" class="form-control" rows="3"
                              placeholder="Notas internas u observaciones generales"><?= $value('notas') ?></textarea>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="index.php?page=ligas" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-success">Guardar liga</button>
                </div>
            </form>
        </div>
    </div>
</div>
