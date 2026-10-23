
CREATE TABLE usuarios (
  ID_Usuario int(11) NOT NULL AUTO_INCREMENT,
  Nombre varchar(20) DEFAULT NULL,
  Apellido varchar(20) DEFAULT NULL,
  Cedula int(11) NOT NULL,
  Fecha_De_Nacimiento date NOT NULL,
  Correo varchar(50) DEFAULT NULL,
  Contrasenia varchar(255) NOT NULL,
  Telefono int(11) NOT NULL,
  Genero varchar(20) NOT NULL,
  Rol varchar(20) NOT NULL,
  Foto_Perfil varchar(255) DEFAULT NULL,
  PRIMARY KEY (ID_Usuario),
  UNIQUE KEY Correo (Correo)
);


CREATE TABLE cursos (
  ID_Curso int(11) NOT NULL AUTO_INCREMENT,
  ID_Docente int(11) NOT NULL,
  ID_Usuario int(11) DEFAULT NULL,
  Titulo_Curso text NOT NULL,
  Descripcion_Curso text NOT NULL,
  Tipo_Curso varchar(50) NOT NULL,
  Nivel_Curso text NOT NULL,
  Duracion_Estimada int(20) NOT NULL,
  Precio int(20) NOT NULL,
  Dataso varchar(50) NOT NULL,
  PRIMARY KEY (ID_Curso),
  KEY fk_cursos_docente (ID_Docente),
  KEY fk_cursos_usuario (ID_Usuario),
  CONSTRAINT fk_cursos_docente
    FOREIGN KEY (ID_Docente)
    REFERENCES usuarios (ID_Usuario)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_cursos_usuario
    FOREIGN KEY (ID_Usuario)
    REFERENCES usuarios (ID_Usuario)
);


CREATE TABLE inscripciones (
  ID_Usuario int(11) NOT NULL,
  ID_Curso int(11) NOT NULL,
  Fecha_Inscripcion datetime NOT NULL,
  PRIMARY KEY (ID_Usuario, ID_Curso),
  KEY fk_inscripcion_curso (ID_Curso),
  CONSTRAINT fk_inscripcion_curso
    FOREIGN KEY (ID_Curso)
    REFERENCES cursos (ID_Curso)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_inscripcion_usuario
    FOREIGN KEY (ID_Usuario)
    REFERENCES usuarios (ID_Usuario)
    ON DELETE CASCADE
    ON UPDATE CASCADE
);


CREATE TABLE carpetas (
  ID_Carpeta int(11) NOT NULL AUTO_INCREMENT,
  ID_Curso int(11) NOT NULL,
  Nombre_Carpeta varchar(150) NOT NULL,
  Orden int(11) DEFAULT 0,
  PRIMARY KEY (ID_Carpeta),
  KEY fk_carpeta_curso (ID_Curso),
  CONSTRAINT fk_carpeta_curso
    FOREIGN KEY (ID_Curso)
    REFERENCES cursos (ID_Curso)
);


CREATE TABLE materiales (
  ID_Material int(11) NOT NULL AUTO_INCREMENT,
  ID_Curso int(11) NOT NULL,
  ID_Carpeta int(11) DEFAULT NULL,
  Titulo_Material varchar(200) NOT NULL,
  Tipo_Material enum('enlace','tarea','archivo','foro','video','audio') NOT NULL DEFAULT 'archivo',
  Contenido_URL text DEFAULT NULL,
  Fecha_De_Vencimiento datetime DEFAULT NULL,
  PRIMARY KEY (ID_Material),
  KEY fk_material_curso (ID_Curso),
  KEY fk_material_carpeta (ID_Carpeta),
  CONSTRAINT fk_material_carpeta
    FOREIGN KEY (ID_Carpeta)
    REFERENCES carpetas (ID_Carpeta)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  CONSTRAINT fk_material_curso
    FOREIGN KEY (ID_Curso)
    REFERENCES cursos (ID_Curso)
    ON DELETE CASCADE
    ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS valoraciones (
    ID_Valoracion INT AUTO_INCREMENT PRIMARY KEY,
    ID_Curso INT NOT NULL,
    ID_Usuario INT NOT NULL,
    Puntuacion TINYINT NOT NULL,
    Comentario TEXT NULL,
    Fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uk_usuario_curso (ID_Usuario, ID_Curso),
    CONSTRAINT fk_val_curso FOREIGN KEY (ID_Curso) 
    REFERENCES cursos(ID_Curso) ON DELETE CASCADE,
    CONSTRAINT fk_val_usuario FOREIGN KEY (ID_Usuario) REFERENCES usuarios(ID_Usuario) ON DELETE CASCADE,
    CONSTRAINT chk_puntuacion CHECK (Puntuacion BETWEEN 1 AND 5)
)