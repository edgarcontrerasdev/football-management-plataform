<?php

class Equipo{
    private $pdo;

    public function __construct($pdo){
        $this->pdo = $pdo;
    }

    public function all() {
        $sql = "SELECT e.id,e.nombre AS equipo,e.estado,e.created_at,e.escudo,l.id AS liga_id,l.nombre AS liga 
        FROM tb_equipos e LEFT JOIN tb_ligas l ON e.liga_id = l.id ORDER BY e.nombre ASC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $sql = "
            SELECT
                e.id,
                e.nombre AS equipo,
                e.siglas,
                e.escudo,
                e.observaciones,
                e.created_at,
                e.updated_at
            FROM tb_equipos e
            WHERE e.id = :id
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', (int) $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByEstado($estado){
        $sql = "SELECT e.id,e.nombre AS equipo,e.estado,e.created_at,e.escudo,l.id AS liga_id,l.nombre AS liga  
        FROM tb_equipos e INNER JOIN tb_ligas l ON e.liga_id = l.id AND e.estado = :estado ORDER BY e.nombre ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':estado' => $estado]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByLiga($ligaId){
        $sql = "SELECT * FROM tb_equipos e WHERE e.liga_id = :ligaId";
        $stm = $this->pdo->prepare($sql);
        $stm->execute([':ligaId' => $ligaId]);
        return $stm->fetchAll(PDO::FETCH_ASSOC);
    }

