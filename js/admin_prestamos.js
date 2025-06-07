document.addEventListener('DOMContentLoaded', function() {
    // Confirmación para bloquear usuario
    function confirmar_bloqueo() {
        return confirm('¿Estás seguro de que deseas bloquear este usuario? El usuario no podrá acceder al sistema hasta que sea desbloqueado.');
    }

    // Asignar eventos a los botones
    const blockButtons = document.querySelectorAll('.block-btn');
    blockButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirmar_bloqueo()) {
                e.preventDefault();
            }
        });
    });

    // Efectos hover mejorados para botones
    const buttons = document.querySelectorAll('.button, .action-btn');
    buttons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.style.transition = 'all 0.3s ease';
        });
        
        button.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = 'none';
        });
    });

    // Resaltar el enlace activo en el sidebar
    const currentPage = window.location.pathname.split('/').pop();
    const menuLinks = document.querySelectorAll('.menu-admin a');
    
    menuLinks.forEach(link => {
        const linkPage = link.getAttribute('href');
        if (currentPage === linkPage) {
            link.classList.add('active');
        } else {
            link.classList.remove('active');
        }
    });
});