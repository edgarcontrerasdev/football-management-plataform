<div class="row g-3">

    <!-- ================= DATOS GENERALES ================= -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">

            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <strong><i class="fas fa-info-circle"></i> Datos Generales</strong>

                <button class="btn btn-sm btn-outline-primary" id="btnGenerales"> 
                    Editar
                </button>
            </div>

            <div class="card-body small">

                <p><strong>Nombre:</strong> <?= $liga['nombre'] ?></p>

                <p><strong>Siglas:</strong> 
                    <?= $equipo['siglas'] ?? 'No definido' ?>
                </p>

                <p><strong>Registro:</strong> 
                    <?= date('d/m/Y', strtotime($liga['created_at'])) ?? 'No definido' ?>
                </p>

                <p><strong>Estado:</strong> 
                    <?= $liga['estado'] ?? 'Activo' ?>
                </p>

            </div>

        </div>
    </div>

    <!-- ================= CONTACTO ================= -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">

            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <strong><i class="fas fa-envelope"></i> Contacto</strong>

                <button class="btn btn-sm btn-outline-primary" id="btnContacto">
                    Editar
                </button>
            </div>

            <div class="card-body small">

                <p><strong>Email:</strong> 
                    <span id="emailContacto">
                        <?= $liga['correo'] ?? 'No registrado' ?>
                    </span>
                </p>

                <p><strong>Teléfono:</strong> 
                    <span id="telefonoContacto">
                        <?= $liga['telefono'] ?? 'No registrado' ?>
                    </span>
                </p>

                <p><strong>Redes:</strong> 
                    <span id="redesContacto">
                        <?= $liga['redes_sociales'] ?? 'No registrado' ?>
                    </span>
                </p>

            </div>

        </div>
    </div>

    <!-- ================= DIRECCION ================= -->
    <div class="col-md-6">
        
        <div class="card border-0 shadow-sm h-100">

            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <strong><i class="fas fa-map-marker"></i> Dirección</strong>

                <?php if(!empty($direccionTexto)): ?>
                    <span class="badge bg-success">Completa</span>
                <?php else: ?>
                    <span class="badge bg-warning text-dark">Pendiente</span>
                <?php endif; ?>
            </div>

            <div class="card-body small">

                <?php if(!empty($direccionTexto)): ?>
                    <p id="direccionTexto"><?= $direccionTexto ?></p>
                <?php else: ?>
                    <p class="text-muted">
                        No se ha registrado la dirección.
                    </p>
                <?php endif; ?>

                <button id="btnDireccion" class="btn btn-sm btn-outline-primary">
                    <?= !empty($direccionTexto) ? 'Editar' : 'Agregar' ?>
                </button>

            </div>

        </div>
    </div>

    <!-- ================= ESTADO / CONTROL ================= -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">

            <div class="card-header bg-white">
                <strong><i class="fas fa-gear"></i> Estado de la liga</strong>
            </div>

            <div class="card-body small">

                <p>
                    <strong>Estatus actual:</strong>
                    <span class="badge bg-success">
                        <?= $liga['estado'] ?? 'Activo' ?>
                    </span>
                </p>

                <p class="text-muted">
                    Aquí podrás activar, suspender o desactivar la liga.
                </p>

                <button class="btn btn-sm btn-outline-secondary">
                    Cambiar estado
                </button>

            </div>

        </div>
    </div>

    <!-- ================= ORGANISMOS ================= -->
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
                <strong><i class="fas fa-sitemap me-1"></i> Organismos vinculados</strong>
                <?php if (!empty($liga['organismos'])): ?>
                    <span class="badge bg-light text-dark"><?= count($liga['organismos']) ?> vinculados</span>
                <?php endif; ?>
            </div>
            <div class="card-body small">
                <?php if (in_array(Auth::role(), ['ADMIN', 'LIGA'], true)): ?>
                    <div class="border rounded-3 p-3 mb-3">
                        <h6 class="fw-semibold mb-2">Solicitar vinculación a un organismo</h6>
                        <p class="text-muted mb-3">La solicitud será enviada al organismo seleccionado. La relación solo se activará si el organismo la aprueba.</p>
                        <?php $pendientesOrg = array_filter($solicitudesOrganismo ?? [], static fn($s) => ($s['estado'] ?? '') === 'pendiente'); ?>
                        <?php if (!empty($organismosDisponibles)): ?>
                            <form method="POST" action="index.php?page=ligas&action=solicitarVinculacionOrganismo" class="row g-2 align-items-end">
                                <input type="hidden" name="liga_id" value="<?= (int) $liga['id'] ?>">
                                <div class="col-md-5"><label class="form-label" for="organismo_solicitado">Organismo *</label><select class="form-select" id="organismo_solicitado" name="organismo_id" required><option value="">Selecciona un organismo</option><?php foreach ($organismosDisponibles as $orgDisponible): ?><option value="<?= (int) $orgDisponible['id'] ?>"><?= htmlspecialchars($orgDisponible['nombre'] . (!empty($orgDisponible['siglas']) ? ' (' . $orgDisponible['siglas'] . ')' : ''), ENT_QUOTES, 'UTF-8') ?></option><?php endforeach; ?></select></div>
                                <div class="col-md-5"><label class="form-label" for="observaciones_solicitud">Motivo (opcional)</label><input class="form-control" id="observaciones_solicitud" name="observaciones" maxlength="500" placeholder="Motivo de la solicitud"></div>
                                <div class="col-md-2"><button class="btn btn-primary w-100" type="submit">Solicitar</button></div>
                            </form>
                        <?php else: ?><p class="text-muted mb-0">No hay organismos disponibles para solicitar o ya existen solicitudes pendientes.</p><?php endif; ?>
                    </div>
                <?php endif; ?>
                <?php if (!empty($solicitudesOrganismo)): ?>
                    <div class="mb-3"><strong>Historial de solicitudes</strong><?php foreach ($solicitudesOrganismo as $solicitudOrg): ?><div class="d-flex flex-wrap justify-content-between border-bottom py-2 gap-2"><span><?= htmlspecialchars($solicitudOrg['organismo_nombre'], ENT_QUOTES, 'UTF-8') ?><?= !empty($solicitudOrg['siglas']) ? ' (' . htmlspecialchars($solicitudOrg['siglas'], ENT_QUOTES, 'UTF-8') . ')' : '' ?></span><span class="badge bg-<?= $solicitudOrg['estado'] === 'aprobada' ? 'success' : ($solicitudOrg['estado'] === 'rechazada' ? 'danger' : 'warning text-dark') ?>"><?= htmlspecialchars(ucfirst($solicitudOrg['estado']), ENT_QUOTES, 'UTF-8') ?></span></div><?php endforeach; ?></div>
                <?php endif; ?>
                <?php if (empty($liga['organismos'])): ?>
                    <p class="text-muted mb-0">Esta liga no tiene organismos vinculados actualmente.</p>
                <?php else: ?>
                    <?php foreach ($liga['organismos'] as $organismo): ?>
                        <?php
                            $tipoRelacion = [
                                'gestion' => 'Gestión',
                                'afiliacion' => 'Afiliación',
                                'colaboracion' => 'Colaboración',
                                'otro' => 'Otro'
                            ];
                            $nombreOrganismo = htmlspecialchars((string) ($organismo['nombre'] ?? ''), ENT_QUOTES, 'UTF-8');
                            $siglasOrganismo = htmlspecialchars((string) ($organismo['siglas'] ?? ''), ENT_QUOTES, 'UTF-8');
                            $tipoTexto = $tipoRelacion[$organismo['tipo_relacion'] ?? ''] ?? ucfirst((string) ($organismo['tipo_relacion'] ?? ''));
                        ?>
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 py-2 border-bottom">
                            <div>
                                <div class="fw-semibold"><?= $nombreOrganismo ?><?= $siglasOrganismo !== '' ? ' (' . $siglasOrganismo . ')' : '' ?></div>
                                <span class="text-muted"><?= htmlspecialchars($tipoTexto, ENT_QUOTES, 'UTF-8') ?></span>
                            </div>
                            <div class="d-flex flex-wrap gap-2 align-items-center">
                                <?php if ((int) ($organismo['es_gestor'] ?? 0) === 1): ?>
                                    <span class="badge bg-primary">Organismo gestor</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Relacionado</span>
                                <?php endif; ?>
                                <span class="badge bg-success">Vigente</span>
                                <?php if (in_array(Auth::role(), ['ADMIN', 'SUPERVISOR'], true)): ?>
                                    <a class="btn btn-sm btn-outline-primary" href="index.php?page=organismos&action=detalles&id=<?= (int) $organismo['id'] ?>">Ver organismo</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>
