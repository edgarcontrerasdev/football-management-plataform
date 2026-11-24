<?php
/**
 * Componente común para seleccionar y previsualizar imágenes de entidades.
 * Variables: $imageUploadId, $imageUploadName, $imageUploadSrc,
 * $imageUploadLabel, $imageUploadAlt, $imageUploadHint, $imageUploadShape.
 */
$imageUploadId = $imageUploadId ?? 'entityImage';
$imageUploadName = $imageUploadName ?? 'logo';
$imageUploadSrc = $imageUploadSrc ?? (ASSETS . '/img/ligas/default.png');
$imageUploadLabel = $imageUploadLabel ?? 'Cambiar imagen';
$imageUploadAlt = $imageUploadAlt ?? 'Vista previa de imagen';
$imageUploadHint = $imageUploadHint ?? 'Opcional. JPG, PNG o WEBP. Máximo 2 MB.';
$imageUploadShape = $imageUploadShape ?? 'square';
?>
<div class="afec-image-upload afec-image-upload--<?= htmlspecialchars($imageUploadShape, ENT_QUOTES, 'UTF-8') ?>">
    <label class="afec-image-upload__preview" for="<?= htmlspecialchars($imageUploadId, ENT_QUOTES, 'UTF-8') ?>" title="<?= htmlspecialchars($imageUploadLabel, ENT_QUOTES, 'UTF-8') ?>" tabindex="0" role="button" aria-label="<?= htmlspecialchars($imageUploadLabel, ENT_QUOTES, 'UTF-8') ?>">
        <img id="<?= htmlspecialchars($imageUploadId, ENT_QUOTES, 'UTF-8') ?>Preview" src="<?= htmlspecialchars($imageUploadSrc, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($imageUploadAlt, ENT_QUOTES, 'UTF-8') ?>">
        <span class="afec-image-upload__overlay">
            <i class="fa-solid fa-camera" aria-hidden="true"></i>
            <small><?= htmlspecialchars($imageUploadLabel, ENT_QUOTES, 'UTF-8') ?></small>
        </span>
    </label>
    <input class="afec-image-upload__input" type="file" id="<?= htmlspecialchars($imageUploadId, ENT_QUOTES, 'UTF-8') ?>" name="<?= htmlspecialchars($imageUploadName, ENT_QUOTES, 'UTF-8') ?>" accept="image/jpeg,image/png,image/webp" data-afec-image-input hidden>
    <small class="afec-image-upload__hint"><?= htmlspecialchars($imageUploadHint, ENT_QUOTES, 'UTF-8') ?></small>
</div>
