<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Home Ligas FIFA Ultra PRO Responsive</title>

<style>

  body {
    font-family: 'Bebas', sans-serif;
    margin: 0;
    padding: 0;
    background: #f5f5f5;
    overflow-x: hidden;
  }

  .sponsor {
    width: 100px;
    text-align: center;
  }

  .sponsor img {
    width: 100%;
    object-fit: contain;
    filter: grayscale(50%);
    transition: filter 0.3s, transform 0.3s;
  }

  .sponsor img:hover {
    filter: grayscale(0%);
    transform: scale(1.05);
  }


.slider-wrapper {
  width: 95%;
  max-width: 1400px;
  margin: 40px auto;
  position: relative;
}

.slider {
  position: relative;
  overflow: hidden;
  border-radius: 20px;
  background: #111;
  height: 300px;
}

.slides {
  display: flex;
  transition: transform 0.7s ease-in-out;
}

.slide {
  min-width: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
}

.card-liga {
  width: 80%;
  max-width: 900px;
  height: 320px;
  border-radius: 20px;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 25px;
  background: linear-gradient(to top, #1e1e1e 0%, #111 100%);
  color: #fff;
  transform-style: preserve-3d;
  box-shadow: 0 15px 40px rgba(0,0,0,0.5);
  transition: transform 0.5s, box-shadow 0.5s;
}

.card-liga:hover {
  transform: scale(1.05);
  box-shadow: 0 30px 60px rgba(0,0,0,0.6);
}

.card-liga img.liga-bg {
  position: absolute;
  top: 0; left: 0;
  width: 100%; height: 100%;
  object-fit: cover;
  opacity: 0.2;
  transform: translateZ(-50px) scale(1.1);
  transition: transform 0.5s;
}

.card-liga:hover img.liga-bg {
  transform: translateZ(-20px) scale(1.15);
}

.card-liga .info {
  z-index: 2;
  max-width: 60%;
}

.card-liga h2 {
  font-size: 30px;
  font-family: 'Bebas Neue', sans-serif;
  margin-bottom: 10px;
}

.card-liga p {
  font-size: 16px;
  line-height: 1.4;
}

.card-liga img.logo {
  width: 160px;
  height: 160px;
  border-radius: 50%;
  z-index: 2;
  transition: transform 0.3s;
  cursor: pointer;
}

.card-liga img.logo:hover {
  transform: scale(1.3);
}

.card-liga .qr-btn {
  position: absolute;
  bottom: 20px; right: 20px;
  background: #ffcc00;
  color: #111;
  padding: 10px 14px;
  border-radius: 8px;
  font-weight: bold;
  text-decoration: none;
  transition: transform 0.3s, background 0.3s;
}

.card-liga .qr-btn:hover {
  transform: scale(1.15);
  background: #ffaa00;
}

/* Navegación */
.nav {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  background: rgba(0,0,0,0.5);
  color: #fff;
  border: none;
  font-size: 2rem;
  padding: 10px 15px;
  cursor: pointer;
  border-radius: 5px;
  z-index: 3;
  transition: background 0.3s;
}

.nav:hover {
  background: rgba(0,0,0,0.8);
}

.nav.prev { left: 10px; }
.nav.next { right: 10px; }

/* Indicadores */
.dots {
  position: absolute;
  bottom: 15px;
  left: 50%;
  transform: translateX(-50%);
  display: flex;
  gap: 10px;
  z-index: 3;
}

.dot {
  width: 12px;
  height: 12px;
  background: rgba(255,255,255,0.5);
  border-radius: 50%;
  cursor: pointer;
  transition: background 0.3s;
}

.dot.active {
  background: #ffcc00;
}

/* Responsive */
@media (max-width: 900px) {
  .slider { height: 320px; }
  .card-liga { width: 90%; height: 280px; padding: 20px; }
  .card-liga img.logo { width: 130px; height: 130px; }
}

@media (max-width: 600px) {
  .slider { height: 260px; }
  .card-liga { width: 95%; height: 240px; padding: 15px; }
  .card-liga img.logo { width: 110px; height: 110px; }
}


/* Navegación */
.nav {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  background: rgba(0,0,0,0.5);
  color: #fff;
  border: none;
  font-size: 2rem;
  padding: 10px 15px;
  cursor: pointer;
  border-radius: 5px;
  z-index: 3;
  transition: background 0.3s;
}

.nav:hover {
  background: rgba(0,0,0,0.8);
}

.nav.prev { left: 10px; }
.nav.next { right: 10px; }

  /* Tarjeta liga */
  .card-liga {
    position: relative;
    width: 750px;
    max-width: 95%;
    height: 300px;
    border-radius: 15px;
    overflow: hidden;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px;
    background: linear-gradient(to top, #1e1e1e 0%, #111 100%);
    transform-style: preserve-3d;
    transition: transform 0.5s, box-shadow 0.5s;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3), 0 20px 40px rgba(0,0,0,0.2);
  }

  .card-liga:hover {
    transform: rotateY(10deg) scale(1.05);
    box-shadow: 0 30px 60px rgba(0,0,0,0.5), 0 40px 80px rgba(0,0,0,0.3);
  }

  .card-liga img.liga-bg {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    opacity: 0.25;
    transform: translateZ(-50px) scale(1.1);
    transition: transform 0.5s;
  }

  .card-liga:hover img.liga-bg {
    transform: translateZ(-20px) scale(1.15);
  }

  .card-liga::after {
    content: "";
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle at center, rgba(255,255,255,0.1) 0%, transparent 60%);
    transform: rotate(45deg);
    pointer-events: none;
    transition: transform 0.5s;
  }

  .card-liga:hover::after {
    transform: rotate(0deg);
  }

  .card-liga .info {
    z-index: 2;
    max-width: 60%;
  }

  .card-liga h2 {
    font-size: 28px;
    margin: 0 0 10px 0;
  }

  .card-liga p {
    font-size: 16px;
    line-height: 1.3;
  }

  .card-liga img.logo {
    width: 150px;
    height: 150px;
    object-fit: cover;
    border-radius: 50%;
    z-index: 2;
    transition: transform 0.3s;
    cursor: pointer;
  }

  .card-liga img.logo:hover {
    transform: scale(1.25);
  }

  .card-liga .qr-btn {
    position: absolute;
    bottom: 20px;
    right: 20px;
    background: #ffcc00;
    color: #111;
    padding: 8px 12px;
    border-radius: 5px;
    font-weight: bold;
    text-decoration: none;
    transition: transform 0.3s, background 0.3s;
  }

  .card-liga .qr-btn:hover {
    transform: scale(1.15);
    background: #ffaa00;
  }

  /* Contenido debajo */
  .content {
    max-width: 1200px;
    margin: 40px auto;
    padding: 0 20px;
  }

  .content h2 {
    text-align: center;
    margin-bottom: 20px;
    font-size: 32px;
    color: #222;
  }

  .cards {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    justify-content: center;
  }

  .card {
    background: white;
    padding: 20px;
    width: 250px;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    text-align: center;
    transition: transform 0.3s, box-shadow 0.3s;
    cursor: pointer;
    opacity: 0;
    transform: translateY(30px);
    animation: cardFadeIn 0.6s forwards;
  }

  .card:nth-child(1) { animation-delay: 0.2s; }
  .card:nth-child(2) { animation-delay: 0.4s; }
  .card:nth-child(3) { animation-delay: 0.6s; }

  .card:hover {
    transform: translateY(-10px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.2);
  }

  .card img {
    width: 100px;
    height: 100px;
    object-fit: cover;
    border-radius: 50%;
    margin-bottom: 15px;
    transition: transform 0.3s;
  }

  .card:hover img {
    transform: scale(1.1);
  }

  @keyframes cardFadeIn {
    to { opacity: 1; transform: translateY(0); }
  }

  /* Responsive */
  @media (max-width: 900px) {
    .slider-wrapper {
      flex-direction: column;
      gap: 10px;
    }
    .slider {
      height: 300px;
    }
    .card-liga {
      height: 280px;
      padding: 15px;
    }
    .card-liga .info {
      max-width: 55%;
    }
    .card-liga h2 { font-size: 24px; }
    .card-liga p { font-size: 14px; }
    .card-liga img.logo { width: 120px; height: 120px; }
  }

  @media (max-width: 600px) {
    .slider { height: 250px; }
    .card-liga { height: 240px; padding: 10px; }
    .card-liga .info { max-width: 50%; }
    .card-liga h2 { font-size: 20px; }
    .card-liga p { font-size: 12px; }
    .card-liga img.logo { width: 100px; height: 100px; }
  }
