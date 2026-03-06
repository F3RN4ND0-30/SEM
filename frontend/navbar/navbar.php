<?php $currentPage = basename($_SERVER['PHP_SELF']); ?>
<aside class="sidebar">
    <!-- Logo -->
    <div class="logo">
        <div class="logo-flag">
            <span></span><span></span><span></span>
        </div>
        <div class="logo-text">
            SEM
            <small>Sistema Municipal</small>
        </div>
    </div>

    <span class="nav-label">Menú</span>

    <!-- Menú -->
    <a class="nav-item <?php echo ($currentPage == 'escritorio.php') ? 'active' : ''; ?>" href="../sisvis/escritorio.php">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M3 9L12 2l9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9z" />
            <polyline points="9 22 9 12 15 12 15 22" />
        </svg>
        Inicio
    </a>

    <a class="nav-item <?php echo ($currentPage == 'registrar_empa.php') ? 'active' : ''; ?>" href="../empa/registrar_empa.php">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 20h9" />
            <path d="M16 4H8a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2z" />
            <line x1="12" y1="10" x2="12" y2="14" />
            <line x1="10" y1="12" x2="14" y2="12" />
        </svg>
        Empadronamiento
    </a>

    <a class="nav-item <?php echo ($currentPage == 'listar_empa.php') ? 'active' : ''; ?>" href="../empa/listar_empa.php">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="8" y1="6" x2="21" y2="6" />
            <line x1="8" y1="12" x2="21" y2="12" />
            <line x1="8" y1="18" x2="21" y2="18" />
            <line x1="3" y1="6" x2="3" y2="6" />
            <line x1="3" y1="12" x2="3" y2="12" />
            <line x1="3" y1="18" x2="3" y2="18" />
        </svg>
        Listado
    </a>

    <a class="nav-item <?php echo ($currentPage == 'reportes.php') ? 'active' : ''; ?>" href="reportes.php">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="4" y1="19" x2="4" y2="10" />
            <line x1="12" y1="19" x2="12" y2="4" />
            <line x1="20" y1="19" x2="20" y2="14" />
            <path d="M4 10a4 4 0 0 1 8 0" />
            <path d="M12 4a4 4 0 0 1 8 0" />
        </svg>
        Reportes
    </a>

    <a class="nav-item <?php echo ($currentPage == 'usuarios.php') ? 'active' : ''; ?>" href="usuarios.php">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="9" cy="7" r="4" />
            <path d="M17 11v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-6" />
            <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
        </svg>
        Usuarios
    </a>

    <a class="nav-item <?php echo ($currentPage == 'configuracion.php') ? 'active' : ''; ?>" href="configuracion.php">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="3" />
            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09a1.65 1.65 0 0 0 1.51-1 1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51h.09a1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z" />
        </svg>
        Configuración
    </a>

    <!-- Footer / Logout -->
    <div class="sidebar-footer">
        <a class="nav-item logout" href="../../frontend/logout.php">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                <polyline points="16 17 21 12 16 7" />
                <line x1="21" y1="12" x2="9" y2="12" />
            </svg>
            Cerrar Sesión
        </a>
    </div>
</aside>