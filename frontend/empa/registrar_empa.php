<?php
require_once "../../frontend/auth.php";
require_once '../../backend/db/conexion.php';

$tipoSolicitudes = $pdo->query("SELECT IdTipoSoli, Descripcion FROM tipo_solicitud")->fetchAll(PDO::FETCH_ASSOC);
$tipoRemisiones  = $pdo->query("SELECT IdTipoRemi, Descripcion FROM tipo_remision")->fetchAll(PDO::FETCH_ASSOC);
$empadronadores  = $pdo->query("SELECT IdUsuario, Nombres, Ape_Pat, Ape_Mat FROM usuarios WHERE IdTipoUsuario = 2")->fetchAll(PDO::FETCH_ASSOC);
$anioActual      = date('Y');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Registrar Empadronamiento</title>
    <link rel="stylesheet" href="../../backend/css/empa/registrar_empa.css" />
    <link rel="stylesheet" href="../../backend/css/navbar/navbar.css" />
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <link rel="icon" type="image/png" href="../../backend/img/logoPisco.png" />
    <style>
        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 32px;
            border-bottom: 1px solid var(--border);
            background: var(--surface);
            position: sticky;
            top: 0;
            z-index: 5;
        }

        .topbar-title {
            font-family: 'Nunito', sans-serif;
            font-size: 18px;
            font-weight: 700;
        }

        .topbar-title span {
            color: var(--accent);
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .badge-tag {
            font-size: 11px;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 20px;
            background: rgba(200, 16, 46, 0.12);
            color: #ff6b81;
            letter-spacing: .04em;
        }

        .user-chip {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 6px 12px 6px 6px;
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 50px;
            font-size: 12px;
            color: var(--muted);
        }

        .user-avatar {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent), #1a3a6b);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
            color: #fff;
        }

        /* Ocultar sidebar en móviles por defecto */
        @media (max-width: 768px) {

            /* Overlay solo cubre el contenido, no la topbar ni el toggle */
            .sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.4);
                z-index: 1000;
                /* debajo del toggle */
                opacity: 0;
                visibility: hidden;
                transition: opacity 0.3s ease;
                pointer-events: all;
                /* sí bloquea el contenido debajo */
            }

            /* Cuando esté activo */
            .sidebar-overlay.active {
                opacity: 1;
                visibility: visible;
            }

            /* Sidebar encima del overlay */
            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                height: 100%;
                width: var(--sidebar-w);
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                z-index: 1005;
                /* encima del overlay */
            }

            /* Toggle siempre encima de todo */
            .topbar-toggle {
                z-index: 1010;
                /* encima de sidebar y overlay */
                position: relative;
                /* relativo dentro de la topbar */
            }
        }

        /* En escritorio, ocultar botón toggle */
        @media (min-width: 769px) {
            .topbar-toggle {
                display: none;
            }
        }
    </style>
