<?php
require_once '../vendor/autoload.php';
require_once '../database.php';
require_once '../header.php';

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

// Obtengo categorías
$categorias = $pdo->query("SELECT * FROM categorias")->fetchAll();

// Filtrado
$filtro_categoria = $_GET['categoria_id'] ?? '';
$sql = "SELECT a.*, c.nombre AS categoria_nombre 
        FROM articulos a 
        LEFT JOIN categorias c ON a.categoria_id = c.id";
if ($filtro_categoria !== '') {
    $sql .= " WHERE a.categoria_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$filtro_categoria]);
} else {
    $stmt = $pdo->query($sql);
}
$articulos = $stmt->fetchAll();
?>

<h2>Lista de Artículos</h2>
<a href="nuevo.php">➕ Nuevo artículo</a>

<form method="get">
    <label>Filtrar por categoría:
        <select name="categoria_id" onchange="this.form.submit()">
            <option value="">-- Todas --</option>
            <?php foreach ($categorias as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= $filtro_categoria == $cat['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['nombre']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>
</form>

<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Categoría</th>
        <th>Imagen</th>
        <th>Ubicación</th>
        <th>Stock</th>
        <th>QR</th>
        <th>Acciones</th>
        

    </tr>
    <?php foreach ($articulos as $art): ?>
        <?php
            // Construir URL del QR (aunque ver.php no exista, se puede cambiar por mostrar.php si lo creás eso dice la IA)
            $url = 'http://localhost/control_inventario/articulos/ver.php?id=' . $art['id'];

            // Crear código QR
            $qrCode = new QrCode($url);// En versión 6.0.8 este constructor es válido creo.
            $writer = new PngWriter();
            $result = $writer->write($qrCode);
            $qrImage = $result->getDataUri();
        ?>
        <tr>
            <td><?= $art['id'] ?></td>
            <td><?= htmlspecialchars($art['nombre']) ?></td>
            <td><?= htmlspecialchars($art['categoria_nombre']) ?></td>
            <td>
                <?php if (!empty($art['imagen'])): ?>
                    <img src="../img/articulos/<?= htmlspecialchars($art['imagen']) ?>" width="50" height="50" alt="Imagen">
                <?php else: ?>
                    Sin imagen
                <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($art['ubicacion'] ?? 'No asignada') ?></td>
            <td><?= htmlspecialchars($art['stock']) ?></td> 
            <td><img src="<?= $qrImage ?>" width="60" height="60" alt="QR"></td>
            <td><a href="editar.php?id=<?= $art['id'] ?>">✏️ Editar</a></td>
        </tr>
    <?php endforeach; ?>
</table>

<?php include '../footer.php'; ?>
