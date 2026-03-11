<?php

require_once '../../db/conexion.php';

$id = $_POST['id'];

$stmt = $pdo->prepare("
UPDATE empadronamiento SET
IdTipoSoli=?,
IdTipoRemi=?,
DNI_Soli=?,
Solicitante=?,
Integrantes=?,
Archivador=?,
AÑO=?,
TipoCSE=?,
Empadronador=?,
Observaciones=?
WHERE IdEmpa=?
");

$stmt->execute([
    $_POST['tipo_solicitud'],
    $_POST['tipo_remision'],
    $_POST['dni_solicitante'],
    $_POST['nombre_solicitante'],
    $_POST['num_integrantes'],
    $_POST['num_archivador'],
    $_POST['anio'],
    $_POST['tipo_cse'],
    $_POST['empadronador'],
    $_POST['observaciones'],
    $id
]);
