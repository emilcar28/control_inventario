<?php
require_once '../database.php';
require_once '../header.php';

$stmt = $pdo->query("SELECT * FROM categorias ORDER BY nombre ASC");
$categorias = $stmt->fetchAll();
?>

<h2>Lista de Categorías</h2>
<a href="nueva.php">➕ Nueva categoría</a>
<table border="1">
    <tr>
        <th>ID</th>
        <th>Nombre</th>
    </tr>
    <?php foreach ($categorias as $cat): ?>
        <tr>
            <td><?= $cat['id'] ?></td>
            <td><?= htmlspecialchars($cat['nombre']) ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<?php include '../footer.php'; ?>
