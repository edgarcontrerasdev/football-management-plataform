-- AFEC-LAP
-- Agrega el rol de administrador de organismo sin modificar cuentas existentes.
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
