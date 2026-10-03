<?php

declare(strict_types=1);

class Router
{
    private ?array $user;
    private ?string $rol;
    private ?string $page;
    private string $action;
    private ?PDO $db = null;

    /**
     * Rutas administrativas disponibles.
     *
     * permission = null
     * significa que cualquier usuario autenticado puede entrar.
     */
    private array $routes = [

        'dashboard' => [
            'controller' => 'DashboardController',
            'permission' => null
        ],

        'temporadas' => [
            'controller' => 'TemporadasController',
            'permission' => 'temporadas.ver'
        ],

        'ligas' => [
            'controller' => 'LigasController',
            'permission' => 'ligas.ver'
        ],

        'equipos' => [
            'controller' => 'EquiposController',
            'permission' => 'equipos.ver'
        ],

        'users' => [
            'controller' => 'UsersController',
            'permission' => 'usuarios.ver'
        ],

        'torneos' => [
            'controller' => 'TorneosController',
            'permission' => 'torneos.ver'
        ],

        /*
         * Alias heredados.
         * Los conservamos temporalmente para no romper
         * enlaces existentes del sistema anterior.
         */
        'liga' => [
            'controller' => 'LigasController',
            'permission' => 'ligas.ver'
        ],

        'equipo' => [
            'controller' => 'EquiposController',
            'permission' => 'equipos.ver'
        ],

        /*
         * AJAX queda accesible a usuarios autenticados por ahora.
         * Sus acciones específicas deberán protegerse posteriormente.
         */
        'ajax' => [
            'controller' => 'AjaxController',
            'permission' => null
        ]
    ];

    public function __construct()
    {
        $this->user = Auth::user();
        $this->rol  = $this->user['rol_clave'] ?? null;

        $this->page = $_GET['page'] ?? null;

        $this->action = $_GET['action'] ?? 'index';
    }

