(function () {
    'use strict';
    const form = document.getElementById('formEditClub');
    let confirmed = false;
    if (form) {
        form.addEventListener('submit', function (event) {
            if (confirmed) return;
            event.preventDefault();
            const submit = function () { confirmed = true; form.requestSubmit(); };
            if (typeof Swal === 'undefined') {
                if (window.confirm('¿Deseas guardar los cambios de este club?')) submit();
                return;
            }
            Swal.fire({
                icon: 'question',
                title: '¿Guardar cambios?',
                text: 'Se actualizará la información del club.',
                showCancelButton: true,
                confirmButtonText: 'Sí, guardar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#0033ff',
                reverseButtons: true
            }).then(function (result) { if (result.isConfirmed) submit(); });
        });
    }
    const input = document.getElementById('logo');
    const picker = document.querySelector('.club-logo-picker');
    const preview = document.getElementById('clubLogoPreview');
    if (!input || !picker || !preview) return;
    const original = preview.src;
    const types = ['image/jpeg', 'image/png', 'image/webp'];
    const maxSize = 2 * 1024 * 1024;
    picker.addEventListener('click', function (event) { event.preventDefault(); input.click(); });
    input.addEventListener('change', function () {
        const file = input.files && input.files[0];
        if (!file) { preview.src = original; return; }
        if (!types.includes(file.type) || file.size > maxSize) {
            input.value = '';
            preview.src = original;
            if (typeof Swal !== 'undefined') Swal.fire({
                icon: 'error',
                title: file.size > maxSize ? 'Imagen demasiado grande' : 'Formato no permitido',
                text: file.size > maxSize ? 'El logo no debe superar los 2 MB.' : 'Selecciona una imagen JPG, PNG o WEBP.'
            });
            return;
        }
        const reader = new FileReader();
        reader.addEventListener('load', function () { if (typeof reader.result === 'string') preview.src = reader.result; });
        reader.readAsDataURL(file);
    });
})();
