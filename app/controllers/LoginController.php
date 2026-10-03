<?php

declare(strict_types=1);

require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../models/User.php';

class LoginController
{
    /**
     * Procesa el inicio de sesión.
     */
    public function login(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        require_once __DIR__ . '/../../config/database.php';

        Auth::start();

        /*
         * ---------------------------------------------------------
         * 1. Leer información enviada
         * ---------------------------------------------------------
         *
         * El formulario actual trabaja con JSON.
         * Dejamos también $_POST como respaldo para facilitar futuras
         * integraciones o pruebas.
         */

        $rawInput = file_get_contents('php://input');
        $input    = json_decode($rawInput, true);

        if (!is_array($input)) {
            $input = $_POST;
        }

        $inputUsuario  = trim((string) ($input['usuario'] ?? ''));
        $inputPassword = (string) ($input['password'] ?? '');

        /*
         * ---------------------------------------------------------
         * 2. Validación de campos
         * ---------------------------------------------------------
         */

        if ($inputUsuario === '' && $inputPassword === '') {

            echo json_encode([
                'success' => false,
                'mensaje' => 'Los campos usuario y contraseña no pueden estar vacíos.'
            ]);

            exit;
        }

        if ($inputUsuario === '') {

            echo json_encode([
                'success' => false,
                'mensaje' => 'El campo usuario no puede estar vacío.'
            ]);

            exit;
        }

        if ($inputPassword === '') {

            echo json_encode([
                'success' => false,
                'mensaje' => 'El campo contraseña no puede estar vacío.'
            ]);

            exit;
        }

        /*
         * ---------------------------------------------------------
         * 3. Buscar usuario
         * ---------------------------------------------------------
         */

        $userModel = new User($pdo);

        $user = $userModel->usuarioExiste($inputUsuario);

        if (!$user) {

            echo json_encode([
                'success' => false,
                'mensaje' => 'Las credenciales proporcionadas no son válidas.'
            ]);

            exit;
        }

        /*
         * ---------------------------------------------------------
         * 4. Verificar contraseña
         * ---------------------------------------------------------
         */

        if (!password_verify($inputPassword, $user['password'])) {

            echo json_encode([
                'success' => false,
                'mensaje' => 'Las credenciales proporcionadas no son válidas.'
            ]);

            exit;
        }

        /*
         * ---------------------------------------------------------
         * 5. Verificar estado del usuario
         * ---------------------------------------------------------
         *
         * En V2 estado es texto:
         * activo
         */

        if (strtolower((string) $user['estado']) !== 'activo') {

            echo json_encode([
                'success' => false,
                'mensaje' => 'El usuario se encuentra inhabilitado. Contactar al administrador.'
            ]);

            exit;
        }

        /*
         * ---------------------------------------------------------
         * 6. Verificar estado del rol
         * ---------------------------------------------------------
         */

        if (
            isset($user['rol_estado']) &&
            strtolower((string) $user['rol_estado']) !== 'activo'
        ) {

            echo json_encode([
                'success' => false,
                'mensaje' => 'El rol asignado al usuario no se encuentra activo.'
            ]);

            exit;
        }

        /*
         * ---------------------------------------------------------
         * 7. Verificar que exista un rol válido
         * ---------------------------------------------------------
         */

        if (
            empty($user['rol_clave']) ||
            empty($user['rol_nombre'])
        ) {

            echo json_encode([
                'success' => false,
                'mensaje' => 'El usuario no tiene un rol de acceso válido.'
            ]);

            exit;
        }

        /*
         * ---------------------------------------------------------
         * 8. Login exitoso
         * ---------------------------------------------------------
         */

        Auth::login($user);

        echo json_encode([
            'success'  => true,
            'mensaje'  => 'Inicio de sesión exitoso.',
            'redirect' => '/afec/admin/index.php'
        ]);

        exit;
    }

    /**
     * Cierra la sesión.
     */
    public function logout(): void
    {
        Auth::start();

        Auth::logout();

        header('Location: /afec/public/index.php?page=login');
        exit;
    }
}