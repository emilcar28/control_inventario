<?php
require_once '../database.php';
require_once '../header.php';

if ($_SESSION['rol'] !== 'admin') {
    echo "<p>No tienes permiso para acceder a esta sección.</p>";
    include '../footer.php';
    exit;
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$id]);
$usuario = $stmt->fetch();

if (!$usuario) {
    echo "<p>Usuario no encontrado.</p>";
    include '../footer.php';
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevo_usuario = $_POST['usuario'];
    $rol = $_POST['rol'];

    if (!empty($_POST['clave'])) {
        $clave = password_hash($_POST['clave'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE usuarios SET usuario=?, clave=?, rol=? WHERE id=?");
        $ok = $stmt->execute([$nuevo_usuario, $clave, $rol, $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE usuarios SET usuario=?, rol=? WHERE id=?");
        $ok = $stmt->execute([$nuevo_usuario, $rol, $id]);
    }

    if ($ok) {
        header("Location: lista.php");
        exit;
    } else {
        $error = "Error al actualizar el usuario.";
    }
}
?>

<h2>Editar Usuario</h2>
<form method="post">
    <label>Usuario: <input type="text" name="usuario" value="<?= htmlspecialchars($usuario['usuario']) ?>" required></label><br>
    <label>Contraseña (dejar en blanco si no se cambia): <input type="password" name="clave"></label><br>
    <label>Rol:
        <select name="rol">
            <option value="admin" <?= $usuario['rol'] === 'admin' ? 'selected' : '' ?>>Administrador</option>
            <option value="usuario" <?= $usuario['rol'] === 'usuario' ? 'selected' : '' ?>>Usuario</option>
        </select>
    </label><br>
    <button type="submit">Actualizar</button>
</form>
<p style="color:red;"><?php echo $error; ?></p>

<?php include '../footer.php'; ?>
