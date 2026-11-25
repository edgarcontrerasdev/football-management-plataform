<?php

declare(strict_types=1);

class Temporada
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function listarPropietarios(): array
    {
        $organismos = $this->pdo->query("
            SELECT id, nombre, siglas
            FROM tb_organismos
            WHERE estado = 'activo'
            ORDER BY nombre
        ")->fetchAll(PDO::FETCH_ASSOC);

        $ligas = $this->pdo->query("
            SELECT l.id, l.nombre, l.siglas
            FROM tb_ligas l
            WHERE l.estado = 'activo'
              AND NOT EXISTS (
                  SELECT 1 FROM tb_liga_organismos lo
                  WHERE lo.liga_id = l.id
                    AND lo.es_gestor = 1
                    AND lo.estado = 'activo'
              )
            ORDER BY l.nombre
        ")->fetchAll(PDO::FETCH_ASSOC);

        return ['organismos' => $organismos, 'ligas' => $ligas];
    }

    public function getAll(): array
    {
        return $this->listar([], 1000, 0);
    }

    public function listarPorAmbito(string $tipo, int $propietarioId): array
    {
        if (!in_array($tipo, ['organismo', 'liga'], true) || $propietarioId <= 0) {
            return [];
        }
        $columna = $tipo === 'organismo' ? 'organismo_id' : 'liga_propietaria_id';
        $stmt = $this->pdo->prepare("
            SELECT id, temporada, fecha_inicio, fecha_fin, estado, es_actual
            FROM tb_temporadas
            WHERE ambito_tipo = :ambito_tipo AND {$columna} = :propietario_id
            ORDER BY fecha_inicio DESC, id DESC
        ");
        $stmt->execute([':ambito_tipo' => $tipo, ':propietario_id' => $propietarioId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /** Temporadas elegibles para vincular a una liga, excluyendo vínculos existentes. */
    public function listarDisponiblesParaLiga(int $ligaId, ?int $organismoGestorId): array
    {
        if ($ligaId <= 0) {
            return [];
        }

        $sql = $organismoGestorId !== null
            ? "SELECT t.id, t.temporada, t.fecha_inicio, t.fecha_fin, t.estado, t.es_actual
               FROM tb_temporadas t
               WHERE t.ambito_tipo = 'organismo' AND t.organismo_id = :propietario"
            : "SELECT t.id, t.temporada, t.fecha_inicio, t.fecha_fin, t.estado, t.es_actual
               FROM tb_temporadas t
               WHERE t.ambito_tipo = 'liga' AND t.liga_propietaria_id = :propietario";
        $sql .= " AND NOT EXISTS (SELECT 1 FROM tb_liga_temporadas lt
                   WHERE lt.liga_id = :liga_id AND lt.temporada_id = t.id)
                 ORDER BY t.fecha_inicio DESC, t.id DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':propietario' => $organismoGestorId ?? $ligaId, ':liga_id' => $ligaId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT t.*,
                   o.nombre AS organismo_nombre,
                   l.nombre AS liga_propietaria_nombre
            FROM tb_temporadas t
            LEFT JOIN tb_organismos o ON o.id = t.organismo_id
            LEFT JOIN tb_ligas l ON l.id = t.liga_propietaria_id
            WHERE t.id = :id
        ");
        $stmt->execute([':id' => (int) $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getActiva(?string $tipo = null, ?int $propietarioId = null): ?array
    {
        $sql = "SELECT * FROM tb_temporadas WHERE es_actual = 1";
        $params = [];

        if ($tipo !== null && $propietarioId !== null) {
            $sql .= $tipo === 'organismo'
                ? " AND ambito_tipo = 'organismo' AND organismo_id = :propietario"
                : " AND ambito_tipo = 'liga' AND liga_propietaria_id = :propietario";
            $params[':propietario'] = $propietarioId;
        }

        $sql .= " ORDER BY id DESC LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getByEstado($estado): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM tb_temporadas WHERE estado = :estado");
        $stmt->execute([':estado' => $estado]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crear(array $data): bool
    {
        $tipo = $data['ambito_tipo'] ?? 'organismo';
        $organismoId = $tipo === 'organismo' ? (int) ($data['organismo_id'] ?? 0) : null;
        $ligaId = $tipo === 'liga' ? (int) ($data['liga_propietaria_id'] ?? 0) : null;

        if (!in_array($tipo, ['organismo', 'liga'], true)
            || ($tipo === 'organismo' && $organismoId <= 0)
            || ($tipo === 'liga' && $ligaId <= 0)) {
            throw new InvalidArgumentException('Selecciona un propietario válido para la temporada.');
        }

        $stmt = $this->pdo->prepare("
            INSERT INTO tb_temporadas (
                ambito_tipo, organismo_id, liga_propietaria_id,
                temporada, fecha_inicio, fecha_fin, estado, es_actual,
                flexibilidad_fin, observaciones
            ) VALUES (
                :ambito_tipo, :organismo_id, :liga_propietaria_id,
                :temporada, :fecha_inicio, :fecha_fin, 'borrador', 0,
                :flexibilidad_fin, :observaciones
            )
        ");

        return $stmt->execute([
            ':ambito_tipo' => $tipo,
            ':organismo_id' => $organismoId ?: null,
            ':liga_propietaria_id' => $ligaId ?: null,
            ':temporada' => trim((string) $data['temporada']),
            ':fecha_inicio' => $data['fecha_inicio'],
            ':fecha_fin' => $data['fecha_fin'],
            ':flexibilidad_fin' => (int) ($data['flexibilidad_fin'] ?? 0),
            ':observaciones' => $data['observaciones'] ?? null
        ]);
    }

    public function actualizar($id, array $data): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE tb_temporadas
            SET temporada = :temporada,
                fecha_inicio = :fecha_inicio,
                fecha_fin = :fecha_fin,
                observaciones = :observaciones
            WHERE id = :id
              AND estado IN ('borrador', 'planeada')
        ");

        return $stmt->execute([
            ':temporada' => trim((string) $data['temporada']),
            ':fecha_inicio' => $data['fecha_inicio'],
            ':fecha_fin' => $data['fecha_fin'],
            ':observaciones' => $data['observaciones'] ?? null,
            ':id' => (int) $id
        ]);
    }

    public function eliminar($id): bool
    {
        $stmt = $this->pdo->prepare("
            DELETE FROM tb_temporadas
            WHERE id = :id AND estado IN ('borrador', 'planeada')
        ");
        $stmt->execute([':id' => (int) $id]);
        return $stmt->rowCount() > 0;
    }

    public function activar($id): bool
    {
        $this->pdo->beginTransaction();

        try {
            $lock = $this->pdo->prepare("
                SELECT ambito_tipo, organismo_id, liga_propietaria_id, estado
                FROM tb_temporadas
                WHERE id = :id
                FOR UPDATE
            ");
            $lock->execute([':id' => (int) $id]);
            $temporada = $lock->fetch(PDO::FETCH_ASSOC);

            if (!$temporada || !in_array($temporada['estado'], ['borrador', 'planeada'], true)) {
                throw new InvalidArgumentException('Solo se pueden activar temporadas planeadas.');
            }

            $where = $temporada['ambito_tipo'] === 'organismo'
                ? "ambito_tipo = 'organismo' AND organismo_id = :propietario"
                : "ambito_tipo = 'liga' AND liga_propietaria_id = :propietario";

            $propietarioId = $temporada['ambito_tipo'] === 'organismo'
                ? (int) $temporada['organismo_id']
                : (int) $temporada['liga_propietaria_id'];

            $finalizar = $this->pdo->prepare("
                UPDATE tb_temporadas
                SET estado = 'finalizada', es_actual = 0
                WHERE {$where} AND es_actual = 1
            ");
            $finalizar->execute([':propietario' => $propietarioId]);

            $activar = $this->pdo->prepare("
                UPDATE tb_temporadas
                SET estado = 'activa', es_actual = 1
                WHERE id = :id
            ");
            $activar->execute([':id' => (int) $id]);

            $this->pdo->commit();
            return true;
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }

    public function cerrar($id): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE tb_temporadas
            SET estado = 'cerrada', es_actual = 0
            WHERE id = :id AND estado = 'finalizada'
        ");
        $stmt->execute([':id' => (int) $id]);
        return $stmt->rowCount() > 0;
    }

    public function finalizarTemporada($id): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE tb_temporadas
            SET estado = 'finalizada', es_actual = 0
            WHERE id = :id AND estado = 'activa'
        ");
        $stmt->execute([':id' => (int) $id]);
        return $stmt->rowCount() > 0;
    }

    public function existeNombre($nombre, $ignoredId = null, ?string $tipo = null, ?int $propietarioId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM tb_temporadas WHERE temporada = :temporada";
        $params = [':temporada' => trim((string) $nombre)];

        if ($ignoredId !== null) {
            $sql .= " AND id <> :id";
            $params[':id'] = (int) $ignoredId;
        }

        if ($tipo === 'organismo' && $propietarioId !== null) {
            $sql .= " AND ambito_tipo = 'organismo' AND organismo_id = :propietario";
            $params[':propietario'] = $propietarioId;
        } elseif ($tipo === 'liga' && $propietarioId !== null) {
            $sql .= " AND ambito_tipo = 'liga' AND liga_propietaria_id = :propietario";
            $params[':propietario'] = $propietarioId;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function listar(array $filtros = [], int $limit = 10, int $offset = 0): array
    {
        $where = ['1 = 1'];
        $params = [];

        if (array_key_exists('ids', $filtros)) {
            $ids = array_values(array_unique(array_filter(array_map('intval', (array) $filtros['ids']), fn($id) => $id > 0)));
            if (!$ids) {
                $where[] = '1 = 0';
            } else {
                $placeholders = [];
                foreach ($ids as $i => $id) {
                    $key = ':scope_temporada_' . $i;
                    $placeholders[] = $key;
                    $params[$key] = $id;
                }
                $where[] = 't.id IN (' . implode(',', $placeholders) . ')';
            }
        }

        if (!empty($filtros['estado'])) {
            $where[] = 't.estado = :estado';
            $params[':estado'] = $filtros['estado'];
        }
        if (!empty($filtros['buscar'])) {
            $where[] = 't.temporada LIKE :buscar';
            $params[':buscar'] = '%' . $filtros['buscar'] . '%';
        }
        if (!empty($filtros['ambito_tipo'])) {
            $where[] = 't.ambito_tipo = :ambito_tipo';
            $params[':ambito_tipo'] = $filtros['ambito_tipo'];
        }
        if (!empty($filtros['propietario_id'])) {
            $column = ($filtros['ambito_tipo'] ?? '') === 'liga' ? 't.liga_propietaria_id' : 't.organismo_id';
            $where[] = $column . ' = :propietario_id';
            $params[':propietario_id'] = (int) $filtros['propietario_id'];
        }

        $sql = "
            SELECT t.*,
                   COALESCE(o.nombre, l.nombre) AS propietario_nombre
            FROM tb_temporadas t
            LEFT JOIN tb_organismos o ON o.id = t.organismo_id
            LEFT JOIN tb_ligas l ON l.id = t.liga_propietaria_id
            WHERE " . implode(' AND ', $where) . "
            ORDER BY t.fecha_inicio DESC, t.id DESC
            LIMIT :limit OFFSET :offset
        ";
        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function contar(array $filtros = []): int
    {
        $where = ['1 = 1'];
        $params = [];

        if (array_key_exists('ids', $filtros)) {
            $ids = array_values(array_unique(array_filter(array_map('intval', (array) $filtros['ids']), fn($id) => $id > 0)));
            if (!$ids) {
                $where[] = '1 = 0';
            } else {
                $placeholders = [];
                foreach ($ids as $i => $id) {
                    $key = ':scope_temporada_' . $i;
                    $placeholders[] = $key;
                    $params[$key] = $id;
                }
                $where[] = 'id IN (' . implode(',', $placeholders) . ')';
            }
        }

        if (!empty($filtros['estado'])) {
            $where[] = 'estado = :estado';
            $params[':estado'] = $filtros['estado'];
        }
        if (!empty($filtros['buscar'])) {
            $where[] = 'temporada LIKE :buscar';
            $params[':buscar'] = '%' . $filtros['buscar'] . '%';
        }
        if (!empty($filtros['ambito_tipo'])) {
            $where[] = 'ambito_tipo = :ambito_tipo';
            $params[':ambito_tipo'] = $filtros['ambito_tipo'];
        }
        if (!empty($filtros['propietario_id'])) {
            $column = ($filtros['ambito_tipo'] ?? '') === 'liga' ? 'liga_propietaria_id' : 'organismo_id';
            $where[] = $column . ' = :propietario_id';
            $params[':propietario_id'] = (int) $filtros['propietario_id'];
        }

        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM tb_temporadas WHERE " . implode(' AND ', $where));
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }
}
