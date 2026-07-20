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
  <div class="logo-wrapper" style="display:flex; align-items:center;">
    <img src="/../afec/public/assets/img/logo.png" alt="Logo" style="height:35px;">
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


<style>

.logo-text {
  color: #cfe8ff; /* Dorado para resaltar sobre el azul */
  font-family: 'Bebas Neue', sans-serif;
  font-size: 18px;
  font-weight: bold;
  margin-left: 10px;
  line-height: 50px; /* Centra verticalmente en el header */
  text-shadow: 1px 1px 2px rgba(0,0,0,0.5); /* Sombra sutil para destacar */
  white-space: nowrap; /* Evita que se rompa en varias líneas */
}


.header-n1 {
  background: #08244d;
  height: 50px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 30px;
  border-bottom: 1px solid rgba(255, 215, 0, 0.25);
  position: relative;
  z-index: 10;
}

.icon-wrapper i {
  font-size: 22px;
  color: #fff;
  cursor: pointer;
  transition: 0.3s;
}

.icon-wrapper i:hover {
  color: #ffd700;
  transform: scale(1.15);
}

.tooltip-n1 {
  position: absolute;
  top: 55px;
  background: rgba(0,0,0,0.85);
  color: #fff;
  padding: 6px 12px;
  border-radius: 6px;
  font-size: 13px;
  display: none;
}

.icon-wrapper:hover .tooltip-n1 {
  display: block;
}

.iconos-derecha {
  display: flex;
  align-items: center;
  gap: 15px;
  margin-left: auto;
}

/* --- MODAL BUSCADOR (cortina) --- */
.modal-cortina {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  pointer-events: none;
  display: flex;
  justify-content: center;
  transition: all 0.5s ease;
  z-index: 1000;
}

.modal-cortina.active {
  pointer-events: all;
}

.modal-search-content {
  background: #fff;
  padding: 15px 20px;
  border-radius: 10px;
  width: 400px;
  margin-top: -150px; /* empieza fuera de pantalla */
  opacity: 0;
  transform: translateY(-50px);
  box-shadow: 0 10px 25px rgba(0,0,0,0.2);
  position: relative;
  transition: all 0.5s ease;
}

.modal-cortina.active .modal-search-content {
  margin-top: 60px; /* justo debajo del header */
  opacity: 1;
  transform: translateY(0);
}

.modal-search-content .close {
  position: absolute;
  top:5px; right:10px;
  font-size:18px;
  cursor:pointer;
}

.search-input {
  flex:1;
  padding:8px 10px;
  border-radius:5px;
  border:1px solid #ccc;
}

.btn-search {
  background:#08244d;
  color:#fff;
  border:none;
  padding:8px 12px;
  border-radius:5px;
  cursor:pointer;
  transition:0.3s;
}

