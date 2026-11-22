<?php

declare(strict_types=1);

class Organismo
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function listar(array $filtros = [], int $limit = 10, int $offset = 0): array
    {
        $where = ['1 = 1'];
        $params = [];

        $estado = trim((string) ($filtros['estado'] ?? ''));
        $tipo = trim((string) ($filtros['tipo'] ?? ''));
        $buscar = trim((string) ($filtros['buscar'] ?? ''));

        if ($estado !== '') {
            $where[] = 'estado = :estado';
            $params[':estado'] = $estado;
        }

        if ($tipo !== '') {
            $where[] = 'tipo = :tipo';
            $params[':tipo'] = $tipo;
        }

        if ($buscar !== '') {
            $where[] = '(nombre LIKE :buscar_nombre OR siglas LIKE :buscar_siglas)';
            $valor = '%' . $buscar . '%';
            $params[':buscar_nombre'] = $valor;
            $params[':buscar_siglas'] = $valor;
        }

        $sql = "
            SELECT id, nombre, siglas, tipo, alcance_territorial, estado,
                   correo, telefono, logo, created_at
            FROM tb_organismos
            WHERE " . implode(' AND ', $where) . "
            ORDER BY nombre ASC
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $this->pdo->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function contar(array $filtros = []): int
    {
        $where = [];
        $params = [];

        $estado = trim((string) ($filtros['estado'] ?? ''));
        $tipo = trim((string) ($filtros['tipo'] ?? ''));
        $buscar = trim((string) ($filtros['buscar'] ?? ''));

        if ($estado !== '') {
            $where[] = 'estado = :estado';
            $params[':estado'] = $estado;
        }

        if ($tipo !== '') {
            $where[] = 'tipo = :tipo';
            $params[':tipo'] = $tipo;
        }

        if ($buscar !== '') {
            $where[] = '(nombre LIKE :buscar_nombre OR siglas LIKE :buscar_siglas)';
            $valor = '%' . $buscar . '%';
            $params[':buscar_nombre'] = $valor;
            $params[':buscar_siglas'] = $valor;
        }

        $sql = 'SELECT COUNT(*) FROM tb_organismos';
        if ($where !== []) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    public function metricas(): array
    {
        $stmt = $this->pdo->query("
            SELECT
                COUNT(*) AS total,
                SUM(estado = 'activo') AS activos,
                SUM(estado <> 'activo') AS inactivos
            FROM tb_organismos
        ");

        $row = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

        return [
            'total' => (int) ($row['total'] ?? 0),
            'activos' => (int) ($row['activos'] ?? 0),
            'inactivos' => (int) ($row['inactivos'] ?? 0)
        ];
    }

    public function obtenerPorId(int $id): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT id, nombre, siglas, tipo, alcance_territorial, estado,
                   descripcion, correo, telefono, sitio_web, logo,
                   fecha_creacion, fecha_modificacion, created_at, updated_at
            FROM tb_organismos
            WHERE id = :id
            LIMIT 1
        ");

        $stmt->execute([':id' => $id]);
        $organismo = $stmt->fetch(PDO::FETCH_ASSOC);

        return $organismo ?: null;
    }

    public function crear(array $data): int
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO tb_organismos (
                nombre, siglas, tipo, alcance_territorial, estado,
                descripcion, correo, telefono, sitio_web, logo, fecha_creacion
            ) VALUES (
                :nombre, :siglas, :tipo, :alcance_territorial, 'activo',
                :descripcion, :correo, :telefono, :sitio_web, :logo, :fecha_creacion
            )
        ");

        $stmt->execute([
            ':nombre' => trim((string) $data['nombre']),
            ':siglas' => trim((string) ($data['siglas'] ?? '')) ?: null,
            ':tipo' => trim((string) $data['tipo']),
            ':alcance_territorial' => trim((string) ($data['alcance_territorial'] ?? '')) ?: null,
            ':descripcion' => trim((string) ($data['descripcion'] ?? '')) ?: null,
            ':correo' => trim((string) ($data['correo'] ?? '')) ?: null,
            ':telefono' => trim((string) ($data['telefono'] ?? '')) ?: null,
            ':sitio_web' => trim((string) ($data['sitio_web'] ?? '')) ?: null,
            ':logo' => $data['logo'] ?? null,
            ':fecha_creacion' => trim((string) ($data['fecha_creacion'] ?? '')) ?: null
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function actualizar(int $id, array $data): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE tb_organismos
            SET nombre = :nombre,
                siglas = :siglas,
                tipo = :tipo,
                alcance_territorial = :alcance_territorial,
                estado = :estado,
                descripcion = :descripcion,
                correo = :correo,
                telefono = :telefono,
                sitio_web = :sitio_web,
                logo = :logo,
                fecha_creacion = :fecha_creacion,
                fecha_modificacion = NOW()
            WHERE id = :id
        ");

        return $stmt->execute([
            ':nombre' => trim((string) $data['nombre']),
            ':siglas' => trim((string) ($data['siglas'] ?? '')) ?: null,
            ':tipo' => trim((string) $data['tipo']),
            ':alcance_territorial' => trim((string) ($data['alcance_territorial'] ?? '')) ?: null,
            ':estado' => trim((string) $data['estado']),
            ':descripcion' => trim((string) ($data['descripcion'] ?? '')) ?: null,
            ':correo' => trim((string) ($data['correo'] ?? '')) ?: null,
            ':telefono' => trim((string) ($data['telefono'] ?? '')) ?: null,
            ':sitio_web' => trim((string) ($data['sitio_web'] ?? '')) ?: null,
            ':logo' => $data['logo'] ?? null,
            ':fecha_creacion' => trim((string) ($data['fecha_creacion'] ?? '')) ?: null,
            ':id' => $id
        ]);
    }

    public function obtenerLigas(int $organismoId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT l.id, l.nombre, l.siglas, l.estado AS estado_liga,
                   lo.tipo_relacion, lo.es_gestor, lo.fecha_inicio,
                   lo.fecha_fin, lo.estado AS estado_relacion
            FROM tb_liga_organismos lo
            INNER JOIN tb_ligas l ON l.id = lo.liga_id
            WHERE lo.organismo_id = :organismo_id
            ORDER BY lo.estado = 'activo' DESC, l.nombre ASC
        ");
        $stmt->execute([':organismo_id' => $organismoId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarLigasDisponibles(int $organismoId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT l.id, l.nombre, l.siglas
            FROM tb_ligas l
            WHERE l.estado = 'activo'
              AND NOT EXISTS (
                  SELECT 1 FROM tb_liga_organismos lo
                  WHERE lo.liga_id = l.id
                    AND lo.organismo_id = :organismo_id
                    AND lo.estado = 'activo'
              )
            ORDER BY l.nombre ASC
        ");
        $stmt->execute([':organismo_id' => $organismoId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
