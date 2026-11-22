<!-- admin/views/components/flash.php -->

<?php if ($success): ?>
<div class="js-flash-message" data-icon="success" data-title="Correcto" data-content="<?= htmlspecialchars(json_encode($success), ENT_QUOTES, 'UTF-8') ?>"></div>
<?php endif; ?>

<?php if ($error): ?>
<div class="js-flash-message" data-icon="error" data-title="Error" data-content="<?= htmlspecialchars(json_encode($error), ENT_QUOTES, 'UTF-8') ?>"></div>
<?php endif; ?>

<?php if (!empty($errors)): ?>
<div class="js-flash-message" data-icon="error" data-title="Se encontraron errores" data-html="true" data-content="<?= htmlspecialchars(json_encode('<ul><li>' . implode('</li><li>', $errors) . '</li></ul>'), ENT_QUOTES, 'UTF-8') ?>"></div>
<?php endif; ?>

<?php if ($warning): ?>
<div class="js-flash-message" data-icon="warning" data-title="Atención" data-content="<?= htmlspecialchars(json_encode($warning), ENT_QUOTES, 'UTF-8') ?>"></div>
<?php endif; ?>

<?php if ($info): ?>
<div class="js-flash-message" data-icon="info" data-title="Información" data-content="<?= htmlspecialchars(json_encode($info), ENT_QUOTES, 'UTF-8') ?>"></div>
<?php endif; ?>

<?php startSection('scripts'); ?>
<script src="<?= BASE_URL ?>/public/assets/js/admin/components/flash-messages.js"></script>
<?php endSection('scripts'); ?>
