<?php
session_start();
require_once __DIR__ . '/../../backend/db/conexion.php';

// --- Proteger ruta ---
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$id = $_SESSION['user_id'];

// Obtener datos del usuario
$stmt = $pdo->prepare("
    SELECT u.Nombres, u.Ape_Pat, u.Ape_Mat, u.DNI, u.Correo, t.Descripcion AS tipo
    FROM usuarios u
    LEFT JOIN tipo_usuario t ON u.IdTipoUsuario = t.IdTipoUsuario
    WHERE u.IdUsuario = ?
");
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$userName = $user['Nombres'] ?? 'Usuario';
$userInitial = strtoupper(substr($userName, 0, 1));
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Mi Cuenta - SEM</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <link rel="stylesheet" href="../../backend/css/config/config.css">
    <link rel="stylesheet" href="../../backend/css/navbar/navbar.css">

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
        <header class="topbar">
            <button id="toggleSidebar" class="topbar-toggle">☰</button>
            <div class="topbar-title">Mi <span>Perfil</span></div>
            <div class="topbar-right">
                <span class="badge-tag">En vivo</span>
                <div class="user-chip">
                    <div class="user-avatar"><?= htmlspecialchars($userInitial) ?></div>
                    <?= htmlspecialchars($userName) ?>
                </div>
            </div>
        </header>

        <div class="container">

            <div class="card cuenta-card shadow">

                <div class="card-body">

                    <div class="text-center mb-4">
                        <div class="cuenta-avatar"><?= $userInitial ?></div>
                        <h4 class="mt-2"><?= $user['Nombres'] . " " . $user['Ape_Pat'] . " " . $user['Ape_Mat'] ?></h4>
                        <p class="text-muted"><?= $user['tipo'] ?></p>
                    </div>

                    <hr>

                    <h5 class="mb-3"><i class="material-icons me-2">person</i> Información de Cuenta</h5>

                    <div class="row mb-2">
                        <div class="col-md-6">
                            <strong>DNI:</strong> <?= $user['DNI'] ?>
                        </div>

                        <div class="col-md-6">
                            <strong>Correo:</strong> <?= $user['Correo'] ?>
                        </div>
                    </div>

                    <hr>

                    <h5 class="mt-4 mb-3"><i class="material-icons me-2">lock</i> Cambiar Contraseña</h5>

                    <form id="formCambiarPassword">

                        <div class="mb-3">
                            <label>Nueva contraseña</label>
                            <input type="password" class="form-control" id="nueva_password" required>
                        </div>

                        <div class="mb-3">
                            <label>Confirmar contraseña</label>
                            <input type="password" class="form-control" id="confirmar_password" required>
                        </div>

                        <button type="button" class="btn btn-primary" id="btnCambiarPassword">
                            <i class="material-icons me-1">save</i>
                            Cambiar contraseña
                        </button>

                    </form>

                    <div id="alertaCuenta" class="mt-3"></div>

                </div>
            </div>

        </div>

    </div>

    <script src="../../backend/js/navbar/sidebar-toggle.js"></script>

    <script>
        const API = "../../backend/php/usuarios/fcs_usuarios.php";

        document.getElementById("btnCambiarPassword").addEventListener("click", () => {

            const nueva = document.getElementById("nueva_password").value;
            const confirmar = document.getElementById("confirmar_password").value;

            if (!nueva || !confirmar) {
                mostrarAlerta("Complete todos los campos", "danger");
                return;
            }

            if (nueva !== confirmar) {
                mostrarAlerta("Las contraseñas no coinciden", "danger");
                return;
            }

            fetch(API + "?action=password", {

                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },

                    body: JSON.stringify({
                        id_usuario: <?= $id ?>,
                        nueva: nueva
                    })

                })
                .then(r => r.json())
                .then(res => {

                    if (res.status === "success") {

                        mostrarAlerta("Contraseña actualizada correctamente", "success");

                        document.getElementById("formCambiarPassword").reset();

                    } else {

                        mostrarAlerta(res.message, "danger");

                    }

                });

        });

        function mostrarAlerta(msg, tipo) {

            document.getElementById("alertaCuenta").innerHTML =
                `<div class="alert alert-${tipo}">${msg}</div>`;

            setTimeout(() => {
                document.getElementById("alertaCuenta").innerHTML = "";
            }, 4000);

        }
    </script>

</body>

</html>