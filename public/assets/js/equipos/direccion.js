document.addEventListener('DOMContentLoaded', () => {

    // 🔥 abrir modal (CONTROL TOTAL)
    const btn = document.getElementById('btnDireccion');

    if(btn){
        btn.addEventListener('click', () => {
            Modal.open('modalDireccion');
        });
    }

    // 🔥 manejar formulario
    FormHandler({
        form: '#formDireccion',
        modal: 'modalDireccion',
        url: 'index.php?page=equipos&action=guardarDireccion',

        onSuccess: (res) => {

            const contenedor = document.querySelector('#direccionTexto');

            const texto = res.direccion && res.direccion.trim() !== ''
                ? res.direccion
                : '<span class="text-muted">No se ha registrado el contacto</span>';

            if(contenedor){
                contenedor.innerHTML = texto;

                // animación
                contenedor.classList.remove('fade-update');
                void contenedor.offsetWidth;
                contenedor.classList.add('fade-update');
            }

        }
    });

});