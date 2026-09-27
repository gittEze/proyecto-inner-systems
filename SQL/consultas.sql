CREATE TABLE usuarios (
    ID_Usuario INT AUTO_INCREMENT PRIMARY KEY,
    Rol VARCHAR(30) NOT NULL,
    Contrasenia VARCHAR(255) NOT NULL,
    Nombre VARCHAR(100) NOT NULL,
    Apellido VARCHAR(100) NOT NULL,
    Correo VARCHAR(150) NOT NULL UNIQUE,
    Fecha_De_Nacimiento DATE,
    Telefono VARCHAR(50),
    Cedula VARCHAR(20) NOT NULL UNIQUE,
    Genero VARCHAR(20)
);

CREATE TABLE cursos (
    ID_Curso INT AUTO_INCREMENT PRIMARY KEY,
    ID_Docente INT NOT NULL,
    Dataso VARCHAR(100),
    Duracion_Estimada INT,
    Precio DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    Titulo_Curso VARCHAR(150) NOT NULL,
    Descripcion_Curso TEXT,
    Tipo_Curso VARCHAR(50),
    Nivel_Curso VARCHAR(50),

    CONSTRAINT fk_curso_usuario
        FOREIGN KEY (ID_Docente)
        REFERENCES usuarios(ID_Usuario)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

CREATE TABLE inscripciones (
    ID_Inscripcion INT AUTO_INCREMENT PRIMARY KEY,
    ID_Curso INT NOT NULL,
    ID_Usuario INT NOT NULL,
    Fecha_Inscripcion DATETIME DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_inscripcion_curso
        FOREIGN KEY (ID_Curso)
        REFERENCES cursos(ID_Curso)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_inscripcion_usuario
        FOREIGN KEY (ID_Usuario)
        REFERENCES usuarios(ID_Usuario)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT uq_inscripcion_usuario_cursos
        UNIQUE (ID_Curso, ID_Usuario)
);

CREATE TABLE carpetas (
    ID_Carpeta INT AUTO_INCREMENT PRIMARY KEY,
    ID_Curso INT NOT NULL,
    Nombre_Carpeta VARCHAR(150) NOT NULL,

    CONSTRAINT fk_carpeta_curso
        FOREIGN KEY (ID_Curso)
        REFERENCES cursos(ID_Curso)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

CREATE TABLE materiales (
    ID_Material INT AUTO_INCREMENT PRIMARY KEY,
    ID_Carpeta INT NOT NULL,
    Nombre_Material VARCHAR(150) NOT NULL,
    Descripcion_Material TEXT,
    Tipo_Material VARCHAR(50),
    Archivo VARCHAR(255),

    CONSTRAINT fk_material_carpeta
        FOREIGN KEY (ID_Carpeta)
        REFERENCES carpetas(ID_Carpeta)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

INSERT INTO usuarios
(ID_Usuario, Rol, Contrasenia, Nombre, Apellido, Correo, Fecha_De_Nacimiento, Telefono, Cedula, Genero)
VALUES
(1, 'Docente', 'Carlos2026', 'Carlos', 'Rodriguez', 'carlos.rodriguez@email.com', '1985-03-15', '099111111', '12345678', 'Masculino'),
(2, 'Docente', 'Laura2026', 'Laura', 'Martinez', 'laura.martinez@email.com', '1988-07-22', '099222222', '23456789', 'Femenino'),
(3, 'Docente', 'Andres2026', 'Andres', 'Gomez', 'andres.gomez@email.com', '1982-11-10', '099333333', '34567890', 'Masculino'),
(4, 'Estudiante', 'Sofia2026', 'Sofia', 'Fernandez', 'sofia.fernandez@email.com', '2003-01-25', '099444444', '45678901', 'Femenino'),
(5, 'Estudiante', 'Mateo2026', 'Mateo', 'Silva', 'mateo.silva@email.com', '2002-05-18', '099555555', '56789012', 'Masculino'),
(6, 'Estudiante', 'Valentina2026', 'Valentina', 'Lopez', 'valentina.lopez@email.com', '2004-09-12', '099666666', '67890123', 'Femenino'),
(7, 'Estudiante', 'Martin2026', 'Martin', 'Perez', 'martin.perez@email.com', '2001-12-03', '099777777', '78901234', 'Masculino'),
(8, 'Estudiante', 'Camila2026', 'Camila', 'Gonzalez', 'camila.gonzalez@email.com', '2003-06-30', '099888888', '89012345', 'Femenino'),
(9, 'Estudiante', 'Nicolas2026', 'Nicolas', 'Torres', 'nicolas.torres@email.com', '2002-08-14', '099999999', '90123456', 'Masculino'),
(10, 'Estudiante', 'Lucia2026', 'Lucia', 'Ramirez', 'lucia.ramirez@email.com', '2004-02-20', '098101010', '01234567', 'Femenino');

INSERT INTO cursos
(ID_Curso, ID_Docente, Dataso, Duracion_Estimada, Precio, Titulo_Curso, Descripcion_Curso, Tipo_Curso, Nivel_Curso)
VALUES
(1, 1, 'Datos generales', 40, 150.00, 'Programacion en Python', 'Introduccion a la programacion utilizando Python.', 'Programacion', 'Principiante'),
(2, 1, 'Datos generales', 50, 180.00, 'Desarrollo Web', 'Fundamentos del desarrollo de sitios web.', 'Desarrollo Web', 'Intermedio'),
(3, 2, 'Datos generales', 35, 120.00, 'Base de Datos', 'Conceptos fundamentales de bases de datos y SQL.', 'Base de Datos', 'Principiante'),
(4, 2, 'Datos generales', 45, 160.00, 'SQL Avanzado', 'Consultas avanzadas y optimizacion en SQL.', 'Base de Datos', 'Avanzado'),
(5, 3, 'Datos generales', 30, 100.00, 'HTML y CSS', 'Creacion y diseño de paginas web.', 'Diseño Web', 'Principiante'),
(6, 3, 'Datos generales', 40, 140.00, 'JavaScript', 'Fundamentos de programacion con JavaScript.', 'Programacion', 'Intermedio'),
(7, 1, 'Datos generales', 25, 90.00, 'Git y GitHub', 'Control de versiones y trabajo colaborativo.', 'Herramientas', 'Principiante'),
(8, 2, 'Datos generales', 50, 200.00, 'Java', 'Programacion orientada a objetos utilizando Java.', 'Programacion', 'Intermedio'),
(9, 3, 'Datos generales', 35, 130.00, 'Python Avanzado', 'Programacion avanzada y manejo de datos con Python.', 'Programacion', 'Avanzado'),
(10, 1, 'Datos generales', 30, 110.00, 'Introduccion a la Informatica', 'Conceptos basicos de informatica y tecnologia.', 'Informatica', 'Principiante');

INSERT INTO inscripciones
(ID_Inscripcion, ID_Curso, ID_Usuario, Fecha_Inscripcion)
VALUES
(1, 1, 4, '2026-01-10 10:30:00'),
(2, 1, 5, '2026-01-11 11:00:00'),
(3, 2, 6, '2026-01-12 09:15:00'),
(4, 3, 7, '2026-01-13 14:20:00'),
(5, 4, 8, '2026-01-14 16:45:00'),
(6, 5, 9, '2026-01-15 12:10:00'),
(7, 6, 10, '2026-01-16 10:00:00'),
(8, 7, 4, '2026-01-17 15:30:00'),
(9, 8, 5, '2026-01-18 13:45:00'),
(10, 9, 6, '2026-01-19 17:00:00');

INSERT INTO carpetas
(ID_Carpeta, ID_Curso, Nombre_Carpeta)
VALUES
(1, 1, 'Introduccion a Python'),
(2, 2, 'Desarrollo Web'),
(3, 3, 'Fundamentos de SQL'),
(4, 4, 'SQL Avanzado'),
(5, 5, 'HTML y CSS'),
(6, 6, 'JavaScript'),
(7, 7, 'Git y GitHub'),
(8, 8, 'Programacion en Java'),
(9, 9, 'Python Avanzado'),
(10, 10, 'Informatica Basica');

INSERT INTO materiales
(ID_Material, ID_Carpeta, Nombre_Material, Descripcion_Material, Tipo_Material, Archivo)
VALUES
(1, 1, 'Introduccion a Python', 'Material introductorio sobre Python.', 'PDF', 'introduccion_python.pdf'),
(2, 2, 'HTML Basico', 'Material sobre estructura de paginas HTML.', 'PDF', 'html_basico.pdf'),
(3, 3, 'Introduccion a SQL', 'Conceptos basicos de SQL.', 'PDF', 'introduccion_sql.pdf'),
(4, 4, 'Consultas Avanzadas', 'Ejemplos de consultas SQL avanzadas.', 'PDF', 'consultas_avanzadas.pdf'),
(5, 5, 'Estilos CSS', 'Material sobre estilos y diseño con CSS.', 'PDF', 'estilos_css.pdf'),
(6, 6, 'Variables en JavaScript', 'Introduccion a variables y tipos de datos.', 'PDF', 'variables_javascript.pdf'),
(7, 7, 'Comandos Git', 'Guia de comandos principales de Git.', 'PDF', 'comandos_git.pdf'),
(8, 8, 'Programacion Orientada a Objetos', 'Conceptos de POO en Java.', 'PDF', 'poo_java.pdf'),
(9, 9, 'Python y Datos', 'Introduccion al procesamiento de datos con Python.', 'PDF', 'python_datos.pdf'),
(10, 10, 'Conceptos de Informatica', 'Material introductorio de informatica.', 'PDF', 'conceptos_informatica.pdf');

