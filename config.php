<?php
// config.php

define('DB_HOST', 'localhost:3306');
define('DB_NAME', 'inventario_gabinete');
define('DB_USER', 'root');
define('DB_PASS', 'bt3411bt');

// Solo inicia sesión si no está ya iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
