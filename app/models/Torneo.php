<?php

class Torneo{
    
    private $pdo;

    public function __construct($pdo){
        $this->pdo = $pdo;
    }

    public function getAll(){
        $sql = "SELECT t.nombre,t.id,t.fecha_inicio,t.fecha_fin,t.estado,t.tipo,l.nombre AS liga 
        FROM tb_torneos t INNER JOIN tb_ligas l WHERE t.liga_id = l.id";
        $stm = $this->pdo->query($sql);
        return $stm->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id){
        $sql = "SELECT t.nombre,t.id,t.fecha_inicio,t.fecha_fin,t.estado,t.tipo,t.liga_id,l.nombre AS liga 
        FROM tb_torneos t INNER JOIN tb_ligas l WHERE t.liga_id = l.id AND t.id = :id"; 
        $stm = $this->pdo->prepare($sql);
        $stm->execute([':id' => $id]);
        return $stm->fetch(PDO::FETCH_ASSOC);
    }

    public function limpiarEquipos($torneoId){
        $sql = "DELETE FROM tb_torneo_equipos WHERE torneo_id = :torneoId";
        $stm = $this->pdo->prepare($sql);
        $stm->execute([':torneoId' => $torneoId]);
    }

    public function asignarEquipo($torneoId, $equipoId){
        $sql = 'INSERT INTO tb_torneo_equipos (torneo_id, equipo_id) VALUES (:torneoId, :equipoId)';
        $stm = $this->pdo->prepare($sql);
        $stm->execute([
            ':torneoId' => $torneoId,
            ':equipoId' => $equipoId
        ]);
    }

    public function totalEquiposInscritos($torneoId){
        $sql = "SELECT COUNT(*) AS totalEquiposInscritos FROM tb_torneo_equipos te WHERE 
        te.torneo_id = :torneoId";
        $stm = $this->pdo->prepare($sql);
        $stm->execute([
            ':torneoId' => $torneoId
        ]);
        return $stm->fetch(PDO::FETCH_ASSOC);
    }
    

    public function crear($data)
    {
        $sql = "INSERT INTO tb_torneos 
                (liga_id, temporada_id, nombre, tipo, estado, created_at)
                VALUES (:liga_id, :temporada_id, :nombre, :tipo, :estado, NOW())";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);

        return $this->pdo->lastInsertId();
    }

    public function contar($filtros = []){
        $sql = "SELECT COUNT(*) total FROM tb_torneos t WHERE 1=1";
        $params = [];
        if(!empty($filtros['estado'])){
            $sql.= "AND t.estado = :estado";
            $params['estado'] = $filtros['estado'];
        }
        $stm = $this->pdo->prepare($sql);
        $stm->execute($params);
        return $stm->fetch(PDO::FETCH_ASSOC)['total'];
    }

