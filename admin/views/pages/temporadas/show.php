<div class="container mt-4">

    <h3><?= htmlspecialchars($temporada['temporada']) ?></h3>
    <p class="text-muted" style="color:gold">Detalle de temporada</p>

    <table class="table table-bordered mt-3">
        <tr>
            <th>Estado</th>
            <td><?= ucfirst($temporada['estado']) ?></td>
        </tr>
        <tr>
            <th>Fecha inicio</th>
            <td><?= $temporada['fecha_inicio'] ?></td>
        </tr>
        <tr>
            <th>Fecha fin</th>
            <td><?= $temporada['fecha_fin'] ?></td>
        </tr>
        <tr>
            <th>Descripción</th>
            <td><?= $temporada['observaciones'] ?: '—' ?></td>
        </tr>
    </table>

    <div class="d-flex gap-2">

        <?php if ($temporada['estado'] === 'borrador'): ?>

            <a href="index.php?page=temporadas&action=edit&id=<?= $temporada['id'] ?>"
               class="btn btn-warning">
               Editar
            </a>
            
            <a  href="index.php?page=temporadas&action=activar&id=<?= $temporada['id']; ?>"
                class="btn btn-sm btn-success"
                onclick="return confirm('¿Confirmas que quieres activar esta temporada? Si aceptas se desactivara automaticamente la temporada ACTIVA');">
                Activar
            </a>

            <a href="index.php?page=temporadas&action=delete&id=<?=  $temporada['id']; ?>"
                class="btn btn-sm btn-alert"
                onclick="return confirm('Deseas eliminar esta temporada?')">
                Eliminar
            </a>

        <?php elseif ($temporada['estado'] === 'activa'): ?>
            <a  href="index.php?page=temporadas&action=cerrar&id=<?= $temporada['id']; ?>"
                class="btn btn-sm btn-outline-danger"
                onclick="return confirm('¿Cerrar esta temporada?. Una vez cerrada no se podran asociar mas registros a este temporada');">
                Cerrar
            </a>
        <?php endif; ?>

        <a href="index.php?page=temporadas" class="btn btn-light">
            Volver
        </a>

    </div>

</div>
