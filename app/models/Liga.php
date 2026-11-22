<?php

declare(strict_types=1);

class Liga
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Lista todas las ligas.
     *
     * $scopeIds:
     * - null = global
     * - []   = ninguna
     * - [ids] = ligas autorizadas
     */
    
    public function listar(
        array $filtros = [],
        ?array $scopeIds = null,
        int $limit = 10,
        int $offset = 0
    ): array {
        $where = ["1 = 1"];
        $params = [];

        $estado = trim((string) ($filtros['estado'] ?? ''));
        $buscar = trim((string) ($filtros['buscar'] ?? ''));

        if ($estado !== '') {
            $where[] = "l.estado = :estado";
            $params[':estado'] = $estado;
        }

        if ($buscar !== '') 
        {
            $where[] = "
            (
                l.nombre LIKE :buscar_nombre
                OR l.siglas LIKE :buscar_siglas
                OR l.municipio LIKE :buscar_municipio
            )
            ";

            $valorBuscar = '%' . $buscar . '%';

            $params[':buscar_nombre'] = $valorBuscar;
            $params[':buscar_siglas'] = $valorBuscar;
            $params[':buscar_municipio'] = $valorBuscar;
        }

        /*
         * null = alcance global.
         */
        if ($scopeIds !== null) {

            /*
             * [] = ningún alcance.
             */
            if ($scopeIds === []) {
                $where[] = "1 = 0";

            } else {

                $placeholders = [];

                foreach ($scopeIds as $index => $id) {
                    $placeholder = ":scope_$index";
                    $placeholders[] = $placeholder;
                    $params[$placeholder] = (int) $id;
                }

                $where[] =
                    "l.id IN (" .
                    implode(', ', $placeholders) .
                    ")";
            }
        }

        $whereSql = implode(' AND ', $where);

        $sql = "
            SELECT
                l.id,
                l.nombre,
                l.siglas,
                l.estado,
                l.logo,
                l.correo,
                l.telefono,
                l.municipio,
                l.estado_republica,

                (
                    SELECT COUNT(*)
                    FROM tb_liga_organismos lo
                    WHERE lo.liga_id = l.id
                      AND lo.estado = 'activo'
                ) AS organismos_relacionados

            FROM tb_ligas l

            WHERE {$whereSql}

            ORDER BY l.nombre ASC

            LIMIT :limit
            OFFSET :offset
        ";

        $params[':limit'] = $limit;
        $params[':offset'] = $offset;

        $stmt = $this->pdo->prepare($sql);

        foreach ($params as $key => $value) {

            if ($key === ':limit' || $key === ':offset') {
                $stmt->bindValue($key, $value, PDO::PARAM_INT);
            } elseif (is_int($value)) {
                $stmt->bindValue($key, $value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($key, $value);
            }
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cuenta ligas respetando filtros y alcance.
     */
    public function contar(
        array $filtros = [],
        ?array $scopeIds = null
    ): int {
        $where = ["1 = 1"];
        $params = [];

        $estado = trim((string) ($filtros['estado'] ?? ''));
        $buscar = trim((string) ($filtros['buscar'] ?? ''));

        if ($estado !== '') {
            $where[] = "l.estado = :estado";
            $params[':estado'] = $estado;
        }

        if ($buscar !== '') {
            $where[] = "
                (
                    l.nombre LIKE :buscar_nombre
                    OR l.siglas LIKE :buscar_siglas
                    OR l.municipio LIKE :buscar_municipio
                )
            ";

            $valorBuscar = '%' . $buscar . '%';

            $params[':buscar_nombre'] = $valorBuscar;
            $params[':buscar_siglas'] = $valorBuscar;
            $params[':buscar_municipio'] = $valorBuscar;
        }

        if ($scopeIds !== null) {

            if ($scopeIds === []) {
                return 0;
            }

            $placeholders = [];

            foreach ($scopeIds as $index => $id) {
                $placeholder = ":scope_$index";
                $placeholders[] = $placeholder;
                $params[$placeholder] = (int) $id;
            }

            $where[] =
                "l.id IN (" .
                implode(', ', $placeholders) .
                ")";
        }

        $whereSql = implode(' AND ', $where);

        $sql = "
            SELECT COUNT(*)
            FROM tb_ligas l
            WHERE {$whereSql}
        ";

        $stmt = $this->pdo->prepare($sql);

        foreach ($params as $key => $value) {

            if (is_int($value)) {
                $stmt->bindValue($key, $value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($key, $value);
            }
        }

        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    /**
     * Obtiene una liga respetando el alcance.
     */
    public function getById(
        int $id,
        ?array $scopeIds = null
    ): ?array {

        if ($scopeIds !== null) {

            if ($scopeIds === []) {
                return null;
            }

            if (!in_array($id, $scopeIds, true)) {
                return null;
            }
        }

        $sql = "
            SELECT
                l.id,
                l.nombre,
                l.siglas,
                l.estado,
                l.logo,
                l.correo,
                l.telefono,
                l.direccion,
                l.colonia,
                l.municipio,
                l.estado_republica,
                l.codigo_postal,
                l.latitud,
                l.longitud,
                l.redes_sociales,
                l.notas,
                l.created_at
            FROM tb_ligas l
            WHERE l.id = :id
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':id' => $id
        ]);

        $liga = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$liga) {
            return null;
        }

        $stmtOrganismos = $this->pdo->prepare("
            SELECT
                o.id,
                o.nombre,
                o.siglas,
                lo.tipo_relacion,
                lo.es_gestor,
                lo.estado
            FROM tb_liga_organismos lo
            INNER JOIN tb_organismos o
                ON o.id = lo.organismo_id
            WHERE lo.liga_id = :liga_id
              AND lo.estado = 'activo'
            ORDER BY o.nombre ASC
        ");

        $stmtOrganismos->execute([
            ':liga_id' => $id
        ]);

        $liga['organismos'] =
            $stmtOrganismos->fetchAll(PDO::FETCH_ASSOC);

        return $liga;
    }

    /**
     * Compatibilidad para consultas generales existentes.
     */
    public function all(): array
    {
        return $this->listar([], null, 1000, 0);
    }

    /**
     * Métricas básicas del módulo.
     */
    public function metricas(?array $scopeIds = null): array
    {
        if ($scopeIds === []) {
            return [
                'total' => 0,
                'activas' => 0,
                'inactivas' => 0
            ];
        }

        $where = '';
        $params = [];

        if ($scopeIds !== null) {

            $placeholders = [];

            foreach ($scopeIds as $index => $id) {
                $placeholder = ":m_scope_$index";
                $placeholders[] = $placeholder;
                $params[$placeholder] = (int) $id;
            }

            $where = "
                WHERE l.id IN (" .
                implode(', ', $placeholders) .
                ")
            ";
        }

        $sql = "
            SELECT
                COUNT(*) AS total,
                SUM(l.estado = 'activo') AS activas,
                SUM(l.estado <> 'activo') AS inactivas
            FROM tb_ligas l
            {$where}
        ";

        $stmt = $this->pdo->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_INT);
        }

        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return [
            'total'     => (int) ($row['total'] ?? 0),
            'activas'   => (int) ($row['activas'] ?? 0),
            'inactivas' => (int) ($row['inactivas'] ?? 0)
        ];
    }
}