document.addEventListener('DOMContentLoaded', function () {
    $(document).on('click', '.torneos-page .btn-delete', function (event) {
        event.preventDefault();

        const deleteUrl = new URL(this.href, window.location.href);
        const torneoId = deleteUrl.searchParams.get('id');

        if (!torneoId) {
            Swal.fire('Error', 'No fue posible identificar el torneo.', 'error');
            return;
        }

        Swal.fire({
            title: '¿Eliminar torneo?',
            text: 'Esta acción no se puede deshacer',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then(function (result) {
            if (!result.isConfirmed) return;

            $.ajax({
                url: 'index.php?page=torneos&action=eliminar',
                type: 'POST',
                dataType: 'json',
                data: { id: torneoId },
                success: function (response) {
                    if (response.success) {
                        Swal.fire(
                            'Eliminado',
                            'El torneo fue eliminado correctamente',
                            'success'
                        ).then(function () {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire(
                            'Error',
                            response.msg || response.msj || 'No fue posible eliminar el torneo.',
                            'error'
                        );
                    }
                },
                error: function () {
                    Swal.fire('Error', 'Error de comunicación con el servidor', 'error');
                }
            });
        });
    });
});
