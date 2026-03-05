<?php
session_start();
require_once '../db/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = trim($_POST['correo'] ?? '');
    $pass = $_POST['pass'] ?? '';

    if (empty($correo) || empty($pass)) {
        die("❌ Por favor completa todos los campos.");
    }

    // Validar formato de correo
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        die("❌ Correo no válido.");
    }

    // Buscar usuario por correo
    $stmt = $pdo->prepare("SELECT IdUsuario, Nombres, Ape_Pat, Ape_Mat, Correo, Pass, IdTipoUsuario, Estado FROM usuarios WHERE Correo = :correo");
    $stmt->execute(['correo' => $correo]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("❌ Usuario no encontrado.");
    }

    // Verificar contraseña
    if (!password_verify($pass, $user['Pass'])) {
        die("❌ Contraseña incorrecta.");
    }

    if ($user['Estado'] != 1) {
        die("❌ Usuario inactivo.");
    }

    // Guardar datos del usuario en sesión
    $_SESSION['user_id'] = $user['IdUsuario'];
    $_SESSION['user_name'] = $user['Nombres'] . ' ' . $user['Ape_Pat'] . ' ' . $user['Ape_Mat'];
    $_SESSION['user_type'] = $user['IdTipoUsuario'];

    // Redirigir al escritorio
    header("Location: ../../frontend/sisvis/escritorio.php");
    exit;
}
