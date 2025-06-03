document.addEventListener('DOMContentLoaded', function() {
    // Toggle del sidebar en móviles
    const menuToggle = document.querySelector('.menu-toggle');
    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
        });
    }

    // Confirmación para eliminar noticias
    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('¿Seguro que deseas eliminar esta noticia?')) {
                e.preventDefault();
            }
        });
    });

    // Efectos hover mejorados
    const buttons = document.querySelectorAll('.button, .action-btn');
    buttons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.style.transition = 'all 0.3s ease';
        });
    });
});