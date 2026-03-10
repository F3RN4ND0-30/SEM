const toggleButton = document.getElementById('toggleSidebar');
const sidebar = document.getElementById('sidebar');

// Crear overlay si no existe
let overlay = document.querySelector('.sidebar-overlay');
if (!overlay) {
    overlay = document.createElement('div');
    overlay.className = 'sidebar-overlay';
    document.body.appendChild(overlay);
}

// Abrir/cerrar sidebar
toggleButton.addEventListener('click', () => {
    sidebar.classList.toggle('active');
    overlay.classList.toggle('active');
});

// Cerrar al hacer click fuera
overlay.addEventListener('click', () => {
    sidebar.classList.remove('active');
    overlay.classList.remove('active');
});