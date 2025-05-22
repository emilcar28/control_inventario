<?php
require_once '../vendor/autoload.php';
require_once '../database.php';
require_once '../header.php';

if ($_SESSION['rol'] !== 'admin') {
    echo "<p>No tienes permiso para editar artículos.</p>";
    include '../footer.php';
    exit;
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM articulos WHERE id = ?");
$stmt->execute([$id]);
$articulo = $stmt->fetch();

if (!$articulo) {
    echo "<p>Artículo no encontrado.</p>";
    include '../footer.php';
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $categoria_id = $_POST['categoria_id'];
    $ubicacion = trim($_POST['ubicacion']);
    $stock = intval($_POST['stock']);
    $imagen = $articulo['imagen']; // Mantener la imagen original

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $imagen = $_FILES['imagen']['name'];
        move_uploaded_file($_FILES['imagen']['tmp_name'], "../img/articulos/" . $imagen);
    }

    if ($nombre !== '' && $categoria_id !== '') {
        $stmt = $pdo->prepare("UPDATE articulos SET nombre=?, categoria_id=?, ubicacion=?, imagen=?, stock=? WHERE id=?");
        if ($stmt->execute([$nombre, $categoria_id, $ubicacion, $imagen, $stock, $id])) {
            header("Location: lista.php");
            exit;
        } else {
            $error = "Error al actualizar el artículo.";
        }
    } else {
        $error = "Todos los campos son obligatorios.";
    }
}

// Obtener categorías
$stmt = $pdo->query("SELECT * FROM categorias");
$categorias = $stmt->fetchAll();
?>

<h2>Editar Artículo</h2>
<form method="post" enctype="multipart/form-data">
    <label>Nombre: <input type="text" name="nombre" value="<?= htmlspecialchars($articulo['nombre']) ?>" required></label><br>
    
    <label>Categoría:
        <select name="categoria_id" required>
            <?php foreach ($categorias as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $articulo['categoria_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['nombre']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label><br>

    <label>Ubicación: <input type="text" name="ubicacion" value="<?= htmlspecialchars($articulo['ubicacion']) ?>"></label><br>

    <label>Imagen: <input type="file" name="imagen"></label><br>

    <label>Cantidad (stock): <input type="number" name="stock" value="<?= htmlspecialchars($articulo['stock']) ?>" required min="0"></label><br>

    <button type="submit">Actualizar</button>
</form>

<p style="color:red;"><?= $error ?></p>

<?php include '../footer.php'; ?>


