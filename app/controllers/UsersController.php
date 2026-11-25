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
        Auth::requireRole(['ADMIN', 'ORGANISMO']);

        if (Auth::role() === 'ORGANISMO' && !$this->currentOrganismoId()) {
            http_response_code(403);
            view('403.php');
            exit;
        }
    }

    private function isPlatformAdmin(): bool
    {
        return Auth::role() === 'ADMIN';
    }

    private function currentOrganismoId(): ?int
    {
        if ($this->isPlatformAdmin()) {
            return null;
        }

        $stmt = $this->db->prepare("
            SELECT entidad_id
            FROM tb_usuario_alcances
            WHERE usuario_id = :usuario_id
              AND tipo_entidad = 'organismo'
              AND estado = 'activo'
              AND (fecha_inicio IS NULL OR fecha_inicio <= CURDATE())
              AND (fecha_fin IS NULL OR fecha_fin >= CURDATE())
            ORDER BY id DESC
            LIMIT 1
        ");
        $stmt->execute([':usuario_id' => Auth::id()]);
        $id = $stmt->fetchColumn();
        return $id ? (int) $id : 0;
    }

    private function assertCanManageUser(array $user): void
    {
        if ($this->isPlatformAdmin()) {
            return;
        }

        $organismoId = $this->currentOrganismoId();
        if (
            !$organismoId
            || (int) ($user['organismo_id'] ?? 0) !== $organismoId
            || !in_array(strtoupper((string) ($user['rol_clave'] ?? '')), ['LIGA', 'EQUIPO'], true)
        ) {
            http_response_code(403);
            view('403.php');
            exit;
        }

        // La cuenta no puede conservar alcances activos fuera del organismo gestor.
        $scopeCheck = $this->db->prepare("
            SELECT COUNT(*)
            FROM tb_usuario_alcances ua
            WHERE ua.usuario_id = :usuario_id
              AND ua.estado = 'activo'
              AND (
                    COALESCE(
                        ua.organismo_id,
                        CASE WHEN ua.tipo_entidad = 'organismo' THEN ua.entidad_id END
                    ) IS NULL
                    OR COALESCE(
                        ua.organismo_id,
                        CASE WHEN ua.tipo_entidad = 'organismo' THEN ua.entidad_id END
                    ) <> :organismo_id
              )
        ");
        $scopeCheck->execute([
            ':usuario_id' => (int) $user['id'],
            ':organismo_id' => $organismoId
        ]);

        if ((int) $scopeCheck->fetchColumn() > 0) {
            http_response_code(403);
            view('403.php');
            exit;
        }
    }

    private function protectLastPlatformAdmin(array $target, ?string $newRoleKey = null, bool $deactivating = false): void
    {
        if (strtoupper((string) ($target['rol_clave'] ?? '')) !== 'ADMIN') {
            return;
        }

        $removingAdmin = $deactivating || ($newRoleKey !== null && $newRoleKey !== 'ADMIN');
        if (!$removingAdmin) {
            return;
        }

        $count = (int) $this->db->query("
            SELECT COUNT(*)
            FROM tb_usuarios u
            INNER JOIN tb_roles r ON r.id = u.rol_id
            WHERE r.clave = 'ADMIN'
              AND r.estado = 'activo'
              AND u.estado = 'activo'
        ")->fetchColumn();

        if ($count <= 1) {
            throw new RuntimeException('No puedes desactivar o convertir al último administrador de plataforma. Crea primero otra cuenta ADMIN.');
        }
    }

    private function catalogos(): array
    {
        $organismoId = $this->currentOrganismoId();
        if ($this->isPlatformAdmin()) {
            $roles = array_values(array_filter(
                (new Role($this->db))->all(),
                static fn($role) => in_array(strtoupper((string) ($role['clave'] ?? '')), ['ADMIN', 'ORGANISMO', 'LIGA', 'EQUIPO'], true)
                    && ($role['estado'] ?? 'activo') === 'activo'
            ));
            $organismos = $this->db->query("
                SELECT id, nombre
                FROM tb_organismos
                WHERE estado = 'activo'
                ORDER BY nombre
            ")->fetchAll(PDO::FETCH_ASSOC);
            $ligaWhere = "l.estado = 'activo'
                AND (
                    lo.id IS NOT NULL
                    OR NOT EXISTS (
                        SELECT 1 FROM tb_liga_organismos lo_any
                        WHERE lo_any.liga_id = l.id AND lo_any.estado = 'activo'
                    )
                )";
            $params = [];
        } else {
            $roles = array_values(array_filter(
                (new Role($this->db))->all(),
                static fn($role) => in_array(strtoupper((string) ($role['clave'] ?? '')), ['LIGA', 'EQUIPO'], true)
                    && ($role['estado'] ?? 'activo') === 'activo'
            ));
            $organismos = [];
            $ligaWhere = "l.estado = 'activo' AND lo.organismo_id = :organismo_id AND lo.estado = 'activo'";
            $params = [':organismo_id' => $organismoId];
        }

        $sqlLigas = "
            SELECT DISTINCT l.id, l.nombre, lo.organismo_id
            FROM tb_ligas l
            " . ($this->isPlatformAdmin()
                ? "LEFT JOIN tb_liga_organismos lo ON lo.liga_id = l.id AND lo.estado = 'activo' AND lo.es_gestor = 1"
                : "INNER JOIN tb_liga_organismos lo ON lo.liga_id = l.id AND lo.estado = 'activo' AND lo.es_gestor = 1") . "
            WHERE {$ligaWhere}
            ORDER BY l.nombre
        ";
        $stmt = $this->db->prepare($sqlLigas);
        $stmt->execute($params);
        $ligas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $sqlEquipos = "
            SELECT DISTINCT e.id, e.nombre, lt.liga_id
            FROM tb_equipos e
            INNER JOIN tb_liga_equipos le ON le.equipo_id = e.id
            INNER JOIN tb_liga_temporadas lt ON lt.id = le.liga_temporada_id
            INNER JOIN tb_ligas l ON l.id = lt.liga_id
            " . ($this->isPlatformAdmin()
                ? "LEFT JOIN tb_liga_organismos lo ON lo.liga_id = l.id AND lo.estado = 'activo' AND lo.es_gestor = 1"
                : "INNER JOIN tb_liga_organismos lo ON lo.liga_id = l.id AND lo.estado = 'activo' AND lo.es_gestor = 1") . "
            WHERE le.estado_participacion = 'activo'
              AND lt.estado = 'activo'
              AND l.estado = 'activo'
              " . ($this->isPlatformAdmin()
                    ? "AND (lo.id IS NOT NULL OR NOT EXISTS (SELECT 1 FROM tb_liga_organismos lo_any WHERE lo_any.liga_id = l.id AND lo_any.estado = 'activo'))"
                    : "AND lo.organismo_id = :organismo_id") . "
            ORDER BY e.nombre
        ";
        $stmt = $this->db->prepare($sqlEquipos);
        $stmt->execute($params);
        $equipos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return compact('roles', 'ligas', 'equipos', 'organismos', 'organismoId');
    }

    private function roleKey(int $roleId): ?string
    {
        $stmt = $this->db->prepare("SELECT clave FROM tb_roles WHERE id = :id AND estado = 'activo' LIMIT 1");
        $stmt->execute([':id' => $roleId]);
        $key = $stmt->fetchColumn();
        return $key ? strtoupper((string) $key) : null;
    }

    private function validateScope(int $roleId, ?int $organismoId, ?int $ligaId, ?int $equipoId): array
    {
        $key = $this->roleKey($roleId);
        if (!$key) {
            return ['El rol seleccionado no existe o está inactivo.'];
        }

        if (!$this->isPlatformAdmin()) {
            $currentOrg = $this->currentOrganismoId();
            if (!$currentOrg || $organismoId !== $currentOrg) {
                return ['No tienes autorización para asignar usuarios a otro organismo.'];
            }
            if (!in_array($key, ['LIGA', 'EQUIPO'], true)) {
                return ['Tu organismo solo puede crear cuentas de liga y equipo.'];
            }
        }

        if ($key === 'ADMIN') {
            return $this->isPlatformAdmin() ? [] : ['No tienes autorización para crear cuentas de administración global.'];
        }
        if ($key === 'SUPERVISOR') {
            return ['El rol Supervisor aún no está habilitado para creación de cuentas; requiere asignación explícita de alcances.'];
        }

        if ($key === 'ORGANISMO') {
            if (!$this->isPlatformAdmin() || !$organismoId) {
                return ['Solo el administrador de plataforma puede crear administradores de organismo y debe seleccionar su organismo.'];
            }
            $orgCheck = $this->db->prepare("SELECT COUNT(*) FROM tb_organismos WHERE id = :id AND estado = 'activo'");
            $orgCheck->execute([':id' => $organismoId]);
            return (int) $orgCheck->fetchColumn() > 0 ? [] : ['El organismo seleccionado no existe o está inactivo.'];
        }

        if (!in_array($key, ['LIGA', 'EQUIPO'], true) || !$ligaId) {
            return ['Selecciona una liga válida para esta cuenta.'];
        }

        if ($organismoId !== null) {
            $orgCheck = $this->db->prepare("SELECT COUNT(*) FROM tb_organismos WHERE id = :id AND estado = 'activo'");
            $orgCheck->execute([':id' => $organismoId]);
            if ((int) $orgCheck->fetchColumn() === 0) {
                return ['El organismo seleccionado no existe o está inactivo.'];
            }

            $ligaCheck = $this->db->prepare("
                SELECT COUNT(*)
                FROM tb_ligas l
                INNER JOIN tb_liga_organismos lo ON lo.liga_id = l.id
                WHERE l.id = :liga_id
                  AND l.estado = 'activo'
                  AND lo.organismo_id = :organismo_id
                  AND lo.estado = 'activo'
                  AND lo.es_gestor = 1
            ");
            $ligaCheck->execute([':liga_id' => $ligaId, ':organismo_id' => $organismoId]);
        } else {
            if (!$this->isPlatformAdmin()) {
                return ['Solo el administrador de plataforma puede asignar cuentas a ligas independientes.'];
            }
            $ligaCheck = $this->db->prepare("
                SELECT COUNT(*)
                FROM tb_ligas l
                WHERE l.id = :liga_id
                  AND l.estado = 'activo'
                  AND NOT EXISTS (
                      SELECT 1 FROM tb_liga_organismos lo
                      WHERE lo.liga_id = l.id AND lo.estado = 'activo' AND lo.es_gestor = 1
                  )
            ");
            $ligaCheck->execute([':liga_id' => $ligaId]);
        }

        if ((int) $ligaCheck->fetchColumn() === 0) {
            return ['La liga no pertenece al organismo seleccionado o no está disponible para asignación independiente.'];
        }

        if ($key === 'EQUIPO') {
            if (!$equipoId) {
                return ['Selecciona el equipo que tendrá asignado esta cuenta.'];
            }
            if ($organismoId !== null) {
                $teamCheck = $this->db->prepare("
                    SELECT COUNT(*)
                    FROM tb_liga_equipos le
                    INNER JOIN tb_liga_temporadas lt ON lt.id = le.liga_temporada_id
                    INNER JOIN tb_liga_organismos lo ON lo.liga_id = lt.liga_id
                    WHERE lt.liga_id = :liga_id
                      AND le.equipo_id = :equipo_id
                      AND lo.organismo_id = :organismo_id
                      AND lo.estado = 'activo'
                      AND le.estado_participacion = 'activo'
                      AND lt.estado = 'activo'
                ");
                $teamCheck->execute([
                    ':liga_id' => $ligaId,
                    ':equipo_id' => $equipoId,
                    ':organismo_id' => $organismoId
                ]);
            } else {
                $teamCheck = $this->db->prepare("
                    SELECT COUNT(*)
                    FROM tb_liga_equipos le
                    INNER JOIN tb_liga_temporadas lt ON lt.id = le.liga_temporada_id
                    WHERE lt.liga_id = :liga_id
                      AND le.equipo_id = :equipo_id
                      AND le.estado_participacion = 'activo'
                      AND lt.estado = 'activo'
                      AND NOT EXISTS (
                          SELECT 1 FROM tb_liga_organismos lo
                          WHERE lo.liga_id = lt.liga_id AND lo.estado = 'activo'
                      )
                ");
                $teamCheck->execute([
                    ':liga_id' => $ligaId,
                    ':equipo_id' => $equipoId
                ]);
            }
            if ((int) $teamCheck->fetchColumn() === 0) {
                return ['El equipo no tiene una inscripción activa en la liga y organismo seleccionados.'];
            }
        }

        return [];
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
        $organismoId = $this->currentOrganismoId();
        if ($organismoId === null) {
            $ligas = (new Liga($this->db))->all();
        } else {
            $stmt = $this->db->prepare("
                SELECT DISTINCT l.id, l.nombre
                FROM tb_ligas l
                INNER JOIN tb_liga_organismos lo ON lo.liga_id = l.id
                WHERE lo.organismo_id = :organismo_id
                  AND lo.estado = 'activo'
                  AND l.estado = 'activo'
                ORDER BY l.nombre
            ");
            $stmt->execute([':organismo_id' => $organismoId]);
            $ligas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        $users = $this->userModel->filtrar([
            'estado' => $_GET['filtroEstado'] ?? 'all',
            'rol' => $_GET['filtroRol'] ?? null,
            'filtro' => trim((string) ($_GET['filtro'] ?? '')),
            'organismo' => $this->currentOrganismoId()
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
            $organismoId = $this->isPlatformAdmin()
                ? (!empty($_POST['organismo_id']) ? (int) $_POST['organismo_id'] : null)
                : $this->currentOrganismoId();
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
            if (in_array($key, ['ADMIN', 'SUPERVISOR', 'ORGANISMO'], true)) {
                $ligaId = $equipoId = null;
            } elseif ($key === 'LIGA') {
                $equipoId = null;
            }
            $errors = $this->validateScope($rolId, $organismoId, $ligaId, $equipoId);
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
                'organismo_id' => $organismoId,
                'organismo_scope' => $key === 'ORGANISMO',
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
        $this->assertCanManageUser($user);
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
        $this->assertCanManageUser($user);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=users&action=edit&id=' . $id);
            exit;
        }

        $usuario = trim((string) ($_POST['usuario'] ?? ''));
        $rolId = (int) ($_POST['rol_id'] ?? 0);
        $organismoId = $this->isPlatformAdmin()
            ? (!empty($_POST['organismo_id']) ? (int) $_POST['organismo_id'] : null)
            : $this->currentOrganismoId();
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
        try {
            $this->protectLastPlatformAdmin($user, $key);
        } catch (RuntimeException $e) {
            $errors[] = $e->getMessage();
        }
        if (in_array($key, ['ADMIN', 'SUPERVISOR', 'ORGANISMO'], true)) {
            $ligaId = $equipoId = null;
        } elseif ($key === 'LIGA') {
            $equipoId = null;
        }
        $errors = array_merge($errors, $this->validateScope($rolId, $organismoId, $ligaId, $equipoId));

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
                    'organismo_id' => $organismoId,
                    'organismo_scope' => $key === 'ORGANISMO',
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
            'organismo_id' => $organismoId,
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
            $target = $this->userModel->findById($id);
            if (!$target) {
                throw new RuntimeException('Usuario no encontrado.');
            }
            $this->assertCanManageUser($target);
            if (!$active) {
                $this->protectLastPlatformAdmin($target, null, true);
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
        $target = $this->userModel->findById((int) $id);
        if (!$target) {
            header('Location: index.php?page=users');
            exit;
        }
        $this->assertCanManageUser($target);
        try {
            $this->protectLastPlatformAdmin($target, null, true);
            $this->userModel->disable((int) $id);
            $_SESSION['success'] = 'El usuario fue desactivado.';
        } catch (RuntimeException $e) {
            $_SESSION['error'] = $e->getMessage();
        }
        header('Location: index.php?page=users');
        exit;
    }

    public function changePassword($id): void
    {
        $this->requireManager();
        $target = $this->userModel->findById((int) $id);
        if (!$target) {
            header('Location: index.php?page=users');
            exit;
        }
        $this->assertCanManageUser($target);
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
