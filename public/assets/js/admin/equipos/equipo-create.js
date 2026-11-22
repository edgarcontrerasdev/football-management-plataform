(function () {
    'use strict';

    const form = document.getElementById('formCreateEquipo');
    let submissionConfirmed = false;

    if (form) {
        form.addEventListener('submit', function (event) {
            if (submissionConfirmed) return;

            event.preventDefault();

            if (typeof Swal === 'undefined') {
                submissionConfirmed = window.confirm('¿Deseas registrar este equipo?');
                if (submissionConfirmed) form.requestSubmit();
                return;
            }

            Swal.fire({
                icon: 'question',
                title: '¿Registrar equipo?',
                text: 'Se guardarán los datos del equipo y el escudo seleccionado.',
                showCancelButton: true,
                confirmButtonText: 'Sí, registrar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#0033ff',
                reverseButtons: true
            }).then(function (result) {
                if (!result.isConfirmed) return;

                submissionConfirmed = true;
                form.requestSubmit();
            });
        });
    }

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