</style>

</head>
<body>

<div class="slider-wrapper">
  <div class="slider" id="slider">

    <!-- Botones de navegación -->
    <button class="nav prev" onclick="prevSlide()">&#10094;</button>
    <button class="nav next" onclick="nextSlide()">&#10095;</button>

    <!-- Slides -->
    <div class="slides" id="slides">
      
      <div class="slide">
        <div class="card-liga">
          <img src="https://afec.com.mx/images/galeria/Galeria_LigaScotiabank_1_c.jpg" class="liga-bg">
          <div class="info">
            <h2>Liga Nacional</h2>
            <p>Temporada 2025. Equipos de todo el país compitiendo al más alto nivel.</p>
          </div>
          <img src="https://picsum.photos/150/150?team1" class="logo">
          <a href="#" class="qr-btn">QR</a>
        </div>
      </div>

      <div class="slide">
        <div class="card-liga">
          <img src="https://afec.com.mx/images/galeria/Galeria_LigaScotiabank_2_c.jpg" class="liga-bg">
          <div class="info">
            <h2>Liga Regional</h2>
            <p>Torneo Apertura. Jóvenes talentos destacando en cada partido.</p>
          </div>
          <img src="https://picsum.photos/150/150?team2" class="logo">
          <a href="#" class="qr-btn">QR</a>
        </div>
      </div>

      <div class="slide">
        <div class="card-liga">
          <img src="https://afec.com.mx/images/galeria/galeria_1_c.jpg" class="liga-bg">
          <div class="info">
            <h2>Liga Juvenil</h2>
            <p>Finales de la temporada. Promesas deportivas mostrando su mejor nivel.</p>
          </div>
          <img src="https://picsum.photos/150/150?team3" class="logo">
          <a href="#" class="qr-btn">QR</a>
        </div>
      </div>

    </div>

    <!-- Indicadores -->
    <div class="dots" id="dots"></div>

  </div>
