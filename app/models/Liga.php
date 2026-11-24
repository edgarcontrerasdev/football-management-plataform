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
    

    /**
     * Comprueba si ya existe una liga con el mismo nombre.
     */
    public function existeNombre(string $nombre): bool
    {
        $stmt = $this->pdo->prepare("
            SELECT 1
            FROM tb_ligas
            WHERE LOWER(TRIM(nombre)) = LOWER(TRIM(:nombre))
            LIMIT 1
        ");
        $stmt->execute([':nombre' => $nombre]);
        return (bool) $stmt->fetchColumn();
    }

    /**
     * Comprueba si el nombre ya está registrado en otra liga.
     */
    public function existeOtroNombre(string $nombre, int $excluirId): bool
    {
        $stmt = $this->pdo->prepare("
            SELECT 1
            FROM tb_ligas
            WHERE LOWER(TRIM(nombre)) = LOWER(TRIM(:nombre))
              AND id <> :id
            LIMIT 1
        ");
        $stmt->execute([':nombre' => $nombre, ':id' => $excluirId]);
        return (bool) $stmt->fetchColumn();
    }

    /**
     * Actualiza los datos generales de una liga sin alterar sus relaciones.
     */
    public function actualizar(int $id, array $data): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE tb_ligas
            SET nombre = :nombre,
                siglas = :siglas,
                estado = :estado,
                logo = :logo,
                correo = :correo,
                telefono = :telefono,
                direccion = :direccion,
                colonia = :colonia,
                municipio = :municipio,
                estado_republica = :estado_republica,
                codigo_postal = :codigo_postal,
                redes_sociales = :redes_sociales,
                notas = :notas
            WHERE id = :id
            LIMIT 1
        ");

        return $stmt->execute([
            ':nombre' => trim((string) $data['nombre']),
            ':siglas' => ($data['siglas'] ?? '') !== '' ? trim((string) $data['siglas']) : null,
            ':estado' => $data['estado'] ?? 'activo',
            ':logo' => ($data['logo'] ?? '') !== '' ? trim((string) $data['logo']) : null,
            ':correo' => ($data['correo'] ?? '') !== '' ? trim((string) $data['correo']) : null,
            ':telefono' => ($data['telefono'] ?? '') !== '' ? trim((string) $data['telefono']) : null,
            ':direccion' => ($data['direccion'] ?? '') !== '' ? trim((string) $data['direccion']) : null,
            ':colonia' => ($data['colonia'] ?? '') !== '' ? trim((string) $data['colonia']) : null,
            ':municipio' => ($data['municipio'] ?? '') !== '' ? trim((string) $data['municipio']) : null,
            ':estado_republica' => ($data['estado_republica'] ?? '') !== '' ? trim((string) $data['estado_republica']) : null,
            ':codigo_postal' => ($data['codigo_postal'] ?? '') !== '' ? trim((string) $data['codigo_postal']) : null,
            ':redes_sociales' => ($data['redes_sociales'] ?? '') !== '' ? trim((string) $data['redes_sociales']) : null,
            ':notas' => ($data['notas'] ?? '') !== '' ? trim((string) $data['notas']) : null,
            ':id' => $id
        ]);
    }

    /**
     * Registra una liga independiente. Su organismo gestor podrá vincularse después.
     */
    public function crear(array $data): bool
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO tb_ligas (
                nombre, siglas, estado, logo, correo, telefono, direccion, colonia,
                municipio, estado_republica, codigo_postal, redes_sociales, notas
            ) VALUES (
                :nombre, :siglas, :estado, :logo, :correo, :telefono, :direccion, :colonia,
                :municipio, :estado_republica, :codigo_postal, :redes_sociales, :notas
            )
        ");

        return $stmt->execute([
            ':nombre' => trim((string) $data['nombre']),
            ':siglas' => ($data['siglas'] ?? '') !== '' ? trim((string) $data['siglas']) : null,
            ':estado' => $data['estado'] ?? 'activo',
            ':logo' => ($data['logo'] ?? '') !== '' ? trim((string) $data['logo']) : null,
            ':correo' => ($data['correo'] ?? '') !== '' ? trim((string) $data['correo']) : null,
            ':telefono' => ($data['telefono'] ?? '') !== '' ? trim((string) $data['telefono']) : null,
            ':direccion' => ($data['direccion'] ?? '') !== '' ? trim((string) $data['direccion']) : null,
            ':colonia' => ($data['colonia'] ?? '') !== '' ? trim((string) $data['colonia']) : null,
            ':municipio' => ($data['municipio'] ?? '') !== '' ? trim((string) $data['municipio']) : null,
            ':estado_republica' => ($data['estado_republica'] ?? '') !== '' ? trim((string) $data['estado_republica']) : null,
            ':codigo_postal' => ($data['codigo_postal'] ?? '') !== '' ? trim((string) $data['codigo_postal']) : null,
            ':redes_sociales' => ($data['redes_sociales'] ?? '') !== '' ? trim((string) $data['redes_sociales']) : null,
            ':notas' => ($data['notas'] ?? '') !== '' ? trim((string) $data['notas']) : null
        ]);
    }

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

    /**
     * Lista los equipos participantes de una liga en una temporada.
     */
    public function listarEquiposPorTemporada(int $ligaId, int $temporadaId): array
    {
        $sql = "
            SELECT
                le.id AS liga_equipo_id,
                le.equipo_id,
                COALESCE(NULLIF(et.nombre_mostrado, ''), e.nombre) AS nombre,
                COALESCE(NULLIF(et.siglas, ''), e.siglas) AS siglas,
                le.numero_registro,
                le.estado_participacion,
                le.estado_documentacion
            FROM tb_liga_temporadas lt
            INNER JOIN tb_liga_equipos le
                ON le.liga_temporada_id = lt.id
            INNER JOIN tb_equipos e
                ON e.id = le.equipo_id
            LEFT JOIN tb_equipo_temporadas et
                ON et.equipo_id = e.id
               AND et.temporada_id = lt.temporada_id
            WHERE lt.liga_id = :liga_id
              AND lt.temporada_id = :temporada_id
            ORDER BY nombre ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':liga_id' => $ligaId,
            ':temporada_id' => $temporadaId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene la relación de una liga con una temporada.
     */
    public function obtenerVinculacionTemporada(int $ligaId, int $temporadaId): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT id, liga_id, temporada_id, estado
            FROM tb_liga_temporadas
            WHERE liga_id = :liga_id AND temporada_id = :temporada_id
            LIMIT 1
        ");
        $stmt->execute([':liga_id' => $ligaId, ':temporada_id' => $temporadaId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Lista entidades equipo que todavía no están inscritas en esa temporada de liga.
     */
    public function listarEquiposDisponibles(int $ligaId, int $temporadaId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT e.id, e.nombre, e.siglas, e.escudo,
                   COALESCE(NULLIF(et.nombre_mostrado, ''), e.nombre) AS nombre_temporada,
                   COALESCE(NULLIF(et.siglas, ''), e.siglas) AS siglas_temporada
            FROM tb_equipos e
            LEFT JOIN tb_equipo_temporadas et
                ON et.equipo_id = e.id AND et.temporada_id = :temporada_join
            WHERE NOT EXISTS (
                SELECT 1
                FROM tb_liga_temporadas lt
                INNER JOIN tb_liga_equipos le ON le.liga_temporada_id = lt.id
                WHERE lt.liga_id = :liga_id
                  AND lt.temporada_id = :temporada_filter
                  AND le.equipo_id = e.id
            )
            ORDER BY nombre_temporada ASC
        ");
        $stmt->execute([
            ':temporada_join' => $temporadaId,
            ':liga_id' => $ligaId,
            ':temporada_filter' => $temporadaId
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Registra una solicitud de inscripción. Los estados quedan en pendiente por defecto.
     */
    public function inscribirEquipo(
        int $ligaId,
        int $temporadaId,
        int $equipoId,
        ?string $numeroRegistro = null
    ): bool {
        $stmt = $this->pdo->prepare("
            INSERT INTO tb_liga_equipos (
                liga_temporada_id, equipo_id, numero_registro, fecha_solicitud
            )
            SELECT lt.id, :equipo_id, :numero_registro, NOW()
            FROM tb_liga_temporadas lt
            WHERE lt.liga_id = :liga_id
              AND lt.temporada_id = :temporada_id
            LIMIT 1
        ");
        $stmt->execute([
            ':equipo_id' => $equipoId,
            ':numero_registro' => $numeroRegistro,
            ':liga_id' => $ligaId,
            ':temporada_id' => $temporadaId
        ]);
        return $stmt->rowCount() === 1;
    }


    /**
     * Resuelve una solicitud pendiente de inscripción, limitada a la liga indicada.
     */
    public function resolverInscripcionEquipo(int $ligaId, int $ligaEquipoId, string $estado): bool
    {
        if (!in_array($estado, ['activo', 'rechazado'], true)) {
            throw new InvalidArgumentException('Estado de resolución no válido.');
        }

        $stmt = $this->pdo->prepare("
            UPDATE tb_liga_equipos le
            INNER JOIN tb_liga_temporadas lt ON lt.id = le.liga_temporada_id
            SET le.estado_participacion = :estado,
                le.fecha_alta = CASE WHEN :estado_fecha = 'activo' THEN NOW() ELSE le.fecha_alta END
            WHERE le.id = :liga_equipo_id
              AND lt.liga_id = :liga_id
              AND le.estado_participacion = 'pendiente'
              AND lt.estado = 'activo'
        ");
        $stmt->execute([
            ':estado' => $estado,
            ':estado_fecha' => $estado,
            ':liga_equipo_id' => $ligaEquipoId,
            ':liga_id' => $ligaId
        ]);

        return $stmt->rowCount() === 1;
    }


    /**
     * Temporadas ya vinculadas a una liga.
     */
    public function listarTemporadasVinculadas(int $ligaId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT lt.id AS liga_temporada_id, lt.estado AS estado_vinculacion,
                   lt.fecha_solicitud, lt.fecha_alta, t.id AS temporada_id,
                   t.temporada, t.ambito_tipo, t.estado AS estado_temporada,
                   t.es_actual, t.fecha_inicio, t.fecha_fin
            FROM tb_liga_temporadas lt
            INNER JOIN tb_temporadas t ON t.id = lt.temporada_id
            WHERE lt.liga_id = :liga_id
            ORDER BY t.fecha_inicio DESC, t.id DESC
        ");
        $stmt->execute([':liga_id' => $ligaId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * Aprueba o rechaza una solicitud pendiente de vinculación de temporada.
     */
    public function resolverVinculacionTemporada(int $ligaId, int $ligaTemporadaId, string $estado): bool
    {
        if (!in_array($estado, ['activo', 'rechazado'], true)) {
            throw new InvalidArgumentException('Estado de vinculación no válido.');
        }

        $stmt = $this->pdo->prepare("
            UPDATE tb_liga_temporadas
            SET estado = :estado,
                fecha_alta = CASE WHEN :estado_fecha = 'activo' THEN NOW() ELSE fecha_alta END
            WHERE id = :liga_temporada_id
              AND liga_id = :liga_id
              AND estado = 'pendiente'
        ");
        $stmt->execute([
            ':estado' => $estado,
            ':estado_fecha' => $estado,
            ':liga_temporada_id' => $ligaTemporadaId,
            ':liga_id' => $ligaId
        ]);

        return $stmt->rowCount() === 1;
    }


    /**
     * Registra la solicitud de vinculación usando el estado predeterminado pendiente.
     */
    public function vincularTemporada(int $ligaId, int $temporadaId): bool
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO tb_liga_temporadas (liga_id, temporada_id, fecha_solicitud)
            VALUES (:liga_id, :temporada_id, NOW())
        ");
        return $stmt->execute([':liga_id' => $ligaId, ':temporada_id' => $temporadaId]);
    }
}