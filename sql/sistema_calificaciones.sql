-- ============================================================
--  SISTEMA DE GESTIÓN DE CALIFICACIONES
--  Escuela de Educación Secundaria Técnica N°1 - Vicente López
-- ============================================================

-- Crear y seleccionar la base de datos
CREATE DATABASE IF NOT EXISTS sistema_calificaciones
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE sistema_calificaciones;

-- ============================================================
--  MÓDULO: USUARIOS
-- ============================================================

CREATE TABLE roles (
    id          INT UNSIGNED    AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(50)     NOT NULL UNIQUE,  -- 'docente', 'administrador', 'alumno', 'tutor'
    descripcion VARCHAR(255)
);

CREATE TABLE usuarios (
    id              INT UNSIGNED    AUTO_INCREMENT PRIMARY KEY,
    id_rol          INT UNSIGNED    NOT NULL,
    nombre          VARCHAR(100)    NOT NULL,
    apellido        VARCHAR(100)    NOT NULL,
    dni             VARCHAR(20)     NOT NULL UNIQUE,
    email           VARCHAR(150)    NOT NULL UNIQUE,
    password_hash   VARCHAR(255)    NOT NULL,
    activo          BOOLEAN         NOT NULL DEFAULT TRUE,
    creado_en       DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_usuario_rol FOREIGN KEY (id_rol) REFERENCES roles(id)
);

-- Relación tutores <-> alumnos (un tutor puede tener varios hijos)
CREATE TABLE tutores_alumnos (
    id_tutor    INT UNSIGNED    NOT NULL,
    id_alumno   INT UNSIGNED    NOT NULL,
    parentesco  VARCHAR(50),
    PRIMARY KEY (id_tutor, id_alumno),
    CONSTRAINT fk_ta_tutor  FOREIGN KEY (id_tutor)  REFERENCES usuarios(id),
    CONSTRAINT fk_ta_alumno FOREIGN KEY (id_alumno) REFERENCES usuarios(id)
);

-- ============================================================
--  MÓDULO: MATERIAS Y ESTRUCTURA ACADÉMICA
-- ============================================================

CREATE TABLE ciclos_lectivos (
    id          INT UNSIGNED    AUTO_INCREMENT PRIMARY KEY,
    anio        YEAR            NOT NULL UNIQUE,
    descripcion VARCHAR(100),
    activo      BOOLEAN         NOT NULL DEFAULT FALSE
);

CREATE TABLE cursos (
    id              INT UNSIGNED    AUTO_INCREMENT PRIMARY KEY,
    id_ciclo        INT UNSIGNED    NOT NULL,
    nombre          VARCHAR(50)     NOT NULL,   -- ej. '7° A'
    anio_escolar    TINYINT UNSIGNED NOT NULL,  -- 1 a 7
    division        VARCHAR(10)     NOT NULL,
    CONSTRAINT fk_curso_ciclo FOREIGN KEY (id_ciclo) REFERENCES ciclos_lectivos(id)
);

CREATE TABLE materias (
    id          INT UNSIGNED    AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(150)    NOT NULL,
    descripcion VARCHAR(255),
    activo      BOOLEAN         NOT NULL DEFAULT TRUE
);

-- Qué materia se dicta en qué curso y por qué docente
CREATE TABLE curso_materia_docente (
    id          INT UNSIGNED    AUTO_INCREMENT PRIMARY KEY,
    id_curso    INT UNSIGNED    NOT NULL,
    id_materia  INT UNSIGNED    NOT NULL,
    id_docente  INT UNSIGNED    NOT NULL,
    UNIQUE KEY uq_cmd (id_curso, id_materia),
    CONSTRAINT fk_cmd_curso   FOREIGN KEY (id_curso)   REFERENCES cursos(id),
    CONSTRAINT fk_cmd_materia FOREIGN KEY (id_materia) REFERENCES materias(id),
    CONSTRAINT fk_cmd_docente FOREIGN KEY (id_docente) REFERENCES usuarios(id)
);

-- Alumnos inscriptos en cada curso
CREATE TABLE inscripciones (
    id          INT UNSIGNED    AUTO_INCREMENT PRIMARY KEY,
    id_alumno   INT UNSIGNED    NOT NULL,
    id_curso    INT UNSIGNED    NOT NULL,
    fecha_alta  DATE            NOT NULL DEFAULT (CURRENT_DATE),
    activo      BOOLEAN         NOT NULL DEFAULT TRUE,
    UNIQUE KEY uq_insc (id_alumno, id_curso),
    CONSTRAINT fk_insc_alumno FOREIGN KEY (id_alumno) REFERENCES usuarios(id),
    CONSTRAINT fk_insc_curso  FOREIGN KEY (id_curso)  REFERENCES cursos(id)
);

-- ============================================================
--  MÓDULO: CALIFICACIONES
-- ============================================================