</div>


<div class="content">
  <h2>Equipos Destacados</h2>
  <div class="cards">
    <div class="card">
      <img src="https://picsum.photos/100/100?1" alt="Equipo 1">
      <h3>Equipo A</h3>
      <p>Participa en la liga nacional, destacando por su gran desempeño y trayectoria.</p>
    </div>
    <div class="card">
      <img src="https://picsum.photos/100/100?2" alt="Equipo 2">
      <h3>Equipo B</h3>
      <p>Un equipo joven con mucho potencial en los torneos regionales.</p>
    </div>
    <div class="card">
      <img src="https://picsum.photos/100/100?3" alt="Equipo 3">
      <h3>Equipo C</h3>
      <p>Con un fuerte enfoque en desarrollo juvenil y campeonatos locales.</p>
    </div>
  </div>
</div>
 
<script>
  // --- BUSCADOR ---
  const btnSearch = document.getElementById('btnSearch');
  const modalSearch = document.getElementById('modalSearch');
  const closeSearch = document.getElementById('closeSearch');

  btnSearch.addEventListener('click', ()=>{
    modalSearch.classList.add('active');
  });
  closeSearch.addEventListener('click', ()=>{
    modalSearch.classList.remove('active');
  });
  window.addEventListener('click', e=>{
    if(e.target === modalSearch) modalSearch.classList.remove('active');
  });

  // --- LOGIN ---
  const btnLogin = document.getElementById('btnLogin');
  const modalLogin = document.getElementById('modalLogin');
  const closeLogin = document.getElementById('closeLogin');

  btnLogin.addEventListener('click', ()=>{
    modalLogin.classList.add('active');
  });
  closeLogin.addEventListener('click', ()=>{
    modalLogin.classList.remove('active');
  });
  window.addEventListener('click', e=>{
    if(e.target === modalLogin) modalLogin.classList.remove('active');
  });
</script>

</body>
</html>
