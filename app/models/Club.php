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

    public function obtenerEquipos(int $clubId): array
    {
        $sql = "
            SELECT
                e.id,
                e.nombre,
                e.siglas,
                e.escudo,
                ce.fecha_inicio,
                ce.fecha_fin,
                ce.estado AS estado_relacion,
                ce.observaciones
            FROM tb_club_equipos ce
            INNER JOIN tb_equipos e ON e.id = ce.equipo_id
            WHERE ce.club_id = :club_id
            ORDER BY ce.fecha_inicio DESC, ce.id DESC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':club_id' => $clubId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Equipos que todavía no tienen una relación vigente con ningún club.
     */
    public function listarEquiposDisponibles(): array
    {
        $sql = "
            SELECT e.id, e.nombre, e.siglas, e.escudo
            FROM tb_equipos e
            WHERE NOT EXISTS (
                SELECT 1
                FROM tb_club_equipos ce
                WHERE ce.equipo_id = e.id
                  AND ce.estado = 'activo'
                  AND ce.fecha_fin IS NULL
            )
            ORDER BY e.nombre ASC
        ";

        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Asigna un equipo a un club sin permitir dos relaciones vigentes.
     * La fila del equipo se bloquea para evitar asignaciones simultáneas.
     */
    public function asignarEquipo(
        int $clubId,
        int $equipoId,
        string $fechaInicio,
        ?string $observaciones = null
    ): int {
        $fecha = DateTimeImmutable::createFromFormat('!Y-m-d', $fechaInicio);
        if (!$fecha || $fecha->format('Y-m-d') !== $fechaInicio) {
            throw new InvalidArgumentException('La fecha de inicio no es válida.');
        }

        $this->pdo->beginTransaction();

        try {
            $clubStmt = $this->pdo->prepare("
                SELECT estado
                FROM tb_clubes
                WHERE id = :club_id
                LIMIT 1
            ");
            $clubStmt->execute([':club_id' => $clubId]);
            $club = $clubStmt->fetch(PDO::FETCH_ASSOC);

            if (!$club) {
                throw new RuntimeException('El club seleccionado no existe.');
            }

            if (strtolower((string) $club['estado']) !== 'activo') {
                throw new RuntimeException('No se pueden asignar equipos a un club inactivo.');
            }

            $equipoStmt = $this->pdo->prepare("
                SELECT id
                FROM tb_equipos
                WHERE id = :equipo_id
                LIMIT 1
                FOR UPDATE
            ");
            $equipoStmt->execute([':equipo_id' => $equipoId]);

            if (!$equipoStmt->fetch(PDO::FETCH_ASSOC)) {
                throw new RuntimeException('El equipo seleccionado no existe.');
            }

            $relacionStmt = $this->pdo->prepare("
                SELECT id, fecha_inicio
                FROM tb_club_equipos
                WHERE equipo_id = :equipo_id
                  AND estado = 'activo'
                  AND fecha_fin IS NULL
                LIMIT 1
            ");
            $relacionStmt->execute([':equipo_id' => $equipoId]);

            if ($relacionStmt->fetch(PDO::FETCH_ASSOC)) {
                throw new RuntimeException('Este equipo ya pertenece actualmente a un club. Primero debe cerrarse su relación vigente.');
            }

            $historialStmt = $this->pdo->prepare("
                SELECT MAX(fecha_fin)
                FROM tb_club_equipos
                WHERE equipo_id = :equipo_id
                  AND fecha_fin IS NOT NULL
            ");
            $historialStmt->execute([':equipo_id' => $equipoId]);
            $ultimaFechaFin = $historialStmt->fetchColumn();

            if ($ultimaFechaFin && $fechaInicio <= $ultimaFechaFin) {
                throw new RuntimeException('La fecha de inicio debe ser posterior al cierre de la relación anterior.');
            }

            $insertStmt = $this->pdo->prepare("
                INSERT INTO tb_club_equipos (
                    club_id,
                    equipo_id,
                    fecha_inicio,
                    fecha_fin,
                    estado,
                    observaciones
                ) VALUES (
                    :club_id,
                    :equipo_id,
                    :fecha_inicio,
                    NULL,
                    'activo',
                    :observaciones
                )
            ");
            $insertStmt->execute([
                ':club_id' => $clubId,
                ':equipo_id' => $equipoId,
                ':fecha_inicio' => $fechaInicio,
                ':observaciones' => $observaciones !== null && trim($observaciones) !== ''
                    ? trim($observaciones)
                    : null
            ]);

            $relacionId = (int) $this->pdo->lastInsertId();
            $this->pdo->commit();

            return $relacionId;
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $e;
        }
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

        $actualizado = $stmt->execute([
            ':nombre' => trim((string) $data['nombre']),
            ':siglas' => ($data['siglas'] ?? '') !== '' ? trim((string) $data['siglas']) : null,
            ':logo' => $data['logo'] ?? null,
            ':estado' => $data['estado'],
            ':observaciones' => ($data['observaciones'] ?? '') !== '' ? trim((string) $data['observaciones']) : null,
            ':id' => $id
        ]);

        if (!$actualizado) {
            throw new RuntimeException('No fue posible actualizar el club en la base de datos.');
        }

        return true;
    }

}
