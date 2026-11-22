<!-- Botones redes sociales fijos -->
    <div class="social-buttons">
        <a href="https://facebook.com" target="_blank" class="social-btn facebook">
           
            <span>Facebook</span>
             <i class="fab fa-facebook-f"></i>
        </a>
        <a href="https://twitter.com" target="_blank" class="social-btn twitter">
            <i class="fab fa-twitter"></i>
            <span>Twitter</span>
        </a>
        <a href="https://instagram.com" target="_blank" class="social-btn instagram">
            <i class="fab fa-instagram"></i>
            <span>Instagram</span>
        </a>
    </div>

<!-- N I V E L  1 -->
<div class="header-n1">

  <!-- IZQUIERDA: Logo -->
  <div class="logo-wrapper">
    <img src="/../afec/public/assets/img/logo.png" alt="Logo" class="logo-header">
    <span class="logo-text">AFEC - Asociación de Futbol del Estado de Colima</span>
  </div>
  
  <!-- DERECHA: Buscador + Login -->
  <div class="iconos-derecha">
    <div class="icon-wrapper" id="btnSearch">
      <i class="fas fa-search"></i>
      <span class="tooltip-n1">Buscar</span>
    </div>

    <div class="icon-wrapper" id="btnLogin">
      <a href="index.php?page=login">
      <i class="fas fa-user-circle"></i>
      <span class="tooltip-n1">Iniciar sesión</span></a>
    </div>
  </div>
</div>

<!-- MODAL BUSCADOR -->
<div id="modalSearch" class="modal-cortina">
  <div class="modal-search-content">
    <span class="close" id="closeSearch">&times;</span>
    <input type="text" placeholder="Buscar..." class="search-input">
    <button class="btn-search">Buscar</button>
  </div>
</div>

<!-- MODAL LOGIN -->
<div id="modalLogin" class="modal-centro">
  <div class="modal-login-content">
    <span class="close" id="closeLogin">&times;</span>
    <h3>Iniciar sesión</h3>
    <input type="text" placeholder="Usuario" class="input-login">
    <input type="password" placeholder="Contraseña" class="input-login">
    <button class="btn-login-form">Entrar</button>
  </div>
</div>



<!-- Header Nivel 2 -->
<header class="header-nivel2">
  <!-- Logos aliados / enlaces izquierda -->
  <div class="header-left">
    <a href="https://amateur.fmf.mx" target="_blank" class="glass-logo">
      <img src="/../afec/public/assets/img/logos/amateur.png" alt="FMF">
    </a>
    <a href="https://fmf.mx" target="_blank" class="glass-logo">
      <img src="/../afec/public/assets/img/logos/fmf.png" alt="Sector Amateur">
    </a>
    <a href="#" target="_blank" class="glass-logo">
      <img src="/../afec/public/assets/img/logos/fifa.jpg" alt="Otro">
    </a>
  </div>

  <!-- Logo central -->
  <div class="header-center">
    <img src="/../afec/public/assets/img/logo.png" alt="Logo Principal" class="logo-central">
  </div>

  <!-- Buscador e íconos sociales -->
  <div class="header-right">
    
  </div>
  
</header>

<nav class="header-nivel3">
  <ul class="menu-principal">
    <li><a href="index.php?page=home"><i class="fas fa-home"></i></a></li>
    <li><a href="index.php?page=liga">Torneos</a></li>
    <li><a href="index.php?page=equipos">Equipos</a></li>
    <li><a href="index.php?page=afiliaciones">Afiliaciones</a></li>
    <li><a href="index.php?page=estadisticas">Estadísticas</a></li>
    <li class="dropdown">
      <a href="#">Ligas </a>
      <ul class="submenu">
        <li class="dropdown">
          <a href="#">Libres</a>
          <ul class="submenu">
            <li><a href="index.php?page=liga">Primera Amateur</a></li>
            <li><a href="index.php?page=liga">Primera Fuerza</a></li>
            <li><a href="index.php?page=liga">Primera Especial</a></li>
            <li><a href="index.php?page=liga">Intermedia</a></li>
          </ul>
        </li>
        <li><a href="index.php?page=liga">Juveniles</a></li>
        <li><a href="index.php?page=liga">Infantiles</a></li>
      </ul>
    </li>
    <li><a href="index.php?page=contacto">Contacto</a></li>
  </ul>
</nav>



