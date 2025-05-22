<?php
require_once '../database.php';
require_once '../header.php';

if ($_SESSION['rol'] !== 'admin') {
    echo "<p>No tienes permiso para acceder a esta sección.</p>";
    include '../footer.php';
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'];
    $clave = password_hash($_POST['clave'], PASSWORD_DEFAULT);
    $rol = $_POST['rol'];

    $stmt = $pdo->prepare("INSERT INTO usuarios (usuario, password, rol) VALUES (?, ?, ?)");
    if ($stmt->execute([$usuario, $clave, $rol])) {
        header("Location: lista.php");
        exit;
    } else {
        $error = "Error al crear el usuario.";
    }
}
?>

<h2>Nuevo Usuario</h2>
<form method="post">
    <label>Usuario: <input type="text" name="usuario" required></label><br>
    <label>Contraseña: <input type="password" name="clave" required></label><br>
    <label>Rol:
        <select name="rol">
            <option value="admin">Administrador</option>
            <option value="usuario">Usuario</option>
        </select>
    </label><br>
    <button type="submit">Guardar</button>
</form>
<p style="color:red;"><?php echo $error; ?></p>

<?php include '../footer.php'; ?>
