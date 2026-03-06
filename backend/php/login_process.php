<?php
session_start();
require_once '../db/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $correo = trim($_POST['correo'] ?? '');
    $pass = $_POST['pass'] ?? '';

    if (empty($correo) || empty($pass)) {
        header("Location: ../../login.php?error=campos");
        exit;
    }

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../../login.php?error=correo");
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
        header("Location: ../../login.php?error=usuario");
        exit;
    }

    if (!password_verify($pass, $user['Pass'])) {
        header("Location: ../../login.php?error=pass");
        exit;
    }

    if ($user['Estado'] != 1) {
        header("Location: ../../login.php?error=inactivo");
        exit;
    }

    // seguridad
    session_regenerate_id(true);

    // guardar sesión
    $_SESSION['user_id'] = $user['IdUsuario'];
    $_SESSION['user_name'] = $user['Nombres'] . ' ' . $user['Ape_Pat'] . ' ' . $user['Ape_Mat'];
    $_SESSION['user_type'] = $user['IdTipoUsuario'];
    $_SESSION['user_email'] = $user['Correo'];
    $_SESSION['login_time'] = time();

    header("Location: ../../frontend/sisvis/escritorio.php");
    exit;
}
