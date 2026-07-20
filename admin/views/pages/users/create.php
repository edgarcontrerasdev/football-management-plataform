<?php if (!empty($data['errors'])): ?>
    <div class="alert alert-danger">
        <strong>Ocurrieron errores:</strong>
        <ul style="margin:0;">
            <?php foreach ($data['errors'] as $error): ?>
                <li><?= htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="user-form-wrapper">

    <div class="user-form-card">

        <h3 class="form-title">Registro de usuario</h3>

        <form id="formNewUser" action="index.php?page=users&action=save" method="POST" enctype="multipart/form-data">
           
            <div class="user-form-grid">

                <!-- Avatar -->
                <div class="avatar-column">
                    <label for="avatar" class="avatar-box">
                        <img id="avatarPreview"
                             src="/afec/public/assets/img/avatars/default.png">
                        <div class="avatar-overlay">
                            <span>📷</span>
                            <small>Cambiar avatar</small>
                        </div>
                    </label>

                    <input type="file" name="avatar" id="avatar" accept="image/png,image/jpeg" hidden>
                    <p>
                        Imagen de perfil del usuario.<br>
                        <strong>PNG, JPG</strong><br>
                        Máx. recomendado <strong>2MB</strong>
                    </p>
                </div>
        
            <!-- 🟩 Columna formulario -->
            <div class="form-column">

                <div class="mb-3">
                    <label class="form-label">Nombre completo</label>
                    <input type="text" name="nombre" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label" >Correo</label>
                    <input type="text" name="correo" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label" >Telefono</label>
                    <input type="text" name="telefono" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">¿Qué rol tendrá el usuario? </label>
                    <select name="rol_id" id="rol_id" class="form-control" required>
                        <option value="0">***selecciona un rol para el usuario ***</option>
                        <?php
                        foreach($data['roles'] as $rol):?>
                        <option value="<?= $rol['id']; ?>"> <?= $rol['nombre']; ?> </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3 d-none" id="boxLiga">
                    <label for="liga_id">Liga </label>
                    <select name="liga_id" id="liga_id" class="form-select">
                        <option value="">Seleccione liga</option>
                    </select>
                </div>

                <div class="mb-3 d-none" id="boxEquipo" class="d-none">
                    <label for="equipo_id">Equipo</label>
                    <select name="equipo_id" id="equipo_id" class="form-select">
                        <option value="">Seleccione equipo</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Usuario</label>
                    <input type="text" name="usuario" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Contraseña</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <!-- botones -->
                <button type="submit" class="btn btn-primary">Crear Usuario</button>
                <a href="index.php?page=users" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
        
    </div>

</div>


<style>

     /* ===============================
    CONTENEDOR GENERAL
    =================================*/
    .user-form-wrapper{
        display: flex;
        justify-content: center;
        padding: 40px 24px;
    }

    /* ===============================
    TARJETA DEL FORM
    =================================*/
    .user-form-card{
        background: #fff;
        border-radius: 18px;
        padding: 32px 40px;
        width: 100%;
        max-width: 1100px;
        box-shadow: 0 15px 40px rgba(0,0,0,.08);
    }

    /* ===============================
    TÍTULO
    =================================*/
    .form-title{
        font-size: 1.6rem;
        font-weight: 700;
        margin-bottom: 28px;
        border-bottom: 1px solid #eee;
        padding-bottom: 12px;
        color: #1e40af;
    }

    /* ===============================
    GRID PRINCIPAL
    =================================*/
    .user-form-grid{
        display: grid;
        grid-template-columns: 280px 1fr;
        gap: 40px;
        align-items: flex-start;
    }

    /* ===============================
    COLUMNA AVATAR
    =================================*/
    .avatar-column{
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .avatar-box{
        width: 160px;
        height: 160px;
        border-radius: 50%;
        overflow: hidden;
        cursor: pointer;
        position: relative;
        border: 3px solid #aaa;
        box-shadow: 0 10px 30px rgba(0,0,0,.15);
    }

    .avatar-box img{
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform .3s ease;
    }

    .avatar-box:hover img{
        transform: scale(1.05);
    }

    /* Overlay SOLO visual */
    .avatar-overlay{
        position: absolute;
        inset: 0;
        background: rgba(0,0,0,.45);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #fff;
        opacity: 0;
        transition: opacity .3s ease;
        pointer-events: none; /* 🔑 evita bloqueo del formulario */
    }

    .avatar-box:hover .avatar-overlay{
        opacity: 1;
    }

    .avatar-overlay span{
        font-size: 26px;
    }

    .avatar-overlay small{
        font-size: 12px;
        margin-top: 4px;
    }

    .avatar-column p{
        font-size: 13px;
        color: #666;
        line-height: 1.4;
        margin-top: 12px;
        text-align: center;
        max-width: 220px;
    }

    /* ===============================
    COLUMNA FORMULARIO
    =================================*/
    .form-column{
        width: 100%;
    }

    .form-column .mb-3{
        margin-bottom: 18px;
    }

    .form-column label{
        color:#1e40af;
        letter-spacing: 2px;
    }

    /* ===============================
    BOTONES
    =================================*/
    .form-column .btn{
        min-width: 140px;
    }

    .form-column .btn + .btn{
        margin-left: 10px;
    }

</style>

<script>

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
   
</script>


<script>

    //script para manejar previsulaizacion de avatar de usuarios

    document.addEventListener('DOMContentLoaded', () => {

        const inputAvatar = document.getElementById('avatar');

        if (!inputAvatar) return;

        const tiposPermitidos = [
            'image/jpeg',
            'image/png'
        ];

        const maxSize = 2 * 1024 * 1024; // 2MB

        inputAvatar.addEventListener('change', function () {

            const file = this.files[0];
            if (!file) return;

            // ❌ Tipo no permitido
            if (!tiposPermitidos.includes(file.type)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Archivo no permitido',
                    text: 'Solo se permiten imágenes JPG, PNG o WEBP',
                });
                this.value = '';
                return;
            }

            // ❌ Tamaño excedido
            if (file.size > maxSize) {
                Swal.fire({
                    icon: 'error',
                        title: 'Archivo demasiado grande',
                text: 'El avatar no debe superar los 2 MB',
                });
                this.value = '';
                return;
            }

            // ✅ Preview del avatar
            const reader = new FileReader();
            reader.onload = e => {
                document.getElementById('avatarPreview').src = e.target.result;
            };
            reader.readAsDataURL(file);

        });

    });

</script>


<script>

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
</script>

<script>

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


</script>