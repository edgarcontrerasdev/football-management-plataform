<?php
require_once __DIR__.'../../core/Auth.php';

class DashboardController {

    public function index() {
        Auth::check(); // asegura que el usuario esté logueado

        // Aquí defines los datos que necesite la vista
        $stats = [
            'equipos' => 18,
            'torneos' => 4,
            'jugadores' => 523,
            'partidos' => 52
        ];

        // Indicar qué vista cargar
    
        $viewPath = ROOT_PATH.'/admin/views/pages/dashboard/dashboard.php';

        // Si la vista no existe, carga 404
        if (!file_exists($viewPath)) {
            $viewPath = ROOT_PATH.'/admin/views/404.php';
        }

        // Incluir layout principal que a su vez incluirá la vista
        include ROOT_PATH.'/admin/views/layout.php';
    }
}

?>