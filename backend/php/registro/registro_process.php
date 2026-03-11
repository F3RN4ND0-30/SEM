<?php
require_once __DIR__ . '/../../db/conexion.php'; // ✅ ruta absoluta desde este archivo

function validarFormulario($data) {
    $errores = [];

    if (!preg_match('/^[0-9]{8}$/', $data['dni'])) {
        $errores[] = "El DNI debe tener 8 dígitos numéricos";
    }

    if (!filter_var($data['correo'], FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El correo electrónico no es válido";
    }

    if ($data['contrasena'] != $data['confirmar']) {
        $errores[] = "Las contraseñas no coinciden";
    }

    if (strlen($data['contrasena']) < 6) {
        $errores[] = "La contraseña debe tener al menos 6 caracteres";
    }

    return $errores;
}

function verificarUsuarioExistente($pdo, $dni, $correo) {
    $errores = [];

    $stmt = $pdo->prepare("SELECT DNI, Correo FROM usuarios WHERE DNI = :dni OR Correo = :correo");
    $stmt->execute(['dni' => $dni, 'correo' => $correo]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        if ($usuario['DNI'] == $dni) {
            $errores[] = "El DNI ya está registrado";
        }
        if ($usuario['Correo'] == $correo) {
            $errores[] = "El correo electrónico ya está registrado";
        }
    }

    return $errores;
}

function registrarUsuario($pdo, $data) {

    $errores_existencia = verificarUsuarioExistente($pdo, $data['dni'], $data['correo']);
    if (!empty($errores_existencia)) {
        return [
            'exito'   => false,
            'mensaje' => implode("<br>", $errores_existencia)
        ];
    }

    // Obtener IdTipoUsuario
    $stmt = $pdo->prepare("SELECT IdTipoUsuario FROM tipo_usuario WHERE Descripcion = :tipo");
    $stmt->execute(['tipo' => $data['tipo']]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        return [
            'exito'   => false,
            'mensaje' => "Tipo de usuario no válido"
        ];
    }

    $idTipoUsuario = $row['IdTipoUsuario'];

    // Hash de contraseña
    $contrasena_hash = password_hash($data['contrasena'], PASSWORD_DEFAULT);

    // Separar apellido paterno y materno
    $partes = explode(' ', trim($data['apellidos']), 2);
    $ape_pat = $partes[0];
    $ape_mat = $partes[1] ?? '';

    // Insertar usuario
    $stmt = $pdo->prepare("
        INSERT INTO usuarios (Nombres, Ape_Pat, Ape_Mat, DNI, Correo, Pass, IdTipoUsuario, Estado)
        VALUES (:nombres, :ape_pat, :ape_mat, :dni, :correo, :pass, :tipo, 1)
    ");

    $ok = $stmt->execute([
        'nombres' => $data['nombres'],
        'ape_pat' => $ape_pat,
        'ape_mat' => $ape_mat,
        'dni'     => $data['dni'],
        'correo'  => $data['correo'],
        'pass'    => $contrasena_hash,
        'tipo'    => $idTipoUsuario,
    ]);

    if ($ok) {
        return ['exito' => true, 'mensaje' => "Usuario registrado correctamente"];
    } else {
        return ['exito' => false, 'mensaje' => "Error al registrar usuario"];
    }
}