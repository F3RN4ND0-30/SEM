<?php
require_once 'db/conexion.php';

$correo = "fllerena@sem.gob.pe";
$pass = "123456";
$hash = password_hash($pass, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("INSERT INTO usuarios (Nombres, Ape_Pat, Ape_Mat, DNI, Correo, Pass, IdTipoUsuario, Estado) VALUES (:n, :ap, :am, :dni, :correo, :pass, :tipo, :estado)");
$stmt->execute([
    'n' => 'FERNANDO JOSE',
    'ap' => 'LLERENA',
    'am' => 'MENDOZA',
    'dni' => '71131433',
    'correo' => $correo,
    'pass' => $hash,
    'tipo' => 1,
    'estado' => 1
]);

echo "Usuario creado ✅";
