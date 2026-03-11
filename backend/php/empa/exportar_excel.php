<?php
/**
 * Exportar Excel — SEM
 * Ruta: backend/php/empa/exportar_excel.php
 */

ini_set('display_errors', 0);
error_reporting(0);
ob_start();

require_once '../../db/conexion.php';
require_once '../../../vendor/autoload.php';

date_default_timezone_set('America/Lima');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

function limpiar($v) { return htmlspecialchars(strip_tags(trim($v ?? ''))); }

/* ── Filtros ── */
$p = []; $w = [];
$fi = limpiar($_GET['fecha_inicio'] ?? '');
$ff = limpiar($_GET['fecha_fin']    ?? '');
$em = limpiar($_GET['empadronador'] ?? '');
$tp = limpiar($_GET['tipo_cse']     ?? '');

if ($fi) { $w[] = 'DATE(e.Fecha) >= :fi';    $p[':fi'] = $fi; }
if ($ff) { $w[] = 'DATE(e.Fecha) <= :ff';    $p[':ff'] = $ff; }
if ($em) { $w[] = 'e.Empadronador LIKE :em'; $p[':em'] = "%$em%"; }
if ($tp) { $w[] = 'e.TipoCSE = :tp';         $p[':tp'] = $tp; }
$where = $w ? 'WHERE ' . implode(' AND ', $w) : '';

/* ── Consulta — mismos campos que el PDF ── */
try {
    $st = $pdo->prepare("
        SELECT
            e.IdEmpa                                    AS IdEmpa,
            e.DNI_Soli                                  AS DNI,
            e.Solicitante                               AS Solicitante,
            e.TipoCSE                                   AS TipoCSE,
            ts.Descripcion                              AS TipoSolicitud,
            e.Integrantes                               AS Integrantes,
            e.Archivador                                AS Archivador,
            e.Empadronador                              AS Empadronador,
            DATE_FORMAT(e.Fecha, '%d/%m/%Y')            AS Fecha,
            e.Observaciones                             AS Observaciones
        FROM empadronamiento e
        LEFT JOIN tipo_solicitud ts ON e.IdTipoSoli = ts.IdTipoSoli
        $where
        ORDER BY e.Fecha DESC, e.IdEmpa DESC
    ");
    $st->execute($p);
    $filas = $st->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    ob_end_clean();
    die('Error BD: ' . $e->getMessage());
}

$total   = count($filas);
$nopobre = count(array_filter($filas, fn($r) => $r['TipoCSE'] === 'NO POBRE'));
$pobre   = count(array_filter($filas, fn($r) => $r['TipoCSE'] === 'POBRE'));
$extremo = count(array_filter($filas, fn($r) => $r['TipoCSE'] === 'POBRE EXTREMO'));

$periodo = match(true) {
    (bool)$fi && (bool)$ff => "Del $fi al $ff",
    (bool)$fi              => "Desde $fi",
    (bool)$ff              => "Hasta $ff",
    default                => 'Todos los registros',
};

/* ── Colores ── */
$C_HDR_BG = '1A2332';
$C_HDR_FG = 'FFFFFF';
$C_META   = 'EEF2F9';
$C_ALT    = 'F5F8FD';
$C_NP_BG  = 'F0FDF4'; $C_NP_FG = '15803D';
$C_P_BG   = 'FFFBEB'; $C_P_FG  = '92400E';
$C_PE_BG  = 'FEF2F2'; $C_PE_FG = '991B1B';
$C_BRD    = 'DDE5F0';

/* ── Cabeceras igual que PDF ── */
$cabeceras = ['#', 'ID', 'DNI', 'Solicitante', 'Tipo CSE', 'Tipo Solicitud',
              'Integrantes', 'Archivador', 'Empadronador', 'Fecha', 'Observaciones'];
$numCols = count($cabeceras);
$lastCol = Coordinate::stringFromColumnIndex($numCols);

/* ══════════ SPREADSHEET ══════════ */
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Empadronamientos');

/* ── Fila 1: Título ── */
$sheet->mergeCells("A1:{$lastCol}1");
$sheet->setCellValue('A1', 'SISTEMA DE EMPADRONAMIENTO MUNICIPAL — REPORTE');
$sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
    'font'      => ['bold'=>true,'size'=>13,'color'=>['rgb'=>$C_HDR_FG],'name'=>'Arial'],
    'fill'      => ['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>$C_HDR_BG]],
    'alignment' => ['horizontal'=>Alignment::HORIZONTAL_CENTER,'vertical'=>Alignment::VERTICAL_CENTER],
]);
$sheet->getRowDimension(1)->setRowHeight(26);

/* ── Filas metadata ── */
$metaRows = [['Periodo', $periodo], ['Generado', date('d/m/Y H:i:s')]];
if ($em) $metaRows[] = ['Empadronador', $em];
if ($tp) $metaRows[] = ['Tipo CSE', $tp];

