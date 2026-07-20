<?php

class Liga {

    private $pdo;

    public function __construct($pdo){
        $this->pdo = $pdo;
    }

    /* =========================
       CONSULTAS GENERALES
    ========================= */

    //Contar registros
    public function contar($filtros = []){
        $sql = "SELECT COUNT(*) total FROM tb_ligas l WHERE 1=1";
        $params = [];

        if(!empty($filtros['estado'])){
            $sql .= ' AND l.estado = :estado';
            $params[':estado']  = $filtros['estado'];
        }

        if(!empty($filtros['buscar'])){
            $sql.=' AND l.nombre LIKE :buscar'; 
            $params[':buscar'] = $filtros['buscar'];
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
    }

    //listar ligas con filtros
    public function listar($filtros = [], $limit = 10, $offset = 0)
    {
        $sql = "SELECT 
                l.id,
                l.nombre AS liga,
                l.estado,
                l.fecha_creacion,
                l.escudo
                FROM tb_ligas l
                WHERE 1=1";

        $params = [];

        if(!empty($filtros['estado'])){
            $sql .= " AND l.estado = :estado";
            $params[':estado'] = $filtros['estado'];
        }

        if(!empty($filtros['buscar'])){
            $sql .= " AND l.nombre LIKE :buscar";
            $params[':buscar'] = "%".$filtros['buscar']."%";
        }

        $sql .= " ORDER BY l.nombre ASC LIMIT :limit OFFSET :offset";

        $stmt = $this->pdo->prepare($sql);

        foreach($params as $k=>$v){
            $stmt->bindValue($k,$v);
        }

        $stmt->bindValue(':limit',(int)$limit,PDO::PARAM_INT);
        $stmt->bindValue(':offset',(int)$offset,PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Obtener todas las ligas
    public function all() {
        $sql = "SELECT * FROM tb_ligas ORDER BY nombre ASC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
    // Obtener liga por ID
    public function getById($id) {
        $sql = "SELECT * FROM tb_ligas WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Obtener ligas por estado
    public function getByEstado($estado) {
        $sql = "SELECT * FROM tb_ligas WHERE estado = :estado ORDER BY nombre ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':estado' => $estado]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* =========================
       CREAR / ACTUALIZAR
    ========================= */

    // Crear nueva liga
    public function crear($data)
    {
        $sql = "INSERT INTO tb_ligas(
                    nombre, estado, logo, contacto_email, contacto_telefono,
                    calle, colonia, codigo_postal, ciudad, estado_direccion, pais,
                    latitud, longitud, redes_sociales, notas
                ) VALUES(
                    :nombre, :estado, :logo, :contacto_email, :contacto_telefono,
                    :calle, :colonia, :codigo_postal, :ciudad, :estado_direccion, :pais,
                    :latitud, :longitud, :redes_sociales, :notas
                )";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':nombre' => $data['nombre'],
            ':estado' => $data['estado'] ?? 'activa',
            ':logo' => $data['logo'] ?? null,
            ':contacto_email' => $data['contacto_email'] ?? null,
            ':contacto_telefono' => $data['contacto_telefono'] ?? null,
            ':calle' => $data['calle'] ?? null,
            ':colonia' => $data['colonia'] ?? null,
            ':codigo_postal' => $data['codigo_postal'] ?? null,
            ':ciudad' => $data['ciudad'] ?? null,
            ':estado_direccion' => $data['estado_direccion'] ?? null,
            ':pais' => $data['pais'] ?? null,
            ':latitud' => $data['latitud'] ?? null,
            ':longitud' => $data['longitud'] ?? null,
            ':redes_sociales' => $data['redes_sociales'] ?? null,
            ':notas' => $data['notas'] ?? null
        ]);

        return $this->pdo->lastInsertId(); 
    }

    // Actualizar liga
    public function actualizar($id, $data){
        $sql = "UPDATE tb_ligas SET
                    nombre = :nombre,
                    estado = :estado,
                    logo = :logo,
                    contacto_email = :contacto_email,
                    contacto_telefono = :contacto_telefono,
                    calle = :calle,
                    colonia = :colonia,
                    codigo_postal = :codigo_postal,
                    ciudad = :ciudad,
                    estado_direccion = :estado_direccion,
                    pais = :pais,
                    latitud = :latitud,
                    longitud = :longitud,
                    redes_sociales = :redes_sociales,
                    notas = :notas,
                    fecha_modificacion = NOW()
                WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':nombre' => $data['nombre'],
            ':estado' => $data['estado'],
            ':logo' => $data['logo'] ?? null,
            ':contacto_email' => $data['contacto_email'] ?? null,
            ':contacto_telefono' => $data['contacto_telefono'] ?? null,
            ':calle' => $data['calle'] ?? null,
            ':colonia' => $data['colonia'] ?? null,
            ':codigo_postal' => $data['codigo_postal'] ?? null,
            ':ciudad' => $data['ciudad'] ?? null,
            ':estado_direccion' => $data['estado_direccion'] ?? null,
            ':pais' => $data['pais'] ?? null,
            ':latitud' => $data['latitud'] ?? null,
            ':longitud' => $data['longitud'] ?? null,
            ':redes_sociales' => $data['redes_sociales'] ?? null,
            ':notas' => $data['notas'] ?? null,
            ':id' => $id
        ]);
    }

    public function guardarDireccion($liga_id, $data)
    {
        $sql = "UPDATE tb_ligas SET
                calle = :calle,
                colonia = :colonia,
                codigo_postal = :codigo_postal,
                ciudad = :ciudad,
                estado_direccion = :estado_direccion,
                pais = :pais
            WHERE id = :liga_id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':calle' => $data['calle'] ?? null,
            ':colonia' => $data['colonia'] ?? null,
            ':codigo_postal' => $data['codigo_postal'] ?? null,
            ':ciudad' => $data['ciudad'] ?? null,
            ':estado_direccion' => $data['estado_direccion'] ?? null,
            ':pais' => $data['pais'] ?? null,
            ':liga_id' => $liga_id
        ]);
    }

    public function actualizarEscudo($liga_id, $escudo){
        $sql = "UPDATE tb_ligas SET escudo = :escudo WHERE id= :liga_id";
        $stm = $this->pdo->prepare($sql);
        return $stm->execute([
            ':liga_id' => $liga_id,
            ':escudo' => $escudo
        ]);
    }

    /* ============ DIRECTIVOS ========== */

    // Obtener directivos de la liga
    public function getDirectivos($liga_id){
       
    }

    // Agregar directivo
    public function addDirectivo($liga_id, $afiliado_id, $rol){
      
    }

    /* ============ DOCUMENTOS ================ */

    // Obtener documentos
    public function getDocumentos($liga_id){
       
    }

    // Agregar documento
    public function addDocumento($liga_id, $tipo, $archivo){
        
    }

    /* =========================
       RESUMEN / HISTORICO
    ========================= */

    public function getResumen($liga_id){
    
    }

}
?>
