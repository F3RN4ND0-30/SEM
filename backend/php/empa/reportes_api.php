<?php
/**
 * API Reportes — SEM
 * Ruta: backend/php/empa/reportes_api.php
 */

header('Content-Type: application/json; charset=utf-8');
require_once '../../db/conexion.php';

function limpiar($v) {
    return htmlspecialchars(strip_tags(trim($v ?? '')));
}

/* ── Construye WHERE con los filtros disponibles ── */
function buildWhere(array &$p): string {
    $w = [];

    $fi = limpiar($_GET['fecha_inicio'] ?? '');
    $ff = limpiar($_GET['fecha_fin']    ?? '');
    $em = limpiar($_GET['empadronador'] ?? '');
    $tp = limpiar($_GET['tipo_cse']     ?? '');

    if ($fi) { $w[] = 'DATE(e.Fecha) >= :fi';        $p[':fi'] = $fi; }
    if ($ff) { $w[] = 'DATE(e.Fecha) <= :ff';        $p[':ff'] = $ff; }
    if ($em) { $w[] = 'e.Empadronador LIKE :em';     $p[':em'] = "%$em%"; }
    if ($tp) { $w[] = 'e.TipoCSE = :tp';             $p[':tp'] = $tp; }

    return $w ? 'WHERE ' . implode(' AND ', $w) : '';
}

$accion = limpiar($_GET['accion'] ?? 'listar');

match ($accion) {
    'listar'         => accionListar($pdo),
    'estadisticas'   => accionEstadisticas($pdo),
    'empadronadores' => accionEmpadronadores($pdo),
    default          => print json_encode(['error' => 'Acción inválida']),
};

/* ══════════════════════════════════════════
   LISTAR (paginado)
══════════════════════════════════════════ */
function accionListar(PDO $pdo): void {
    $pagina = max(1, (int)($_GET['pagina']     ?? 1));
    $porPag = min(100, max(5, (int)($_GET['por_pagina'] ?? 25)));
    $offset = ($pagina - 1) * $porPag;

    $p = [];
    $w = buildWhere($p);

    try {
        $cnt = $pdo->prepare("SELECT COUNT(*) FROM empadronamiento e LEFT JOIN tipo_solicitud ts ON e.IdTipoSoli = ts.IdTipoSoli $w");
        $cnt->execute($p);
        $total = (int) $cnt->fetchColumn();

        $st = $pdo->prepare("
            SELECT
                e.IdEmpa,
                e.DNI_Soli                                      AS dni,
                e.Solicitante                                   AS solicitante,
                e.TipoCSE                                       AS tipo_cse,
                e.Empadronador                                  AS empadronador,
                e.Integrantes                                   AS integrantes,
                e.Archivador                                    AS archivador,
                ts.Descripcion                                  AS tipo_solicitud,
                e.Observaciones                                 AS observaciones,
                DATE_FORMAT(e.Fecha, '%d/%m/%Y')                AS fecha
            FROM empadronamiento e
            LEFT JOIN tipo_solicitud ts ON e.IdTipoSoli = ts.IdTipoSoli
            $w
            ORDER BY e.Fecha DESC, e.IdEmpa DESC
            LIMIT :lim OFFSET :off
        ");

        foreach ($p as $k => $v) $st->bindValue($k, $v);
        $st->bindValue(':lim', $porPag, PDO::PARAM_INT);
        $st->bindValue(':off', $offset, PDO::PARAM_INT);
        $st->execute();

        echo json_encode([
            'ok'            => true,
            'datos'         => $st->fetchAll(PDO::FETCH_ASSOC),
            'total'         => $total,
            'pagina'        => $pagina,
            'por_pagina'    => $porPag,
            'total_paginas' => (int) ceil($total / $porPag),
        ]);

    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}

/* ══════════════════════════════════════════
   ESTADÍSTICAS
══════════════════════════════════════════ */
function accionEstadisticas(PDO $pdo): void {
    $p = [];
    $w = buildWhere($p);

    try {
        $st = $pdo->prepare("
            SELECT
                COUNT(*)                              AS total,
                SUM(e.TipoCSE = 'NO POBRE')           AS no_pobre,
                SUM(e.TipoCSE = 'POBRE')              AS pobre,
                SUM(e.TipoCSE = 'POBRE EXTREMO')      AS extremo,
                COUNT(DISTINCT e.Empadronador)        AS num_empadronadores
            FROM empadronamiento e
            $w
        ");
        $st->execute($p);
        echo json_encode(['ok' => true, 'stats' => $st->fetch(PDO::FETCH_ASSOC)]);

    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}

/* ══════════════════════════════════════════
   LISTA DE EMPADRONADORES
══════════════════════════════════════════ */
function accionEmpadronadores(PDO $pdo): void {
    try {
        $st = $pdo->query("
            SELECT DISTINCT Empadronador
            FROM empadronamiento
            WHERE Empadronador IS NOT NULL AND Empadronador != ''
            ORDER BY Empadronador
        ");
        echo json_encode(['ok' => true, 'lista' => $st->fetchAll(PDO::FETCH_COLUMN)]);

    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}