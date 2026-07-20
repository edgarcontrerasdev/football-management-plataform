<?php

// obtener ruta limpia
$url = $_GET['page'] ?? ''; // si no viene, queda vacío y será home
$action = $_GET['action'] ?? null;
switch ($url) {
    case '':
        $view = 'pages/home.php';
        break;
    case 'home':
        $view = 'pages/home.php';
        break;
    case 'index':
        $view = 'pages/home.php';
        break;
    case 'login':
        $view = 'views/pages/login.php';
        $noLayout = true;
        break;
    case 'logout':
        require_once '../app/controllers/LoginController.php';
        $login = new LoginController();
        $login->logout();
        exit;
    case 'liga':
        $view = 'pages/liga.php';
        break;
    case 'auth':
        require_once '../app/controllers/LoginController.php';
 
        $login = new LoginController();
        
        if($action === 'login'){
            $login->login();
        }
        break;
    default:
        http_response_code(404);
        $view = 'pages/404.php';
        break;
}

// Cargar layout
if(isset($noLayout) && $noLayout === true){
    include __DIR__.'/'.$view;
    exit;  
}
include __DIR__ . '/views/layout.php';

?>