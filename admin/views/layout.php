<?php 
    if (!isset($viewPath)) {
        die('Vista no definida');
    }
    $page = $_GET['page'] ?? 'dashboard';
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Panel Administrativo</title>

  <!-- Bootstrap icons (solo para íconos de ejemplo) -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<!-- archivo principal que importa el css -->
<link rel="stylesheet" href="<?= ASSETS ?>/css/app.css">

<style>

    /* Reset / base */
    *{box-sizing:border-box;}

    html,body{
        height:100%; 
        margin:0; 
        font-family: Inter, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial; 
        background: var(--bg); color:#eaf5ff;
    }

    /* ================= HEADER FULL WIDTH ================= */
    .header {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 68px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 20px;
        z-index: 40;
        background: linear-gradient(180deg, rgba(217,226,232,0.95), rgba(217,226,232,0.85));
        box-shadow: 0 6px 30px rgba(2,8,20,0.6);
        border-bottom: 3px solid var(--royal);
        color: var(--royal);
    }

    /* header left - logo + title */
    .header .left {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .header .logo {
        width: 56px; height: 56px;
        border-radius: 8px;
        background: linear-gradient(180deg, rgba(255,255,255,0.6), rgba(255,255,255,0.2));
        display:flex; align-items:center; justify-content:center;
        overflow: hidden;
        border: 1px solid rgba(11,62,166,0.08);
        box-shadow: 0 6px 18px rgba(11,62,166,0.08);
    }

    .header .logo img { width: 90%; height: auto; display:block; }

    /* header center - page title (optional) */
    .header .center {
        display:flex;
        align-items:center;
        gap:12px;
        color: var(--royal);
        font-weight:700;
    }

    /* header right - controls */
    .header .right {
        display:flex;
        align-items:center;
        gap:14px;
        color: var(--royale);
    }

    .icon-btn {
        background: transparent;
        border: none;
        font-size: 18px;
        padding: 8px;
        border-radius: 8px;
        cursor: pointer;
        color: inherit;
    }

    .icon-btn:hover { background: rgba(11,62,166,0.08); }

    /* ================= SIDEBAR ================= */
    .sidebar {
        position: fixed;
        top: 68px; /* debajo del header */
        left: 0;
        width: 260px;
        height: calc(100vh - 68px);
        padding: 20px 10px;
        background: linear-gradient(180deg, rgba(3,12,30,0.88), rgba(8,18,34,0.92));
        border-right: 1px solid rgba(255,255,255,0.03);
        transition: transform .28s ease, width .28s ease;
        z-index: 30;
        overflow: auto;
    }

    /* collapsed state */
    .sidebar.collapsed {
        transform: translateX(0px);
        width: 60px;
    }

    /* sidebar items */
    .nav-item {
        display:flex;
        align-items:center;
        gap:12px;
        padding:10px 14px;
        margin:6px 8px;
        border-radius:10px;
        color: var(--muted);
        text-decoration:none;
        transition: all .28s ease;
        position: relative;
    }

    .nav-item i { font-size:20px; width:20px; text-align:center; color:var(--accent); }

    /* HOVER (diferente de active) */
    .nav-item:hover {
        color: #ffffff;
        background: rgba(94,168,255,0.08);
        transform: translateX(4px);
    }

    /* ACTIVE item: barra a la izquierda + fondo ligeramente más oscuro */
    .nav-item.active {
        color: white;
        background: linear-gradient(90deg, rgba(94,168,255,0.08), rgba(11,62,166,0.03));
        box-shadow: 0 6px 18px rgba(11,62,166,0.06) inset;
    }

    .nav-item.active::before{
        content:"";
        position:absolute;
        left:0; top:8px; bottom:8px;
        width:4px;
        background: linear-gradient(180deg,var(--royal), #0041a8);
        border-radius: 4px;
    }

    /* if collapsed show only icons */
    .sidebar.collapsed .nav-item span.label { display:none; }
    .sidebar.collapsed .nav-item { justify-content:center; padding:10px 6px; width: 30px; }

    /* small helper for section title */
    .sidebar .section-title {
        font-size:12px; color: rgba(255,255,255,0.55); margin:14px 12px 6px;
        padding-left:6px; text-transform:uppercase; letter-spacing:1px;
    }

    /* ================= MAIN CONTENT AREA ================= */
    .main {
        margin-left: 260px;
        padding: 88px 28px 28px 28px; /* leave space for header */
        transition: margin-left .28s ease;
    }

    .main.collapsed { margin-left: 80px; }

    /* "box" that visually separates content from frame */
    .content-frame {
        background: linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01));
        border-radius: 14px;
        padding: 22px;
        box-shadow: 0 10px 40px rgba(2,8,20,0.6);
        border: 1px solid rgba(255,255,255,0.03);
    }

    /* Breadcrumb / path area (distinguida) */
    .breadcrumbs {
    display:flex; align-items:center; gap:12px;
    padding:10px 0 18px 0; color:var(--muted);
    border-bottom: 1px dashed rgba(255,255,255,0.03);
    margin-bottom:14px;
    }

    .breadcrumbs .crumb { font-weight:600; color:#eaf5ff; }

    /* Example content grid */
    .grid {
        display:grid;
        grid-template-columns: repeat(auto-fit,minmax(220px,1fr));
        gap:18px;
    }

   
    /* responsive */
    @media (max-width: 900px) {
    .sidebar { transform: translateX(-260px); width: 260px; } /* collapsed by default on small */
    .sidebar.show { transform: translateX(0); }
    .main { margin-left: 0; padding: 88px 16px 16px 16px; }
    }

    /* small niceties */
    a { color:inherit; }

    .dropdown-menu {
        background: #f0f4f7;
        border-radius: 10px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        overflow: hidden;
    }

  .dropdown-item:hover {
      background: rgba(11,62,166,0.15);
  }

    /*estilo para los mensajes guias */
    .text-helper {
            color: #657285; /* azul claro tipo sky */
            font-size: 0.9rem;
            background-color: #b2c5df;
            padding:5px;
            border-radius: 5px;
        }

        .modal label {
            color: #031125; /* Azul bootstrap “primary” */
            font-weight: 500; /* un poco más grueso para destacar */
        }

        .modal-title{
            color:var(--royal);
            font-weight: 500;
        }

        .spinner {
        border: 4px solid #f3f3f3;
        border-top: 4px solid #3498db;
        border-radius: 50%;
        width: 36px;
        height: 36px;
        animation: girar 1s linear infinite;
    }

    /* 🔥 TRANSICIÓN TABLA */
    .table-fade-enter {
        opacity: 0;
        transform: translateY(10px);
    }

    .table-fade-enter-active {
        opacity: 1;
        transform: translateY(0);
        transition: all .35s ease;
    }

    .table-fade-exit {
        opacity: 1;
        transform: translateY(0);
    }

    .table-fade-exit-active {
        opacity: 0;
        transform: translateY(-5px);
        transition: all .2s ease;
    }
 
    .badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 110px;
        height: 28px;
        font-size: 12px;
        border-radius: 20px;
    }

    .badge i{
        font-size:11px;
    }

