

<?php
        modal([
            'id'        => 'modalGenerales', 
            'title'     => 'Editar datos generales',
            'size'      => 'lg',
            'icon'      => 'fas fa-search',
            'form'        => [
                    'id' => 'formGenerales'
            ],
            'body'      => function() use($equipo)
            {
    ?>
        <input type="hidden" name="equipo_id" value="<?= $equipo['id'] ?>">

        <div class="row g-3">

            <div class="col-md-6">
                <label class="form-label modal-label">Nombre</label>
                <input type="text" class="form-control" name="nombreEquipo" 
                value="<?= htmlspecialchars($equipo['equipo']) ?>">
>
            </div>

            <div class="col-md-6">
                <label class="form-label modal-label">Siglas</label>
                <input type="text" class="form-control" name="siglas"         
                value="<?= htmlspecialchars($equipo['siglas']) ?>">
>
            </div>

            <div class="col-md-6">
                <label class="form-label modal-label">Observaciones</label>
                <input type="text" class="form-control" name="observaciones"         
                value="<?= htmlspecialchars($equipo['observaciones']) ?>">
>
            </div>

        </div>
        
<?php
    },
    'footer'    => function()
    {
?>
        <button type="button" class="btn btn-outline-secondary btn-cancelar-modal">
                Cancelar
        </button>
        <button type="submit" class="btn btn-primary"> 
                <i class="fas fa-save"> </i> Guardar datos
        </button>

<?php
    }]); 
?>