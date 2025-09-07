-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 06-05-2025 a las 14:16:50
-- Versión del servidor: 10.11.11-MariaDB-cll-lve-log
-- Versión de PHP: 8.3.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `dentistap_proyin`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `blog`
--

CREATE TABLE `blog` (
  `id_blog` smallint(5) UNSIGNED NOT NULL,
  `id_dentista` tinyint(3) NOT NULL,
  `contenido` text NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `blog`
--

INSERT INTO `blog` (`id_blog`, `id_dentista`, `contenido`, `fecha`) VALUES
(1, 1, 'Hola, ¿cómo estan? Espero que muy bien, no olviden que la primera valoración es gratuita.', '2025-03-28 13:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `citas`
--

CREATE TABLE `citas` (
  `id_cita` smallint(5) UNSIGNED NOT NULL,
  `id_paciente` tinyint(3) UNSIGNED NOT NULL,
  `id_servicio` tinyint(3) UNSIGNED NOT NULL,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `detalles` varchar(50) NOT NULL,
  `estado` enum('pendiente','confirmada','cancelada') DEFAULT 'pendiente',
  `hora_fin` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `galeria`
--

CREATE TABLE `galeria` (
  `id_galeria` smallint(5) UNSIGNED NOT NULL,
  `url` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `galeria`
--

INSERT INTO `galeria` (`id_galeria`, `url`, `descripcion`, `fecha`) VALUES
(23, 'img/galeria_exitos.png', '', '2025-03-31 01:42:19');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicios`
--

CREATE TABLE `servicios` (
  `id_servicio` tinyint(3) UNSIGNED NOT NULL,
  `nombre` varchar(35) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(7,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `servicios`
--

INSERT INTO `servicios` (`id_servicio`, `nombre`, `descripcion`, `precio`) VALUES
(1, 'Limpieza Dental', 'Duración: 45 minutos. Elimina placa y sarro para mantener una sonrisa saludable.\')', 425.00),
(2, 'Revisión / Consulta Inicial', 'Duración: 30 minutos. Evaluación y diagnóstico para determinar el tratamiento necesario.', 340.00),
(3, 'Blanqueamiento Dental', 'Duración: 60 minutos. Aclara el color de tus dientes para una sonrisa más brillante.', 1700.00),
(4, 'Obturación (empaste)', 'Duración: 60 minutos. Reparación de dientes con empaste para restaurar su función.', 680.00),
(5, 'Extracción Dental Simple', 'Duración: 45 minutos. Extracción de un diente dañado o no recuperable.', 850.00),
(6, 'Extracción de Muela del Juicio', 'Duración: 90 minutos. Extracción de muelas del juicio que causan molestias.', 1700.00),
(7, 'Colocación de Carillas', 'Duración: 90 minutos. Mejora estética dental con carillas en una sesión.', 2550.00),
(8, 'Tratamiento de Conducto', 'Duración: 90 minutos. Tratamiento para dientes con infecciones o daños en el nervio.', 3000.00),
(9, 'Colocación de Corona Dental', 'Duración: 90 minutos (requiere 2 citas). Colocación de una corona para restaurar un diente dañado.', 5100.00),
(10, 'Colocación de Implante Dental', 'Duración: 120 minutos. Sustitución de dientes perdidos con implantes de titanio.', 17000.00),
(11, 'Ortodoncia', 'Duración: 60 minutos. Consulta o mantenimiento de ortodoncia para mejorar la alineación dental.', 680.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `testimonios`
--

CREATE TABLE `testimonios` (
  `id_testimonio` smallint(5) UNSIGNED NOT NULL,
  `id_paciente` tinyint(3) UNSIGNED NOT NULL,
  `comentario` text NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `testimonios`
--

INSERT INTO `testimonios` (`id_testimonio`, `id_paciente`, `comentario`, `fecha`) VALUES
(2, 1, '\"Tenía mucho miedo de ir al dentista, pero la Dra. Ximena Martínez me hizo sentir seguro desde el primer momento. Fue amable, profesional y muy detallista. Me realizó una limpieza dental y una ortodoncia, ¡el resultado fue increíble! Ahora sonrío con confianza gracias a ella. 100% recomendada.\"', '2025-03-28 14:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` tinyint(3) UNSIGNED NOT NULL,
  `nombre` varchar(15) NOT NULL,
  `segundo_nombre` varchar(15) NOT NULL,
  `apellido_paterno` varchar(15) NOT NULL,
  `apellido_materno` varchar(15) DEFAULT NULL,
  `correo` varchar(50) NOT NULL,
  `telefono` char(12) DEFAULT NULL,
  `tipo` enum('dentista','paciente') NOT NULL,
  `contraseña` char(30) NOT NULL,
  `recuperar_contraseña` char(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `segundo_nombre`, `apellido_paterno`, `apellido_materno`, `correo`, `telefono`, `tipo`, `contraseña`, `recuperar_contraseña`) VALUES
(1, 'admin', 'admin', 'admin', 'admin', 'admin@gmail.com', '528111111111', 'dentista', 'admin123', '');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `blog`
--
ALTER TABLE `blog`
  ADD PRIMARY KEY (`id_blog`),
  ADD UNIQUE KEY `id_dentista` (`id_dentista`);

--
-- Indices de la tabla `citas`
--
ALTER TABLE `citas`
  ADD PRIMARY KEY (`id_cita`),
  ADD KEY `citas_ibfk_2` (`id_servicio`);

--
-- Indices de la tabla `galeria`
--
ALTER TABLE `galeria`
  ADD PRIMARY KEY (`id_galeria`);

--
-- Indices de la tabla `servicios`
--
ALTER TABLE `servicios`
  ADD PRIMARY KEY (`id_servicio`);

--
-- Indices de la tabla `testimonios`
--
ALTER TABLE `testimonios`
  ADD PRIMARY KEY (`id_testimonio`),
  ADD UNIQUE KEY `id_paciente` (`id_paciente`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `blog`
--
ALTER TABLE `blog`
  MODIFY `id_blog` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `citas`
--
ALTER TABLE `citas`
  MODIFY `id_cita` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `galeria`
--
ALTER TABLE `galeria`
  MODIFY `id_galeria` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `servicios`
--
ALTER TABLE `servicios`
  MODIFY `id_servicio` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `testimonios`
--
ALTER TABLE `testimonios`
  MODIFY `id_testimonio` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `citas`
--
ALTER TABLE `citas`
  ADD CONSTRAINT `citas_ibfk_2` FOREIGN KEY (`id_servicio`) REFERENCES `servicios` (`id_servicio`) ON DELETE CASCADE;

--
-- Filtros para la tabla `testimonios`
--
ALTER TABLE `testimonios`
  ADD CONSTRAINT `testimonios_ibfk_1` FOREIGN KEY (`id_paciente`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