</style>

</head>

<body>

    <?php 
        $usuario = Auth::user(); 
        $avatar = $usuario['avatar'];
    ?>

    <!-- HEADER -->
    <header class="header" role="banner">

        <div class="left">
            <button id="btnToggle" class="icon-btn" title="Ocultar menú" aria-label="Ocultar menú">
                <i class="fas fa-bars" style="font-size:25px;"></i>
            </button>

            <div class="logo" aria-hidden="true">
                <!-- usa tu logo local aquí -->
                <img src="../public/assets/img/logos/logo.png" alt="AFEC Colima">
            </div>

            <div class="center">
                <div style="font-weight:800; color:var(--royal)">AFEC</div>
                <div style="font-size:12px; color:#0f2a46; margin-left:8px;">Panel Administrativo</div>
            </div>
        </div>

        <div class="right">

            <div style="display:flex; align-items:center; gap:12px;">
        
            <div style="width:44px;height:44px;border-radius:50%;background:linear-gradient(180deg,#fff,#f0f4f7); display:flex;align-items:center;justify-content:center; box-shadow:0 8px 20px rgba(11,62,166,0.06);">
                <img style="border-radius:50%" src="/afec/public/assets/img/avatars/<?= htmlspecialchars($avatar) ?>" 
                    alt="Avatar" class="user-avatar" width="50"  height="50">
            </div>

            
            <div style="display:flex; align-items:center; gap:10px; padding-left:12px;">

            <div style="text-align:right; font-size:13px;">
                <div style="font-weight:700; color:#07203a">
                    <?= htmlspecialchars(Auth::user()['usuario']); ?>
                </div>
                <div style="font-size:12px; color:#3b4f6b;">
                    <?php echo htmlspecialchars(Auth::user()['usuario']).'@afec.mx'; ?>
                </div>
            </div>

            <button class="icon-btn" id="btnFull" title="Pantalla completa"><i class="bi bi-arrows-fullscreen"></i></button>
            <button class="icon-btn" id="btnNotif" title="Notificaciones"><i class="bi bi-bell"></i></button>

            <div class="dropdown">
                <button class="icon-btn dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="bi bi-box-arrow-right"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a href="/afec/admin/index.php?page=logout" id="btnLogout" class="dropdown-item fas fa-sign-out-alt" href="#">Cerrar sesión</a></li>
                </ul>
            </div>

            </div>
        
            </div>

        </div>

    </header>

    <!-- SIDEBAR -->
    <aside id="sidebar" class="sidebar" role="navigation" aria-label="Menú principal">

        <div class="section-title">Navegación</div>

            <a href="index.php?page=dashboard" class="nav-item active" data-key="dashboard"><i class="bi bi-speedometer2"></i><span class="label">Panel</span></a>
            <a href="index.php?page=ligas" class="nav-item" data-key="ligas"><i class="bi bi-trophy"></i><span class="label">Ligas</span></a>
            <a href="index.php?page=equipos" class="nav-item" data-key="equipos"><i class="bi bi-people"></i><span class="label">Equipos</span></a>
            <a href="index.php?page=torneos" class="nav-item" data-key="torneos"><i class="bi bi-flag"></i><span class="label">Torneos</span></a>
            <a href="#" class="nav-item" data-key="partidos"><i class="bi bi-calendar-week"></i><span class="label">Partidos</span></a>
            <a href="index.php?page=temporadas" class="nav-item" data-key="temporadas"><i class="fas fa-calendar"></i><span class="label">Temporadas</span></a>
    
            <div class="section-title">Administración</div>

                <a href="#" class="nav-item" data-key="amonestaciones"><i class="bi bi-card-list"></i><span class="label">Amonestaciones</span></a>
                <a href="#" class="nav-item" data-key="finanzas"><i class="bi bi-currency-dollar"></i><span class="label">Finanzas</span></a>
        
                <?php if (in_array(Auth::user()['rol'], ['1','2'])): ?>
                    <a href="index.php?page=users" class="nav-item" data-key="usuarios">
                    <i class="bi bi-person-gear"></i><span class="label">Usuarios</span></a>
                <?php endif; ?>
            </div>
        </div>
    </aside>

    <!-- MAIN -->
    <main id="main" class="main" role="main">

        <div class="content-frame">
            <!-- Breadcrumbs / esta area indica el mapa de navegacion -->
            <div class="breadcrumbs" id="breadcrumbs">
                <div style="font-size:13px; color:var(--muted)">Usted está en:</div>
                <div class="crumb" id="crumb-current"> </div>
            </div>

            <!-- Area de contenido principal -->
            <section id="view-area">      
                <?php include $viewPath; ?>
            </section>

        </div>
    </main>

    <?php flash() ?>

