-- AFEC-LAP
-- Agrega el rol de administrador de organismo y prepara los alcances existentes.
-- Ejecutar una sola vez sobre la base afec_v2 existente.

USE afec_v2;

INSERT INTO tb_roles (clave, nombre, descripcion, estado)
SELECT 'ORGANISMO',
       'Administrador de organismo',
       'Administración de usuarios y entidades dentro de un organismo autorizado.',
       'activo'
WHERE NOT EXISTS (
    SELECT 1 FROM tb_roles WHERE clave = 'ORGANISMO'
);

UPDATE tb_roles
SET nombre = 'Administrador de plataforma',
    descripcion = 'Administración global y excepcional de la plataforma.'
WHERE clave = 'ADMIN';

UPDATE tb_roles
SET nombre = 'Administrador de liga',
    descripcion = 'Operación de una liga dentro de sus alcances.'
WHERE clave = 'LIGA';

UPDATE tb_roles
SET nombre = 'Administrador de equipo',
    descripcion = 'Operación de un equipo dentro de sus alcances.'
WHERE clave = 'EQUIPO';

-- Asocia alcances de liga existentes con su organismo cuando la relación es única.
UPDATE tb_usuario_alcances ua
INNER JOIN (
    SELECT liga_id, MIN(organismo_id) AS organismo_id
    FROM tb_liga_organismos
    WHERE estado = 'activo'
    GROUP BY liga_id
    HAVING COUNT(DISTINCT organismo_id) = 1
) relacion ON relacion.liga_id = ua.entidad_id
SET ua.organismo_id = relacion.organismo_id
WHERE ua.tipo_entidad = 'liga'
  AND ua.estado = 'activo'
  AND ua.organismo_id IS NULL;

-- Prepara una tabla temporal para no actualizar y consultar la misma tabla
-- dentro de una subconsulta correlacionada.
DROP TEMPORARY TABLE IF EXISTS tmp_afec_usuario_equipo_org;
CREATE TEMPORARY TABLE tmp_afec_usuario_equipo_org AS
SELECT
    alcance_equipo.id AS alcance_equipo_id,
    MIN(relacion.organismo_id) AS organismo_id
FROM tb_usuario_alcances alcance_equipo
INNER JOIN tb_usuario_alcances alcance_liga
    ON alcance_liga.usuario_id = alcance_equipo.usuario_id
   AND alcance_liga.tipo_entidad = 'liga'
   AND alcance_liga.estado = 'activo'
INNER JOIN tb_liga_organismos relacion
    ON relacion.liga_id = alcance_liga.entidad_id
   AND relacion.estado = 'activo'
WHERE alcance_equipo.tipo_entidad = 'equipo'
  AND alcance_equipo.estado = 'activo'
GROUP BY alcance_equipo.id
HAVING COUNT(DISTINCT relacion.organismo_id) = 1;

UPDATE tb_usuario_alcances equipo
INNER JOIN tmp_afec_usuario_equipo_org asignacion
    ON asignacion.alcance_equipo_id = equipo.id
SET equipo.organismo_id = asignacion.organismo_id
WHERE equipo.organismo_id IS NULL
  AND equipo.tipo_entidad = 'equipo'
  AND equipo.estado = 'activo';

DROP TEMPORARY TABLE IF EXISTS tmp_afec_usuario_equipo_org;
