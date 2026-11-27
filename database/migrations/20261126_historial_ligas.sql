-- Historial de cambios administrativos de ligas.
-- Ejecutar una sola vez sobre la base existente afec_v2.
CREATE TABLE IF NOT EXISTS tb_liga_historial (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    liga_id INT UNSIGNED NOT NULL,
    usuario_id INT UNSIGNED NOT NULL,
    accion VARCHAR(30) NOT NULL,
    campo VARCHAR(60) NOT NULL,
    valor_anterior TEXT NULL,
    valor_nuevo TEXT NULL,
    descripcion VARCHAR(255) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_liga_historial_liga
        FOREIGN KEY (liga_id) REFERENCES tb_ligas(id)
    CONSTRAINT fk_liga_historial_usuario
        FOREIGN KEY (usuario_id) REFERENCES tb_usuarios(id),
    INDEX idx_liga_historial_liga_fecha (liga_id, created_at, id),
    INDEX idx_liga_historial_usuario (usuario_id, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
