-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 16-09-2026 a las 20:44:33
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
-- Base de datos: `votaciones`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `encuesta`
--

CREATE TABLE `encuesta` (
  `id` bigint(20) NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `descripcion` varchar(400) NOT NULL,
  `estado` enum('ACTIVA','CERRADA') NOT NULL DEFAULT 'ACTIVA'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `encuesta`
--

INSERT INTO `encuesta` (`id`, `titulo`, `descripcion`, `estado`) VALUES
(5, 'Eleccion de representante del SENA', 'Eleccion democratica para representante', 'CERRADA'),
(6, 'Eleccion de representante del SENA', 'ashjda', 'CERRADA'),
(7, 'Eleccion de representante del SENA', 'aybjlk', 'CERRADA'),
(8, 'Eleccion de representante del SENA', 'sdsgcxv', 'CERRADA'),
(9, 'Eleccion de representante del SENA', 'shgfkjhj', 'CERRADA'),
(10, 'Eleccion de representante del SENA', 'Elecciones para elegir el representante que se convertira en la voz de los aprendices', 'ACTIVA');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ficha`
--

CREATE TABLE `ficha` (
  `id` bigint(20) NOT NULL,
  `numero` varchar(20) NOT NULL,
  `programa` varchar(150) NOT NULL,
  `jornada` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ficha`
--

INSERT INTO `ficha` (`id`, `numero`, `programa`, `jornada`) VALUES
(1, '3232460', 'ADSO', 'Manana'),
(2, '3232461', 'ADSO', 'Manana'),
(3, '3232462', 'ADSO', 'Tarde'),
(4, '3232463', 'ADSO', 'Tarde'),
(5, '3232464', 'TIC', 'Noche'),
(6, '3232465', 'ADSO', 'Noche');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `opcion`
--

CREATE TABLE `opcion` (
  `id` bigint(20) NOT NULL,
  `encuesta_id` bigint(20) NOT NULL,
  `candidato_id` bigint(20) DEFAULT NULL,
  `texto` varchar(150) NOT NULL,
  `foto_url` varchar(255) DEFAULT NULL,
<<<<<<< HEAD
  `propuestas` text DEFAULT NULL,
  `votos_total` int(11) NOT NULL DEFAULT 0
=======
  `votos_total` int(11) NOT NULL DEFAULT 0,
  `propuestas` text DEFAULT NULL
>>>>>>> ef9ed9b225c9021b8be829b82ee1d1bb67aefa19
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `opcion`
--

INSERT INTO `opcion` (`id`, `encuesta_id`, `candidato_id`, `texto`, `foto_url`, `votos_total`, `propuestas`) VALUES
(9, 5, 2, 'Juan David', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRtOxfMf48T7MnGBZZz7sb5khFpEvx5yGveJnS8_jOnypCQ0NOofZ3RCopT&s=10', 1, NULL),
(10, 5, 1, 'Laura Sofia', 'https://thumb.wikimedia.org/wikipedia/commons/thumb/3/32/Juan_Daniel_Oviedo_2025.jpg/250px-Juan_Daniel_Oviedo_2025.jpg?utm_source=es.wikipedia.org&utm_campaign=parser&utm_content=thumbnail', 0, NULL),
(11, 6, NULL, 'Juan david', NULL, 1, NULL),
(12, 6, NULL, 'Laura SOfia', NULL, 0, NULL),
(13, 7, NULL, 'Juan David Calixto', NULL, 0, NULL),
(14, 7, NULL, 'Paola SOfia', NULL, 1, NULL),
(15, 8, 2, 'juan david', NULL, 0, NULL),
(16, 8, NULL, 'paola calixto', NULL, 0, NULL),
(17, 9, 4, 'Carlos Manana 2', NULL, 0, NULL),
(18, 9, 5, 'Laura Tarde 1', NULL, 0, NULL),
(19, 9, 6, 'Jorge Tarde 2', NULL, 1, NULL),
(20, 9, 7, 'Ana Noche 1', NULL, 0, NULL),
(21, 9, 8, 'Pedro Noche 2', NULL, 0, NULL),
(22, 9, 2, 'SofiaAprendiz', NULL, 0, NULL),
(23, 10, 4, 'Carlos Alberto', 'https://i.pinimg.com/1200x/d3/a1/ce/d3a1ceaf64d8aa5a8791e556215fcc55.jpg', 0, 'Mejorar horarios de laboratorio\nJornadas deportivas mensuales'),
(24, 10, 5, 'Laura Rodriguez', 'https://i.pinimg.com/736x/e0/c1/62/e0c16260a50d8105f39fff05a36784ea.jpg', 0, 'Flexibilidad de horarios tarde\nComite de bienestar aprendiz'),
(25, 10, 6, 'Jorge Santiago', 'https://i.pinimg.com/1200x/46/b9/33/46b9332be33df7ab0ff6dd12f198809f.jpg', 0, 'Sala de estudio hasta las 9pm\nRutas de transporte seguras'),
(26, 10, 7, 'Ana Maria', 'https://i.pinimg.com/736x/ea/8e/70/ea8e70cbee2f534a9ffe4d4d87cf32c8.jpg', 0, 'Iluminacion y seguridad nocturna\nCafeteria en horario noche'),
(27, 10, 8, 'Pedro Ismael', 'https://i.pinimg.com/736x/5a/9c/f3/5a9cf3e90352f618939ecfca30a5a969.jpg', 0, 'Tutorias nocturnas entre aprendices\nCanal anonimo de quejas'),
(28, 10, 2, 'Juliana Sofia', 'https://i.pinimg.com/736x/39/7d/a1/397da17353ad7cee227fcdd30dc3b932.jpg', 0, 'Mas torneos y cultura\nMejorar salas de computo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `token_otp`
--

CREATE TABLE `token_otp` (
  `id` bigint(20) NOT NULL,
  `encuesta_id` bigint(20) NOT NULL,
  `usuario_id` bigint(20) NOT NULL,
  `codigo` varchar(10) NOT NULL,
  `estado` enum('DISPONIBLE','USADO') NOT NULL DEFAULT 'DISPONIBLE',
  `expira_en` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `token_otp`
--

INSERT INTO `token_otp` (`id`, `encuesta_id`, `usuario_id`, `codigo`, `estado`, `expira_en`) VALUES
(4, 5, 2, '381238', 'USADO', '2026-09-22 21:02:52'),
(5, 6, 2, '398329', 'USADO', '2026-09-22 21:18:49'),
(6, 7, 2, '080427', 'USADO', '2026-09-22 21:25:09'),
(7, 8, 2, '817311', 'DISPONIBLE', '2026-09-22 21:32:20'),
(8, 9, 2, '754027', 'DISPONIBLE', '2026-09-22 22:00:55'),
(9, 9, 3, '322526', 'DISPONIBLE', '2026-09-22 22:00:55'),
(10, 9, 4, '868696', 'DISPONIBLE', '2026-09-22 22:00:55'),
(11, 9, 5, '623035', 'USADO', '2026-09-22 22:00:55'),
(12, 9, 6, '055808', 'DISPONIBLE', '2026-09-22 22:00:55'),
(13, 9, 7, '820859', 'DISPONIBLE', '2026-09-22 22:00:55'),
(14, 9, 8, '423357', 'DISPONIBLE', '2026-09-22 22:00:55'),
(15, 10, 2, '243815', 'DISPONIBLE', '2026-09-23 12:39:15'),
(16, 10, 3, '253345', 'DISPONIBLE', '2026-09-23 12:39:15'),
(17, 10, 4, '658300', 'DISPONIBLE', '2026-09-23 12:39:15'),
(18, 10, 5, '251974', 'DISPONIBLE', '2026-09-23 12:39:15'),
(19, 10, 6, '566021', 'DISPONIBLE', '2026-09-23 12:39:15'),
(20, 10, 7, '126270', 'DISPONIBLE', '2026-09-23 12:39:15'),
(21, 10, 8, '690885', 'DISPONIBLE', '2026-09-23 12:39:15');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id` bigint(20) NOT NULL,
  `username` varchar(120) NOT NULL,
  `contrasenia` varchar(120) NOT NULL,
  `nombre` varchar(120) NOT NULL,
  `documento` varchar(120) NOT NULL,
  `ficha_id` bigint(20) DEFAULT NULL,
  `rol` enum('ADMIN','USUARIO') NOT NULL DEFAULT 'USUARIO'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id`, `username`, `contrasenia`, `nombre`, `documento`, `ficha_id`, `rol`) VALUES
(1, 'juanda', '123', 'Juan David', '123456789', NULL, 'ADMIN'),
(2, 'sofias', '123', 'Juliana Sofia ', '1058274558', 1, 'USUARIO'),
(3, 'panda', '123', 'Pablo Antonio', '1058274559', 1, 'USUARIO'),
(4, 'carlos_m2', '123', 'Carlos Alberto', '1000000002', 2, 'USUARIO'),
(5, 'laura_t1', '123', 'Laura Rodriguez', '1000000003', 3, 'USUARIO'),
(6, 'jorge_t2', '123', 'Jorge Santiago', '1000000004', 4, 'USUARIO'),
(7, 'ana_n1', '123', 'Ana Maria', '1000000005', 5, 'USUARIO'),
(8, 'pedro_n2', '123', 'Pedro Ismael', '1000000006', 6, 'USUARIO');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `encuesta`
--
ALTER TABLE `encuesta`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `ficha`
--
ALTER TABLE `ficha`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero` (`numero`);

--
-- Indices de la tabla `opcion`
--
ALTER TABLE `opcion`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_opc_enc` (`encuesta_id`),
  ADD KEY `fk_opc_cand` (`candidato_id`);

--
-- Indices de la tabla `token_otp`
--
ALTER TABLE `token_otp`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_encuesta_codigo` (`encuesta_id`,`codigo`),
  ADD UNIQUE KEY `uq_encuesta_usuario` (`encuesta_id`,`usuario_id`),
  ADD KEY `fk_totp_usu` (`usuario_id`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `documento` (`documento`),
  ADD KEY `fk_usu_ficha` (`ficha_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `encuesta`
--
ALTER TABLE `encuesta`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `ficha`
--
ALTER TABLE `ficha`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `opcion`
--
ALTER TABLE `opcion`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT de la tabla `token_otp`
--
ALTER TABLE `token_otp`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `opcion`
--
ALTER TABLE `opcion`
  ADD CONSTRAINT `fk_opc_cand` FOREIGN KEY (`candidato_id`) REFERENCES `usuario` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_opc_enc` FOREIGN KEY (`encuesta_id`) REFERENCES `encuesta` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `token_otp`
--
ALTER TABLE `token_otp`
  ADD CONSTRAINT `fk_totp_enc` FOREIGN KEY (`encuesta_id`) REFERENCES `encuesta` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_totp_usu` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `fk_usu_ficha` FOREIGN KEY (`ficha_id`) REFERENCES `ficha` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