</body>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        (function () {
            // Forzar recarga real si se navega con atrás/adelante
            window.addEventListener('pageshow', function (event) {
                if (event.persisted) {
                    window.location.reload();
                }
            });
        })();
    </script>

    <!-- SCRIPT: toggles, navigation (simulación), breadcrumbs -->
    <script>

        (function(){

            const sidebar = document.getElementById('sidebar');
            const main = document.getElementById('main');
            const btnToggle = document.getElementById('btnToggle');
            const navItems = document.querySelectorAll('.nav-item');
            const crumbs = document.getElementById('crumb-current');
            const btnFull = document.getElementById('btnFull');

            let collapsed = false;

            /* ------------------------------------------------------------------
            TOGGLE SIDEBAR
            ------------------------------------------------------------------ */
            btnToggle.addEventListener('click', () => {
                collapsed = !collapsed;

                if (collapsed) {
                    sidebar.classList.add('collapsed');
                    main.classList.add('collapsed');
                    sidebar.setAttribute('aria-hidden', 'true');
                } else {
                    sidebar.classList.remove('collapsed');
                    main.classList.remove('collapsed');
                    sidebar.setAttribute('aria-hidden', 'false');
                }
            });

    

            /* ------------------------------------------------------------------
            BOTÓN DE PANTALLA COMPLETA
            ------------------------------------------------------------------ */
            btnFull.addEventListener('click', () => {
                if (!document.fullscreenElement) {
                    document.documentElement.requestFullscreen().catch(()=>{});
                } else {
                    document.exitFullscreen().catch(()=>{});
                }
            });

            /* ------------------------------------------------------------------
                CERRAR SIDEBAR SI HACEN CLIC AFUERA (MÓVILES)
            ------------------------------------------------------------------ */
            document.addEventListener('click', (e) => {
                if (window.innerWidth < 900 && !sidebar.classList.contains('collapsed')) {
                    const dentro = e.composedPath().includes(sidebar) || e.composedPath().includes(btnToggle);
                    if (!dentro) {
                        sidebar.classList.add('collapsed');
                        main.classList.add('collapsed');
                    }
                }
            });

        })();
    </script>

    <script>
   
        function renderWithAnimation(wrapper, html){

            // 🔻 salida (fade out)
            wrapper.classList.add('table-fade-exit-active');

            setTimeout(() => {

                // 🔁 reemplazar contenido
                wrapper.innerHTML = html;

                // 🔺 entrada (fade in)
                wrapper.classList.remove('table-fade-exit-active');
                wrapper.classList.add('table-fade-enter');

                setTimeout(() => {
                    wrapper.classList.add('table-fade-enter-active');

                    setTimeout(() => {
                        wrapper.classList.remove('table-fade-enter');
                        wrapper.classList.remove('table-fade-enter-active');
                    }, 350);

                }, 10);

            }, 150);
        }

        document.addEventListener('click', function(e){

            const link = e.target.closest('#table-wrapper .pagination a, #table-wrapper #paginacion a');

            if(!link) return;

            e.preventDefault();

            const wrapper = link.closest('#table-wrapper');
            if(!wrapper) return;

            //mostrar loader
            wrapper.innerHTML = `
                <div style="padding:40px;text-align:center;">
                    <div class="spinner"></div>
                    <p>Cargando...</p>
                </div>
            `;

            requestAnimationFrame(() =>{
                fetch(link.getAttribute('href') + '&ajax=1')
                .then(res => res.text())
                .then(html =>{
                        setTimeout(()=>{
                            wrapper.innerHTML = html;
                        },250);
                });
            });

        });

        document.addEventListener('DOMContentLoaded', () => 
        {
            const form = document.querySelector('.wrap-toolbar form');
            const wrapper = document.getElementById('table-wrapper');

            if(!form || !wrapper) return;

            const searchInput = form.querySelector('input[name="buscar"]');

            let timeout = null;

            if(searchInput){
                searchInput.addEventListener('input', () => {

                    clearTimeout(timeout);

                    timeout = setTimeout(() => {

                    const params = new URLSearchParams(new FormData(form));
                    loadData('?' + params.toString());

                }, 400); // 🔥 delay tipo pro

             });
        }

            // 🔥 Loader
            function showLoader(){
                wrapper.innerHTML = `
                    <div style="padding:40px;text-align:center; color:#fff">
                        <div class="spinner"></div>
                        <p style="text-color:#fff">Cargando...</p>
                    </div>
                `;
            }

            // 🔥 Fetch data
            async function loadData(url){
                showLoader();

                await new Promise(resolve => setTimeout(resolve, 150));

                try{
                    const res = await fetch(url + '&ajax=1');
                    const html = await res.text();
                    setTimeout(()=>{
                        renderWithAnimation(wrapper, html);
                    }, 300);
                }catch(e){
                    wrapper.innerHTML = '<p>Error al cargar datos</p>';
                }
            }

            // 🔥 Submit filtros
            form.addEventListener('submit', (e)=>{
                e.preventDefault();
                const params = new URLSearchParams(new FormData(form));
                                console.log(params);

                loadData('?' + params.toString());
            });

        });
    </script>

    <script>

        function showTableSkeleton(){

            document.querySelector(".table-data").style.display="none";

            document.querySelector(".table-skeleton").style.display="table-row-group";

        }

        document.querySelector("#formFiltro").addEventListener("submit",function(){

            showTableSkeleton();

        });

        document.getElementById("toolbarForm").addEventListener("submit",function(){

            document.querySelector(".wrap-toolbar").classList.add("toolbar-loading");

        });

    </script>

<?php section('scripts') ?>

</html>
