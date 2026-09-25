-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 25-09-2026 a las 23:00:09
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `aprendomo`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carpetas`
--

CREATE TABLE `carpetas` (
  `ID_Carpeta` int(11) NOT NULL,
  `ID_Curso` int(11) NOT NULL,
  `Nombre_Carpeta` varchar(150) NOT NULL,
  `Orden` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cursos`
--

CREATE TABLE `cursos` (
  `ID_Curso` int(11) NOT NULL,
  `ID_Docente` int(11) NOT NULL,
  `ID_Usuario` int(11) DEFAULT NULL,
  `Titulo_Curso` text NOT NULL,
  `Descripcion_Curso` text NOT NULL,
  `Tipo_Curso` varchar(50) NOT NULL,
  `Nivel_Curso` text NOT NULL,
  `Duracion_Estimada` int(20) NOT NULL,
  `Precio` int(20) NOT NULL,
  `Dataso` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cursos`
--

INSERT INTO `cursos` (`ID_Curso`, `ID_Docente`, `ID_Usuario`, `Titulo_Curso`, `Descripcion_Curso`, `Tipo_Curso`, `Nivel_Curso`, `Duracion_Estimada`, `Precio`, `Dataso`) VALUES
(1, 10, NULL, 'Queseso', 'ERWERWRE', 'Animaciones', 'Bajo', 12, 10000, '1789521635-filtervar.png'),
(2, 10, NULL, 'Queseso', 'ERWERWRE', 'Animaciones', 'Bajo', 12, 10000, '1789521715-filtervar.png'),
(3, 10, NULL, 'Desarrollo bien crack', 'Descripción insana:', 'Informática', 'Bajo', 10, 13000, '1789521919-oye no nada.jpg'),
(4, 10, NULL, 'Filosofía del queso', 'Hacemos empanas de carne', 'Hardware', 'Alto', 100, 1000, '1789655408-372728b97e569622acc63955cb84bbd8.jpg'),
(5, 10, NULL, 'Filosofía del queso2', 'Si muy buena la materia', 'Animaciones', 'Bajo', 10, 12341, '1789954625-APRENDOMO LOGO-Photoroom.png');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inscripciones`
--

CREATE TABLE `inscripciones` (
  `ID_Usuario` int(11) NOT NULL,
  `ID_Curso` int(11) NOT NULL,
  `Fecha_Inscripcion` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `inscripciones`
--

INSERT INTO `inscripciones` (`ID_Usuario`, `ID_Curso`, `Fecha_Inscripcion`) VALUES
(9, 2, '0000-00-00 00:00:00'),
(9, 3, '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materiales`
--

CREATE TABLE `materiales` (
  `ID_Material` int(11) NOT NULL,
  `ID_Curso` int(11) NOT NULL,
  `ID_Carpeta` int(11) DEFAULT NULL,
  `Titulo_Material` varchar(200) NOT NULL,
  `Tipo_Material` enum('enlace','tarea','archivo','foro','video','audio') NOT NULL DEFAULT 'archivo',
  `Contenido_URL` text DEFAULT NULL,
  `Fecha_De_Vencimiento` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `ID_Usuario` int(11) NOT NULL,
  `Nombre` varchar(20) DEFAULT NULL,
  `Apellido` varchar(20) DEFAULT NULL,
  `Cedula` int(11) NOT NULL,
  `Fecha_De_Nacimiento` date NOT NULL,
  `Correo` varchar(50) DEFAULT NULL,
  `Contrasenia` varchar(50) NOT NULL,
  `Telefono` int(11) NOT NULL,
  `Genero` varchar(20) NOT NULL,
  `Rol` varchar(20) NOT NULL,
  `Foto_Perfil` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`ID_Usuario`, `Nombre`, `Apellido`, `Cedula`, `Fecha_De_Nacimiento`, `Correo`, `Contrasenia`, `Telefono`, `Genero`, `Rol`, `Foto_Perfil`) VALUES
(9, 'Thiago', 'García', 0, '0000-00-00', 'garcía@gmail.com', 'garcia89', 0, 'Masculino', 'Docente', NULL),
(10, 'JoseJose', 'Apellido', 213134, '2026-09-18', 'thiagoagustin2005garcia@gmail.', 'queseson7', 32424, 'Masculino', 'Docente', NULL),
(11, 'trte', 'Jjson', 35242432, '2026-09-23', 'levantate@gmail.com', 'yayo', 5353, 'Masculino', 'Docente', NULL),
(12, 'trte', 'Jjson', 35242432, '2026-09-23', 'levantate2@gmail.com', 'ohiheiuthe', 5353, 'Masculino', 'Docente', NULL),
(14, 'juanito', 'jorge', 2342432, '2026-09-15', 'german@gmail.com', 'queseso', 2147483647, 'Masculino', 'Docente', NULL),
(15, 'Juanelo', 'Zorillae', 23424, '0000-00-00', 'quesoe@gmail.com', 'tefydytd', 65664654, 'Masculino', 'Docente', NULL),
(16, 'Juanelo', 'Zorillae', 23424, '2026-10-02', 'quesoue@gmail.com', 'yfuffu', 65664654, 'Masculino', 'Docente', NULL),
(17, 'trte', 'Choclos', 1423424, '0000-00-00', 'quesuardo@gmail.com', 'pepito', 453543, 'Masculino', 'Docente', NULL),
(19, 'Lucas', 'Silva', 55067283, '2004-08-17', 'lucasls@gmail.com', 'lucasmilanesa', 92602280, 'Masculino', 'Docente', '1790318633-20220212_221531.jpg');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `carpetas`
--
ALTER TABLE `carpetas`
  ADD PRIMARY KEY (`ID_Carpeta`),
  ADD KEY `fk_carpeta_curso` (`ID_Curso`);

--
-- Indices de la tabla `cursos`
--
ALTER TABLE `cursos`
  ADD PRIMARY KEY (`ID_Curso`),
  ADD KEY `fk_cursos_docente` (`ID_Docente`),
  ADD KEY `fk_cursos_usuario` (`ID_Usuario`);

--
-- Indices de la tabla `inscripciones`
--
ALTER TABLE `inscripciones`
  ADD PRIMARY KEY (`ID_Usuario`,`ID_Curso`),
  ADD KEY `fk_inscripcion_curso` (`ID_Curso`);

--
-- Indices de la tabla `materiales`
--
ALTER TABLE `materiales`
  ADD PRIMARY KEY (`ID_Material`),
  ADD KEY `fk_material_curso` (`ID_Curso`),
  ADD KEY `fk_material_carpeta` (`ID_Carpeta`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`ID_Usuario`),
  ADD UNIQUE KEY `Correo` (`Correo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `carpetas`
--
ALTER TABLE `carpetas`
  MODIFY `ID_Carpeta` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `carpetas`
--
ALTER TABLE `carpetas`
  ADD CONSTRAINT `fk_carpeta_curso` FOREIGN KEY (`ID_Curso`) REFERENCES `cursos` (`ID_Curso`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `cursos`
--
ALTER TABLE `cursos`
  ADD CONSTRAINT `fk_cursos_docente` FOREIGN KEY (`ID_Docente`) REFERENCES `usuarios` (`ID_Usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_cursos_usuario` FOREIGN KEY (`ID_Usuario`) REFERENCES `usuarios` (`ID_Usuario`);

--
-- Filtros para la tabla `inscripciones`
--
ALTER TABLE `inscripciones`
  ADD CONSTRAINT `fk_inscripcion_curso` FOREIGN KEY (`ID_Curso`) REFERENCES `cursos` (`ID_Curso`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_inscripcion_usuario` FOREIGN KEY (`ID_Usuario`) REFERENCES `usuarios` (`ID_Usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `materiales`
--
ALTER TABLE `materiales`
  ADD CONSTRAINT `fk_material_carpeta` FOREIGN KEY (`ID_Carpeta`) REFERENCES `carpetas` (`ID_Carpeta`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_material_curso` FOREIGN KEY (`ID_Curso`) REFERENCES `cursos` (`ID_Curso`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
