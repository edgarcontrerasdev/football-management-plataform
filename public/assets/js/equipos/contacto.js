document.addEventListener('DOMContentLoaded', () => {

    // 🔥 abrir modal (CONTROL TOTAL)
    const btn = document.getElementById('btnContacto');

    if(btn){
        btn.addEventListener('click', () => {
            Modal.open('modalContacto');
        });
    }

    // 🔥 manejar formulario
    FormHandler({
        form: '#formContacto',
        modal: 'modalContacto',
        url: 'index.php?page=equipos&action=guardarContacto',

        onSuccess: (res) => {

            document.getElementById('emailContacto').textContent = res.contacto.email;

            document.getElementById('telefonoContacto').textContent = res.contacto.telefono;

            document.getElementById('redesContacto').textContent = res.contacto.redes;
        }
    });

});