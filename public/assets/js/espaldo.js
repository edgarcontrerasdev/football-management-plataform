
    document.addEventListener('submit', function(e){

        if(!e.target.matches('#formDireccion')) return;

        e.preventDefault();

        const form = e.target;
        const btn = form.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        const data = new FormData(form);

        // inhabilitar boton de guardar y mostrar spinner+mensaje
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
        
        fetch('index.php?page=equipos&action=guardarDireccion', {
            method: 'POST',
            body: data
        })
        .then(res => res.text())
        .then(text => {

            let res;

            try {
                res = JSON.parse(text);
            } catch (e) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error del servidor',
                    text: 'La respuesta no es válida'
                });
                return;
            }

            if(!res.success){
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: res.message || 'No se pudo guardar'
                });
                return;
            }

            // ✅ cerrar modal
            const modalEl = document.getElementById('modalDireccion');
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);

            modal.hide();

            setTimeout(()=>{

                // ✅ actualizar UI
                const contenedor = document.querySelector('#direccionTexto');
        
                const texto = res.direccion && res.direccion.trim() !== ''
                    ? res.direccion
                    :   '<span class="text-muted">No se ha registrado la dirección</span>';

                if (contenedor){
                    contenedor.innerHTML = texto;

                    // animación
                    contenedor.classList.remove('fade-update');
                    void contenedor.offsetWidth;
                    contenedor.classList.add('fade-update');
                }

                // ✅ SweetAlert éxito
                Swal.fire({
                    icon: 'success',
                    title: 'Guardado correctamente',
                    timer: 1200,
                    showConfirmButton: false
                });
            },350);
        })
        .catch(err => {
            console.error(err);
            alert('Error en comunicación');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = originalText;
        });

    });
   
  
    document.getElementById('formGenerales').addEventListener('submit', function(e){
        e.preventDefault();

        const form = this;
        const data = new FormData(form);
        
        fetch('index.php?page=equipos&action=guardarGenerales', {
            method: 'POST',
            body: data
        })
        .then(res =>{
            if(!res.ok){
                throw new Error('ERROR http: ' + res.status);
            }
            return res.text();
        })
        .then(text =>{
             let res;
            try {
                res = JSON.parse(text); // 👈 parse manual
            } catch (e) {
                throw new Error('La respuesta no es JSON válido');
            }

            if(!res.success){
                alert(res.message || 'Ocurrió un error');
                return;
            }

            // ✅ cerrar modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalGenerales'));
            if(modal) modal.hide();

            // ✅ actualizar UI
            document.querySelector('#info p strong').parentNode.innerHTML =
                `<strong>Nombre:</strong> ${res.data.nombre}`;

            // 🚧 temporal
            location.reload();
        })
        .catch(err => {
            console.error(err);
            alert('Error en la comunicación con el servidor');
        });
    });

   document.addEventListener('DOMContentLoaded', function(){
        document.getElementById('btnDireccion').addEventListener('click',()=>{
            Modal.open('modalDireccion');
        });
        
        const form = document.getElementById('formContacto');
    
        if(!form){
            console.error('No existe formContacto');
            return;
        }

        form.addEventListener('submit', function(e){
            e.preventDefault();

            const data = new FormData(this);
            const btn = form.querySelector('button[type="submit"]');
            const originalText = btn.innerHTML;

            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';

            fetch('index.php?page=equipos&action=guardarContacto', {
                method: 'POST',
                body: data
            })
            .then(res => res.text())
            .then(text => {
                let res;
                try{
                    res = JSON.parse(text);
                }catch{
                    Swal.fire('Error','Respuesta invalida del servidor','error');
                    return;
                }

                if(!res.success){
                    Swal.fire('Error', res.message || 'No se pudo guardar', 'error');
                    return;
                }
                
                // ✅ cerrar modal (FORZADO)
                document.querySelector('#modalContacto .btn-close')?.click();

                if(res.hasOwnProperty('contacto')){
                    document.getElementById('emailContacto').innerText = res.contacto.email || 'No registrado';
                    document.getElementById('telefonoContacto').innerText = res.contacto.telefono || 'No registrado';
                    document.getElementById('redesContacto').innerText = res.contacto.redes || 'No registrado';
                }
                Swal.fire({
                    icon: 'success',
                    title: 'Guardado',
                    timer: 1200,
                    showConfirmButton: false
                });

            })
            .catch(err => {
                console.error(err);
                Swal.fire('Error','Error en la comunicación','error');
            }).finally(()=>{
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
        });
    });
