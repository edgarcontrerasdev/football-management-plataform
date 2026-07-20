<div class="row g-4">

    <!-- BLOQUES -->
    <div class="col-md-3">
        <div class="card h-100 modulo-card <?= $progreso['bloques'] ? 'modulo-ok' : 'modulo-pendiente' ?>">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="mb-0">Bloques</h6>
                </div>

                <p class="text-muted small">
                    Define fases como temporada regular, liguilla, etc.
                </p>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalBloque">
                    <i class="fas fa-plus"></i> Agregar bloque
                </button>
            </div>
        </div>
    </div>

    <!-- EQUIPOS -->
    <div class="col-md-3">
        <div class="card h-100 modulo-card <?= $progreso['equipos'] ? 'modulo-ok' : 'modulo-pendiente' ?>">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="mb-0">Equipos</h6>
                </div>

                <p class="text-muted small">
                    Inscripción y control de equipos participantes.
                </p>

                <button class="btn btn-outline-primary btn-sm btnAbrirEquipos">
                    Administrar equipos
                </button>
            </div>
        </div>
    </div>

    <!-- FIXTURE -->
    <div class="col-md-3">
        <div class="card h-100 modulo-card <?= $progreso['fixture'] ? 'modulo-ok' : 'modulo-pendiente' ?>">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="mb-0">Fixture</h6>
                </div>

                <p class="text-muted small">
                    Generación y gestión de partidos.
                </p>

                <?php if ($progreso['bloques']): ?>
                    <a href="/admin/torneos/<?= $torneo['id'] ?>/fixture"
                       class="btn btn-outline-primary btn-sm">
                        Ir al fixture
                    </a>
                <?php else: ?>
                    <button class="btn btn-outline-secondary btn-sm" disabled>
                        Requiere bloques
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ACTIVAR -->
    <div class="col-md-3">
        <div class="card h-100 modulo-card <?= (
            $progreso['datos'] &&
            $progreso['costos'] &&
            $progreso['equipos'] &&
            $progreso['bloques'] &&
            $progreso['fixture']
        ) ? 'modulo-ok' : 'modulo-bloqueado' ?>">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="mb-0">Activar torneo</h6>

                    <span class="badge bg-<?= (
                        $progreso['datos'] &&
                        $progreso['costos'] &&
                        $progreso['equipos'] &&
                        $progreso['bloques'] &&
                        $progreso['fixture']
                    ) ? 'success' : 'secondary' ?>">
                        <?= (
                            $progreso['datos'] &&
                            $progreso['costos'] &&
                            $progreso['equipos'] &&
                            $progreso['bloques'] &&
                            $progreso['fixture']
                        ) ? 'Listo' : 'Bloqueado' ?>
                    </span>
                </div>

                <p class="text-muted small">
                    Inicia oficialmente el torneo.
                </p>

                <?php if (
                    $progreso['datos'] &&
                    $progreso['costos'] &&
                    $progreso['equipos'] &&
                    $progreso['bloques'] &&
                    $progreso['fixture']
                ): ?>
                    <button class="btn btn-success btn-sm">
                        Activar torneo
                    </button>
                <?php else: ?>
                    <button class="btn btn-success btn-sm" disabled>
                        Completa la configuración
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>

<style>
    .modulo-card {
        transition: all .2s ease;
    }

    .modulo-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0,0,0,.12);
    }

    .modulo-card.modulo-ok {
        border-left: 4px solid var(--success);
    }

    .modulo-card.modulo-pendiente {
        border-left: 4px solid var(--warning);
    }

    .modulo-card.modulo-bloqueado {
    border-left: 4px solid #6c757d;
}
</style>