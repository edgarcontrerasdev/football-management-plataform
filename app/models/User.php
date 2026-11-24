<?php

class User
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    private function selectBase(): string
    {
        return "
            SELECT
                u.id, u.afiliado_id, u.usuario, u.password, u.rol_id,
                u.estado, u.avatar, u.created_at,
                COALESCE(
                    NULLIF(TRIM(CONCAT_WS(' ', a.nombres, a.apellido_paterno, a.apellido_materno)), ''),
                    u.usuario
                ) AS nombre,
                r.clave AS rol_clave,
                r.nombre AS rol_nombre,
                (
                    SELECT ua.entidad_id
                    FROM tb_usuario_alcances ua
                    WHERE ua.usuario_id = u.id
                      AND ua.tipo_entidad = 'liga'
                      AND ua.estado = 'activo'
                    ORDER BY ua.id DESC LIMIT 1
                ) AS liga_id,
                (
                    SELECT ua.entidad_id
                    FROM tb_usuario_alcances ua
                    WHERE ua.usuario_id = u.id
                      AND ua.tipo_entidad = 'equipo'
                      AND ua.estado = 'activo'
                    ORDER BY ua.id DESC LIMIT 1
                ) AS equipo_id,
                (
                    SELECT l.nombre
                    FROM tb_usuario_alcances ua
                    INNER JOIN tb_ligas l ON l.id = ua.entidad_id
                    WHERE ua.usuario_id = u.id
                      AND ua.tipo_entidad = 'liga'
                      AND ua.estado = 'activo'
                    ORDER BY ua.id DESC LIMIT 1
                ) AS liga,
                (
                    SELECT e.nombre
                    FROM tb_usuario_alcances ua
                    INNER JOIN tb_equipos e ON e.id = ua.entidad_id
                    WHERE ua.usuario_id = u.id
                      AND ua.tipo_entidad = 'equipo'
                      AND ua.estado = 'activo'
                    ORDER BY ua.id DESC LIMIT 1
                ) AS equipo
            FROM tb_usuarios u
            INNER JOIN tb_roles r ON r.id = u.rol_id
            LEFT JOIN tb_afiliados a ON a.id = u.afiliado_id
        ";
    }

    public function findByUsername($usuario)
    {
        $sql = "
            SELECT u.*, r.clave AS rol_clave, r.nombre AS rol_nombre,
                   r.estado AS rol_estado
            FROM tb_usuarios u
            INNER JOIN tb_roles r ON r.id = u.rol_id
            WHERE u.usuario = :usuario AND u.estado = 'activo'
            LIMIT 1
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':usuario' => trim((string) $usuario)]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function usuarioExiste($usuario, $ignoredId = null)
    {
        $sql = "SELECT id FROM tb_usuarios WHERE usuario = :usuario";
        $params = [':usuario' => trim((string) $usuario)];
        if ($ignoredId !== null) {
            $sql .= " AND id <> :ignored_id";
            $params[':ignored_id'] = (int) $ignoredId;
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function findById($id)
    {
        $stmt = $this->pdo->prepare($this->selectBase() . " WHERE u.id = :id LIMIT 1");
        $stmt->execute([':id' => (int) $id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user) {
            return null;
        }

        $role = $this->pdo->prepare("SELECT estado AS rol_estado FROM tb_roles WHERE id = :id");
        $role->execute([':id' => (int) $user['rol_id']]);
        $user['rol_estado'] = $role->fetchColumn() ?: null;
        return $user;
    }

    public function all($estado = null)
    {
        $sql = $this->selectBase();
        $params = [];
        if ($estado !== null && $estado !== 'all') {
            $sql .= " WHERE u.estado = :estado";
            $params[':estado'] = $estado;
        }
        $sql .= " ORDER BY u.id DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function filtrar(array $filtros)
    {
        $filtros = array_merge(['estado' => 'all', 'rol' => null, 'filtro' => ''], $filtros);
        $sql = $this->selectBase() . " WHERE 1=1";
        $params = [];

        if ($filtros['estado'] === 'active' || $filtros['estado'] === '1') {
            $sql .= " AND u.estado = 'activo'";
        } elseif ($filtros['estado'] === 'inactive' || $filtros['estado'] === '0') {
            $sql .= " AND u.estado <> 'activo'";
        }

        if ($filtros['rol'] !== null && $filtros['rol'] !== '') {
            $sql .= " AND u.rol_id = :rol";
            $params[':rol'] = (int) $filtros['rol'];
        }

        if (!empty($filtros['liga'])) {
            $sql .= " AND EXISTS (
                SELECT 1 FROM tb_usuario_alcances ula
                WHERE ula.usuario_id = u.id
                  AND ula.tipo_entidad = 'liga'
                  AND ula.entidad_id = :liga_id
                  AND ula.estado = 'activo'
            )";
            $params[':liga_id'] = (int) $filtros['liga'];
        }

        if ($filtros['filtro'] !== '') {
            $sql .= " AND (
                u.usuario LIKE :filtro
                OR a.nombres LIKE :filtro
                OR a.apellido_paterno LIKE :filtro
                OR a.apellido_materno LIKE :filtro
            )";
            $params[':filtro'] = '%' . $filtros['filtro'] . '%';
        }

        $sql .= " ORDER BY u.id DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO tb_usuarios (afiliado_id, usuario, password, rol_id, estado, avatar)
                VALUES (:afiliado_id, :usuario, :password, :rol_id, :estado, :avatar)
            ");
            $stmt->execute([
                ':afiliado_id' => !empty($data['afiliado_id']) ? (int) $data['afiliado_id'] : null,
                ':usuario' => trim((string) $data['usuario']),
                ':password' => $data['password'],
                ':rol_id' => (int) $data['rol_id'],
                ':estado' => $data['estado'] ?? 'activo',
                ':avatar' => $data['avatar'] ?? null
            ]);

            $userId = (int) $this->pdo->lastInsertId();
            $this->replaceScopes($userId, $data);
            $this->pdo->commit();
            return $userId;
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }

    public function update($id, $data)
    {
        $id = (int) $id;
        $fields = [];
        $params = [':id' => $id];
        $columns = [
            'usuario' => 'usuario',
            'rol_id' => 'rol_id',
            'estado' => 'estado',
            'avatar' => 'avatar',
            'password' => 'password',
            'afiliado_id' => 'afiliado_id'
        ];

        foreach ($columns as $key => $column) {
            if (array_key_exists($key, $data)) {
                $fields[] = $column . ' = :' . $key;
                $params[':' . $key] = $data[$key];
            }
        }

        $this->pdo->beginTransaction();
        try {
            if ($fields) {
                $stmt = $this->pdo->prepare(
                    'UPDATE tb_usuarios SET ' . implode(', ', $fields) . ' WHERE id = :id'
                );
                $stmt->execute($params);
            }

            if (array_key_exists('liga_id', $data) || array_key_exists('equipo_id', $data)) {
                $this->replaceScopes($id, $data);
            }

            $this->pdo->commit();
            return true;
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }

    private function replaceScopes(int $userId, array $data): void
    {
        $this->pdo->prepare("
            UPDATE tb_usuario_alcances
            SET estado = 'inactivo', fecha_fin = COALESCE(fecha_fin, CURDATE())
            WHERE usuario_id = :usuario_id
              AND tipo_entidad IN ('liga', 'equipo')
              AND estado = 'activo'
        ")->execute([':usuario_id' => $userId]);

        $insert = $this->pdo->prepare("
            INSERT INTO tb_usuario_alcances
                (usuario_id, tipo_entidad, entidad_id, estado, fecha_inicio)
            VALUES (:usuario_id, :tipo_entidad, :entidad_id, 'activo', CURDATE())
        ");

        if (!empty($data['liga_id'])) {
            $insert->execute([
                ':usuario_id' => $userId,
                ':tipo_entidad' => 'liga',
                ':entidad_id' => (int) $data['liga_id']
            ]);
        }

        if (!empty($data['equipo_id'])) {
            $insert->execute([
                ':usuario_id' => $userId,
                ':tipo_entidad' => 'equipo',
                ':entidad_id' => (int) $data['equipo_id']
            ]);
        }
    }

    public function disable($id)
    {
        return $this->update($id, ['estado' => 'inactivo']);
    }

    public function changePassword($id, $hashedPassword)
    {
        return $this->update($id, ['password' => $hashedPassword]);
    }

    public function filter($search = '', $estado = 'all', $rol = null)
    {
        return $this->filtrar([
            'filtro' => $search,
            'estado' => $estado,
            'rol' => $rol
        ]);
    }

    public function filterUsers($params)
    {
        $filtros = [
            'filtro' => trim((string) ($params['filtro'] ?? '')),
            'estado' => $params['estado'] ?? 'all',
            'rol' => $params['rol'] ?? null,
            'liga' => $params['liga'] ?? null
        ];
        $todos = $this->filtrar($filtros);
        $total = count($todos);
        $pagina = max(1, (int) ($params['pagina'] ?? 1));
        $limite = min(100, max(1, (int) ($params['limite'] ?? 10)));
        return [
            'total' => $total,
            'usuarios' => array_slice($todos, ($pagina - 1) * $limite, $limite)
        ];
    }

    public function countUsers($params)
    {
        $filtros = [
            'filtro' => trim((string) ($params['filtro'] ?? '')),
            'estado' => $params['estado'] ?? 'all',
            'rol' => $params['rol'] ?? null
        ];
        return count($this->filtrar($filtros));
    }
}
