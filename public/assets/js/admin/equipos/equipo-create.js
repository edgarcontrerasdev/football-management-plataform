(function () {
    'use strict';

    const input = document.getElementById('escudo');
    const picker = document.querySelector('.equipo-crest-picker');
    const preview = document.getElementById('equipoCrestPreview');

    if (!input || !picker || !preview) return;

    const defaultImage = preview.src;
    const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    const maxSize = 2 * 1024 * 1024;

    picker.addEventListener('click', function (event) {
        event.preventDefault();
        input.click();
    });

    input.addEventListener('change', function () {
        const file = input.files && input.files[0];

        if (!file) {
            preview.src = defaultImage;
            return;
        }

        if (!allowedTypes.includes(file.type)) {
            input.value = '';
            preview.src = defaultImage;
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Formato no permitido',
                    text: 'Selecciona una imagen JPG, PNG o WEBP.'
                });
            }
            return;
        }

        if (file.size > maxSize) {
            input.value = '';
            preview.src = defaultImage;
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Imagen demasiado grande',
                    text: 'El escudo no debe superar los 2 MB.'
                });
            }
            return;
        }

        const reader = new FileReader();
        reader.addEventListener('load', function () {
            if (typeof reader.result === 'string') {
                preview.src = reader.result;
            }
        });
        reader.readAsDataURL(file);
    });
})();