CREATE TABLE tipos_evaluacion (
    id          INT UNSIGNED    AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(80)     NOT NULL UNIQUE,  -- 'Trabajo Práctico', 'Examen', 'Oral', etc.
    descripcion VARCHAR(255)
);

CREATE TABLE periodos (
    id          INT UNSIGNED    AUTO_INCREMENT PRIMARY KEY,
    id_ciclo    INT UNSIGNED    NOT NULL,
    nombre      VARCHAR(80)     NOT NULL,         -- 'Primer Trimestre', 'Segundo Trimestre', etc.
    fecha_inicio DATE           NOT NULL,
    fecha_fin    DATE           NOT NULL,
    tipo        ENUM('regular','intensificacion','recursada') NOT NULL DEFAULT 'regular',
    CONSTRAINT fk_periodo_ciclo FOREIGN KEY (id_ciclo) REFERENCES ciclos_lectivos(id)
);

CREATE TABLE calificaciones (
    id                  INT UNSIGNED        AUTO_INCREMENT PRIMARY KEY,
    id_alumno           INT UNSIGNED        NOT NULL,
    id_curso_materia    INT UNSIGNED        NOT NULL,   -- FK a curso_materia_docente
    id_tipo_evaluacion  INT UNSIGNED        NOT NULL,
    id_periodo          INT UNSIGNED        NOT NULL,
    nota_numerica       DECIMAL(4,2),                  -- ej. 7.50
    nota_conceptual     VARCHAR(50),                   -- ej. 'Satisfactorio'
    fecha_evaluacion    DATE                NOT NULL,
    observaciones       TEXT,
    anulada             BOOLEAN             NOT NULL DEFAULT FALSE,
    CONSTRAINT fk_cal_alumno  FOREIGN KEY (id_alumno)          REFERENCES usuarios(id),
    CONSTRAINT fk_cal_cmd     FOREIGN KEY (id_curso_materia)   REFERENCES curso_materia_docente(id),
    CONSTRAINT fk_cal_tipo    FOREIGN KEY (id_tipo_evaluacion) REFERENCES tipos_evaluacion(id),
    CONSTRAINT fk_cal_periodo FOREIGN KEY (id_periodo)         REFERENCES periodos(id)
);

-- ============================================================
--  MÓDULO: RÉGIMEN ACADÉMICO (RITE)
-- ============================================================

CREATE TABLE estados_materia (
    id      INT UNSIGNED    AUTO_INCREMENT PRIMARY KEY,
    nombre  VARCHAR(50)     NOT NULL UNIQUE  -- 'Aprobada', 'En proceso', 'No aprobada'
);

-- Trayectoria académica: estado de cada alumno en cada materia por ciclo
CREATE TABLE trayectorias (
    id                  INT UNSIGNED    AUTO_INCREMENT PRIMARY KEY,
    id_alumno           INT UNSIGNED    NOT NULL,
    id_curso_materia    INT UNSIGNED    NOT NULL,
    id_estado_materia   INT UNSIGNED    NOT NULL,
    promedio_final      DECIMAL(4,2),
    fecha_actualizacion DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    observaciones       TEXT,
    UNIQUE KEY uq_tray (id_alumno, id_curso_materia),
    CONSTRAINT fk_tray_alumno  FOREIGN KEY (id_alumno)        REFERENCES usuarios(id),
    CONSTRAINT fk_tray_cmd     FOREIGN KEY (id_curso_materia) REFERENCES curso_materia_docente(id),
    CONSTRAINT fk_tray_estado  FOREIGN KEY (id_estado_materia) REFERENCES estados_materia(id)
);

-- Instancias de intensificación (alumnos que deben rendir en períodos especiales)
CREATE TABLE intensificaciones (
    id                  INT UNSIGNED    AUTO_INCREMENT PRIMARY KEY,
    id_alumno           INT UNSIGNED    NOT NULL,
    id_curso_materia    INT UNSIGNED    NOT NULL,
    id_periodo          INT UNSIGNED    NOT NULL,   -- período de intensificación
    motivo              TEXT,
    id_estado_materia   INT UNSIGNED    NOT NULL,   -- resultado después de la instancia
    fecha_registro      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_int_alumno  FOREIGN KEY (id_alumno)         REFERENCES usuarios(id),
    CONSTRAINT fk_int_cmd     FOREIGN KEY (id_curso_materia)  REFERENCES curso_materia_docente(id),
    CONSTRAINT fk_int_periodo FOREIGN KEY (id_periodo)        REFERENCES periodos(id),
    CONSTRAINT fk_int_estado  FOREIGN KEY (id_estado_materia) REFERENCES estados_materia(id)
);

