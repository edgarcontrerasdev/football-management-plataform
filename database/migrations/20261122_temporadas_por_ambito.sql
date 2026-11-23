-- AFEC-LAP
-- Migracion: temporadas independientes por organismo o liga.
-- Ejecutar una sola vez sobre la base afec_v2.
-- Conserva los IDs, fechas, estados y relaciones existentes.
--
-- Estado verificado antes de esta migracion:
--   Organismo AFEC: id = 1.
--   tb_temporadas: una temporada existente (id = 1), sin ligas asociadas.
--   La estructura actual aun no contiene las columnas de ambito.
--
-- IMPORTANTE: no ejecutar sobre una base distinta ni volver a ejecutar
-- despues de que la migracion haya concluido.

USE afec_v2;

-- 1. Agregar el propietario y el ambito de cada temporada.
ALTER TABLE tb_temporadas
    ADD COLUMN ambito_tipo ENUM('organismo', 'liga') NOT NULL DEFAULT 'organismo' AFTER id,
    ADD COLUMN organismo_id INT UNSIGNED NULL AFTER ambito_tipo,
    ADD COLUMN liga_propietaria_id INT UNSIGNED NULL AFTER organismo_id;

-- 2. Las temporadas preexistentes pertenecen al organismo AFEC (id 1).
--    Las asociaciones de tb_liga_temporadas no se modifican.
UPDATE tb_temporadas
SET ambito_tipo = 'organismo',
    organismo_id = 1,
    liga_propietaria_id = NULL;

-- 3. Cambiar la unicidad global por unicidad dentro de cada propietario.
--    propietario_id permite que el indice unico funcione con ambos tipos
--    de propietario, evitando la semantica de NULL de indices compuestos.
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

-- Verificacion posterior:
-- SELECT id, temporada, ambito_tipo, organismo_id, liga_propietaria_id,
--        propietario_id, estado, es_actual
-- FROM tb_temporadas
-- ORDER BY id;
