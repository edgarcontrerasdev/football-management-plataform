document.addEventListener('DOMContentLoaded', () => 
{
    $(document).on('click', '.btnEliminarTorneo', function () {
        let torneoId = $(this).data('id');

        Swal.fire({
            title: '¿Eliminar torneo?',
            text: 'Esta acción no se puede deshacer',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
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
                            ).then(() => location.reload());
                        } else {
                            Swal.fire('Error', response.msg, 'error');
                        }
                        },
                    error: function () {
                        Swal.fire('Error', 'Error de comunicación con el servidor', 'error');
                    }
                });
            }
        });
    });
});

