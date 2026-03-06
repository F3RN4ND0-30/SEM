<?php
// --- CONEXIÓN A LA BASE DE DATOS ---
$servername = "localhost";
$username = "root";      // Tu usuario de MySQL
$password = "";          // Tu contraseña de MySQL (vacía en XAMPP)
$dbname = "sem";         // Nombre de tu base de datos

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// --- VARIABLES PARA MENSAJES ---
$mensaje = "";
$tipo_mensaje = ""; // 'exito' o 'error'

// --- PROCESAR EL FORMULARIO ---
if(isset($_POST['registrar'])){

    // Obtener y limpiar datos del formulario
    $dni = trim($_POST['dni']);
    $nombres = trim($_POST['nombres']);
    $apellidos = trim($_POST['apellidos']);
    $correo = trim($_POST['correo']);
    $tipo_descripcion = $_POST['tipo'];
    $contrasena = $_POST['contrasena'];
    $confirmar = $_POST['confirmar'];

    // Validaciones básicas
    $errores = [];
    
    // Validar DNI (8 dígitos)
    if(!preg_match('/^[0-9]{8}$/', $dni)) {
        $errores[] = "El DNI debe tener 8 dígitos numéricos";
    }
    
    // Validar correo
    if(!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El correo electrónico no es válido";
    }
    
    // Validar que las contraseñas coincidan
    if($contrasena != $confirmar){
        $errores[] = "Las contraseñas no coinciden";
    }
    
    // Validar longitud de contraseña (mínimo 6 caracteres)
    if(strlen($contrasena) < 6) {
        $errores[] = "La contraseña debe tener al menos 6 caracteres";
    }

    // Si no hay errores, proceder con el registro
    if(empty($errores)) {
        
        // Verificar si el DNI o correo ya existen
        $sql_verificar = "SELECT DNI, Correo FROM usuarios WHERE DNI = ? OR Correo = ?";
        $stmt_verificar = $conn->prepare($sql_verificar);
        $stmt_verificar->bind_param("ss", $dni, $correo);
        $stmt_verificar->execute();
        $resultado = $stmt_verificar->get_result();
        
        if($resultado->num_rows > 0) {
            $usuario_existente = $resultado->fetch_assoc();
            if($usuario_existente['DNI'] == $dni) {
                $errores[] = "El DNI ya está registrado";
            }
            if($usuario_existente['Correo'] == $correo) {
                $errores[] = "El correo electrónico ya está registrado";
            }
        }
        $stmt_verificar->close();
    }

    // Si aún no hay errores, proceder con la inserción
    if(empty($errores)) {
        
        // Obtener el IdTipoUsuario según la descripción
        $sql_tipo = "SELECT IdTipoUsuario FROM tipo_usuario WHERE Descripcion = ?";
        $stmt_tipo = $conn->prepare($sql_tipo);
        $stmt_tipo->bind_param("s", $tipo_descripcion);
        $stmt_tipo->execute();
        $result_tipo = $stmt_tipo->get_result();
        
        if ($result_tipo->num_rows > 0) {
            $row_tipo = $result_tipo->fetch_assoc();
            $idTipoUsuario = $row_tipo['IdTipoUsuario'];

            // Encriptar la contraseña
            $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);

            // Separar apellidos en paterno y materno
            $partes_apellidos = explode(' ', trim($apellidos), 2);
            $ape_pat = $partes_apellidos[0];
            $ape_mat = isset($partes_apellidos[1]) ? $partes_apellidos[1] : '';

            // Insertar en la tabla usuarios
            $sql_insert = "INSERT INTO usuarios (Nombres, Ape_Pat, Ape_Mat, DNI, Correo, Pass, IdTipoUsuario, Estado) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, 1)";

            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("ssssssi", 
                $nombres, 
                $ape_pat, 
                $ape_mat, 
                $dni, 
                $correo, 
                $contrasena_hash, 
                $idTipoUsuario
            );

            if ($stmt_insert->execute()) {
                $mensaje = "¡Usuario registrado correctamente!";
                $tipo_mensaje = "exito";
                
                // Limpiar variables del formulario (opcional)
                $_POST = array();
                
                // Redireccionar después de 2 segundos al login
                header("refresh:2;url=login.php");
                
            } else {
                $mensaje = "Error al registrar: " . $stmt_insert->error;
                $tipo_mensaje = "error";
            }
            $stmt_insert->close();
            
        } else {
            $errores[] = "Tipo de usuario no válido";
        }
        $stmt_tipo->close();
    }

    // Si hay errores, mostrarlos
    if(!empty($errores)) {
        $mensaje = implode("<br>", $errores);
        $tipo_mensaje = "error";
    }
}