-- Recursadas: nueva cursada de una materia no aprobada
CREATE TABLE recursadas (
    id                      INT UNSIGNED    AUTO_INCREMENT PRIMARY KEY,
    id_alumno               INT UNSIGNED    NOT NULL,
    id_materia              INT UNSIGNED    NOT NULL,
    id_ciclo_original       INT UNSIGNED    NOT NULL,   -- ciclo en que se cursó originalmente
    id_ciclo_recursada      INT UNSIGNED    NOT NULL,   -- ciclo en que se recursa
    id_curso_recursada      INT UNSIGNED    NOT NULL,   -- curso donde recursa (puede ser distinto)
    motivo                  TEXT,
    fecha_registro          DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_rec_alumno    FOREIGN KEY (id_alumno)          REFERENCES usuarios(id),
    CONSTRAINT fk_rec_materia   FOREIGN KEY (id_materia)         REFERENCES materias(id),
    CONSTRAINT fk_rec_ciclo_or  FOREIGN KEY (id_ciclo_original)  REFERENCES ciclos_lectivos(id),
    CONSTRAINT fk_rec_ciclo_rec FOREIGN KEY (id_ciclo_recursada) REFERENCES ciclos_lectivos(id),
    CONSTRAINT fk_rec_curso     FOREIGN KEY (id_curso_recursada) REFERENCES cursos(id)
);

-- ============================================================
--  MÓDULO: AUDITORÍA
-- ============================================================

CREATE TABLE auditoria (
    id              BIGINT UNSIGNED     AUTO_INCREMENT PRIMARY KEY,
    id_usuario      INT UNSIGNED        NOT NULL,
    accion          VARCHAR(100)        NOT NULL,   -- 'INSERT_CALIFICACION', 'UPDATE_ESTADO', etc.
    tabla_afectada  VARCHAR(100)        NOT NULL,
    id_registro     INT UNSIGNED,                   -- PK del registro afectado
    valor_anterior  JSON,                           -- estado anterior (opcional)
    valor_nuevo     JSON,                           -- estado nuevo  (opcional)
    ip_origen       VARCHAR(45),
    fecha           DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_aud_usuario FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
);

-- ============================================================
--  DATOS INICIALES
-- ============================================================

INSERT INTO roles (nombre, descripcion) VALUES
    ('administrador', 'Gestiona usuarios, materias, cursos y ciclos lectivos'),
    ('docente',       'Registra y gestiona calificaciones de sus materias'),
    ('alumno',        'Consulta sus propias calificaciones y estado académico'),
    ('tutor',         'Visualiza el rendimiento académico del alumno a su cargo');

INSERT INTO estados_materia (nombre) VALUES
    ('Aprobada'),
    ('En proceso'),
    ('No aprobada');

INSERT INTO tipos_evaluacion (nombre) VALUES
    ('Trabajo Práctico'),
    ('Examen Escrito'),
    ('Examen Oral'),
    ('Trabajo Integrador'),
    ('Evaluación Conceptual');

-- ============================================================
--  VISTAS ÚTILES
-- ============================================================

-- Promedio de cada alumno por materia en un período regular
CREATE VIEW v_promedios_alumno_materia AS
SELECT
    u.id                            AS id_alumno,
    CONCAT(u.apellido, ', ', u.nombre) AS alumno,
    m.nombre                        AS materia,
    cl.anio                         AS ciclo,
    p.nombre                        AS periodo,
    ROUND(AVG(c.nota_numerica), 2)  AS promedio,
    COUNT(c.id)                     AS cant_evaluaciones
FROM calificaciones c
JOIN usuarios               u   ON c.id_alumno          = u.id
JOIN curso_materia_docente  cmd ON c.id_curso_materia    = cmd.id
JOIN materias               m   ON cmd.id_materia        = m.id
JOIN periodos               p   ON c.id_periodo          = p.id
JOIN ciclos_lectivos        cl  ON p.id_ciclo            = cl.id
WHERE c.anulada = FALSE
GROUP BY u.id, m.id, cl.id, p.id;

-- Estado académico general de cada alumno
CREATE VIEW v_estado_academico AS
SELECT
    u.id                            AS id_alumno,
    CONCAT(u.apellido, ', ', u.nombre) AS alumno,
    m.nombre                        AS materia,
    cl.anio                         AS ciclo,
    em.nombre                       AS estado,
    t.promedio_final,
    t.fecha_actualizacion
FROM trayectorias t
JOIN usuarios               u   ON t.id_alumno          = u.id
JOIN curso_materia_docente  cmd ON t.id_curso_materia    = cmd.id
JOIN materias               m   ON cmd.id_materia        = m.id
JOIN cursos                 cu  ON cmd.id_curso          = cu.id
JOIN ciclos_lectivos        cl  ON cu.id_ciclo           = cl.id
JOIN estados_materia        em  ON t.id_estado_materia   = em.id;

-- Alumnos con materias pendientes (no aprobadas)
CREATE VIEW v_materias_pendientes AS
SELECT *
FROM v_estado_academico
WHERE estado IN ('En proceso', 'No aprobada');
