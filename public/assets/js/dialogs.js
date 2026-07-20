function confirmDialog(options) {
    return Swal.fire({
        icon: options.icon ?? 'warning',
        title: options.title ?? '¿Confirmar acción?',
        html: options.html ?? '',
        showCancelButton: true,
        confirmButtonText: options.confirmButtonText ?? 'Sí, continuar',
        cancelButtonText: options.cancelButtonText ?? 'Cancelar',
        confirmButtonColor: '#0b3ea6',
        cancelButtonColor: '#6b7280',
        reverseButtons: true
    });
}