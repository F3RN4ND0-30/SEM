<header class="topbar">
    <div class="topbar-left">
        <button class="topbar-toggle" id="sidebarCollapse" aria-label="Toggle sidebar">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="3" y1="6" x2="21" y2="6"/>
                <line x1="3" y1="12" x2="21" y2="12"/>
                <line x1="3" y1="18" x2="21" y2="18"/>
            </svg>
        </button>
        <div class="topbar-title">Panel de <span>Empadronamiento</span></div>
    </div>
    <div class="topbar-right">
        <span class="badge-tag">En vivo</span>
        <div class="user-chip">
            <div class="user-avatar"><?= htmlspecialchars($userInitial) ?></div>
            <?= htmlspecialchars($userName) ?>
        </div>
    </div>
</header>