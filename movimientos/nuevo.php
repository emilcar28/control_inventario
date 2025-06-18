<?php
session_start();
require_once '../vendor/autoload.php';
require_once '../database.php';

use Dompdf\Dompdf;

if ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'usuario') {
    die("No tienes permiso para registrar movimientos.");
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $articulo_id = $_POST['articulo_id'];
    $cantidad = (int)$_POST['cantidad'];
    $tipo = $_POST['tipo'];
    $receptor = trim($_POST['receptor']);
    $destino = trim($_POST['destino']);
    $usuario_id = $_SESSION['user_id'];

    // Verificar campos
    if ($cantidad > 0 && $articulo_id !== '' && $tipo !== '') {
        // Stock actual
        $stmtStock = $pdo->prepare("SELECT nombre, stock FROM articulos WHERE id = ?");
        $stmtStock->execute([$articulo_id]);
        $articulo = $stmtStock->fetch();

        if (!$articulo) {
            $error = "Artículo no encontrado.";
        } elseif (in_array($tipo, ['salida', 'baja', 'en préstamo', 'traslado', 'en mantenimiento']) && $cantidad > $articulo['stock']) {
            $error = "No hay suficiente stock disponible.";
        } else {
            // Registrar movimiento
            $stmt = $pdo->prepare("INSERT INTO movimientos (articulo_id, cantidad, tipo, usuario_id, fecha, receptor, destino) 
                                   VALUES (?, ?, ?, ?, NOW(), ?, ?)");
            if ($stmt->execute([$articulo_id, $cantidad, $tipo, $usuario_id, $receptor, $destino])) {
                // Actualizar stock
                if (in_array($tipo, ['entrada', 'alta', 'devolución', 'reparado'])) {
                    $pdo->prepare("UPDATE articulos SET cantidad = cantidad + ?, stock = stock + ? WHERE id = ?")
                        ->execute([$cantidad, $cantidad, $articulo_id]);
                } else {
                    $pdo->prepare("UPDATE articulos SET cantidad = cantidad - ?, stock = stock - ? WHERE id = ?")
                        ->execute([$cantidad, $cantidad, $articulo_id]);
                }

                // Generar PDF
                $fecha_actual = date("d/m/Y H:i");
                $stmtUser = $pdo->prepare("SELECT usuario FROM usuarios WHERE id = ?");
                $stmtUser->execute([$usuario_id]);
                $usuario_nombre = $stmtUser->fetchColumn();

                $html = "
                <h2>Comprobante de Movimiento</h2>
                <p><strong>Fecha y Hora:</strong> $fecha_actual</p>
                <p><strong>Artículo:</strong> " . htmlspecialchars($articulo['nombre']) . "</p>
                <p><strong>Cantidad:</strong> $cantidad</p>
                <p><strong>Tipo de Movimiento:</strong> " . ucfirst($tipo) . "</p>
                <p><strong>Receptor:</strong> " . htmlspecialchars($receptor) . "</p>
                <p><strong>Destino:</strong> " . htmlspecialchars($destino) . "</p>
                <p><strong>Registrado por:</strong> " . htmlspecialchars($usuario_nombre) . "</p>
                <p style='margin-top: 50px;'>Firma del Solicitante: ____________________________</p>
                 <p style='margin-top: 50px;'>Firma del Personal Autorizado: ____________________________</p>
                ";

                $dompdf = new Dompdf();
                $dompdf->loadHtml($html);
                $dompdf->setPaper('A4', 'portrait');
                $dompdf->render();

                // Descargar el PDF
                header("Content-Type: application/pdf");
                header("Content-Disposition: attachment; filename=movimiento_" . date("Ymd_His") . ".pdf");
                echo $dompdf->output();
                exit;
            } else {
                $error = "Error al registrar el movimiento.";
            }
        }
    } else {
        $error = "Todos los campos son obligatorios y la cantidad debe ser mayor que 0.";
    }
}

// Mostrar formulario
$stmt = $pdo->query("SELECT * FROM articulos");
$articulos = $stmt->fetchAll();
?>

<?php include '../header.php'; ?>

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
