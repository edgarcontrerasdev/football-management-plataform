<?php 
    if (!isset($viewPath)) {
        die('Vista no definida');
    }
    $page = $_GET['page'] ?? 'dashboard';
    $isAdminManager = in_array(Auth::role(), ['ADMIN', 'SUPERVISOR'], true);
    $isActivePage = static fn(string $key): string => $page === $key ? ' active' : '';
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
                <i class="fas fa-bars header-menu-icon"></i>
            </button>

            <div class="logo" aria-hidden="true">
                <!-- usa tu logo local aquí -->
                <img src="../public/assets/img/logos/logo.png" alt="AFEC Colima">
            </div>

            <div class="center">
                <div class="header-brand-name">AFEC</div>
                <div class="header-brand-subtitle">Panel Administrativo</div>
            </div>
        </div>

        <div class="right">

            <div class="header-user-area">
        
            <div class="header-avatar-frame">
                <img class="header-avatar-image" src="/afec/public/assets/img/avatars/<?= htmlspecialchars($avatar) ?>" 
                    alt="Avatar" class="user-avatar" width="50"  height="50">
            </div>

            
            <div class="header-user-controls">

            <div class="header-user-details">
                <div class="header-user-name">
                    <?= htmlspecialchars(Auth::user()['usuario']); ?>
                </div>
                <div class="header-user-email">
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

            <a href="index.php?page=dashboard" class="nav-item<?= $isActivePage('dashboard') ?>" data-key="dashboard"><i class="bi bi-speedometer2"></i><span class="label">Panel</span></a>
            <a href="index.php?page=ligas" class="nav-item<?= $isActivePage('ligas') ?>" data-key="ligas"><i class="bi bi-trophy"></i><span class="label">Ligas</span></a>
            <a href="index.php?page=equipos" class="nav-item<?= $isActivePage('equipos') ?>" data-key="equipos"><i class="bi bi-people"></i><span class="label">Equipos</span></a>
            <?php if (in_array(Auth::role(), ['ADMIN', 'SUPERVISOR'], true)): ?>
            <a href="index.php?page=clubes" class="nav-item<?= $isActivePage('clubes') ?>" data-key="clubes"><i class="bi bi-shield"></i><span class="label">Clubes</span></a>
            <?php endif; ?>
            <?php if (in_array(Auth::role(), ['ADMIN', 'SUPERVISOR'], true)): ?>
            <a href="index.php?page=organismos" class="nav-item<?= $isActivePage('organismos') ?>" data-key="organismos"><i class="bi bi-diagram-3"></i><span class="label">Organismos</span></a>
            <?php endif; ?>
            <a href="index.php?page=torneos" class="nav-item<?= $isActivePage('torneos') ?>" data-key="torneos"><i class="bi bi-flag"></i><span class="label">Torneos</span></a>
            <a href="#" class="nav-item<?= $isActivePage('partidos') ?>" data-key="partidos"><i class="bi bi-calendar-week"></i><span class="label">Partidos</span></a>
            <a href="index.php?page=temporadas" class="nav-item<?= $isActivePage('temporadas') ?>" data-key="temporadas"><i class="fas fa-calendar"></i><span class="label">Temporadas</span></a>
    
            <div class="section-title">Administración</div>

                <a href="#" class="nav-item" data-key="amonestaciones"><i class="bi bi-card-list"></i><span class="label">Amonestaciones</span></a>
                <a href="#" class="nav-item" data-key="finanzas"><i class="bi bi-currency-dollar"></i><span class="label">Finanzas</span></a>
        
                <?php if ($isAdminManager): ?>
                    <a href="index.php?page=users" class="nav-item<?= $isActivePage('users') ?>" data-key="users">
                        <i class="bi bi-person-gear"></i><span class="label">Usuarios</span>
                    </a>
                <?php endif; ?>
    </aside>

    <!-- MAIN -->
    <main id="main" class="main" role="main">

        <div class="content-frame">
            <!-- Breadcrumbs / esta area indica el mapa de navegacion -->
            <div class="breadcrumbs" id="breadcrumbs">
                <div class="breadcrumbs-label">Usted está en:</div>
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

    <script src="<?= ASSETS ?>/js/admin/layout/admin-layout.js"></script>
    <script src="<?= ASSETS ?>/js/admin/components/image-upload.js"></script>
    <script src="<?= ASSETS ?>/js/admin/tables/table-manager.js"></script>



<?php section('scripts') ?>

</html>
