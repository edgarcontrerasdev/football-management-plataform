/**
 * Confirmaciones de acciones en el detalle de temporadas.
 * Mantiene la navegación original si el usuario confirma.
 */
document.addEventListener('click', (event) => {
    const link = event.target.closest('a[data-confirm]');
    if (!link) return;

    const message = link.dataset.confirm;
    if (message && !window.confirm(message)) {
        event.preventDefault();
    }
});
