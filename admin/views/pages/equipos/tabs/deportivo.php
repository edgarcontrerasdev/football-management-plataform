<?php

$torneos = [
    [
        'id' => '1',
        'nombre' => 'torneo de prueba #1'
    ],
    [
        'id' => '2',
        'nombre' => 'torneo de prueba #2'
    ]
];

$stats = [
    'posicion' => 3,
    'puntos' => 26,
    'jugados' => 10,
    'ganados' => 8,
    'empatados' => 2,
    'perdidos' => 0,
    'gf' => 54,
    'gc' => 7,
    'dg' => 47
];

$partidos = [
    [
        'local' => 'equipo 1',
        'visitante' => 'equipo 2',
        'gol_local' => 3,
        'gol_visitante' => 1,
        'fecha' => '10/marzo/2026'
    ]
];

?>

<div class="d-flex justify-content-between align-items-center mb-3">

    <div>
        <h6 class="fw-bold mb-0">Rendimiento Deportivo</h6>
        <small class="text-muted">Estadísticas por torneo</small>
    </div>

    <div style="width:250px">
        <select class="form-select form-select-sm" id="torneoSelect">

            <?php foreach($torneos as $t): ?>
                <option value="<?= $t['id'] ?>">
                    <?= $t['nombre'] ?>
                </option>
            <?php endforeach; ?>

        </select>
    </div>

</div>

<div class="st$stats g-2 mb-3">

    <div class="col">
        <div class="kpi-mini text-center">
            <h5><?= $stats['posicion'] ?? '-' ?></h5>
            <small>Posición</small>
        </div>
    </div>

    <div class="col">
        <div class="kpi-mini success text-center">
            <h5><?= $stats['puntos'] ?? 0 ?></h5>
            <small>Puntos</small>
        </div>
    </div>

    <div class="col">
        <div class="kpi-mini info text-center">
            <h5><?= $stats['jugados'] ?? 0 ?></h5>
            <small>Jugados</small>
        </div>
    </div>

    <div class="col">
        <div class="kpi-mini success text-center">
            <h5><?= $stats['ganados'] ?? 0 ?></h5>
            <small>Ganados</small>
        </div>
    </div>

    <div class="col">
        <div class="kpi-mini warning text-center">
            <h5><?= $stats['empatados'] ?? 0 ?></h5>
            <small>Empates</small>
        </div>
    </div>

    <div class="col">
        <div class="kpi-mini danger text-center">
            <h5><?= $stats['perdidos'] ?? 0 ?></h5>
            <small>Perdidos</small>
        </div>
    </div>

    <div class="col">
        <div class="kpi-mini text-center">
            <h5><?= $stats['gf'] ?? 0 ?></h5>
            <small>GF</small>
        </div>
    </div>

    <div class="col">
        <div class="kpi-mini text-center">
            <h5><?= $stats['gc'] ?? 0 ?></h5>
            <small>GC</small>
        </div>
    </div>

</div>

<div class="card border-0 shadow-sm mb-3">

<div class="card-header bg-white fw-bold">
Tabla de posiciones
</div>

<div class="table-responsive">

<table class="table table-sm table-hover align-middle mb-0">

<thead class="table-light">
<tr>
<th>#</th>
<th>Equipo</th>
<th>PTS</th>
<th>JJ</th>
<th>G</th>
<th>E</th>
<th>P</th>
<th>GF</th>
<th>GC</th>
<th>DG</th>
</tr>
</thead>

<tbody>



<td><?= $stats['posicion'] ?></td>

<td><?= $equipo['nombre']; ?></td>

<td><?= $stats['puntos'] ?></td>
<td><?= $stats['jugados'] ?></td>
<td><?= $stats['ganados'] ?></td>
<td><?= $stats['empatados'] ?></td>
<td><?= $stats['perdidos'] ?></td>
<td><?= $stats['gf'] ?></td>
<td><?= $stats['gc'] ?></td>
<td><?= $stats['dg'] ?></td>

</tr>


</tbody>

</table>

</div>
</div>

<div class="card border-0 shadow-sm">

<div class="card-header bg-white fw-bold">
Últimos partidos
</div>

<div class="card-body">

<?php foreach($partidos as $p): ?>

<div class="d-flex justify-content-between align-items-center mb-2 border-bottom pb-2">

    <div>
        <strong><?= $p['local'] ?></strong> 
        vs 
        <strong><?= $p['visitante'] ?></strong>
    </div>

    <div>
        <span class="badge bg-dark">
            <?= $p['gol_local'] ?> - <?= $p['gol_visitante'] ?>
        </span>
    </div>

    <small class="text-muted">
        <?= date('d/m', strtotime($p['fecha'])) ?>
    </small>

</div>

<?php endforeach; ?>

</div>
</div>
