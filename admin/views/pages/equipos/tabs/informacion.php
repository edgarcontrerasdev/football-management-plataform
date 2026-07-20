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

                <p><strong>Nombre:</strong> 
                    <span id="nombreEquipo"> <?= $equipo['equipo'] ?> </span> 
                </p>

                <p><strong>Siglas:</strong> 
                    <span id="siglas"> <?= $equipo['siglas'] ?? 'No definido' ?> </span>
                </p>

                <p><strong>Registro:</strong> 
                    <?= date('d/m/Y', strtotime($equipo['fecha_creacion'])) ?>
                </p>

                <p><strong>Estado:</strong> 
                    <?= $equipo['estado'] ?? 'Activo' ?>
                </p>

                <p> <strong>Observaciones </strong>
                <span id="observaciones"><?= $equipo['observaciones'] ?? 'sin observaciones' ?> </span>
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
                        <?= $equipo['contacto_email'] ?? 'No registrado' ?>
                    </span>
                </p>

                <p><strong>Teléfono:</strong> 
                    <span id="telefonoContacto">
                        <?= $equipo['contacto_telefono'] ?? 'No registrado' ?>
                    </span>
                </p>

                <p><strong>Redes:</strong> 
                    <span id="redesContacto">
                        <?= $equipo['redes_sociales'] ?? 'No registrado' ?>
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

                    <p id="direccionTexto"><?= $direccionTexto; ?></p>
               

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
                <strong><i class="fas fa-gear"></i> Estado del equipo</strong>
            </div>

            <div class="card-body small">

                <p>
                    <strong>Estatus actual:</strong>
                    <span class="badge bg-success">
                        <?= $equipo['estado'] ?? 'Activo' ?>
                    </span>
                </p>

                <p class="text-muted">
                    Aquí podrás activar, suspender o desactivar el equipo.
                </p>

                <button class="btn btn-sm btn-outline-secondary">
                    Cambiar estado
                </button>

            </div>

        </div>
    </div>

</div>