<?php
require_once __DIR__.'../../../config/database.php';
require_once __DIR__.'../../models/Liga.php';
require_once __DIR__.'../../models/Equipo.php';

class AjaxController {

    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Devuelve todas las ligas en JSON
    public function ligas() {
        $ligaModel = new Liga($this->pdo);
        $ligas = $ligaModel->all();
        echo json_encode($ligas);
        exit;
    }

    // Devuelve equipos según liga_id en JSON
    public function equipos() {
        $equipoModel = new Equipo($this->pdo);
        $liga_id = $_GET['liga_id'] ?? null;
        if (!$liga_id) {
            echo json_encode([]);
            exit;
        }

        $equipos = $equipoModel->getByLiga($liga_id); // Método que filtra por liga
        echo json_encode($equipos);
        exit;
    }

    public function index(){
        echo 'Controlador AJAX listo';
    }

    
   public function usersFilter() {
        require_once __DIR__ . '/../models/User.php';
        require_once __DIR__ . '/../../config/database.php';

        $filtros = [
            'filtro' => $_GET['filtro'] ?? '',
            'estado' => isset($_GET['estado']) && $_GET['estado'] !== '' ? (int)$_GET['estado'] : null,
            'rol'    => $_GET['rol'] ?? null,
            'liga'   => $_GET['liga'] ?? null,
            'pagina' => isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1,
            'limite' => isset($_GET['limite']) ? (int)$_GET['limite'] : 10,
        ];

        $userModel = new User($this->pdo);

        //usuariosPaginados y filtrados
        $usuarios = $userModel->filterUsers($filtros);
        //total de usuarios filtrados(sin limit)
        $total = $userModel->countUsers($filtros);

        header('Content-Type: application/json');
        echo json_encode([
            'usuarios' => $usuarios,
            'total' => $total
        ]);
        exit;
    }

}

?>