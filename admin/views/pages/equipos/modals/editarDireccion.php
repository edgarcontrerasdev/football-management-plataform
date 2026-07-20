<?php

modal([
        'id'        => 'modalDireccion',
        'title'     => 'Editar Direccion',
        'size'      => 'lg',
        'icon'      => 'fas fa-pencil',
        'form'      =>[
                    'id' => 'formDireccion'
        ],
        'body'      => function() use($equipo)
        {
?>
            <input type="hidden" name="equipo_id" value="<?= $equipo['id'] ?>">

            <div class="row g-3">

                <div class="col-md-6">
                    <label class="form-label modal-label">Calle</label>
                    <input type="text" class="form-control" name="calle">
                </div>

                <div class="col-md-6">
                    <label class="form-label modal-label">Colonia</label>
                    <input type="text" class="form-control" name="colonia">
                </div>

                <div class="col-md-6">
                    <label class="form-label modal-label">Código Postal</label>
                    <input type="text" class="form-control" name="codigo_postal">
                </div>

                <div class="col-md-6">
                    <label class="form-label modal-label">Ciudad</label>
                    <input type="text" class="form-control" name="ciudad">
                </div>

                <div class="col-md-6">
                    <label class="form-label modal-label">Estado</label>
                    <input type="text" class="form-control" name="estado_direccion">
                </div>

                <div class="col-md-6">
                    <label class="form-label modal-label">País</label>
                    <input type="text" class="form-control" name="pais">
                </div>
        
            </div>

<?php
        },
        'footer'     => function()
        {
?>
            <button type="button" class="btn btn-outline-secondary btn-cancelar-modal">
                Cancelar
            </button>
            <button type="submit" class="btn btn-primary"> 
                <i class="fas fa-save"> </i> Guardar dirección 
            </button>
<?php
        }
    ]);
?>