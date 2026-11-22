<?php
    $isEdit = isset($temporada);
    $old = $_SESSION['old'] ?? [];
    $errors = $_SESSION['errors'] ?? [];
    unset($_SESSION['old'], $_SESSION['errors']);

    $action = $isEdit ? 'update' : 'store';
    $title = $isEdit ? 'EDITAR TEMPORADA' : 'CREAR NUEVA TEMPORADA';

    $helperText = $isEdit
        ? 'Modifica la información general de la temporada. El estado actual no cambiará.'
        : 'Define el periodo de vigencia. La temporada se guardará inicialmente como Planeada.';
?>

<h3 class="users-title mb-1">
    <?= $title ?>
</h3>

<div class="context-bar mb-3">
    <div class="context-icon">
        <i class="fa-solid fa-calendar-days"></i>
    </div>

    <div>
        <strong><?= $isEdit ? 'Edición de temporada' : 'Nueva temporada' ?></strong>
        <p class="mb-0">
            <?= $helperText ?>
        </p>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">

        <div class="row g-0">

            <div class="col-lg-8 p-4 border-end">
                <?php include ROOT_PATH . '/admin/views/pages/temporadas/partials/form-new.php'; ?>
            </div>

            <div class="col-lg-4 p-4 bg-light">
                <?php include ROOT_PATH . '/admin/views/side-bars/temporadas.php'; ?>
            </div>

        </div>

    </div>
</div>


<script>

    document.addEventListener('click',function(e){
        const btn = e.target.closest('.btn-send');
         if(!btn) return;

         e.preventDefault();

         const url = btn.getAttribute('href');

         Swal.fire({
            icon:'info',
            title: '¿Guardar temporada?',
            html: 'Los datos seran guardados..',
            showCancelButton: true,
            confirmButtonText: 'Sí, guardar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#0033ff'
         }).then((result) =>{
            if(result.isConfirmed){
                window.location.href = url;
            }
         });
    });
</script>