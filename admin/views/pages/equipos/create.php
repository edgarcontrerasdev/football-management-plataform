<div class="container mt-4">

    <div class="mb-3">
        <h3>Nuevo equipo</h3>
        <p class="text-helper mb-3">
            <i class="fas fa-circle-info"></i>
            Registra la entidad del equipo. Su club, identidad por temporada e inscripción en una liga se administrarán por separado.
        </p>
    </div>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success" role="alert">
            El equipo se registró correctamente. El siguiente paso será completar sus relaciones y datos de temporada.
        </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <form action="index.php?page=equipos&action=store" method="POST">
                <div class="mb-3">
                    <label class="form-label" for="nombre">
                        Nombre del equipo <span class="text-danger">*</span>
                    </label>
                    <input
                        type="text"
                        id="nombre"
                        name="nombre"
                        class="form-control"
                        maxlength="150"
                        required
                        autofocus
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label" for="siglas">Siglas / clave corta</label>
                    <input
                        type="text"
                        id="siglas"
                        name="siglas"
                        class="form-control"
                        maxlength="30"
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label" for="observaciones">Observaciones</label>
                    <textarea
                        id="observaciones"
                        name="observaciones"
                        class="form-control"
                        rows="3"
                        placeholder="Notas internas u observaciones generales"
                    ></textarea>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="index.php?page=equipos" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-success">
                        Guardar equipo
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
