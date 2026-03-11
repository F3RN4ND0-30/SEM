<?php

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

$usuarioActual = $_SESSION['user_name'] ?? 'Desconocido';

require_once '../../db/conexion.php';

date_default_timezone_set('America/Lima');

function limpiar($v)
{
  return htmlspecialchars(strip_tags(trim($v ?? '')));
}
function esc($v)
{
  return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
}

$p = [];
$w = [];
$fi = limpiar($_GET['fecha_inicio'] ?? '');
$ff = limpiar($_GET['fecha_fin']    ?? '');
$em = limpiar($_GET['empadronador'] ?? '');
$tp = limpiar($_GET['tipo_cse']     ?? '');

if ($fi) {
  $w[] = 'DATE(e.Fecha) >= :fi';
  $p[':fi'] = $fi;
}
if ($ff) {
  $w[] = 'DATE(e.Fecha) <= :ff';
  $p[':ff'] = $ff;
}
if ($em) {
  $w[] = 'e.Empadronador LIKE :em';
  $p[':em'] = "%$em%";
}
if ($tp) {
  $w[] = 'e.TipoCSE = :tp';
  $p[':tp'] = $tp;
}
$where = $w ? 'WHERE ' . implode(' AND ', $w) : '';

try {
  $st = $pdo->prepare("
        SELECT
            e.IdEmpa, e.DNI_Soli, e.Solicitante,
            e.TipoCSE, ts.Descripcion AS TipoSolicitud,
            e.Integrantes, e.Archivador, e.Empadronador,
            DATE_FORMAT(e.Fecha, '%d/%m/%Y') AS fecha,
            e.Observaciones
        FROM empadronamiento e
        LEFT JOIN tipo_solicitud ts ON e.IdTipoSoli = ts.IdTipoSoli
        LEFT JOIN tipo_remision  tr ON e.IdTipoRemi = tr.IdTipoRemi
        $where
        ORDER BY e.Fecha DESC, e.IdEmpa DESC
    ");
  $st->execute($p);
  $filas = $st->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  die('Error: ' . $e->getMessage());
}

$total   = count($filas);
$nopobre = count(array_filter($filas, fn($r) => $r['TipoCSE'] === 'NO POBRE'));
$pobre   = count(array_filter($filas, fn($r) => $r['TipoCSE'] === 'POBRE'));
$extremo = count(array_filter($filas, fn($r) => $r['TipoCSE'] === 'POBRE EXTREMO'));

$periodo = match (true) {
  (bool)$fi && (bool)$ff => "Del $fi al $ff",
  (bool)$fi              => "Desde $fi",
  (bool)$ff              => "Hasta $ff",
  default                => 'Todos los registros',
};
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Reporte — SEM</title>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Nunito:wght@400;700;800&display=swap" rel="stylesheet">

  <link rel="icon" type="image/png" href="../../img/logoPisco.png" />
  <style>
    /* ── Reset ── */
    *,
    *::before,
    *::after {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Plus Jakarta Sans', sans-serif;
      color: #1a2332;
      background: #fff;
      font-size: 10.5px;
    }

    /* ── BOTÓN IMPRIMIR ── */
    .btn-print {
      position: fixed;
      top: 14px;
      right: 14px;
      background: #1a2332;
      color: #fff;
      border: none;
      border-radius: 8px;
      padding: 9px 18px;
      cursor: pointer;
      font-family: 'Plus Jakarta Sans', sans-serif;
      font-size: 12px;
      font-weight: 700;
      box-shadow: 0 4px 14px rgba(26, 35, 50, .3);
      display: flex;
      align-items: center;
      gap: 7px;
      z-index: 999;
      transition: background .15s;
    }

    .btn-print:hover {
      background: #2a3f6f;
    }

    /* ── ENCABEZADO ── */
    .cabecera {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 18px 28px 14px;
      border-bottom: 3px solid #1a2332;
      margin-bottom: 16px;
    }

    .cab-logo {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .cab-logo-icono {
      width: 38px;
      height: 38px;
      background: #1a2332;
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 18px;
    }

    .cab-logo-txt .marca {
      font-size: 17px;
      font-weight: 800;
      color: #1a2332;
      letter-spacing: -.4px;
      font-family: 'Nunito', sans-serif;
    }

    .cab-logo-txt .sub {
      font-size: 9px;
      color: #6b7f99;
      margin-top: 1px;
    }

    .cab-der {
      text-align: right;
    }

    .cab-der .titulo {
      font-size: 13px;
      font-weight: 800;
      color: #1a2332;
      text-transform: uppercase;
      letter-spacing: .4px;
    }

    .cab-der .meta {
      font-size: 9px;
      color: #6b7f99;
      margin-top: 3px;
      font-family: monospace;
    }

    /* ── CHIPS DE FILTROS ── */
    .filtros-aplicados {
      display: flex;
      gap: 8px;
      flex-wrap: wrap;
      padding: 0 28px 12px;
      border-bottom: 1px solid #e8eef6;
      margin-bottom: 14px;
    }

    .f-chip {
      background: #eef2f9;
      color: #2a3f6f;
      font-size: 8.5px;
      font-weight: 700;
      padding: 3px 9px;
      border-radius: 20px;
      display: flex;
      align-items: center;
      gap: 4px;
    }

    .f-chip .lbl {
      color: #6b7f99;
      font-weight: 500;
    }

    /* ── ESTADÍSTICAS ── */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 10px;
      padding: 0 28px 16px;
    }

    .sbox {
      border-radius: 8px;
      padding: 10px 12px;
      text-align: center;
    }

    .sbox .n {
      font-size: 20px;
      font-weight: 800;
      font-family: 'Nunito', sans-serif;
      line-height: 1;
    }

    .sbox .l {
      font-size: 8px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .5px;
      margin-top: 3px;
    }

    .sbox-total {
      background: #eef2f9;
      border: 1px solid #c8d4ea;
    }

    .sbox-total .n {
      color: #1a2332;
    }

    .sbox-total .l {
      color: #6b7f99;
    }

    .sbox-np {
      background: #f0fdf4;
      border: 1px solid #86efac;
    }

    .sbox-np .n {
      color: #15803d;
    }

    .sbox-np .l {
      color: #16a34a;
    }

    .sbox-p {
      background: #fffbeb;
      border: 1px solid #fcd34d;
    }

    .sbox-p .n {
      color: #92400e;
    }

    .sbox-p .l {
      color: #b45309;
    }

    .sbox-pe {
      background: #fef2f2;
      border: 1px solid #fca5a5;
    }

    .sbox-pe .n {
      color: #991b1b;
    }

    .sbox-pe .l {
      color: #b91c1c;
    }

    /* ── TABLA ── */
    .tabla-wrap {
      padding: 0 28px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    thead th {
      background: #1a2332;
      color: #fff;
      font-size: 7.5px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .5px;
      padding: 7px 8px;
      text-align: left;
    }

    tbody tr:nth-child(even) {
      background: #f5f8fd;
    }

    tbody tr {
      border-bottom: 1px solid #dde5f0;
    }

    tbody tr:last-child {
      border-bottom: 2px solid #1a2332;
    }

    tbody td {
      padding: 5.5px 8px;
      vertical-align: middle;
    }

    .td-id {
      font-family: monospace;
      color: #6b7f99;
      font-size: 9px;
    }

    .td-dni {
      font-family: monospace;
      font-size: 9.5px;
    }

    .td-bold {
      font-weight: 700;
    }

    .td-fecha {
      font-family: monospace;
      color: #6b7f99;
      font-size: 9px;
    }

    .td-obs {
      color: #6b7f99;
      font-size: 9px;
      font-style: italic;
    }

    .badge {
      display: inline-block;
      padding: 2px 7px;
      border-radius: 20px;
      font-size: 8px;
      font-weight: 700;
      white-space: nowrap;
    }

    .b-np {
      background: #f0fdf4;
      color: #15803d;
    }

    .b-p {
      background: #fffbeb;
      color: #92400e;
    }

    .b-pe {
      background: #fef2f2;
      color: #991b1b;
    }

    /* ── PIE ── */
    .pie {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 11px 28px 8px;
      margin-top: 16px;
      border-top: 2px solid #dde5f0;
      font-size: 8.5px;
      color: #6b7f99;
    }

    .pie strong {
      color: #1a2332;
    }

    /* ── PRINT ── */
    @media print {
      .btn-print {
        display: none !important;
      }

      body {
        print-color-adjust: exact;
        -webkit-print-color-adjust: exact;
      }

      thead th {
        background: #1a2332 !important;
        color: #fff !important;
      }

      .sbox-total {
        background: #eef2f9 !important;
      }

      .sbox-np {
        background: #f0fdf4 !important;
      }

      .sbox-p {
        background: #fffbeb !important;
      }

      .sbox-pe {
        background: #fef2f2 !important;
      }

      tbody tr:nth-child(even) {
        background: #f5f8fd !important;
      }

      .b-np {
        background: #f0fdf4 !important;
        color: #15803d !important;
      }

      .b-p {
        background: #fffbeb !important;
        color: #92400e !important;
      }

      .b-pe {
        background: #fef2f2 !important;
        color: #991b1b !important;
      }
    }
  </style>
</head>

<body>

  <button class="btn-print" onclick="window.print()">🖨 Imprimir / PDF</button>

  <!-- ENCABEZADO -->
  <div class="cabecera">
    <div class="cab-logo">
      <div class="cab-logo-icono">🏛</div>
      <div class="cab-logo-txt">
        <div class="marca">SEM</div>
        <div class="sub">Sistema de Empadronamiento Municipal</div>
      </div>
    </div>
    <div class="cab-der">
      <div class="titulo">Reporte de Empadronamientos</div>
      <div class="meta">
        <?= esc($periodo) ?> &nbsp;·&nbsp; <?= date('d/m/Y H:i') ?><br>
        Usuario: <?= esc($usuarioActual) ?>
      </div>
    </div>
  </div>

  <!-- FILTROS APLICADOS -->
  <div class="filtros-aplicados">
    <div class="f-chip"><span class="lbl">Período:</span> <?= esc($periodo) ?></div>
    <?php if ($em): ?>
      <div class="f-chip"><span class="lbl">Empadronador:</span> <?= esc($em) ?></div>
    <?php endif; ?>
    <?php if ($tp): ?>
      <div class="f-chip"><span class="lbl">Tipo CSE:</span> <?= esc($tp) ?></div>
    <?php endif; ?>
    <div class="f-chip"><span class="lbl">Usuario:</span> <?= esc($usuarioActual) ?></div>
  </div>

  <!-- STATS -->
  <div class="stats-grid">
    <div class="sbox sbox-total">
      <div class="n"><?= $total ?></div>
      <div class="l">Total</div>
    </div>
    <div class="sbox sbox-np">
      <div class="n"><?= $nopobre ?></div>
      <div class="l">No Pobre</div>
    </div>
    <div class="sbox sbox-p">
      <div class="n"><?= $pobre ?></div>
      <div class="l">Pobre</div>
    </div>
    <div class="sbox sbox-pe">
      <div class="n"><?= $extremo ?></div>
      <div class="l">Pobre Extremo</div>
    </div>
  </div>

  <!-- TABLA -->
  <div class="tabla-wrap">
    <?php if (empty($filas)): ?>
      <p style="text-align:center;color:#6b7f99;padding:28px 0">Sin registros para los filtros seleccionados.</p>
    <?php else: ?>
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>ID</th>
            <th>DNI</th>
            <th>Solicitante</th>
            <th>Tipo CSE</th>
            <th>Tipo Solicitud</th>
            <th>Integrantes</th>
            <th>Archivador</th>
            <th>Empadronador</th>
            <th>Fecha</th>
            <th>Observaciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($filas as $i => $r):
            $bc = match ($r['TipoCSE']) {
              'NO POBRE' => 'b-np',
              'POBRE' => 'b-p',
              default => 'b-pe'
            };
          ?>
            <tr>
              <td class="td-id"><?= $i + 1 ?></td>
              <td class="td-id"><?= esc($r['IdEmpa']) ?></td>
              <td class="td-dni"><?= esc($r['DNI_Soli']) ?></td>
              <td class="td-bold"><?= esc($r['Solicitante']) ?></td>
              <td><span class="badge <?= $bc ?>"><?= esc($r['TipoCSE']) ?></span></td>
              <td><?= esc($r['TipoSolicitud']) ?></td>
              <td style="text-align:center"><?= esc($r['Integrantes']) ?></td>
              <td style="text-align:center"><?= esc($r['Archivador']) ?></td>
              <td><?= esc($r['Empadronador']) ?></td>
              <td class="td-fecha"><?= esc($r['fecha']) ?></td>
              <td class="td-obs"><?= esc($r['Observaciones']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>

  <!-- PIE -->
  <div class="pie">
    <span>SEM — Sistema de Empadronamiento Municipal</span>
    <span><strong><?= $total ?></strong> registros exportados</span>
    <span>Generado: <?= date('d/m/Y H:i:s') ?></span>
  </div>

</body>

</html>