    /**
     * Ejecuta el enrutamiento principal.
     */
    public function route(): void
    {
        /*
         * ---------------------------------------------------------
         * LOGOUT
         * ---------------------------------------------------------
         */
        if ($this->page === 'logout') {

            require_once ROOT_PATH . '/app/controllers/LoginController.php';

            (new LoginController())->logout();

            exit;
        }

        /*
         * ---------------------------------------------------------
         * AUTENTICACIÓN
         * ---------------------------------------------------------
         */
        Auth::requireLogin();

        /*
         * ---------------------------------------------------------
         * BASE DE DATOS
         * ---------------------------------------------------------
         */
        require_once ROOT_PATH . '/config/database.php';

        $this->db = $pdo;

        /*
         * ---------------------------------------------------------
         * ACTUALIZAR DATOS DEL USUARIO
         * ---------------------------------------------------------
         */

        require_once ROOT_PATH . '/app/models/User.php';

        $userModel = new User($this->db);

        $freshUser = $userModel->findById(
            Auth::id()
        );

        /*
         * Usuario eliminado o inexistente.
         */
        if (!$freshUser) {

            Auth::logout();

            header(
                'Location: /afec/public/index.php?page=login&error=inactivo'
            );

            exit;
        }

        /*
         * Usuario inactivo.
         */
        if (strtolower((string) $freshUser['estado']) !== 'activo') {

            Auth::logout();

            header(
                'Location: /afec/public/index.php?page=login&error=inactivo'
            );

            exit;
        }

        /*
         * Rol inexistente o inactivo.
         */
        if (
            empty($freshUser['rol_clave']) ||
            strtolower((string) $freshUser['rol_estado']) !== 'activo'
        ) {

            Auth::logout();

            header(
                'Location: /afec/public/index.php?page=login&error=rol'
            );

            exit;
        }

        /*
         * Actualizamos la sesión sin regenerar su ID.
         */
        Auth::refreshUser($freshUser);

        $this->user = Auth::user();

        $this->rol = Auth::role();

        /*
         * ---------------------------------------------------------
         * PÁGINA POR DEFECTO
         * ---------------------------------------------------------
         */

        if (!$this->page) {

            $this->page = $this->defaultPageByRole();
        }

        /*
         * ---------------------------------------------------------
         * VALIDAR QUE LA RUTA EXISTA
         * ---------------------------------------------------------
         */

        if (!isset($this->routes[$this->page])) {

            $this->errorPage(
                "Página '{$this->page}' no encontrada.",
                404
            );

            return;
        }

        $route = $this->routes[$this->page];

        /*
         * ---------------------------------------------------------
         * VALIDAR PERMISO
         * ---------------------------------------------------------
         */

        if (
            $route['permission'] !== null &&
            !$this->hasPermission($route['permission'])
        ) {

            $this->forbiddenPage();

            return;
        }

        /*
         * ---------------------------------------------------------
         * RESOLVER CONTROLADOR
         * ---------------------------------------------------------
         */

        $controllerName = $route['controller'];

        $controllerPath =
            ROOT_PATH .
            '/app/controllers/' .
            $controllerName .
            '.php';

        if (!file_exists($controllerPath)) {

            $this->errorPage(
                "Controlador '{$controllerName}' no encontrado.",
                404
            );

            return;
        }

        require_once $controllerPath;

        /*
         * ---------------------------------------------------------
         * INSTANCIAR CONTROLADOR
         * ---------------------------------------------------------
         */

        $controller = new $controllerName($this->db);

        /*
         * ---------------------------------------------------------
         * VALIDAR ACCIÓN
         * ---------------------------------------------------------
         */

        if (
            $this->action === '' ||
            !method_exists($controller, $this->action)
        ) {

            if (method_exists($controller, 'index')) {

                $method = 'index';

            } else {

                $this->errorPage(
                    "Método '{$this->action}' no encontrado en '{$controllerName}'.",
                    404
                );

                return;
            }

        } else {

            $method = $this->action;
        }

        /*
         * ---------------------------------------------------------
         * RESOLVER PARÁMETROS
         * ---------------------------------------------------------
         */

        $reflection = new ReflectionMethod(
            $controller,
            $method
        );

        if (!$reflection->isPublic()) {

            $this->errorPage(
                "El método '{$method}' no es accesible.",
                404
            );

            return;
        }

        $params = [];

        foreach ($reflection->getParameters() as $param) {

            $name = $param->getName();

            if (isset($_GET[$name])) {

                $params[] = $_GET[$name];

            } elseif ($param->isDefaultValueAvailable()) {

                $params[] = $param->getDefaultValue();

            } else {

                $this->errorPage(
                    "Parámetro '{$name}' requerido para {$controllerName}::{$method}().",
                    400
                );

                return;
            }
        }

        /*
         * ---------------------------------------------------------
         * EJECUTAR ACCIÓN
         * ---------------------------------------------------------
         */

        call_user_func_array(
            [$controller, $method],
            $params
        );
    }

    /**
     * Determina la página inicial según el rol.
     */
    private function defaultPageByRole(): string
    {
        return match ($this->rol) {

            'LIGA' => 'ligas',

            'EQUIPO' => 'equipos',

            'ADMIN',
            'SUPERVISOR' => 'dashboard',

            default => 'dashboard'
        };
    }

    /**
     * Comprueba un permiso asignado al rol.
     */
    private function hasPermission(string $permission): bool
    {
        if (!$this->db || !$this->user) {
            return false;
        }

        $sql = "
            SELECT 1
            FROM tb_rol_permisos rp
            INNER JOIN tb_permisos p
                ON p.id = rp.permiso_id
            WHERE rp.rol_id = :rol_id
              AND p.clave = :permiso
              AND p.estado = 'activo'
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':rol_id'  => (int) $this->user['rol_id'],
            ':permiso' => $permission
        ]);

        return (bool) $stmt->fetchColumn();
    }

    /**
     * Página 403.
     */
    private function forbiddenPage(): void
    {
        http_response_code(403);

        $errorFile = ROOT_PATH . '/admin/views/404.php';

        if (file_exists($errorFile)) {

            include_once $errorFile;

        } else {

            echo '<h2>ERROR 403</h2>';
            echo '<p>No tienes permisos para acceder a este recurso.</p>';
        }

        exit;
    }

    /**
     * Página de error.
     */
    private function errorPage(
        string $mensaje,
        int $status = 404
    ): void {

        http_response_code($status);

        $errorFile = ROOT_PATH . '/admin/views/404.php';

        if (file_exists($errorFile)) {

            include_once $errorFile;

        } else {

            echo '<h2>ERROR ' . $status . '</h2>';
            echo '<p>' .
                htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') .
                '</p>';
        }

        exit;
    }
}