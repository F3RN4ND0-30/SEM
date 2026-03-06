<?php
// frontend/sisvis/escritorio.php
session_start();
require_once __DIR__ . '/../../backend/db/conexion.php'; // exporta $pdo

// --- Proteger ruta: solo usuarios logueados ---
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// --- Datos del usuario en sesión ---
$userName     = $_SESSION['user_name']  ?? 'Usuario';
$userInitial  = strtoupper(substr($userName, 0, 1));
$userType     = $_SESSION['user_type']  ?? '';

// --- Consultas a la BD ---
// Total por clasificación (ajusta el nombre de columna según tu tabla)
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

// Porcentajes (evitar división por cero)
$pctNoPobre = $totalRegistros > 0 ? round($totalNoPobre  / $totalRegistros * 100) : 0;
$pctPobre   = $totalRegistros > 0 ? round($totalPobre    / $totalRegistros * 100) : 0;
$pctExtremo = $totalRegistros > 0 ? round($totalExtremo  / $totalRegistros * 100) : 0;

// Actualizaciones de hoy
$stmtActual = $pdo->query("
    SELECT COUNT(*) FROM empadronamiento
    WHERE DATE(Fecha) = CURDATE()
");
$actualizaciones = (int)$stmtActual->fetchColumn();

// Usuarios activos (Estado = 1)
$stmtUsers = $pdo->query("SELECT COUNT(*) FROM usuarios WHERE Estado = 1");
$usuariosActivos = (int)$stmtUsers->fetchColumn();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Escritorio — SEM</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../backend/css/sisvis/escritorio.css">
</head>
<body>

<!-- ══ SIDEBAR ══ -->
<aside class="sidebar">
    <div class="logo">
        <div class="logo-flag">
            <span></span><span></span><span></span>
        </div>
        <div class="logo-text">
            SEM
            <small>Sistema Nacional</small>
        </div>
    </div>

    <span class="nav-label">Menú</span>

    <a class="nav-item active" href="escritorio.php">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/>
            <rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>
        </svg>
        Inicio
    </a>

    <a class="nav-item" href="#">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
            <polyline points="14 2 14 8 20 8"/>
            <line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
        </svg>
        Reportes
    </a>

    <a class="nav-item" href="#">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
            <circle cx="9" cy="7" r="4"/>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
        </svg>
        Usuarios
    </a>

    <a class="nav-item" href="#">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="3"/>
            <path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/>
        </svg>
        Configuración
    </a>

    <div class="sidebar-footer">
        <a class="nav-item logout" href="../../frontend/logout.php">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                <polyline points="16 17 21 12 16 7"/>
                <line x1="21" y1="12" x2="9" y2="12"/>
            </svg>
            Cerrar Sesión
        </a>
    </div>
</aside>

<!-- ══ MAIN ══ -->
<div class="main">

    <!-- TOPBAR -->
    <header class="topbar">
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
                    <div class="chip-label">No Pobres</div>
                    <div class="chip-value" data-count="<?= $totalNoPobre ?>">000</div>
                </div>
            </div>
            <div class="chip yellow">
                <div class="chip-dot"></div>
                <div>
                    <div class="chip-label">Pobres</div>
                    <div class="chip-value" data-count="<?= $totalPobre ?>">000</div>
                </div>
            </div>
            <div class="chip red">
                <div class="chip-dot"></div>
                <div>
                    <div class="chip-label">Pobre Extremo</div>
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
                    <div class="stat-card-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                    </div>
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
                    <div class="stat-card-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="23 4 23 10 17 10"/>
                            <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/>
                        </svg>
                    </div>
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
                    <div class="stat-card-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                    </div>
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

<script>
    window.addEventListener('load', () => {
        // Animar barras
        setTimeout(() => {
            document.querySelectorAll('.bar-fill').forEach(el => {
                el.style.width = el.dataset.w + '%';
            });
            document.querySelectorAll('.stat-card-bar-fill').forEach(el => {
                el.style.width = el.dataset.w + '%';
            });
        }, 300);

        // Count-up
        function countUp(el, target, duration = 1400) {
            if (target === 0) { el.textContent = '000'; return; }
            let start = 0;
            const step = target / (duration / 16);
            const timer = setInterval(() => {
                start = Math.min(start + step, target);
                el.textContent = Math.floor(start).toString().padStart(3, '0');
                if (start >= target) clearInterval(timer);
            }, 16);
        }

        setTimeout(() => {
            document.querySelectorAll('[data-count]').forEach(el => {
                countUp(el, parseInt(el.dataset.count));
            });
        }, 400);
    });
</script>

</body>
</html>