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

    <link rel="icon" type="image/png" href="../../backend/img/logoPisco.png" />
</head>

<body>

    <!-- ══ OVERLAY MOBILE ══ -->
    <div class="body-overlay"></div>

    <!-- ══ NAVBAR ══ -->
    <?php include "../navbar/navbar.php"; ?>

    <!-- ══ MAIN ══ -->
    <div class="main">

        <!-- TOPBAR -->
        <?php include "../topbar/topbar.php"; ?>

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
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
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
                                <polyline points="23 4 23 10 17 10" />
                                <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10" />
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
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                <circle cx="12" cy="7" r="4" />
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
                if (target === 0) {
                    el.textContent = '000';
                    return;
                }
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
        // Sidebar toggle
        const sidebar  = document.querySelector('.sidebar');
        const main     = document.querySelector('.main');
        const overlay  = document.querySelector('.body-overlay');
        const isMobile = () => window.innerWidth <= 768;

        document.getElementById('sidebarCollapse').addEventListener('click', () => {
            if (isMobile()) {
                sidebar.classList.toggle('mobile-active');
                overlay.classList.toggle('active');
            } else {
                sidebar.classList.toggle('collapsed');
                main.classList.toggle('collapsed');
            }
        });
        overlay.addEventListener('click', () => {
            sidebar.classList.remove('mobile-active');
            overlay.classList.remove('active');
        });
        window.addEventListener('resize', () => {
            if (!isMobile()) {
                sidebar.classList.remove('mobile-active');
                overlay.classList.remove('active');
            }
        });
    </script>

</body>

</html>