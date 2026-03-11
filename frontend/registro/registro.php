<?php
// frontend/sisvis/escritorio.php
session_start();
require_once __DIR__ . '/../../backend/db/conexion.php';

// --- Proteger ruta: solo usuarios logueados ---
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Gestión de Usuarios - SEM</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" />

    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />

    <!-- CSS personalizado (AL FINAL, para que sobreescriba) -->
    <link rel="stylesheet" href="../../backend/css/navbar/navbar.css" />
    <link rel="stylesheet" href="../../backend/css/sisvis/registro/registro.css" />
    <link rel="stylesheet" href="../../backend/css/sisvis/registro/modales.css" />

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
    <?php include '../navbar/navbar.php'; ?>
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

        <div class="container-fluid p-4">
            <div class="usuarios-alert-container"></div>

            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="material-icons me-2">people</i> Gestión de Usuarios</h4>
                    <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#modalCrearUsuario">
                        <i class="material-icons me-1">person_add</i> Nuevo Usuario
                    </button>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table id="tablaUsuarios" class="table table-hover table-striped w-100">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombres</th>
                                    <th>Apellido Paterno</th>
                                    <th>Apellido Materno</th>
                                    <th>DNI</th>
                                    <th>Correo</th>
                                    <th>Tipo Usuario</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody><!-- Cargado vía JS --></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- ====================== MODALES ====================== -->
        <!-- Crear Usuario -->
        <div class="modal fade" id="modalCrearUsuario" tabindex="-1" data-bs-backdrop="static">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title"><i class="material-icons me-2">person_add</i> Crear Nuevo Usuario</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formCrearUsuario">
                            <div class="mb-3 row">
                                <div class="col-md-6">
                                    <label for="dni" class="form-label">DNI *</label>
                                    <input type="text" id="dni" class="form-control" maxlength="8" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="nombres" class="form-label">Nombres *</label>
                                    <input type="text" id="nombres" class="form-control" required>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <div class="col-md-6">
                                    <label for="ape_pat" class="form-label">Apellido Paterno *</label>
                                    <input type="text" id="ape_pat" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="ape_mat" class="form-label">Apellido Materno *</label>
                                    <input type="text" id="ape_mat" class="form-control" required>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <div class="col-md-6">
                                    <label for="correo" class="form-label">Correo *</label>
                                    <input type="email" id="correo" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="tipo_usuario" class="form-label">Tipo Usuario *</label>
                                    <select id="tipo_usuario" class="form-select" required></select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="pass" class="form-label">Contraseña Temporal *</label>
                                <div class="input-group">
                                    <input type="password" id="pass" class="form-control" required>
                                    <button type="button" class="btn btn-outline-secondary" id="btnGenerarPass">
                                        <i class="material-icons">refresh</i>
                                    </button>
                                </div>
                                <small class="text-muted">O generar automáticamente</small>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal"><i class="material-icons me-1">cancel</i> Cancelar</button>
                        <button class="btn btn-primary" id="btnGuardarUsuario"><i class="material-icons me-1">save</i> Crear Usuario</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Editar Usuario -->
        <div class="modal fade" id="modalEditarUsuario" tabindex="-1" data-bs-backdrop="static">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title"><i class="material-icons me-2">edit</i> Editar Usuario</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formEditarUsuario">
                            <input type="hidden" id="edit_id_usuario">
                            <div class="mb-3 row">
                                <div class="col-md-6">
                                    <label for="edit_nombres" class="form-label">Nombres</label>
                                    <input type="text" id="edit_nombres" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label for="edit_ape_pat" class="form-label">Apellido Paterno</label>
                                    <input type="text" id="edit_ape_pat" class="form-control">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <div class="col-md-6">
                                    <label for="edit_ape_mat" class="form-label">Apellido Materno</label>
                                    <input type="text" id="edit_ape_mat" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label for="edit_dni" class="form-label">DNI</label>
                                    <input type="text" id="edit_dni" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <div class="col-md-6">
                                    <label for="edit_correo" class="form-label">Correo</label>
                                    <input type="email" id="edit_correo" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label for="edit_tipo_usuario" class="form-label">Tipo Usuario</label>
                                    <select id="edit_tipo_usuario" class="form-select"></select>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <div class="col-md-6">
                                    <label for="edit_estado" class="form-label">Estado</label>
                                    <select id="edit_estado" class="form-select">
                                        <option value="1">Activo</option>
                                        <option value="0">Inactivo</option>
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal"><i class="material-icons me-1">cancel</i> Cancelar</button>
                        <button class="btn btn-warning" id="btnActualizarUsuario"><i class="material-icons me-1">update</i> Actualizar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL CAMBIAR CONTRASEÑA -->
    <div class="modal fade" id="modalPasswordUsuario" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-md">
            <div class="modal-content usuarios-modal">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="material-icons me-2">lock_reset</i> Cambiar Contraseña</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formPasswordUsuario">
                        <input type="hidden" id="pass_id_usuario">
                        <div class="usuarios-section">
                            <h6 class="usuarios-section-title"><i class="material-icons">vpn_key</i> Nueva Contraseña</h6>
                            <div class="mb-3"><label>Contraseña Nueva *</label><input type="password" class="form-control" id="nueva_password" required></div>
                            <div class="mb-3"><label>Confirmar Contraseña *</label><input type="password" class="form-control" id="confirmar_password" required></div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="material-icons me-1">cancel</i> Cancelar</button>
                    <button type="button" class="btn btn-success" id="btnGuardarPassword"><i class="material-icons me-1">save</i> Guardar Nueva Contraseña</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ====================== SCRIPTS ====================== -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../backend/js/navbar/sidebar-toggle.js"></script>
    <script src="../../backend/js/usuarios/usuarios.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const dniInput = document.getElementById('dni');
            const nombresInput = document.getElementById('nombres');
            const apePatInput = document.getElementById('ape_pat');
            const apeMatInput = document.getElementById('ape_mat');
            const correoInput = document.getElementById('correo');

            function generarCorreo(primerNombre, primerApellido) {
                const nombre = primerNombre
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .replace(/ñ/gi, 'n');

                const apellido = primerApellido
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .replace(/ñ/gi, 'n');

                return (nombre[0] + apellido).toLowerCase() + '@sem.gob.pe';
            }

            dniInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    buscarReniec();
                }
            });

            dniInput.addEventListener('blur', buscarReniec);

            function buscarReniec() {
                const dni = dniInput.value.trim();
                if (dni.length !== 8 || isNaN(dni)) return;

                fetch('../../backend/php/api/api_reniec.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            numdni: dni
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success') {
                            let fullName = `${data.prenombres}`.replace(/\s+/g, ' ').trim();
                            nombresInput.value = fullName;
                            nombresInput.focus();

                            apePatInput.value = data.apPrimer || '';
                            apeMatInput.value = data.apSegundo || '';

                            if (!correoInput.value.includes('@')) {
                                const primerNombre = data.prenombres.split(' ')[0];
                                const primerApellido = data.apPrimer.split(' ')[0];
                                correoInput.value = generarCorreo(primerNombre, primerApellido);
                            }
                        } else {
                            nombresInput.value = '';
                            apePatInput.value = '';
                            apeMatInput.value = '';
                            correoInput.value = '';
                        }
                    })
                    .catch(err => console.error('Error al consultar RENIEC:', err));
            }
        });
    </script>
</body>

</html>