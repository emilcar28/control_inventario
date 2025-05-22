<?php
require_once '../database.php';
require_once '../header.php';

// Solo admin o roles autorizados......por ahora queda asi luego verificar
if (!in_array($_SESSION['rol'], ['admin', 'usuario'])) {
    echo "<p>No tienes permisos para registrar movimientos.</p>";
    include '../footer.php';
    exit;
}

$error = '';
$success = '';

// Obtener artículos para el selector
$stmt = $pdo->query("SELECT id, nombre FROM articulos ORDER BY nombre");
$articulos = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $articulo_id = $_POST['articulo_id'] ?? '';
    $tipo = $_POST['tipo'] ?? '';
    $cantidad = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : 0;
    $fecha = date('Y-m-d H:i:s');

    if ($articulo_id && $tipo && $cantidad > 0) {
        // Insertar movimiento
        $stmt = $pdo->prepare("INSERT INTO movimientos (articulo_id, tipo, cantidad, fecha) VALUES (?, ?, ?, ?)");
        $stmt->execute([$articulo_id, $tipo, $cantidad, $fecha]);

        // Obtener stock actual
        $stmt = $pdo->prepare("SELECT stock FROM articulos WHERE id = ?");
        $stmt->execute([$articulo_id]);
        $articulo = $stmt->fetch();

        if ($articulo) {
            $stock_actual = (int)$articulo['stock'];

            // Lógica de stock según tipo de movimiento
            switch ($tipo) {
                case 'entrada':
                case 'alta':
                case 'devolución':
                case 'reparado':
                    $nuevo_stock = $stock_actual + $cantidad;
                    break;

                case 'salida':
                case 'baja':
                case 'en préstamo':
                    $nuevo_stock = max(0, $stock_actual - $cantidad); // evitar negativos
                    break;

                case 'traslado':
                case 'en mantenimiento':
                default:
                    $nuevo_stock = $stock_actual; // no cambia el stock
            }

            // Actualizar el stock
            $stmt = $pdo->prepare("UPDATE articulos SET stock = ? WHERE id = ?");
            $stmt->execute([$nuevo_stock, $articulo_id]);

            $success = "Movimiento registrado correctamente. Stock actualizado.";
        } else {
            $error = "Artículo no encontrado.";
        }
    } else {
        $error = "Todos los campos son obligatorios y la cantidad debe ser mayor a 0.";
    }
}
?>

<h2>Registrar Movimiento</h2>

<?php if ($error): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<?php if ($success): ?>
    <p style="color:green;"><?= htmlspecialchars($success) ?></p>
<?php endif; ?>

<form method="post">
    <label>Artículo:
        <select name="articulo_id" required>
            <option value="">Seleccione</option>
            <?php foreach ($articulos as $art): ?>
                <option value="<?= $art['id'] ?>"><?= htmlspecialchars($art['nombre']) ?></option>
            <?php endforeach; ?>
        </select>
    </label><br><br>

    <label>Tipo de movimiento:
        <select name="tipo" required>
            <option value="">Seleccione</option>
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
    </label><br><br>

    <label>Cantidad:
        <input type="number" name="cantidad" min="1" required>
    </label><br><br>

    <button type="submit">Registrar Movimiento</button>
</form>

<?php include '../footer.php'; ?>
