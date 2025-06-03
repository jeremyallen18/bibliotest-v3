// Toggle del sidebar en móviles
function setupSidebar() {
    const menuToggle = document.querySelector('.menu-toggle');
    const sidebar = document.querySelector('.sidebar');
    
    if (menuToggle && sidebar) {
        menuToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
        });
    }

    // Resaltar el enlace activo
    const currentPage = window.location.pathname.split('/').pop();
    const menuLinks = document.querySelectorAll('.admin-menu a');
    
    menuLinks.forEach(link => {
        const linkPage = link.getAttribute('href');
        if (currentPage === linkPage) {
            link.classList.add('active');
        }
    });
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    setupSidebar();
    
    // Otros scripts generales pueden ir aquí
});