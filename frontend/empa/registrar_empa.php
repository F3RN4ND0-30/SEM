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
    <title>Registrar Empadronamiento — SEM</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Nunito:wght@700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../backend/css/empa/registrar_empa.css">
    <link rel="stylesheet" href="../../backend/css/sisvis/escritorio.css">
    <!-- Flatpickr -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <link rel="icon" type="image/png" href="../../backend/img/logoPisco.png">
</head>
<body>

<div class="body-overlay"></div>

<!-- ══ SIDEBAR ══ -->
<?php include "../navbar/navbar.php"; ?>

<!-- ══ MAIN ══ -->
<div class="form-wrapper" id="mainContent">

    <!-- TOPBAR -->
    <header class="topbar">
        <button class="topbar-toggle" id="sidebarCollapse" aria-label="Toggle sidebar">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="3" y1="6" x2="21" y2="6"/>
                <line x1="3" y1="12" x2="21" y2="12"/>
                <line x1="3" y1="18" x2="21" y2="18"/>
            </svg>
        </button>
        <a href="listar_empa.php" class="topbar-back">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <polyline points="15 18 9 12 15 6"/>
            </svg>
            Volver
        </a>
        <div class="topbar-title">Nuevo <span>Empadronamiento</span></div>
    </header>

    <!-- CONTENT -->
    <div class="content">
        <div class="form-card">

            <!-- HEADER -->
            <div class="form-card-header">
                <div class="form-card-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="12" y1="18" x2="12" y2="12"/>
                        <line x1="9" y1="15" x2="15" y2="15"/>
                    </svg>
                </div>
                <div>
                    <div class="form-card-title">Registrar Empadronamiento</div>
                    <div class="form-card-sub">Complete todos los campos para registrar un nuevo hogar empadronado</div>
                </div>
            </div>

            <!-- BODY -->
            <form action="../../backend/php/empa/guardar_empa.php" method="POST">
            <div class="form-card-body">

                <!-- §1 Clasificación de solicitud -->
                <div class="form-section">
                    <div class="section-title">Clasificación de solicitud</div>
                    <div class="fg-2">
                        <div class="form-group">
                            <label>Tipo de Solicitud</label>
                            <select name="tipo_solicitud" required>
                                <option value="">Seleccione</option>
                                <?php foreach ($tipoSolicitudes as $ts): ?>
                                    <option value="<?= $ts['IdTipoSoli'] ?>"><?= htmlspecialchars($ts['Descripcion']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Tipo de Remisión</label>
                            <select name="tipo_remision" required>
                                <option value="">Seleccione</option>
                                <?php foreach ($tipoRemisiones as $tr): ?>
                                    <option value="<?= $tr['IdTipoRemi'] ?>"><?= htmlspecialchars($tr['Descripcion']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- §2 Formatos y fechas -->
                <div class="form-section">
                    <div class="section-title">Formatos y fechas</div>
                    <div class="fg-3">
                        <div class="form-group">
                            <label>Formato D100</label>
                            <input type="number" name="d100" required placeholder="Ej: 3605226">
                        </div>
                        <div class="form-group">
                            <label>S100</label>
                            <input type="number" name="s100" required placeholder="Ej: 16678028">
                        </div>
                        <div class="form-group">
                            <label>Fecha S100</label>
                            <input type="text" class="datepicker" name="fecha_s100" required placeholder="AAAA-MM-DD">
                        </div>
                        <div class="form-group">
                            <label>FSU</label>
                            <input type="number" name="fsu" required placeholder="Ej: 25997199">
                        </div>
                        <div class="form-group">
                            <label>Fecha FSU</label>
                            <input type="text" class="datepicker" name="fecha_fsu" required placeholder="AAAA-MM-DD">
                        </div>
                        <div class="form-group">
                            <label>Año</label>
                            <input type="number" name="anio" value="<?= $anioActual ?>" required>
                        </div>
                    </div>
                </div>

                <!-- §3 Datos del solicitante -->
                <div class="form-section">
                    <div class="section-title">Datos del solicitante</div>
                    <div class="fg-2">
                        <div class="form-group">
                            <label>Tipo Documento</label>
                            <select name="tipo_doc" required>
                                <option value="">Seleccione</option>
                                <option value="DNI">DNI</option>
                                <option value="CE">CE</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>DNI del Solicitante</label>
                            <input type="number" name="dni_solicitante" id="dniInput" required placeholder="8 dígitos">
                        </div>
                    </div>
                    <div class="fg-1 mt-s">
                        <div class="form-group">
                            <label>Nombre del Solicitante</label>
                            <input type="text" name="nombre_solicitante" id="nombreInput" required placeholder="Se completa automáticamente con el DNI">
                        </div>
                    </div>
                    <div class="fg-2 mt-s">
                        <div class="form-group">
                            <label>N° de Integrantes</label>
                            <input type="number" name="num_integrantes" required placeholder="Ej: 4" min="1">
                        </div>
                        <div class="form-group">
                            <label>N° de Archivador</label>
                            <input type="number" name="num_archivador" required placeholder="Ej: 1">
                        </div>
                    </div>
                </div>

                <!-- §4 Clasificación CSE -->
                <div class="form-section">
                    <div class="section-title">Clasificación socioeconómica (CSE)</div>
                    <div class="fg-3">
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
                            <input type="text" class="datepicker" name="fecha_inicio_cse" required placeholder="AAAA-MM-DD">
                        </div>
                        <div class="form-group">
                            <label>Fecha Fin CSE</label>
                            <input type="text" class="datepicker" name="fecha_fin_cse" required placeholder="AAAA-MM-DD">
                        </div>
                    </div>
                </div>

                <!-- §5 Asignación -->
                <div class="form-section">
                    <div class="section-title">Asignación y observaciones</div>
                    <div class="fg-1">
                        <div class="form-group">
                            <label>Empadronador</label>
                            <select name="empadronador" required>
                                <option value="">Seleccione un empadronador</option>
                                <?php foreach ($empadronadores as $emp):
                                    $full = trim($emp['Nombres'] . ' ' . $emp['Ape_Pat'] . ' ' . $emp['Ape_Mat']);
                                ?>
                                    <option value="<?= htmlspecialchars($full) ?>"><?= htmlspecialchars($full) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="fg-1 mt-s">
                        <div class="form-group">
                            <label>Observaciones <span class="opt">(opcional)</span></label>
                            <textarea name="observaciones" rows="3" placeholder="Notas adicionales sobre el empadronamiento…"></textarea>
                        </div>
                    </div>
                </div>

            </div><!-- /form-card-body -->

            <!-- FOOTER -->
            <div class="form-card-footer">
                <a href="listar_empa.php" class="btn-cancel">Cancelar</a>
                <button type="submit" class="btn-submit">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                        <polyline points="17 21 17 13 7 13 7 21"/>
                        <polyline points="7 3 7 8 15 8"/>
                    </svg>
                    Guardar registro
                </button>
            </div>
            </form>

        </div><!-- /form-card -->
    </div><!-- /content -->
</div><!-- /form-wrapper -->

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