    //LISTA DE TORNEOS CON FILTROS
    public function listar($filtros = [], $limit = 10, $offset = 0)
    {
         $sql = "SELECT * FROM tb_torneos t WHERE 1=1";

        $params = [];

        if(!empty($filtros['estado'])){
            $sql .= " AND t.estado = :estado";
            $params[':estado'] = $filtros['estado'];
        }

        if(!empty($filtros['buscar'])){
            $sql .= " AND t.nombre LIKE :buscar";
            $params[':buscar'] = "%".$filtros['buscar']."%";
        }

        $sql .= " ORDER BY t.nombre ASC LIMIT :limit OFFSET :offset";

        $stmt = $this->pdo->prepare($sql);

        foreach($params as $k=>$v){
            $stmt->bindValue($k,$v);
        }

        $stmt->bindValue(':limit',(int)$limit,PDO::PARAM_INT);
        $stmt->bindValue(':offset',(int)$offset,PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function eliminar($id){
        $sql ="DELETE FROM tb_torneos WHERE id = :id";
        $stm = $this->pdo->prepare($sql);
        return $stm->execute([
            ':id' => $id
        ]);
    }

    public function actualizar($id, $data)
    {
            $sql = "UPDATE tb_torneos SET
                        liga_id = :liga_id,
                        temporada_id = :temporada_id,
                        nombre = :nombre,
                        tipo = :tipo,
                        estado = :estado
                    WHERE id = :id";

            $stmt = $this->pdo->prepare($sql);
            $data['id'] = $id;
            $data['estado'] = 'planeado';
            return $stmt->execute([
                ':liga_id' => $data['liga_id'],
                ':temporada_id' => $data['temporada_id'],
                ':nombre' => $data['nombre'],
                ':tipo' => $data['tipo'],
                ':estado' => $data['estado'],
                ':id' => $data['id']
            ]);
    }

    public function actualizarStep2($id, $data)
    {
        $sql = "UPDATE tb_torneos SET
                    inscripcion = :inscripcion,
                    fianza = :fianza,
                    moneda = :moneda,
                    requiere_pagos = :requiere_pagos,
                    permite_invitados = :permite_invitados,
                    updated_at = NOW()
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $data['id'] = $id;

        return $stmt->execute($data);
    }

    public function actualizarStep3($id, $data){
        $sql='UPDATE tb_torneos SET
            modalidad = :modalidad,
            tiene_liguilla = :tiene_liguilla,
            segunda_vuelta_puntos = :segunda_vuelta_puntos,
            permite_inconcluso = :permite_inconcluso,
            updated_at = NOW()
        WHERE id = :id';
        $stm = $this->pdo->prepare($sql);
        $data['id'] = $id;

        return $stm->execute($data);
    }

    public function actualizarStep4($id, $data)
    {
        $sql = "UPDATE tb_torneos SET
                    tipo_bloques = :tipo_bloques,
                    comparte_bloques = :comparte_bloques,
                    updated_at = NOW()
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        $data['id'] = $id;

        return $stmt->execute($data);
    }

    public function getBloquesDisponibles($ligaId, $temporadaId)
    {
        $sql = "SELECT *
                FROM tb_bloques_competencia
                WHERE liga_id = :liga_id
                AND temporada_id = :temporada_id
                ORDER BY id DESC";

        $stm = $this->pdo->prepare($sql);
        $stm->execute([
            ':liga_id' => $ligaId,
            ':temporada_id' => $temporadaId
        ]);

        return $stm->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBloquesPorTorneo($torneoId)
    {
        $sql = "SELECT 
                    tb.id AS torneo_bloque_id,
                    tb.orden,
                    tb.impacta_puntos,
                    tb.impacta_suspensiones,
                    bc.id AS bloque_id,
                    bc.nombre,
                    bc.tipo_bloque,
                    bc.formato,
                    bc.vueltas_planeadas,
                    bc.estado
                FROM tb_torneo_bloques tb
                INNER JOIN tb_bloques_competencia bc 
                    ON bc.id = tb.bloque_id
                WHERE tb.torneo_id = :torneo_id
                ORDER BY tb.orden ASC";

        $stm = $this->pdo->prepare($sql);
        $stm->execute([':torneo_id' => $torneoId]);

        return $stm->fetchAll(PDO::FETCH_ASSOC);
    }

    public function torneoTieneBloques($torneoId)
    {
        $sql = "SELECT COUNT(*) 
                FROM tb_torneo_bloques
                WHERE torneo_id = :torneo_id";

        $stm = $this->pdo->prepare($sql);
        $stm->execute([':torneo_id' => $torneoId]);

        return $stm->fetchColumn() > 0;
    }
    

    public function tieneEquipos($torneoId)
    {
        $sql = "SELECT COUNT(*) FROM tb_torneo_equipos WHERE torneo_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$torneoId]);
        return $stmt->fetchColumn() > 0;
    }

    public function tieneBloques($torneoId)
    {
        $sql = "SELECT COUNT(*) FROM tb_torneo_bloques WHERE torneo_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$torneoId]);
        return $stmt->fetchColumn() > 0;
    }

    public function tieneFixture($torneoId)
    {
        return false;

        $sql = "SELECT COUNT(*) FROM tb_partidos WHERE torneo_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$torneoId]);
        return $stmt->fetchColumn() > 0;
    }
    public function siguienteOrdenBloque($torneoId)
    {
        $sql = "SELECT COALESCE(MAX(orden), 0) + 1
                FROM tb_torneo_bloques
                WHERE torneo_id = :torneo_id";

        $stm = $this->pdo->prepare($sql);
        $stm->execute([
            ':torneo_id' => $torneoId
        ]);

        return (int) $stm->fetchColumn();
    }

    public function crearBloqueCompetencia($data)
    {
        $sql = "INSERT INTO tb_bloques_competencia
                (
                    nombre,
                    temporada_id,
                    liga_id,
                    tipo_bloque,
                    formato,
                    vueltas_planeadas,
                    impacta_estadisticas,
                    impacta_suspensiones,
                    estado
                )
                VALUES
                (
                    :nombre,
                    :temporada_id,
                    :liga_id,
                    :tipo_bloque,
                    :formato,
                    :vueltas_planeadas,
                    :impacta_estadisticas,
                    :impacta_suspensiones,
                    :estado
                )";

        $stm = $this->pdo->prepare($sql);
        $stm->execute($data);

        return $this->pdo->lastInsertId();
    }

    public function asociarBloqueATorneo($data)
    {
        $sql = "INSERT INTO tb_torneo_bloques
                (
                    torneo_id,
                    bloque_id,
                    orden,
                    impacta_puntos,
                    impacta_suspensiones
                )
                VALUES
                (
                    :torneo_id,
                    :bloque_id,
                    :orden,
                    :impacta_puntos,
                    :impacta_suspensiones
                )";

        $stm = $this->pdo->prepare($sql);
        return $stm->execute($data);
    }

}


?>