<?php
session_start();
require_once '../../db/conexion.php';

// Verificar sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../../frontend/login.php");
    exit;
}

// Validar que sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../../frontend/empa/registrar_empa.php");
    exit;
}

// Recibir y limpiar datos
$tipo_soli       = $_POST['tipo_solicitud'] ?? null;
$tipo_remi       = $_POST['tipo_remision'] ?? null;
$d100            = !empty($_POST['d100']) ? (int)$_POST['d100'] : null;
$s100            = !empty($_POST['s100']) ? (int)$_POST['s100'] : null;
$fecha_s100      = !empty($_POST['fecha_s100']) ? $_POST['fecha_s100'] : null;
$fsu             = !empty($_POST['fsu']) ? (int)$_POST['fsu'] : null;
$fecha_fsu       = !empty($_POST['fecha_fsu']) ? $_POST['fecha_fsu'] : null;
$tipo_docu       = $_POST['tipo_doc'] ?? null;
$dni_soli        = !empty($_POST['dni_solicitante']) ? (int)$_POST['dni_solicitante'] : null;
$solicitante     = $_POST['nombre_solicitante'] ?? null;
$integrantes     = !empty($_POST['num_integrantes']) ? (int)$_POST['num_integrantes'] : null;
$archivador      = !empty($_POST['num_archivador']) ? (int)$_POST['num_archivador'] : null;
$anio            = !empty($_POST['anio']) ? (int)$_POST['anio'] : null;
$tipo_cse        = $_POST['tipo_cse'] ?? null;
$inicio_cse      = !empty($_POST['fecha_inicio_cse']) ? $_POST['fecha_inicio_cse'] : null;
$final_cse       = !empty($_POST['fecha_fin_cse']) ? $_POST['fecha_fin_cse'] : null;
$empadronador = $_POST['empadronador'] ?? '';
$observaciones   = $_POST['observaciones'] ?? null;

// Validación ENUM
$valid_docs = ['DNI', 'CE'];
$valid_cse  = ['NO POBRE', 'POBRE', 'POBRE EXTREMO'];

if (!in_array($tipo_docu, $valid_docs)) {
    die("❌ Tipo de documento inválido.");
}
if (!in_array($tipo_cse, $valid_cse)) {
    die("❌ Tipo de CSE inválido.");
}

// Campos obligatorios
if (
    trim($tipo_soli) === '' ||
    trim($tipo_remi) === '' ||
    $dni_soli === null ||
    trim($solicitante) === ''
) {
    die("❌ Faltan campos obligatorios.");
}

try {
    $stmt = $pdo->prepare("
        INSERT INTO empadronamiento (
            IdTipoSoli,
            IdTipoRemi,
            Fecha,
            Formato_D100,
            S100,
            Fecha_S100,
            FSU,
            Fecha_FSU,
            TipoDocu,
            DNI_Soli,
            Solicitante,
            Integrantes,
            Archivador,
            AÑO,
            TipoCSE,
            InicioCSE,
            FinalCSE,
            Empadronador,
            Observaciones
        ) VALUES (
            :tipo_soli,
            :tipo_remi,
            NOW(),
            :d100,
            :s100,
            :fecha_s100,
            :fsu,
            :fecha_fsu,
            :tipo_docu,
            :dni_soli,
            :solicitante,
            :integrantes,
            :archivador,
            :anio,
            :tipo_cse,
            :inicio_cse,
            :final_cse,
            :empadronador,
            :observaciones
        )
    ");

    $stmt->execute([
        ':tipo_soli'     => $tipo_soli,
        ':tipo_remi'     => $tipo_remi,
        ':d100'          => $d100,
        ':s100'          => $s100,
        ':fecha_s100'    => $fecha_s100,
        ':fsu'           => $fsu,
        ':fecha_fsu'     => $fecha_fsu,
        ':tipo_docu'     => $tipo_docu,
        ':dni_soli'      => $dni_soli,
        ':solicitante'   => $solicitante,
        ':integrantes'   => $integrantes,
        ':archivador'    => $archivador,
        ':anio'          => $anio,
        ':tipo_cse'      => $tipo_cse,
        ':inicio_cse'    => $inicio_cse,
        ':final_cse'     => $final_cse,
        ':empadronador'  => $empadronador,
        ':observaciones' => $observaciones
    ]);

    header("Location: ../../../frontend/empa/registrar_empa.php?success=1");
    exit;
} catch (PDOException $e) {
    die("❌ Error al guardar empadronamiento: " . $e->getMessage());
}
