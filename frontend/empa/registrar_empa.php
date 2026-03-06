<?php
require_once "../../frontend/auth.php";
require_once '../../backend/db/conexion.php';

// Traer opciones de la DB
$tipoSolicitudes = $pdo->query("SELECT IdTipoSoli, Descripcion FROM tipo_solicitud")->fetchAll(PDO::FETCH_ASSOC);
$tipoRemisiones = $pdo->query("SELECT IdTipoRemi, Descripcion FROM tipo_remision")->fetchAll(PDO::FETCH_ASSOC);
$empadronadores = $pdo->query("SELECT IdUsuario, Nombres, Ape_Pat, Ape_Mat FROM usuarios WHERE IdTipoUsuario = 2")->fetchAll(PDO::FETCH_ASSOC);
$anioActual = date('Y');
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Registrar Empadronamiento</title>
    <link rel="stylesheet" href="../../backend/css/navbar/navbar.css" />
    <link rel="stylesheet" href="../../backend/css/empa/registrar_empa.css" />
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>

<body>
    <?php include "../navbar/navbar.php"; ?>
    <div class="form-container">
        <h2>Registrar Empadronamiento</h2>
        <form action="../../backend/php/empa/guardar_empa.php" method="POST">

            <!-- Tipo de Solicitud -->
            <div class="form-group">
                <label>Tipo de Solicitud</label>
                <select name="tipo_solicitud" required>
                    <option value="">Seleccione</option>
                    <?php foreach ($tipoSolicitudes as $ts): ?>
                        <option value="<?= $ts['IdTipoSoli'] ?>"><?= $ts['Descripcion'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Tipo de Remisión -->
            <div class="form-group">
                <label>Tipo de Remisión</label>
                <select name="tipo_remision" required>
                    <option value="">Seleccione</option>
                    <?php foreach ($tipoRemisiones as $tr): ?>
                        <option value="<?= $tr['IdTipoRemi'] ?>"><?= $tr['Descripcion'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Formatos y fechas -->
            <div class="form-group">
                <label>Formato D100</label>
                <input type="number" name="d100" required>
            </div>

            <div class="form-group">
                <label>S100</label>
                <input type="number" name="s100" required>
            </div>

            <div class="form-group">
                <label>Fecha S100</label>
                <input type="text" class="datepicker" name="fecha_s100" required>
            </div>

            <div class="form-group">
                <label>FSU</label>
                <input type="number" name="fsu" required>
            </div>

            <div class="form-group">
                <label>Fecha FSU</label>
                <input type="text" class="datepicker" name="fecha_fsu" required>
            </div>

            <!-- Tipo documento -->
            <div class="form-group">
                <label>Tipo Documento</label>
                <select name="tipo_doc" required>
                    <option value="">Seleccione</option>
                    <option value="DNI">DNI</option>
                    <option value="CE">CE</option>
                </select>
            </div>

            <!-- Datos solicitante -->
            <div class="form-group">
                <label>DNI del Solicitante</label>
                <input type="number" name="dni_solicitante" required>
            </div>

            <div class="form-group">
                <label>Nombre del Solicitante</label>
                <input type="text" name="nombre_solicitante" required>
            </div>

            <div class="form-group">
                <label>Número de Integrantes</label>
                <input type="number" name="num_integrantes" required>
            </div>

            <div class="form-group">
                <label>Número de Archivador</label>
                <input type="number" name="num_archivador" required>
            </div>

            <div class="form-group">
                <label>Año</label>
                <input type="number" name="anio" value="<?= $anioActual ?>" required>
            </div>

            <!-- Tipo de CSE -->
            <div class="form-group">
                <label>Tipo de CSE</label>
                <select name="tipo_cse" required>
                    <option value="">Seleccione</option>
                    <option value="NO POBRE">NO POBRE</option>
                    <option value="POBRE">POBRE</option>
                    <option value="POBRE EXTREMO">POBRE EXTREMO</option>
                </select>
            </div>

            <div class="form-group">
                <label>Fecha Inicio CSE</label>
                <input type="text" class="datepicker" name="fecha_inicio_cse" required>
            </div>

            <div class="form-group">
                <label>Fecha Fin CSE</label>
                <input type="text" class="datepicker" name="fecha_fin_cse" required>
            </div>

            <!-- Empadronador -->
            <div class="form-group">
                <label>Empadronador</label>
                <select name="empadronador" required>
                    <option value="">Seleccione</option>
                    <?php foreach ($empadronadores as $emp): ?>
                        <option value="<?= $emp['Nombres'] ?> <?= $emp['Ape_Pat'] ?> <?= $emp['Ape_Mat'] ?>">
                            <?= $emp['Nombres'] ?> <?= $emp['Ape_Pat'] ?> <?= $emp['Ape_Mat'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Observaciones -->
            <div class="form-group">
                <label>Observaciones</label>
                <textarea name="observaciones" rows="3"></textarea>
            </div>

            <button type="submit" class="btn-submit">Registrar</button>
        </form>
    </div>

    <script>
        flatpickr(".datepicker", {
            dateFormat: "Y-m-d"
        });
    </script>

</body>

</html>