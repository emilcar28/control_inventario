<?php
require_once '../vendor/autoload.php';
require_once '../database.php';
require_once '../header.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "<p>ID no válido.</p>";
    include '../footer.php';
    exit;
}

$stmt = $pdo->prepare("SELECT a.*, c.nombre AS categoria_nombre FROM articulos a LEFT JOIN categorias c ON a.categoria_id = c.id WHERE a.id = ?");
$stmt->execute([$id]);
$articulo = $stmt->fetch();

if (!$articulo) {
    echo "<p>Artículo no encontrado.</p>";
    include '../footer.php';
    exit;
}
?>

<h2>Detalles del Artículo</h2>
<p><strong>ID:</strong> <?= $articulo['id'] ?></p>
<p><strong>Nombre:</strong> <?= htmlspecialchars($articulo['nombre']) ?></p>
<p><strong>Categoría:</strong> <?= htmlspecialchars($articulo['categoria_nombre']) ?></p>
<p><strong>Ubicación:</strong> <?= htmlspecialchars($articulo['ubicacion'] ?? 'No asignada') ?></p>
<?php if (!empty($articulo['imagen'])): ?>
    <img src="../img/articulos/<?= htmlspecialchars($articulo['imagen']) ?>" width="150">
<?php endif; ?>

<?php include '../footer.php'; ?>
