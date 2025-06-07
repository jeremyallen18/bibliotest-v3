document.addEventListener('DOMContentLoaded', function() {
    // Efectos para los inputs
    const inputs = document.querySelectorAll('.form-group input, .form-group textarea');
    
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentNode.querySelector('label').style.color = '#45f3ff';
            this.parentNode.querySelector('label').style.textShadow = '0 0 5px #45f3ff';
        });
        
        input.addEventListener('blur', function() {
            this.parentNode.querySelector('label').style.color = '';
            this.parentNode.querySelector('label').style.textShadow = '';
        });
    });

    // Confirmación antes de enviar
    const form = document.querySelector('.news-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const titulo = document.getElementById('titulo').value.trim();
            const contenido = document.getElementById('contenido').value.trim();
            
            if (!titulo || !contenido) {
                e.preventDefault();
                alert('Por favor complete todos los campos requeridos');
                return;
            }
            
            if (!confirm('¿Está seguro que desea publicar esta noticia?')) {
                e.preventDefault();
            }
        });
    }

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