<?php
require_once '../vendor/autoload.php';
require_once '../database.php';
require_once '../header.php';

if ($_SESSION['rol'] !== 'admin') {
    echo "<p>No tienes permiso para acceder a esta sección.</p>";
    include '../footer.php';
    exit;
}

$stmt = $pdo->query("SELECT * FROM usuarios");
$usuarios = $stmt->fetchAll();
?>

<h2>Lista de Usuarios</h2>
<a href="nuevo.php">➕ Nuevo usuario</a>
<table border="1">
    <tr>
        <th>ID</th>
        <th>Usuario</th>
        <th>Rol</th>
        <th>Acciones</th>
    </tr>
    <?php foreach ($usuarios as $u): ?>
        <tr>
            <td><?= $u['id'] ?></td>
            <td><?= htmlspecialchars($u['usuario']) ?></td>
            <td><?= $u['rol'] ?></td>
            <td>
                <a href="editar.php?id=<?= $u['id'] ?>">✏️ Editar</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<?php include '../footer.php'; ?>
