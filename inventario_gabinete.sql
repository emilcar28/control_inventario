-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 22-05-2025 a las 21:16:49
-- Versión del servidor: 8.0.40
-- Versión de PHP: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `inventario_gabinete`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `articulos`
--

CREATE TABLE `articulos` (
  `id` int NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text,
  `cantidad` int DEFAULT '0',
  `categoria_id` int DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `stock` int DEFAULT '0',
  `codigo_qr` varchar(255) DEFAULT NULL,
  `ubicacion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `articulos`
--

INSERT INTO `articulos` (`id`, `nombre`, `descripcion`, `cantidad`, `categoria_id`, `imagen`, `stock`, `codigo_qr`, `ubicacion`) VALUES
(1, 'Simman 3G', NULL, 1, 1, 'simman3g.jpg', 1, NULL, 'GSC-Moreno-ShockRoom A'),
(2, 'Litle Anne Baby', NULL, -2, 1, 'Litle Anne Baby.jpg', 2, NULL, 'GSC-Campus Sgto Cabral');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`) VALUES
(1, 'Simuladores'),
(2, 'Repuestos');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientos`
--

CREATE TABLE `movimientos` (
  `id` int NOT NULL,
  `articulo_id` int NOT NULL,
  `cantidad` int NOT NULL,
  `tipo` enum('entrada','salida','alta','baja','en préstamo','devolución','en mantenimiento','traslado','reparado') NOT NULL,
  `usuario_id` int NOT NULL,
  `fecha` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `destino` varchar(255) DEFAULT NULL,
  `receptor` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `movimientos`
--

INSERT INTO `movimientos` (`id`, `articulo_id`, `cantidad`, `tipo`, `usuario_id`, `fecha`, `destino`, `receptor`) VALUES
(1, 1, 1, 'entrada', 2, '2025-05-13 19:17:22', NULL, NULL),
(2, 1, 1, 'traslado', 2, '2025-05-14 22:30:41', NULL, NULL),
(3, 1, 1, 'devolución', 2, '2025-05-14 22:30:58', NULL, NULL),
(4, 1, 1, 'en préstamo', 6, '2025-05-15 13:43:18', NULL, NULL),
(5, 1, 1, 'devolución', 6, '2025-05-15 13:43:43', NULL, NULL),
(6, 1, 1, 'en préstamo', 6, '2025-05-17 02:47:56', 'Kinesiologia (Prueba)', 'Gisela Cabrera'),
(7, 1, 1, 'devolución', 6, '2025-05-18 01:22:46', 'GSC', 'Ruben ¨Palacios'),
(8, 2, 2, 'en préstamo', 1, '2025-05-20 18:37:02', 'GSC prueba', 'Ruben ¨Palacios');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int NOT NULL,
  `usuario` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('admin','usuario') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `usuario`, `password`, `rol`) VALUES
(1, 'admin', '$2y$10$UN7cAvwS.RcjYMu6Tv5V7u87FmjCmHIny38WiL.zc.N.SldfYuw0a', 'admin'),
(2, 'Laura', '$2y$10$2hpGnkDA4Wpj0VFdKY.u8efd7UVHGROFMEnTr4FEGJglTYBYUJV9C', 'usuario'),
(3, 'Fidel', '$2y$10$04jr/ZoxZJNtq7i5f4yZguwUot8G6zWMe92MOZP3c21M.MI/nmle6', 'usuario'),
(4, 'Gisela', '$2y$10$xU6li0zVKNATovkFfYhcWuFejbiSzX8tnDwPtan8CaWa7.OIkz71W', 'usuario'),
(5, 'Luis', '$2y$10$u9zMkAMQHKHMwItz36oFYOpnpUTKioVeYFsaqtZ85t0NFhmtATIQC', 'usuario'),
(6, 'Ruben', '$2y$10$xZVKEzrc6ctEBnBXPz9.KuoMQ318w97NU5i6I8x2FALuJ.JgXbcAe', 'usuario');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `articulos`
--
ALTER TABLE `articulos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `movimientos`
--
ALTER TABLE `movimientos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `articulo_id` (`articulo_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `articulos`
--
ALTER TABLE `articulos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `movimientos`
--
ALTER TABLE `movimientos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `movimientos`
--
ALTER TABLE `movimientos`
  ADD CONSTRAINT `movimientos_ibfk_1` FOREIGN KEY (`articulo_id`) REFERENCES `articulos` (`id`),
  ADD CONSTRAINT `movimientos_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
