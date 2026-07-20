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

    public function getById($id){
        $sql = "SELECT 
                e.id, e.siglas, e.nombre AS equipo,
                e.estado, e.fecha_creacion,
                e.escudo, e.calle,e.colonia,
                e.codigo_postal, e.ciudad,
                e.estado_direccion, e.pais,
                e.contacto_email, e.contacto_telefono,
                e.redes_sociales, e.observaciones,
                l.id AS liga_id,
                l.nombre AS liga
            FROM tb_equipos e
            LEFT JOIN tb_ligas l ON e.liga_id = l.id WHERE e.id=:id"; 
            
        $stmt = $this->pdo->prepare($sql);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

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

    //LISTA DE EQUIPOS CON FILTROS
    public function listar($filtros = [], $limit = 10, $offset = 0)
    {
        $sql = "SELECT 
                e.id, e.nombre AS equipo,
                e.estado, e.fecha_creacion,
                e.escudo, e.calle,
                e.colonia,e.codigo_postal, e.ciudad,
                e.estado_direccion, e.pais,
                e.contacto_email, e.contacto_telefono,
                e.observaciones,
                l.id AS liga_id,
                l.nombre AS liga
            FROM tb_equipos e
            LEFT JOIN tb_ligas l ON e.liga_id = l.id WHERE 1=1";

        $params = [];

        if(!empty($filtros['estado'])){
            $sql .= " AND e.estado = :estado";
            $params[':estado'] = $filtros['estado'];
        }

        if(!empty($filtros['liga'])){
            $sql .= " AND e.liga_id = :liga";
            $params[':liga'] = $filtros['liga'];
        }

        if(!empty($filtros['buscar'])){
            $sql .= " AND e.nombre LIKE :buscar";
            $params[':buscar'] = "%".$filtros['buscar']."%";
        }

        $sql .= " ORDER BY e.nombre ASC LIMIT :limit OFFSET :offset";

        $stmt = $this->pdo->prepare($sql);

        foreach($params as $k=>$v){
            $stmt->bindValue($k,$v);
        }

        $stmt->bindValue(':limit',(int)$limit,PDO::PARAM_INT);
        $stmt->bindValue(':offset',(int)$offset,PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function contar($filtros = [])
    {
        $sql = "SELECT COUNT(*) total 
                FROM tb_equipos e 
                WHERE 1=1";

        $params = [];

        if(!empty($filtros['estado'])){
            $sql .= " AND e.estado = :estado";
            $params[':estado'] = $filtros['estado'];
        }

        if(!empty($filtros['liga'])){
            $sql .= " AND e.liga_id = :liga";
            $params[':liga'] = $filtros['liga'];
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
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



}

?>