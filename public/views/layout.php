<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sistema</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- Fuentes utilizadas por el encabezado público -->
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Orbitron:wght@400;600;700&family=Poppins:wght@400;500;600;700&family=Roboto&display=swap" rel="stylesheet">

    <!-- Estilos compartidos del sitio público -->
    <link rel="stylesheet" href="/afec/public/assets/css/public.css">
    <link rel="stylesheet" href="/afec/public/assets/css/public-header.css">
    <link rel="stylesheet" href="/afec/public/assets/css/public-footer.css">

    <!-- Estilos específicos de cada página -->
    <?php if ($view === 'pages/home.php'): ?>
        <link rel="stylesheet" href="/afec/public/assets/css/home.css">
    <?php elseif ($view === 'pages/liga.php'): ?>
        <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@400;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="/afec/public/assets/css/liga.css">
    <?php elseif ($view === 'pages/404.php'): ?>
        <link rel="stylesheet" href="/afec/public/assets/css/404.css">
    <?php endif; ?>

    <!-- Dependencia de estilos existente; se conserva por compatibilidad -->
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="/afec/public/assets/js/public-navigation.js" defer></script>
    <script src="/afec/public/assets/js/public-modals.js" defer></script>
</head>
<body class="public-site">

    <?php include 'partials/header.php'; ?>

    <main>
        <?php include $view; ?>
    </main>

    <?php include 'partials/footer.php'; ?>

</body>
</html>
