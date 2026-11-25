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

    private function currentOrganismoId(): ?int
    {
        if (Auth::role() === 'ADMIN') {
            return null;
        }
        $stmt = $this->pdo->prepare("
            SELECT entidad_id
            FROM tb_usuario_alcances
            WHERE usuario_id = :usuario_id
              AND tipo_entidad = 'organismo'
              AND estado = 'activo'
              AND (fecha_inicio IS NULL OR fecha_inicio <= CURDATE())
              AND (fecha_fin IS NULL OR fecha_fin >= CURDATE())
            ORDER BY id DESC LIMIT 1
        ");
        $stmt->execute([':usuario_id' => Auth::id()]);
        $id = $stmt->fetchColumn();
        return $id ? (int) $id : 0;
    }

    public function ligas(): void
    {
        Auth::requireRole(['ADMIN', 'ORGANISMO']);
        header('Content-Type: application/json; charset=utf-8');
        $organismoId = $this->currentOrganismoId();
        if (Auth::role() === 'ORGANISMO' && !$organismoId) {
            http_response_code(403);
            echo json_encode(['success' => false, 'mensaje' => 'La cuenta no tiene un organismo asignado.']);
            exit;
        }

        $requestedOrganismoId = $organismoId !== null
            ? $organismoId
            : (array_key_exists('organismo_id', $_GET) ? (int) $_GET['organismo_id'] : null);
        if ($organismoId !== null && $requestedOrganismoId !== $organismoId) {
            http_response_code(403);
            echo json_encode(['success' => false, 'mensaje' => 'Organismo no autorizado.']);
            exit;
        }

        $sql = "
            SELECT DISTINCT l.id, l.nombre, lo.organismo_id
            FROM tb_ligas l
            LEFT JOIN tb_liga_organismos lo
                ON lo.liga_id = l.id AND lo.estado = 'activo' AND lo.es_gestor = 1
            WHERE l.estado = 'activo'
        ";
        $params = [];
        if ($requestedOrganismoId !== null && $requestedOrganismoId > 0) {
            $sql .= " AND lo.organismo_id = :organismo_id";
            $params[':organismo_id'] = $requestedOrganismoId;
        } elseif ($requestedOrganismoId === 0) {
            $sql .= " AND lo.id IS NULL
                      AND NOT EXISTS (
                          SELECT 1 FROM tb_liga_organismos lo_any
                          WHERE lo_any.liga_id = l.id AND lo_any.estado = 'activo'
                      )";
        }
        $sql .= " ORDER BY l.nombre";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC), JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function equipos(): void
    {
        Auth::requireRole(['ADMIN', 'ORGANISMO']);
        header('Content-Type: application/json; charset=utf-8');
        $ligaId = (int) ($_GET['liga_id'] ?? 0);
        $organismoId = $this->currentOrganismoId();
        if (Auth::role() === 'ORGANISMO' && !$organismoId) {
            http_response_code(403);
            echo json_encode(['success' => false, 'mensaje' => 'La cuenta no tiene un organismo asignado.']);
            exit;
        }
        $requestedOrganismoId = $organismoId !== null
            ? $organismoId
            : (array_key_exists('organismo_id', $_GET) ? (int) $_GET['organismo_id'] : null);

        if ($ligaId <= 0) {
            echo json_encode([]);
            exit;
        }

        $sql = "
            SELECT DISTINCT e.id, e.nombre
            FROM tb_equipos e
            INNER JOIN tb_liga_equipos le ON le.equipo_id = e.id
            INNER JOIN tb_liga_temporadas lt ON lt.id = le.liga_temporada_id
            INNER JOIN tb_ligas l ON l.id = lt.liga_id
            WHERE lt.liga_id = :liga_id
              AND lt.estado = 'activo'
              AND le.estado_participacion = 'activo'
              AND l.estado = 'activo'
        ";
        $params = [':liga_id' => $ligaId];
        if ($organismoId !== null && $requestedOrganismoId !== $organismoId) {
            http_response_code(403);
            echo json_encode(['success' => false, 'mensaje' => 'Organismo no autorizado.']);
            exit;
        }
        if ($requestedOrganismoId !== null && $requestedOrganismoId > 0) {
            $sql .= " AND EXISTS (
                SELECT 1 FROM tb_liga_organismos lo
                WHERE lo.liga_id = l.id
                  AND lo.organismo_id = :organismo_id
                  AND lo.estado = 'activo'
                  AND lo.es_gestor = 1
            )";
            $params[':organismo_id'] = $requestedOrganismoId;
        } elseif ($requestedOrganismoId === 0) {
            $sql .= " AND NOT EXISTS (
                SELECT 1 FROM tb_liga_organismos lo
                WHERE lo.liga_id = l.id AND lo.estado = 'activo'
            )";
        }
        $sql .= " ORDER BY e.nombre";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC), JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function index(): void
    {
        echo 'Controlador AJAX listo';
    }

    public function usersFilter(): void
    {
        Auth::requireRole(['ADMIN', 'ORGANISMO']);

        header('Content-Type: application/json; charset=utf-8');

        try {
            $filtros = [
                'filtro' => trim((string) ($_GET['filtro'] ?? '')),
                'estado' => (string) ($_GET['estado'] ?? 'all'),
                'rol' => $_GET['rol'] ?? null,
                'liga' => $_GET['liga'] ?? null,
                'pagina' => max(1, (int) ($_GET['pagina'] ?? 1)),
                'limite' => min(100, max(1, (int) ($_GET['limite'] ?? 10))),
                'organismo' => $this->currentOrganismoId(),
                'roles_permitidos' => Auth::role() === 'ADMIN' ? null : ['LIGA', 'EQUIPO'],
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
