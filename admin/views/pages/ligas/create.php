<div class="container mt-4">

    <div class="mb-3">
        <h3 >Nueva liga</h3>
        <p class="text-helper mb-3">
            <i class="fas fa-circle-info"></i>Solo necesitas capturar la información básica.         
        </p>
        <p class="text-helper mb-3"> Podrás completar dirección, directivos y documentos después.</p>
    </div>

   
    <div class="card shadow-sm">
                
        <div class="card-body">

            <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>

            <form action="index.php?page=ligas&action=store" method="POST">
                <!-- ======IDENTIDAD ============== -->

                <div class="mb-3">
                            <label class="form-label">
                                Nombre de la liga <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   name="nombre"
                                   class="form-control"
                                   required
                                   autofocus>
                </div>

                <div class="mb-3">
                            <label class="form-label">
                                Siglas / Clave corta
                            </label>
                            <input type="text"
                                   name="siglas"
                                   class="form-control"
                                   maxlength="10">
                </div>

                <div class="mb-3">
                            <label class="form-label">
                                Estado
                            </label>
                            <select name="estado" class="form-select">
                                <option value="activa" selected>Activa</option>
                                <option value="inactiva">Inactiva</option>
                                <option value="suspendida">Suspendida</option>
                            </select>
                </div>

                 <hr>
                <!-- =============CONTACTO=========== -->
                <div class="mb-3">
                            <label class="form-label">
                                Correo de contacto
                            </label>
                            <input type="email"
                                   name="contacto_email"
                                   class="form-control"
                                   placeholder="correo@liga.com">
                </div>

                <div class="mb-3">
                            <label class="form-label">
                                Teléfono de contacto
                            </label>
                            <input type="text"
                                   name="contacto_telefono"
                                   class="form-control"
                                   placeholder="Opcional">
                </div>

                <hr>
                <!-- ===========OBSERVACIONES=============== -->

                <div class="mb-3">
                            <label class="form-label">
                                Observaciones
                            </label>
                            <textarea name="notas"
                                      class="form-control"
                                      rows="3"
                                      placeholder="Notas internas u observaciones generales"></textarea>
                </div>

                <!-- ===========BOTONES============== -->

                <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="index.php?page=ligas" class="btn btn-secondary">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-success">
                                Guardar liga
                            </button>
                </div>

            </form>

        </div>
    </div>

</div>
