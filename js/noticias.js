document.addEventListener('DOMContentLoaded', function() {
    // Verificar si hay noticias antes de aplicar animaciones
    const newsGrid = document.getElementById('news-grid');
    const newsCards = document.querySelectorAll('.news-card');
    
    if (newsCards.length > 0) {
        // Animación inicial solo si hay noticias
        gsap.from('.news-header', {
            y: -50,
            opacity: 0,
            duration: 0.8,
            ease: "power3.out"
        });

        gsap.from(newsCards, {
            y: 30,
            opacity: 0,
            stagger: 0.1,
            duration: 0.6,
            delay: 0.3,
            ease: "back.out(1.2)"
        });

        // Efectos hover con GSAP para mejor rendimiento
        newsCards.forEach(card => {
            card.addEventListener('mouseenter', () => {
                gsap.to(card, {
                    y: -5,
                    duration: 0.3,
                    ease: "power2.out"
                });
            });
            
            card.addEventListener('mouseleave', () => {
                gsap.to(card, {
                    y: 0,
                    duration: 0.3,
                    ease: "power2.out"
                });
            });
        });

        // Toggle entre vista de cuadrícula y lista
        const toggleViewBtn = document.getElementById('toggle-view');
        
        toggleViewBtn.addEventListener('click', function() {
            newsGrid.classList.toggle('list-view');
            
            // Cambiar icono
            const icon = this.querySelector('i');
            if (newsGrid.classList.contains('list-view')) {
                icon.classList.remove('fa-th');
                icon.classList.add('fa-list');
                this.setAttribute('title', 'Vista de cuadrícula');
            } else {
                icon.classList.remove('fa-list');
                icon.classList.add('fa-th');
                this.setAttribute('title', 'Vista de lista');
            }
        });

        // Modal para noticias completas
        const modal = document.getElementById('news-modal');
        const closeModalBtn = document.getElementById('close-modal');
        const modalContent = document.getElementById('modal-content');
        const readMoreBtns = document.querySelectorAll('.read-more');
        
        // Cargar noticia completa via AJAX
        readMoreBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const newsCard = this.closest('.news-card');
                const newsId = newsCard.dataset.id;
                
                // Mostrar loader
                modalContent.innerHTML = '<div class="loader"><i class="fas fa-spinner fa-spin"></i> Cargando noticia...</div>';
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';
                
                fetch(`get_noticia.php?id=${newsId}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Error en la respuesta del servidor');
                        }
                        return response.text();
                    })
                    .then(html => {
                        modalContent.innerHTML = html;
                        
                        // Animación de entrada del modal
                        gsap.from('.modal-content', {
                            y: 50,
                            opacity: 0,
                            duration: 0.5,
                            ease: "back.out(1.2)"
                        });
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        modalContent.innerHTML = `
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                <p>Error al cargar la noticia</p>
                                <p class="error-detail">${error.message}</p>
                            </div>
                        `;
                    });
            });
        });
        
        // Cerrar modal
        closeModalBtn.addEventListener('click', function() {
            gsap.to('.modal-content', {
                y: 50,
                opacity: 0,
                duration: 0.3,
                ease: "power2.in",
                onComplete: () => {
                    modal.classList.remove('active');
                    document.body.style.overflow = 'auto';
                }
            });
        });
        
        // Cerrar al hacer clic fuera del contenido
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                gsap.to('.modal-content', {
                    y: 50,
                    opacity: 0,
                    duration: 0.3,
                    ease: "power2.in",
                    onComplete: () => {
                        modal.classList.remove('active');
                        document.body.style.overflow = 'auto';
                    }
                });
            }
        });
    } else {
        // Mostrar mensaje de error de manera destacada
        gsap.from('.no-news', {
            scale: 0.8,
            opacity: 0,
            duration: 0.6,
            ease: "back.out(1.2)"
        });
    }
});