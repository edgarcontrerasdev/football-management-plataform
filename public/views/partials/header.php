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
    <img src="/../afec/public/assets/img/logo.png" alt="AFEC - Asociación de Fútbol del Estado de Colima" class="logo-header">
    <span class="logo-text">AFEC - Asociación de Futbol del Estado de Colima</span>
  </div>
  
  <!-- DERECHA: Buscador + Login -->
  <div class="iconos-derecha">
    <button type="button" class="icon-wrapper" id="btnSearch" aria-label="Abrir buscador">
      <i class="fas fa-search" aria-hidden="true"></i>
      <span class="tooltip-n1" aria-hidden="true">Buscar</span>
    </button>

    <button type="button" class="icon-wrapper" id="btnLogin" aria-label="Abrir inicio de sesión">
      <i class="fas fa-user-circle" aria-hidden="true"></i>
      <span class="tooltip-n1" aria-hidden="true">Iniciar sesión</span>
    </button>
  </div>
</div>

<!-- MODAL BUSCADOR -->
<div id="modalSearch" class="modal-cortina">
  <div class="modal-search-content">
    <button type="button" class="close" id="closeSearch" aria-label="Cerrar buscador">&times;</button>
    <input type="text" placeholder="Buscar..." class="search-input" aria-label="Texto de búsqueda">
    <button class="btn-search">Buscar</button>
  </div>
</div>

<!-- MODAL LOGIN -->
<div id="modalLogin" class="modal-centro">
  <div class="modal-login-content" role="dialog" aria-modal="true" aria-labelledby="loginModalTitle">
    <button type="button" class="close" id="closeLogin" aria-label="Cerrar inicio de sesión">&times;</button>
    <h3 id="loginModalTitle">Iniciar sesión</h3>
    <input type="text" placeholder="Usuario" class="input-login" aria-label="Usuario">
    <input type="password" placeholder="Contraseña" class="input-login" aria-label="Contraseña">
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
    <img src="/../afec/public/assets/img/logo.png" alt="Logotipo de AFEC" class="logo-central">
  </div>

  <!-- Buscador e íconos sociales -->
  <div class="header-right">
    
  </div>
  
</header>

<nav class="header-nivel3" aria-label="Navegación principal">
  <ul class="menu-principal">
    <li><a href="index.php?page=home"><i class="fas fa-home"></i></a></li>
    <li><a href="index.php?page=liga">Torneos</a></li>
    <li><a href="index.php?page=equipos">Equipos</a></li>
    <li><a href="index.php?page=afiliaciones">Afiliaciones</a></li>
    <li><a href="index.php?page=estadisticas">Estadísticas</a></li>
    <li class="dropdown">
      <a href="#" aria-haspopup="true" aria-expanded="false" aria-controls="submenu-ligas">Ligas</a>
      <ul class="submenu" id="submenu-ligas">
        <li class="dropdown">
          <a href="#" aria-haspopup="true" aria-expanded="false" aria-controls="submenu-libres">Libres</a>
          <ul class="submenu" id="submenu-libres">
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



