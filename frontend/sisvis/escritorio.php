<?php
// frontend/sisvis/escritorio.php
session_start();
require_once __DIR__ . '/../../backend/db/conexion.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$userName     = $_SESSION['user_name']  ?? 'Usuario';
$userInitial  = strtoupper(substr($userName, 0, 1));
$userType     = $_SESSION['user_type']  ?? '';

$stmtClasif = $pdo->query("
    SELECT TipoCSE, COUNT(*) AS total
    FROM empadronamiento
    GROUP BY TipoCSE
");
$clasifRaw = $stmtClasif->fetchAll(PDO::FETCH_KEY_PAIR);

$totalNoPobre  = (int)($clasifRaw['NO POBRE']      ?? 0);
$totalPobre    = (int)($clasifRaw['POBRE']          ?? 0);
$totalExtremo  = (int)($clasifRaw['POBRE EXTREMO']  ?? 0);
$totalRegistros = $totalNoPobre + $totalPobre + $totalExtremo;

$pctNoPobre = $totalRegistros > 0 ? round($totalNoPobre  / $totalRegistros * 100) : 0;
$pctPobre   = $totalRegistros > 0 ? round($totalPobre    / $totalRegistros * 100) : 0;
$pctExtremo = $totalRegistros > 0 ? round($totalExtremo  / $totalRegistros * 100) : 0;

$stmtActual = $pdo->query("
    SELECT COUNT(*) FROM empadronamiento
    WHERE DATE(Fecha) = CURDATE()
");
$actualizaciones = (int)$stmtActual->fetchColumn();

$stmtUsers = $pdo->query("SELECT COUNT(*) FROM usuarios WHERE Estado = 1");
$usuariosActivos = (int)$stmtUsers->fetchColumn();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Escritorio — SEM</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../backend/css/sisvis/escritorio.css">
    <link rel="icon" type="image/png" href="../../backend/img/logoPisco.png" />
    <style>
        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 32px;
            border-bottom: 1px solid var(--border);
            background: var(--surface);
            position: sticky;
            top: 0;
            z-index: 5;
        }

        .topbar-title {
            font-family: 'Nunito', sans-serif;
            font-size: 18px;
            font-weight: 700;
        }

        .topbar-title span {
            color: var(--accent);
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .badge-tag {
            font-size: 11px;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 20px;
            background: rgba(200, 16, 46, 0.12);
            color: #ff6b81;
            letter-spacing: .04em;
        }

        .user-chip {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 6px 12px 6px 6px;
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 50px;
            font-size: 12px;
            color: var(--muted);
        }

        .user-avatar {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent), #1a3a6b);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
            color: #fff;
        }

        /* Ocultar sidebar en móviles por defecto */
        @media (max-width: 768px) {

            /* Overlay solo cubre el contenido, no la topbar ni el toggle */
            .sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.4);
                z-index: 1000;
                /* debajo del toggle */
                opacity: 0;
                visibility: hidden;
                transition: opacity 0.3s ease;
                pointer-events: all;
                /* sí bloquea el contenido debajo */
            }

            /* Cuando esté activo */
            .sidebar-overlay.active {
                opacity: 1;
                visibility: visible;
            }

            /* Sidebar encima del overlay */
            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                height: 100%;
                width: var(--sidebar-w);
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                z-index: 1005;
                /* encima del overlay */
            }

            /* Toggle siempre encima de todo */
            .topbar-toggle {
                z-index: 1010;
                /* encima de sidebar y overlay */
                position: relative;
                /* relativo dentro de la topbar */
            }
        }

        /* En escritorio, ocultar botón toggle */
        @media (min-width: 769px) {
            .topbar-toggle {
                display: none;
            }
        }
    </style>
</head>

