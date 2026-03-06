<?php
function esPaginaActiva($fragmento_ruta)
{
    $ruta_actual = $_SERVER['REQUEST_URI'];
    return strpos($ruta_actual, $fragmento_ruta) !== false;
}
function obtenerPrimerNombre($nombre_completo)
{
    return explode(' ', $nombre_completo)[0];
}
?>
<div class="wrapper">
    <div class="sidebar-menu">
        <button id="sidebarCollapse" class="btn-toggle siderbar-text">☰ MENU</button>
    </div>

    <nav id="sidebar">
        <div class="sidebar-header">
            <h3>
                <img src="/sisti/backend/img/logoPisco.png" class="img-fluid" alt="Logo" />
                <span class="sidebar-text">SEM</span>
            </h3>
        </div>

        <ul class="list-unstyled components">
            <li <?php echo esPaginaActiva('/escritorio.php') ? 'class="active"' : ''; ?>>
                <a href="../../frontend/sisvis/escritorio.php">
                    <span class="icon">🏠</span>
                    <span class="text">Inicio</span>
                </a>
            </li>
            <li <?php echo esPaginaActiva('/usuarios.php') ? 'class="active"' : ''; ?>>
                <a href="#">
                    <span class="icon">👤</span>
                    <span class="text">Usuarios</span>
                </a>
            </li>
            <li <?php echo esPaginaActiva('../../frontend/empa/registrar_empa.php') ? 'class="active"' : ''; ?>>
                <a href="../../frontend/empa/registrar_empa.php">
                    <span class="icon">📊</span>
                    <span class="text">Empadronamiento</span>
                </a>
            </li>
            <li <?php echo esPaginaActiva('/config.php') ? 'class="active"' : ''; ?>>
                <a href="#">
                    <span class="icon">⚙️</span>
                    <span class="text">Configuración</span>
                </a>
            </li>
        </ul>

        <div class="logout">
            <a href="../logout.php">
                <span class="icon">🚪</span>
                <span class="text">Cerrar sesión</span>
            </a>
        </div>
    </nav>
    <div class="body-overlay"></div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.querySelector('.body-overlay');
        const toggleBtn = document.getElementById('sidebarCollapse');

        toggleBtn.addEventListener('click', () => {

            sidebar.classList.toggle('collapsed');

            // overlay solo en móviles
            if (window.innerWidth <= 768) {
                overlay.classList.toggle('active');
            }

        });

        overlay.addEventListener('click', () => {
            sidebar.classList.add('collapsed');
            overlay.classList.remove('active');
        });
    });
</script>