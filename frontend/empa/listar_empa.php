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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../backend/css/navbar/navbar.css" />
    <link rel="stylesheet" href="../../backend/css/empa/listar_empa.css" />
    <link rel="stylesheet" href="../../backend/css/empa/modal_empa.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
    <link rel="icon" type="image/png" href="../../backend/img/logoPisco.png" />
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
                            <td><?= htmlspecialchars($e['TipoSolicitud'] ?? '') ?></td>
                            <td><?= htmlspecialchars($e['TipoRemision'] ?? '') ?></td>
                            <td><?= $e['Formato_D100'] ?></td>
                            <td><?= $e['Fecha'] ?></td>
                            <td><?= $e['S100'] ?></td>
                            <td><?= $e['Fecha_S100'] ?></td>
                            <td><?= $e['FSU'] ?></td>
                            <td><?= $e['Fecha_FSU'] ?></td>
                            <td><?= $e['TipoDocu'] ?></td>
                            <td><?= $e['DNI_Soli'] ?></td>
                            <td><?= htmlspecialchars($e['Solicitante'] ?? '') ?></td>
                            <td><?= $e['Integrantes'] ?></td>
                            <td><?= $e['Archivador'] ?></td>
                            <td><?= $e['AÑO'] ?></td>
                            <td><?= $e['TipoCSE'] ?></td>
                            <td><?= $e['InicioCSE'] ?></td>
                            <td><?= $e['FinalCSE'] ?></td>
                            <td><?= htmlspecialchars($e['Empadronador'] ?? '') ?></td>
                            <td><?= htmlspecialchars($e['Observaciones'] ?? '') ?></td>
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

    </div><!-- /main -->

    <!-- JS al final del body -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="../../backend/js/navbar/sidebar-toggle.js"></script>

    <script>
        $(document).ready(function () {

            // ── DataTables ──
            $('#empadronamientos').DataTable({
                responsive: true,
                deferRender: true,
                pageLength: 25,
                lengthMenu: [10, 25, 50, 100],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },
                order: [[0, 'desc']],
                initComplete: function () {
                    // Hacer aparecer la tabla con fade solo cuando está lista
                    $('.table-container').addClass('dt-ready');
                }
            });

            // ── Modal: abrir ──
            $(document).on('click', '.btn-editar', function () {
                $('#modalEditar').addClass('activo');
                $.ajax({
                    url: '../../backend/php/empa/obtener_empa.php',
                    type: 'GET',
                    data: { id: $(this).data('id') },
                    dataType: 'json',
                    success: function (data) {
                        $('#edit_id').val(data.IdEmpa);
                        $('#edit_tipo_soli').val(data.IdTipoSoli);
                        $('#edit_tipo_remi').val(data.IdTipoRemi);
                        $('#edit_dni').val(data.DNI_Soli);
                        $('#edit_solicitante').val(data.Solicitante);
                        $('#edit_integrantes').val(data.Integrantes);
                        $('#edit_archivador').val(data.Archivador);
                        $('#edit_anio').val(data['AÑO']);
                        $('#edit_tipo_cse').val(data.TipoCSE);
                        $('#edit_empadronador').val(data.Empadronador);
                        $('#edit_observaciones').val(data.Observaciones);
                    },
                    error: function () { alert('Error al obtener los datos del registro'); }
                });
            });

            // ── Modal: cerrar ──
            $(document).on('click', '.cerrar', function () {
                $('#modalEditar').removeClass('activo');
            });
            $(window).on('click', function (e) {
                if ($(e.target).is('#modalEditar')) $('#modalEditar').removeClass('activo');
            });

            // ── Guardar cambios ──
            $('#formEditar').submit(function (e) {
                e.preventDefault();
                $.ajax({
                    url: '../../backend/php/empa/actualizar_empa.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function () {
                        alert('Registro actualizado correctamente');
                        $('#modalEditar').removeClass('activo');
                        location.reload();
                    },
                    error: function () { alert('Error al actualizar el registro'); }
                });
            });

            // ── RENIEC autocomplete ──
            $(document).on('input', '#edit_dni', function () {
                var dni = $(this).val().trim();
                if (dni.length === 8 && /^\d{8}$/.test(dni)) {
                    fetch('../../backend/php/api/api_reniec.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ numdni: dni })
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.status === 'success') {
                            var full = (data.prenombres + ' ' + data.apPrimer + ' ' + data.apSegundo)
                                        .replace(/\s+/g, ' ').trim();
                            $('#edit_solicitante').val(full);
                        }
                    })
                    .catch(err => console.error('RENIEC error:', err));
                }
            });
        });
    </script>

</body>
</html>