<form method="POST" action="index.php?page=temporadas&action=<?= $action; ?>">

    <?php if ($isEdit): ?>
        <input type="hidden" name="id" value="<?= $temporada['id']; ?>">
    <?php endif; ?>

    <div class="mb-3">
        <h6 class="text-muted text-uppercase fw-bold mb-2">
            Datos generales
        </h6>
        <hr class="mt-0">
    </div>

    <?php
        $ownerValue = $old['propietario'] ?? (
            isset($temporada['organismo_id']) && $temporada['organismo_id']
                ? 'organismo:' . $temporada['organismo_id']
                : (isset($temporada['liga_propietaria_id']) && $temporada['liga_propietaria_id']
                    ? 'liga:' . $temporada['liga_propietaria_id'] : '')
        );
    ?>
    <?php if (!$isEdit): ?>
        <div class="mb-3">
            <label class="form-label">Organismo o liga propietaria</label>
            <select name="propietario" class="form-select" required>
                <option value="">Selecciona el propietario</option>
                <optgroup label="Organismos">
                    <?php foreach (($propietarios['organismos'] ?? []) as $organismo): ?>
                        <option value="organismo:<?= (int) $organismo['id']; ?>"
                            <?= $ownerValue === 'organismo:' . $organismo['id'] ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($organismo['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </optgroup>
                <optgroup label="Ligas independientes">
                    <?php foreach (($propietarios['ligas'] ?? []) as $liga): ?>
                        <option value="liga:<?= (int) $liga['id']; ?>"
                            <?= $ownerValue === 'liga:' . $liga['id'] ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($liga['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </optgroup>
            </select>
            <small class="text-muted">Las ligas con organismo gestor utilizarán las temporadas de ese organismo.</small>
        </div>
    <?php else: ?>
        <div class="mb-3">
            <label class="form-label">Propietario</label>
            <input type="text" class="form-control"
                   value="<?= htmlspecialchars($temporada['organismo_nombre'] ?? $temporada['liga_propietaria_nombre'] ?? ''); ?>"
                   disabled>
            <small class="text-muted">El propietario se conserva para mantener el historial y las relaciones existentes.</small>
        </div>
    <?php endif; ?>

    <div class="mb-3">
        <label class="form-label">Nombre de la temporada</label>
        <input type="text"
               name="temporada"
               class="form-control"
               placeholder="Ej. Temporada 2025-2026"
               value="<?= htmlspecialchars($old['temporada'] ?? $temporada['temporada'] ?? ''); ?>">
    </div>

    <label class="form-label">Periodo de vigencia</label>

    <div class="row">
        <div class="col-md-6 mb-3">
            <input type="date"
                   name="fecha_inicio"
                   class="form-control"
                   value="<?= htmlspecialchars($old['fecha_inicio'] ?? $temporada['fecha_inicio'] ?? ''); ?>">
            <small class="text-muted">Fecha de inicio</small>
        </div>

        <div class="col-md-6 mb-3">
            <input type="date"
                   name="fecha_fin"
                   class="form-control"
                   value="<?= htmlspecialchars($old['fecha_fin'] ?? $temporada['fecha_fin'] ?? ''); ?>">
            <small class="text-muted">Fecha de fin</small>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Observaciones</label>
        <textarea name="observaciones"
                  class="form-control"
                  rows="2"
                  placeholder="Notas internas, observaciones..."><?= htmlspecialchars($old['observaciones'] ?? $temporada['observaciones'] ?? ''); ?></textarea>
    </div>

    <div class="d-flex justify-content-end gap-2">
        <a href="index.php?page=temporadas" class="btn btn-light">
            Cancelar
        </a>

        <button type="submit" class="btn btn-primary btn-send">
            <?= $isEdit ? 'Actualizar temporada' : 'Guardar temporada'; ?>
        </button>
    </div>

</form>