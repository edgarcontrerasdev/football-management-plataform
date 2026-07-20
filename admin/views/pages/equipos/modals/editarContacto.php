<?php

modal([
        'id'        => 'modalContacto',
        'title'     => 'Editar Contacto',
        'size'      => 'lg',
        'icon'      => 'fas fa-pencil',
        'form'      =>[
                    'id' => 'formContacto'
        ],
        'body'      => function() use($equipo){
?>
        <input type="hidden" name="equipo_id" value="<?= $equipo['id'] ?>">

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label modal-label">Email</label>
                <input type="text" class="form-control" name="email" value="<?= $equipo['contacto_email'] ?>">
            </div>

            <div class="col-md-6">
                <label class="form-label modal-label">Telefono</label>
                <input type="text" class="form-control" name="telefono" value="<?= $equipo['contacto_telefono'] ?>">
            </div>

            <div class="col-md-6">
                <label class="form-label modal-label">Redes Sociales</label>
                    <input type="text" class="form-control" name="redes" value="<?= $equipo['redes_sociales'] ?>">
            </div>
        </div>
<?php
        },
        'footer'        => function()
        {
?>
        <button type="button" class="btn btn-outline-secondary btn-cancelar-modal">
                Cancelar
            </button>
            <button type="submit" class="btn btn-primary"> 
                <i class="fas fa-save"> </i> Guardar contacto
        </button>
<?php
        }
]);

?>