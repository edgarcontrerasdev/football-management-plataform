-- =============================================================
-- AFEC-LAP V2
-- DDL base limpio para MySQL/MariaDB
-- No migra datos de la base AFEC anterior.
-- =============================================================
DROP DATABASE IF EXISTS afec_v2;
CREATE DATABASE afec_v2 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE afec_v2;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- -------------------------------------------------------------
-- tb_organismos
-- -------------------------------------------------------------
CREATE TABLE tb_organismos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    siglas VARCHAR(30) NULL,
    tipo VARCHAR(30) NOT NULL,
    alcance_territorial VARCHAR(30) NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'activo',
    descripcion TEXT NULL,
    correo VARCHAR(150) NULL,
    telefono VARCHAR(30) NULL,
    sitio_web VARCHAR(255) NULL,
    logo VARCHAR(255) NULL,
    fecha_creacion DATE NULL,
    fecha_modificacion DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_organismos_nombre (nombre),
    INDEX idx_organismos_tipo_estado (tipo, estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_organismo_relaciones
-- -------------------------------------------------------------
CREATE TABLE tb_organismo_relaciones (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    organismo_origen_id INT UNSIGNED NOT NULL,
    organismo_destino_id INT UNSIGNED NOT NULL,
    tipo_relacion VARCHAR(40) NOT NULL,
    es_gestor TINYINT(1) NOT NULL DEFAULT 0,
    fecha_inicio DATE NULL,
    fecha_fin DATE NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'activo',
    referencia VARCHAR(100) NULL,
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_org_rel_origen FOREIGN KEY (organismo_origen_id) REFERENCES tb_organismos(id),
    CONSTRAINT fk_org_rel_destino FOREIGN KEY (organismo_destino_id) REFERENCES tb_organismos(id),
    INDEX idx_org_rel_origen (organismo_origen_id),
    INDEX idx_org_rel_destino (organismo_destino_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_temporadas
-- -------------------------------------------------------------
CREATE TABLE tb_temporadas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    temporada VARCHAR(50) NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'planeada',
    es_actual TINYINT(1) NOT NULL DEFAULT 0,
    flexibilidad_fin TINYINT(1) NOT NULL DEFAULT 0,
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_temporadas_nombre (temporada),
    INDEX idx_temporadas_estado_actual (estado, es_actual),
    INDEX idx_temporadas_fechas (fecha_inicio, fecha_fin)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_roles_funcionales
-- -------------------------------------------------------------
CREATE TABLE tb_roles_funcionales (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    clave VARCHAR(50) NOT NULL,
    tipo VARCHAR(30) NOT NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'activo',
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_roles_funcionales_clave (clave)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_ligas
-- -------------------------------------------------------------
CREATE TABLE tb_ligas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    siglas VARCHAR(30) NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'activo',
    logo VARCHAR(255) NULL,
    correo VARCHAR(150) NULL,
    telefono VARCHAR(30) NULL,
    direccion VARCHAR(255) NULL,
    colonia VARCHAR(100) NULL,
    municipio VARCHAR(100) NULL,
    estado_republica VARCHAR(100) NULL,
    codigo_postal VARCHAR(10) NULL,
    latitud DECIMAL(10,7) NULL,
    longitud DECIMAL(10,7) NULL,
    redes_sociales TEXT NULL,
    notas TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_ligas_nombre (nombre),
    INDEX idx_ligas_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_liga_organismos
-- -------------------------------------------------------------
CREATE TABLE tb_liga_organismos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    liga_id INT UNSIGNED NOT NULL,
    organismo_id INT UNSIGNED NOT NULL,
    tipo_relacion VARCHAR(30) NOT NULL,
    es_gestor TINYINT(1) NOT NULL DEFAULT 0,
    fecha_inicio DATE NULL,
    fecha_fin DATE NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'activo',
    referencia VARCHAR(100) NULL,
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_liga_org_liga FOREIGN KEY (liga_id) REFERENCES tb_ligas(id),
    CONSTRAINT fk_liga_org_organismo FOREIGN KEY (organismo_id) REFERENCES tb_organismos(id),
    INDEX idx_liga_org_liga (liga_id),
    INDEX idx_liga_org_org (organismo_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_liga_temporadas
-- -------------------------------------------------------------
CREATE TABLE tb_liga_temporadas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    liga_id INT UNSIGNED NOT NULL,
    temporada_id INT UNSIGNED NOT NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'pendiente',
    fecha_solicitud DATETIME NULL,
    fecha_alta DATETIME NULL,
    fecha_baja DATETIME NULL,
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_liga_temp_liga FOREIGN KEY (liga_id) REFERENCES tb_ligas(id),
    CONSTRAINT fk_liga_temp_temporada FOREIGN KEY (temporada_id) REFERENCES tb_temporadas(id),
    UNIQUE KEY uq_liga_temporada (liga_id, temporada_id),
    INDEX idx_liga_temporadas_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_clubes
-- -------------------------------------------------------------
CREATE TABLE tb_clubes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    siglas VARCHAR(30) NULL,
    logo VARCHAR(255) NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'activo',
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_clubes_nombre (nombre),
    INDEX idx_clubes_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_equipos
-- -------------------------------------------------------------
CREATE TABLE tb_equipos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    siglas VARCHAR(30) NULL,
    escudo VARCHAR(255) NULL,
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_equipos_nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_club_equipos
-- -------------------------------------------------------------
CREATE TABLE tb_club_equipos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    club_id INT UNSIGNED NOT NULL,
    equipo_id INT UNSIGNED NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'activo',
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_club_equipos_club FOREIGN KEY (club_id) REFERENCES tb_clubes(id),
    CONSTRAINT fk_club_equipos_equipo FOREIGN KEY (equipo_id) REFERENCES tb_equipos(id),
    INDEX idx_club_equipos_club (club_id),
    INDEX idx_club_equipos_equipo (equipo_id),
    INDEX idx_club_equipos_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_categorias
-- -------------------------------------------------------------
CREATE TABLE tb_categorias (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    clave VARCHAR(30) NOT NULL,
    tipo VARCHAR(30) NOT NULL,
    rama VARCHAR(30) NULL,
    edad_minima TINYINT UNSIGNED NULL,
    edad_maxima TINYINT UNSIGNED NULL,
    jerarquia INT NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'activo',
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_categorias_clave (clave),
    INDEX idx_categorias_jerarquia (jerarquia)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_divisiones
-- -------------------------------------------------------------
CREATE TABLE tb_divisiones (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    clave VARCHAR(30) NOT NULL,
    orden INT UNSIGNED NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'activo',
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_divisiones_clave (clave),
    INDEX idx_divisiones_orden (orden)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_equipo_temporadas
-- -------------------------------------------------------------
CREATE TABLE tb_equipo_temporadas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    equipo_id INT UNSIGNED NOT NULL,
    temporada_id INT UNSIGNED NOT NULL,
    nombre_mostrado VARCHAR(150) NULL,
    siglas VARCHAR(30) NULL,
    escudo VARCHAR(255) NULL,
    color_principal VARCHAR(30) NULL,
    color_secundario VARCHAR(30) NULL,
    descripcion TEXT NULL,
    patrocinador VARCHAR(150) NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'activo',
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_equipo_temp_equipo FOREIGN KEY (equipo_id) REFERENCES tb_equipos(id),
    CONSTRAINT fk_equipo_temp_temporada FOREIGN KEY (temporada_id) REFERENCES tb_temporadas(id),
    UNIQUE KEY uq_equipo_temporada (equipo_id, temporada_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- -------------------------------------------------------------
-- tb_equipo_identidad_historial
-- -------------------------------------------------------------
CREATE TABLE tb_equipo_identidad_historial (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    equipo_id INT UNSIGNED NOT NULL,
    equipo_temporada_id INT UNSIGNED NULL,
    nombre_anterior VARCHAR(150) NULL,
    nombre_nuevo VARCHAR(150) NULL,
    siglas_anterior VARCHAR(30) NULL,
    siglas_nueva VARCHAR(30) NULL,
    motivo TEXT NULL,
    fecha_cambio DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    usuario_id INT UNSIGNED NULL,
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_equipo_ident_hist_equipo
        FOREIGN KEY (equipo_id) REFERENCES tb_equipos(id),
    CONSTRAINT fk_equipo_ident_hist_temp
        FOREIGN KEY (equipo_temporada_id) REFERENCES tb_equipo_temporadas(id),
    INDEX idx_equipo_ident_hist_equipo_fecha (equipo_id, fecha_cambio),
    INDEX idx_equipo_ident_hist_temp (equipo_temporada_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_equipo_uniformes
-- -------------------------------------------------------------
CREATE TABLE tb_equipo_uniformes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    equipo_temporada_id INT UNSIGNED NOT NULL,
    tipo VARCHAR(30) NOT NULL,
    descripcion VARCHAR(150) NULL,
    imagen VARCHAR(255) NULL,
    color_principal VARCHAR(30) NULL,
    color_secundario VARCHAR(30) NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'activo',
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_equipo_uniforme_temp FOREIGN KEY (equipo_temporada_id) REFERENCES tb_equipo_temporadas(id),
    INDEX idx_equipo_uniformes_temp_tipo (equipo_temporada_id, tipo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_unidades_deportivas
-- -------------------------------------------------------------
CREATE TABLE tb_unidades_deportivas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    tipo VARCHAR(40) NULL,
    direccion VARCHAR(255) NULL,
    referencia TEXT NULL,
    acceso TEXT NULL,
    contacto VARCHAR(150) NULL,
    telefono VARCHAR(30) NULL,
    latitud DECIMAL(10,7) NULL,
    longitud DECIMAL(10,7) NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'activo',
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_unidades_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_campos_deportivos
-- -------------------------------------------------------------
CREATE TABLE tb_campos_deportivos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    unidad_id INT UNSIGNED NOT NULL,
    nombre VARCHAR(150) NOT NULL,
    identificador VARCHAR(50) NULL,
    tipo VARCHAR(40) NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'activo',
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_campos_unidad FOREIGN KEY (unidad_id) REFERENCES tb_unidades_deportivas(id),
    UNIQUE KEY uq_campo_unidad_identificador (unidad_id, identificador),
    INDEX idx_campos_unidad (unidad_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_equipo_campos
-- -------------------------------------------------------------
CREATE TABLE tb_equipo_campos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    equipo_id INT UNSIGNED NOT NULL,
    campo_id INT UNSIGNED NOT NULL,
    tipo VARCHAR(30) NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'activo',
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_equipo_campos_equipo FOREIGN KEY (equipo_id) REFERENCES tb_equipos(id),
    CONSTRAINT fk_equipo_campos_campo FOREIGN KEY (campo_id) REFERENCES tb_campos_deportivos(id),
    INDEX idx_equipo_campos_equipo (equipo_id),
    INDEX idx_equipo_campos_campo (campo_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_liga_equipos
-- -------------------------------------------------------------
CREATE TABLE tb_liga_equipos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    liga_temporada_id INT UNSIGNED NOT NULL,
    equipo_id INT UNSIGNED NOT NULL,
    numero_registro VARCHAR(50) NULL,
    estado_participacion VARCHAR(30) NOT NULL DEFAULT 'pendiente',
    estado_documentacion VARCHAR(30) NOT NULL DEFAULT 'pendiente',
    fecha_solicitud DATETIME NULL,
    fecha_alta DATETIME NULL,
    fecha_baja DATETIME NULL,
    motivo_baja VARCHAR(255) NULL,
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_liga_equipos_liga_temp FOREIGN KEY (liga_temporada_id) REFERENCES tb_liga_temporadas(id),
    CONSTRAINT fk_liga_equipos_equipo FOREIGN KEY (equipo_id) REFERENCES tb_equipos(id),
    UNIQUE KEY uq_liga_temp_equipo (liga_temporada_id, equipo_id),
    INDEX idx_liga_equipos_numero (numero_registro)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_liga_competencias
-- -------------------------------------------------------------
CREATE TABLE tb_liga_competencias (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    liga_temporada_id INT UNSIGNED NOT NULL,
    categoria_id INT UNSIGNED NOT NULL,
    division_id INT UNSIGNED NULL,
    nombre VARCHAR(150) NOT NULL,
    clave VARCHAR(50) NOT NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'activo',
    orden INT UNSIGNED NULL,
    fecha_inicio DATE NULL,
    fecha_fin DATE NULL,
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_liga_comp_liga_temp FOREIGN KEY (liga_temporada_id) REFERENCES tb_liga_temporadas(id),
    CONSTRAINT fk_liga_comp_categoria FOREIGN KEY (categoria_id) REFERENCES tb_categorias(id),
    CONSTRAINT fk_liga_comp_division FOREIGN KEY (division_id) REFERENCES tb_divisiones(id),
    UNIQUE KEY uq_liga_competencia_clave (liga_temporada_id, clave),
    INDEX idx_liga_comp_categoria (categoria_id),
    INDEX idx_liga_comp_division (division_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_liga_competencia_equipos
-- -------------------------------------------------------------
CREATE TABLE tb_liga_competencia_equipos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    liga_competencia_id INT UNSIGNED NOT NULL,
    liga_equipo_id INT UNSIGNED NOT NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'activo',
    fecha_alta DATETIME NULL,
    fecha_baja DATETIME NULL,
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_liga_comp_equipos_comp FOREIGN KEY (liga_competencia_id) REFERENCES tb_liga_competencias(id),
    CONSTRAINT fk_liga_comp_equipos_liga_equipo FOREIGN KEY (liga_equipo_id) REFERENCES tb_liga_equipos(id),
    UNIQUE KEY uq_liga_comp_equipo (liga_competencia_id, liga_equipo_id),
    INDEX idx_liga_comp_equipo_liga (liga_equipo_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_bloques_competencia
-- -------------------------------------------------------------
CREATE TABLE tb_bloques_competencia (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    tipo_bloque VARCHAR(30) NOT NULL,
    formato VARCHAR(30) NOT NULL,
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_bloques_tipo (tipo_bloque)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_torneos
-- -------------------------------------------------------------
CREATE TABLE tb_torneos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    liga_temporada_id INT UNSIGNED NOT NULL,
    liga_competencia_id INT UNSIGNED NULL,
    nombre VARCHAR(150) NOT NULL,
    tipo VARCHAR(30) NOT NULL,
    descripcion TEXT NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'planeada',
    fecha_inicio DATE NULL,
    fecha_fin DATE NULL,
    permite_invitados TINYINT(1) NOT NULL DEFAULT 0,
    activated_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_torneos_liga_temp FOREIGN KEY (liga_temporada_id) REFERENCES tb_liga_temporadas(id),
    CONSTRAINT fk_torneos_liga_comp FOREIGN KEY (liga_competencia_id) REFERENCES tb_liga_competencias(id),
    INDEX idx_torneos_liga_temp (liga_temporada_id),
    INDEX idx_torneos_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_torneo_equipos
-- -------------------------------------------------------------
CREATE TABLE tb_torneo_equipos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    torneo_id INT UNSIGNED NOT NULL,
    equipo_id INT UNSIGNED NOT NULL,
    liga_competencia_equipo_id INT UNSIGNED NULL,
    tipo_inscripcion VARCHAR(30) NOT NULL DEFAULT 'normal',
    estado_inscripcion VARCHAR(30) NOT NULL DEFAULT 'pendiente',
    fecha_inscripcion DATETIME NULL,
    fecha_autorizacion DATETIME NULL,
    fecha_retiro DATETIME NULL,
    motivo_retiro VARCHAR(255) NULL,
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_torneo_equipos_torneo FOREIGN KEY (torneo_id) REFERENCES tb_torneos(id),
    CONSTRAINT fk_torneo_equipos_equipo FOREIGN KEY (equipo_id) REFERENCES tb_equipos(id),
    CONSTRAINT fk_torneo_equipos_comp_equipo FOREIGN KEY (liga_competencia_equipo_id) REFERENCES tb_liga_competencia_equipos(id),
    UNIQUE KEY uq_torneo_equipo (torneo_id, equipo_id),
    INDEX idx_torneo_equipos_equipo (equipo_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_torneo_bloques
-- -------------------------------------------------------------
CREATE TABLE tb_torneo_bloques (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    torneo_id INT UNSIGNED NOT NULL,
    bloque_id INT UNSIGNED NOT NULL,
    orden INT UNSIGNED NOT NULL,
    nombre_instancia VARCHAR(150) NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'planeada',
    fecha_inicio DATE NULL,
    fecha_fin DATE NULL,
    impacta_puntos TINYINT(1) NOT NULL DEFAULT 1,
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_torneo_bloques_torneo FOREIGN KEY (torneo_id) REFERENCES tb_torneos(id),
    CONSTRAINT fk_torneo_bloques_bloque FOREIGN KEY (bloque_id) REFERENCES tb_bloques_competencia(id),
    UNIQUE KEY uq_torneo_bloque_orden (torneo_id, orden),
    INDEX idx_torneo_bloques_bloque (bloque_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_torneo_bloque_grupos
-- -------------------------------------------------------------
CREATE TABLE tb_torneo_bloque_grupos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    torneo_bloque_id INT UNSIGNED NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    clave VARCHAR(30) NOT NULL,
    orden INT UNSIGNED NOT NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'activo',
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_bloque_grupos_bloque FOREIGN KEY (torneo_bloque_id) REFERENCES tb_torneo_bloques(id),
    UNIQUE KEY uq_bloque_grupo_clave (torneo_bloque_id, clave)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_torneo_bloque_equipos
-- -------------------------------------------------------------
CREATE TABLE tb_torneo_bloque_equipos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    torneo_bloque_id INT UNSIGNED NOT NULL,
    torneo_equipo_id INT UNSIGNED NOT NULL,
    grupo_id INT UNSIGNED NULL,
    orden_sorteo INT UNSIGNED NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'activo',
    fecha_alta DATETIME NULL,
    fecha_baja DATETIME NULL,
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_bloque_equipos_bloque FOREIGN KEY (torneo_bloque_id) REFERENCES tb_torneo_bloques(id),
    CONSTRAINT fk_bloque_equipos_torneo_equipo FOREIGN KEY (torneo_equipo_id) REFERENCES tb_torneo_equipos(id),
    CONSTRAINT fk_bloque_equipos_grupo FOREIGN KEY (grupo_id) REFERENCES tb_torneo_bloque_grupos(id),
    UNIQUE KEY uq_bloque_torneo_equipo (torneo_bloque_id, torneo_equipo_id),
    INDEX idx_bloque_equipos_grupo (grupo_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_torneo_jornadas
-- -------------------------------------------------------------
CREATE TABLE tb_torneo_jornadas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    torneo_id INT UNSIGNED NOT NULL,
    torneo_bloque_id INT UNSIGNED NOT NULL,
    numero INT UNSIGNED NOT NULL,
    nombre VARCHAR(100) NULL,
    fecha_programada DATE NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'planeada',
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_torneo_jornadas_torneo FOREIGN KEY (torneo_id) REFERENCES tb_torneos(id),
    CONSTRAINT fk_torneo_jornadas_bloque FOREIGN KEY (torneo_bloque_id) REFERENCES tb_torneo_bloques(id),
    UNIQUE KEY uq_torneo_jornada_numero (torneo_id, numero),
    INDEX idx_torneo_jornadas_bloque (torneo_bloque_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_bloque_configuracion
-- -------------------------------------------------------------
CREATE TABLE tb_bloque_configuracion (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    torneo_bloque_id INT UNSIGNED NOT NULL,
    clasificacion_por VARCHAR(30) NOT NULL,
    puntos_victoria DECIMAL(5,2) NOT NULL DEFAULT 3.00,
    puntos_empate DECIMAL(5,2) NOT NULL DEFAULT 1.00,
    puntos_derrota DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    permite_incompleto TINYINT(1) NOT NULL DEFAULT 0,
    define_campeon TINYINT(1) NOT NULL DEFAULT 0,
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_bloque_config_bloque FOREIGN KEY (torneo_bloque_id) REFERENCES tb_torneo_bloques(id),
    UNIQUE KEY uq_bloque_config (torneo_bloque_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_bloque_desempates
-- -------------------------------------------------------------
CREATE TABLE tb_bloque_desempates (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    torneo_bloque_id INT UNSIGNED NOT NULL,
    orden INT UNSIGNED NOT NULL,
    criterio VARCHAR(50) NOT NULL,
    asc_desc VARCHAR(4) NOT NULL DEFAULT 'DESC',
    activo TINYINT(1) NOT NULL DEFAULT 1,
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_bloque_desempates_bloque FOREIGN KEY (torneo_bloque_id) REFERENCES tb_torneo_bloques(id),
    UNIQUE KEY uq_bloque_desempate_orden (torneo_bloque_id, orden)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_bloque_avances
-- -------------------------------------------------------------
CREATE TABLE tb_bloque_avances (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    torneo_bloque_origen_id INT UNSIGNED NOT NULL,
    torneo_bloque_destino_id INT UNSIGNED NOT NULL,
    tipo_criterio VARCHAR(30) NOT NULL,
    cantidad INT UNSIGNED NULL,
    posicion_desde INT UNSIGNED NULL,
    posicion_hasta INT UNSIGNED NULL,
    grupo_id INT UNSIGNED NULL,
    metodo VARCHAR(50) NOT NULL,
    orden INT UNSIGNED NULL,
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_bloque_avances_origen FOREIGN KEY (torneo_bloque_origen_id) REFERENCES tb_torneo_bloques(id),
    CONSTRAINT fk_bloque_avances_destino FOREIGN KEY (torneo_bloque_destino_id) REFERENCES tb_torneo_bloques(id),
    CONSTRAINT fk_bloque_avances_grupo FOREIGN KEY (grupo_id) REFERENCES tb_torneo_bloque_grupos(id),
    INDEX idx_bloque_avances_origen (torneo_bloque_origen_id),
    INDEX idx_bloque_avances_destino (torneo_bloque_destino_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_afiliados
-- -------------------------------------------------------------
CREATE TABLE tb_afiliados (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombres VARCHAR(100) NOT NULL,
    apellido_paterno VARCHAR(100) NOT NULL,
    apellido_materno VARCHAR(100) NULL,
    fecha_nacimiento DATE NULL,
    sexo VARCHAR(20) NULL,
    nacionalidad VARCHAR(80) NULL,
    foto VARCHAR(255) NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'activo',
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_afiliados_nombre (apellido_paterno, apellido_materno, nombres),
    INDEX idx_afiliados_fecha_nacimiento (fecha_nacimiento)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_tipos_documento
-- -------------------------------------------------------------
CREATE TABLE tb_tipos_documento (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    clave VARCHAR(50) NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    categoria VARCHAR(50) NULL,
    descripcion TEXT NULL,
    requiere_numero TINYINT(1) NOT NULL DEFAULT 0,
    permite_vencimiento TINYINT(1) NOT NULL DEFAULT 0,
    estado VARCHAR(30) NOT NULL DEFAULT 'activo',
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_tipos_documento_clave (clave)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_afiliado_documentos
-- -------------------------------------------------------------
CREATE TABLE tb_afiliado_documentos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    afiliado_id INT UNSIGNED NOT NULL,
    tipo_documento_id INT UNSIGNED NOT NULL,
    archivo VARCHAR(255) NOT NULL,
    nombre_original VARCHAR(255) NULL,
    numero_documento VARCHAR(100) NULL,
    fecha_emision DATE NULL,
    fecha_vencimiento DATE NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'vigente',
    es_principal TINYINT(1) NOT NULL DEFAULT 0,
    fecha_carga DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    usuario_carga_id INT UNSIGNED NULL,
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_afiliado_doc_afiliado FOREIGN KEY (afiliado_id) REFERENCES tb_afiliados(id),
    CONSTRAINT fk_afiliado_doc_tipo FOREIGN KEY (tipo_documento_id) REFERENCES tb_tipos_documento(id),
    INDEX idx_afiliado_doc_afiliado (afiliado_id),
    INDEX idx_afiliado_doc_tipo (tipo_documento_id),
    INDEX idx_afiliado_doc_vigencia (fecha_vencimiento)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_afiliaciones
-- -------------------------------------------------------------
CREATE TABLE tb_afiliaciones (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    afiliado_id INT UNSIGNED NOT NULL,
    liga_equipo_id INT UNSIGNED NOT NULL,
    rol_id INT UNSIGNED NOT NULL,
    estado_afiliacion VARCHAR(30) NOT NULL DEFAULT 'inactiva',
    estado_tramite VARCHAR(40) NOT NULL DEFAULT 'no_iniciada',
    numero_afiliacion VARCHAR(50) NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NULL,
    referencia_registro_externo VARCHAR(100) NULL,
    fecha_registro_externo DATETIME NULL,
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_afiliaciones_afiliado FOREIGN KEY (afiliado_id) REFERENCES tb_afiliados(id),
    CONSTRAINT fk_afiliaciones_liga_equipo FOREIGN KEY (liga_equipo_id) REFERENCES tb_liga_equipos(id),
    CONSTRAINT fk_afiliaciones_rol_funcional FOREIGN KEY (rol_id) REFERENCES tb_roles_funcionales(id),
    INDEX idx_afiliaciones_afiliado (afiliado_id),
    INDEX idx_afiliaciones_liga_equipo (liga_equipo_id),
    INDEX idx_afiliaciones_estados (estado_afiliacion, estado_tramite),
    INDEX idx_afiliaciones_externo (referencia_registro_externo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_requisitos_documentales
-- -------------------------------------------------------------
CREATE TABLE tb_requisitos_documentales (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    organismo_id INT UNSIGNED NULL,
    tipo_proceso VARCHAR(40) NOT NULL,
    tipo_tramite VARCHAR(40) NULL,
    tipo_documento_id INT UNSIGNED NOT NULL,
    obligatorio TINYINT(1) NOT NULL DEFAULT 1,
    permite_reutilizacion TINYINT(1) NOT NULL DEFAULT 1,
    requiere_vigencia TINYINT(1) NOT NULL DEFAULT 0,
    dias_vigencia INT UNSIGNED NULL,
    fecha_inicio DATE NULL,
    fecha_fin DATE NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'activo',
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_requisitos_org FOREIGN KEY (organismo_id) REFERENCES tb_organismos(id),
    CONSTRAINT fk_requisitos_tipo_doc FOREIGN KEY (tipo_documento_id) REFERENCES tb_tipos_documento(id),
    INDEX idx_requisitos_proceso (tipo_proceso, tipo_tramite),
    INDEX idx_requisitos_org (organismo_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_tramites_afiliacion
-- -------------------------------------------------------------
CREATE TABLE tb_tramites_afiliacion (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    afiliacion_id INT UNSIGNED NOT NULL,
    tipo VARCHAR(30) NOT NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'en_proceso',
    fecha_inicio DATETIME NOT NULL,
    fecha_recepcion DATETIME NULL,
    fecha_revision DATETIME NULL,
    fecha_resolucion DATETIME NULL,
    usuario_recepcion_id INT UNSIGNED NULL,
    usuario_revision_id INT UNSIGNED NULL,
    usuario_resolucion_id INT UNSIGNED NULL,
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_tramite_afiliacion FOREIGN KEY (afiliacion_id) REFERENCES tb_afiliaciones(id),
    INDEX idx_tramites_afiliacion (afiliacion_id),
    INDEX idx_tramites_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_tramite_afiliacion_historial
-- -------------------------------------------------------------
CREATE TABLE tb_tramite_afiliacion_historial (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tramite_id INT UNSIGNED NOT NULL,
    estado_anterior VARCHAR(30) NULL,
    estado_nuevo VARCHAR(30) NOT NULL,
    fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    usuario_id INT UNSIGNED NULL,
    motivo VARCHAR(255) NULL,
    observaciones TEXT NULL,
    CONSTRAINT fk_tramite_hist_tramite FOREIGN KEY (tramite_id) REFERENCES tb_tramites_afiliacion(id),
    INDEX idx_tramite_historial_tramite (tramite_id, fecha)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_tramite_afiliacion_observaciones
-- -------------------------------------------------------------
CREATE TABLE tb_tramite_afiliacion_observaciones (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tramite_id INT UNSIGNED NOT NULL,
    tipo VARCHAR(30) NOT NULL,
    descripcion TEXT NOT NULL,
    documento_id INT UNSIGNED NULL,
    fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    usuario_id INT UNSIGNED NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'pendiente',
    fecha_atencion DATETIME NULL,
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_tramite_obs_tramite FOREIGN KEY (tramite_id) REFERENCES tb_tramites_afiliacion(id),
    CONSTRAINT fk_tramite_obs_doc FOREIGN KEY (documento_id) REFERENCES tb_afiliado_documentos(id),
    INDEX idx_tramite_obs_tramite (tramite_id),
    INDEX idx_tramite_obs_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_credenciales
-- -------------------------------------------------------------
CREATE TABLE tb_credenciales (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    afiliacion_id INT UNSIGNED NOT NULL,
    numero_credencial VARCHAR(50) NOT NULL,
    tipo VARCHAR(30) NOT NULL DEFAULT 'fisica',
    version INT UNSIGNED NOT NULL DEFAULT 1,
    estado VARCHAR(30) NOT NULL DEFAULT 'emitida',
    fecha_emision DATETIME NULL,
    fecha_impresion DATETIME NULL,
    fecha_vigencia_inicio DATE NULL,
    fecha_vigencia_fin DATE NULL,
    motivo_cancelacion VARCHAR(255) NULL,
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_credenciales_afiliacion FOREIGN KEY (afiliacion_id) REFERENCES tb_afiliaciones(id),
    UNIQUE KEY uq_credencial_numero (numero_credencial),
    INDEX idx_credenciales_afiliacion (afiliacion_id),
    INDEX idx_credenciales_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_credencial_movimientos
-- -------------------------------------------------------------
CREATE TABLE tb_credencial_movimientos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    credencial_id INT UNSIGNED NOT NULL,
    tipo_movimiento VARCHAR(30) NOT NULL,
    origen_tipo VARCHAR(30) NULL,
    origen_id INT UNSIGNED NULL,
    destino_tipo VARCHAR(30) NULL,
    destino_id INT UNSIGNED NULL,
    fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    usuario_id INT UNSIGNED NULL,
    documento_referencia VARCHAR(100) NULL,
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_cred_mov_cred FOREIGN KEY (credencial_id) REFERENCES tb_credenciales(id),
    INDEX idx_cred_mov_cred_fecha (credencial_id, fecha)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_entregas_credenciales
-- -------------------------------------------------------------
CREATE TABLE tb_entregas_credenciales (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    organismo_origen_id INT UNSIGNED NOT NULL,
    organismo_destino_id INT UNSIGNED NOT NULL,
    fecha_entrega DATETIME NOT NULL,
    usuario_entrega_id INT UNSIGNED NULL,
    recibido_por_afiliado_id INT UNSIGNED NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'pendiente',
    documento VARCHAR(255) NULL,
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_entrega_cred_org_origen FOREIGN KEY (organismo_origen_id) REFERENCES tb_organismos(id),
    CONSTRAINT fk_entrega_cred_org_destino FOREIGN KEY (organismo_destino_id) REFERENCES tb_organismos(id),
    CONSTRAINT fk_entrega_cred_recibido FOREIGN KEY (recibido_por_afiliado_id) REFERENCES tb_afiliados(id),
    INDEX idx_entregas_cred_fecha (fecha_entrega)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_entrega_credenciales_detalle
-- -------------------------------------------------------------
CREATE TABLE tb_entrega_credenciales_detalle (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    entrega_id INT UNSIGNED NOT NULL,
    credencial_id INT UNSIGNED NOT NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'entregada',
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_entrega_det_entrega FOREIGN KEY (entrega_id) REFERENCES tb_entregas_credenciales(id),
    CONSTRAINT fk_entrega_det_cred FOREIGN KEY (credencial_id) REFERENCES tb_credenciales(id),
    UNIQUE KEY uq_entrega_cred (entrega_id, credencial_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_bajas_afiliacion
-- -------------------------------------------------------------
CREATE TABLE tb_bajas_afiliacion (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    afiliacion_id INT UNSIGNED NOT NULL,
    tipo_baja VARCHAR(40) NOT NULL,
    motivo VARCHAR(100) NOT NULL,
    observaciones TEXT NULL,
    fecha_solicitud DATETIME NOT NULL,
    fecha_resolucion DATETIME NULL,
    fecha_efectiva DATE NULL,
    solicitante_tipo VARCHAR(30) NULL,
    solicitante_afiliado_id INT UNSIGNED NULL,
    usuario_revision_id INT UNSIGNED NULL,
    usuario_resolucion_id INT UNSIGNED NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'pendiente',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_baja_afiliacion FOREIGN KEY (afiliacion_id) REFERENCES tb_afiliaciones(id),
    CONSTRAINT fk_baja_solicitante FOREIGN KEY (solicitante_afiliado_id) REFERENCES tb_afiliados(id),
    INDEX idx_bajas_afiliacion (afiliacion_id),
    INDEX idx_bajas_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_reincorporaciones_afiliacion
-- -------------------------------------------------------------
CREATE TABLE tb_reincorporaciones_afiliacion (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    afiliacion_anterior_id INT UNSIGNED NOT NULL,
    nueva_afiliacion_id INT UNSIGNED NULL,
    motivo VARCHAR(100) NOT NULL,
    observaciones TEXT NULL,
    fecha_solicitud DATETIME NOT NULL,
    fecha_resolucion DATETIME NULL,
    usuario_revision_id INT UNSIGNED NULL,
    usuario_resolucion_id INT UNSIGNED NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'pendiente',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_reinc_afiliacion_anterior FOREIGN KEY (afiliacion_anterior_id) REFERENCES tb_afiliaciones(id),
    CONSTRAINT fk_reinc_nueva_afiliacion FOREIGN KEY (nueva_afiliacion_id) REFERENCES tb_afiliaciones(id),
    INDEX idx_reinc_anterior (afiliacion_anterior_id),
    INDEX idx_reinc_nueva (nueva_afiliacion_id),
    INDEX idx_reinc_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_reglas_baja
-- -------------------------------------------------------------
CREATE TABLE tb_reglas_baja (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    organismo_id INT UNSIGNED NULL,
    tipo_baja VARCHAR(40) NOT NULL,
    requiere_autorizacion TINYINT(1) NOT NULL DEFAULT 1,
    permite_solicitud_equipo TINYINT(1) NOT NULL DEFAULT 1,
    permite_solicitud_afec TINYINT(1) NOT NULL DEFAULT 1,
    bloquea_reincorporacion TINYINT(1) NOT NULL DEFAULT 0,
    periodo_restriccion_dias INT UNSIGNED NULL,
    requiere_documento TINYINT(1) NOT NULL DEFAULT 0,
    estado VARCHAR(30) NOT NULL DEFAULT 'activo',
    observaciones TEXT NULL,
    fecha_inicio DATE NULL,
    fecha_fin DATE NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_reglas_baja_org FOREIGN KEY (organismo_id) REFERENCES tb_organismos(id),
    INDEX idx_reglas_baja_org_tipo (organismo_id, tipo_baja)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_reglas_reincorporacion
-- -------------------------------------------------------------
CREATE TABLE tb_reglas_reincorporacion (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    organismo_id INT UNSIGNED NULL,
    tipo_reincorporacion VARCHAR(40) NOT NULL,
    requiere_revision_afec TINYINT(1) NOT NULL DEFAULT 1,
    verifica_participacion_actual TINYINT(1) NOT NULL DEFAULT 1,
    genera_alerta_participacion TINYINT(1) NOT NULL DEFAULT 1,
    permite_reutilizar_documentos TINYINT(1) NOT NULL DEFAULT 1,
    requiere_documentacion_nueva TINYINT(1) NOT NULL DEFAULT 0,
    requiere_autorizacion TINYINT(1) NOT NULL DEFAULT 1,
    estado VARCHAR(30) NOT NULL DEFAULT 'activo',
    observaciones TEXT NULL,
    fecha_inicio DATE NULL,
    fecha_fin DATE NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_reglas_reinc_org FOREIGN KEY (organismo_id) REFERENCES tb_organismos(id),
    INDEX idx_reglas_reinc_org_tipo (organismo_id, tipo_reincorporacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_reglas_refuerzo
-- -------------------------------------------------------------
CREATE TABLE tb_reglas_refuerzo (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    organismo_id INT UNSIGNED NULL,
    nombre VARCHAR(100) NOT NULL,
    equipo_mismo_club TINYINT(1) NOT NULL DEFAULT 1,
    requiere_afiliacion_activa TINYINT(1) NOT NULL DEFAULT 1,
    permite_descenso_categoria TINYINT(1) NOT NULL DEFAULT 0,
    permite_ascenso_categoria TINYINT(1) NOT NULL DEFAULT 1,
    requiere_misma_rama TINYINT(1) NOT NULL DEFAULT 1,
    permite_equipo_inferior TINYINT(1) NOT NULL DEFAULT 1,
    permite_equipo_superior TINYINT(1) NOT NULL DEFAULT 1,
    max_partidos INT UNSIGNED NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'activo',
    fecha_inicio DATE NULL,
    fecha_fin DATE NULL,
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_reglas_refuerzo_org FOREIGN KEY (organismo_id) REFERENCES tb_organismos(id),
    INDEX idx_reglas_refuerzo_org (organismo_id, estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_tipos_incidencia
-- -------------------------------------------------------------
CREATE TABLE tb_tipos_incidencia (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    clave VARCHAR(50) NOT NULL,
    categoria VARCHAR(50) NULL,
    requiere_revision TINYINT(1) NOT NULL DEFAULT 1,
    estado VARCHAR(30) NOT NULL DEFAULT 'activo',
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_tipos_inc_clave (clave)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_incidencias
-- -------------------------------------------------------------
CREATE TABLE tb_incidencias (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tipo_incidencia_id INT UNSIGNED NOT NULL,
    partido_id INT UNSIGNED NULL,
    origen VARCHAR(40) NOT NULL,
    fecha_incidencia DATETIME NOT NULL,
    fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    descripcion TEXT NOT NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'registrada',
    documento_origen VARCHAR(255) NULL,
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_incidencias_tipo FOREIGN KEY (tipo_incidencia_id) REFERENCES tb_tipos_incidencia(id),
    INDEX idx_incidencias_tipo (tipo_incidencia_id),
    INDEX idx_incidencias_partido (partido_id),
    INDEX idx_incidencias_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_incidencia_sujetos
-- -------------------------------------------------------------
CREATE TABLE tb_incidencia_sujetos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    incidencia_id INT UNSIGNED NOT NULL,
    afiliado_id INT UNSIGNED NULL,
    equipo_id INT UNSIGNED NULL,
    tipo_sujeto VARCHAR(30) NOT NULL,
    rol_en_incidente VARCHAR(50) NULL,
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_inc_sujetos_inc FOREIGN KEY (incidencia_id) REFERENCES tb_incidencias(id),
    CONSTRAINT fk_inc_sujetos_afiliado FOREIGN KEY (afiliado_id) REFERENCES tb_afiliados(id),
    CONSTRAINT fk_inc_sujetos_equipo FOREIGN KEY (equipo_id) REFERENCES tb_equipos(id),
    INDEX idx_inc_sujetos_inc (incidencia_id),
    INDEX idx_inc_sujetos_afiliado (afiliado_id),
    INDEX idx_inc_sujetos_equipo (equipo_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_resoluciones
-- -------------------------------------------------------------
CREATE TABLE tb_resoluciones (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    incidencia_id INT UNSIGNED NOT NULL,
    autoridad VARCHAR(50) NOT NULL,
    usuario_resolucion_id INT UNSIGNED NULL,
    fecha_resolucion DATETIME NOT NULL,
    fundamento TEXT NULL,
    hechos_acreditados TEXT NULL,
    determinacion TEXT NOT NULL,
    efectos_deportivos TEXT NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'emitida',
    fecha_efectiva DATETIME NULL,
    documento VARCHAR(255) NULL,
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_resoluciones_inc FOREIGN KEY (incidencia_id) REFERENCES tb_incidencias(id),
    INDEX idx_resoluciones_inc (incidencia_id),
    INDEX idx_resoluciones_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_sanciones
-- -------------------------------------------------------------
CREATE TABLE tb_sanciones (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    resolucion_id INT UNSIGNED NOT NULL,
    afiliado_id INT UNSIGNED NULL,
    equipo_id INT UNSIGNED NULL,
    tipo_sancion VARCHAR(50) NOT NULL,
    descripcion TEXT NOT NULL,
    fecha_inicio DATE NULL,
    fecha_fin DATE NULL,
    cantidad_partidos INT UNSIGNED NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'activa',
    motivo TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_sanciones_res FOREIGN KEY (resolucion_id) REFERENCES tb_resoluciones(id),
    CONSTRAINT fk_sanciones_afiliado FOREIGN KEY (afiliado_id) REFERENCES tb_afiliados(id),
    CONSTRAINT fk_sanciones_equipo FOREIGN KEY (equipo_id) REFERENCES tb_equipos(id),
    INDEX idx_sanciones_afiliado_estado (afiliado_id, estado),
    INDEX idx_sanciones_equipo_estado (equipo_id, estado),
    INDEX idx_sanciones_fechas (fecha_inicio, fecha_fin)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_sancion_cumplimientos
-- -------------------------------------------------------------
CREATE TABLE tb_sancion_cumplimientos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sancion_id INT UNSIGNED NOT NULL,
    partido_id INT UNSIGNED NULL,
    fecha DATETIME NOT NULL,
    tipo_cumplimiento VARCHAR(30) NOT NULL,
    cantidad DECIMAL(8,2) NULL,
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_sancion_cumpl_sancion FOREIGN KEY (sancion_id) REFERENCES tb_sanciones(id),
    INDEX idx_sancion_cumpl_sancion (sancion_id),
    INDEX idx_sancion_cumpl_partido (partido_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_protestas
-- -------------------------------------------------------------
CREATE TABLE tb_protestas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    equipo_id INT UNSIGNED NOT NULL,
    partido_id INT UNSIGNED NULL,
    incidencia_id INT UNSIGNED NULL,
    fecha_presentacion DATETIME NOT NULL,
    tipo VARCHAR(40) NOT NULL,
    descripcion TEXT NOT NULL,
    fundamento TEXT NULL,
    fecha_limite_ratificacion DATETIME NULL,
    fecha_ratificacion DATETIME NULL,
    cuota DECIMAL(12,2) NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'presentada',
    documento VARCHAR(255) NULL,
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_protestas_equipo FOREIGN KEY (equipo_id) REFERENCES tb_equipos(id),
    CONSTRAINT fk_protestas_incidencia FOREIGN KEY (incidencia_id) REFERENCES tb_incidencias(id),
    INDEX idx_protestas_partido (partido_id),
    INDEX idx_protestas_equipo (equipo_id),
    INDEX idx_protestas_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_apelaciones
-- -------------------------------------------------------------
CREATE TABLE tb_apelaciones (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    resolucion_id INT UNSIGNED NOT NULL,
    resolucion_resultante_id INT UNSIGNED NULL,
    apelante_tipo VARCHAR(30) NOT NULL,
    apelante_afiliado_id INT UNSIGNED NULL,
    apelante_equipo_id INT UNSIGNED NULL,
    autoridad_origen VARCHAR(50) NOT NULL,
    autoridad_destino VARCHAR(50) NOT NULL,
    fecha_presentacion DATETIME NOT NULL,
    fecha_limite DATETIME NULL,
    motivo TEXT NOT NULL,
    peticion TEXT NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'presentada',
    documento VARCHAR(255) NULL,
    fecha_resolucion DATETIME NULL,
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_apelaciones_res FOREIGN KEY (resolucion_id) REFERENCES tb_resoluciones(id),
    CONSTRAINT fk_apelaciones_res_resultado FOREIGN KEY (resolucion_resultante_id) REFERENCES tb_resoluciones(id),
    CONSTRAINT fk_apelaciones_afiliado FOREIGN KEY (apelante_afiliado_id) REFERENCES tb_afiliados(id),
    CONSTRAINT fk_apelaciones_equipo FOREIGN KEY (apelante_equipo_id) REFERENCES tb_equipos(id),
    INDEX idx_apelaciones_res (resolucion_id),
    INDEX idx_apelaciones_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_colegios_arbitros
-- -------------------------------------------------------------
CREATE TABLE tb_colegios_arbitros (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    siglas VARCHAR(30) NULL,
    correo VARCHAR(150) NULL,
    telefono VARCHAR(30) NULL,
    direccion VARCHAR(255) NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'activo',
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_colegios_nombre (nombre),
    INDEX idx_colegios_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_colegio_organismos
-- -------------------------------------------------------------
CREATE TABLE tb_colegio_organismos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    colegio_id INT UNSIGNED NOT NULL,
    organismo_id INT UNSIGNED NOT NULL,
    tipo_relacion VARCHAR(30) NOT NULL,
    fecha_inicio DATE NULL,
    fecha_fin DATE NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'activo',
    referencia VARCHAR(100) NULL,
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_colegio_org_colegio FOREIGN KEY (colegio_id) REFERENCES tb_colegios_arbitros(id),
    CONSTRAINT fk_colegio_org_organismo FOREIGN KEY (organismo_id) REFERENCES tb_organismos(id),
    INDEX idx_colegio_org_colegio (colegio_id),
    INDEX idx_colegio_org_organismo (organismo_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_colegio_afiliados
-- -------------------------------------------------------------
CREATE TABLE tb_colegio_afiliados (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    colegio_id INT UNSIGNED NOT NULL,
    afiliado_id INT UNSIGNED NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'activo',
    numero_registro VARCHAR(50) NULL,
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_colegio_afiliados_colegio FOREIGN KEY (colegio_id) REFERENCES tb_colegios_arbitros(id),
    CONSTRAINT fk_colegio_afiliados_afiliado FOREIGN KEY (afiliado_id) REFERENCES tb_afiliados(id),
    INDEX idx_colegio_afiliados_colegio (colegio_id),
    INDEX idx_colegio_afiliados_afiliado (afiliado_id),
    INDEX idx_colegio_afiliados_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_categorias_arbitrales
-- -------------------------------------------------------------
CREATE TABLE tb_categorias_arbitrales (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    colegio_id INT UNSIGNED NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    clave VARCHAR(30) NOT NULL,
    orden INT UNSIGNED NULL,
    descripcion TEXT NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'activo',
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_cat_arbit_colegio FOREIGN KEY (colegio_id) REFERENCES tb_colegios_arbitros(id),
    UNIQUE KEY uq_cat_arbit_colegio_clave (colegio_id, clave)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_arbitro_categorias
-- -------------------------------------------------------------
CREATE TABLE tb_arbitro_categorias (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    colegio_afiliado_id INT UNSIGNED NOT NULL,
    categoria_arbitral_id INT UNSIGNED NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'activo',
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_arbitro_cat_afiliado FOREIGN KEY (colegio_afiliado_id) REFERENCES tb_colegio_afiliados(id),
    CONSTRAINT fk_arbitro_cat_categoria FOREIGN KEY (categoria_arbitral_id) REFERENCES tb_categorias_arbitrales(id),
    INDEX idx_arbitro_cat_afiliado (colegio_afiliado_id),
    INDEX idx_arbitro_cat_categoria (categoria_arbitral_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_arbitro_disponibilidad
-- -------------------------------------------------------------
CREATE TABLE tb_arbitro_disponibilidad (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    colegio_afiliado_id INT UNSIGNED NOT NULL,
    fecha DATE NOT NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'sin_informacion',
    hora_inicio TIME NULL,
    hora_fin TIME NULL,
    motivo VARCHAR(255) NULL,
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_arbitro_disp_afiliado FOREIGN KEY (colegio_afiliado_id) REFERENCES tb_colegio_afiliados(id),
    UNIQUE KEY uq_arbitro_disp_fecha (colegio_afiliado_id, fecha),
    INDEX idx_arbitro_disp_fecha (fecha, estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_colegio_cargos
-- -------------------------------------------------------------
CREATE TABLE tb_colegio_cargos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    colegio_id INT UNSIGNED NOT NULL,
    afiliado_id INT UNSIGNED NOT NULL,
    rol_funcional_id INT UNSIGNED NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'activo',
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_colegio_cargos_colegio FOREIGN KEY (colegio_id) REFERENCES tb_colegios_arbitros(id),
    CONSTRAINT fk_colegio_cargos_afiliado FOREIGN KEY (afiliado_id) REFERENCES tb_afiliados(id),
    CONSTRAINT fk_colegio_cargos_rol FOREIGN KEY (rol_funcional_id) REFERENCES tb_roles_funcionales(id),
    INDEX idx_colegio_cargos_colegio (colegio_id),
    INDEX idx_colegio_cargos_rol (rol_funcional_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_organismo_cargos
-- -------------------------------------------------------------
CREATE TABLE tb_organismo_cargos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    organismo_id INT UNSIGNED NOT NULL,
    afiliado_id INT UNSIGNED NOT NULL,
    rol_funcional_id INT UNSIGNED NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'activo',
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_org_cargos_organismo FOREIGN KEY (organismo_id) REFERENCES tb_organismos(id),
    CONSTRAINT fk_org_cargos_afiliado FOREIGN KEY (afiliado_id) REFERENCES tb_afiliados(id),
    CONSTRAINT fk_org_cargos_rol FOREIGN KEY (rol_funcional_id) REFERENCES tb_roles_funcionales(id),
    INDEX idx_org_cargos_organismo (organismo_id),
    INDEX idx_org_cargos_rol (rol_funcional_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_colegio_tarifas
-- -------------------------------------------------------------
CREATE TABLE tb_colegio_tarifas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    colegio_id INT UNSIGNED NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    tipo_servicio VARCHAR(30) NOT NULL,
    categoria_arbitral_id INT UNSIGNED NULL,
    zona VARCHAR(50) NULL,
    importe_servicio DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    importe_viatico DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    moneda VARCHAR(10) NOT NULL DEFAULT 'MXN',
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'activo',
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_colegio_tarifa_colegio FOREIGN KEY (colegio_id) REFERENCES tb_colegios_arbitros(id),
    CONSTRAINT fk_colegio_tarifa_categoria FOREIGN KEY (categoria_arbitral_id) REFERENCES tb_categorias_arbitrales(id),
    INDEX idx_colegio_tarifa_vigencia (colegio_id, fecha_inicio, fecha_fin)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_partidos
-- -------------------------------------------------------------
CREATE TABLE tb_partidos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    torneo_id INT UNSIGNED NOT NULL,
    torneo_bloque_id INT UNSIGNED NOT NULL,
    jornada_id INT UNSIGNED NOT NULL,
    local_torneo_equipo_id INT UNSIGNED NOT NULL,
    visitante_torneo_equipo_id INT UNSIGNED NOT NULL,
    unidad_id INT UNSIGNED NULL,
    campo_id INT UNSIGNED NULL,
    fecha_programada DATE NULL,
    hora_programada TIME NULL,
    duracion_estimada SMALLINT UNSIGNED NULL,
    estado_programacion VARCHAR(30) NOT NULL DEFAULT 'pendiente',
    estado VARCHAR(30) NOT NULL DEFAULT 'programado',
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_partidos_torneo FOREIGN KEY (torneo_id) REFERENCES tb_torneos(id),
    CONSTRAINT fk_partidos_bloque FOREIGN KEY (torneo_bloque_id) REFERENCES tb_torneo_bloques(id),
    CONSTRAINT fk_partidos_jornada FOREIGN KEY (jornada_id) REFERENCES tb_torneo_jornadas(id),
    CONSTRAINT fk_partidos_local FOREIGN KEY (local_torneo_equipo_id) REFERENCES tb_torneo_equipos(id),
    CONSTRAINT fk_partidos_visitante FOREIGN KEY (visitante_torneo_equipo_id) REFERENCES tb_torneo_equipos(id),
    CONSTRAINT fk_partidos_unidad FOREIGN KEY (unidad_id) REFERENCES tb_unidades_deportivas(id),
    CONSTRAINT fk_partidos_campo FOREIGN KEY (campo_id) REFERENCES tb_campos_deportivos(id),
    INDEX idx_partidos_jornada (jornada_id),
    INDEX idx_partidos_fecha (fecha_programada, hora_programada),
    INDEX idx_partidos_estado (estado),
    INDEX idx_partidos_local (local_torneo_equipo_id),
    INDEX idx_partidos_visitante (visitante_torneo_equipo_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_partido_programaciones
-- -------------------------------------------------------------
CREATE TABLE tb_partido_programaciones (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    partido_id INT UNSIGNED NOT NULL,
    version INT UNSIGNED NOT NULL,
    fecha_programada DATE NULL,
    hora_programada TIME NULL,
    unidad_id INT UNSIGNED NULL,
    campo_id INT UNSIGNED NULL,
    motivo VARCHAR(255) NULL,
    usuario_id INT UNSIGNED NULL,
    fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    estado VARCHAR(30) NOT NULL,
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_partido_prog_partido FOREIGN KEY (partido_id) REFERENCES tb_partidos(id),
    CONSTRAINT fk_partido_prog_unidad FOREIGN KEY (unidad_id) REFERENCES tb_unidades_deportivas(id),
    CONSTRAINT fk_partido_prog_campo FOREIGN KEY (campo_id) REFERENCES tb_campos_deportivos(id),
    UNIQUE KEY uq_partido_programacion_version (partido_id, version),
    INDEX idx_partido_programacion_fecha (fecha_programada, hora_programada)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_partido_designaciones
-- -------------------------------------------------------------
CREATE TABLE tb_partido_designaciones (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    partido_id INT UNSIGNED NOT NULL,
    colegio_id INT UNSIGNED NOT NULL,
    colegio_afiliado_id INT UNSIGNED NULL,
    funcion VARCHAR(30) NOT NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'propuesta',
    designador_afiliado_id INT UNSIGNED NULL,
    fecha_designacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_respuesta DATETIME NULL,
    designacion_anterior_id BIGINT UNSIGNED NULL,
    motivo_rechazo VARCHAR(255) NULL,
    motivo_sustitucion VARCHAR(255) NULL,
    fecha_sustitucion DATETIME NULL,
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_designaciones_partido FOREIGN KEY (partido_id) REFERENCES tb_partidos(id),
    CONSTRAINT fk_designaciones_colegio FOREIGN KEY (colegio_id) REFERENCES tb_colegios_arbitros(id),
    CONSTRAINT fk_designaciones_arbitro FOREIGN KEY (colegio_afiliado_id) REFERENCES tb_colegio_afiliados(id),
    CONSTRAINT fk_designaciones_designador FOREIGN KEY (designador_afiliado_id) REFERENCES tb_afiliados(id),
    INDEX idx_designaciones_partido (partido_id),
    INDEX idx_designaciones_colegio (colegio_id),
    INDEX idx_designaciones_arbitro (colegio_afiliado_id),
    INDEX idx_designaciones_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_cedulas_partido
-- -------------------------------------------------------------
CREATE TABLE tb_cedulas_partido (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    partido_id INT UNSIGNED NOT NULL,
    version INT UNSIGNED NOT NULL DEFAULT 1,
    folio VARCHAR(50) NULL,
    arbitro_responsable_id INT UNSIGNED NOT NULL,
    fecha DATETIME NOT NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'pendiente',
    documento VARCHAR(255) NULL,
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_cedulas_partido FOREIGN KEY (partido_id) REFERENCES tb_partidos(id),
    CONSTRAINT fk_cedulas_arbitro FOREIGN KEY (arbitro_responsable_id) REFERENCES tb_afiliados(id),
    UNIQUE KEY uq_cedula_partido_version (partido_id, version),
    INDEX idx_cedulas_partido (partido_id),
    INDEX idx_cedulas_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_partido_participantes
-- -------------------------------------------------------------
CREATE TABLE tb_partido_participantes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    partido_id INT UNSIGNED NOT NULL,
    torneo_equipo_id INT UNSIGNED NOT NULL,
    afiliado_id INT UNSIGNED NOT NULL,
    afiliacion_id INT UNSIGNED NOT NULL,
    tipo_participacion VARCHAR(30) NOT NULL,
    titular TINYINT(1) NOT NULL DEFAULT 0,
    capitan TINYINT(1) NOT NULL DEFAULT 0,
    numero_dorsal VARCHAR(10) NULL,
    estado_elegibilidad VARCHAR(30) NOT NULL DEFAULT 'requiere_revision',
    resultado_validacion VARCHAR(255) NULL,
    minuto_entrada SMALLINT UNSIGNED NULL,
    minuto_salida SMALLINT UNSIGNED NULL,
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_participantes_partido FOREIGN KEY (partido_id) REFERENCES tb_partidos(id),
    CONSTRAINT fk_participantes_torneo_equipo FOREIGN KEY (torneo_equipo_id) REFERENCES tb_torneo_equipos(id),
    CONSTRAINT fk_participantes_afiliado FOREIGN KEY (afiliado_id) REFERENCES tb_afiliados(id),
    CONSTRAINT fk_participantes_afiliacion FOREIGN KEY (afiliacion_id) REFERENCES tb_afiliaciones(id),
    UNIQUE KEY uq_participante_partido_afiliado (partido_id, afiliado_id),
    INDEX idx_participantes_partido_equipo (partido_id, torneo_equipo_id),
    INDEX idx_participantes_afiliado (afiliado_id),
    INDEX idx_participantes_afiliacion (afiliacion_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_partido_refuerzos
-- -------------------------------------------------------------
CREATE TABLE tb_partido_refuerzos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    partido_participante_id BIGINT UNSIGNED NOT NULL,
    equipo_origen_id INT UNSIGNED NOT NULL,
    equipo_destino_id INT UNSIGNED NOT NULL,
    regla_aplicada VARCHAR(100) NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'habilitado',
    autorizado_por INT UNSIGNED NULL,
    fecha_autorizacion DATETIME NULL,
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_refuerzos_participante FOREIGN KEY (partido_participante_id) REFERENCES tb_partido_participantes(id),
    CONSTRAINT fk_refuerzos_equipo_origen FOREIGN KEY (equipo_origen_id) REFERENCES tb_equipos(id),
    CONSTRAINT fk_refuerzos_equipo_destino FOREIGN KEY (equipo_destino_id) REFERENCES tb_equipos(id),
    INDEX idx_refuerzos_participante (partido_participante_id),
    INDEX idx_refuerzos_equipos (equipo_origen_id, equipo_destino_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_partido_eventos
-- -------------------------------------------------------------
CREATE TABLE tb_partido_eventos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    partido_id INT UNSIGNED NOT NULL,
    tipo_evento VARCHAR(30) NOT NULL,
    minuto SMALLINT UNSIGNED NULL,
    segundo SMALLINT UNSIGNED NULL,
    torneo_equipo_id INT UNSIGNED NULL,
    participante_id BIGINT UNSIGNED NULL,
    participante_relacionado_id BIGINT UNSIGNED NULL,
    descripcion TEXT NULL,
    origen VARCHAR(30) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_eventos_partido FOREIGN KEY (partido_id) REFERENCES tb_partidos(id),
    CONSTRAINT fk_eventos_torneo_equipo FOREIGN KEY (torneo_equipo_id) REFERENCES tb_torneo_equipos(id),
    CONSTRAINT fk_eventos_participante FOREIGN KEY (participante_id) REFERENCES tb_partido_participantes(id),
    CONSTRAINT fk_eventos_participante_rel FOREIGN KEY (participante_relacionado_id) REFERENCES tb_partido_participantes(id),
    INDEX idx_eventos_partido_minuto (partido_id, minuto, segundo),
    INDEX idx_eventos_participante (participante_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_partido_resultados
-- -------------------------------------------------------------
CREATE TABLE tb_partido_resultados (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    partido_id INT UNSIGNED NOT NULL,
    version INT UNSIGNED NOT NULL,
    goles_local SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    goles_visitante SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    condicion_resultado VARCHAR(30) NOT NULL DEFAULT 'normal',
    estado VARCHAR(30) NOT NULL DEFAULT 'capturado',
    cedula_id INT UNSIGNED NULL,
    fecha_captura DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_autorizacion DATETIME NULL,
    usuario_captura_id INT UNSIGNED NULL,
    usuario_autorizacion_id INT UNSIGNED NULL,
    observaciones TEXT NULL,
    CONSTRAINT fk_resultados_partido FOREIGN KEY (partido_id) REFERENCES tb_partidos(id),
    CONSTRAINT fk_resultados_cedula FOREIGN KEY (cedula_id) REFERENCES tb_cedulas_partido(id),
    UNIQUE KEY uq_resultado_version (partido_id, version),
    INDEX idx_resultados_partido_estado (partido_id, estado),
    INDEX idx_resultados_autorizacion (fecha_autorizacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_partido_segmentos
-- -------------------------------------------------------------
CREATE TABLE tb_partido_segmentos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    partido_id INT UNSIGNED NOT NULL,
    numero INT UNSIGNED NOT NULL,
    fecha_inicio DATE NULL,
    hora_inicio TIME NULL,
    minuto_inicio SMALLINT UNSIGNED NULL,
    minuto_fin SMALLINT UNSIGNED NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'programado',
    motivo VARCHAR(255) NULL,
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_segmentos_partido FOREIGN KEY (partido_id) REFERENCES tb_partidos(id),
    UNIQUE KEY uq_segmento_partido_numero (partido_id, numero)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_clasificaciones
-- -------------------------------------------------------------
CREATE TABLE tb_clasificaciones (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    torneo_bloque_id INT UNSIGNED NOT NULL,
    grupo_id INT UNSIGNED NULL,
    nombre VARCHAR(150) NOT NULL,
    tipo VARCHAR(30) NOT NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'activa',
    fecha_corte DATETIME NULL,
    version INT UNSIGNED NOT NULL DEFAULT 1,
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_clasificaciones_bloque FOREIGN KEY (torneo_bloque_id) REFERENCES tb_torneo_bloques(id),
    CONSTRAINT fk_clasificaciones_grupo FOREIGN KEY (grupo_id) REFERENCES tb_torneo_bloque_grupos(id),
    INDEX idx_clasificaciones_bloque (torneo_bloque_id),
    INDEX idx_clasificaciones_grupo (grupo_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_clasificacion_equipos
-- -------------------------------------------------------------
CREATE TABLE tb_clasificacion_equipos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    clasificacion_id INT UNSIGNED NOT NULL,
    torneo_equipo_id INT UNSIGNED NOT NULL,
    pj INT UNSIGNED NOT NULL DEFAULT 0,
    pg INT UNSIGNED NOT NULL DEFAULT 0,
    pe INT UNSIGNED NOT NULL DEFAULT 0,
    pp INT UNSIGNED NOT NULL DEFAULT 0,
    gf INT UNSIGNED NOT NULL DEFAULT 0,
    gc INT UNSIGNED NOT NULL DEFAULT 0,
    dg INT NOT NULL DEFAULT 0,
    pts DECIMAL(8,2) NOT NULL DEFAULT 0.00,
    posicion INT UNSIGNED NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'vigente',
    version INT UNSIGNED NOT NULL DEFAULT 1,
    fecha_calculo DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_clasif_equipos_clasif FOREIGN KEY (clasificacion_id) REFERENCES tb_clasificaciones(id),
    CONSTRAINT fk_clasif_equipos_torneo_equipo FOREIGN KEY (torneo_equipo_id) REFERENCES tb_torneo_equipos(id),
    UNIQUE KEY uq_clasif_equipo_version (clasificacion_id, torneo_equipo_id, version),
    INDEX idx_clasif_equipos_pos (clasificacion_id, posicion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_proveedores
-- -------------------------------------------------------------
CREATE TABLE tb_proveedores (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    razon_social VARCHAR(200) NULL,
    rfc VARCHAR(20) NULL,
    tipo VARCHAR(30) NULL,
    correo VARCHAR(150) NULL,
    telefono VARCHAR(30) NULL,
    direccion VARCHAR(255) NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'activo',
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_proveedores_nombre (nombre),
    INDEX idx_proveedores_rfc (rfc)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_cuentas_financieras
-- -------------------------------------------------------------
CREATE TABLE tb_cuentas_financieras (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    organismo_id INT UNSIGNED NOT NULL,
    tipo_entidad VARCHAR(30) NOT NULL,
    entidad_id INT UNSIGNED NOT NULL,
    numero_cuenta VARCHAR(50) NOT NULL,
    nombre VARCHAR(150) NOT NULL,
    moneda VARCHAR(10) NOT NULL DEFAULT 'MXN',
    estado VARCHAR(30) NOT NULL DEFAULT 'activa',
    fecha_apertura DATE NOT NULL,
    fecha_cierre DATE NULL,
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_cuentas_organismo FOREIGN KEY (organismo_id) REFERENCES tb_organismos(id),
    UNIQUE KEY uq_cuenta_numero_org (organismo_id, numero_cuenta),
    INDEX idx_cuentas_entidad (tipo_entidad, entidad_id),
    INDEX idx_cuentas_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_conceptos_financieros
-- -------------------------------------------------------------
CREATE TABLE tb_conceptos_financieros (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    organismo_id INT UNSIGNED NULL,
    clave VARCHAR(50) NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    tipo VARCHAR(30) NOT NULL,
    naturaleza VARCHAR(20) NOT NULL,
    requiere_tarifa TINYINT(1) NOT NULL DEFAULT 1,
    permite_condonacion TINYINT(1) NOT NULL DEFAULT 0,
    permite_reembolso TINYINT(1) NOT NULL DEFAULT 0,
    estado VARCHAR(30) NOT NULL DEFAULT 'activo',
    descripcion TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_conceptos_fin_org FOREIGN KEY (organismo_id) REFERENCES tb_organismos(id),
    UNIQUE KEY uq_conceptos_fin_org_clave (organismo_id, clave),
    INDEX idx_conceptos_fin_tipo (tipo, naturaleza)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_tarifas_financieras
-- -------------------------------------------------------------
CREATE TABLE tb_tarifas_financieras (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    concepto_id INT UNSIGNED NOT NULL,
    organismo_id INT UNSIGNED NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    importe DECIMAL(12,2) NOT NULL,
    unidad VARCHAR(30) NOT NULL DEFAULT 'unidad',
    cantidad_minima DECIMAL(12,2) NULL,
    cantidad_maxima DECIMAL(12,2) NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'activo',
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_tarifas_fin_concepto FOREIGN KEY (concepto_id) REFERENCES tb_conceptos_financieros(id),
    CONSTRAINT fk_tarifas_fin_org FOREIGN KEY (organismo_id) REFERENCES tb_organismos(id),
    INDEX idx_tarifas_fin_vigencia (organismo_id, concepto_id, fecha_inicio, fecha_fin)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_obligaciones_financieras
-- -------------------------------------------------------------
CREATE TABLE tb_obligaciones_financieras (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cuenta_id INT UNSIGNED NOT NULL,
    concepto_id INT UNSIGNED NOT NULL,
    torneo_id INT UNSIGNED NULL,
    partido_id INT UNSIGNED NULL,
    proveedor_id INT UNSIGNED NULL,
    referencia VARCHAR(100) NULL,
    descripcion TEXT NOT NULL,
    cantidad DECIMAL(12,2) NOT NULL DEFAULT 1.00,
    importe_unitario DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    importe_total DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    saldo_pendiente DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    fecha_generacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_vencimiento DATE NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'pendiente',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_obligaciones_cuenta FOREIGN KEY (cuenta_id) REFERENCES tb_cuentas_financieras(id),
    CONSTRAINT fk_obligaciones_concepto FOREIGN KEY (concepto_id) REFERENCES tb_conceptos_financieros(id),
    CONSTRAINT fk_obligaciones_torneo FOREIGN KEY (torneo_id) REFERENCES tb_torneos(id),
    CONSTRAINT fk_obligaciones_partido FOREIGN KEY (partido_id) REFERENCES tb_partidos(id),
    CONSTRAINT fk_obligaciones_proveedor FOREIGN KEY (proveedor_id) REFERENCES tb_proveedores(id),
    INDEX idx_obligaciones_cuenta_estado (cuenta_id, estado),
    INDEX idx_obligaciones_vencimiento (fecha_vencimiento, estado),
    INDEX idx_obligaciones_referencia (referencia)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_ordenes_pago
-- -------------------------------------------------------------
CREATE TABLE tb_ordenes_pago (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cuenta_id INT UNSIGNED NOT NULL,
    numero_orden VARCHAR(50) NOT NULL,
    tipo VARCHAR(30) NOT NULL DEFAULT 'normal',
    fecha_emision DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_vencimiento DATE NULL,
    importe_total DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    moneda VARCHAR(10) NOT NULL DEFAULT 'MXN',
    estado VARCHAR(30) NOT NULL DEFAULT 'emitida',
    usuario_generacion_id INT UNSIGNED NULL,
    fecha_cancelacion DATETIME NULL,
    motivo_cancelacion TEXT NULL,
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_ordenes_pago_cuenta FOREIGN KEY (cuenta_id) REFERENCES tb_cuentas_financieras(id),
    UNIQUE KEY uq_orden_numero_cuenta (cuenta_id, numero_orden),
    INDEX idx_ordenes_pago_estado (estado),
    INDEX idx_ordenes_pago_vencimiento (fecha_vencimiento)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_orden_partidas
-- -------------------------------------------------------------
CREATE TABLE tb_orden_partidas (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    orden_id BIGINT UNSIGNED NOT NULL,
    obligacion_id BIGINT UNSIGNED NULL,
    concepto_id INT UNSIGNED NOT NULL,
    descripcion VARCHAR(255) NOT NULL,
    cantidad DECIMAL(12,2) NOT NULL DEFAULT 1.00,
    importe_unitario DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    importe_total DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_orden_partidas_orden FOREIGN KEY (orden_id) REFERENCES tb_ordenes_pago(id),
    CONSTRAINT fk_orden_partidas_obligacion FOREIGN KEY (obligacion_id) REFERENCES tb_obligaciones_financieras(id),
    CONSTRAINT fk_orden_partidas_concepto FOREIGN KEY (concepto_id) REFERENCES tb_conceptos_financieros(id),
    INDEX idx_orden_partidas_orden (orden_id),
    INDEX idx_orden_partidas_obligacion (obligacion_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_pagos
-- -------------------------------------------------------------
CREATE TABLE tb_pagos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cuenta_id INT UNSIGNED NOT NULL,
    orden_id BIGINT UNSIGNED NULL,
    fecha_pago DATETIME NOT NULL,
    importe DECIMAL(12,2) NOT NULL,
    moneda VARCHAR(10) NOT NULL DEFAULT 'MXN',
    metodo_pago VARCHAR(30) NOT NULL,
    referencia VARCHAR(100) NULL,
    comprobante VARCHAR(255) NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'reportado',
    usuario_registro_id INT UNSIGNED NULL,
    fecha_validacion DATETIME NULL,
    usuario_validacion_id INT UNSIGNED NULL,
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_pagos_cuenta FOREIGN KEY (cuenta_id) REFERENCES tb_cuentas_financieras(id),
    CONSTRAINT fk_pagos_orden FOREIGN KEY (orden_id) REFERENCES tb_ordenes_pago(id),
    INDEX idx_pagos_cuenta_fecha (cuenta_id, fecha_pago),
    INDEX idx_pagos_estado (estado),
    INDEX idx_pagos_referencia (referencia)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_aplicaciones_pago
-- -------------------------------------------------------------
CREATE TABLE tb_aplicaciones_pago (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pago_id BIGINT UNSIGNED NOT NULL,
    obligacion_id BIGINT UNSIGNED NOT NULL,
    importe_aplicado DECIMAL(12,2) NOT NULL,
    fecha_aplicacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    usuario_id INT UNSIGNED NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'aplicada',
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_aplic_pago_pago FOREIGN KEY (pago_id) REFERENCES tb_pagos(id),
    CONSTRAINT fk_aplic_pago_obligacion FOREIGN KEY (obligacion_id) REFERENCES tb_obligaciones_financieras(id),
    INDEX idx_aplic_pago_pago (pago_id),
    INDEX idx_aplic_pago_obligacion (obligacion_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_saldos_favor
-- -------------------------------------------------------------
CREATE TABLE tb_saldos_favor (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cuenta_id INT UNSIGNED NOT NULL,
    origen_pago_id BIGINT UNSIGNED NULL,
    importe_original DECIMAL(12,2) NOT NULL,
    importe_disponible DECIMAL(12,2) NOT NULL,
    fecha_generacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    estado VARCHAR(30) NOT NULL DEFAULT 'disponible',
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_saldos_favor_cuenta FOREIGN KEY (cuenta_id) REFERENCES tb_cuentas_financieras(id),
    CONSTRAINT fk_saldos_favor_pago FOREIGN KEY (origen_pago_id) REFERENCES tb_pagos(id),
    INDEX idx_saldos_favor_cuenta_estado (cuenta_id, estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_aplicaciones_saldo_favor
-- -------------------------------------------------------------
CREATE TABLE tb_aplicaciones_saldo_favor (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    saldo_favor_id BIGINT UNSIGNED NOT NULL,
    obligacion_id BIGINT UNSIGNED NOT NULL,
    importe_aplicado DECIMAL(12,2) NOT NULL,
    fecha_aplicacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    usuario_id INT UNSIGNED NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'aplicada',
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_aplic_sf_saldo FOREIGN KEY (saldo_favor_id) REFERENCES tb_saldos_favor(id),
    CONSTRAINT fk_aplic_sf_obligacion FOREIGN KEY (obligacion_id) REFERENCES tb_obligaciones_financieras(id),
    INDEX idx_aplic_sf_saldo (saldo_favor_id),
    INDEX idx_aplic_sf_obligacion (obligacion_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_creditos
-- -------------------------------------------------------------
CREATE TABLE tb_creditos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cuenta_id INT UNSIGNED NOT NULL,
    tipo VARCHAR(30) NOT NULL,
    referencia VARCHAR(100) NULL,
    importe_original DECIMAL(12,2) NOT NULL,
    importe_disponible DECIMAL(12,2) NOT NULL,
    fecha_generacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_vencimiento DATE NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'disponible',
    motivo TEXT NULL,
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_creditos_cuenta FOREIGN KEY (cuenta_id) REFERENCES tb_cuentas_financieras(id),
    INDEX idx_creditos_cuenta_estado (cuenta_id, estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_aplicaciones_credito
-- -------------------------------------------------------------
CREATE TABLE tb_aplicaciones_credito (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    credito_id BIGINT UNSIGNED NOT NULL,
    obligacion_id BIGINT UNSIGNED NOT NULL,
    importe_aplicado DECIMAL(12,2) NOT NULL,
    fecha_aplicacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    usuario_id INT UNSIGNED NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'aplicada',
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_aplic_credito_credito FOREIGN KEY (credito_id) REFERENCES tb_creditos(id),
    CONSTRAINT fk_aplic_credito_obligacion FOREIGN KEY (obligacion_id) REFERENCES tb_obligaciones_financieras(id),
    INDEX idx_aplic_credito_credito (credito_id),
    INDEX idx_aplic_credito_obligacion (obligacion_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_servicios_proveedor
-- -------------------------------------------------------------
CREATE TABLE tb_servicios_proveedor (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    proveedor_id INT UNSIGNED NOT NULL,
    organismo_id INT UNSIGNED NOT NULL,
    tipo_servicio VARCHAR(50) NOT NULL,
    referencia VARCHAR(100) NULL,
    torneo_id INT UNSIGNED NULL,
    partido_id INT UNSIGNED NULL,
    fecha_servicio DATETIME NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'registrado',
    descripcion TEXT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_servicios_prov_proveedor FOREIGN KEY (proveedor_id) REFERENCES tb_proveedores(id),
    CONSTRAINT fk_servicios_prov_organismo FOREIGN KEY (organismo_id) REFERENCES tb_organismos(id),
    CONSTRAINT fk_servicios_prov_torneo FOREIGN KEY (torneo_id) REFERENCES tb_torneos(id),
    CONSTRAINT fk_servicios_prov_partido FOREIGN KEY (partido_id) REFERENCES tb_partidos(id),
    INDEX idx_servicios_prov_proveedor (proveedor_id),
    INDEX idx_servicios_prov_fecha (fecha_servicio)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_costos_servicio
-- -------------------------------------------------------------
CREATE TABLE tb_costos_servicio (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    servicio_id BIGINT UNSIGNED NOT NULL,
    concepto_id INT UNSIGNED NULL,
    descripcion VARCHAR(255) NOT NULL,
    cantidad DECIMAL(12,2) NOT NULL DEFAULT 1.00,
    importe_unitario DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    importe_total DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    tipo_costo VARCHAR(30) NOT NULL,
    fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_costos_servicio_servicio FOREIGN KEY (servicio_id) REFERENCES tb_servicios_proveedor(id),
    CONSTRAINT fk_costos_servicio_concepto FOREIGN KEY (concepto_id) REFERENCES tb_conceptos_financieros(id),
    INDEX idx_costos_servicio_servicio (servicio_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_distribuciones_servicio
-- -------------------------------------------------------------
CREATE TABLE tb_distribuciones_servicio (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    servicio_id BIGINT UNSIGNED NOT NULL,
    cuenta_id INT UNSIGNED NOT NULL,
    obligacion_id BIGINT UNSIGNED NULL,
    tipo_distribucion VARCHAR(30) NOT NULL,
    importe DECIMAL(12,2) NOT NULL,
    porcentaje DECIMAL(7,4) NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'generada',
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_dist_servicio_servicio FOREIGN KEY (servicio_id) REFERENCES tb_servicios_proveedor(id),
    CONSTRAINT fk_dist_servicio_cuenta FOREIGN KEY (cuenta_id) REFERENCES tb_cuentas_financieras(id),
    CONSTRAINT fk_dist_servicio_obligacion FOREIGN KEY (obligacion_id) REFERENCES tb_obligaciones_financieras(id),
    INDEX idx_dist_servicio_servicio (servicio_id),
    INDEX idx_dist_servicio_cuenta (cuenta_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_liquidaciones_proveedor
-- -------------------------------------------------------------
CREATE TABLE tb_liquidaciones_proveedor (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    proveedor_id INT UNSIGNED NOT NULL,
    organismo_id INT UNSIGNED NOT NULL,
    servicio_id BIGINT UNSIGNED NULL,
    referencia VARCHAR(100) NULL,
    importe DECIMAL(12,2) NOT NULL,
    fecha_generacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_envio DATETIME NULL,
    fecha_recepcion DATETIME NULL,
    fecha_conciliacion DATETIME NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'pendiente',
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_liquidaciones_proveedor FOREIGN KEY (proveedor_id) REFERENCES tb_proveedores(id),
    CONSTRAINT fk_liquidaciones_organismo FOREIGN KEY (organismo_id) REFERENCES tb_organismos(id),
    CONSTRAINT fk_liquidaciones_servicio FOREIGN KEY (servicio_id) REFERENCES tb_servicios_proveedor(id),
    INDEX idx_liquidaciones_prov_estado (proveedor_id, estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_liquidacion_partidas
-- -------------------------------------------------------------
CREATE TABLE tb_liquidacion_partidas (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    liquidacion_id BIGINT UNSIGNED NOT NULL,
    servicio_id BIGINT UNSIGNED NOT NULL,
    importe DECIMAL(12,2) NOT NULL,
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_liq_partidas_liquidacion FOREIGN KEY (liquidacion_id) REFERENCES tb_liquidaciones_proveedor(id),
    CONSTRAINT fk_liq_partidas_servicio FOREIGN KEY (servicio_id) REFERENCES tb_servicios_proveedor(id),
    INDEX idx_liq_partidas_liquidacion (liquidacion_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_movimientos_financieros
-- -------------------------------------------------------------
CREATE TABLE tb_movimientos_financieros (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cuenta_id INT UNSIGNED NOT NULL,
    tipo_movimiento VARCHAR(40) NOT NULL,
    referencia_tipo VARCHAR(40) NULL,
    referencia_id BIGINT UNSIGNED NULL,
    importe DECIMAL(12,2) NOT NULL,
    naturaleza VARCHAR(10) NOT NULL,
    fecha_movimiento DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    usuario_id INT UNSIGNED NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'aplicado',
    descripcion TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_mov_fin_cuenta FOREIGN KEY (cuenta_id) REFERENCES tb_cuentas_financieras(id),
    INDEX idx_mov_fin_cuenta_fecha (cuenta_id, fecha_movimiento),
    INDEX idx_mov_fin_referencia (referencia_tipo, referencia_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_ajustes_financieros
-- -------------------------------------------------------------
CREATE TABLE tb_ajustes_financieros (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cuenta_id INT UNSIGNED NOT NULL,
    tipo_ajuste VARCHAR(30) NOT NULL,
    referencia VARCHAR(100) NULL,
    importe DECIMAL(12,2) NOT NULL,
    motivo TEXT NOT NULL,
    fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    usuario_id INT UNSIGNED NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'aplicado',
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_ajustes_fin_cuenta FOREIGN KEY (cuenta_id) REFERENCES tb_cuentas_financieras(id),
    INDEX idx_ajustes_fin_cuenta_fecha (cuenta_id, fecha)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_reversiones_financieras
-- -------------------------------------------------------------
CREATE TABLE tb_reversiones_financieras (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    movimiento_origen_id BIGINT UNSIGNED NOT NULL,
    motivo TEXT NOT NULL,
    importe DECIMAL(12,2) NOT NULL,
    fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    usuario_id INT UNSIGNED NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'aplicada',
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_reversiones_mov FOREIGN KEY (movimiento_origen_id) REFERENCES tb_movimientos_financieros(id),
    INDEX idx_reversiones_mov (movimiento_origen_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_transferencias_financieras
-- -------------------------------------------------------------
CREATE TABLE tb_transferencias_financieras (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cuenta_origen_id INT UNSIGNED NOT NULL,
    cuenta_destino_id INT UNSIGNED NOT NULL,
    importe DECIMAL(12,2) NOT NULL,
    fecha_solicitud DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_autorizacion DATETIME NULL,
    fecha_aplicacion DATETIME NULL,
    usuario_solicitud_id INT UNSIGNED NULL,
    usuario_autorizacion_id INT UNSIGNED NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'pendiente',
    motivo TEXT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_transferencias_origen FOREIGN KEY (cuenta_origen_id) REFERENCES tb_cuentas_financieras(id),
    CONSTRAINT fk_transferencias_destino FOREIGN KEY (cuenta_destino_id) REFERENCES tb_cuentas_financieras(id),
    INDEX idx_transferencias_estado (estado),
    INDEX idx_transferencias_origen_destino (cuenta_origen_id, cuenta_destino_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_reembolsos_financieros
-- -------------------------------------------------------------
CREATE TABLE tb_reembolsos_financieros (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pago_id BIGINT UNSIGNED NULL,
    cuenta_id INT UNSIGNED NOT NULL,
    importe DECIMAL(12,2) NOT NULL,
    motivo TEXT NOT NULL,
    fecha_solicitud DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_autorizacion DATETIME NULL,
    fecha_aplicacion DATETIME NULL,
    usuario_solicitud_id INT UNSIGNED NULL,
    usuario_autorizacion_id INT UNSIGNED NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'pendiente',
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_reembolsos_pago FOREIGN KEY (pago_id) REFERENCES tb_pagos(id),
    CONSTRAINT fk_reembolsos_cuenta FOREIGN KEY (cuenta_id) REFERENCES tb_cuentas_financieras(id),
    INDEX idx_reembolsos_cuenta_estado (cuenta_id, estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_garantias_financieras
-- -------------------------------------------------------------
CREATE TABLE tb_garantias_financieras (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cuenta_id INT UNSIGNED NOT NULL,
    concepto_id INT UNSIGNED NOT NULL,
    referencia VARCHAR(100) NULL,
    importe_original DECIMAL(12,2) NOT NULL,
    importe_retenido DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    importe_aplicado DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    importe_devuelto DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    fecha_entrega DATETIME NOT NULL,
    fecha_aplicacion DATETIME NULL,
    fecha_devolucion DATETIME NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'retenida',
    motivo TEXT NULL,
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_garantias_cuenta FOREIGN KEY (cuenta_id) REFERENCES tb_cuentas_financieras(id),
    CONSTRAINT fk_garantias_concepto FOREIGN KEY (concepto_id) REFERENCES tb_conceptos_financieros(id),
    INDEX idx_garantias_cuenta_estado (cuenta_id, estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_roles
-- -------------------------------------------------------------
CREATE TABLE tb_roles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    clave VARCHAR(50) NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'activo',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_roles_clave (clave)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_permisos
-- -------------------------------------------------------------
CREATE TABLE tb_permisos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    clave VARCHAR(100) NOT NULL,
    nombre VARCHAR(150) NOT NULL,
    modulo VARCHAR(50) NOT NULL,
    descripcion TEXT NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'activo',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_permisos_clave (clave),
    INDEX idx_permisos_modulo (modulo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_rol_permisos
-- -------------------------------------------------------------
CREATE TABLE tb_rol_permisos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    rol_id INT UNSIGNED NOT NULL,
    permiso_id INT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_rol_permisos_rol FOREIGN KEY (rol_id) REFERENCES tb_roles(id),
    CONSTRAINT fk_rol_permisos_permiso FOREIGN KEY (permiso_id) REFERENCES tb_permisos(id),
    UNIQUE KEY uq_rol_permiso (rol_id, permiso_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_usuarios
-- -------------------------------------------------------------
CREATE TABLE tb_usuarios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    afiliado_id INT UNSIGNED NULL,
    usuario VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    rol_id INT UNSIGNED NOT NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'activo',
    avatar VARCHAR(255) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_usuarios_afiliado FOREIGN KEY (afiliado_id) REFERENCES tb_afiliados(id),
    CONSTRAINT fk_usuarios_rol FOREIGN KEY (rol_id) REFERENCES tb_roles(id),
    UNIQUE KEY uq_usuarios_usuario (usuario),
    INDEX idx_usuarios_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_usuario_alcances
-- -------------------------------------------------------------
CREATE TABLE tb_usuario_alcances (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT UNSIGNED NOT NULL,
    organismo_id INT UNSIGNED NULL,
    tipo_entidad VARCHAR(30) NOT NULL,
    entidad_id INT UNSIGNED NOT NULL,
    fecha_inicio DATE NULL,
    fecha_fin DATE NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'activo',
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_usuario_alcances_usuario FOREIGN KEY (usuario_id) REFERENCES tb_usuarios(id),
    CONSTRAINT fk_usuario_alcances_organismo FOREIGN KEY (organismo_id) REFERENCES tb_organismos(id),
    INDEX idx_usuario_alcances_usuario (usuario_id),
    INDEX idx_usuario_alcances_entidad (tipo_entidad, entidad_id),
    INDEX idx_usuario_alcances_organismo (organismo_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_configuraciones
-- -------------------------------------------------------------
CREATE TABLE tb_configuraciones (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    organismo_id INT UNSIGNED NULL,
    clave VARCHAR(100) NOT NULL,
    valor TEXT NULL,
    tipo_valor VARCHAR(20) NOT NULL DEFAULT 'texto',
    descripcion TEXT NULL,
    fecha_inicio DATE NULL,
    fecha_fin DATE NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'activo',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_configuraciones_organismo FOREIGN KEY (organismo_id) REFERENCES tb_organismos(id),
    UNIQUE KEY uq_config_org_clave (organismo_id, clave),
    INDEX idx_config_vigencia (fecha_inicio, fecha_fin)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_configuracion_comunicaciones
-- -------------------------------------------------------------
CREATE TABLE tb_configuracion_comunicaciones (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    organismo_id INT UNSIGNED NULL,
    evento VARCHAR(100) NOT NULL,
    tipo_comunicacion VARCHAR(40) NOT NULL,
    prioridad VARCHAR(20) NOT NULL DEFAULT 'normal',
    requiere_atencion TINYINT(1) NOT NULL DEFAULT 0,
    enviar_inmediatamente TINYINT(1) NOT NULL DEFAULT 1,
    plantilla_clave VARCHAR(100) NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'activo',
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_config_com_org FOREIGN KEY (organismo_id) REFERENCES tb_organismos(id),
    INDEX idx_config_com_evento (organismo_id, evento)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_configuracion_comunicacion_destinatarios
-- -------------------------------------------------------------
CREATE TABLE tb_configuracion_comunicacion_destinatarios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    configuracion_id INT UNSIGNED NOT NULL,
    tipo_destinatario VARCHAR(30) NOT NULL,
    rol_id INT UNSIGNED NULL,
    tipo_entidad VARCHAR(30) NULL,
    entidad_id INT UNSIGNED NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'activo',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_config_com_dest_config FOREIGN KEY (configuracion_id) REFERENCES tb_configuracion_comunicaciones(id),
    CONSTRAINT fk_config_com_dest_rol FOREIGN KEY (rol_id) REFERENCES tb_roles(id),
    INDEX idx_config_com_dest_config (configuracion_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_comunicaciones
-- -------------------------------------------------------------
CREATE TABLE tb_comunicaciones (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    organismo_id INT UNSIGNED NULL,
    tipo VARCHAR(40) NOT NULL,
    origen_modulo VARCHAR(50) NULL,
    origen_entidad VARCHAR(50) NULL,
    origen_entidad_id INT UNSIGNED NULL,
    asunto VARCHAR(200) NOT NULL,
    mensaje TEXT NOT NULL,
    prioridad VARCHAR(20) NOT NULL DEFAULT 'normal',
    fecha_emision DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_limite DATETIME NULL,
    requiere_atencion TINYINT(1) NOT NULL DEFAULT 0,
    estado VARCHAR(30) NOT NULL DEFAULT 'pendiente',
    usuario_emisor_id INT UNSIGNED NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_comunicaciones_org FOREIGN KEY (organismo_id) REFERENCES tb_organismos(id),
    CONSTRAINT fk_comunicaciones_usuario FOREIGN KEY (usuario_emisor_id) REFERENCES tb_usuarios(id),
    INDEX idx_comunicaciones_origen (origen_modulo, origen_entidad, origen_entidad_id),
    INDEX idx_comunicaciones_estado (estado),
    INDEX idx_comunicaciones_emision (fecha_emision)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_comunicacion_destinatarios
-- -------------------------------------------------------------
CREATE TABLE tb_comunicacion_destinatarios (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    comunicacion_id BIGINT UNSIGNED NOT NULL,
    tipo_destinatario VARCHAR(30) NOT NULL,
    entidad_id INT UNSIGNED NULL,
    usuario_id INT UNSIGNED NULL,
    nombre_destinatario VARCHAR(200) NULL,
    fecha_envio DATETIME NULL,
    fecha_lectura DATETIME NULL,
    fecha_atencion DATETIME NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'pendiente',
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_com_dest_com FOREIGN KEY (comunicacion_id) REFERENCES tb_comunicaciones(id),
    CONSTRAINT fk_com_dest_usuario FOREIGN KEY (usuario_id) REFERENCES tb_usuarios(id),
    INDEX idx_com_dest_com_estado (comunicacion_id, estado),
    INDEX idx_com_dest_usuario (usuario_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_comunicacion_documentos
-- -------------------------------------------------------------
CREATE TABLE tb_comunicacion_documentos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    comunicacion_id BIGINT UNSIGNED NOT NULL,
    tipo_documento VARCHAR(40) NOT NULL,
    archivo VARCHAR(255) NOT NULL,
    nombre_original VARCHAR(255) NULL,
    fecha_carga DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    usuario_carga_id INT UNSIGNED NULL,
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_com_doc_com FOREIGN KEY (comunicacion_id) REFERENCES tb_comunicaciones(id),
    INDEX idx_com_doc_com (comunicacion_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_comunicaciones_oficiales
-- -------------------------------------------------------------
CREATE TABLE tb_comunicaciones_oficiales (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    comunicacion_id BIGINT UNSIGNED NOT NULL,
    numero_oficio VARCHAR(100) NULL,
    fecha_emision DATE NOT NULL,
    fecha_efectiva DATE NULL,
    fecha_notificacion DATETIME NULL,
    fundamento TEXT NULL,
    plazo_respuesta INT UNSIGNED NULL,
    fecha_limite_respuesta DATE NULL,
    usuario_firma_id INT UNSIGNED NULL,
    documento_firmado VARCHAR(255) NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'emitida',
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_com_oficial_com FOREIGN KEY (comunicacion_id) REFERENCES tb_comunicaciones(id),
    CONSTRAINT fk_com_oficial_firma FOREIGN KEY (usuario_firma_id) REFERENCES tb_usuarios(id),
    UNIQUE KEY uq_com_oficial_com (comunicacion_id),
    INDEX idx_com_oficial_numero (numero_oficio)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_comunicacion_respuestas
-- -------------------------------------------------------------
CREATE TABLE tb_comunicacion_respuestas (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    comunicacion_id BIGINT UNSIGNED NOT NULL,
    usuario_id INT UNSIGNED NOT NULL,
    fecha_respuesta DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    tipo_respuesta VARCHAR(40) NOT NULL,
    mensaje TEXT NOT NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'registrada',
    documento VARCHAR(255) NULL,
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_com_resp_com FOREIGN KEY (comunicacion_id) REFERENCES tb_comunicaciones(id),
    CONSTRAINT fk_com_resp_usuario FOREIGN KEY (usuario_id) REFERENCES tb_usuarios(id),
    INDEX idx_com_resp_com (comunicacion_id, fecha_respuesta)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_comunicacion_historial
-- -------------------------------------------------------------
CREATE TABLE tb_comunicacion_historial (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    comunicacion_id BIGINT UNSIGNED NOT NULL,
    estado_anterior VARCHAR(30) NULL,
    estado_nuevo VARCHAR(30) NOT NULL,
    fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    usuario_id INT UNSIGNED NULL,
    motivo VARCHAR(255) NULL,
    observaciones TEXT NULL,
    CONSTRAINT fk_com_hist_com FOREIGN KEY (comunicacion_id) REFERENCES tb_comunicaciones(id),
    CONSTRAINT fk_com_hist_usuario FOREIGN KEY (usuario_id) REFERENCES tb_usuarios(id),
    INDEX idx_com_hist_com_fecha (comunicacion_id, fecha)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_reuniones
-- -------------------------------------------------------------
CREATE TABLE tb_reuniones (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    organismo_id INT UNSIGNED NOT NULL,
    tipo VARCHAR(40) NOT NULL,
    titulo VARCHAR(200) NOT NULL,
    fecha_programada DATE NOT NULL,
    hora_programada TIME NULL,
    fecha_inicio DATETIME NULL,
    fecha_fin DATETIME NULL,
    lugar VARCHAR(255) NULL,
    modalidad VARCHAR(20) NULL,
    convocada_por_id INT UNSIGNED NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'programada',
    orden_del_dia TEXT NULL,
    desarrollo TEXT NULL,
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_reuniones_org FOREIGN KEY (organismo_id) REFERENCES tb_organismos(id),
    CONSTRAINT fk_reuniones_convoca FOREIGN KEY (convocada_por_id) REFERENCES tb_usuarios(id),
    INDEX idx_reuniones_org_fecha (organismo_id, fecha_programada),
    INDEX idx_reuniones_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_reunion_asistentes
-- -------------------------------------------------------------
CREATE TABLE tb_reunion_asistentes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    reunion_id INT UNSIGNED NOT NULL,
    afiliado_id INT UNSIGNED NULL,
    usuario_id INT UNSIGNED NULL,
    tipo_representacion VARCHAR(40) NULL,
    entidad_tipo VARCHAR(30) NULL,
    entidad_id INT UNSIGNED NULL,
    rol_asistencia VARCHAR(50) NULL,
    asistio TINYINT(1) NOT NULL DEFAULT 1,
    hora_llegada TIME NULL,
    hora_salida TIME NULL,
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_reunion_asist_reunion FOREIGN KEY (reunion_id) REFERENCES tb_reuniones(id),
    CONSTRAINT fk_reunion_asist_afiliado FOREIGN KEY (afiliado_id) REFERENCES tb_afiliados(id),
    CONSTRAINT fk_reunion_asist_usuario FOREIGN KEY (usuario_id) REFERENCES tb_usuarios(id),
    INDEX idx_reunion_asist_reunion (reunion_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_reunion_asuntos
-- -------------------------------------------------------------
CREATE TABLE tb_reunion_asuntos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    reunion_id INT UNSIGNED NOT NULL,
    orden INT UNSIGNED NOT NULL,
    titulo VARCHAR(200) NOT NULL,
    descripcion TEXT NULL,
    tipo VARCHAR(40) NULL,
    resultado TEXT NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'pendiente',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_reunion_asuntos_reunion FOREIGN KEY (reunion_id) REFERENCES tb_reuniones(id),
    UNIQUE KEY uq_reunion_asunto_orden (reunion_id, orden)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_reunion_acuerdos
-- -------------------------------------------------------------
CREATE TABLE tb_reunion_acuerdos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    reunion_id INT UNSIGNED NOT NULL,
    asunto_id INT UNSIGNED NULL,
    numero INT UNSIGNED NOT NULL,
    descripcion TEXT NOT NULL,
    tipo VARCHAR(40) NULL,
    responsable_afiliado_id INT UNSIGNED NULL,
    responsable_usuario_id INT UNSIGNED NULL,
    fecha_compromiso DATE NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'pendiente',
    fecha_cumplimiento DATETIME NULL,
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_reunion_acuerdos_reunion FOREIGN KEY (reunion_id) REFERENCES tb_reuniones(id),
    CONSTRAINT fk_reunion_acuerdos_asunto FOREIGN KEY (asunto_id) REFERENCES tb_reunion_asuntos(id),
    CONSTRAINT fk_reunion_acuerdos_afiliado FOREIGN KEY (responsable_afiliado_id) REFERENCES tb_afiliados(id),
    CONSTRAINT fk_reunion_acuerdos_usuario FOREIGN KEY (responsable_usuario_id) REFERENCES tb_usuarios(id),
    UNIQUE KEY uq_reunion_acuerdo_numero (reunion_id, numero),
    INDEX idx_reunion_acuerdos_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_reunion_documentos
-- -------------------------------------------------------------
CREATE TABLE tb_reunion_documentos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    reunion_id INT UNSIGNED NOT NULL,
    tipo_documento VARCHAR(40) NOT NULL,
    nombre VARCHAR(200) NOT NULL,
    archivo VARCHAR(255) NOT NULL,
    nombre_original VARCHAR(255) NULL,
    fecha_documento DATE NULL,
    usuario_carga_id INT UNSIGNED NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'vigente',
    observaciones TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_reunion_docs_reunion FOREIGN KEY (reunion_id) REFERENCES tb_reuniones(id),
    CONSTRAINT fk_reunion_docs_usuario FOREIGN KEY (usuario_carga_id) REFERENCES tb_usuarios(id),
    INDEX idx_reunion_docs_reunion (reunion_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- tb_auditoria
-- -------------------------------------------------------------
CREATE TABLE tb_auditoria (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    organismo_id INT UNSIGNED NULL,
    usuario_id INT UNSIGNED NULL,
    fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    modulo VARCHAR(50) NOT NULL,
    entidad VARCHAR(50) NOT NULL,
    entidad_id INT UNSIGNED NULL,
    accion VARCHAR(50) NOT NULL,
    descripcion TEXT NULL,
    datos_anteriores JSON NULL,
    datos_nuevos JSON NULL,
    motivo TEXT NULL,
    ip VARCHAR(45) NULL,
    user_agent TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_auditoria_org FOREIGN KEY (organismo_id) REFERENCES tb_organismos(id),
    CONSTRAINT fk_auditoria_usuario FOREIGN KEY (usuario_id) REFERENCES tb_usuarios(id),
    INDEX idx_auditoria_entidad (entidad, entidad_id),
    INDEX idx_auditoria_usuario_fecha (usuario_id, fecha),
    INDEX idx_auditoria_modulo_fecha (modulo, fecha)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- -------------------------------------------------------------
-- FKs diferidas de auditoría/trazabilidad
-- Estas FK se agregan al final porque varias tablas se crean antes
-- de tb_usuarios o porque existe una referencia circular/self-reference.
-- -------------------------------------------------------------
ALTER TABLE tb_afiliado_documentos
    ADD CONSTRAINT fk_afiliado_docs_usuario
    FOREIGN KEY (usuario_carga_id) REFERENCES tb_usuarios(id);

ALTER TABLE tb_tramites_afiliacion
    ADD CONSTRAINT fk_tramites_usuario_recepcion
    FOREIGN KEY (usuario_recepcion_id) REFERENCES tb_usuarios(id),
    ADD CONSTRAINT fk_tramites_usuario_revision
    FOREIGN KEY (usuario_revision_id) REFERENCES tb_usuarios(id),
    ADD CONSTRAINT fk_tramites_usuario_resolucion
    FOREIGN KEY (usuario_resolucion_id) REFERENCES tb_usuarios(id);

ALTER TABLE tb_tramite_afiliacion_historial
    ADD CONSTRAINT fk_tramite_hist_usuario
    FOREIGN KEY (usuario_id) REFERENCES tb_usuarios(id);

ALTER TABLE tb_tramite_afiliacion_observaciones
    ADD CONSTRAINT fk_tramite_obs_usuario
    FOREIGN KEY (usuario_id) REFERENCES tb_usuarios(id);

ALTER TABLE tb_credencial_movimientos
    ADD CONSTRAINT fk_cred_mov_usuario
    FOREIGN KEY (usuario_id) REFERENCES tb_usuarios(id);

ALTER TABLE tb_entregas_credenciales
    ADD CONSTRAINT fk_entrega_cred_usuario
    FOREIGN KEY (usuario_entrega_id) REFERENCES tb_usuarios(id);

ALTER TABLE tb_bajas_afiliacion
    ADD CONSTRAINT fk_baja_usuario_revision
    FOREIGN KEY (usuario_revision_id) REFERENCES tb_usuarios(id),
    ADD CONSTRAINT fk_baja_usuario_resolucion
    FOREIGN KEY (usuario_resolucion_id) REFERENCES tb_usuarios(id);

ALTER TABLE tb_reincorporaciones_afiliacion
    ADD CONSTRAINT fk_reinc_usuario_revision
    FOREIGN KEY (usuario_revision_id) REFERENCES tb_usuarios(id),
    ADD CONSTRAINT fk_reinc_usuario_resolucion
    FOREIGN KEY (usuario_resolucion_id) REFERENCES tb_usuarios(id);

ALTER TABLE tb_resoluciones
    ADD CONSTRAINT fk_resoluciones_usuario
    FOREIGN KEY (usuario_resolucion_id) REFERENCES tb_usuarios(id);

ALTER TABLE tb_partido_programaciones
    ADD CONSTRAINT fk_partido_prog_usuario
    FOREIGN KEY (usuario_id) REFERENCES tb_usuarios(id);

ALTER TABLE tb_partido_refuerzos
    ADD CONSTRAINT fk_refuerzos_usuario_autorizo
    FOREIGN KEY (autorizado_por) REFERENCES tb_usuarios(id);

ALTER TABLE tb_partido_resultados
    ADD CONSTRAINT fk_resultados_usuario_captura
    FOREIGN KEY (usuario_captura_id) REFERENCES tb_usuarios(id),
    ADD CONSTRAINT fk_resultados_usuario_autorizacion
    FOREIGN KEY (usuario_autorizacion_id) REFERENCES tb_usuarios(id);

ALTER TABLE tb_partido_segmentos
    ADD INDEX idx_segmentos_partido_estado (partido_id, estado);

ALTER TABLE tb_incidencias
    ADD CONSTRAINT fk_incidencias_partido
    FOREIGN KEY (partido_id) REFERENCES tb_partidos(id);

ALTER TABLE tb_sancion_cumplimientos
    ADD CONSTRAINT fk_sancion_cumplimiento_partido
    FOREIGN KEY (partido_id) REFERENCES tb_partidos(id);

ALTER TABLE tb_protestas
    ADD CONSTRAINT fk_protestas_partido
    FOREIGN KEY (partido_id) REFERENCES tb_partidos(id);

ALTER TABLE tb_ordenes_pago
    ADD CONSTRAINT fk_ordenes_pago_usuario
    FOREIGN KEY (usuario_generacion_id) REFERENCES tb_usuarios(id);

ALTER TABLE tb_pagos
    ADD CONSTRAINT fk_pagos_usuario_registro
    FOREIGN KEY (usuario_registro_id) REFERENCES tb_usuarios(id),
    ADD CONSTRAINT fk_pagos_usuario_validacion
    FOREIGN KEY (usuario_validacion_id) REFERENCES tb_usuarios(id);

ALTER TABLE tb_aplicaciones_pago
    ADD CONSTRAINT fk_aplicaciones_pago_usuario
    FOREIGN KEY (usuario_id) REFERENCES tb_usuarios(id);

ALTER TABLE tb_aplicaciones_saldo_favor
    ADD CONSTRAINT fk_aplicaciones_saldo_usuario
    FOREIGN KEY (usuario_id) REFERENCES tb_usuarios(id);

ALTER TABLE tb_aplicaciones_credito
    ADD CONSTRAINT fk_aplicaciones_credito_usuario
    FOREIGN KEY (usuario_id) REFERENCES tb_usuarios(id);

ALTER TABLE tb_movimientos_financieros
    ADD CONSTRAINT fk_mov_fin_usuario
    FOREIGN KEY (usuario_id) REFERENCES tb_usuarios(id);

ALTER TABLE tb_ajustes_financieros
    ADD CONSTRAINT fk_ajustes_fin_usuario
    FOREIGN KEY (usuario_id) REFERENCES tb_usuarios(id);

ALTER TABLE tb_reversiones_financieras
    ADD CONSTRAINT fk_reversiones_usuario
    FOREIGN KEY (usuario_id) REFERENCES tb_usuarios(id);

ALTER TABLE tb_transferencias_financieras
    ADD CONSTRAINT fk_transferencias_usuario_solicitud
    FOREIGN KEY (usuario_solicitud_id) REFERENCES tb_usuarios(id),
    ADD CONSTRAINT fk_transferencias_usuario_autorizacion
    FOREIGN KEY (usuario_autorizacion_id) REFERENCES tb_usuarios(id);

ALTER TABLE tb_reembolsos_financieros
    ADD CONSTRAINT fk_reembolsos_usuario_solicitud
    FOREIGN KEY (usuario_solicitud_id) REFERENCES tb_usuarios(id),
    ADD CONSTRAINT fk_reembolsos_usuario_autorizacion
    FOREIGN KEY (usuario_autorizacion_id) REFERENCES tb_usuarios(id);

ALTER TABLE tb_comunicacion_documentos
    ADD CONSTRAINT fk_com_documentos_usuario
    FOREIGN KEY (usuario_carga_id) REFERENCES tb_usuarios(id);

ALTER TABLE tb_equipo_identidad_historial
    ADD CONSTRAINT fk_equipo_ident_hist_usuario
    FOREIGN KEY (usuario_id) REFERENCES tb_usuarios(id);

-- -------------------------------------------------------------
-- Restricciones de coherencia de alto valor
-- -------------------------------------------------------------
ALTER TABLE tb_organismo_relaciones
    ADD CONSTRAINT chk_org_rel_distintos
    CHECK (organismo_origen_id <> organismo_destino_id);

ALTER TABLE tb_partidos
    ADD CONSTRAINT chk_partido_equipos_distintos
    CHECK (local_torneo_equipo_id <> visitante_torneo_equipo_id);

ALTER TABLE tb_transferencias_financieras
    ADD CONSTRAINT chk_transferencia_cuentas_distintas
    CHECK (cuenta_origen_id <> cuenta_destino_id);

-- -------------------------------------------------------------
-- FKs diferidas por referencias circulares/self-reference
-- -------------------------------------------------------------
ALTER TABLE tb_partido_designaciones
    ADD CONSTRAINT fk_designaciones_anterior
    FOREIGN KEY (designacion_anterior_id) REFERENCES tb_partido_designaciones(id);

SET FOREIGN_KEY_CHECKS = 1;

-- -------------------------------------------------------------
-- Datos iniciales mínimos de sistema
-- -------------------------------------------------------------
INSERT INTO tb_roles (clave, nombre, descripcion) VALUES
('ADMIN', 'Administrador', 'Acceso administrativo general.'),
('SUPERVISOR', 'Supervisor', 'Supervisión operativa.'),
('LIGA', 'Liga', 'Operación de una liga dentro de sus alcances.'),
('EQUIPO', 'Equipo', 'Operación de un equipo dentro de sus alcances.');

-- Nota: permisos, catálogos de dominio y configuraciones específicas de AFEC
-- se sembrarán en una etapa posterior, una vez aprobada la matriz de permisos
-- y los catálogos operativos.
