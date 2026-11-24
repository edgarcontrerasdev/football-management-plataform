(function () {
    'use strict';
    const allowed = ['image/jpeg', 'image/png', 'image/webp'];
    const maxSize = 2 * 1024 * 1024;

    document.querySelectorAll('[data-afec-image-input]').forEach(function (input) {
        const component = input.closest('.afec-image-upload');
        const preview = component && component.querySelector('img');
        if (!preview) return;
        const original = preview.getAttribute('src');

        input.addEventListener('change', function () {
            const file = input.files && input.files[0];
            if (!file) { preview.src = original; return; }

            let message = '';
            if (!allowed.includes(file.type)) message = 'Selecciona una imagen JPG, PNG o WEBP.';
            else if (file.size > maxSize) message = 'La imagen no debe superar los 2 MB.';

            if (message) {
                input.value = '';
                preview.src = original;
                if (window.Swal && typeof window.Swal.fire === 'function') {
                    window.Swal.fire({ icon: 'error', title: 'No se pudo seleccionar la imagen', text: message });
                } else {
                    window.alert(message);
                }
                return;
            }

            const reader = new FileReader();
            reader.addEventListener('load', function () {
                if (typeof reader.result === 'string') preview.src = reader.result;
            });
            reader.readAsDataURL(file);
        });
    });
})();
