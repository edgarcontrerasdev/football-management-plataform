<?php

function modal($config = [])
{
    $id     = $config['id'] ?? 'modal';
    $title  = $config['title'] ?? '';
    $size   = $config['size'] ?? 'md';
    $body   = $config['body'] ?? null;
    $icon   = $config['icon'] ?? null;
    $footer = $config['footer'] ?? null;
    $form   = $config['form'] ??  false;
    $headerClass = $config['headerClass'] ?? 'bg-light';

    $sizeClass = match($size){
        'sm' => 'modal-sm',
        'lg' => 'modal-lg',
        'xl' => 'modal-xl',
        default => ''
    };

    ob_start();
?>

<div class="modal fade" id="<?= $id ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog <?= $sizeClass ?> modal-dialog-centered">
        <div class="modal-content">

            <?php if($form): ?>
            <form id="<?= $form['id'] ?? 'formModal' ?>">
            <?php endif; ?>

            <div class="modal-header <?= $headerClass ?>">
                <h5 class="modal-title fw-bold">
                    <?php if ($icon): ?>
                        <i class="<?= $icon ?>"></i>
                    <?php endif; ?>
                    <?= $title ?>
                </h5>
                <button type="button" class="btn-close btn-cancelar-modal" ></button>
            </div>

            <div class="modal-body">
                <?php if (is_callable($body)) $body(); ?>
            </div>

            <?php if($footer): ?>
            <div class="modal-footer">
                <?php if (is_callable($footer)) $footer(); ?>
            </div>
            <?php endif; ?>
            <?php  if($form): ?>
            </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
    echo ob_get_clean();
}
?>