<body>

    <!-- ══ NAVBAR (incluye el <aside class="sidebar">) ══ -->
    <?php include "../navbar/navbar.php"; ?>

    <!-- ══ MAIN ══ -->
    <div class="main">

        <!-- TOPBAR -->
        <header class="topbar">
            <button id="toggleSidebar" class="topbar-toggle">☰</button>
            <div class="topbar-title">Panel de <span>Empadronamiento</span></div>
            <div class="topbar-right">
                <span class="badge-tag">En vivo</span>
                <div class="user-chip">
                    <div class="user-avatar"><?= htmlspecialchars($userInitial) ?></div>
                    <?= htmlspecialchars($userName) ?>
                </div>
            </div>
        </header>

        <!-- CONTENT -->
        <div class="content">

            <!-- STAT CHIPS -->
            <div class="stat-chips">
                <div class="chip green">
                    <div class="chip-dot"></div>
                    <div>
                        <div class="chip-label">NO POBRE</div>
                        <div class="chip-value" data-count="<?= $totalNoPobre ?>">000</div>
                    </div>
                </div>
                <div class="chip yellow">
                    <div class="chip-dot"></div>
                    <div>
                        <div class="chip-label">POBRE</div>
                        <div class="chip-value" data-count="<?= $totalPobre ?>">000</div>
                    </div>
                </div>
                <div class="chip red">
                    <div class="chip-dot"></div>
                    <div>
                        <div class="chip-label">POBRE EXTREMO</div>
                        <div class="chip-value" data-count="<?= $totalExtremo ?>">000</div>
                    </div>
                </div>
            </div>

            <!-- GRID -->
            <div class="grid">

                <!-- BARRAS -->
                <div class="panel">
                    <div class="panel-header">
                        <div>
                            <div class="panel-title">Distribución por Nivel de Pobreza</div>
                            <div class="panel-sub">Clasificación de hogares empadronados</div>
                        </div>
                        <span class="pill green">Actualizado</span>
                    </div>

                    <div class="bar-row green">
                        <div class="bar-info">
                            <div class="bar-name">No Pobre</div>
                            <div class="bar-pct"><?= $pctNoPobre ?>%</div>
                        </div>
                        <div class="bar-track">
                            <div class="bar-fill" data-w="<?= $pctNoPobre ?>"></div>
                        </div>
                        <div class="bar-count" style="color:var(--green)"><?= $totalNoPobre ?></div>
                    </div>

                    <div class="bar-row yellow">
                        <div class="bar-info">
                            <div class="bar-name">Pobre</div>
                            <div class="bar-pct"><?= $pctPobre ?>%</div>
                        </div>
                        <div class="bar-track">
                            <div class="bar-fill" data-w="<?= $pctPobre ?>"></div>
                        </div>
                        <div class="bar-count" style="color:var(--yellow)"><?= $totalPobre ?></div>
                    </div>

                    <div class="bar-row red">
                        <div class="bar-info">
                            <div class="bar-name">Pobre Extremo</div>
                            <div class="bar-pct"><?= $pctExtremo ?>%</div>
                        </div>
                        <div class="bar-track">
                            <div class="bar-fill" data-w="<?= $pctExtremo ?>"></div>
                        </div>
                        <div class="bar-count" style="color:var(--red)"><?= $totalExtremo ?></div>
                    </div>

                    <div class="panel-footer">
                        <span class="status-dot">Sistema operativo</span>
                        <span>Total: <?= $totalRegistros ?> registros</span>
                    </div>
                </div>

                <!-- TARJETAS DERECHA -->
                <div class="right-col">

                    <div class="stat-card">
                        <div class="stat-card-body">
                            <div class="stat-card-label">Total Registros</div>
                            <div class="stat-card-value" data-count="<?= $totalRegistros ?>">0</div>
                            <div class="stat-card-sub">Hogares empadronados en total</div>
                            <div class="stat-card-bar">
                                <div class="stat-card-bar-fill" data-w="<?= min(100, round($totalRegistros / 10)) ?>"></div>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-card-body">
                            <div class="stat-card-label">Actualizaciones</div>
                            <div class="stat-card-value" data-count="<?= $actualizaciones ?>">0</div>
                            <div class="stat-card-sub">Registros modificados hoy</div>
                            <div class="stat-card-bar">
                                <div class="stat-card-bar-fill" data-w="<?= min(100, $actualizaciones) ?>"></div>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-card-body">
                            <div class="stat-card-label">Usuarios Activos</div>
                            <div class="stat-card-value" data-count="<?= $usuariosActivos ?>">0</div>
                            <div class="stat-card-sub">Cuentas habilitadas en el sistema</div>
                            <div class="stat-card-bar">
                                <div class="stat-card-bar-fill" data-w="<?= min(100, $usuariosActivos * 10) ?>"></div>
                            </div>
                        </div>
                    </div>

                </div>
            </div><!-- /grid -->
        </div><!-- /content -->
    </div><!-- /main -->

    <!-- sidebar-toggle.js crea el overlay dinámicamente, no duplicar en el HTML -->
    <script src="../../backend/js/navbar/sidebar-toggle.js"></script>

    <script>
        // ── ANIMACIONES ──
        document.addEventListener('DOMContentLoaded', function() {

            // 1. Barras — doble rAF para que el navegador pinte width:0 primero
            requestAnimationFrame(function() {
                requestAnimationFrame(function() {
                    document.querySelectorAll('.bar-fill').forEach(function(el) {
                        el.style.width = (el.dataset.w || 0) + '%';
                    });
                    document.querySelectorAll('.stat-card-bar-fill').forEach(function(el) {
                        el.style.width = (el.dataset.w || 0) + '%';
                    });
                });
            });

            // 2. Count-up para los números grandes
            function countUp(el, target) {
                var duration = 1400;
                if (target === 0) {
                    el.textContent = '000';
                    return;
                }
                var start = 0;
                var step = target / (duration / 16);
                var timer = setInterval(function() {
                    start = Math.min(start + step, target);
                    el.textContent = Math.floor(start).toString().padStart(3, '0');
                    if (start >= target) clearInterval(timer);
                }, 16);
            }

            document.querySelectorAll('[data-count]').forEach(function(el) {
                countUp(el, parseInt(el.dataset.count, 10));
            });
        });
    </script>

</body>

</html>