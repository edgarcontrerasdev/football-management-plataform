<?php
$equiposLiga = $equiposLiga ?? [];
$equiposDisponibles = $equiposDisponibles ?? [];
$ligaTemporada = $ligaTemporada ?? null;
$estadosParticipacion = [
    'activo' => ['Activo', 'success'],
    'pendiente' => ['Pendiente', 'warning'],
    'suspendido' => ['Suspendido', 'danger'],
    'baja' => ['Baja', 'secondary'],
    'rechazado' => ['Rechazado', 'danger']
];
$estadosDocumentacion = [
    'completa' => ['Completa', 'success'],
    'pendiente' => ['Pendiente', 'warning'],
    'incompleta' => ['Incompleta', 'danger'],
    'rechazada' => ['Rechazada', 'danger']
];
?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
        <h5 class="mb-1">Equipos de la liga</h5>
        <p class="text-muted small mb-0">
            Participaciones registradas en la temporada
            <strong><?= htmlspecialchars($temporadaActual['temporada'] ?? 'actual', ENT_QUOTES, 'UTF-8') ?></strong>.
        </p>
    </div>
    <span class="badge bg-primary">
        <?= count($equiposLiga) ?> equipo(s)
    </span>
</div>

<?php if (!empty($temporadaActual) && $ligaTemporada): ?>
    <form method="POST" action="index.php?page=ligas&action=inscribirEquipo" class="card card-body border mb-4">
        <input type="hidden" name="liga_id" value="<?= (int) $liga['id'] ?>">
        <input type="hidden" name="temporada_id" value="<?= (int) $temporadaActual['id'] ?>">
        <h6 class="mb-3">Registrar equipo en esta temporada</h6>
        <?php if ($equiposDisponibles): ?>
            <div class="row g-2 align-items-end">
                <div class="col-12 col-lg-7">
                    <label for="equipo_id" class="form-label">Equipo</label>
                    <select class="form-select" id="equipo_id" name="equipo_id" required>
                        <option value="">Selecciona un equipo</option>
                        <?php foreach ($equiposDisponibles as $disponible): ?>
                            <option value="<?= (int) $disponible['id'] ?>">
                                <?= htmlspecialchars($disponible['nombre_temporada'], ENT_QUOTES, 'UTF-8') ?>
                                <?= !empty($disponible['siglas_temporada']) ? ' · ' . htmlspecialchars($disponible['siglas_temporada'], ENT_QUOTES, 'UTF-8') : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 col-lg-3">
                    <label for="numero_registro" class="form-label">Número de registro</label>
                    <input type="text" class="form-control" id="numero_registro" name="numero_registro" maxlength="50">
                </div>
                <div class="col-12 col-lg-2">
                    <button type="submit" class="btn btn-primary w-100">Inscribir</button>
                </div>
            </div>
            <small class="text-muted d-block mt-2">La inscripción se registra como pendiente de revisión.</small>
        <?php else: ?>
            <p class="text-muted mb-0">No hay equipos disponibles para inscribir. Todos los equipos registrados ya participan o todavía no existen equipos en el catálogo.</p>
        <?php endif; ?>
    </form>
<?php elseif (!empty($temporadaActual)): ?>
    <div class="alert alert-warning">
        La temporada actual todavía no está vinculada a esta liga. Primero registra la solicitud en la pestaña Temporada.
    </div>
<?php endif; ?>

<?php if (empty($temporadaActual)): ?>
    <div class="alert alert-info mb-0">
        No hay una temporada marcada como actual. No es posible mostrar las participaciones.
    </div>
<?php elseif (empty($equiposLiga)): ?>
    <div class="text-center py-4">
        <i class="fas fa-users text-muted fa-2x mb-3"></i>
        <h6>No hay equipos registrados</h6>
        <p class="text-muted small mb-0">
            Esta liga todavía no tiene equipos inscritos en la temporada actual.
        </p>
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Equipo</th>
                    <th>Registro</th>
                    <th>Participación</th>
                    <th>Documentación</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($equiposLiga as $equipo): ?>
                    <?php
                    $estadoParticipacion = strtolower((string) ($equipo['estado_participacion'] ?? ''));
                    $estadoDocumentacion = strtolower((string) ($equipo['estado_documentacion'] ?? ''));
                    [$participacionTexto, $participacionClase] =
                        $estadosParticipacion[$estadoParticipacion] ?? [ucfirst($estadoParticipacion ?: 'Sin estado'), 'secondary'];
                    [$documentacionTexto, $documentacionClase] =
                        $estadosDocumentacion[$estadoDocumentacion] ?? [ucfirst($estadoDocumentacion ?: 'Sin estado'), 'secondary'];
                    ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($equipo['nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong>
                            <?php if (!empty($equipo['siglas'])): ?>
                                <div class="text-muted small"><?= htmlspecialchars($equipo['siglas'], ENT_QUOTES, 'UTF-8') ?></div>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($equipo['numero_registro'] ?: '—', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><span class="badge bg-<?= $participacionClase ?>"><?= htmlspecialchars($participacionTexto, ENT_QUOTES, 'UTF-8') ?></span></td>
                        <td><span class="badge bg-<?= $documentacionClase ?>"><?= htmlspecialchars($documentacionTexto, ENT_QUOTES, 'UTF-8') ?></span></td>
                        <td>
                            <?php if ($estadoParticipacion === 'pendiente'): ?>
                                <div class="d-flex flex-wrap gap-1">
                                    <form method="POST" action="index.php?page=ligas&action=resolverInscripcionEquipo">
                                        <input type="hidden" name="liga_id" value="<?= (int) $liga['id'] ?>">
                                        <input type="hidden" name="liga_equipo_id" value="<?= (int) $equipo['liga_equipo_id'] ?>">
                                        <input type="hidden" name="decision" value="aprobar">
                                        <button type="submit" class="btn btn-sm btn-success">Aprobar</button>
                                    </form>
                                    <form method="POST" action="index.php?page=ligas&action=resolverInscripcionEquipo">
                                        <input type="hidden" name="liga_id" value="<?= (int) $liga['id'] ?>">
                                        <input type="hidden" name="liga_equipo_id" value="<?= (int) $equipo['liga_equipo_id'] ?>">
                                        <input type="hidden" name="decision" value="rechazar">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Rechazar</button>
                                    </form>
                                </div>
                            <?php else: ?>
                                <span class="text-muted small">Sin acciones</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
