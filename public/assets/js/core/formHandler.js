function FormHandler(config){

    const form = document.querySelector(config.form);

    if(!form){
        console.warn('FormHandler: formulario no encontrado', config.form);
        return;
    }

    const btnSubmit = form.querySelector('[type="submit"]');
    let originalBtnText = btnSubmit ? btnSubmit.innerHTML : '';

    form.addEventListener('submit', async function(e){

        e.preventDefault();

        const data = new FormData(form);

        // 🔥 limpiar errores previos
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        form.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

        try{

            // 🔥 loading botón
            if(btnSubmit){
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Guardando...`;
            }

            // 🔥 loader global opcional
            if(config.loader && typeof Swal !== 'undefined'){
                Swal.fire({
                    title: config.loaderText || 'Procesando...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });
            }

            const res = await fetch(config.url, {
                method: config.method || 'POST',
                body: data
            });

            const json = await res.json();

            if(json.success){

                // 🔥 cerrar modal
                if(config.modal && typeof Modal !== 'undefined'){
                    Modal.close(config.modal);
                }

                // 🔥 reset form
                if(config.reset){
                    form.reset();
                }

                // 🔥 callback principal
                if(config.onSuccess){
                    config.onSuccess(json, form);
                }

                // 🔥 actualizar elementos dinámicos
                if(config.update){
                    Object.keys(config.update).forEach(selector => {
                        const el = document.querySelector(selector);
                        if(el && json[config.update[selector]]){
                            el.innerHTML = json[config.update[selector]];
                        }
                    });
                }

                // 🔥 toast success
                if(config.toast !== false && typeof Swal !== 'undefined'){
                    Swal.fire({
                        icon: 'success',
                        title: json.message || 'Guardado correctamente',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }

            }else{

                // 🔥 errores por campo
                if(json.errors){
                    Object.keys(json.errors).forEach(name => {
                        const input = form.querySelector(`[name="${name}"]`);
                        if(input){
                            input.classList.add('is-invalid');

                            const error = document.createElement('div');
                            error.className = 'invalid-feedback';
                            error.innerText = json.errors[name];

                            input.parentNode.appendChild(error);
                        }
                    });
                }

                throw new Error(json.message || 'Error en el formulario');
            }

        }catch(error){

            console.error(error);

            if(typeof Swal !== 'undefined'){
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message
                });
            }

        }finally{

            // 🔥 restaurar botón
            if(btnSubmit){
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = originalBtnText;
            }

        }

    });

}