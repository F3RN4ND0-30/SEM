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
    <link rel="stylesheet" href="../../backend/css/empa/modal_empa.css" />
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
    <style>
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
    <?php include "../navbar/navbar.php"; ?>
    <div class="main">

        <!-- TOPBAR -->
        <header class="topbar">
            <button id="toggleSidebar" class="topbar-toggle">☰</button>
            <div class="topbar-title">Lista de <span>Empadronamiento</span></div>
            <div class="topbar-right">
                <span class="badge-tag">En vivo</span>
                <div class="user-chip">
                    <div class="user-avatar"><?= htmlspecialchars($userInitial) ?></div>
                    <?= htmlspecialchars($userName) ?>
                </div>
            </div>
        </header>

        <div class="table-container">
            <table id="empadronamientos" class="display nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tipo Solicitud</th>
                        <th>Tipo Remisión</th>                        
                        <th>D100</th>
                        <th>Fecha</th>
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
                        <?php if ($_SESSION['user_type'] == 1): ?>
                            <th>Acciones</th>
                        <?php endif; ?>
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
                            <?php if ($_SESSION['user_type'] == 1): ?>
                                <td>
                                    <button class="btn-editar" data-id="<?= $e['IdEmpa'] ?>">
                                        ✏️ Editar
                                    </button>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- MODAL DE EDICIÓN -->
        <div id="modalEditar" class="modal">
            <div class="modal-contenido">

                <span class="cerrar">&times;</span>
                <h3>Editar Empadronamiento</h3>

                <form id="formEditar">

                    <input type="hidden" name="id" id="edit_id">

                    <label>Tipo Solicitud</label>
                    <input type="text" name="tipo_solicitud" id="edit_tipo_soli">

                    <label>Tipo Remisión</label>
                    <input type="text" name="tipo_remision" id="edit_tipo_remi">

                    <label>DNI</label>
                    <input type="number" name="dni_solicitante" id="edit_dni">

                    <label>Solicitante</label>
                    <input type="text" name="nombre_solicitante" id="edit_solicitante">

                    <label>Integrantes</label>
                    <input type="number" name="num_integrantes" id="edit_integrantes">

                    <label>Archivador</label>
                    <input type="number" name="num_archivador" id="edit_archivador">

                    <label>Año</label>
                    <input type="number" name="anio" id="edit_anio">

                    <label>Tipo CSE</label>
                    <select name="tipo_cse" id="edit_tipo_cse">
                        <option>NO POBRE</option>
                        <option>POBRE</option>
                        <option>POBRE EXTREMO</option>
                    </select>

                    <label>Empadronador</label>
                    <input type="text" name="empadronador" id="edit_empadronador">

                    <label>Observaciones</label>
                    <textarea name="observaciones" id="edit_observaciones"></textarea>

                    <br>

                    <button type="submit">Guardar cambios</button>

                </form>

            </div>
        </div>
    </div>


    <script src="../../backend/js/navbar/sidebar-toggle.js"></script>

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

    <!-- SCRIPT PARA EL MODAL Y EDITAR REGISTRO -->
    <script>
        // ABRIR MODAL Y CARGAR DATOS
        $(document).on("click", ".btn-editar", function() {
            let id = $(this).data("id");
            const modal = $("#modalEditar");

            // Mostrar modal
            modal.addClass("activo");

            // Obtener datos vía AJAX
            $.ajax({
                url: "../../backend/php/empa/obtener_empa.php",
                type: "GET",
                data: {
                    id: id
                },
                dataType: "json",
                success: function(data) {
                    $("#edit_id").val(data.IdEmpa);
                    $("#edit_tipo_soli").val(data.IdTipoSoli);
                    $("#edit_tipo_remi").val(data.IdTipoRemi);
                    $("#edit_dni").val(data.DNI_Soli);
                    $("#edit_solicitante").val(data.Solicitante);
                    $("#edit_integrantes").val(data.Integrantes);
                    $("#edit_archivador").val(data.Archivador);
                    $("#edit_anio").val(data.AÑO);
                    $("#edit_tipo_cse").val(data.TipoCSE);
                    $("#edit_empadronador").val(data.Empadronador);
                    $("#edit_observaciones").val(data.Observaciones);
                },
                error: function() {
                    alert("Error al obtener los datos del registro");
                }
            });
        });

        // CERRAR MODAL (botón X)
        $(document).on("click", ".cerrar", function() {
            $("#modalEditar").removeClass("activo");
        });

        // CERRAR MODAL SI HACEN CLICK FUERA DEL CONTENIDO
        $(window).on("click", function(e) {
            const modal = $("#modalEditar");
            if ($(e.target).is(modal)) {
                modal.removeClass("activo");
            }
        });

        // GUARDAR CAMBIOS
        $("#formEditar").submit(function(e) {
            e.preventDefault();
            $.ajax({
                url: "../../backend/php/empa/actualizar_empa.php",
                type: "POST",
                data: $(this).serialize(),
                success: function() {
                    alert("Registro actualizado correctamente");
                    $("#modalEditar").removeClass("activo");
                    location.reload(); // Opcional: reemplazar con actualización de tabla sin recargar
                },
                error: function() {
                    alert("Error al actualizar el registro");
                }
            });
        });
    </script>

    <!-- SCRIPT PARA EL NAVBAR -->
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
</body>

</html>