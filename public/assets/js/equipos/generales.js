document.addEventListener('DOMContentLoaded', () => {

    // 🔥 abrir modal (CONTROL TOTAL)
    const btn = document.getElementById('btnGenerales');

    if(btn){
        btn.addEventListener('click', () => {
            Modal.open('modalGenerales');
        });
    }

    // 🔥 manejar formulario
    FormHandler({
        form: '#formGenerales',
        modal: 'modalGenerales',
        url: 'index.php?page=equipos&action=guardarGenerales',

        onSuccess: (res) => {
            
            document.getElementById('nombreEquipo').textContent = res.data.nombre;

            document.getElementById('siglas').textContent = res.data.siglas;

            document.getElementById('observaciones').textContent = res.data.observaciones;

        }
    });

});