<?php

class Router {

    private array|null $user;
    private string|null $rol;
    private string|null $page;
    private string|null $action;

    // Configuración de permisos para cada página
    private $pagesByRole = [
        '1'      => ['dashboard','ligas','equipos','users','temporadas','torneos','ajax'],
        '2'      => ['dashboard','ligas','equipos','users','temporadas','ajax'],
        '3'      => ['liga'],
        '4'      => ['equipo'],
    ];

    public function __construct() {
        $this->user = Auth::user();
        $this->rol  = $this->user['rol'] ?? null;
        $this->page = $_GET['page'] ?? null;
        $this->action = $_GET['action'] ?? 'index';
    }

    public function route() {

        // Si no está logueado, redireccionar a login
        if (!Auth::check()) {
            header("Location: /afec/public/index.php?page=login");
            exit;
        }

        // Verificar estado real del usuario en cada request
        if ($this->user) {
            require_once ROOT_PATH.'/config/database.php';
            require_once ROOT_PATH.'/app/models/User.php';

            $userModel = new User($pdo);
            $freshUser = $userModel->findById($this->user['id']);

            if (!$freshUser || $freshUser['estado'] == 0) {
                // Usuario inactivo: destruir sesión y redirigir
                Auth::logout();
                header("Location: /afec/public/index.php?page=login&error=inactivo");
                exit;
            }

            // Actualizar solo los campos críticos en la sesión
            $_SESSION['user']['estado'] = $freshUser['estado'];
            $_SESSION['user']['usuario'] = $freshUser['usuario'];
            // Mantener rol, liga_id y equipo_id intactos
            $this->user = $_SESSION['user'];
        }

        // Si hace Logout
        if ($this->page === 'logout') {
            require_once ROOT_PATH.'/app/controllers/LoginController.php';
            (new LoginController())->logout();
            exit;
        }

        // Página por defecto
        if (!$this->page) {
            $this->page = $this->defaultPageByRole();
        }

        // Validar permisos
        if (!in_array($this->page, $this->pagesByRole[$this->rol] ?? [])) {
            $this->page = $this->defaultPageByRole();
        }

        /* =========================
           RESOLVER CONTROLADOR
        ========================= */
        $controllerName = ucfirst($this->page).'Controller';
        $controllerPath = ROOT_PATH.'/app/controllers/'.$controllerName.'.php';

        if (!file_exists($controllerPath)) {
            $this->errorPage("Controlador '$controllerName' no encontrado.");
            return;
        }

        require_once ROOT_PATH.'/config/database.php';
        require_once $controllerPath;

        $controller = new $controllerName($pdo);

        /* =========================
           VALIDAR ACCIÓN
        ========================= */
        if ($this->action && method_exists($controller, $this->action)) {
            $method = $this->action;
        } elseif (method_exists($controller, 'index')) {
            $method = 'index';
        } else {
            $this->errorPage("Método '{$this->action}' no encontrado en '$controllerName'.");
            return;
        }

        /* ==============================
           EJECUTAR ACCIÓN CON PARÁMETROS
        ================================= */
        $reflection = new ReflectionMethod($controller, $method);
        $params = [];

        foreach ($reflection->getParameters() as $param) {
            $name = $param->getName();
            if (isset($_GET[$name])) {
                $params[] = $_GET[$name];
            } elseif ($param->isDefaultValueAvailable()) {
                $params[] = $param->getDefaultValue();
            } else {
                $this->errorPage("Parámetro '$name' requerido para {$controllerName}::{$this->action}().");
                return;
            }
        }

        // Llamar a la función con los parámetros resueltos automáticamente
        call_user_func_array([$controller, $method], $params);
    }

    private function defaultPageByRole() {
        return match($this->rol) {
            'liga'   => 'liga',
            'equipo' => 'equipo',
            default  => 'dashboard'
        };
    }

    private function errorPage(string $mensaje) {
        http_response_code(404);
        $errorFile = ROOT_PATH.'/admin/views/404.php';
        if (file_exists($errorFile)) {
            include_once $errorFile;
        } else {
            echo '<h2>ERROR 404</h2><p>' . htmlspecialchars($mensaje) . '</p>';
        }
        exit;
    }
}

?>