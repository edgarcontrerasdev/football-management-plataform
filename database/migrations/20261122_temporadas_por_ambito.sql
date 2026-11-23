-- AFEC-LAP
-- Migracion: temporadas independientes por organismo o liga.
-- Ejecutar una sola vez sobre la base de datos AFEC-LAP.
-- La migracion conserva IDs, fechas, estados y relaciones existentes.
-- La base se selecciona explícitamente para evitar ejecutarla en otro esquema.

USE afec_v2;


DROP PROCEDURE IF EXISTS migrar_temporadas_por_ambito;

DELIMITER $

CREATE PROCEDURE migrar_temporadas_por_ambito()
BEGIN
    DECLARE v_organismo_id INT UNSIGNED DEFAULT NULL;
    DECLARE v_total INT DEFAULT 0;

    SELECT COUNT(*), MIN(id)
      INTO v_total, v_organismo_id
    FROM tb_organismos
    WHERE UPPER(COALESCE(siglas, '')) = 'AFEC'
       OR nombre = 'Asociación de Fútbol del Estado de Colima A.C.';

    IF v_total <> 1 THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'La migracion requiere exactamente un organismo AFEC registrado. Verifica tb_organismos antes de continuar.';
    END IF;

    ALTER TABLE tb_temporadas
        ADD COLUMN ambito_tipo ENUM('organismo', 'liga') NOT NULL DEFAULT 'organismo' AFTER id,
        ADD COLUMN organismo_id INT UNSIGNED NULL AFTER ambito_tipo,
        ADD COLUMN liga_propietaria_id INT UNSIGNED NULL AFTER organismo_id;

    UPDATE tb_temporadas
       SET organismo_id = v_organismo_id,
           ambito_tipo = 'organismo';

    ALTER TABLE tb_temporadas
        DROP INDEX uq_temporadas_nombre,
        ADD COLUMN propietario_id INT UNSIGNED
            GENERATED ALWAYS AS (COALESCE(organismo_id, liga_propietaria_id)) STORED,
        ADD UNIQUE KEY uq_temporadas_ambito_nombre (ambito_tipo, propietario_id, temporada),
        ADD INDEX idx_temporadas_organismo (organismo_id, estado, es_actual),
        ADD INDEX idx_temporadas_liga_propietaria (liga_propietaria_id, estado, es_actual),
        ADD CONSTRAINT fk_temporadas_organismo
            FOREIGN KEY (organismo_id) REFERENCES tb_organismos(id),
        ADD CONSTRAINT fk_temporadas_liga_propietaria
            FOREIGN KEY (liga_propietaria_id) REFERENCES tb_ligas(id),
        ADD CONSTRAINT chk_temporadas_propietario
            CHECK (
                (ambito_tipo = 'organismo' AND organismo_id IS NOT NULL AND liga_propietaria_id IS NULL)
                OR
                (ambito_tipo = 'liga' AND organismo_id IS NULL AND liga_propietaria_id IS NOT NULL)
            );
END$$

DELIMITER ;

CALL migrar_temporadas_por_ambito();
DROP PROCEDURE migrar_temporadas_por_ambito;

-- Verificacion posterior:
-- SELECT id, temporada, ambito_tipo, organismo_id, liga_propietaria_id,
--        estado, es_actual
-- FROM tb_temporadas
-- ORDER BY fecha_inicio;
