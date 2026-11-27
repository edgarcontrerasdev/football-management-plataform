<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <strong><i class="fas fa-clock-rotate-left me-1"></i> Historial administrativo</strong>
        <span class="badge bg-light text-dark"><?= count($historialLiga ?? []) ?> movimientos recientes</span>
    </div>
    <div class="card-body">
        <?php if (empty($historialLiga)): ?>
            <div class="text-center text-muted py-4">
                <i class="fas fa-clipboard-list fa-2x mb-2"></i>
                <p class="mb-0">Aún no hay movimientos registrados para esta liga.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Movimiento</th>
                            <th>Detalle</th>
                            <th>Realizado por</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($historialLiga as $movimiento): ?>
                            <?php
                                $etiquetas = [
                                    'nombre' => 'Nombre',
                                    'siglas' => 'Siglas',
                                    'logo' => 'Logotipo',
                                    'correo' => 'Correo',
                                    'telefono' => 'Teléfono',
                                    'direccion' => 'Dirección',
                                    'colonia' => 'Colonia',
                                    'municipio' => 'Municipio',
                                    'estado_republica' => 'Estado',
                                    'codigo_postal' => 'Código postal',
                                    'redes_sociales' => 'Redes sociales',
                                    'notas' => 'Notas',
                                    'estado' => 'Estado institucional'
                                ];
                                $campo = $etiquetas[$movimiento['campo']] ?? ucfirst((string) $movimiento['campo']);
                                $anterior = trim((string) ($movimiento['valor_anterior'] ?? ''));
                                $nuevo = trim((string) ($movimiento['valor_nuevo'] ?? ''));
                                $anterior = $anterior !== '' ? $anterior : 'Vacío';
                                $nuevo = $nuevo !== '' ? $nuevo : 'Vacío';
                            ?>
                            <tr>
                                <td class="text-nowrap small">
                                    <?= htmlspecialchars(date('d/m/Y H:i', strtotime($movimiento['created_at'])), ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $movimiento['accion'] === 'cambio_estado' ? 'warning text-dark' : 'primary' ?>">
                                        <?= $movimiento['accion'] === 'cambio_estado' ? 'Cambio de estado' : 'Actualización' ?>
                                    </span>
                                </td>
                                <td class="small">
                                    <strong><?= htmlspecialchars($campo, ENT_QUOTES, 'UTF-8') ?>:</strong>
                                    <span class="text-muted"><?= htmlspecialchars($anterior, ENT_QUOTES, 'UTF-8') ?></span>
                                    <i class="fas fa-arrow-right mx-1 text-muted"></i>
                                    <span><?= htmlspecialchars($nuevo, ENT_QUOTES, 'UTF-8') ?></span>
                                </td>
                                <td class="small">
                                    <strong><?= htmlspecialchars($movimiento['usuario_nombre'], ENT_QUOTES, 'UTF-8') ?></strong>
                                    <div class="text-muted"><?= htmlspecialchars($movimiento['rol_nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