.btn-search:hover { background:#ffd700; color:#08244d; }

/* --- MODAL LOGIN CENTRADO --- */
.modal-centro {
  position: fixed;
  top:0; left:0;
  width:100%; height:100%;
  background: rgba(0,0,0,0.6);
  display: none;
  justify-content: center;
  align-items: center;
  z-index:1000;
}

.modal-centro.active {
  display: flex;
}

.modal-login-content {
  background:#fff;
  padding:20px 25px;
  border-radius:10px;
  width:300px;
  position:relative;
  display:flex;
  flex-direction:column;
  gap:10px;
  box-shadow:0 15px 40px rgba(0,0,0,0.3);
}

.modal-login-content .close {
  position:absolute;
  top:5px; right:10px;
  font-size:18px;
  cursor:pointer;
}

.input-login {
  padding:8px;
  border:1px solid #ccc;
  border-radius:5px;
}

.btn-login-form {
  padding:8px;
  background:#08244d;
  color:#fff;
  border:none;
  border-radius:5px;
  cursor:pointer;
  transition:0.3s;
}

.btn-login-form:hover { background:#ffd700; color:#08244d; }



/* estilos para el header nivel 2 */

  .header-nivel2 {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 50px;
    background: linear-gradient(135deg, #0a1f3c, #1f3c6a);
    color: white;
    position: relative;
    overflow: hidden;
  }

    /* Patrón hexagonal de fondo */
    .header-nivel2::before {
      content: "";
      position: absolute;
      top: 0; left: 0; right: 0; bottom: 0;
      background-image: 
        repeating-linear-gradient(30deg, rgba(255,255,255,0.05) 0, rgba(255,255,255,0.05) 1px, transparent 1px, transparent 60px),
        repeating-linear-gradient(-30deg, rgba(255,255,255,0.05) 0, rgba(255,255,255,0.05) 1px, transparent 1px, transparent 60px);
      z-index: 0;
    }

    /* Secciones */
    .header-left, .header-right {
      display: flex;
      align-items: center;
      gap: 15px;
      z-index: 1;
    }
    .header-center {
      z-index: 1;
      text-align: center;
      display: flex;
      height: 100%;
      justify-content: center;
      align-items: center;
    }

    .header-center img{
      max-height:200px;
      width:auto;
    }

    /* Logos aliados / glass effect */
    /* Logos aliados / glass effect camuflados */
    .glass-logo {
      display: inline-block;
      padding: 5px;
      border-radius: 10px;
      background: rgba(255,255,255,0.02);
      backdrop-filter: blur(3px);
      border: 1px solid rgba(255,255,255,0.05);
      opacity: 0.5; /* camuflado */
      transition: all 0.4s ease;
    }
    .glass-logo img {
      height: 50px;
      filter: grayscale(70%) brightness(0.8); /* efecto camuflado */
    }
    .glass-logo:hover {
      opacity: 1;
      filter: grayscale(0%) brightness(1) drop-shadow(0 0 10px #00ffd0);
      transform: translateY(-3px) scale(1.05);
    }


  /************************************ */
.header-nivel3 {
  background: linear-gradient(135deg, #0a1f3c, #1f3c6a);
  padding: 10px 50px;
  position: relative;
  z-index: 2;
  border-top: 2px solid rgba(0,255,208,0.2);
  font-family: 'Orbitron', sans-serif;
}

/* Menú principal */
.menu-principal {
  list-style: none;
  display: flex;
  gap: 25px;
  margin: 0;
  padding: 0;
  align-items: center;
}

.menu-principal li {
  position: relative;
}

.menu-principal a {
  text-decoration: none;
  color: rgba(255,255,255,0.7);
  font-weight: bold;
  padding: 6px 0;
  transition: all 0.3s ease;
}

.menu-principal a:hover {
  color: #00ffd0;
  text-shadow: 0 0 8px #00ffd0;
}

/* Submenús base */
.submenu {
  display: none;
  position: absolute;
  list-style: none;
  top: 100%;
  left: 0;
  background: rgba(10,31,60,0.95);
  padding: 10px 0;
  border-radius: 8px;
  min-width: 190px;
  opacity: 0;
  visibility: hidden;
  transition: opacity 0.18s ease;
}

.submenu li {
  padding: 0;
}

.submenu a {
  display: block;
  padding: 8px 20px;
  color: rgba(255,255,255,0.8);
  font-weight: normal;
}

.submenu a:hover {
  color: #00ffd0;
  text-shadow: 0 0 6px #00ffd0;
}

/* 🔥 Mostrar submenú nivel 1 con estabilidad */
.menu-principal li:hover > .submenu {
  display: block;
  opacity: 1;
  visibility: visible;
}

/* 🔥 Submenú de segundo nivel */
.submenu .submenu {
  display: block;
  opacity: 0;
  visibility: hidden;
  top: 0;
  left: 100%;
  border-radius: 8px;
}

/* 🔥 Mostrar submenú de segundo nivel */
.submenu li:hover > .submenu {
  opacity: 1;
  visibility: visible;
}

/* Flechas automáticas ▼ ▸ */
.dropdown > a::after {
  content: " ▼";
  font-size: 11px;
  margin-left: 4px;
}

/* Flechas laterales automáticas para sub-submenús */
.submenu li.dropdown > a::after {
  content: " ▸";
  float: right;
}

/* Botones flotantes redes (sin cambios) */
.social-buttons {
  position: fixed;
  top: 50%;
  left: 0;
  transform: translateY(-50%);
  display: flex;
  flex-direction: column;
  gap: 10px;
  z-index: 1000;
}


.social-btn {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 15px;
  color: white;
  text-decoration: none;
  font-family: 'Roboto', sans-serif;
  font-weight: bold;
  border-radius: 0 5px 5px 0;
  transform: translateX(-120px);
  transition: transform 0.3s, background-color 0.3s;
}

.social-btn i {
  font-size: 1.2rem;
}

.social-btn span {
  white-space: nowrap;
}

/* Colores según red */
.facebook { background: #3b5998; }
.twitter { background: #1da1f2; }
.instagram { background: #e1306c; }

/* Hover: deslizar hacia afuera */
.social-btn:hover {
  transform: translateX(0);
  cursor: pointer;
}


</style>
