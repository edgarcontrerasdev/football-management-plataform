<?php
$equiposLiga = $equiposLiga ?? [];
$estadosParticipacion = [
    'activo' => ['Activo', 'success'],
    'pendiente' => ['Pendiente', 'warning'],
    'suspendido' => ['Suspendido', 'danger'],
    'baja' => ['Baja', 'secondary']
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
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
