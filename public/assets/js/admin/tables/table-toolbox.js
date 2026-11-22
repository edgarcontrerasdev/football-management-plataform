// Gestiona la navegación del botón de creación de las barras de herramientas.
document.addEventListener('click', (event) => {
    const button = event.target.closest('.btn-add[data-create-url]');
    if (!button) return;

    const url = button.dataset.createUrl;
    if (url) {
        window.location.href = url;
    }
});