foreach ($metaRows as $i => $item) {
    $r = $i + 2;
    $sheet->mergeCells("A{$r}:C{$r}");
    $sheet->mergeCells("D{$r}:{$lastCol}{$r}");
    $sheet->setCellValue("A{$r}", $item[0]);
    $sheet->setCellValue("D{$r}", $item[1]);
    $sheet->getStyle("A{$r}:{$lastCol}{$r}")->applyFromArray([
        'fill'      => ['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>$C_META]],
        'font'      => ['name'=>'Arial','size'=>9],
        'alignment' => ['vertical'=>Alignment::VERTICAL_CENTER],
    ]);
    $sheet->getStyle("A{$r}")->getFont()->setBold(true)
        ->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF'.$C_HDR_BG));
    $sheet->getRowDimension($r)->setRowHeight(15);
}

/* ── Fila stats ── */
$statsRow = count($metaRows) + 3;
$statRanges = [['A','C'],['D','F'],['G','H'],['I',$lastCol]];
$statsData  = [
    ['Total',        $total,   'EEF2F9', $C_HDR_BG],
    ['No Pobre',     $nopobre, $C_NP_BG, $C_NP_FG],
    ['Pobre',        $pobre,   $C_P_BG,  $C_P_FG],
    ['Pobre Extremo',$extremo, $C_PE_BG, $C_PE_FG],
];
foreach ($statsData as $i => [$lbl,$val,$bg,$fg]) {
    [$c1,$c2] = $statRanges[$i];
    $sheet->mergeCells("{$c1}{$statsRow}:{$c2}{$statsRow}");
    $sheet->setCellValue("{$c1}{$statsRow}", "$lbl: $val");
    $sheet->getStyle("{$c1}{$statsRow}:{$c2}{$statsRow}")->applyFromArray([
        'font'      => ['bold'=>true,'size'=>9,'color'=>['rgb'=>$fg],'name'=>'Arial'],
        'fill'      => ['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>$bg]],
        'alignment' => ['horizontal'=>Alignment::HORIZONTAL_CENTER,'vertical'=>Alignment::VERTICAL_CENTER],
        'borders'   => ['allBorders'=>['borderStyle'=>Border::BORDER_THIN,'color'=>['rgb'=>$C_BRD]]],
    ]);
}
$sheet->getRowDimension($statsRow)->setRowHeight(18);

/* ── Cabecera tabla ── */
$headerRow = $statsRow + 2;
foreach ($cabeceras as $ci => $nombre) {
    $col = Coordinate::stringFromColumnIndex($ci + 1);
    $sheet->setCellValue("{$col}{$headerRow}", $nombre);
}
$sheet->getStyle("A{$headerRow}:{$lastCol}{$headerRow}")->applyFromArray([
    'font'      => ['bold'=>true,'size'=>9,'color'=>['rgb'=>$C_HDR_FG],'name'=>'Arial'],
    'fill'      => ['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>$C_HDR_BG]],
    'alignment' => ['horizontal'=>Alignment::HORIZONTAL_CENTER,'vertical'=>Alignment::VERTICAL_CENTER],
    'borders'   => ['allBorders'=>['borderStyle'=>Border::BORDER_THIN,'color'=>['rgb'=>'FFFFFF']]],
]);
$sheet->getRowDimension($headerRow)->setRowHeight(16);

/* ── Datos ── */
foreach ($filas as $idx => $row) {
    $r      = $headerRow + 1 + $idx;
    $bgBase = ($idx % 2 === 0) ? 'FFFFFF' : $C_ALT;

    // Mismo orden que cabeceras
    $valores = [
        $idx + 1,
        $row['IdEmpa'],
        $row['DNI'],
        $row['Solicitante'],
        $row['TipoCSE'],
        $row['TipoSolicitud'],
        $row['Integrantes'],
        $row['Archivador'],
        $row['Empadronador'],
        $row['Fecha'],
        $row['Observaciones'],
    ];

    foreach ($valores as $ci => $val) {
        $col = Coordinate::stringFromColumnIndex($ci + 1);
        $sheet->setCellValue("{$col}{$r}", $val ?? '');
    }

    $sheet->getStyle("A{$r}:{$lastCol}{$r}")->applyFromArray([
        'font'      => ['size'=>9,'name'=>'Arial'],
        'fill'      => ['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>$bgBase]],
        'alignment' => ['vertical'=>Alignment::VERTICAL_CENTER],
        'borders'   => ['bottom'=>['borderStyle'=>Border::BORDER_THIN,'color'=>['rgb'=>$C_BRD]]],
    ]);

    // Color Tipo CSE — columna 5 (E)
    $tipoCSE = $row['TipoCSE'] ?? '';
    [$cBg,$cFg] = match($tipoCSE) {
        'NO POBRE'      => [$C_NP_BG, $C_NP_FG],
        'POBRE'         => [$C_P_BG,  $C_P_FG],
        'POBRE EXTREMO' => [$C_PE_BG, $C_PE_FG],
        default         => [$bgBase,  $C_HDR_BG],
    };
    $sheet->getStyle("E{$r}")->applyFromArray([
        'font' => ['bold'=>true,'color'=>['rgb'=>$cFg]],
        'fill' => ['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>$cBg]],
    ]);

    $sheet->getRowDimension($r)->setRowHeight(14);
}

/* ── Borde exterior tabla ── */
if ($total > 0) {
    $lastDataRow = $headerRow + $total;
    $sheet->getStyle("A{$headerRow}:{$lastCol}{$lastDataRow}")->applyFromArray([
        'borders' => ['outline'=>['borderStyle'=>Border::BORDER_MEDIUM,'color'=>['rgb'=>$C_HDR_BG]]],
    ]);
}

/* ── Anchos columna (mismo orden que cabeceras) ── */
$anchos = [5, 7, 11, 30, 16, 20, 12, 12, 20, 12, 35];
foreach ($anchos as $ci => $ancho) {
    $col = Coordinate::stringFromColumnIndex($ci + 1);
    $sheet->getColumnDimension($col)->setWidth($ancho);
}

/* ── Freeze + AutoFiltro ── */
$sheet->freezePane("A" . ($headerRow + 1));
$sheet->setAutoFilter("A{$headerRow}:{$lastCol}{$headerRow}");
/* ── Proteger hoja ── */
$sheet->getProtection()->setSheet(true);
$sheet->getProtection()->setSort(true);
$sheet->getProtection()->setInsertRows(false);
$sheet->getProtection()->setFormatCells(false);
$sheet->getProtection()->setPassword('PISCO@2026');

/* ── Hoja 2: Resumen ── */
$resumen = $spreadsheet->createSheet();
$resumen->setTitle('Resumen');
$resumen->mergeCells('A1:C1');
$resumen->setCellValue('A1', 'RESUMEN ESTADÍSTICO');
$resumen->getStyle('A1')->applyFromArray([
    'font'      => ['bold'=>true,'size'=>11,'color'=>['rgb'=>$C_HDR_FG],'name'=>'Arial'],
    'fill'      => ['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>$C_HDR_BG]],
    'alignment' => ['horizontal'=>Alignment::HORIZONTAL_CENTER],
]);
$resumen->getRowDimension(1)->setRowHeight(22);

$rStats = [
    [2, 'Tipo CSE',      'Cantidad', '%',   $C_HDR_FG, $C_META],
    [3, 'Total',         $total,     '100%', $C_HDR_FG, $C_META],
    [4, 'No Pobre',      $nopobre,   ($total>0?round($nopobre/$total*100,1):'0').'%', $C_NP_FG, $C_NP_BG],
    [5, 'Pobre',         $pobre,     ($total>0?round($pobre/$total*100,1):'0').'%',   $C_P_FG,  $C_P_BG],
    [6, 'Pobre Extremo', $extremo,   ($total>0?round($extremo/$total*100,1):'0').'%', $C_PE_FG, $C_PE_BG],
];
foreach ($rStats as [$rn,$a,$b,$c,$fg,$bg]) {
    $resumen->setCellValue("A{$rn}", $a);
    $resumen->setCellValue("B{$rn}", $b);
    $resumen->setCellValue("C{$rn}", $c);
    $resumen->getStyle("A{$rn}:C{$rn}")->applyFromArray([
        'font'      => ['bold'=>true,'name'=>'Arial','size'=>9,'color'=>['rgb'=>$fg]],
        'fill'      => ['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>$bg]],
        'borders'   => ['allBorders'=>['borderStyle'=>Border::BORDER_THIN,'color'=>['rgb'=>$C_BRD]]],
        'alignment' => ['horizontal'=>Alignment::HORIZONTAL_CENTER],
    ]);
}
$resumen->getStyle('A2:C2')->getFont()->getColor()->setRGB('1A2332');
$resumen->getStyle('A3:C3')->getFont()->getColor()->setRGB('1A2332');
$resumen->getColumnDimension('A')->setWidth(20);
$resumen->getColumnDimension('B')->setWidth(12);
$resumen->getColumnDimension('C')->setWidth(10);
/* ── Proteger hoja resumen ── */
$resumen->getProtection()->setSheet(true);
$resumen->getProtection()->setPassword('PISCO@2026');

/* ══════════ ENVIAR ══════════ */
ob_end_clean();

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="Reporte_SEM_' . date('Ymd_His') . '.xlsx"');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;