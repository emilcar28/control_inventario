<?php
require_once '../database.php';
require_once '../header.php';
require_once '../vendor/autoload.php';

// Filtros
$tipo = $_GET['tipo'] ?? '';
$buscar_articulo = $_GET['buscar_articulo'] ?? '';
$articulo_id = $_GET['articulo_id'] ?? '';

// Obtener lista de artículos para filtro 
$articulos = $pdo->query("SELECT id, nombre FROM articulos ORDER BY nombre")->fetchAll();

$sql = "SELECT m.*, a.nombre AS articulo_nombre, u.usuario AS usuario_nombre
        FROM movimientos m
        JOIN articulos a ON m.articulo_id = a.id
        JOIN usuarios u ON m.usuario_id = u.id
        WHERE 1=1";

$params = [];

if ($tipo !== '') {
    $sql .= " AND m.tipo = ?";
    $params[] = $tipo;
}

if ($buscar_articulo !== '') {
    $sql .= " AND a.nombre LIKE ?";
    $params[] = '%' . $buscar_articulo . '%';
}

if ($articulo_id !== '') {
    $sql .= " AND a.id = ?";
    $params[] = $articulo_id;
}

$sql .= " ORDER BY m.fecha DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$movimientos = $stmt->fetchAll();
?>

<h2>Lista de Movimientos</h2>

<a href="nuevo.php" style="display:inline-block; margin-bottom: 1em; font-weight:bold;">➕ Registrar Nuevo Movimiento</a>
<a href="exportar_excel.php?tipo=<?= urlencode($tipo) ?>&buscar_articulo=<?= urlencode($buscar_articulo) ?>" style="margin-left: 1em; font-weight:bold;">📁 Exportar a Excel</a>


<!-- Filtros --> 
<form method="get" style="margin-bottom: 1em;">
    <label>Filtrar por tipo:
        <select name="tipo">
            <option value="">-- Todos --</option>
            <?php
            $tipos = ['entrada', 'salida', 'alta', 'baja', 'en préstamo', 'devolución', 'en mantenimiento', 'traslado', 'reparado'];
            foreach ($tipos as $t) {
                echo "<option value=\"$t\"" . ($tipo === $t ? ' selected' : '') . ">$t</option>";
            }
            ?>
        </select>
    </label>

    <label>Buscar artículo (nombre):
        <input type="text" name="buscar_articulo" value="<?= htmlspecialchars($buscar_articulo) ?>" placeholder="Nombre parcial">
    </label>

    <label>Artículo exacto:
        <select name="articulo_id">
            <option value="">-- Todos --</option>
            <?php foreach ($articulos as $art): ?>
                <option value="<?= $art['id'] ?>" <?= ($articulo_id == $art['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($art['nombre']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>

    <button type="submit">🔍 Filtrar</button>
    <a href="lista.php">🔄 Limpiar</a>
</form>

<table border="1" cellpadding="5">
    <tr>
        <th>ID</th>
        <th>Artículo</th>
        <th>Cantidad</th>
        <th>Tipo</th>
        <th>Receptor</th>
        <th>Destino</th>
        <th>Fecha</th>
        <th>Usuario</th>
    </tr>
    <?php foreach ($movimientos as $mov): ?>
        <tr>
            <td><?= $mov['id'] ?></td>
            <td><?= htmlspecialchars($mov['articulo_nombre']) ?></td>
            <td><?= $mov['cantidad'] ?></td>
            <td><?= ucfirst($mov['tipo']) ?></td>
            <td><?= htmlspecialchars($mov['receptor']) ?></td>
            <td><?= htmlspecialchars($mov['destino']) ?></td>
            <td><?= $mov['fecha'] ?></td>
            <td><?= htmlspecialchars($mov['usuario_nombre']) ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<?php include '../footer.php'; ?>

