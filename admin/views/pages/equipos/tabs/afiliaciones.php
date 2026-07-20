<div class="card border-0 shadow-sm">

    <!-- HEADER -->
    <div class="card-header bg-white d-flex justify-content-between align-items-center">

        <div>
            <h6 class="mb-0 fw-bold">
                <i class="bi bi-person-badge"></i> Afiliaciones
            </h6>
            <small class="text-muted">
                Gestión de afiliaciones del equipo
            </small>
        </div>

        <button class="btn btn-primary btn-sm" id="btnNuevaAfiliacion">
            <i class="bi bi-plus"></i> Nueva afiliación
        </button>

    </div>

    <!-- FILTROS -->
    <div class="p-3 d-flex gap-2">

        <input type="text" class="form-control form-control-sm"
               placeholder="Buscar afiliado...">

        <select class="form-select form-select-sm" style="max-width:200px">
            <option value="">Todos</option>
            <option value="pendiente">Pendientes</option>
            <option value="aprobada">Aprobados</option>
            <option value="rechazada">Rechazados</option>
        </select>

    </div>

    <!-- TABLA -->
    <div class="table-responsive">

        <table class="table table-hover align-middle mb-0">

            <thead class="table-light">
                <tr>
                    <th>Afiliado</th>
                    <th>Tipo</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>

            <tbody>

            <?php if(!empty($afiliaciones)): ?>
            <?php foreach($afiliaciones as $a): ?>

                <tr>

                    <td>
                        <div class="d-flex align-items-center gap-2">

                            <img src="/uploads/fotos/<?= $a['foto'] ?? 'default.png' ?>"
                                 class="rounded-circle"
                                 width="40" height="40">

                            <div>
                                <strong><?= $a['nombre'] ?></strong><br>
                                <small class="text-muted"><?= $a['curp'] ?></small>
                            </div>

                        </div>
                    </td>

                    <td><?= $a['tipo'] ?></td>

                    <td><?= estadoBadge($a['estado']) ?></td>

                    <td>
                        <small class="text-muted">
                            <?= date('d/m/Y', strtotime($a['fecha_creacion'])) ?>
                        </small>
                    </td>

                    <td class="text-end">

                        <button class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye"></i>
                        </button>

                        <button class="btn btn-sm btn-outline-warning">
                            <i class="bi bi-pencil"></i>
                        </button>

                    </td>

                </tr>

            <?php endforeach; ?>
            <?php else: ?>

                <tr>
                    <td colspan="5">
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="bi bi-person-badge"></i>
                            </div>
                            <h6>No hay afiliaciones</h6>
                            <p>Agrega afiliados al equipo</p>
                        </div>
                    </td>
                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>