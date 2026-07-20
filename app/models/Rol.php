<?php

class Role {

    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Obtener todos los roles
    public function all() {
        $stmt = $this->db->query("SELECT * FROM tb_roles ORDER BY id ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Buscar un rol por ID
    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM tb_roles WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Buscar un rol por clave
    public function findByClave($clave) {
        $stmt = $this->db->prepare("SELECT * FROM roles WHERE clave = ? LIMIT 1");
        $stmt->execute([$clave]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Crear un rol
    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO tb_roles (clave, descripcion) VALUES (?, ?)");
        return $stmt->execute([$data['clave'], $data['descripcion']]);
    }

    // Actualizar un rol
    public function update($id, $data) {
        $stmt = $this->db->prepare("UPDATE roles SET clave = ?, descripcion = ? WHERE id = ?");
        return $stmt->execute([$data['clave'], $data['descripcion'], $id]);
    }

    // Eliminar un rol
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM roles WHERE id = ?");
        return $stmt->execute([$id]);
    }
}

?>