(function () {
    const input = document.getElementById('escudo');
    const preview = document.getElementById('equipoCrestPreview');

    if (!input || !preview) return;

    const defaultImage = preview.src;

    input.addEventListener('change', function () {
        const file = input.files && input.files[0];

        if (!file) {
            preview.src = defaultImage;
            return;
        }

        const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        if (!allowedTypes.includes(file.type)) {
            input.value = '';
            preview.src = defaultImage;
            window.alert('Selecciona una imagen JPG, PNG o WEBP.');
            return;
        }

        if (file.size > 2 * 1024 * 1024) {
            input.value = '';
            preview.src = defaultImage;
            window.alert('El escudo no debe superar los 2 MB.');
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
