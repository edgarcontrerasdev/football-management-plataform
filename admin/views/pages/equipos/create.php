<div class="container mt-4">

    <div class="mb-3">
        <h3 >Nuevo Equipo</h3>
        <p class="text-helper mb-3">
            <i class="fas fa-circle-info"></i>Solo necesitas capturar la información básica.         
        <br> Podrás completar dirección, directivos y documentos después.</p>
    </div>

   
    <div class="card shadow-sm">
                
        <div class="card-body">

            <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>

            <form action="index.php?page=equipos&action=store" method="POST">
                <!-- ======IDENTIDAD ============== -->

                <div class="mb-3">
                    <label class="form-label">
                        Nombre del equipo<span class="text-danger">*</span>
                    </label>
                    <input type="text" name="nombre" class="form-control" required autofocus>
                </div>

                <div class="mb-3">
                    <label class="form-label"> Siglas / Clave corta </label>
                    <input type="text" name="siglas" class="form-control" maxlength="10">
                </div>

                <div class="mb-3">
                    <label class="form-label"> Liga </label>
                    <select name="liga" class="form-select">
                        <option value="">*** selecciona una liga *** </option>
                        <?php foreach($ligas as $liga): ?>
                        <option value="<?= $liga['id']; ?>"> <?= $liga['nombre']; ?> </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label"> Estado </label>
                    <select name="estado" class="form-select" readonly>
                        <option value="1" >Activo</option>
                        <option value="2">Inactivo</option>
                        <option value="3">Suspendido</option>
                    </select>
                </div>

                 <hr>
                <!-- =============CONTACTO=========== -->
                <div class="mb-3">
                    <label class="form-label"> Correo de contacto </label>
                    <input type="email" name="contacto_email" class="form-control" placeholder="correo@equipo.com">
                </div>

                <div class="mb-3">
                    <label class="form-label"> Teléfono de contacto </label>
                    <input type="text" name="contacto_telefono" class="form-control" placeholder="Opcional">
                </div>

                <hr>
                <!-- ===========OBSERVACIONES=============== -->

                <div class="mb-3">
                    <label class="form-label"> Observaciones </label>
                    <textarea name="notas" class="form-control" rows="3" placeholder="Notas internas u observaciones generales"></textarea>
                </div>

                <!-- ===========BOTONES============== -->

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="index.php?page=ligas" class="btn btn-secondary"> Cancelar </a>
                    <button type="submit" class="btn btn-success">
                         Guardar equipo
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>
