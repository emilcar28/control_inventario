<?php 
require_once '../vendor/autoload.php';
require_once '../database.php';
require_once '../header.php';

if ($_SESSION['rol'] !== 'admin') {
    echo "<p>No tienes permiso para crear artículos.</p>";
    include '../footer.php';
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $categoria_id = $_POST['categoria_id'];
    $ubicacion = trim($_POST['ubicacion']);
    $stock = isset($_POST['stock']) ? (int)$_POST['stock'] : 0;
    $imagen = null;

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $imagen = $_FILES['imagen']['name'];

        // Verificar y crear carpeta si no existe
        $dir = "../img/articulos/";
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        move_uploaded_file($_FILES['imagen']['tmp_name'], $dir . $imagen);
    }

    if ($nombre !== '' && $categoria_id !== '') {
        $stmt = $pdo->prepare("INSERT INTO articulos (nombre, categoria_id, imagen, ubicacion, stock) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$nombre, $categoria_id, $imagen, $ubicacion, $stock])) {
            header("Location: lista.php");
            exit;
        } else {
            $error = "Error al guardar el artículo.";
        }
    } else {
        $error = "Todos los campos obligatorios.";
    }
}

// Obtengo las categorías para el formulario
$stmt = $pdo->query("SELECT * FROM categorias");
$categorias = $stmt->fetchAll();
?>

<h2>Nuevo Artículo</h2>
<form method="post" enctype="multipart/form-data">
    <label>Nombre: <input type="text" name="nombre" required></label><br>
    <label>Categoría:
        <select name="categoria_id" required>
            <?php foreach ($categorias as $cat): ?>
                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
            <?php endforeach; ?>
        </select>
    </label><br>
    <label>Imagen: <input type="file" name="imagen"></label><br>
    <label>Ubicación: <input type="text" name="ubicacion" required></label><br>
    <label>Cantidad en stock: <input type="number" name="stock" required min="0"></label><br>
    <button type="submit">Guardar</button>
</form>
<p style="color:red;"><?= $error ?></p>

<?php include '../footer.php'; ?>

