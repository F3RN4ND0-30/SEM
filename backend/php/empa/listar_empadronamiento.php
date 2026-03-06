<?php
require_once '../../backend/db/conexion.php';

// Verificar sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../../frontend/login.php");
    exit;
}

try {
    $stmt = $pdo->query("
        SELECT 
            e.IdEmpa,
            ts.Descripcion AS TipoSolicitud,
            tr.Descripcion AS TipoRemision,
            e.Fecha,
            e.Formato_D100,
            e.S100,
            e.Fecha_S100,
            e.FSU,
            e.Fecha_FSU,
            e.TipoDocu,
            e.DNI_Soli,
            e.Solicitante,
            e.Integrantes,
            e.Archivador,
            e.AÑO,
            e.TipoCSE,
            e.InicioCSE,
            e.FinalCSE,
            e.Empadronador,
            e.Observaciones
        FROM empadronamiento e
        LEFT JOIN tipo_solicitud ts ON e.IdTipoSoli = ts.IdTipoSoli
        LEFT JOIN tipo_remision tr ON e.IdTipoRemi = tr.IdTipoRemi
        ORDER BY e.IdEmpa DESC
    ");
    $empadronamientos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al consultar empadronamientos: " . $e->getMessage());
}
