<?php

class Temporada{

    private $pdo;

    public function __construct($pdo){
        $this->pdo = $pdo; 
    }

    //obtener todas las temporadas
    public function getAll(){
        $sql = "SELECT *FROM tb_temporadas t";
        $stm = $this->pdo->query($sql);
        return $stm->fetchAll(PDO::FETCH_ASSOC);
    }

    //obtener una temporada por ID
    public function getById($id){
        $sql = "SELECT * FROM tb_temporadas t WHERE t.id = :id";
        $stm = $this->pdo->prepare($sql);
        $stm->execute([':id' => $id]);
        return $stm->fetch(PDO::FETCH_ASSOC);
    }

    //obtener la temporada activa
    public function getActiva(){
        $sql = "SELECT * FROM tb_temporadas t WHERE t.es_actual = 1 LIMIT 1";
        $stm = $this->pdo->prepare($sql);
        $stm->execute();        
        return $stm->fetch(PDO::FETCH_ASSOC);
    }

    //obtener temporada por estado (activa, inactiva, borrador)
    public function getByEstado($estado){
        $sql = "SELECT * FROM tb_temporadas t WHERE t.estado = :estado";
        $stm = $this->pdo->prepare($sql);
        $stm->execute([':estado'=> $estado]);
        return $stm->fetchAll(PDO::FETCH_ASSOC);
    }

    //crear nueva temporada
    public function crear($data){
        $sql = "INSERT INTO tb_temporadas(temporada, fecha_inicio, fecha_fin, estado, es_actual, flexibilidad_fin, observaciones)
         VALUES(:temporada, :fecha_inicio, :fecha_fin, :estado, :es_actual, :flexibilidad_fin, :observaciones)";
       $stm = $this->pdo->prepare($sql);
       return $stm->execute([
            ':temporada' => $data['temporada'],
            ':fecha_inicio' => $data['fecha_inicio'],
            ':fecha_fin' => $data['fecha_fin'],
            ':estado' => $data['estado'],
            ':es_actual' => $data['es_actual'] ?? 0,
            ':flexibilidad_fin' => $data['flexibilidad_fin'] ?? 0,
            ':observaciones' => $data['observaciones'] ?? null
       ]);  
    }

    //actualizar temporada
     public function actualizar($id, $data) {
        $sql = "UPDATE tb_temporadas SET
                    temporada = :temporada,
                    fecha_inicio = :fecha_inicio,
                    fecha_fin = :fecha_fin,
                    estado = :estado,
                    observaciones = :observaciones
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':temporada'    => $data['temporada'],
            ':fecha_inicio' => $data['fecha_inicio'],
            ':fecha_fin'    => $data['fecha_fin'],
            ':estado'       => $data['estado'],
            ':observaciones'  => $data['observaciones'] ?? null,
            ':id'           => $id
        ]);
    }

    //eliminar una temporada
    public function eliminar($id){
        $sql = "DELETE FROM tb_temporadas WHERE id = :id AND estado = 'borrador'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id'    => $id]);
        return $stmt->rowCount() > 0;
    }
    
    /* =========================
       REGLAS DE NEGOCIO
    ========================= */

    // Activar una temporada (cierra la activa anterior)
    public function activar($id) {
        try{
            $this->pdo->beginTransaction();

            // Cerrar cualquier temporada activa
            $stmt = $this->pdo->prepare("
                UPDATE tb_temporadas
                SET estado = 'finalizada', es_actual = 0
                WHERE es_actual = 1
            ");

            $stmt->execute();

            // Activar la nueva
            $sql = "UPDATE tb_temporadas 
                    SET estado = 'activa', es_actual = 1 
                    WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([':id' => $id]);
            $this->pdo->commit();
            return true;
        }catch(Exception $e){
            $this->pdo->rollBack();
            throw $e;
        }

    }

    // Cerrar una temporada específica
    public function cerrar($id) {
        $sql = "UPDATE tb_temporadas 
                SET estado = 'cerrada', es_actual=0 
                WHERE id = :id
                AND estado = 'finalizada'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount() > 0;
    }

    //finalizar una temporada especifica
    public function finalizarTemporada($id){
        $sql = "UPDATE tb_temporadas 
                SET estado = 'finalizada' 
                WHERE id = :id";
        $stm = $this->pdo->prepare($sql);
        return $stm->execute([':id' => $id]);
    }

    public function cerrarTemporadaActiva(){
        $sql = "UPDATE tb_temporadas t SET estado = 'cerrada' WHERE t.estado = 'activa'";
        $this->pdo->query($sql);
    }

    public function finalizarTemporadaActiva(){
        $sql = "UPDATE tb_temporadas t SET estado = 'finalizada' WHERE t.estado = 'activa'";
        $this->pdo->query($sql);
    }

    // Validar que no exista una temporada con el mismo nombre
    public function existeNombre($temporada, $ignoredId = null) {
        $sql = "SELECT COUNT(*) 
                FROM tb_temporadas 
                WHERE temporada = :temporada";
            
        $params = [':temporada' => $temporada];

        if($ignoredId !== null){
            $sql.=" AND id != :id";
            $params[':id'] = $ignoredId;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    //LISTA DE TEMPORADAS CON FILTROS
    public function listar($filtros = [], $limit = 10, $offset = 0)
    {
        $sql = "SELECT * FROM tb_temporadas t WHERE 1=1";

        $params = [];

        if(!empty($filtros['estado'])){
            $sql .= " AND t.estado = :estado";
            $params[':estado'] = $filtros['estado'];
        }

        if(!empty($filtros['buscar'])){
            $sql .= " AND t.temporada LIKE :buscar";
            $params[':buscar'] = "%".$filtros['buscar']."%";
        }

        $sql .= " ORDER BY t.temporada ASC LIMIT :limit OFFSET :offset";

        $stmt = $this->pdo->prepare($sql);

        foreach($params as $k=>$v){
            $stmt->bindValue($k,$v);
        }

        
        $stmt->bindValue(':limit',(int)$limit,PDO::PARAM_INT);
        $stmt->bindValue(':offset',(int)$offset,PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //CONTAR NUMERO DE TORNEOS BASADO EN FILTROS
    public function contar($filtros =[]){
        $sql = "SELECT COUNT(*) total FROM tb_temporadas t WHERE 1 = 1";

        $params = [];

        if(!empty($filtros['estado'])){
            $sql .= " AND t.estado = :estado";
            $params[':estado'] = $filtros['estado'];
        }
        

        $stm = $this->pdo->prepare($sql);
        $stm->execute($params);
        return $stm->fetch(PDO::FETCH_ASSOC)['total'];
    }

}

?>