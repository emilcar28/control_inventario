<?php
require_once '../vendor/autoload.php';
require_once '../database.php';
require_once '../header.php';

if ($_SESSION['rol'] !== 'admin') {
    echo "<p>No tienes permiso para crear categorías.</p>";
    include '../footer.php';
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    if ($nombre !== '') {
        $stmt = $pdo->prepare("INSERT INTO categorias (nombre) VALUES (?)");
        if ($stmt->execute([$nombre])) {
            header("Location: lista.php");
            exit;
        } else {
            $error = "Error al guardar la categoría.";
        }
    } else {
        $error = "El nombre no puede estar vacío.";
    }
}
?>

<h2>Nueva Categoría</h2>
<form method="post">
    <label>Nombre: <input type="text" name="nombre" required></label><br>
    <button type="submit">Guardar</button>
</form>
<p style="color:red;"><?= $error ?></p>

<?php include '../footer.php'; ?>
