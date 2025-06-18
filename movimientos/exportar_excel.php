<?php
require_once '../vendor/autoload.php';
require_once '../database.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Desactivar todo tipo de salida
ob_end_clean();
ob_start();

// Obtener filtros
$tipo = $_GET['tipo'] ?? '';
$buscar_articulo = $_GET['buscar_articulo'] ?? '';

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

$sql .= " ORDER BY m.fecha DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$movimientos = $stmt->fetchAll();

// Crear Excel
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->fromArray(['ID', 'Artículo', 'Cantidad', 'Tipo', 'Receptor', 'Destino', 'Fecha', 'Usuario'], null, 'A1');

$row = 2;
foreach ($movimientos as $mov) {
    $sheet->setCellValue("A{$row}", $mov['id']);
    $sheet->setCellValue("B{$row}", $mov['articulo_nombre']);
    $sheet->setCellValue("C{$row}", $mov['cantidad']);
    $sheet->setCellValue("D{$row}", ucfirst($mov['tipo']));
    $sheet->setCellValue("E{$row}", $mov['receptor']);
    $sheet->setCellValue("F{$row}", $mov['destino']);
    $sheet->setCellValue("G{$row}", $mov['fecha']);
    $sheet->setCellValue("H{$row}", $mov['usuario_nombre']);
    $row++;
}

// Configurar cabeceras correctamente
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="movimientos_' . date("Ymd_His") . '.xlsx"');
header('Cache-Control: max-age=0');

// Guardar en salida
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;

