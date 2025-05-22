<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario'])) {
    header("Location: /control_inventario/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inventario Gabinete</title>
    <link rel="stylesheet" href="/control_inventario/css/styles.css"> <!-- Ruta absoluta -->
</head>
<body>
<header>
    <h1>Control de Inventario - Gabinete de Simulación</h1>
    <nav>
        <a href="/control_inventario/index.php">Inicio</a> |
        <a href="/control_inventario/usuarios/lista.php">Usuarios</a> |
        <a href="/control_inventario/categorias/lista.php">Categorías</a> |
        <a href="/control_inventario/articulos/lista.php">Artículos</a> |
        <a href="/control_inventario/movimientos/lista.php">Movimientos</a> |
        <a href="/control_inventario/logout.php">Cerrar sesión</a>
    </nav>
</header>
<main>
