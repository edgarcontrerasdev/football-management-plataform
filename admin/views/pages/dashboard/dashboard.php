<?php
    // Asegurarnos de que $stats está definido
    $stats = $stats ?? [
        'equipos' => 0,
        'torneos' => 0,
        'jugadores' => 0,
        'partidos' => 0
    ];
?>
<div class="grid charts-grid">

    <div class="card">
        <h6>Equipos por liga</h6>
        <canvas id="chartEquiposLiga"></canvas>
    </div>

    <div class="card">
        <h6>Afiliaciones por rol</h6>
        <canvas id="chartAfiliaciones"></canvas>
    </div>

    <div class="card full">
        <h6>Partidos disputados por mes</h6>
        <canvas id="chartPartidosMes"></canvas>
    </div>

</div>


<style>

    .charts-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin-top: 25px;
    }

    .charts-grid .card.full {
        grid-column: 1 / -1;
    }

    .card {
        background: #111;
        padding: 16px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0,0,0,.4);
    }

    .card h6 {
        color: gold;
        margin-bottom: 10px;
        font-weight: 600;
    }

</style>


<?php startSection('scripts') ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const ctxLiga = document.getElementById('chartEquiposLiga');

        new Chart(ctxLiga, {
            type: 'bar',
            data: {
                labels: ['Liga Colima', 'Liga Villa', 'Liga Tecomán'],
                datasets: [{
                    label: 'Equipos',
                    data: [12, 8, 6],
                    backgroundColor: 'gold'
                }]
            },
            options: {
                plugins: { legend: { display: false } },
                scales: {
                    x: { ticks: { color: '#ccc' } },
                    y: { ticks: { color: '#ccc' } }
                }
            }
        });

        const ctxAfiliados = document.getElementById('chartAfiliaciones');

        new Chart(ctxAfiliados, {
            type: 'doughnut',
            data: {
                labels: ['Jugadores', 'Técnicos', 'Árbitros'],
                datasets: [{
                    data: [320, 45, 18],
                    backgroundColor: ['#f5c542', '#4caf50', '#2196f3']
                }]
            },
            options: {
                plugins: {
                    legend: {
                        labels: { color: '#ccc' }
                    }
                }
            }
        });

        const ctxPartidos = document.getElementById('chartPartidosMes');

        new Chart(ctxPartidos, {
            type: 'line',
            data: {
                labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May'],
                datasets: [{
                    label: 'Partidos',
                    data: [22, 35, 40, 28, 50],
                    borderColor: 'gold',
                    backgroundColor: 'rgba(255,215,0,.2)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                plugins: {
                    legend: { labels: { color: '#ccc' } }
                },
                scales: {
                    x: { ticks: { color: '#ccc' } },
                    y: { ticks: { color: '#ccc' } }
                }
            }
        });

    </script>

<?php endSection('scripts') ?>
