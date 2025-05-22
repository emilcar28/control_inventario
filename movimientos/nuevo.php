<?php
require_once '../vendor/autoload.php';
require_once '../database.php';
require_once '../header.php';

if ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'usuario') {
    echo "<p>No tienes permiso para registrar movimientos.</p>";
    include '../footer.php';
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $articulo_id = $_POST['articulo_id'];
    $cantidad = (int)$_POST['cantidad'];
    $tipo = $_POST['tipo'];
    $receptor = trim($_POST['receptor']);
    $destino = trim($_POST['destino']);
    $usuario_id = $_SESSION['user_id'];

    if ($cantidad > 0 && $articulo_id !== '' && $tipo !== '') {
        // Obtener stock actual
        $stmtStock = $pdo->prepare("SELECT stock FROM articulos WHERE id = ?");
        $stmtStock->execute([$articulo_id]);
        $articulo = $stmtStock->fetch();

        if (!$articulo) {
            $error = "Artículo no encontrado.";
        } elseif (in_array($tipo, ['salida', 'baja', 'en préstamo', 'traslado', 'en mantenimiento']) && $cantidad > $articulo['stock']) {
            $error = "No hay suficiente stock disponible.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO movimientos (articulo_id, cantidad, tipo, usuario_id, fecha, receptor, destino) 
                                   VALUES (?, ?, ?, ?, NOW(), ?, ?)");
            if ($stmt->execute([$articulo_id, $cantidad, $tipo, $usuario_id, $receptor, $destino])) {
                if (in_array($tipo, ['entrada', 'alta', 'devolución', 'reparado'])) {
                    $stmt2 = $pdo->prepare("UPDATE articulos SET cantidad = cantidad + ?, stock = stock + ? WHERE id = ?");
                    $stmt2->execute([$cantidad, $cantidad, $articulo_id]);
                } elseif (in_array($tipo, ['salida', 'baja', 'en préstamo', 'traslado', 'en mantenimiento'])) {
                    $stmt2 = $pdo->prepare("UPDATE articulos SET cantidad = cantidad - ?, stock = stock - ? WHERE id = ?");
                    $stmt2->execute([$cantidad, $cantidad, $articulo_id]);
                }
                header("Location: lista.php");
                exit;
            } else {
                $error = "Error al registrar el movimiento.";
            }
        }
    } else {
        $error = "Todos los campos son obligatorios y la cantidad debe ser mayor que 0.";
    }
}

$stmt = $pdo->query("SELECT * FROM articulos");
$articulos = $stmt->fetchAll();
?>

<h2>Nuevo Movimiento</h2>
<a href="lista.php">🔙 Volver a la lista de movimientos</a>
<form method="post">
    <label>Artículo:
        <select name="articulo_id" required>
            <?php foreach ($articulos as $art): ?>
                <option value="<?= $art['id'] ?>"><?= htmlspecialchars($art['nombre']) ?></option>
            <?php endforeach; ?>
        </select>
    </label><br>

    <label>Cantidad: <input type="number" name="cantidad" min="1" required></label><br>

    <label>Tipo de Movimiento:
        <select name="tipo" required>
            <option value="entrada">Entrada</option>
            <option value="salida">Salida</option>
            <option value="alta">Alta</option>
            <option value="baja">Baja</option>
            <option value="en préstamo">En préstamo</option>
            <option value="devolución">Devolución</option>
            <option value="en mantenimiento">En mantenimiento</option>
            <option value="traslado">Traslado</option>
            <option value="reparado">Reparado</option>
        </select>
    </label><br>

    <label>¿A quién se entrega o destina? (Receptor):
        <input type="text" name="receptor" required>
    </label><br>

    <label>¿A dónde se destina? (Destino):
        <input type="text" name="destino" required>
    </label><br>

    <button type="submit">Registrar Movimiento</button>
</form>
<p style="color:red;"><?= $error ?></p>

<?php include '../footer.php'; ?>

