<?php
$old = $old ?? [];
$errors = $errors ?? [];
$valor = static fn(string $campo, $default = ''): string =>
    htmlspecialchars((string) ($old[$campo] ?? ($equipo[$campo] ?? $default)), ENT_QUOTES, 'UTF-8');
$escudo = $equipo['escudo'] ?? 'default.png';
$escudoUrl = ASSETS . '/img/equipos/' . rawurlencode($escudo ?: 'default.png');
?>
<div class="equipos-edit-page">
    <div class="admin-page-header mb-4">
        <div>
            <h3 class="admin-page-title mb-1">Editar equipo</h3>
            <p class="admin-page-description mb-0">Actualiza la identidad permanente y el escudo del equipo.</p>
        </div>
        <a href="index.php?page=equipos&action=detalles&id=<?= (int) $equipo['id'] ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver al equipo
        </a>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger" role="alert"><?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="index.php?page=equipos&action=actualizar&id=<?= (int) $equipo['id'] ?>" method="POST" enctype="multipart/form-data">
                <div class="row g-4">
                    <div class="col-lg-8">
                        <h5 class="mb-3">Datos generales</h5>
                        <div class="mb-3">
                            <label class="form-label" for="nombre">Nombre del equipo <span class="text-danger">*</span></label>
                            <input class="form-control" id="nombre" name="nombre" maxlength="150" required value="<?= $valor('nombre', $equipo['equipo'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="siglas">Siglas o clave corta</label>
                            <input class="form-control" id="siglas" name="siglas" maxlength="30" value="<?= $valor('siglas') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="observaciones">Observaciones</label>
                            <textarea class="form-control" id="observaciones" name="observaciones" rows="4"><?= $valor('observaciones') ?></textarea>
                        </div>
                    </div>
                    <aside class="col-lg-4">
                        <h5 class="mb-3">Escudo</h5>
                        <div class="d-flex flex-column align-items-center gap-3 p-3 border rounded-3 bg-light">
                            <img id="teamCrestPreview" src="<?= htmlspecialchars($escudoUrl, ENT_QUOTES, 'UTF-8') ?>" alt="Escudo actual" style="width:180px;height:180px;object-fit:contain">
                            <label class="form-label w-100" for="escudo">Seleccionar nuevo escudo</label>
                            <input class="form-control" type="file" id="escudo" name="escudo" accept="image/png,image/jpeg,image/webp">
                            <small class="text-muted">JPG, PNG o WEBP. Máximo 2 MB.</small>
                        </div>
                    </aside>
                </div>
                <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                    <a href="index.php?page=equipos&action=detalles&id=<?= (int) $equipo['id'] ?>" class="btn btn-light">Cancelar</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
document.getElementById('escudo')?.addEventListener('change', function () {
    const file = this.files && this.files[0];
    if (file) document.getElementById('teamCrestPreview').src = URL.createObjectURL(file);
});
</script>
