<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Rol.php';
require_once __DIR__ . '/../models/Liga.php';
require_once __DIR__ . '/../core/Auth.php';

class UsersController
{
    private PDO $db;
    private User $userModel;

    public function __construct(PDO $db)
    {
        $this->db = $db;
        $this->userModel = new User($db);
    }

    private function requireManager(): void
    {
        Auth::requireRole(['ADMIN', 'SUPERVISOR']);
    }

    private function catalogos(): array
    {
        $roles = (new Role($this->db))->all();
        $ligas = $this->db->query("
            SELECT id, nombre
            FROM tb_ligas
            WHERE estado = 'activo'
            ORDER BY nombre
        ")->fetchAll(PDO::FETCH_ASSOC);
        $equipos = $this->db->query("
            SELECT DISTINCT e.id, e.nombre, lt.liga_id
            FROM tb_equipos e
            INNER JOIN tb_liga_equipos le ON le.equipo_id = e.id
            INNER JOIN tb_liga_temporadas lt ON lt.id = le.liga_temporada_id
            INNER JOIN tb_ligas l ON l.id = lt.liga_id
            WHERE le.estado_participacion = 'activo'
              AND lt.estado = 'activo'
              AND l.estado = 'activo'
            ORDER BY e.nombre
        ")->fetchAll(PDO::FETCH_ASSOC);
        return compact('roles', 'ligas', 'equipos');
    }

    private function roleKey(int $roleId): ?string
    {
        $stmt = $this->db->prepare("SELECT clave FROM tb_roles WHERE id = :id AND estado = 'activo' LIMIT 1");
        $stmt->execute([':id' => $roleId]);
        $key = $stmt->fetchColumn();
        return $key ? strtoupper((string) $key) : null;
    }

    private function validateScope(int $roleId, ?int $ligaId, ?int $equipoId): array
    {
        $errors = [];
        $key = $this->roleKey($roleId);

        if (!$key) {
            return ['El rol seleccionado no existe o está inactivo.'];
        }

        if (in_array($key, ['ADMIN', 'SUPERVISOR'], true)) {
            return [];
        }

        if ($key === 'LIGA') {
            if (!$ligaId) {
                $errors[] = 'Selecciona la liga que tendrá asignada esta cuenta.';
            } else {
                $check = $this->db->prepare("SELECT COUNT(*) FROM tb_ligas WHERE id = :id AND estado = 'activo'");
                $check->execute([':id' => $ligaId]);
                if ((int) $check->fetchColumn() === 0) {
                    $errors[] = 'La liga seleccionada no existe o está inactiva.';
                }
            }
            return $errors;
        }

        if ($key === 'EQUIPO') {
            if (!$ligaId || !$equipoId) {
                $errors[] = 'Selecciona la liga y el equipo que tendrá asignados esta cuenta.';
                return $errors;
            }

            $stmt = $this->db->prepare("
                SELECT COUNT(*)
                FROM tb_liga_equipos le
                INNER JOIN tb_liga_temporadas lt ON lt.id = le.liga_temporada_id
                WHERE lt.liga_id = :liga_id
                  AND le.equipo_id = :equipo_id
                  AND le.estado_participacion = 'activo'
                  AND lt.estado = 'activo'
            ");
            $stmt->execute([':liga_id' => $ligaId, ':equipo_id' => $equipoId]);
            if ((int) $stmt->fetchColumn() === 0) {
                $errors[] = 'El equipo no tiene una inscripción activa en la liga seleccionada.';
            }
            return $errors;
        }

        $errors[] = 'El rol seleccionado no tiene un flujo de acceso configurado.';
        return $errors;
    }

    private function saveAvatar(?array $file, ?string $current = null): ?string
    {
        if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return $current;
        }
        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK || ($file['size'] ?? 0) > 2 * 1024 * 1024) {
            throw new RuntimeException('El avatar debe cargarse correctamente y no superar 2 MB.');
        }

        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        $formats = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        if (!isset($formats[$mime]) || @getimagesize($file['tmp_name']) === false) {
            throw new RuntimeException('El avatar debe ser una imagen JPG, PNG o WEBP válida.');
        }

        $directory = ROOT_PATH . '/public/assets/img/avatars/';
        if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
            throw new RuntimeException('No está disponible la carpeta de avatares.');
        }

        $filename = 'avatar_' . bin2hex(random_bytes(12)) . '.' . $formats[$mime];
        if (!move_uploaded_file($file['tmp_name'], $directory . $filename)) {
            throw new RuntimeException('No fue posible guardar el avatar.');
        }
        return $filename;
    }

