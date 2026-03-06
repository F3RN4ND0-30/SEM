<?php
// frontend/registro/registro.php
session_start();
require_once __DIR__ . '/../../backend/php/registro/registro_process.php'; // ✅ ruta absoluta
// $pdo ya está disponible desde registro_process.php -> conexion.php

$mensaje     = "";
$tipo_mensaje = "";
$form_data   = [];

if (isset($_POST['registrar'])) {

    $form_data = [
        'dni'        => trim($_POST['dni']),
        'nombres'    => trim($_POST['nombres']),
        'apellidos'  => trim($_POST['apellidos']),
        'correo'     => trim($_POST['correo']),
        'tipo'       => $_POST['tipo'],
        'contrasena' => $_POST['contrasena'],
        'confirmar'  => $_POST['confirmar']
    ];

    $errores = validarFormulario($form_data);

    if (empty($errores)) {
        $resultado = registrarUsuario($pdo, $form_data); // ✅ usa $pdo (no $conn)

        if ($resultado['exito']) {
            $mensaje      = "¡Usuario registrado correctamente!";
            $tipo_mensaje = "exito";
            $form_data    = [];
            header("refresh:2;url=../../frontend/login.php"); // ✅ ruta corregida
        } else {
            $mensaje      = $resultado['mensaje'];
            $tipo_mensaje = "error";
        }
    } else {
        $mensaje      = implode("<br>", $errores);
        $tipo_mensaje = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario - SEM</title>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;800;900&family=Barlow:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../backend/css/sisvis/registro/registro.css"> <!-- ✅ ruta corregida -->
</head>
<body>

    <div class="top-bar">
        <div></div><div></div><div></div>
    </div>

    <div class="wrapper">
        <div class="brand">
            <div class="brand-flag">
                <div></div><div></div><div></div>
            </div>
            <span class="brand-name">Sistema Nacional</span>
        </div>

        <div class="card">
            <div class="card-title">Registro de <span>Usuario</span></div>
            <div class="card-sub">Completa todos los campos para registrarte</div>
            <hr/>

            <?php if ($mensaje != ""): ?>
                <div class="mensaje <?php echo $tipo_mensaje; ?>">
                    <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">

                <div class="field">
                    <label for="dni">DNI</label>
                    <div class="input-wrap">
                        <span class="icon">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="3" width="18" height="18" rx="2"/>
                                <line x1="9" y1="9" x2="15" y2="15"/>
                                <line x1="15" y1="9" x2="9" y2="15"/>
                            </svg>
                        </span>
                        <input type="text" id="dni" name="dni" placeholder="12345678" maxlength="8"
                               value="<?php echo isset($form_data['dni']) ? htmlspecialchars($form_data['dni']) : ''; ?>" required>
                    </div>
                </div>

                <div class="field">
                    <label for="nombres">Nombres</label>
                    <div class="input-wrap">
                        <span class="icon">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                        </span>
                        <input type="text" id="nombres" name="nombres" placeholder="Juan Carlos"
                               value="<?php echo isset($form_data['nombres']) ? htmlspecialchars($form_data['nombres']) : ''; ?>" required>
                    </div>
                </div>

                <div class="field">
                    <label for="apellidos">Apellidos</label>
                    <div class="input-wrap">
                        <span class="icon">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                        </span>
                        <input type="text" id="apellidos" name="apellidos" placeholder="Pérez García"
                               value="<?php echo isset($form_data['apellidos']) ? htmlspecialchars($form_data['apellidos']) : ''; ?>" required>
                    </div>
                </div>

                <div class="field">
                    <label for="correo">Correo Electrónico</label>
                    <div class="input-wrap">
                        <span class="icon">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                <polyline points="22,6 12,13 2,6"/>
                            </svg>
                        </span>
                        <input type="email" id="correo" name="correo" placeholder="correo@ejemplo.com"
                               value="<?php echo isset($form_data['correo']) ? htmlspecialchars($form_data['correo']) : ''; ?>" required>
                    </div>
                </div>

                <div class="field">
                    <label for="tipo">Tipo de Cuenta</label>
                    <div class="input-wrap">
                        <span class="icon">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4z"/>
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            </svg>
                        </span>
                        <select id="tipo" name="tipo" required>
                            <option value="">Seleccione tipo de cuenta</option>
                            <option value="Administrador" <?php echo (isset($form_data['tipo']) && $form_data['tipo'] == 'Administrador') ? 'selected' : ''; ?>>Administrador</option>
                            <option value="Empadronador"  <?php echo (isset($form_data['tipo']) && $form_data['tipo'] == 'Empadronador')  ? 'selected' : ''; ?>>Empadronador</option>
                        </select>
                    </div>
                </div>

                <div class="field">
                    <label for="contrasena">Contraseña</label>
                    <div class="input-wrap">
                        <span class="icon">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2"/>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                        </span>
                        <input type="password" id="contrasena" name="contrasena" placeholder="•••••••• (mínimo 6 caracteres)" required>
                        <button type="button" class="toggle-btn" onclick="togglePassword('contrasena', this)">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="field">
                    <label for="confirmar">Confirmar Contraseña</label>
                    <div class="input-wrap">
                        <span class="icon">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2"/>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                        </span>
                        <input type="password" id="confirmar" name="confirmar" placeholder="••••••••" required>
                        <button type="button" class="toggle-btn" onclick="togglePassword('confirmar', this)">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit" name="registrar" class="btn">
                    Registrar Usuario
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <line x1="5" y1="12" x2="19" y2="12"/>
                        <polyline points="12 5 19 12 12 19"/>
                    </svg>
                </button>
            </form>

            <div class="foot-note">
                ¿Ya tienes una cuenta? <a href="../../frontend/login.php">Inicia sesión aquí</a> <!-- ✅ ruta corregida -->
            </div>
        </div>

        <div class="status-row">
            <span class="status-dot"></span> Conexión segura
        </div>
    </div>

    <script>
        function togglePassword(id, btn) {
            const input = document.getElementById(id);
            const isPass = input.type === 'password';
            input.type = isPass ? 'text' : 'password';
            btn.querySelector('svg').innerHTML = isPass
                ? `<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/>`
                : `<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>`;
        }
    </script>

</body>
</html>