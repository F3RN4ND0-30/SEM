<?php
// backend/php/login_process.php
session_start();
require_once __DIR__ . '/../db/conexion.php'; // ✅ ruta absoluta desde este archivo

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $correo = trim($_POST['correo'] ?? '');
    $pass   = $_POST['pass'] ?? '';

    if (empty($correo) || empty($pass)) {
        header("Location: ../../frontend/login.php?error=campos"); // ✅ ruta corregida
        exit;
    }

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../../frontend/login.php?error=correo");
        exit;
    }

    $stmt = $pdo->prepare("
        SELECT IdUsuario, Nombres, Ape_Pat, Ape_Mat, Correo, Pass, IdTipoUsuario, Estado
        FROM usuarios
        WHERE Correo = :correo
    ");
    $stmt->execute(['correo' => $correo]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        header("Location: ../../frontend/login.php?error=usuario");
        exit;
    }

    if (!password_verify($pass, $user['Pass'])) {
        header("Location: ../../frontend/login.php?error=pass");
        exit;
    }

    if ($user['Estado'] != 1) {
        header("Location: ../../frontend/login.php?error=inactivo");
        exit;
    }

    // Seguridad: regenerar ID de sesión
    session_regenerate_id(true);

    // Guardar datos en sesión
    $_SESSION['user_id']    = $user['IdUsuario'];
    $_SESSION['user_name']  = $user['Nombres'] . ' ' . $user['Ape_Pat'] . ' ' . $user['Ape_Mat'];
    $_SESSION['user_type']  = $user['IdTipoUsuario'];
    $_SESSION['user_email'] = $user['Correo'];
    $_SESSION['login_time'] = time();

    header("Location: ../../frontend/sisvis/escritorio.php"); // ✅ ruta corregida
    exit;
}