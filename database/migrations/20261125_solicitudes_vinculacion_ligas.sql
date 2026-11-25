-- Solicitudes de vinculación institucional de ligas.
-- Ejecutar una sola vez sobre la base existente afec_v2.
CREATE TABLE IF NOT EXISTS tb_liga_organismo_solicitudes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    liga_id INT UNSIGNED NOT NULL,
    organismo_id INT UNSIGNED NOT NULL,
    solicitante_usuario_id INT UNSIGNED NOT NULL,
    solicitante_rol VARCHAR(30) NOT NULL,
    estado VARCHAR(20) NOT NULL DEFAULT 'pendiente',
    fecha_solicitud DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_resolucion DATETIME NULL,
    resuelto_por_usuario_id INT UNSIGNED NULL,
    observaciones TEXT NULL,
    respuesta VARCHAR(255) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_liga_org_sol_liga FOREIGN KEY (liga_id) REFERENCES tb_ligas(id),
    CONSTRAINT fk_liga_org_sol_organismo FOREIGN KEY (organismo_id) REFERENCES tb_organismos(id),
    CONSTRAINT fk_liga_org_sol_solicitante FOREIGN KEY (solicitante_usuario_id) REFERENCES tb_usuarios(id),
    CONSTRAINT fk_liga_org_sol_resolutor FOREIGN KEY (resuelto_por_usuario_id) REFERENCES tb_usuarios(id),
    INDEX idx_liga_org_sol_destino_estado (organismo_id, estado, fecha_solicitud),
    INDEX idx_liga_org_sol_liga (liga_id, fecha_solicitud),
    INDEX idx_liga_org_sol_par (liga_id, organismo_id, estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
