  <?php
        modal([
            'id'        => 'modalBuscarAfiliado', 
            'title'     => 'Buscar afiliado',
            'size'      => 'md',
            'icon'      => 'fas fa-search',
            'body'      => function() use($equipo){
    ?>
    <form id="formDireccion">
        <input type="hidden" name="equipo_id" value="<?= $equipo['id'] ?>">

        <div class="row g-3">
            <div class="col-md-6">
                <input class="form-control" name="nui" placeholder="Calle">
            </div>
            <div class="col-md-6">
                <input class="form-control" name="curp" placeholder="Colonia">
            </div>
        </div>
        <button class="btn btn-secondary btn-cancelar-modal">
            Cancelar
        </button>
        <button class="btn btn-primary" type="submit" form="formDireccion">
            Guardar
        </button>
    </form>
    <?php
    },

    'footer' => null
    ]); ?>