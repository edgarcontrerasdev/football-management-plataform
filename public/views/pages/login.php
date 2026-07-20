<?php session_start(); ?>

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

<style>
    
    :root{
      --royal:#0b3ea6;         /* azul royal */
      --silver:#c9d6dc;        /* plata suave */
      --neon: #66b9ff;         /* neon highlight */
      --bg-dark:#041226;
    }

    *{box-sizing:border-box;font-family:Inter,ui-sans-serif,system-ui,-apple-system,"Segoe UI",Roboto,"Helvetica Neue",Arial;}
    html,body{
      height:100%;
      margin:0;
      color:#fff;
      background:
        linear-gradient(
          rgba(4,18,38,0.88),
          rgba(4,18,38,0.88)
        ),
        url("../../assets/img/fondos/estadio.jpg");

      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      background-attachment: fixed;
    }

    /* ---------- Intro / Start Screen ---------- */
    .intro {
      position:fixed; inset:0; display:flex; align-items:center; justify-content:center;
      background: radial-gradient(1200px 600px at 10% 20%, rgba(12,58,128,0.25), transparent 10%),
                  radial-gradient(800px 400px at 90% 80%, rgba(192,200,208,0.07), transparent 15%),
                  linear-gradient(180deg,#021225 0%, #041226 100%);
      z-index:50;
      transition:opacity .8s ease, transform .8s ease;
    }

    .intro.hidden { opacity:0; pointer-events:none; transform:scale(.98); }

    /* Intro card */
    .intro-card{
      width:880px; max-width:94%;
      display:grid; grid-template-columns: 1fr 480px; gap:30px; align-items:center;
      padding:36px; border-radius:14px;
      background: linear-gradient(135deg, rgba(11,62,166,0.18), rgba(201,214,220,0.03));
      box-shadow: 0 20px 60px rgba(4,18,38,0.7), 0 0 40px rgba(102,185,255,0.06) inset;
      border: 1px solid rgba(200,214,220,0.06);
    }

    /* Left: title */
    .intro-left h1{
      margin:0 0 8px 0; font-size:32px; letter-spacing:1px; color:var(--silver);
      text-shadow: 0 4px 18px rgba(11,62,166,0.35);
    }
    .intro-left p{ margin:0 0 18px 0; color: #cfdbe6; font-size:15px; }
    .start-btn{
      display:inline-flex; align-items:center; gap:12px; padding:12px 18px; border-radius:10px;
      background: linear-gradient(90deg, var(--royal), #0041a8);
      color:white; font-weight:700; border:none; cursor:pointer;
      box-shadow: 0 10px 30px rgba(11,62,166,0.35), 0 0 12px rgba(102,185,255,0.12) inset;
      transition: transform .12s ease, box-shadow .12s ease;
    }
    .start-btn:active{ transform:translateY(2px); box-shadow: 0 6px 20px rgba(11,62,166,0.25); }
    .hint { margin-top:12px; color:#b8cbe0; font-size:13px; }

    /* Right: hero / placeholder */
    .intro-right{
      display:flex; align-items:center; justify-content:center; gap:18px; flex-direction:column;
    }
    .logo-wrap{
      width:220px; height:220px; border-radius:18px; display:flex; align-items:center; justify-content:center;
      background: linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01));
      border: 1px solid rgba(201,214,220,0.06);
      box-shadow: 0 12px 30px rgba(11,62,166,0.28);
    }
    .logo-wrap img{ max-width:84%; max-height:84%; display:block; filter: drop-shadow(0 6px 18px rgba(102,185,255,0.12)); }

    /* prompt "press start" blinking */
    .start-pulse{ margin-top:6px; color:var(--neon); font-weight:700; letter-spacing:1px;
      animation: pulse 1.3s infinite; font-size:13px;}
    @keyframes pulse{ 0%{opacity:1; transform:translateY(0)}50%{opacity:.35;transform:translateY(3px)}100%{opacity:1;transform:translateY(0)} }

    /* ---------- Main stage (login area) ---------- */
    .stage { position:fixed; inset:0; display:flex; align-items:center; justify-content:center; }
    .stage.hidden{ display:none; }

    /* Hexagon neon background (SVG data URL embedded to avoid external images) */
    .hex-bg {
      position:fixed; inset:-25% -25% auto -25%; width:150%; height:150%; z-index:1;
      background-image: url("logo.jpg' width='140' height='120' viewBox='0 0 140 120'%3E%3Cpath d='M35 4 L105 4 L136 60 L105 116 L35 116 L4 60 Z' stroke='%2366b9ff' stroke-opacity='0.25' stroke-width='3' fill='none'/%3E%3C/svg%3E");
      background-repeat:repeat; background-size:200px 170px;
      filter: drop-shadow(0 0 10px rgba(102,185,255,0.12));
      opacity:0.45; pointer-events:none;
      animation: hexMove 24s linear infinite, hexGlow 3.6s ease-in-out infinite;
    }
    @keyframes hexMove { 0%{transform:translate(0,0) rotate(0);} 100%{transform:translate(-360px,-220px) rotate(1deg);} }
    @keyframes hexGlow { 0%,100%{filter: drop-shadow(0 0 6px rgba(102,185,255,0.08)); } 50%{filter:drop-shadow(0 0 20px rgba(102,185,255,0.18));} }

    /* stage content (login card) */
    .login-card {
      position:relative; z-index:5; width:420px; max-width:92%;
      background: linear-gradient(180deg, rgba(255,255,255,0.04), rgba(255,255,255,0.02));
      border-radius:14px; padding:32px; box-shadow: 0 20px 50px rgba(2,10,25,0.6);
      border:1px solid rgba(200,214,220,0.04); backdrop-filter: blur(10px);
      display:flex; flex-direction:column; align-items:center;
    }

    /* logo in login */
    #logo { width:120px; height:auto; margin-bottom:10px; filter: drop-shadow(0 8px 28px rgba(102,185,255,0.12)); }

    /* title and subtitle */
    .login-title { font-size:18px; font-weight:800; color:var(--silver); text-align:center; margin-bottom:4px; }
    .login-sub { font-size:12px; color:#bacfe0; margin-bottom:18px; text-align:center; }

    /* form fields */
    .form-control { border-radius:10px; border:1px solid rgba(102,185,255,0.12); background: rgba(255,255,255,0.02); color:#eef8ff; }
    .form-label{ color:#cfe8ff; font-weight:600; font-size:13px; }
    .btn-primary-custom {
      width:100%; padding:10px 14px; margin-top:10px; background:linear-gradient(90deg,var(--royal), #0041a8);
      border:none; color:white; font-weight:700; border-radius:10px; box-shadow:0 8px 26px rgba(11,62,166,0.28);
    }

    /* error shake effect */
    .input-error { animation: shakeX .36s; border-color:#ff8b8b !important; }
    @keyframes shakeX{ 0%{transform:translateX(0)}25%{transform:translateX(-6px)}50%{transform:translateX(6px)}75%{transform:translateX(-4px)}100%{transform:translateX(0)} }

    /* small responsive */
    @media (max-width:720px){
      .intro-card{ grid-template-columns: 1fr; padding:20px; gap:18px; }
      .intro-right{ order:-1; }
      .logo-wrap{ width:140px; height:140px; }
      .login-card{ width:92%; padding:24px; }
    }
</style>

<!-- ========== pantalla de introduccion antes del ver fomulario de login ========= -->
<div id="intro" class="intro">
  <div class="intro-card">
    <div class="intro-left">
      <h1>ASOCIACIÓN DE FÚTBOL DEL ESTADO DE COLIMA</h1>
      <p><strong>Sistema Integral de Gestión Deportiva</strong><br>
         Bienvenido. Presiona <em>Enter</em> o haz clic en el botón para continuar al sistema.</p>

      <button id="btnStart" class="start-btn">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" style="transform:translateY(1px)">
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
      <div style="color:#bcd9f8; font-weight:600">ASOCIACIÓN DE FÚTBOL DEL ESTADO DE COLIMA<br><small style="opacity:.8">Sistema Integral de Gestión Deportiva</small></div>
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

    <form id="formLogin" action="index.php?page=auth&action=login" method="post" style="width:100%; max-width:360px;">
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

    <div style="margin-top:12px; font-size:13px; color:#9fb7d6">¿Olvidaste tu contraseña? &middot; Contacta a administración</div>
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