</head>
<body>
    <?php include "../navbar/navbar.php"; ?>

    <div class="main">

        <!-- TOPBAR -->
        <header class="topbar">
            <button id="toggleSidebar" class="topbar-toggle">☰</button>
            <div class="topbar-title">Registro de <span>Empadronamiento</span></div>
            <div class="topbar-right">
                <span class="badge-tag">En vivo</span>
                <div class="user-chip">
                    <div class="user-avatar"><?= htmlspecialchars($userInitial) ?></div>
                    <?= htmlspecialchars($userName) ?>
                </div>
            </div>
        </header>

        <div class="form-container">
            <form action="../../backend/php/empa/guardar_empa.php" method="POST">

                <div class="form-grid">

                    <!-- Tipo Solicitud -->
                    <div class="form-group">
                        <label>Tipo de Solicitud</label>
                        <select name="tipo_solicitud" required>
                            <option value="">Seleccione</option>
                            <?php foreach ($tipoSolicitudes as $ts): ?>
                                <option value="<?= $ts['IdTipoSoli'] ?>"><?= $ts['Descripcion'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Tipo Remision -->
                    <div class="form-group">
                        <label>Tipo de Remisión</label>
                        <select name="tipo_remision" required>
                            <option value="">Seleccione</option>
                            <?php foreach ($tipoRemisiones as $tr): ?>
                                <option value="<?= $tr['IdTipoRemi'] ?>"><?= $tr['Descripcion'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- D100 ancho completo -->
                    <div class="form-group span-2">
                        <label>Formato D100</label>
                        <input type="number" name="d100" required>
                    </div>

                    <!-- S100 -->
                    <div class="form-group">
                        <label>S100</label>
                        <input type="number" name="s100" required>
                    </div>

                    <!-- Fecha S100 -->
                    <div class="form-group">
                        <label>Fecha S100</label>
                        <input type="text" class="datepicker" name="fecha_s100" required>
                    </div>

                    <!-- FSU -->
                    <div class="form-group">
                        <label>FSU</label>
                        <input type="number" name="fsu" required>
                    </div>

                    <!-- Fecha FSU -->
                    <div class="form-group">
                        <label>Fecha FSU</label>
                        <input type="text" class="datepicker" name="fecha_fsu" required>
                    </div>

                    <!-- Tipo Documento -->
                    <div class="form-group">
                        <label>Tipo Documento</label>
                        <select name="tipo_doc" required>
                            <option value="">Seleccione</option>
                            <option value="DNI">DNI</option>
                            <option value="CE">CE</option>
                        </select>
                    </div>

                    <!-- DNI -->
                    <div class="form-group">
                        <label>DNI del Solicitante</label>
                        <input type="number" name="dni_solicitante" required>
                    </div>

                    <!-- Nombre ancho completo -->
                    <div class="form-group span-2">
                        <label>Nombre del Solicitante</label>
                        <input type="text" name="nombre_solicitante" required>
                    </div>

                    <!-- Integrantes -->
                    <div class="form-group">
                        <label>Número de Integrantes</label>
                        <input type="number" name="num_integrantes" required>
                    </div>

                    <!-- Archivador -->
                    <div class="form-group">
                        <label>Número de Archivador</label>
                        <input type="number" name="num_archivador" required>
                    </div>

                    <!-- Año -->
                    <div class="form-group">
                        <label>Año</label>
                        <input type="number" name="anio" value="<?= $anioActual ?>" required>
                    </div>

                    <!-- Tipo CSE -->
                    <div class="form-group">
                        <label>Tipo de CSE</label>
                        <select name="tipo_cse" required>
                            <option value="">Seleccione</option>
                            <option value="NO POBRE">NO POBRE</option>
                            <option value="POBRE">POBRE</option>
                            <option value="POBRE EXTREMO">POBRE EXTREMO</option>
                        </select>
                    </div>

                    <!-- Fecha inicio -->
                    <div class="form-group">
                        <label>Fecha Inicio CSE</label>
                        <input type="text" class="datepicker" name="fecha_inicio_cse" required>
                    </div>

                    <!-- Fecha fin -->
                    <div class="form-group">
                        <label>Fecha Fin CSE</label>
                        <input type="text" class="datepicker" name="fecha_fin_cse" required>
                    </div>

                    <!-- Empadronador -->
                    <div class="form-group span-2">
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

                    <!-- Observaciones ancho completo -->
                    <div class="form-group span-2">
                        <label>Observaciones</label>
                        <textarea name="observaciones" rows="3"></textarea>
                    </div>

                </div>

                <button type="submit" class="btn-submit">Registrar</button>

            </form>
        </div>
    </div>

    <script src="../../backend/js/navbar/sidebar-toggle.js"></script>

<script>
// Flatpickr
flatpickr(".datepicker", {
    dateFormat: "Y-m-d",
    locale: { firstDayOfWeek: 1 }
});

// Limitar dígitos
[['d100',7],['s100',8],['fsu',8],['dni_solicitante',8]].forEach(([name, max]) => {
    document.querySelector(`input[name="${name}"]`)?.addEventListener('input', function() {
        if (this.value.length > max) this.value = this.value.slice(0, max);
    });
});

// RENIEC autocomplete
document.getElementById('dniInput')?.addEventListener('input', function () {
    const dni = this.value.trim();
    const nombreInput = document.getElementById('nombreInput');
    if (dni.length === 8 && /^\d{8}$/.test(dni)) {
        fetch('../../backend/php/api/api_reniec.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ numdni: dni })
        })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'success') {
                nombreInput.value = `${data.prenombres} ${data.apPrimer} ${data.apSegundo}`.replace(/\s+/g, ' ').trim();
            }
        })
        .catch(console.error);
    } else {
        nombreInput.value = '';
    }
});

// Sidebar toggle
const sidebar     = document.querySelector('.sidebar');
const mainContent = document.getElementById('mainContent');
const overlay     = document.querySelector('.body-overlay');
const isMobile    = () => window.innerWidth <= 768;

document.getElementById('sidebarCollapse').addEventListener('click', () => {
    if (isMobile()) {
        sidebar.classList.toggle('mobile-active');
        overlay.classList.toggle('active');
    } else {
        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('collapsed');
    }
});
overlay.addEventListener('click', () => {
    sidebar.classList.remove('mobile-active');
    overlay.classList.remove('active');
});
window.addEventListener('resize', () => {
    if (!isMobile()) {
        sidebar.classList.remove('mobile-active');
        overlay.classList.remove('active');
    }
});
</script>
</body>
</html>