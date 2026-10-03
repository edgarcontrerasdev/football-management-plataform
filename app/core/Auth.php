<?php

declare(strict_types=1);

// app/core/Auth.php

class Auth
{
    /**
     * Inicia la sesión si todavía no existe.
     */
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {

            session_set_cookie_params([
                'lifetime' => 0,
                'path'     => '/',
                'secure'   => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
                'httponly' => true,
                'samesite' => 'Lax'
            ]);

            session_start();
        }
    }

    /**
     * Inicia sesión con los datos del usuario autenticado.
     */
    public static function login(array $user): void
    {
        self::start();

        // Evita reutilización del ID de sesión anterior.
        session_regenerate_id(true);

        $_SESSION['user'] = [
            'id'          => (int) $user['id'],
            'afiliado_id' => isset($user['afiliado_id'])
                ? (int) $user['afiliado_id']
                : null,

            'usuario'     => $user['usuario'],

            'rol_id'      => (int) $user['rol_id'],
            'rol_clave'   => $user['rol_clave'],
            'rol_nombre'  => $user['rol_nombre'],

            'estado'      => $user['estado'],

            'avatar'      => $user['avatar'] ?? null
        ];
    }

    /**
     * Cierra la sesión del usuario.
     */
    public static function logout(): void
    {
        self::start();

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {

            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
    }

    /**
     * Determina si existe una sesión autenticada.
     */
    public static function check(): bool
    {
        self::start();

        return isset($_SESSION['user']);
    }

    /**
     * Devuelve los datos del usuario autenticado.
     */
    public static function user(): ?array
    {
        self::start();

        return $_SESSION['user'] ?? null;
    }

    /**
     * Devuelve el ID del usuario autenticado.
     */
    public static function id(): ?int
    {
        $user = self::user();

        return $user !== null
            ? (int) $user['id']
            : null;
    }

    /**
     * Devuelve la clave del rol actual.
     */
    public static function role(): ?string
    {
        $user = self::user();

        return $user['rol_clave'] ?? null;
    }

    /**
     * Exige una sesión autenticada.
     */
    public static function requireLogin(): void
    {
        if (!self::check()) {
            header('Location: /afec/public/index.php?page=login');
            exit;
        }
    }

    /**
     * Exige uno de los roles especificados.
     *
     * Ejemplo:
     * Auth::requireRole(['ADMIN', 'SUPERVISOR']);
     */
    public static function requireRole(array $roles): void
    {
        self::requireLogin();

        $role = self::role();

        if ($role === null || !in_array($role, $roles, true)) {
            header('Location: /afec/public/index.php?page=login&error=denied');
            exit;
        }
    }
}