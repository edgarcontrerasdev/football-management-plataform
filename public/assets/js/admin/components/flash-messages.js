// Presenta los mensajes flash de PHP mediante SweetAlert2.
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.js-flash-message').forEach((message) => {
        let content = '';

        try {
            content = JSON.parse(message.dataset.content || '""');
        } catch (error) {
            console.error('No se pudo interpretar un mensaje flash.', error);
            return;
        }

        const options = {
            icon: message.dataset.icon,
            title: message.dataset.title
        };

        if (message.dataset.html === 'true') {
            options.html = content;
        } else {
            options.text = content;
        }

        Swal.fire(options);
    });
});
