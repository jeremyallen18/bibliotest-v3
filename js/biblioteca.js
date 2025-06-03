document.addEventListener('DOMContentLoaded', function() {
  // Toggle del menú móvil
  const mobileMenuBtn = document.querySelector('.mobile-menu-toggle');
  const sidebar = document.querySelector('.library-sidebar');
  const overlay = document.createElement('div');
  overlay.className = 'overlay';
  document.body.appendChild(overlay);

  function toggleMenu() {
    sidebar.classList.toggle('active');
    overlay.classList.toggle('active');
    document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
  }

  mobileMenuBtn.addEventListener('click', toggleMenu);
  overlay.addEventListener('click', toggleMenu);

  // Interacciones con las tarjetas de libros
  const bookCards = document.querySelectorAll('.book-card');

  bookCards.forEach(card => {
    // Click en la tarjeta
    card.addEventListener('click', function(e) {
      // Evitar redirección si se hace click en botones de acción
      if (e.target.closest('.book-actions')) return;
      
      const bookId = card.dataset.id;
      window.location.href = `detalle_libro.php?id=${bookId}`;
    });

    // Navegación por teclado
    card.addEventListener('keydown', function(e) {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        const bookId = card.dataset.id;
        window.location.href = `detalle_libro.php?id=${bookId}`;
      }
    });

    // Botón de favoritos
    const favBtn = card.querySelector('.favorite-btn');
    if (favBtn) {
      favBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        this.classList.toggle('far');
        this.classList.toggle('fas');
        this.classList.toggle('active');
        
        // Aquí iría la lógica para agregar/eliminar de favoritos
        const bookId = card.dataset.id;
        console.log(`Libro ${bookId} ${this.classList.contains('active') ? 'añadido a' : 'eliminado de'} favoritos`);
      });
    }
  });

  // Efecto de carga para imágenes
  const bookImages = document.querySelectorAll('.book-cover');
  bookImages.forEach(img => {
    img.style.opacity = '0';
    img.addEventListener('load', function() {
      this.style.transition = 'opacity 0.5s ease';
      this.style.opacity = '1';
    });

    if (img.complete) {
      img.style.opacity = '1';
    }
  });

  // Estado de carga para el formulario
  const searchForm = document.querySelector('.search-form');
  if (searchForm) {
    searchForm.addEventListener('submit', function() {
      const submitBtn = this.querySelector('button[type="submit"]');
      const originalText = submitBtn.innerHTML;
      
      submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Buscando...';
      submitBtn.disabled = true;
      
      // Restaurar el botón si la búsqueda falla
      setTimeout(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
      }, 5000);
    });
  }
});