    public function index(): void
    {
        $this->requireManager();
        $roles = (new Role($this->db))->all();
        $ligas = (new Liga($this->db))->all();
        $users = $this->userModel->filtrar([
            'estado' => $_GET['filtroEstado'] ?? 'all',
            'rol' => $_GET['filtroRol'] ?? null,
            'filtro' => trim((string) ($_GET['filtro'] ?? ''))
        ]);
        $viewPath = ROOT_PATH . '/admin/views/pages/users/users.php';
        include ROOT_PATH . '/admin/views/layout.php';
    }

    public function create(): void
    {
        $this->requireManager();
        $data = ['errors' => []];
        extract($this->catalogos());
        $viewPath = ROOT_PATH . '/admin/views/pages/users/create.php';
        include ROOT_PATH . '/admin/views/layout.php';
    }

    public function save(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        try {
            $this->requireManager();
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new RuntimeException('Método no permitido.');
            }

            $usuario = trim((string) ($_POST['usuario'] ?? ''));
            $password = (string) ($_POST['password'] ?? '');
            $rolId = (int) ($_POST['rol_id'] ?? 0);
            $ligaId = !empty($_POST['liga_id']) ? (int) $_POST['liga_id'] : null;
            $equipoId = !empty($_POST['equipo_id']) ? (int) $_POST['equipo_id'] : null;

            if ($usuario === '' || $password === '' || !$rolId) {
                throw new RuntimeException('Usuario, contraseña y rol son obligatorios.');
            }
            if (mb_strlen($usuario) > 100) {
                throw new RuntimeException('El nombre de usuario no puede superar 100 caracteres.');
            }
            if ($this->userModel->usuarioExiste($usuario)) {
                throw new RuntimeException('El nombre de usuario ya está registrado.');
            }

            $key = $this->roleKey($rolId);
            if (in_array($key, ['ADMIN', 'SUPERVISOR'], true)) {
                $ligaId = $equipoId = null;
            } elseif ($key === 'LIGA') {
                $equipoId = null;
            }
            $errors = $this->validateScope($rolId, $ligaId, $equipoId);
            if ($errors) {
                throw new RuntimeException(implode(' ', $errors));
            }

            $avatar = $this->saveAvatar($_FILES['avatar'] ?? null);
            $this->userModel->create([
                'usuario' => $usuario,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'rol_id' => $rolId,
                'estado' => 'activo',
                'avatar' => $avatar,
                'liga_id' => $ligaId,
                'equipo_id' => $equipoId
            ]);

            echo json_encode(['success' => true, 'mensaje' => 'Usuario creado correctamente.']);
        } catch (Throwable $e) {
            error_log('Error al crear usuario: ' . $e->getMessage());
            echo json_encode(['success' => false, 'mensaje' => $e instanceof RuntimeException ? $e->getMessage() : 'No fue posible crear el usuario.']);
        }
        exit;
    }

    public function edit($id): void
    {
        $this->requireManager();
        $user = $this->userModel->findById((int) $id);
        if (!$user) {
            header('Location: index.php?page=users');
            exit;
        }
        $data = ['errors' => []];
        extract($this->catalogos());
        $viewPath = ROOT_PATH . '/admin/views/pages/users/edit.php';
        include ROOT_PATH . '/admin/views/layout.php';
    }

    public function update($id): void
    {
        $this->requireManager();
        $id = (int) $id;
        $user = $this->userModel->findById($id);
        if (!$user) {
            header('Location: index.php?page=users');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=users&action=edit&id=' . $id);
            exit;
        }

        $usuario = trim((string) ($_POST['usuario'] ?? ''));
        $rolId = (int) ($_POST['rol_id'] ?? 0);
        $ligaId = !empty($_POST['liga_id']) ? (int) $_POST['liga_id'] : null;
        $equipoId = !empty($_POST['equipo_id']) ? (int) $_POST['equipo_id'] : null;
        $errors = [];

        if ($usuario === '') {
            $errors[] = 'El nombre de usuario es obligatorio.';
        } elseif (mb_strlen($usuario) > 100) {
            $errors[] = 'El nombre de usuario no puede superar 100 caracteres.';
        }
        if ($this->userModel->usuarioExiste($usuario, $id)) {
            $errors[] = 'El nombre de usuario ya está registrado.';
        }
        $key = $this->roleKey($rolId);
        if (in_array($key, ['ADMIN', 'SUPERVISOR'], true)) {
            $ligaId = $equipoId = null;
        } elseif ($key === 'LIGA') {
            $equipoId = null;
        }
        $errors = array_merge($errors, $this->validateScope($rolId, $ligaId, $equipoId));

        $newAvatar = null;
        try {
            if (!$errors) {
                $avatar = $this->saveAvatar($_FILES['avatar'] ?? null, $user['avatar'] ?? null);
                if ($avatar !== ($user['avatar'] ?? null)) {
                    $newAvatar = $avatar;
                }

                $update = [
                    'usuario' => $usuario,
                    'rol_id' => $rolId,
                    'liga_id' => $ligaId,
                    'equipo_id' => $equipoId,
                    'avatar' => $avatar
                ];
                $password = (string) ($_POST['password'] ?? '');
                if ($password !== '') {
                    $update['password'] = password_hash($password, PASSWORD_DEFAULT);
                }

                $this->userModel->update($id, $update);
                if (!empty($user['avatar']) && $newAvatar) {
                    $oldPath = ROOT_PATH . '/public/assets/img/avatars/' . basename($user['avatar']);
                    if (is_file($oldPath)) {
                        unlink($oldPath);
                    }
                }
                $_SESSION['success'] = 'El usuario se actualizó correctamente.';
                header('Location: index.php?page=users');
                exit;
            }
        } catch (Throwable $e) {
            error_log('Error al actualizar usuario: ' . $e->getMessage());
            $errors[] = 'No fue posible guardar los cambios del usuario.';
        }

        if ($newAvatar) {
            $newPath = ROOT_PATH . '/public/assets/img/avatars/' . basename($newAvatar);
            if (is_file($newPath)) {
                unlink($newPath);
            }
        }
        $user = array_merge($user, [
            'usuario' => $usuario,
            'rol_id' => $rolId,
            'liga_id' => $ligaId,
            'equipo_id' => $equipoId
        ]);
        $data = ['errors' => $errors];
        extract($this->catalogos());
        $viewPath = ROOT_PATH . '/admin/views/pages/users/edit.php';
        include ROOT_PATH . '/admin/views/layout.php';
    }

    public function toggleState(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        try {
            $this->requireManager();
            $id = (int) ($_POST['id'] ?? 0);
            $active = (string) ($_POST['estado'] ?? '') === '1';
            if (!$id) {
                throw new RuntimeException('Usuario no válido.');
            }
            $this->userModel->update($id, ['estado' => $active ? 'activo' : 'inactivo']);
            echo json_encode(['success' => true]);
        } catch (Throwable $e) {
            echo json_encode(['success' => false, 'mensaje' => 'No fue posible cambiar el estado.']);
        }
        exit;
    }

    public function disable($id): void
    {
        $this->requireManager();
        $this->userModel->disable((int) $id);
        header('Location: index.php?page=users');
        exit;
    }

    public function changePassword($id): void
    {
        $this->requireManager();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $password = (string) ($_POST['password'] ?? '');
            if ($password === '') {
                throw new RuntimeException('La nueva contraseña es obligatoria.');
            }
            $this->userModel->changePassword((int) $id, password_hash($password, PASSWORD_DEFAULT));
            header('Location: index.php?page=users');
            exit;
        }
        include ROOT_PATH . '/admin/views/pages/users/change_password.php';
    }
}
