<?php

require_once __DIR__ . '/../models/Liga.php';
require_once __DIR__ . '/../models/Equipo.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../core/Auth.php';

class AjaxController
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function ligas(): void
    {
        $ligaModel = new Liga($this->pdo);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($ligaModel->all());
        exit;
    }

    public function equipos(): void
    {
        $ligaId = (int) ($_GET['liga_id'] ?? 0);
        $equipos = $ligaId > 0 ? (new Equipo($this->pdo))->getByLiga($ligaId) : [];
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($equipos);
        exit;
    }

    public function index(): void
    {
        echo 'Controlador AJAX listo';
    }

    public function usersFilter(): void
    {
        Auth::requireRole(['ADMIN', 'SUPERVISOR']);

        header('Content-Type: application/json; charset=utf-8');

        try {
            $filtros = [
                'filtro' => trim((string) ($_GET['filtro'] ?? '')),
                'estado' => (string) ($_GET['estado'] ?? 'all'),
                'rol' => $_GET['rol'] ?? null,
                'liga' => $_GET['liga'] ?? null,
                'pagina' => max(1, (int) ($_GET['pagina'] ?? 1)),
                'limite' => min(100, max(1, (int) ($_GET['limite'] ?? 10))),
            ];

            $resultado = (new User($this->pdo))->filterUsers($filtros);

            echo json_encode([
                'usuarios' => $resultado,
                'total' => (int) $resultado['total'],
            ], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
        } catch (Throwable $e) {
            error_log('Error al filtrar usuarios: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'mensaje' => 'No fue posible cargar los usuarios.',
            ], JSON_UNESCAPED_UNICODE);
        }

        exit;
    }
}
