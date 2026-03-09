<?php

require_once '../../db/conexion.php';

$id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM empadronamiento WHERE IdEmpa = ?");
$stmt->execute([$id]);

echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
