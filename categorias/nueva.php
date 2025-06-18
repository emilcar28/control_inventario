<?php
require_once '../vendor/autoload.php';
require_once '../database.php';
require_once '../header.php';

if ($_SESSION['rol'] !== 'admin') {
    echo "<p>No tienes permiso para gestionar categorías.</p>";
    include '../footer.php';
    exit;
}

$error = '';
$modo = $_GET['modo'] ?? '';
$id_editar = $_GET['id'] ?? null;

// AGREGAR o ACTUALIZAR
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $id = $_POST['id'] ?? null;

    if ($nombre !== '') {
        if ($id) {
            // actualizar
            $stmt = $pdo->prepare("UPDATE categorias SET nombre=? WHERE id=?");
            $success = $stmt->execute([$nombre, $id]);
        } else {
            // nuevo
            $stmt = $pdo->prepare("INSERT INTO categorias (nombre) VALUES (?)");
            $success = $stmt->execute([$nombre]);
        }

        if ($success) {
            header("Location: nueva.php");
            exit;
        } else {
            $error = "Error al guardar la categoría.";
        }
    } else {
        $error = "El nombre no puede estar vacío.";
    }
}

// BORRAR
if ($modo === 'eliminar' && $id_editar) {
    $stmt = $pdo->prepare("DELETE FROM categorias WHERE id = ?");
    $stmt->execute([$id_editar]);
    header("Location: nueva.php");
    exit;
}

// Cargar categoría a editar
$categoria_a_editar = null;
if ($modo === 'editar' && $id_editar) {
    $stmt = $pdo->prepare("SELECT * FROM categorias WHERE id = ?");
    $stmt->execute([$id_editar]);
    $categoria_a_editar = $stmt->fetch();
}

// Obtener todas las categorías
$stmt = $pdo->query("SELECT * FROM categorias ORDER BY id ASC");
$categorias = $stmt->fetchAll();
?>

<h2><?= $categoria_a_editar ? 'Editar' : 'Nueva' ?> Categoría</h2>

<form method="post">
    <input type="hidden" name="id" value="<?= $categoria_a_editar['id'] ?? '' ?>">
    <label>Nombre:
        <input type="text" name="nombre" value="<?= htmlspecialchars($categoria_a_editar['nombre'] ?? '') ?>" required>
    </label><br>
    <button type="submit"><?= $categoria_a_editar ? 'Actualizar' : 'Guardar' ?></button>
    <?php if ($categoria_a_editar): ?>
        <a href="nueva.php">Cancelar</a>
    <?php endif; ?>
</form>

<p style="color:red;"><?= $error ?></p>

<hr>
<h3>Categorías existentes</h3>
<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Acciones</th>
    </tr>
    <?php foreach ($categorias as $cat): ?>
        <tr>
            <td><?= $cat['id'] ?></td>
            <td><?= htmlspecialchars($cat['nombre']) ?></td>
            <td>
                <a href="?modo=editar&id=<?= $cat['id'] ?>">✏️ Editar</a>
                |
                <a href="?modo=eliminar&id=<?= $cat['id'] ?>" onclick="return confirm('¿Seguro que deseas eliminar esta categoría?');">🗑️ Eliminar</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<?php include '../footer.php'; ?>
