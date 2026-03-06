<?php
require_once "../../frontend/auth.php";
require_once '../../backend/php/empa/listar_empadronamiento.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Listado de Empadronamientos</title>
    <link rel="stylesheet" href="../../backend/css/navbar/navbar.css" />
    <link rel="stylesheet" href="../../backend/css/empa/listar_empa.css" />
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <link rel="icon" type="image/png" href="../../backend/img/logoPisco.png" />

</head>

<body>
    <?php include "../navbar/navbar.php"; ?>

    <div class="table-container">
        <h2>Listado de Empadronamientos</h2>
        <table id="empadronamientos" class="display nowrap" style="width:100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tipo Solicitud</th>
                    <th>Tipo Remisión</th>
                    <th>Fecha</th>
                    <th>D100</th>
                    <th>S100</th>
                    <th>Fecha S100</th>
                    <th>FSU</th>
                    <th>Fecha FSU</th>
                    <th>Tipo Doc</th>
                    <th>DNI</th>
                    <th>Solicitante</th>
                    <th>Integrantes</th>
                    <th>Archivador</th>
                    <th>Año</th>
                    <th>Tipo CSE</th>
                    <th>Inicio CSE</th>
                    <th>Final CSE</th>
                    <th>Empadronador</th>
                    <th>Observaciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($empadronamientos as $e): ?>
                    <tr>
                        <td><?= $e['IdEmpa'] ?></td>
                        <td><?= $e['TipoSolicitud'] ?></td>
                        <td><?= $e['TipoRemision'] ?></td>
                        <td><?= $e['Formato_D100'] ?></td>
                        <td><?= $e['Fecha'] ?></td>
                        <td><?= $e['S100'] ?></td>
                        <td><?= $e['Fecha_S100'] ?></td>
                        <td><?= $e['FSU'] ?></td>
                        <td><?= $e['Fecha_FSU'] ?></td>
                        <td><?= $e['TipoDocu'] ?></td>
                        <td><?= $e['DNI_Soli'] ?></td>
                        <td><?= $e['Solicitante'] ?></td>
                        <td><?= $e['Integrantes'] ?></td>
                        <td><?= $e['Archivador'] ?></td>
                        <td><?= $e['AÑO'] ?></td>
                        <td><?= $e['TipoCSE'] ?></td>
                        <td><?= $e['InicioCSE'] ?></td>
                        <td><?= $e['FinalCSE'] ?></td>
                        <td><?= $e['Empadronador'] ?></td>
                        <td><?= $e['Observaciones'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
<script>
    $(document).ready(function() {
        $('#empadronamientos').DataTable({
            responsive: true,
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
            },
            order: [
                [0, 'desc']
            ]
        });
    });
</script>
<!-- script para el navbar -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');
        const tableContainer = document.querySelector('.table-container');
        const overlay = document.querySelector('.body-overlay');
        const toggleBtn = document.getElementById('sidebarCollapse');

        const isMobile = () => window.innerWidth <= 768;

        toggleBtn.addEventListener('click', () => {
            if (isMobile()) {
                // Móvil: ocultar/mostrar completamente
                sidebar.classList.toggle('mobile-active');
                overlay.classList.toggle('active');
            } else {
                // Desktop: colapsar ancho
                sidebar.classList.toggle('collapsed');
                tableContainer.classList.toggle('collapsed');
            }
        });

        overlay.addEventListener('click', () => {
            sidebar.classList.remove('mobile-active');
            overlay.classList.remove('active');
        });

        // Ajuste automático al redimensionar ventana
        window.addEventListener('resize', () => {
            if (!isMobile()) {
                sidebar.classList.remove('mobile-active');
                overlay.classList.remove('active');
            }
        });
    });
</script>

</html>