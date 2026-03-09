<?php
require_once '../../db/conexion.php';
$conn = $pdo; 
header('Content-Type: application/json; charset=utf-8');

$action = $_GET['action'] ?? '';
$input  = json_decode(file_get_contents("php://input"), true);

switch ($action) {
    case 'listar':
        listarUsuarios($conn);
        break;
    case 'tipos':
        listarTiposUsuario($conn);
        break;
    case 'crear':
        crearUsuario($conn, $input);
        break;
    case 'obtener':
        obtenerUsuario($conn, $_GET['id'] ?? 0);
        break;
    case 'editar':
        editarUsuario($conn, $input);
        break;
    case 'toggle':
        toggleUsuario($conn, $input);
        break;
    case 'password':
        cambiarPassword($conn, $input);
        break;
    default:
        echo json_encode(["status" => "error", "message" => "Acción no válida"]);
        break;
}

// Listar todos los usuarios con su tipo
function listarUsuarios($conn)
{
    try {
        $sql = "SELECT u.IdUsuario, u.Nombres, u.Ape_Pat, u.Ape_Mat, u.DNI, u.Correo, u.Estado,
                       t.Descripcion AS tipo_nombre
                FROM usuarios u
                LEFT JOIN tipo_usuario t ON u.IdTipoUsuario = t.IdTipoUsuario
                ORDER BY u.IdUsuario DESC";
        $res = $conn->query($sql);
        $usuarios = $res->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(["data" => $usuarios]);
    } catch (PDOException $e) {
        echo json_encode(["data" => [], "error" => $e->getMessage()]);
    }
}

// Listar tipos de usuario activos
function listarTiposUsuario($conn)
{
    try {
        $sql = "SELECT IdTipoUsuario, Descripcion FROM tipo_usuario WHERE Estado = 1 ORDER BY Descripcion";
        $res = $conn->query($sql);
        echo json_encode(["status" => "success", "data" => $res->fetchAll(PDO::FETCH_ASSOC)]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
}

// Crear nuevo usuario
function crearUsuario($conn, $data)
{
    if (!isset($data['Nombres'], $data['Ape_Pat'], $data['Ape_Mat'], $data['DNI'], $data['Correo'], $data['Pass'], $data['IdTipoUsuario'])) {
        echo json_encode(["status" => "error", "message" => "Datos incompletos"]);
        return;
    }

    try {
        $hash = password_hash($data['Pass'], PASSWORD_BCRYPT);
        $stmt = $conn->prepare("INSERT INTO usuarios 
            (Nombres, Ape_Pat, Ape_Mat, DNI, Correo, Pass, IdTipoUsuario, Estado)
            VALUES (?, ?, ?, ?, ?, ?, ?, 1)");
        $stmt->execute([
            $data['Nombres'],
            $data['Ape_Pat'],
            $data['Ape_Mat'],
            $data['DNI'],
            $data['Correo'],
            $hash,
            $data['IdTipoUsuario']
        ]);
        echo json_encode(["status" => "success", "message" => "Usuario creado correctamente"]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
}

// Obtener usuario específico
function obtenerUsuario($conn, $id)
{
    try {
        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE IdUsuario = ?");
        $stmt->execute([$id]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($usuario) echo json_encode(["status" => "success", "data" => $usuario]);
        else echo json_encode(["status" => "error", "message" => "Usuario no encontrado"]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
}

// Editar usuario (datos y tipo)
function editarUsuario($conn, $data)
{
    if (!isset($data['IdUsuario'], $data['Nombres'], $data['Ape_Pat'], $data['Ape_Mat'], $data['Correo'], $data['IdTipoUsuario'], $data['Estado'])) {
        echo json_encode(["status" => "error", "message" => "Datos incompletos"]);
        return;
    }
    try {
        $stmt = $conn->prepare("UPDATE usuarios SET Nombres = ?, Ape_Pat = ?, Ape_Mat = ?, Correo = ?, IdTipoUsuario = ?, Estado = ? WHERE IdUsuario = ?");
        $stmt->execute([
            $data['Nombres'],
            $data['Ape_Pat'],
            $data['Ape_Mat'],
            $data['Correo'],
            $data['IdTipoUsuario'],
            $data['Estado'],
            $data['IdUsuario']
        ]);
        echo json_encode(["status" => "success", "message" => "Usuario actualizado"]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
}

// Activar / desactivar usuario
function toggleUsuario($conn, $data)
{
    if (!isset($data['IdUsuario'], $data['Estado'])) {
        echo json_encode(["status" => "error", "message" => "Datos incompletos"]);
        return;
    }

    try {
        $stmt = $conn->prepare("UPDATE usuarios SET Estado = ? WHERE IdUsuario = ?");
        $stmt->execute([$data['Estado'], $data['IdUsuario']]);
        echo json_encode(["status" => "success", "message" => "Estado actualizado"]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
}

// Cambiar contraseña
function cambiarPassword($conn, $data)
{
    if (!isset($data['id_usuario'], $data['nueva'])) {
        echo json_encode(["status" => "error", "message" => "Datos incompletos"]);
        return;
    }

    try {
        $hash = password_hash($data['nueva'], PASSWORD_BCRYPT);
        $stmt = $conn->prepare("UPDATE usuarios SET Pass = ? WHERE IdUsuario = ?");
        $stmt->execute([$hash, $data['id_usuario']]);
        echo json_encode(["status" => "success", "message" => "Contraseña actualizada"]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
}
