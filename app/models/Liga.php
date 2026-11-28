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
     * Actualiza datos generales y registra cada campo modificado en una transacción.
     */
    public function actualizar(int $id, array $data, int $usuarioId): bool
    {
        $campos = [
            'nombre' => 'Nombre',
            'siglas' => 'Siglas',
            'logo' => 'Logotipo',
            'correo' => 'Correo',
            'telefono' => 'Teléfono',
            'direccion' => 'Dirección',
            'colonia' => 'Colonia',
            'municipio' => 'Municipio',
            'estado_republica' => 'Estado',
            'codigo_postal' => 'Código postal',
            'redes_sociales' => 'Redes sociales',
            'notas' => 'Notas'
        ];

        $propia = !$this->pdo->inTransaction();
        if ($propia) $this->pdo->beginTransaction();

        try {
            $lock = $this->pdo->prepare("SELECT * FROM tb_ligas WHERE id = :id FOR UPDATE");
            $lock->execute([':id' => $id]);
            $anterior = $lock->fetch(PDO::FETCH_ASSOC);
            if (!$anterior) throw new RuntimeException('La liga no existe.');

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
            $stmt->execute([
                ':nombre' => trim((string) $data['nombre']),
                ':siglas' => ($data['siglas'] ?? '') !== '' ? trim((string) $data['siglas']) : null,
                ':estado' => $anterior['estado'],
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

            $historial = $this->pdo->prepare("
                INSERT INTO tb_liga_historial
                    (liga_id, usuario_id, accion, campo, valor_anterior, valor_nuevo, descripcion)
                VALUES (:liga_id, :usuario_id, 'actualizacion', :campo, :anterior, :nuevo, :descripcion)
            ");
            foreach ($campos as $campo => $etiqueta) {
                $valorNuevo = $data[$campo] ?? null;
                $valorAnterior = $anterior[$campo] ?? null;
                $valorNuevo = ($valorNuevo === '') ? null : $valorNuevo;
                if ((string) ($valorAnterior ?? '') === (string) ($valorNuevo ?? '')) continue;
                $historial->execute([
                    ':liga_id' => $id,
                    ':usuario_id' => $usuarioId,
                    ':campo' => $campo,
                    ':anterior' => $valorAnterior,
                    ':nuevo' => $valorNuevo,
                    ':descripcion' => 'Se modificó ' . $etiqueta . ' de la liga.'
                ]);
            }

            if ($propia) $this->pdo->commit();
            return true;
        } catch (Throwable $e) {
            if ($propia && $this->pdo->inTransaction()) $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Actualiza el estado y registra el cambio en el historial de forma atómica.
     */
    public function cambiarEstado(int $id, string $estado, int $usuarioId): bool
    {
        if (!in_array($estado, ['activo', 'inactivo', 'suspendido'], true)) {
            throw new InvalidArgumentException('Estado de liga no válido.');
        }

        $propia = !$this->pdo->inTransaction();
        if ($propia) $this->pdo->beginTransaction();
        try {
            $lock = $this->pdo->prepare("SELECT estado FROM tb_ligas WHERE id = :id FOR UPDATE");
            $lock->execute([':id' => $id]);
            $anterior = $lock->fetchColumn();
            if ($anterior === false) throw new RuntimeException('La liga no existe.');

            if ($anterior !== $estado) {
                $stmt = $this->pdo->prepare("UPDATE tb_ligas SET estado = :estado WHERE id = :id LIMIT 1");
                $stmt->execute([':estado' => $estado, ':id' => $id]);
                $historial = $this->pdo->prepare("
                    INSERT INTO tb_liga_historial
                        (liga_id, usuario_id, accion, campo, valor_anterior, valor_nuevo, descripcion)
                    VALUES (:liga_id, :usuario_id, 'cambio_estado', 'estado', :anterior, :nuevo, 'Se modificó el estado institucional de la liga.')
                ");
                $historial->execute([
                    ':liga_id' => $id,
                    ':usuario_id' => $usuarioId,
                    ':anterior' => $anterior,
                    ':nuevo' => $estado
                ]);
            }

            if ($propia) $this->pdo->commit();
            return true;
        } catch (Throwable $e) {
            if ($propia && $this->pdo->inTransaction()) $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Devuelve los movimientos recientes de una liga y el usuario responsable.
     */
    public function listarHistorial(int $ligaId, int $limite = 100): array
    {
        $stmt = $this->pdo->prepare("
            SELECT h.id, h.accion, h.campo, h.valor_anterior, h.valor_nuevo,
                   h.descripcion, h.created_at,
                   COALESCE(NULLIF(TRIM(CONCAT_WS(' ', a.nombres, a.apellido_paterno, a.apellido_materno)), ''), u.usuario) AS usuario_nombre,
                   r.nombre AS rol_nombre
            FROM tb_liga_historial h
            INNER JOIN tb_usuarios u ON u.id = h.usuario_id
            LEFT JOIN tb_afiliados a ON a.id = u.afiliado_id
            LEFT JOIN tb_roles r ON r.id = u.rol_id
            WHERE h.liga_id = :liga_id
            ORDER BY h.created_at DESC, h.id DESC
            LIMIT :limite
        ");
        $stmt->bindValue(':liga_id', $ligaId, PDO::PARAM_INT);
        $stmt->bindValue(':limite', max(1, min(200, $limite)), PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Registra una liga independiente. Su organismo gestor podrá vincularse después.
     */
    public function crear(array $data, ?int $organismoId = null, ?int $usuarioId = null): bool
    {
        if ($usuarioId === null || $usuarioId <= 0) {
            throw new RuntimeException('No se identificó al usuario que crea la liga.');
        }

        $inTransaction = $this->pdo->inTransaction();
        if (!$inTransaction) {
            $this->pdo->beginTransaction();
        }

        try {
        $stmt = $this->pdo->prepare("
            INSERT INTO tb_ligas (
                nombre, siglas, estado, logo, correo, telefono, direccion, colonia,
                municipio, estado_republica, codigo_postal, redes_sociales, notas
            ) VALUES (
                :nombre, :siglas, :estado, :logo, :correo, :telefono, :direccion, :colonia,
                :municipio, :estado_republica, :codigo_postal, :redes_sociales, :notas
            )
        ");

        $stmt->execute([
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

        $ligaId = (int) $this->pdo->lastInsertId();

        $historialCreacion = $this->pdo->prepare("
            INSERT INTO tb_liga_historial
                (liga_id, usuario_id, accion, campo, valor_anterior, valor_nuevo, descripcion)
            VALUES (:liga_id, :usuario_id, 'creacion', 'liga', NULL, :nombre, 'Se creó el registro institucional de la liga.')
        ");
        $historialCreacion->execute([
            ':liga_id' => $ligaId,
            ':usuario_id' => $usuarioId,
            ':nombre' => trim((string) $data['nombre'])
        ]);

        if ($organismoId !== null) {
            $relacion = $this->pdo->prepare("
                INSERT INTO tb_liga_organismos
                    (liga_id, organismo_id, tipo_relacion, es_gestor, fecha_inicio, estado)
                VALUES
                    (:liga_id, :organismo_id, 'gestor', 1, CURDATE(), 'activo')
            ");
            $relacion->execute([
                ':liga_id' => $ligaId,
                ':organismo_id' => $organismoId
            ]);

            $organismoStmt = $this->pdo->prepare("SELECT nombre FROM tb_organismos WHERE id = :id");
            $organismoStmt->execute([':id' => $organismoId]);
            $organismoNombre = (string) $organismoStmt->fetchColumn();
            $historial = $this->pdo->prepare("
                INSERT INTO tb_liga_historial
                    (liga_id, usuario_id, accion, campo, valor_anterior, valor_nuevo, descripcion)
                VALUES (:liga_id, :usuario_id, 'vinculacion_organismo', 'relacion_institucional', NULL, :nuevo, 'Liga creada y vinculada automáticamente a su organismo gestor.')
            ");
            $historial->execute([
                ':liga_id' => $ligaId,
                ':usuario_id' => $usuarioId,
                ':nuevo' => $organismoNombre . ' (gestor)'
            ]);
        }

        if (!$inTransaction) {
            $this->pdo->commit();
        }
        return true;
        } catch (Throwable $e) {
            if (!$inTransaction && $this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
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

    public function listarOrganismosDisponibles(int $ligaId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT o.id, o.nombre, o.siglas
            FROM tb_organismos o
            WHERE o.estado = 'activo'
              AND NOT EXISTS (
                  SELECT 1 FROM tb_liga_organismos lo
                  WHERE lo.liga_id = :liga_activa
                    AND lo.organismo_id = o.id
                    AND lo.estado = 'activo'
              )
              AND NOT EXISTS (
                  SELECT 1 FROM tb_liga_organismo_solicitudes s
                  WHERE s.liga_id = :liga_pendiente
                    AND s.organismo_id = o.id
                    AND s.estado = 'pendiente'
              )
            ORDER BY o.nombre ASC
        ");
        $stmt->execute([':liga_activa' => $ligaId, ':liga_pendiente' => $ligaId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarSolicitudesOrganismo(int $ligaId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT s.id, s.organismo_id, o.nombre AS organismo_nombre, o.siglas,
                   s.estado, s.fecha_solicitud, s.fecha_resolucion, s.respuesta
            FROM tb_liga_organismo_solicitudes s
            INNER JOIN tb_organismos o ON o.id = s.organismo_id
            WHERE s.liga_id = :liga_id
            ORDER BY s.fecha_solicitud DESC, s.id DESC
        ");
        $stmt->execute([':liga_id' => $ligaId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function solicitarVinculacionOrganismo(
        int $ligaId,
        int $organismoId,
        int $usuarioId,
        string $rol,
        string $observaciones = ''
    ): void {
        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare("
                SELECT nombre FROM tb_ligas WHERE id = :liga_id AND estado = 'activo' FOR UPDATE
            ");
            $stmt->execute([':liga_id' => $ligaId]);
            $ligaNombre = $stmt->fetchColumn();
            if (!$ligaNombre) {
                throw new InvalidArgumentException('La liga no existe o está inactiva.');
            }

            $org = $this->pdo->prepare("SELECT nombre FROM tb_organismos WHERE id = :id AND estado = 'activo'");
            $org->execute([':id' => $organismoId]);
            $organismoNombre = $org->fetchColumn();
            if (!$organismoNombre) {
                throw new InvalidArgumentException('El organismo seleccionado no existe o está inactivo.');
            }

            $check = $this->pdo->prepare("
                SELECT id FROM tb_liga_organismos
                WHERE liga_id = :liga_id AND organismo_id = :organismo_id AND estado = 'activo'
                LIMIT 1
            ");
            $check->execute([':liga_id' => $ligaId, ':organismo_id' => $organismoId]);
            if ($check->fetchColumn()) {
                throw new InvalidArgumentException('La liga ya está vinculada a ese organismo.');
            }

            $pending = $this->pdo->prepare("
                SELECT id FROM tb_liga_organismo_solicitudes
                WHERE liga_id = :liga_id AND organismo_id = :organismo_id AND estado = 'pendiente'
                LIMIT 1
            ");
            $pending->execute([':liga_id' => $ligaId, ':organismo_id' => $organismoId]);
            if ($pending->fetchColumn()) {
                throw new InvalidArgumentException('Ya existe una solicitud pendiente para ese organismo.');
            }

            $insert = $this->pdo->prepare("
                INSERT INTO tb_liga_organismo_solicitudes
                    (liga_id, organismo_id, solicitante_usuario_id, solicitante_rol, estado, observaciones)
                VALUES (:liga_id, :organismo_id, :usuario_id, :rol, 'pendiente', :observaciones)
            ");
            $insert->execute([
                ':liga_id' => $ligaId,
                ':organismo_id' => $organismoId,
                ':usuario_id' => $usuarioId,
                ':rol' => $rol,
                ':observaciones' => trim($observaciones) ?: null
            ]);

            $historial = $this->pdo->prepare("
                INSERT INTO tb_liga_historial
                    (liga_id, usuario_id, accion, campo, valor_anterior, valor_nuevo, descripcion)
                VALUES (:liga_id, :usuario_id, 'solicitud_vinculacion', 'relacion_institucional', NULL, :nuevo, :descripcion)
            ");
            $historial->execute([
                ':liga_id' => $ligaId,
                ':usuario_id' => $usuarioId,
                ':nuevo' => 'Solicitud enviada a: ' . $organismoNombre,
                ':descripcion' => 'Se envió una solicitud de vinculación institucional.'
            ]);

            $this->pdo->commit();
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            throw $e;
        }
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
                le.estado_documentacion,
                le.fecha_baja,
                le.motivo_baja
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
            WHERE e.liga_id = :liga_propietaria
              AND NOT EXISTS (
                SELECT 1
                FROM tb_liga_temporadas lt
                INNER JOIN tb_liga_equipos le ON le.liga_temporada_id = lt.id
                WHERE lt.liga_id = :liga_id
                  AND lt.temporada_id = :temporada_filter
                  AND lt.estado = 'activo'
                  AND le.equipo_id = e.id
            )
            ORDER BY nombre_temporada ASC
        ");
        $stmt->execute([
            ':temporada_join' => $temporadaId,
            ':liga_propietaria' => $ligaId,
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
        ?string $numeroRegistro = null,
        int $usuarioId = 0
    ): bool {
        $this->pdo->beginTransaction();
        try {
            $equipo = $this->pdo->prepare("
                SELECT nombre
                FROM tb_equipos
                WHERE id = :id AND liga_id = :liga_id
                LIMIT 1
            ");
            $equipo->execute([':id' => $equipoId, ':liga_id' => $ligaId]);
            $equipoNombre = $equipo->fetchColumn();
            $temporada = $this->pdo->prepare("SELECT temporada FROM tb_temporadas WHERE id = :id");
            $temporada->execute([':id' => $temporadaId]);
            $temporadaNombre = $temporada->fetchColumn();
            if (!$equipoNombre || !$temporadaNombre) {
                throw new InvalidArgumentException('El equipo o la temporada no existe.');
            }

            $stmt = $this->pdo->prepare("
                INSERT INTO tb_liga_equipos (
                    liga_temporada_id, equipo_id, numero_registro, fecha_solicitud
                )
                SELECT lt.id, :equipo_id, :numero_registro, NOW()
                FROM tb_liga_temporadas lt
                WHERE lt.liga_id = :liga_id
                  AND lt.temporada_id = :temporada_id
                  AND lt.estado = 'activo'
                LIMIT 1
            ");
            $stmt->execute([
                ':equipo_id' => $equipoId,
                ':numero_registro' => $numeroRegistro,
                ':liga_id' => $ligaId,
                ':temporada_id' => $temporadaId
            ]);
            if ($stmt->rowCount() !== 1) {
                $this->pdo->rollBack();
                return false;
            }

            $historial = $this->pdo->prepare("
                INSERT INTO tb_liga_historial
                    (liga_id, usuario_id, accion, campo, valor_anterior, valor_nuevo, descripcion)
                VALUES (:liga_id, :usuario_id, 'solicitud_inscripcion_equipo', 'equipo_temporada', NULL, :nuevo, :descripcion)
            ");
            $historial->execute([
                ':liga_id' => $ligaId,
                ':usuario_id' => $usuarioId,
                ':nuevo' => $equipoNombre . ' / ' . $temporadaNombre,
                ':descripcion' => 'Se solicitó inscribir un equipo en una temporada de la liga.'
            ]);
            $this->pdo->commit();
            return true;
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            throw $e;
        }
    }


    /**
     * Resuelve una solicitud pendiente de inscripción, limitada a la liga indicada.
     */
    public function resolverInscripcionEquipo(int $ligaId, int $ligaEquipoId, string $estado, int $usuarioId): bool
    {
        if (!in_array($estado, ['activo', 'rechazado'], true)) {
            throw new InvalidArgumentException('Estado de resolución no válido.');
        }

        $this->pdo->beginTransaction();
        try {
            $consulta = $this->pdo->prepare("
                SELECT e.nombre AS equipo, t.temporada
                FROM tb_liga_equipos le
                INNER JOIN tb_liga_temporadas lt ON lt.id = le.liga_temporada_id
                INNER JOIN tb_equipos e ON e.id = le.equipo_id
                INNER JOIN tb_temporadas t ON t.id = lt.temporada_id
                WHERE le.id = :id AND lt.liga_id = :liga_id
                  AND e.liga_id = :liga_propietaria
                  AND le.estado_participacion = 'pendiente' AND lt.estado = 'activo'
                FOR UPDATE
            ");
            $consulta->execute([
                ':id' => $ligaEquipoId,
                ':liga_id' => $ligaId,
                ':liga_propietaria' => $ligaId
            ]);
            $registro = $consulta->fetch(PDO::FETCH_ASSOC);
            if (!$registro) {
                $this->pdo->rollBack();
                return false;
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
            if ($stmt->rowCount() !== 1) {
                $this->pdo->rollBack();
                return false;
            }

            $accion = $estado === 'activo' ? 'aprobacion_inscripcion_equipo' : 'rechazo_inscripcion_equipo';
            $historial = $this->pdo->prepare("
                INSERT INTO tb_liga_historial
                    (liga_id, usuario_id, accion, campo, valor_anterior, valor_nuevo, descripcion)
                VALUES (:liga_id, :usuario_id, :accion, 'equipo_temporada', 'Solicitud pendiente', :nuevo, :descripcion)
            ");
            $historial->execute([
                ':liga_id' => $ligaId,
                ':usuario_id' => $usuarioId,
                ':accion' => $accion,
                ':nuevo' => $registro['equipo'] . ' / ' . $registro['temporada'],
                ':descripcion' => $estado === 'activo'
                    ? 'Se aprobó la inscripción del equipo en la temporada.'
                    : 'Se rechazó la solicitud de inscripción del equipo.'
            ]);
            $this->pdo->commit();
            return true;
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            throw $e;
        }
    }


    /**
     * Cambia la participación de un equipo o registra su baja de una temporada.
     * Cada transición queda auditada en el historial de la liga.
     */
    public function cambiarParticipacionEquipo(
        int $ligaId,
        int $ligaEquipoId,
        string $estadoNuevo,
        string $motivo,
        int $usuarioId
    ): bool {
        if (!in_array($estadoNuevo, ['activo', 'suspendido', 'baja'], true)) {
            throw new InvalidArgumentException('Estado de participación no válido.');
        }
        if ($usuarioId <= 0) {
            throw new InvalidArgumentException('No se identificó al usuario responsable.');
        }
        $motivo = trim($motivo);
        if ($estadoNuevo === 'baja' && $motivo === '') {
            throw new InvalidArgumentException('El motivo de baja es obligatorio.');
        }

        $this->pdo->beginTransaction();
        try {
            $consulta = $this->pdo->prepare("
                SELECT le.estado_participacion, e.nombre AS equipo, t.temporada
                FROM tb_liga_equipos le
                INNER JOIN tb_liga_temporadas lt ON lt.id = le.liga_temporada_id
                INNER JOIN tb_equipos e ON e.id = le.equipo_id
                INNER JOIN tb_temporadas t ON t.id = lt.temporada_id
                WHERE le.id = :liga_equipo_id
                  AND lt.liga_id = :liga_id
                  AND e.liga_id = :liga_propietaria
                  AND lt.estado = 'activo'
                FOR UPDATE
            ");
            $consulta->execute([
                ':liga_equipo_id' => $ligaEquipoId,
                ':liga_id' => $ligaId,
                ':liga_propietaria' => $ligaId
            ]);
            $registro = $consulta->fetch(PDO::FETCH_ASSOC);
            if (!$registro) {
                $this->pdo->rollBack();
                return false;
            }

            $estadoAnterior = (string) $registro['estado_participacion'];
            $transiciones = [
                'activo' => ['suspendido', 'baja'],
                'suspendido' => ['activo', 'baja'],
                'baja' => [],
                'pendiente' => [],
                'rechazado' => []
            ];
            if (!in_array($estadoNuevo, $transiciones[$estadoAnterior] ?? [], true)) {
                $this->pdo->rollBack();
                return false;
            }

            $stmt = $this->pdo->prepare("
                UPDATE tb_liga_equipos
                SET estado_participacion = :estado,
                    fecha_baja = CASE WHEN :estado_fecha = 'baja' THEN NOW() ELSE NULL END,
                    motivo_baja = CASE WHEN :motivo_estado = 'baja' THEN :motivo ELSE NULL END
                WHERE id = :id
            ");
            $stmt->execute([
                ':estado' => $estadoNuevo,
                ':estado_fecha' => $estadoNuevo,
                ':motivo_estado' => $estadoNuevo,
                ':motivo' => $motivo !== '' ? $motivo : null,
                ':id' => $ligaEquipoId
            ]);

            $esBaja = $estadoNuevo === 'baja';
            $accion = $esBaja ? 'baja_equipo_temporada' : 'cambio_participacion_equipo';
            $descripcion = $esBaja
                ? 'Baja de participación. Motivo: ' . $motivo
                : 'Se actualizó el estado de participación del equipo.';
            $historial = $this->pdo->prepare("
                INSERT INTO tb_liga_historial
                    (liga_id, usuario_id, accion, campo, valor_anterior, valor_nuevo, descripcion)
                VALUES (:liga_id, :usuario_id, :accion, 'estado_participacion', :anterior, :nuevo, :descripcion)
            ");
            $historial->execute([
                ':liga_id' => $ligaId,
                ':usuario_id' => $usuarioId,
                ':accion' => $accion,
                ':anterior' => $estadoAnterior . ' · ' . $registro['equipo'] . ' / ' . $registro['temporada'],
                ':nuevo' => $estadoNuevo . ' · ' . $registro['equipo'] . ' / ' . $registro['temporada'],
                ':descripcion' => mb_substr($descripcion, 0, 255)
            ]);

            $this->pdo->commit();
            return true;
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            throw $e;
        }
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
    public function resolverVinculacionTemporada(int $ligaId, int $ligaTemporadaId, string $estado, int $usuarioId): bool
    {
        if (!in_array($estado, ['activo', 'rechazado'], true)) {
            throw new InvalidArgumentException('Estado de vinculación no válido.');
        }

        $this->pdo->beginTransaction();
        try {
            $consulta = $this->pdo->prepare("
                SELECT lt.temporada_id, t.temporada
                FROM tb_liga_temporadas lt
                INNER JOIN tb_temporadas t ON t.id = lt.temporada_id
                WHERE lt.id = :id AND lt.liga_id = :liga_id AND lt.estado = 'pendiente'
                FOR UPDATE
            ");
            $consulta->execute([':id' => $ligaTemporadaId, ':liga_id' => $ligaId]);
            $registro = $consulta->fetch(PDO::FETCH_ASSOC);
            if (!$registro) {
                $this->pdo->rollBack();
                return false;
            }

            $stmt = $this->pdo->prepare("
                UPDATE tb_liga_temporadas
                SET estado = :estado,
                    fecha_alta = CASE WHEN :estado_fecha = 'activo' THEN NOW() ELSE fecha_alta END,
                    fecha_baja = CASE WHEN :estado_baja = 'activo' THEN NULL ELSE fecha_baja END
                WHERE id = :liga_temporada_id AND liga_id = :liga_id AND estado = 'pendiente'
            ");
            $stmt->execute([
                ':estado' => $estado,
                ':estado_fecha' => $estado,
                ':estado_baja' => $estado,
                ':liga_temporada_id' => $ligaTemporadaId,
                ':liga_id' => $ligaId
            ]);
            if ($stmt->rowCount() !== 1) {
                $this->pdo->rollBack();
                return false;
            }

            $accion = $estado === 'activo' ? 'aprobacion_temporada' : 'rechazo_temporada';
            $historial = $this->pdo->prepare("
                INSERT INTO tb_liga_historial
                    (liga_id, usuario_id, accion, campo, valor_anterior, valor_nuevo, descripcion)
                VALUES (:liga_id, :usuario_id, :accion, 'temporada', 'Solicitud pendiente', :nuevo, :descripcion)
            ");
            $historial->execute([
                ':liga_id' => $ligaId,
                ':usuario_id' => $usuarioId,
                ':accion' => $accion,
                ':nuevo' => $registro['temporada'],
                ':descripcion' => $estado === 'activo'
                    ? 'Se aprobó la vinculación de la temporada.'
                    : 'Se rechazó la solicitud de vinculación de la temporada.'
            ]);
            $this->pdo->commit();
            return true;
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            throw $e;
        }
    }


    /**
     * Registra la solicitud de vinculación usando el estado predeterminado pendiente.
     */
    public function vincularTemporada(int $ligaId, int $temporadaId, int $usuarioId): bool
    {
        $this->pdo->beginTransaction();
        try {
            $liga = $this->pdo->prepare("SELECT nombre FROM tb_ligas WHERE id = :id FOR UPDATE");
            $liga->execute([':id' => $ligaId]);
            $ligaNombre = $liga->fetchColumn();
            $temporada = $this->pdo->prepare("SELECT temporada FROM tb_temporadas WHERE id = :id");
            $temporada->execute([':id' => $temporadaId]);
            $temporadaNombre = $temporada->fetchColumn();
            if (!$ligaNombre || !$temporadaNombre) {
                throw new InvalidArgumentException('La liga o temporada no existe.');
            }

            $existente = $this->pdo->prepare("
                SELECT id, estado FROM tb_liga_temporadas
                WHERE liga_id = :liga_id AND temporada_id = :temporada_id
                FOR UPDATE
            ");
            $existente->execute([':liga_id' => $ligaId, ':temporada_id' => $temporadaId]);
            $vinculo = $existente->fetch(PDO::FETCH_ASSOC);

            if ($vinculo) {
                if (!in_array($vinculo['estado'], ['revocado', 'rechazado'], true)) {
                    throw new InvalidArgumentException('La temporada ya tiene una solicitud o autorización vigente.');
                }
                $actualizar = $this->pdo->prepare("
                    UPDATE tb_liga_temporadas
                    SET estado = 'pendiente', fecha_solicitud = NOW()
                    WHERE id = :id AND estado IN ('revocado', 'rechazado')
                ");
                $actualizar->execute([':id' => (int) $vinculo['id']]);
                if ($actualizar->rowCount() !== 1) {
                    throw new RuntimeException('No fue posible reabrir la solicitud de temporada.');
                }
                $accion = 'solicitud_reingreso_temporada';
                $anterior = (string) $vinculo['estado'];
                $descripcion = 'Se solicitó nuevamente la participación en la temporada.';
            } else {
                $insertar = $this->pdo->prepare("
                    INSERT INTO tb_liga_temporadas (liga_id, temporada_id, estado, fecha_solicitud)
                    VALUES (:liga_id, :temporada_id, 'pendiente', NOW())
                ");
                $insertar->execute([':liga_id' => $ligaId, ':temporada_id' => $temporadaId]);
                $accion = 'solicitud_temporada';
                $anterior = null;
                $descripcion = 'Se solicitó vincular una temporada a la liga.';
            }

            $historial = $this->pdo->prepare("
                INSERT INTO tb_liga_historial
                    (liga_id, usuario_id, accion, campo, valor_anterior, valor_nuevo, descripcion)
                VALUES (:liga_id, :usuario_id, :accion, 'temporada', :anterior, :nuevo, :descripcion)
            ");
            $historial->execute([
                ':liga_id' => $ligaId,
                ':usuario_id' => $usuarioId,
                ':accion' => $accion,
                ':anterior' => $anterior,
                ':nuevo' => $temporadaNombre,
                ':descripcion' => $descripcion
            ]);
            $this->pdo->commit();
            return true;
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            throw $e;
        }
    }
}