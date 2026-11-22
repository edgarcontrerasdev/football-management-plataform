(function () {
    document.addEventListener('click', function (event) {
        const button = event.target.closest('.btn-send');

        if (!button) return;

        const form = button.closest('form');

        if (!form) return;

        event.preventDefault();

        Swal.fire({
            icon: 'info',
            title: '¿Guardar temporada?',
            html: 'Los datos serán guardados.',
            showCancelButton: true,
            confirmButtonText: 'Sí, guardar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#0033ff'
        }).then(function (result) {
            if (result.isConfirmed) {
                form.requestSubmit(button);
            }
        });
    });
})();
