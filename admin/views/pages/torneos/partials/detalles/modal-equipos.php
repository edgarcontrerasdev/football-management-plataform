<div class="modal fade" id="modalEquipos" tabindex="-1">

  <div class="modal-dialog modal-xl modal-dialog-scrollable">

    <div class="modal-content">

      <div class="modal-header bg-dark text-white">
        <h5 class="modal-title">
          Inscribir equipos al torneo
          <small class="text-warning"><?= $torneo['nombre']; ?></small>
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <?php 
        if (empty($equipos)) : ?>
            <!-- Si no hay equipos activos en la liga se muestra mensaje --> 
            <div class="alert alert-info text-center">
                <strong>No hay equipos activos en esta liga</strong><br>
                <span> Ve al modulo el de Ligas para activar o registra equipos. </span>
            </div>
        <?php 
        else : ?>
            <!-- mensaje guia para el usuario -->
            <div class="alert alert-secondary d-flex align-items-center gap-2">
                <i class="fas fa-info-circle text-primary"></i>
                <div>
                    <strong>Selecciona los equipos</strong><br>
                    Solo se muestran equipos <b>activos</b> en la liga. Usa el buscador para filtrar.
                </div>
            </div>

            <!-- Buscador -->
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <input type="text" class="form-control flex-grow-1" id="buscadorEquipos" placeholder="🔍 Buscar equipo...">
                <button type="button" class="btn btn-primary btn-sm" id="btnToggleEquipos">
                    Seleccionar todos
                </button>
                <button type="button" id="btnRestaurarCambios" class="btn btn-success d-none">
                    Restaurar cambios
                </button>
                <span class="badge bg-dark">
                    Seleccionados: <span id="contadorSeleccionados">0</span>
                </span>
            </div>
            <div class="leyenda-estados mb-3">
                <div class="d-flex flex-wrap gap-3">

                    <span class="d-flex align-items-center gap-1">
                        <span class="badge estado-equipo bg-success">Inscrito</span>
                    </span>

                    <span class="d-flex align-items-center gap-1">
                        <span class="badge estado-equipo bg-secondary">Disponible</span>
                    </span>

                    <span class="d-flex align-items-center gap-1">
                        <span class="badge bg-warning text-dark">Se inscribirá</span>
                    </span>

                    <span class="d-flex align-items-center gap-1">
                        <span class="badge bg-danger">Se quitará</span>
                    </span>

                </div>
            </div>

            <form id="formEquipos">
                <div class="row" id="contenedorEquipos">
                
                <?php 
                    usort($equipos, function ($a, $b) use ($inscritosIds) {
                        return in_array($b['id'], $inscritosIds) <=> in_array($a['id'], $inscritosIds);
                    });
                    foreach ($equipos as $e):
                    $inscrito = in_array($e['id'], $inscritosIds);
                    $escudo = $e['escudo'] ? '/afec/public/assets/img/equipos/' . $e['escudo'] : '/afec/public/assets/img/equipos/default.png';
                ?>

                    <div class="col-md-3 mb-2 equipo-item <?= $inscrito ? 'inscrito' : '' ?>">
                    <label class="card h-100 shadow-sm equipo-card equipo-label <?= $inscrito ? 'seleccionado' : '' ?>">
                        <div class="card-body d-flex align-items-center gap-3">
                        <input class="form-check-input me-2 equipo-check" type="checkbox" name="equipos[]" 
                            value="<?= $e['id']; ?>"
                            data-original="<?= $inscrito ? '1' : '0' ?>"
                             <?= $inscrito ? 'checked' : '' ?> >
                        <img src="<?= $escudo; ?>" width="35" height="35" class="rounded" alt="escudo">
                        <div class="equipo-info">
                            <div class="equipo-nombre" title="<?= $e['nombre']; ?>"> 
                                <?= $e['nombre']; ?>
                            </div>
                            <?php if ($inscrito): ?>
                                <span class="badge estado-equipo bg-success">Inscrito</span>
                            <?php else:?>
                                <span class="badge estado-equipo bg-secondary">Disponible</span>
                           <?php endif; ?>
                        </div>
                        </div>
                    </label>
                    </div>

                <?php endforeach; ?>

                </div>
            </form>

        <?php 
        endif; ?>

      </div>

      <div class="modal-footer">
        <span id="badgeCambios"
            class="badge bg-warning text-dark d-none">
            Cambios pendientes
        </span>
        <button class="btn btn-dark" data-bs-dismiss="modal">Salir</button>
   
        <?php if (!empty($equipos)):?>
        <button class="btn btn-primary" id="btnGuardarEquipos">
          <i class="fas fa-save"></i> Guardar cambios
        </button>
        <?php else: ?>
         <button class="btn btn-primary" id="btnGuardarEquipos" disabled>
            <i class="fas fa-save"></i> Guardar cambios
        </button>   
        <?php endif ?>
      </div>

    </div>

  </div>

</div>
