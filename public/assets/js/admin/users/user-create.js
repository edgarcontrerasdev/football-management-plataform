// JavaScript del formulario de creación de usuarios.
// Se conserva el comportamiento existente de la vista.

//script para cargar equipos dinamicamente con ajax
    //cuando seleccionamos una liga

    document.addEventListener('DOMContentLoaded', () => {

        const ligaSelect = document.getElementById('liga_id');
        const equipoSelect = document.getElementById('equipo_id');

        // 🔹 Cargar ligas
        fetch('index.php?page=ajax&action=ligas')
            .then(res => res.json())
            .then(data => {
                data.forEach(liga => {
                    ligaSelect.innerHTML += `
                        <option value="${liga.id}">${liga.nombre}</option>
                    `;
                });
            });

        // 🔹 Cargar equipos según liga
        ligaSelect.addEventListener('change', () => {

            const ligaId = ligaSelect.value;
            equipoSelect.innerHTML = '<option value="">Seleccione equipo</option>';

            if (!ligaId) return;

            fetch(`index.php?page=ajax&action=equipos&liga_id=${ligaId}`)
                .then(res => res.json())
                .then(data => {
                    data.forEach(equipo => {
                        equipoSelect.innerHTML += `
                            <option value="${equipo.id}">${equipo.nombre}</option>
                        `;
                    });
                });

        });

    });

// La vista previa y validación del avatar se gestionan desde image-upload.js.

//script para detectar cambio en select de rol
    //y hacer ver u ocultar los list de liga y equipos
    //segun el rol seleccionado

    document.addEventListener('DOMContentLoaded', () => {

        const inputAvatar = document.getElementById('avatar');
        const preview = document.getElementById('avatarPreview');
        const inputRolId = document.getElementById('rol_id');


        if(!inputAvatar || !preview) return;

        //agregar evento cuando cambia el rol
        
        inputRolId.addEventListener('change', function () {

            const rol = this.value;

            document.getElementById('boxLiga').classList.add('d-none');
            document.getElementById('boxEquipo').classList.add('d-none');

            if (rol === '3') { // usuario liga
                document.getElementById('boxLiga').classList.remove('d-none');
            }

            if (rol === '4') { // usuario equipo
                document.getElementById('boxLiga').classList.remove('d-none');
                document.getElementById('boxEquipo').classList.remove('d-none');
            }

        });

    });

//script para preparar formulario y enviar datos 
    //dinamicamente para guardar usuario
    
    const formNewUser = document.getElementById('formNewUser');

    formNewUser.addEventListener('submit',function(e){
        e.preventDefault();
        const formData = new FormData(formNewUser);

        Swal.fire({
            title:'Validando datos',
            text:"por favor espere un momento...",
            timer:1200,
            allowOutsideClick:false,
            allowEscapeKey:false,
            didOpen: ()=>{
                Swal.showLoading();
            }
        });
        

        fetch(formNewUser.action,{
            method: 'POST',
            body:formData
        })
        .then(res => res.json())
        .then(res=>{
            setTimeout(()=>{
                Swal.close();
                if(res.success){
                    Swal.fire({
                        icon:'success',
                        title: '¡EXITO!',
                        text:res.mensaje,
                        timer:800,
                        showConfirmButton:false
                    }).then(()=>{
                        window.location.href = "index.php?page=users"; //redireccion al index de usuarios
                    });
                }else{
                    Swal.fire({
                        icon:'error',
                        title:'Ha ocurrido un error',
                        text:res.mensaje
                    });
                }
            },1200)
        }).catch(err=>{
            Swal.close();
            console.log("Error en la peticion ", err);
            Swal.fire({
                icon:"error",
                title:"Ha ocurrido un error",
                text:"No se pudo conectar con el servidor"
            });
        });

    });
