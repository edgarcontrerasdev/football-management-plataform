<!-- admin/views/components/flash.php -->

<?php if ($success): ?>

<script>
    document.addEventListener('DOMContentLoaded',function(){
        Swal.fire({
            icon:   'success',
            title:  'Correcto',
            text:   <?= json_encode($success) ?>
        });
    });
</script>

<?php endif; ?>

<?php if ($error): ?>

    <script>
        document.addEventListener('DOMContentLoaded',function(){

            Swal.fire({
                icon:   'error',
                title: 'Error',
                text:   <?= json_encode($error) ?>
            });
        });
    </script>

<?php endif; ?>

<?php if (!empty($errors)): ?>

<script>
    document.addEventListener('DOMContentLoaded',function(){

        Swal.fire({
            icon:   'error',
            title: 'Se encontraron errores',
            html:   <?= json_encode('<ul><li>' . implode('</li><li>', $errors) . '</li></ul>') ?>
        });
    });
</script>

<?php endif; ?>

<?php if ($warning): ?>

<script>
    document.addEventListener('DOMContentLoaded',function(){

        Swal.fire({
            icon:   'warning',
            title: 'Atención',
            text:   <?= json_encode($warning) ?>
        });
    });
</script>

<?php endif; ?>

<?php if ($info): ?>

<script>
    document.addEventListener('DOMContentLoaded',function(){

        Swal.fire({
            icon:   'info',
            title:  'Información',
            text:   <?= json_encode($info) ?>
        });
    });
</script>

<?php endif; ?>