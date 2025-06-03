document.addEventListener('DOMContentLoaded', function() {
    // Animación inicial con GSAP
    gsap.registerEffect({
        name: "fadeSlide",
        effect: (targets, config) => {
            return gsap.fromTo(targets, 
                { opacity: 0, y: config.y || 0, x: config.x || 0 },
                { opacity: 1, y: 0, x: 0, duration: config.duration || 0.8, ease: "power2.out", delay: config.delay || 0 }
            );
        }
    });

    // Animación del contenedor principal
    gsap.to(".library-container", {
        y: 0,
        opacity: 1,
        duration: 0.8,
        ease: "back.out(1.2)"
    });

    // Animación del header
    gsap.to(".library-header", {
        y: 0,
        opacity: 1,
        duration: 0.6,
        delay: 0.3,
        ease: "power2.out"
    });

    // Animación de los botones de navegación
    gsap.to(".nav-button", {
        opacity: 1,
        stagger: 0.1,
        delay: 0.6,
        duration: 0.5
    });

    // Animación de la tarjeta del libro
    gsap.to(".book-detail-card", {
        scale: 1,
        opacity: 1,
        duration: 0.8,
        delay: 0.4,
        ease: "elastic.out(1, 0.5)"
    });

    // Animación de la portada del libro
    gsap.to(".book-cover", {
        rotationY: 0,
        duration: 1.2,
        delay: 0.8,
        ease: "back.out(2)"
    });

    // Animación de los botones de acción
    gsap.to(".action-button", {
        opacity: 1,
        y: 0,
        stagger: 0.1,
        delay: 1.2,
        duration: 0.6,
        ease: "power2.out"
    });

    // Efecto hover para los botones
    const buttons = document.querySelectorAll('.action-button');
    buttons.forEach(button => {
        button.addEventListener('mouseenter', () => {
            gsap.to(button, {
                scale: 1.05,
                duration: 0.2,
                ease: "power2.out"
            });
        });
        button.addEventListener('mouseleave', () => {
            gsap.to(button, {
                scale: 1,
                duration: 0.2,
                ease: "power2.out"
            });
        });
    });

    // Efecto de carga para el formulario de préstamo
    const loanButtons = document.querySelectorAll('[href*="confirmar_prestamo"]');
    loanButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            gsap.to(button, {
                scale: 0.9,
                duration: 0.2,
                onComplete: () => {
                    window.location.href = button.href;
                }
            });
        });
    });

    // Animación de scroll suave para los elementos
    const animateOnScroll = () => {
        const elements = document.querySelectorAll('.fade-in, .slide-in, .pop-in');
        elements.forEach(el => {
            const rect = el.getBoundingClientRect();
            const isVisible = (rect.top <= window.innerHeight * 0.8);
            
            if (isVisible) {
                el.style.animationPlayState = 'running';
            }
        });
    };

    window.addEventListener('scroll', animateOnScroll);
    animateOnScroll(); // Ejecutar al cargar
});