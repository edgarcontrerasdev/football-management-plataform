<?php

class User {

    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /* Busca un usuario activo por su nombre de usuario*/
    public function findByUsername($usuario) {
        $query = $this->pdo->prepare(
            "SELECT * FROM tb_usuarios WHERE usuario = ? AND estado = 1 LIMIT 1"
        );
        $query->execute([$usuario]);
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public function usuarioExiste($usuario, $ignoredId = null)
    {
        $query = $this->pdo->prepare(
            "SELECT
                u.id,
                u.afiliado_id,
                u.usuario,
                u.password,
                u.rol_id,
                u.estado,
                u.avatar,
                r.clave AS rol_clave,
                r.nombre AS rol_nombre
                FROM tb_usuarios u
                INNER JOIN tb_roles r
                ON r.id = u.rol_id
                WHERE u.usuario = ?
                  AND (? IS NULL OR u.id <> ?)
                LIMIT 1"
        );

        $query->execute([$usuario, $ignoredId, $ignoredId]);

        return $query->fetch(PDO::FETCH_ASSOC);
    }
    
    /** Buscar un usuario por su ID*/
    public function findById($id)
    {
        $query = $this->pdo->prepare(
            "SELECT
                u.id,
                u.afiliado_id,
                u.usuario,
                u.password,
                u.rol_id,
                u.estado,
                u.avatar,
                u.nombre,
                u.correo,
                u.telefono,
                u.liga_id,
                u.equipo_id,

                r.clave AS rol_clave,
                r.nombre AS rol_nombre,
                r.estado AS rol_estado

            FROM tb_usuarios u

            INNER JOIN tb_roles r
            ON r.id = u.rol_id

            WHERE u.id = ?

            LIMIT 1"
        );

        $query->execute([$id]);

        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public function all($estado = null) 
    {
        $sql = "SELECT u.*, .clave AS rol,l.nombre AS liga,e.nombre AS equipo FROM tb_usuarios u
            LEFT JOIN tb_roles r ON u.rol_id = r.id LEFT JOIN tb_ligas l ON u.liga_id = l.id 
            LEFT JOIN tb_equipos e ON u.equipo_id = e.id";

        if ($estado !== null) {
            $sql .= " WHERE u.estado = :estado";
            $sql .= " ORDER BY u.id DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':estado' => $estado]);
        } else {
            $sql .= " ORDER BY u.id DESC";
            $stmt = $this->pdo->query($sql);
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function filtrar(array $filtros)
    {
        $filtros = array_merge([
            'estado' => 'all',
            'rol' => null,
            'filtro' => ''
        ], $filtros);
        $sql = "
        SELECT u.*, r.clave AS rol,
        l.nombre AS liga,
        e.nombre AS equipo
        FROM tb_usuarios u
        LEFT JOIN tb_roles r ON u.rol_id = r.id
        LEFT JOIN tb_ligas l ON u.liga_id = l.id
        LEFT JOIN tb_equipos e ON u.equipo_id = e.id
        WHERE 1=1
        ";

        $params = [];

        if ($filtros['estado'] === 'active') {
            $sql .= " AND u.estado = 1";
        } elseif ($filtros['estado'] === 'inactive') {
            $sql .= " AND u.estado = 0";
        }

        if ($filtros['rol'] !== null && $filtros['rol'] !== '') {
            $sql .= " AND u.rol_id = :rol";
            $params[':rol'] = $filtros['rol'];
        }

        if ($filtros['filtro'] !== '') {
            $sql .= " AND (
                u.nombre LIKE :filtro OR
                u.usuario LIKE :filtro OR
                u.correo LIKE :filtro
            )";
            $params[':filtro'] = "%{$filtros['filtro']}%";
        }

        $sql .= " ORDER BY u.id DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /** Crear un nuevo usuario */
    public function create($data) 
    {
        $query = $this->pdo->prepare(
            "INSERT INTO tb_usuarios (nombre,telefono,avatar,correo, usuario, password, rol_id, estado,liga_id,equipo_id)
             VALUES (:nombre,:telefono,:avatar,:correo,:usuario, :password, :rol_id, :estado, :liga_id, :equipo_id)"
        );
        return $query->execute([
            ':nombre' => $data['nombre'],
            ':telefono'=> $data['telefono'],
            ':avatar' => $data['avatar'],
            ':correo' => $data['correo'],
            ':usuario' => $data['usuario'],
            ':password' => $data['password'], // hashear antes de pasar aquí
            ':rol_id' => $data['rol_id'],
            ':estado' => $data['estado'] ?? 1,
            ':liga_id' => $data['liga_id'] ?? null,
            ':equipo_id' => $data['equipo_id'] ?? null
        ]);
    }

    /** Actualizar un usuario existente sin modificar campos omitidos. */
    public function update($id, $data)
    {
        $columns = [
            'nombre' => 'nombre',
            'correo' => 'correo',
            'telefono' => 'telefono',
            'usuario' => 'usuario',
            'rol_id' => 'rol_id',
            'estado' => 'estado',
            'liga_id' => 'liga_id',
            'equipo_id' => 'equipo_id',
            'avatar' => 'avatar',
            'password' => 'password'
        ];

        $fields = [];
        $params = [':id' => (int) $id];

        foreach ($columns as $key => $column) {
            if (array_key_exists($key, $data)) {
                $fields[] = $column . ' = :' . $key;
                $params[':' . $key] = $data[$key];
            }
        }

        if (!$fields) {
            return false;
        }

        $stmt = $this->pdo->prepare(
            'UPDATE tb_usuarios SET ' . implode(', ', $fields) . ' WHERE id = :id'
        );
        return $stmt->execute($params);
    }


    /** Desactivar un usuario (soft delete)*/
    public function disable($id) {
        $query = $this->pdo->prepare(
            "UPDATE tb_usuarios SET estado = 0 WHERE id = ?"
        );
        return $query->execute([$id]);
    }

    /** Cambiar contraseña*/
    public function changePassword($id, $hashedPassword) {
        $query = $this->pdo->prepare(
            "UPDATE tb_usuarios SET password = :password WHERE id = :id"
        );
        return $query->execute([
            ':password' => $hashedPassword,
            ':id' => $id
        ]);
    }

    //buscar con filtros
    public function filter($search = '', $estado = 'all', $rol = null)
    {
        $sql = "
            SELECT u.*, r.clave AS rol
            FROM tb_usuarios u
            LEFT JOIN tb_roles r ON u.rol_id = r.id
            WHERE 1=1
        ";

        $params = [];

        if ($search !== '') {
            $sql .= " AND (
                u.nombre LIKE :search
                OR u.usuario LIKE :search
                OR u.correo LIKE :search
            )";
            $params[':search'] = "%$search%";
        }

        if ($estado !== 'all') {
            $sql .= " AND u.estado = :estado";
            $params[':estado'] = $estado;
        }

        if (!empty($rol)) {
            $sql .= " AND u.rol_id = :rol";
            $params[':rol'] = $rol;
        }

        $sql .= " ORDER BY u.id DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function filterUsers($params)
    {
        $sqlBase = "
            FROM tb_usuarios u
            LEFT JOIN tb_roles r ON r.id = u.rol_id
            LEFT JOIN tb_ligas l ON l.id = u.liga_id
            LEFT JOIN tb_equipos e ON e.id = u.equipo_id
            WHERE 1=1
        ";

        $binds = [];

        if ($params['filtro'] !== '') {
            $sqlBase .= " AND (u.usuario LIKE :filtro OR u.nombre LIKE :filtro OR u.correo LIKE :filtro)";
            $binds[':filtro'] = '%' . $params['filtro'] . '%';
        }

        if ($params['estado'] !== '' && $params['estado'] !== null) {
            $sqlBase .= " AND u.estado = :estado ";
            $binds[':estado'] = $params['estado'];
        }

        if (!empty($params['rol'])) {
            $sqlBase .= " AND u.rol_id = :rol ";
            $binds[':rol'] = $params['rol'];
        }

        if (!empty($params['liga'])) {
            $sqlBase .= " AND u.liga_id = :liga ";
            $binds[':liga'] = $params['liga'];
        }

        // 1️⃣ Total de registros filtrados
        $stmtTotal = $this->pdo->prepare("SELECT COUNT(*) AS total $sqlBase");
        $stmtTotal->execute($binds);
        $total = (int)$stmtTotal->fetch(PDO::FETCH_ASSOC)['total'];

        // 2️⃣ Datos de la página actual
        $pagina = max(1, (int)$params['pagina']);
        $limite = max(1, (int)$params['limite']);
        $offset = ($pagina - 1) * $limite;

        $sqlDatos = "
            SELECT 
                u.id,
                u.usuario,
                u.nombre,
                u.correo,
                u.estado,
                u.telefono,
                r.clave AS rol,
                l.nombre AS liga,
                e.nombre AS equipo,
                u.avatar
            $sqlBase
            ORDER BY u.nombre ASC
            LIMIT $limite OFFSET $offset
        ";

        $stmt = $this->pdo->prepare($sqlDatos);
        $stmt->execute($binds);
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'total' => $total,
            'usuarios' => $usuarios
        ];
    }

    //funcion para contar usuarios filtrados
   public function countUsers            ($params)
    {
        $sql = "SELECT COUNT(*) FROM tb_usuarios u
                LEFT JOIN tb_roles r ON r.id = u.rol_id
                LEFT JOIN tb_ligas l ON l.id = u.liga_id
                WHERE 1=1";

        $binds = [];

        // filtros
        if ($params['filtro'] !== '') {
            $sql .= " AND (u.usuario LIKE :filtro OR u.nombre LIKE :filtro OR u.correo LIKE :filtro)";
            $binds[':filtro'] = '%' . $params['filtro'] . '%';
        }

        if ($params['estado'] !== null && $params['estado'] !== '') {
            $sql .= " AND u.estado = :estado";
            $binds[':estado'] = $params['estado'];
        }

        if (!empty($params['rol'])) {
            $sql .= " AND u.rol_id = :rol";
            $binds[':rol'] = $params['rol'];
        }

        if (!empty($params['liga'])) {
            $sql .= " AND u.liga_id = :liga";
            $binds[':liga'] = $params['liga'];
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($binds);

        return (int)$stmt->fetchColumn();
    }

}

?>