// Cerrar conexión
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario - SEM</title>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;800;900&family=Barlow:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Barlow', sans-serif;
            background: #f5f0eb;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: repeating-linear-gradient(
                -55deg,
                transparent,
                transparent 40px,
                rgba(200,16,46,0.03) 40px,
                rgba(200,16,46,0.03) 41px
            );
            pointer-events: none;
        }

        .top-bar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            display: flex;
        }
        .top-bar div:nth-child(1) { background: #C8102E; flex: 1; }
        .top-bar div:nth-child(2) { background: #f5f0eb; flex: 1; }
        .top-bar div:nth-child(3) { background: #C8102E; flex: 1; }

        .wrapper {
            width: 100%;
            max-width: 450px;
            animation: fadeUp 0.55s cubic-bezier(0.22,1,0.36,1) both;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(28px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            justify-content: center;
            margin-bottom: 28px;
        }

        .brand-flag {
            display: flex;
            border-radius: 3px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            height: 26px;
        }
        .brand-flag div { width: 10px; }
        .brand-flag div:nth-child(1),
        .brand-flag div:nth-child(3) { background: #C8102E; }
        .brand-flag div:nth-child(2) { background: white; }

        .brand-name {
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 13px;
            font-weight: 800;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: #1a3a6b;
        }

        .card {
            background: white;
            border-radius: 18px;
            padding: 36px 32px 32px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            border-top: 3px solid #C8102E;
        }

        .card-title {
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 30px;
            font-weight: 900;
            color: #1a1a2e;
            text-transform: uppercase;
            letter-spacing: 0.02em;
            line-height: 1;
            margin-bottom: 4px;
            text-align: center;
        }
        .card-title span { color: #C8102E; }

        .card-sub {
            font-size: 13px;
            color: #999;
            margin-bottom: 26px;
            text-align: center;
        }

        .mensaje {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 500;
            font-size: 14px;
        }
        
        .mensaje.exito {
            background-color: #e6f7e6;
            color: #2e7d32;
            border: 1px solid #a5d6a5;
        }
        
        .mensaje.error {
            background-color: #ffebee;
            color: #c62828;
            border: 1px solid #ef9a9a;
        }

        .field {
            margin-bottom: 16px;
        }

        label {
            display: block;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #1a3a6b;
            margin-bottom: 7px;
        }

        .input-wrap {
            position: relative;
        }

        .icon {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            color: #ccc;
            display: flex;
            align-items: center;
            pointer-events: none;
        }

        input, select {
            width: 100%;
            background: #fafafa;
            border: 1.5px solid #ececec;
            border-radius: 10px;
            padding: 11px 13px 11px 38px;
            font-family: 'Barlow', sans-serif;
            font-size: 14px;
            color: #1a1a2e;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        }

        input::placeholder {
            color: #ccc;
        }

        input:focus, select:focus {
            border-color: #C8102E;
            background: white;
            box-shadow: 0 0 0 3px rgba(200,16,46,0.08);
        }

        select {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23ccc' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 13px center;
            background-size: 14px;
        }

        .toggle-btn {
            position: absolute;
            right: 11px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #ccc;
            cursor: pointer;
            display: flex;
            align-items: center;
            padding: 4px;
            transition: color 0.2s;
        }

        .toggle-btn:hover {
            color: #888;
        }

        .btn {
            width: 100%;
            padding: 13px;
            background: #C8102E;
            color: white;
            border: none;
            border-radius: 10px;
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 16px;
            font-weight: 800;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
            box-shadow: 0 4px 14px rgba(200,16,46,0.28);
            margin-top: 10px;
        }

        .btn:hover {
            background: #a50d25;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(200,16,46,0.38);
        }

        .btn:active {
            transform: translateY(0);
        }

        .foot-note {
            text-align: center;
            font-size: 13px;
            color: #888;
            margin-top: 20px;
        }

        .foot-note a {
            color: #C8102E;
            font-weight: 600;
            text-decoration: none;
        }

        .foot-note a:hover {
            text-decoration: underline;
        }

        .status-row {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 6px;
            margin-top: 18px;
            font-size: 11px;
            color: #bbb;
        }

        .status-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #22c55e;
        }

        hr {
            border: none;
            height: 1px;
            background: #271919;
            margin-bottom: 22px;
        }
    </style>
</head>
<body>

<div class="top-bar">
    <div></div>
    <div></div>
    <div></div>
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

        <?php if($mensaje != ""): ?>
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
                           value="<?php echo isset($_POST['dni']) ? htmlspecialchars($_POST['dni']) : ''; ?>" required>
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
                           value="<?php echo isset($_POST['nombres']) ? htmlspecialchars($_POST['nombres']) : ''; ?>" required>
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
                           value="<?php echo isset($_POST['apellidos']) ? htmlspecialchars($_POST['apellidos']) : ''; ?>" required>
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
                           value="<?php echo isset($_POST['correo']) ? htmlspecialchars($_POST['correo']) : ''; ?>" required>
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
                        <option value="Administrador" <?php echo (isset($_POST['tipo']) && $_POST['tipo'] == 'Administrador') ? 'selected' : ''; ?>>Administrador</option>
                        <option value="Empadronador" <?php echo (isset($_POST['tipo']) && $_POST['tipo'] == 'Empadronador') ? 'selected' : ''; ?>>Empadronador</option>
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
            ¿Ya tienes una cuenta? <a href="login.php">Inicia sesión aquí</a>
        </div>
    </div>

    <div class="status-row">
        <span class="status-dot"></span> Conexión segura
    </div>
</div>

<script>
function togglePassword(inputId, btn) {
    const input = document.getElementById(inputId);
    const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
    input.setAttribute('type', type);
    
    const svg = btn.querySelector('svg');
    if (type === 'text') {
        svg.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/>';
    } else {
        svg.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
    }
}

// Validación en tiempo real de la contraseña (opcional)
document.getElementById('confirmar').addEventListener('input', function(e) {
    const contrasena = document.getElementById('contrasena').value;
    const confirmar = this.value;
    
    if (confirmar.length > 0) {
        if (contrasena === confirmar) {
            this.style.borderColor = '#22c55e';
        } else {
            this.style.borderColor = '#c62828';
        }
    } else {
        this.style.borderColor = '#ececec';
    }
});
</script>

</body>
</html>