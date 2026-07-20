
<div class="card border-0 shadow-sm">

    <div>
        <h6 class="mb-0">Plantilla del equipo</h6>
         <small class="text-muted">
            Jugadores habilitados para competir
        </small>
    </div>
    <div class="d-flex gap-2">
       
        <select class="form-select form-select-sm" style="width:180px">
            <option value="">Todos</option>
            <option>Jugadores</option>
            <option>DT</option>
            <option>Auxiliar</option>
        </select>
        <input type="text" placeholder="buscar por nombre">

        <button class="btn btn-outline-primary btn-sm">
            <i class="bi bi-funnel"></i>
        </button>

    </div>

</div>


<div class="card border-0 shadow-sm">

<div class="table-responsive">

<table class="table table-hover align-middle mb-0">

<thead class="table-light">
<tr>
<th>#</th>
<th>Jugador</th>
<th>Rol</th>
<th>Posición</th>
<th>Número</th>
<th>Seguro</th>
<th>Estatus</th>
<th class="text-end">Acciones</th>
</tr>
</thead>

<tbody>

<?php if(!empty($plantilla)): ?>

<?php foreach($plantilla as $i => $jugador): ?>

<tr>

<td><?= $i+1 ?></td>

<td>
<div class="d-flex align-items-center">

<img src="/uploads/fotos/<?= $jugador['foto'] ?? 'default.png' ?>"
     class="rounded-circle me-2"
     width="40" height="40">

<div>
<strong><?= $jugador['nombre'] ?></strong><br>
<small class="text-muted"><?= $jugador['curp'] ?></small>
</div>

</div>
</td>

<td>
<span class="badge bg-secondary">
<?= $jugador['rol'] ?? 'Jugador' ?>
</span>
</td>

<td><?= $jugador['posicion'] ?? '-' ?></td>

<td>
<span class="badge bg-dark">
<?= $jugador['numero'] ?? '-' ?>
</span>
</td>

<td><?= $jugador['seguro'] ?? '-' ?></td>

<td>
<span class="badge bg-success">
Habilitado
</span>
</td>

<td class="text-end">

<button class="btn btn-sm btn-outline-primary">
<i class="fas fa-eye"></i>
</button>

<button class="btn btn-sm btn-outline-warning">
<i class="fas fa-pencil"></i>
</button>

<button class="btn btn-sm btn-outline-secondary">
<i class="bi bi-file-earmark-text"></i>
</button>

</td>

</tr>

<?php endforeach; ?>

<?php else: ?>

<tr>
<td colspan="8">

<div class="empty-state">
<div class="empty-icon">
<i class="bi bi-people"></i>
</div>
<h5>Sin plantilla registrada</h5>
<p>No hay jugadores habilitados todavía</p>
</div>

</td>
</tr>

<?php endif; ?>

</tbody>

</table>

</div>
</div>