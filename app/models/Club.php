<?php

declare(strict_types=1);

class Club
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function listar(array $filtros = [], int $limit = 10, int $offset = 0): array
    {
        $where = ["1 = 1"];
        $params = [];

        $estado = trim((string) ($filtros['estado'] ?? ''));
        $buscar = trim((string) ($filtros['buscar'] ?? ''));

        if ($estado !== '') {
            $where[] = "estado = :estado";
            $params[':estado'] = $estado;
        }

        if ($buscar !== '') {
            $where[] = "(nombre LIKE :buscar_nombre OR siglas LIKE :buscar_siglas)";
            $valor = '%' . $buscar . '%';
            $params[':buscar_nombre'] = $valor;
            $params[':buscar_siglas'] = $valor;
        }

        $sql = "SELECT id, nombre, siglas, logo, estado, created_at
                FROM tb_clubes
                WHERE " . implode(' AND ', $where) . "
                ORDER BY nombre ASC
                LIMIT :limit OFFSET :offset";

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
        $buscar = trim((string) ($filtros['buscar'] ?? ''));

        if ($estado !== '') {
            $where[] = "estado = :estado";
            $params[':estado'] = $estado;
        }

        if ($buscar !== '') {
            $where[] = "(nombre LIKE :buscar_nombre OR siglas LIKE :buscar_siglas)";
            $valor = '%' . $buscar . '%';
            $params[':buscar_nombre'] = $valor;
            $params[':buscar_siglas'] = $valor;
        }

        $sql = "SELECT COUNT(*) FROM tb_clubes";
        if ($where) {
            $sql .= " WHERE " . implode(' AND ', $where);
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
            FROM tb_clubes
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
            SELECT id, nombre, siglas, logo, estado, observaciones, created_at, updated_at
            FROM tb_clubes
            WHERE id = :id
            LIMIT 1
        ");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $club = $stmt->fetch(PDO::FETCH_ASSOC);

        return $club ?: null;
    }

    public function crear(array $data): int
    {
        $sql = "INSERT INTO tb_clubes (nombre, siglas, logo, estado, observaciones)
                VALUES (:nombre, :siglas, :logo, :estado, :observaciones)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':nombre' => trim($data['nombre']),
            ':siglas' => ($data['siglas'] ?? '') !== '' ? trim($data['siglas']) : null,
            ':logo' => $data['logo'] ?? null,
            ':estado' => 'activo',
            ':observaciones' => ($data['observaciones'] ?? '') !== '' ? trim($data['observaciones']) : null
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function actualizar(int $id, array $data): bool
    {
        $sql = "UPDATE tb_clubes
                SET nombre = :nombre,
                    siglas = :siglas,
                    logo = :logo,
                    estado = :estado,
                    observaciones = :observaciones
                WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':nombre' => trim((string) $data['nombre']),
            ':siglas' => ($data['siglas'] ?? '') !== '' ? trim((string) $data['siglas']) : null,
            ':logo' => $data['logo'] ?? null,
            ':estado' => $data['estado'],
            ':observaciones' => ($data['observaciones'] ?? '') !== '' ? trim((string) $data['observaciones']) : null,
            ':id' => $id
        ]);
    }

}