   public function contarPorLiga(int $ligaId, int $temporadaId): int
    {
        $sql = "
            SELECT COUNT(*) AS total
            FROM tb_liga_equipos le
            INNER JOIN tb_liga_temporadas lt
                ON lt.id = le.liga_temporada_id
            WHERE lt.liga_id = :ligaId
            AND lt.temporada_id = :temporadaId
            AND le.estado_participacion = 'activo'
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':ligaId'      => $ligaId,
            ':temporadaId' => $temporadaId
        ]);

        return (int) $stmt->fetchColumn();
    }

    public function getByTorneo($torneoId){
        $sql = "SELECT * FROM tb_torneo_equipos te WHERE te.torneo_id = :torneoId";
        $stm = $this->pdo->prepare($sql);
        $stm->execute([':torneoId' => $torneoId]);
        return $stm->fetchAll(PDO::FETCH_ASSOC);
    }

    public function actualizarEscudo($equipo_id, $escudo){
        $sql = "UPDATE tb_equipos e SET e.escudo = :escudo WHERE e.id= :equipo_id";
        $stm = $this->pdo->prepare($sql);
        return $stm->execute([
            ':equipo_id' => $equipo_id,
            ':escudo' => $escudo
        ]);
    }

    public function guardarDireccion($equipo_id, $data)
    {
        $sql = "UPDATE tb_equipos SET
                calle = :calle,
                colonia = :colonia,
                codigo_postal = :codigo_postal,
                ciudad = :ciudad,
                estado_direccion = :estado_direccion,
                pais = :pais
                WHERE id = :equipo_id";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':calle' => $data['calle'] ?? null,
            ':colonia' => $data['colonia'] ?? null,
            ':codigo_postal' => $data['codigo_postal'] ?? null,
            ':ciudad' => $data['ciudad'] ?? null,
            ':estado_direccion' => $data['estado_direccion'] ?? null,
            ':pais' => $data['pais'] ?? null,
            ':equipo_id' => $equipo_id
        ]);

        return $stmt->rowCount();
    }

    public function guardarGenerales($equipo_id, $data){
        $sql = "UPDATE tb_equipos SET 
            nombre    = :nombre,
            siglas    = :siglas,
            observaciones = :observaciones 
            WHERE id = :equipo_id";

        $stm = $this->pdo->prepare($sql);
        return $stm->execute([
            ':nombre'           => $data['nombreEquipo'] ?: null,
            ':siglas'           => $data['siglas'] ?: null,
            ':observaciones'    => $data['observaciones'] ?: null,
            ':equipo_id'        => $equipo_id      
        ]);
    }

    public function guardarContacto($equipo_id, $data){
        $sql = "UPDATE tb_equipos SET 
                contacto_email = :email,
                contacto_telefono = :telefono,
                redes_sociales = :redes 
                WHERE id = :equipo_id";
        $stm = $this->pdo->prepare($sql);
         return $stm->execute([
            ':email'        => $data['email'] ?? null,
            ':telefono'     => $data['telefono'] ?? null,
            ':redes'        => $data['redes'] ?? null,
            ':equipo_id'    => $equipo_id
        ]);
    }


    /**
     * Historial de clubes relacionados con el equipo.
     */
    public function obtenerClubes(int $equipoId): array
    {
        $sql = "
            SELECT
                c.id,
                c.nombre,
                ce.fecha_inicio,
                ce.fecha_fin,
                ce.estado,
                ce.observaciones
            FROM tb_club_equipos ce
            INNER JOIN tb_clubes c ON c.id = ce.club_id
            WHERE ce.equipo_id = :equipo_id
            ORDER BY ce.fecha_inicio DESC, ce.id DESC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':equipo_id' => $equipoId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Participaciones del equipo por liga y temporada.
     */
    public function obtenerParticipacionesLigas(int $equipoId): array
    {
        $sql = "
            SELECT
                l.id AS liga_id,
                l.nombre AS liga,
                t.id AS temporada_id,
                t.temporada,
                lt.estado AS estado_liga_temporada,
                le.estado_participacion,
                le.estado_documentacion,
                le.numero_registro,
                le.fecha_solicitud,
                le.fecha_alta,
                le.fecha_baja,
                le.motivo_baja
            FROM tb_liga_equipos le
            INNER JOIN tb_liga_temporadas lt
                ON lt.id = le.liga_temporada_id
            INNER JOIN tb_ligas l
                ON l.id = lt.liga_id
            INNER JOIN tb_temporadas t
                ON t.id = lt.temporada_id
            WHERE le.equipo_id = :equipo_id
            ORDER BY t.fecha_inicio DESC, l.nombre ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':equipo_id' => $equipoId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Listado de entidades equipo. La participación en ligas y temporadas
    // se consulta desde sus relaciones, no desde tb_equipos.
    public function listar($filtros = [], $limit = 10, $offset = 0)
    {
        $sql = "
            SELECT
                e.id,
                e.nombre AS equipo,
                e.siglas,
                e.escudo,
                e.created_at AS fecha_alta
            FROM tb_equipos e
            WHERE 1 = 1
        ";

        $params = [];

        if (!empty($filtros['buscar'])) {
            $sql .= " AND (e.nombre LIKE :buscar OR e.siglas LIKE :buscar)";
            $params[':buscar'] = '%' . trim($filtros['buscar']) . '%';
        }

        $sql .= " ORDER BY e.nombre ASC LIMIT :limit OFFSET :offset";

        $stmt = $this->pdo->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function contar($filtros = [])
    {
        $sql = "SELECT COUNT(*) FROM tb_equipos e WHERE 1 = 1";
        $params = [];

        if (!empty($filtros['buscar'])) {
            $sql .= " AND (e.nombre LIKE :buscar OR e.siglas LIKE :buscar)";
            $params[':buscar'] = '%' . trim($filtros['buscar']) . '%';
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }
  
    // Crear nueva liga
    public function crear($data)
    {
        $sql = "INSERT INTO tb_equipos(
                    nombre, liga_id, estado, escudo
                ) VALUES(
                    :nombre, :ligaId, :estado, :escudo
                )";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':nombre' => $data['nombre'],
            ':estado' => $data['estado'] ?? 'activa',
            ':escudo' => $data['escudo'] ?? 'default.png',
            ':ligaId' =>$data['liga']
        ]);

        return $this->pdo->lastInsertId(); 
    }




    /**
     * Actualiza los datos permanentes de una entidad equipo.
     */
    public function actualizarEntidad(int $id, array $data): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE tb_equipos
            SET nombre = :nombre,
                siglas = :siglas,
                escudo = :escudo,
                observaciones = :observaciones
            WHERE id = :id
            LIMIT 1
        ");

        return $stmt->execute([
            ':nombre' => trim((string) $data['nombre']),
            ':siglas' => ($data['siglas'] ?? '') !== '' ? trim((string) $data['siglas']) : null,
            ':escudo' => $data['escudo'] ?? null,
            ':observaciones' => ($data['observaciones'] ?? '') !== ''
                ? trim((string) $data['observaciones'])
                : null,
            ':id' => $id
        ]);
    }

    /**
     * Registra la entidad base de un equipo.
     * La afiliación a clubes y la participación en ligas se gestionan
     * por separado y no forman parte de este registro.
     */
    public function crearEntidad(array $data): int
    {
        $sql = "
            INSERT INTO tb_equipos (nombre, siglas, escudo, observaciones)
            VALUES (:nombre, :siglas, :escudo, :observaciones)
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':nombre' => trim($data['nombre']),
            ':siglas' => ($data['siglas'] ?? '') !== '' ? trim($data['siglas']) : null,
            ':escudo' => $data['escudo'] ?? null,
            ':observaciones' => ($data['observaciones'] ?? '') !== ''
                ? trim($data['observaciones'])
                : null
        ]);

        return (int) $this->pdo->lastInsertId();
    }

}

?>