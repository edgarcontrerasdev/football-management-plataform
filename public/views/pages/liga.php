<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Liga de Futbol - AFEC</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@400;700&display=swap" rel="stylesheet">
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Roboto Slab', serif; }
    body { background-color: #f4f4f4; color: #333; line-height: 1.4; }
    .container { max-width: 1200px; margin: auto; padding: 20px; }
    
    /* Header Liga */
    .liga-header { display: flex; align-items: center; gap: 20px; margin-bottom: 30px; background: #004aad; color: #fff; padding: 20px; border-radius: 10px; }
    .liga-header img { width: 100px; height: 100px; object-fit: cover; border-radius: 50%; border: 3px solid gold; }
    .liga-header h1 { font-size: 2rem; }
    .liga-header p { font-size: 1rem; margin-top: 5px; }

    /* Info general */
    .info-general { display: flex; gap: 15px; margin-bottom: 30px; }
    .info-card { flex: 1; background: #fff; padding: 15px; border-radius: 10px; text-align: center; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
    .info-card h2 { font-size: 1.5rem; color: #004aad; }
    .info-card p { margin-top: 5px; font-size: 0.9rem; }

    /* Equipos */
    .equipos { margin-bottom: 30px; }
    .equipos h2 { margin-bottom: 15px; color: #004aad; }
    .equipos-grid { display: grid; grid-template-columns: repeat(auto-fill,minmax(150px,1fr)); gap: 15px; }
    .equipo-card { background: #fff; border-radius: 10px; padding: 10px; text-align: center; box-shadow: 0 2px 5px rgba(0,0,0,0.1); transition: transform 0.2s; cursor: pointer; }
    .equipo-card:hover { transform: scale(1.05); }
    .equipo-card img { width: 80px; height: 80px; object-fit: cover; margin-bottom: 5px; border-radius: 50%; }

    /* Próximos partidos */
    .partidos { margin-bottom: 30px; }
    .partidos h2 { margin-bottom: 15px; color: #004aad; }
    .partido-card { background: #fff; padding: 15px; margin-bottom: 10px; border-radius: 10px; display: flex; justify-content: space-between; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }

    /* Top goleadores */
    .top-goleadores { margin-bottom: 30px; }
    .top-goleadores h2 { margin-bottom: 15px; color: #004aad; }
    .goleador-card { background: #fff; padding: 10px; border-radius: 10px; display: flex; align-items: center; gap: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }

    .goleador-card img { width: 50px; height: 50px; border-radius: 50%; object-fit: cover; }

    /* Mesa directiva */
    .directiva { margin-bottom: 30px; }
    .directiva h2 { margin-bottom: 15px; color: #004aad; }
    .directiva-grid { display: grid; grid-template-columns: repeat(auto-fill,minmax(150px,1fr)); gap: 15px; }
    .directivo-card { background: #fff; padding: 10px; border-radius: 10px; text-align: center; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
    .directivo-card img { width: 80px; height: 80px; border-radius: 50%; object-fit: cover; margin-bottom: 5px; }
    .directivo-card p { font-size: 0.9rem; margin-top: 3px; }
  </style>
</head>
<body>

  <div class="container">

    <!-- Header Liga -->
    <div class="liga-header">
      <img src="https://via.placeholder.com/100x100.png?text=Logo" alt="Logo Liga">
      <div>
        <h1>Liga Colima Premier</h1>
        <p>Competencia oficial de fútbol del Estado de Colima</p>
      </div>
    </div>

    <!-- Info General -->
    <div class="info-general">
      <div class="info-card">
        <h2>12</h2>
        <p>Equipos Activos</p>
      </div>
      <div class="info-card">
        <h2>2</h2>
        <p>Torneos Actuales</p>
      </div>
      <div class="info-card">
        <h2>Próx: 12 Dic</h2>
        <p>Partido Destacado</p>
      </div>
      <div class="info-card">
        <h2>Los Halcones</h2>
        <p>Último Campeón</p>
      </div>
    </div>

    <!-- Equipos -->
    <div class="equipos">
      <h2>Equipos Participantes</h2>
      <div class="equipos-grid">
        <div class="equipo-card">
          <img src="https://via.placeholder.com/80" alt="Equipo 1">
          <p>Los Halcones</p>
        </div>
        <div class="equipo-card">
          <img src="https://via.placeholder.com/80" alt="Equipo 2">
          <p>Atlético Colima</p>
        </div>
        <div class="equipo-card">
          <img src="https://via.placeholder.com/80" alt="Equipo 3">
          <p>CD Dorados</p>
        </div>
        <div class="equipo-card">
          <img src="https://via.placeholder.com/80" alt="Equipo 4">
          <p>FC Colimán</p>
        </div>
      </div>
    </div>

    <!-- Próximos Partidos -->
    <div class="partidos">
      <h2>Próximos Partidos</h2>
      <div class="partido-card">
        <span>Los Halcones vs Atlético Colima</span>
        <span>12 Dic - 17:00</span>
      </div>
      <div class="partido-card">
        <span>CD Dorados vs FC Colimán</span>
        <span>13 Dic - 19:00</span>
      </div>
    </div>

    <!-- Top Goleadores -->
    <div class="top-goleadores">
      <h2>Top Goleadores</h2>
      <div class="goleador-card">
        <img src="https://via.placeholder.com/50" alt="Jugador 1">
        <p>Carlos Pérez - 8 goles</p>
      </div>
      <div class="goleador-card">
        <img src="https://via.placeholder.com/50" alt="Jugador 2">
        <p>Juan López - 6 goles</p>
      </div>
      <div class="goleador-card">
        <img src="https://via.placeholder.com/50" alt="Jugador 3">
        <p>Pedro García - 5 goles</p>
      </div>
    </div>

    <!-- Mesa Directiva -->
    <div class="directiva">
      <h2>Mesa Directiva</h2>
      <div class="directiva-grid">
        <div class="directivo-card">
          <img src="https://via.placeholder.com/80" alt="Presidente">
          <p>Presidente</p>
          <p>Andrés Torres</p>
        </div>
        <div class="directivo-card">
          <img src="https://via.placeholder.com/80" alt="Vicepresidente">
          <p>Vicepresidente</p>
          <p>Lucía Martínez</p>
        </div>
        <div class="directivo-card">
          <img src="https://via.placeholder.com/80" alt="Secretario">
          <p>Secretario</p>
          <p>Carlos Ramírez</p>
        </div>
        <div class="directivo-card">
          <img src="https://via.placeholder.com/80" alt="Tesorero">
          <p>Tesorero</p>
          <p>María González</p>
        </div>
      </div>
    </div>

  </div>

</body>
</html>
