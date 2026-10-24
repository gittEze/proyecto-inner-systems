-- Base de Datos de Aprendomo

CREATE DATABASE IF NOT EXISTS softwaredesarrollo DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE softwaredesarrollo;

SET FOREIGN_KEY_CHECKS = 0;

-- 1. Tabla: usuarios

DROP TABLE IF EXISTS usuarios;
CREATE TABLE usuarios (
ID_Usuario INT(11) NOT NULL AUTO_INCREMENT,
Nombre VARCHAR(20) DEFAULT NULL,
Apellido VARCHAR(20) DEFAULT NULL,
Cedula INT(11) NOT NULL,
Fecha_De_Nacimiento DATE NOT NULL,
Correo VARCHAR(50) DEFAULT NULL,
Contrasenia VARCHAR(255) NOT NULL,
Telefono INT(11) NOT NULL,
Genero VARCHAR(20) NOT NULL,
Rol VARCHAR(20) NOT NULL,
Foto_Perfil VARCHAR(255) DEFAULT NULL,
token_recuperacion VARCHAR(255) DEFAULT NULL,
token_expiracion DATETIME DEFAULT NULL,
PRIMARY KEY (ID_Usuario),
UNIQUE KEY Correo (Correo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 2. Tabla: cursos

DROP TABLE IF EXISTS cursos;
CREATE TABLE cursos (
ID_Curso INT(11) NOT NULL AUTO_INCREMENT,
ID_Docente INT(11) NOT NULL,
ID_Usuario INT(11) DEFAULT NULL,
Titulo_Curso TEXT NOT NULL,
Descripcion_Curso TEXT NOT NULL,
Tipo_Curso VARCHAR(50) NOT NULL,
Nivel_Curso TEXT NOT NULL,
Duracion_Estimada INT(20) NOT NULL,
Precio INT(20) NOT NULL,
Dataso VARCHAR(50) NOT NULL,
PRIMARY KEY (ID_Curso),
KEY fk_cursos_docente (ID_Docente),
KEY fk_cursos_usuario (ID_Usuario),
CONSTRAINT fk_cursos_docente FOREIGN KEY (ID_Docente) REFERENCES usuarios (ID_Usuario) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT fk_cursos_usuario FOREIGN KEY (ID_Usuario) REFERENCES usuarios (ID_Usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 3. Tabla: carpetas

DROP TABLE IF EXISTS carpetas;
CREATE TABLE carpetas (
ID_Carpeta INT(11) NOT NULL AUTO_INCREMENT,
ID_Curso INT(11) NOT NULL,
Nombre_Carpeta VARCHAR(150) NOT NULL,
Orden INT(11) DEFAULT 0,
PRIMARY KEY (ID_Carpeta),
KEY fk_carpeta_curso (ID_Curso),
CONSTRAINT fk_carpeta_curso FOREIGN KEY (ID_Curso) REFERENCES cursos (ID_Curso) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 4. Tabla: inscripciones

DROP TABLE IF EXISTS inscripciones;
CREATE TABLE inscripciones (
ID_Usuario INT(11) NOT NULL,
ID_Curso INT(11) NOT NULL,
Fecha_Inscripcion DATETIME NOT NULL,
PRIMARY KEY (ID_Usuario, ID_Curso),
KEY fk_inscripcion_curso (ID_Curso),
CONSTRAINT fk_inscripcion_curso FOREIGN KEY (ID_Curso) REFERENCES cursos (ID_Curso) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT fk_inscripcion_usuario FOREIGN KEY (ID_Usuario) REFERENCES usuarios (ID_Usuario) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 5. Tabla: materiales

DROP TABLE IF EXISTS materiales;
CREATE TABLE materiales (
ID_Material INT(11) NOT NULL AUTO_INCREMENT,
ID_Curso INT(11) NOT NULL,
ID_Carpeta INT(11) DEFAULT NULL,
Titulo_Material VARCHAR(200) NOT NULL,
Tipo_Material ENUM('enlace','tarea','archivo','foro','video','audio') NOT NULL DEFAULT 'archivo',
Contenido_URL TEXT DEFAULT NULL,
Fecha_De_Vencimiento DATETIME DEFAULT NULL,
PRIMARY KEY (ID_Material),
KEY fk_material_curso (ID_Curso),
KEY fk_material_carpeta (ID_Carpeta),
CONSTRAINT fk_material_carpeta FOREIGN KEY (ID_Carpeta) REFERENCES carpetas (ID_Carpeta) ON DELETE SET NULL ON UPDATE CASCADE,
CONSTRAINT fk_material_curso FOREIGN KEY (ID_Curso) REFERENCES cursos (ID_Curso) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 6. Tabla: tareas

DROP TABLE IF EXISTS tareas;
CREATE TABLE tareas (
ID_Tarea INT(11) NOT NULL AUTO_INCREMENT,
ID_Curso INT(11) NOT NULL,
Nombre_Tarea VARCHAR(150) NOT NULL,
Descripcion_Tarea TEXT DEFAULT NULL,
Enlace_Tarea VARCHAR(500) DEFAULT NULL,
Fecha_Publicacion DATETIME DEFAULT CURRENT_TIMESTAMP,
Fecha_Vencimiento DATETIME NOT NULL,
Fecha_Bloqueo DATETIME DEFAULT NULL,
PRIMARY KEY (ID_Tarea),
KEY fk_tarea_curso (ID_Curso),
CONSTRAINT fk_tarea_curso FOREIGN KEY (ID_Curso) REFERENCES cursos (ID_Curso) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 7. Tabla: archivos_tarea

DROP TABLE IF EXISTS archivos_tarea;
CREATE TABLE archivos_tarea (
ID_Archivo INT(11) NOT NULL AUTO_INCREMENT,
ID_Tarea INT(11) NOT NULL,
Nombre_Archivo VARCHAR(255) NOT NULL,
Ruta_Archivo VARCHAR(500) NOT NULL,
PRIMARY KEY (ID_Archivo),
KEY fk_archivo_tarea (ID_Tarea),
CONSTRAINT fk_archivo_tarea FOREIGN KEY (ID_Tarea) REFERENCES tareas (ID_Tarea) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 8. Tabla: valoraciones

DROP TABLE IF EXISTS valoraciones;
CREATE TABLE valoraciones (
ID_Valoracion INT(11) NOT NULL AUTO_INCREMENT,
ID_Curso INT(11) NOT NULL,
ID_Usuario INT(11) NOT NULL,
Puntuacion TINYINT(4) NOT NULL,
Comentario TEXT DEFAULT NULL,
Fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (ID_Valoracion),
UNIQUE KEY uk_usuario_curso (ID_Usuario, ID_Curso),
KEY fk_val_curso (ID_Curso),
CONSTRAINT fk_val_curso FOREIGN KEY (ID_Curso) REFERENCES cursos (ID_Curso) ON DELETE CASCADE,
CONSTRAINT fk_val_usuario FOREIGN KEY (ID_Usuario) REFERENCES usuarios (ID_Usuario) ON DELETE CASCADE,
CONSTRAINT chk_puntuacion CHECK (Puntuacion BETWEEN 1 AND 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

SET FOREIGN_KEY_CHECKS = 1;