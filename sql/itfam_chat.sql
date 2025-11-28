-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 20-05-2025 a las 21:00:51
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
-- Base de datos: `itfam_chat`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estado_escribiendo`
--

CREATE TABLE `estado_escribiendo` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `grupo_id` varchar(255) NOT NULL,
  `ultima_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `eventos_escribiendo`
--

CREATE TABLE `eventos_escribiendo` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `grupo_id` int(11) NOT NULL,
  `timestamp` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grupos`
--

CREATE TABLE `grupos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `creador_id` int(11) NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `grupos`
--

INSERT INTO `grupos` (`id`, `nombre`, `creador_id`, `fecha_creacion`) VALUES
(1, 'Grupo General', 1, '2025-04-17 11:07:38'),
(9, 'prueba', 1, '2025-04-17 22:25:01'),
(10, 'prueba1', 1, '2025-04-22 13:12:29'),
(11, 'prueba23', 10, '2025-04-22 13:12:55');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grupo_usuarios`
--

CREATE TABLE `grupo_usuarios` (
  `id` int(11) NOT NULL,
  `grupo_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `estado` enum('pendiente','aceptado') DEFAULT 'pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `grupo_usuarios`
--

INSERT INTO `grupo_usuarios` (`id`, `grupo_id`, `usuario_id`, `estado`) VALUES
(1, 10, 1, 'pendiente'),
(2, 11, 10, 'pendiente'),
(3, 11, 1, 'pendiente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensajes`
--

CREATE TABLE `mensajes` (
  `id` int(11) NOT NULL,
  `emisor_id` int(11) NOT NULL,
  `receptor_id` int(11) NOT NULL,
  `mensaje` text NOT NULL,
  `fecha_envio` timestamp NOT NULL DEFAULT current_timestamp(),
  `leido` tinyint(1) DEFAULT 0,
  `receptor_vio` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `mensajes`
--

INSERT INTO `mensajes` (`id`, `emisor_id`, `receptor_id`, `mensaje`, `fecha_envio`, `leido`, `receptor_vio`) VALUES
(1, 18, 10, 'hola', '2025-04-18 03:17:49', 1, 0),
(2, 10, 19, 'Hola', '2025-04-18 03:32:20', 1, 0),
(3, 19, 10, 'ey', '2025-04-18 03:32:42', 1, 0),
(4, 1, 10, 'hola', '2025-04-21 13:37:45', 1, 0),
(5, 10, 1, 'Hola', '2025-04-21 16:18:26', 1, 0),
(6, 10, 1, 'Ey', '2025-04-21 16:18:51', 1, 0),
(7, 10, 1, 'NL', '2025-04-22 17:55:53', 1, 0),
(8, 1, 2, 'Hola q', '2025-04-23 15:28:06', 1, 0),
(9, 2, 1, 'Hola', '2025-04-23 15:28:23', 1, 0),
(10, 2, 1, 'Ey pero leelo', '2025-04-23 15:28:38', 1, 0),
(11, 1, 2, 'wey klk,  estaba ocupado', '2025-04-28 03:02:14', 1, 0),
(12, 2, 1, 'Weo aqui toy', '2025-04-29 17:11:51', 1, 0),
(13, 1, 10, 'hola mundoooooooooo', '2025-04-29 18:31:34', 0, 0),
(14, 2, 8, 'Holaasssss', '2025-04-30 18:20:38', 0, 0),
(15, 2, 1, 'wasaaaaaaaa', '2025-04-30 18:21:15', 1, 0),
(16, 1, 2, 'feo', '2025-04-30 19:50:20', 1, 0),
(17, 1, 8, 'Holaaaa', '2025-05-01 16:52:29', 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensajes_grupo`
--

CREATE TABLE `mensajes_grupo` (
  `id` int(11) NOT NULL,
  `grupo_id` int(11) DEFAULT NULL,
  `usuario_id` int(11) NOT NULL,
  `mensaje` text NOT NULL,
  `fecha_envio` timestamp NOT NULL DEFAULT current_timestamp(),
  `anclado` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `mensajes_grupo`
--

INSERT INTO `mensajes_grupo` (`id`, `grupo_id`, `usuario_id`, `mensaje`, `fecha_envio`, `anclado`) VALUES
(42, 1, 10, 'hola', '2025-04-17 22:22:17', 0),
(43, 1, 1, 'hola', '2025-04-17 22:23:06', 0),
(44, 1, 2, 'hola', '2025-04-17 22:39:06', 0),
(45, 1, 10, 'hola que tal estan', '2025-04-17 23:16:56', 0),
(46, 1, 2, 'ajajajajajaj', '2025-04-17 23:17:03', 0),
(47, 1, 18, 'saaadadaafasa', '2025-04-18 03:28:11', 0),
(48, 1, 10, 'hola', '2025-04-18 03:33:24', 0),
(49, 1, 19, 'klk', '2025-04-18 03:33:33', 0),
(50, 1, 1, 'HOLA', '2025-04-21 18:41:17', 0),
(51, 1, 2, 'r', '2025-04-23 22:33:18', 0),
(52, 1, 2, 'hola', '2025-04-23 22:33:28', 0),
(53, 1, 2, 'r', '2025-04-23 22:33:35', 0),
(54, 1, 2, 'hola', '2025-04-23 22:33:51', 0),
(55, 1, 1, 'we', '2025-05-19 12:31:01', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensajes_grupo_leidos`
--

CREATE TABLE `mensajes_grupo_leidos` (
  `id` int(11) NOT NULL,
  `mensaje_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `grupo_id` varchar(255) NOT NULL,
  `leido` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `mensajes_grupo_leidos`
--

INSERT INTO `mensajes_grupo_leidos` (`id`, `mensaje_id`, `usuario_id`, `grupo_id`, `leido`) VALUES
(1, 47, 9, '1', 0),
(2, 47, 8, '1', 0),
(3, 47, 2, '1', 1),
(4, 47, 6, '1', 0),
(5, 47, 3, '1', 0),
(6, 47, 10, '1', 1),
(7, 47, 4, '1', 0),
(8, 47, 5, '1', 0),
(9, 47, 7, '1', 0),
(10, 47, 14, '1', 0),
(11, 47, 17, '1', 0),
(12, 47, 11, '1', 0),
(13, 47, 12, '1', 0),
(14, 47, 13, '1', 0),
(15, 47, 16, '1', 0),
(16, 47, 15, '1', 0),
(17, 47, 1, '1', 1),
(32, 48, 9, '1', 0),
(33, 48, 8, '1', 0),
(34, 48, 2, '1', 1),
(35, 48, 6, '1', 0),
(36, 48, 3, '1', 0),
(37, 48, 4, '1', 0),
(38, 48, 5, '1', 0),
(39, 48, 7, '1', 0),
(40, 48, 14, '1', 0),
(41, 48, 17, '1', 0),
(42, 48, 18, '1', 0),
(43, 48, 19, '1', 1),
(44, 48, 11, '1', 0),
(45, 48, 12, '1', 0),
(46, 48, 13, '1', 0),
(47, 48, 16, '1', 0),
(48, 48, 15, '1', 0),
(49, 48, 1, '1', 1),
(63, 49, 9, '1', 0),
(64, 49, 8, '1', 0),
(65, 49, 2, '1', 1),
(66, 49, 6, '1', 0),
(67, 49, 3, '1', 0),
(68, 49, 10, '1', 1),
(69, 49, 4, '1', 0),
(70, 49, 5, '1', 0),
(71, 49, 7, '1', 0),
(72, 49, 14, '1', 0),
(73, 49, 17, '1', 0),
(74, 49, 18, '1', 0),
(75, 49, 11, '1', 0),
(76, 49, 12, '1', 0),
(77, 49, 13, '1', 0),
(78, 49, 16, '1', 0),
(79, 49, 15, '1', 0),
(80, 49, 1, '1', 1),
(81, 50, 9, '1', 0),
(82, 50, 8, '1', 0),
(83, 50, 2, '1', 1),
(84, 50, 6, '1', 0),
(85, 50, 3, '1', 0),
(86, 50, 10, '1', 1),
(87, 50, 4, '1', 0),
(88, 50, 5, '1', 0),
(89, 50, 7, '1', 0),
(90, 50, 14, '1', 0),
(91, 50, 17, '1', 0),
(92, 50, 18, '1', 0),
(93, 50, 19, '1', 0),
(94, 50, 11, '1', 0),
(95, 50, 12, '1', 0),
(96, 50, 13, '1', 0),
(97, 50, 16, '1', 0),
(98, 50, 15, '1', 0),
(99, 51, 9, '1', 0),
(100, 51, 8, '1', 0),
(101, 51, 6, '1', 0),
(102, 51, 3, '1', 0),
(103, 51, 10, '1', 0),
(104, 51, 4, '1', 0),
(105, 51, 5, '1', 0),
(106, 51, 7, '1', 0),
(107, 51, 14, '1', 0),
(108, 51, 17, '1', 0),
(109, 51, 18, '1', 0),
(110, 51, 19, '1', 0),
(111, 51, 11, '1', 0),
(112, 51, 12, '1', 0),
(113, 51, 13, '1', 0),
(114, 51, 16, '1', 0),
(115, 51, 15, '1', 0),
(116, 51, 1, '1', 1),
(130, 52, 9, '1', 0),
(131, 52, 8, '1', 0),
(132, 52, 6, '1', 0),
(133, 52, 3, '1', 0),
(134, 52, 10, '1', 0),
(135, 52, 4, '1', 0),
(136, 52, 5, '1', 0),
(137, 52, 7, '1', 0),
(138, 52, 14, '1', 0),
(139, 52, 17, '1', 0),
(140, 52, 18, '1', 0),
(141, 52, 19, '1', 0),
(142, 52, 11, '1', 0),
(143, 52, 12, '1', 0),
(144, 52, 13, '1', 0),
(145, 52, 16, '1', 0),
(146, 52, 15, '1', 0),
(147, 52, 1, '1', 1),
(161, 53, 9, '1', 0),
(162, 53, 8, '1', 0),
(163, 53, 6, '1', 0),
(164, 53, 3, '1', 0),
(165, 53, 10, '1', 0),
(166, 53, 4, '1', 0),
(167, 53, 5, '1', 0),
(168, 53, 7, '1', 0),
(169, 53, 14, '1', 0),
(170, 53, 17, '1', 0),
(171, 53, 18, '1', 0),
(172, 53, 19, '1', 0),
(173, 53, 11, '1', 0),
(174, 53, 12, '1', 0),
(175, 53, 13, '1', 0),
(176, 53, 16, '1', 0),
(177, 53, 15, '1', 0),
(178, 53, 1, '1', 1),
(192, 54, 9, '1', 0),
(193, 54, 8, '1', 0),
(194, 54, 6, '1', 0),
(195, 54, 3, '1', 0),
(196, 54, 10, '1', 0),
(197, 54, 4, '1', 0),
(198, 54, 5, '1', 0),
(199, 54, 7, '1', 0),
(200, 54, 14, '1', 0),
(201, 54, 17, '1', 0),
(202, 54, 18, '1', 0),
(203, 54, 19, '1', 0),
(204, 54, 11, '1', 0),
(205, 54, 12, '1', 0),
(206, 54, 13, '1', 0),
(207, 54, 16, '1', 0),
(208, 54, 15, '1', 0),
(209, 54, 1, '1', 1),
(210, 55, 9, '1', 0),
(211, 55, 8, '1', 0),
(212, 55, 2, '1', 0),
(213, 55, 6, '1', 0),
(214, 55, 3, '1', 0),
(215, 55, 10, '1', 0),
(216, 55, 4, '1', 0),
(217, 55, 5, '1', 0),
(218, 55, 7, '1', 0),
(219, 55, 14, '1', 0),
(220, 55, 17, '1', 0),
(221, 55, 18, '1', 0),
(222, 55, 19, '1', 0),
(223, 55, 11, '1', 0),
(224, 55, 12, '1', 0),
(225, 55, 13, '1', 0),
(226, 55, 16, '1', 0),
(227, 55, 15, '1', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitudes_grupo`
--

CREATE TABLE `solicitudes_grupo` (
  `id` int(11) NOT NULL,
  `grupo_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `estado` enum('pendiente','aceptado','rechazado') DEFAULT 'pendiente',
  `fecha_solicitud` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `solicitudes_grupo`
--

INSERT INTO `solicitudes_grupo` (`id`, `grupo_id`, `usuario_id`, `estado`, `fecha_solicitud`) VALUES
(1, 10, 2, 'pendiente', '2025-04-22 13:12:29'),
(2, 10, 11, 'pendiente', '2025-04-22 13:12:29'),
(3, 10, 16, 'pendiente', '2025-04-22 13:12:29'),
(4, 11, 1, 'aceptado', '2025-04-22 13:12:55'),
(5, 11, 2, 'pendiente', '2025-04-22 13:12:55');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `contraseña` varchar(255) NOT NULL,
  `curso` varchar(100) DEFAULT NULL,
  `rol` enum('Estudiante','Profesor','Orientador') NOT NULL,
  `foto_perfil` varchar(255) DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `ultima_actividad` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `apellido`, `correo`, `telefono`, `contraseña`, `curso`, `rol`, `foto_perfil`, `fecha_creacion`, `ultima_actividad`) VALUES
(1, 'Nick Steven', 'Maria Jerez', 'uncorreodeejemplo@gmail.com', '829-835-4979', '$2y$10$/bgNPf9NdrIdEgtjAKm/tuYvFEy2/Zj6qP9z5F5/3kL3X9t7uNkbW', '5B Desarrollo y Administración de Aplicaciones Informáticas', 'Estudiante', '680b2bb53846a.png', '2025-04-13 21:35:57', '2025-05-19 12:31:01'),
(2, 'q', 'a', '12@1', '12', '$2y$10$0/15fc6TQsP2dNiVR2pJY.iFC7UcCk3VMhQFLS2Nmf2kbgL9rMN4i', '', 'Profesor', 'img/perfiles/67fc40946c140.png', '2025-04-13 22:54:12', '2025-04-30 18:21:41'),
(3, 'foul', 'nose', '1@1', '829-835-4979', '$2y$10$MAI0fuAe2UTOF7puM02gGe9CaueZ10m2DYC1Q4/oucnJXZaG/WJl.', '', 'Orientador', '', '2025-04-13 23:15:23', NULL),
(4, '2', '2', '22@2', '8298354979', '$2y$10$0GdR9qmxYA2KFU8fzFkrcuKtGF5b11oSZ6bfCmlJel2oJhVrYtjlm', '', 'Estudiante', '', '2025-04-14 03:39:42', NULL),
(5, '21', '4', '2@3', '21', '$2y$10$6JCN8g/9Qc7eGdPmwUp3lemUrb0cidcKcuGQQt/ITFs45aj5yllna', '', 'Estudiante', '', '2025-04-14 07:01:29', NULL),
(6, '11', '1', '13@1', '1', '$2y$10$fOFPw38MqxisOn0.WT2CJebY8x0xfE6nJQcMi17q6eoQmBtvnnExG', '', 'Orientador', '', '2025-04-14 07:02:32', NULL),
(7, '4', '4', '44@g', '4', '$2y$10$.fOsN/k.YBkOT1h4yoQgiuaBsBUEpxFN1gzO496A8sQVUWEX0bNW6', '', 'Profesor', 'img/perfiles/67fcb3a1e356c.png', '2025-04-14 07:05:06', NULL),
(8, '11', '11', '112@2', '11', '$2y$10$1XmV.Xa/zo5KeJVg3W4/MOIUo9My2brF.pPDkyaNeLkxTEcuL2O/G', '', 'Orientador', 'img/perfiles/67fcb477d5137.png', '2025-04-14 07:08:39', '2025-04-18 02:58:27'),
(9, '111', '111', '1111@11', '11', '$2y$10$1uGvHBDDZhqfWxWKinXMle2dOTOrajg9kIbl/VWTEUCp5G9nNoNHa', '', 'Profesor', '', '2025-04-14 07:09:24', NULL),
(10, 'nombre', 'real', '221@2', '1', '$2y$10$r2KLTbSS0aNzleYjrHz0OuGk6ePcbdRug5H4DTrP8Lh1OcH9eXMjS', '', 'Estudiante', NULL, '2025-04-14 07:10:05', '2025-04-22 17:28:16'),
(11, 'Nick Jonas', 'Maria Jerez', 'jonas@j', '829-835-4979', '$2y$10$uKSf3oN.VsuYCXXPYDKtbukgfkV3hhCQcXdsRX3vBWPJhY82hLKvG', '5A Desarrollo y Administración de Aplicaciones Informáticas', 'Estudiante', '	\r\nimg/perfiles/67ff6c002a6ce.png', '2025-04-16 08:36:16', NULL),
(12, 'Ni', 'Maria Jerez', 'Ni@n', '829-835-4979', '$2y$10$3/xuySC.TWcQMGX.3xGUheP3Fs81upCAqA/yE5VQVeFiwtOpsz/je', '', 'Estudiante', NULL, '2025-04-17 06:24:57', NULL),
(13, 'Ni', 'Maria Jerez', 'nii@i', '829-835-4979', '$2y$10$fxwaz.aGclUaHj16as1AJue8t2koXg0zx8KYy/0OoLTtLCwt/q59O', '4A Desarrollo y Administración de Aplicaciones Informáticas', 'Estudiante', '6800a493d081b.png', '2025-04-17 06:49:55', NULL),
(14, 'blablabla', 'bla', 'bla@bla', '1', '$2y$10$/8WQViXrp6i370k79gKQ4.Kwf16rK4pSCpfAw5gENjgqjU8VL1HdW', '4A Desarrollo y Administración de Aplicaciones Informáticas', 'Estudiante', '6800a4c6f10e8.png', '2025-04-17 06:50:47', NULL),
(15, 'prueba', 'prueba', 'prueba@prueba', '829-835-4979', '$2y$10$bSEY0mQamQJIi/u1JGputuNRyr2tgKHBBW4CWweXevqObfaRfoTCO', '5B Desarrollo y Administración de Aplicaciones Informáticas', 'Estudiante', '6800c397ec659.png', '2025-04-17 09:02:16', NULL),
(16, 'Nick Steven', 'Maria Jerez', 'prueba3@prueba3', '829-835-4979', '$2y$10$bTLuoFAE4t0mOShXn.IzpO5MbP8PurvW5Y7pBHGC/ODcLBx.eyoXu', '5B Desarrollo y Administración de Aplicaciones Informáticas', 'Estudiante', '6801becdae578.jpg', '2025-04-18 02:54:05', NULL),
(17, 'nombre1', 'apellido1', 'correo1@correo', '829-835-4979', '$2y$10$8NQ45e.d/1nCEwOsn6Zsu.Cc3F9PcD80HJZSQ4GtQRnHphPc3onsq', '5B Desarrollo y Administración de Aplicaciones Informáticas', 'Estudiante', '6801c33a35ff4.jpg', '2025-04-18 03:12:58', NULL),
(18, 'nombre2', 'apellido2', 'correo2@correo', '829-835-4979', '$2y$10$v54Pbj.85zgemCNB9B9umu//UZAUzXrHDeQ2KuyGWYJPoj0Z6jNfu', '5B Desarrollo y Administración de Aplicaciones Informáticas', 'Estudiante', '6801c6dfe763c.jpg', '2025-04-18 03:16:41', '2025-04-18 03:28:11'),
(19, 'nombre real', 'apellido ', 'correo3@correo', '829-835-49791', '$2y$10$fEeOhSBQhM4AjuTrKjC4CeFbcHIJUP/Yftig2ICdOR5OIlntEJgVy', '5B Desarrollo y Administración de Aplicaciones Informáticas', 'Estudiante', '6801c786ad9bd.jpg', '2025-04-18 03:30:17', '2025-04-18 03:58:22');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `estado_escribiendo`
--
ALTER TABLE `estado_escribiendo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `eventos_escribiendo`
--
ALTER TABLE `eventos_escribiendo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `grupo_id` (`grupo_id`);

--
-- Indices de la tabla `grupos`
--
ALTER TABLE `grupos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_nombre` (`nombre`),
  ADD KEY `creador_id` (`creador_id`);

--
-- Indices de la tabla `grupo_usuarios`
--
ALTER TABLE `grupo_usuarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `grupo_id` (`grupo_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `emisor_id` (`emisor_id`),
  ADD KEY `receptor_id` (`receptor_id`);

--
-- Indices de la tabla `mensajes_grupo`
--
ALTER TABLE `mensajes_grupo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `grupo_id` (`grupo_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `mensajes_grupo_leidos`
--
ALTER TABLE `mensajes_grupo_leidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mensaje_id` (`mensaje_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `solicitudes_grupo`
--
ALTER TABLE `solicitudes_grupo`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `grupo_usuario` (`grupo_id`,`usuario_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `estado_escribiendo`
--
ALTER TABLE `estado_escribiendo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `eventos_escribiendo`
--
ALTER TABLE `eventos_escribiendo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `grupos`
--
ALTER TABLE `grupos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `grupo_usuarios`
--
ALTER TABLE `grupo_usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `mensajes_grupo`
--
ALTER TABLE `mensajes_grupo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT de la tabla `mensajes_grupo_leidos`
--
ALTER TABLE `mensajes_grupo_leidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=228;

--
-- AUTO_INCREMENT de la tabla `solicitudes_grupo`
--
ALTER TABLE `solicitudes_grupo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `estado_escribiendo`
--
ALTER TABLE `estado_escribiendo`
  ADD CONSTRAINT `estado_escribiendo_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `eventos_escribiendo`
--
ALTER TABLE `eventos_escribiendo`
  ADD CONSTRAINT `eventos_escribiendo_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `eventos_escribiendo_ibfk_2` FOREIGN KEY (`grupo_id`) REFERENCES `grupos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `grupos`
--
ALTER TABLE `grupos`
  ADD CONSTRAINT `grupos_ibfk_1` FOREIGN KEY (`creador_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `grupo_usuarios`
--
ALTER TABLE `grupo_usuarios`
  ADD CONSTRAINT `grupo_usuarios_ibfk_1` FOREIGN KEY (`grupo_id`) REFERENCES `grupos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `grupo_usuarios_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `mensajes`
--
ALTER TABLE `mensajes`
  ADD CONSTRAINT `mensajes_ibfk_1` FOREIGN KEY (`emisor_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mensajes_ibfk_2` FOREIGN KEY (`receptor_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `mensajes_grupo`
--
ALTER TABLE `mensajes_grupo`
  ADD CONSTRAINT `mensajes_grupo_ibfk_1` FOREIGN KEY (`grupo_id`) REFERENCES `grupos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mensajes_grupo_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `mensajes_grupo_leidos`
--
ALTER TABLE `mensajes_grupo_leidos`
  ADD CONSTRAINT `mensajes_grupo_leidos_ibfk_1` FOREIGN KEY (`mensaje_id`) REFERENCES `mensajes_grupo` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mensajes_grupo_leidos_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `solicitudes_grupo`
--
ALTER TABLE `solicitudes_grupo`
  ADD CONSTRAINT `solicitudes_grupo_ibfk_1` FOREIGN KEY (`grupo_id`) REFERENCES `grupos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `solicitudes_grupo_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
