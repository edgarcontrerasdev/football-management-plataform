<?php session_start(); ?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Acceso | AFEC</title>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
  document.addEventListener('DOMContentLoaded', function() {
      <?php if(isset($_SESSION['info'])): ?>
          Swal.fire({
              icon: 'info',
              title: 'Información',
              text: '<?php echo $_SESSION['info']; ?>',
              timer: 2000,
              showConfirmButton: false
          });
          <?php unset($_SESSION['info']); ?>
      <?php endif; ?>

      <?php if(isset($_GET['error'])): ?>
          Swal.fire({
              icon: 'info',
              title: 'Información',
              text: '<?php echo $_GET['error']; ?>',
              timer: 2000,
              showConfirmButton: false
          });
          <?php unset($_SESSION['info']); ?>
      <?php endif; ?>
  });
</script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/login.css">
</head>
<body class="login-page">

<!-- ========== pantalla de introduccion antes del ver fomulario de login ========= -->
<div id="intro" class="intro">
  <div class="intro-card">
    <div class="intro-left">
      <h1>ASOCIACIÓN DE FÚTBOL DEL ESTADO DE COLIMA</h1>
      <p><strong>Sistema Integral de Gestión Deportiva</strong><br>
         Bienvenido. Presiona <em>Enter</em> o haz clic en el botón para continuar al sistema.</p>

      <button id="btnStart" class="start-btn">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" class="login-play-icon">
          <path d="M5 3v18l15-9L5 3z" fill="white"/>
        </svg>
        Iniciar
      </button>

      <div class="hint start-pulse">Presiona ENTER o haz clic para continuar</div>
    </div>

    <div class="intro-right">
      <div class="logo-wrap" aria-hidden="true">
        <!-- placeholder logo: uses uploaded image if available -->
        <img id="logo" src="../public/assets/img/logos/logo.png" alt="Logo (placeholder)">
      </div>
      <div class="intro-brand-copy">ASOCIACIÓN DE FÚTBOL DEL ESTADO DE COLIMA<br><small style="opacity:.8">Sistema Integral de Gestión Deportiva</small></div>
    </div>
  </div>
</div>

<!-- ========== pnatalla principal (login) ========== -->
<div id="stage" class="stage hidden">
  
  <div class="hex-bg" aria-hidden="true">

  </div>

  <div class="login-card" role="main" aria-labelledby="loginTitle">
    
    <img id="logo" src="../public/assets/img/logos/logo.png" alt="Logo de la liga">
    <div class="login-title" id="loginTitle">ASOCIACIÓN DE FÚTBOL DEL ESTADO DE COLIMA</div>
    <div class="login-sub">Sistema Integral de Gestión Deportiva</div>

    <form id="formLogin" class="login-form" action="index.php?page=auth&action=login" method="post">
      <div class="mb-3">
        <label class="form-label">Usuario</label>
        <input id="usuario" name="usuario" class="form-control" autocomplete="username" />
      </div>
      <div class="mb-3">
        <label class="form-label">Contraseña</label>
        <input id="password" name="password" type="password" class="form-control" autocomplete="current-password" />
      </div>

      <button type="submit" class="btn btn-primary-custom">Ingresar</button>
    
    </form>

    <div class="login-help">¿Olvidaste tu contraseña? &middot; Contacta a administración</div>
  </div>
</div>

<script>
  /* ---------- Intro -> Stage transition ---------- */
  const intro = document.getElementById('intro');
  const stage = document.getElementById('stage');
  const btnStart = document.getElementById('btnStart');

  function showStage() {
    // fancy fade
    intro.classList.add('hidden');
    setTimeout(()=> {
      intro.style.display = 'none';
      stage.classList.remove('hidden');
      // small focus on username
      setTimeout(()=> document.getElementById('usuario').focus(), 300);
    }, 720);
  }

  btnStart.addEventListener('click', showStage);

  document.addEventListener('keydown', (e)=>{
    if(!intro.classList.contains('hidden') && (e.key === 'Enter' || e.key === ' ')) {
      e.preventDefault();
      showStage();
    }
  });


  /* ---------- Accessibility: focus trap / keyboard ---------- */
  document.addEventListener('keydown', (e)=>{
    if(e.key === 'Escape') {
      // if stage visible, go back to intro
      if(!intro.classList.contains('hidden')) return;
      intro.style.display = '';
      intro.classList.remove('hidden');
      stage.classList.add('hidden');
    }
  });

  /* ---------- If logo missing, show text fallback ---------- */
  function checkLogo(imgEl)
  {
    imgEl.onerror = function(){
      // hide image and show text (already title/subtitle exist)
      this.style.display = 'none';
    };
  }

  document.querySelectorAll('#logo').forEach(checkLogo);

</script>

<script>
  const formLogin = document.getElementById('formLogin');

  formLogin.addEventListener('submit', function(e) {
      e.preventDefault(); // ❌ evita recargar la página

      const formData = new FormData(formLogin);
      const data = {
          usuario: formData.get('usuario'),
          password: formData.get('password')
      };

      //ventana de loader mientras se validan credenciales del usuario
      Swal.fire({
          title:'Validando credenciales',
          text:'Espere un momento...',
          timer:1200,
          allowOutsideClick:false,
          allowEscapeKey:false,
          didOpen: ()=>{
            Swal.showLoading();
          }
      });
  
      fetch(formLogin.action, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(data)
      })
      .then(res => res.json())
      .then(res => {
          //establecemos minimo 1.5s para cerrar la ventana y percibir la animacion
          setTimeout(()=>{
            //cerramos la ventana de loader de validacion de credenciales
            Swal.close();

            if(res.success){
              Swal.fire({
                  icon: 'success',
                  title: '¡Bienvenido!',
                  text: res.mensaje,
                  timer: 1300,
                  showConfirmButton: false
              }).then(() => {
                  window.location.href = '/afec/admin/index.php'; // redirige al dashboard
              });
            } else {
              Swal.fire({
                  icon: 'error',
                  title: 'Error',
                  text: res.mensaje
              });
            }
          },1400);
      })
      .catch(err => {
          //aqui tambien se debe cerrar la ventana de loader de validacion de credenciales
          //ya que calle en error de servidor y deber mostrarse el error
          Swal.close();

          console.error('Error en la petición:', err);
          Swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'No se pudo conectar con el servidor'
          });
      });
  });
</script>

</body>
</html>
