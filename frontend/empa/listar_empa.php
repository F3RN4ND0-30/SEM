<?php
require_once "../../frontend/auth.php";
require_once '../../backend/php/empa/listar_empadronamiento.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Listado de Empadronamientos — SEM</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Nunito:wght@700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../backend/css/empa/listar_empa.css">
    <link rel="stylesheet" href="../../backend/css/sisvis/escritorio.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <link rel="icon" type="image/png" href="../../backend/img/logoPisco.png">
</head>
<body>

<div class="body-overlay"></div>

<!-- ══ SIDEBAR ══ -->
<?php include "../navbar/navbar.php"; ?>

<!-- ══ MAIN ══ -->
<div class="table-container" id="mainContent">

    <!-- TOPBAR -->
    <header class="topbar">
        <div class="topbar-left">
            <button class="topbar-toggle" id="sidebarCollapse" aria-label="Toggle sidebar">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <line x1="3" y1="6" x2="21" y2="6"/>
                    <line x1="3" y1="12" x2="21" y2="12"/>
                    <line x1="3" y1="18" x2="21" y2="18"/>
                </svg>
            </button>
            <div class="topbar-title">Lista de <span>Empadronamientos</span></div>
        </div>
        <div class="topbar-right">
            <a href="registrar_empa.php" class="btn-new">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Nuevo registro
            </a>
        </div>
    </header>

    <!-- CONTENT -->
    <div class="content">

        <!-- Barra de búsqueda y filtro custom -->
        <div class="search-bar">
            <div class="search-wrap">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" id="customSearch" placeholder="Buscar por solicitante, DNI, empadronador…">
            </div>
            <select id="filterCSE" class="filter-select">
                <option value="">Todos los niveles CSE</option>
                <option value="NO POBRE">No Pobre</option>
                <option value="POBRE">Pobre</option>
                <option value="POBRE EXTREMO">Pobre Extremo</option>
            </select>
        </div>

        <!-- TABLA -->
        <div id="empadronamientos_wrapper">
            <table id="empadronamientos" class="display nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tipo Soli.</th>
                        <th>Tipo Remi.</th>
                        <th>Fecha</th>
                        <th>D100</th>
                        <th>S100</th>
                        <th>F. S100</th>
                        <th>FSU</th>
                        <th>F. FSU</th>
                        <th>Doc.</th>
                        <th>DNI</th>
                        <th>Solicitante</th>
                        <th>Integ.</th>
                        <th>Archiv.</th>
                        <th>Año</th>
                        <th>Tipo CSE</th>
                        <th>Inicio CSE</th>
                        <th>Final CSE</th>
                        <th>Empadronador</th>
                        <th>Obs.</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($empadronamientos as $e):
                        $cse = $e['TipoCSE'] ?? '';
                        $badgeClass = match($cse) {
                            'NO POBRE'      => 'no-pobre',
                            'POBRE'         => 'pobre',
                            'POBRE EXTREMO' => 'extremo',
                            default         => ''
                        };
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($e['IdEmpa']) ?></td>
                            <td><?= htmlspecialchars($e['TipoSolicitud']) ?></td>
                            <td><?= htmlspecialchars($e['TipoRemision']) ?></td>
                            <td><?= htmlspecialchars($e['Fecha']) ?></td>
                            <td><?= htmlspecialchars($e['Formato_D100']) ?></td>
                            <td><?= htmlspecialchars($e['S100']) ?></td>
                            <td><?= htmlspecialchars($e['Fecha_S100']) ?></td>
                            <td><?= htmlspecialchars($e['FSU']) ?></td>
                            <td><?= htmlspecialchars($e['Fecha_FSU']) ?></td>
                            <td><?= htmlspecialchars($e['TipoDocu']) ?></td>
                            <td><?= htmlspecialchars($e['DNI_Soli']) ?></td>
                            <td><?= htmlspecialchars($e['Solicitante']) ?></td>
                            <td><?= htmlspecialchars($e['Integrantes']) ?></td>
                            <td><?= htmlspecialchars($e['Archivador']) ?></td>
                            <td><?= htmlspecialchars($e['AÑO']) ?></td>
                            <td>
                                <?php if ($badgeClass): ?>
                                    <span class="badge-cse <?= $badgeClass ?>"><?= htmlspecialchars($cse) ?></span>
                                <?php else: ?>
                                    <?= htmlspecialchars($cse) ?>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($e['InicioCSE']) ?></td>
                            <td><?= htmlspecialchars($e['FinalCSE']) ?></td>
                            <td><?= htmlspecialchars($e['Empadronador']) ?></td>
                            <td><?= htmlspecialchars($e['Observaciones']) ?></td>
                            <td>
                                <div class="action-btns">
                                    <a href="editar_empa.php?id=<?= $e['IdEmpa'] ?>" class="btn-icon primary" title="Editar">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                        </svg>
                                    </a>
                                    <a href="eliminar_empa.php?id=<?= $e['IdEmpa'] ?>"
                                       class="btn-icon danger"
                                       title="Eliminar"
                                       onclick="return confirm('¿Eliminar este registro?')">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="3 6 5 6 21 6"/>
                                            <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                            <path d="M10 11v6M14 11v6"/>
                                            <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div><!-- /content -->
</div><!-- /table-container -->

<script>
$(document).ready(function () {
    const table = $('#empadronamientos').DataTable({
        responsive: true,
        pageLength: 15,
        lengthMenu: [10, 15, 25, 50, 100],
        dom: 'lrtip',
        language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' },
        order: [[0, 'desc']]
    });

    // Búsqueda global custom
    $('#customSearch').on('keyup', function () {
        table.search(this.value).draw();
    });

    // Filtro por TipoCSE (columna 15)
    $('#filterCSE').on('change', function () {
        table.column(15).search(this.value).draw();
    });

    // Sidebar toggle
    const sidebar     = document.querySelector('.sidebar');
    const mainContent = document.getElementById('mainContent');
    const overlay     = document.querySelector('.body-overlay');
    const isMobile    = () => window.innerWidth <= 768;

    document.getElementById('sidebarCollapse').addEventListener('click', () => {
        if (isMobile()) {
            sidebar.classList.toggle('mobile-active');
            overlay.classList.toggle('active');
        } else {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('collapsed');
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
});
</script>
</body>
</html>