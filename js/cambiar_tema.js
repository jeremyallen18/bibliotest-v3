document.addEventListener('DOMContentLoaded', function() {
    const themeToggle = document.getElementById('theme-toggle');
    const themeStyle = document.getElementById('theme-style'); // Asegúrate de tener un id="theme-style" en tu <link> del CSS
    
    // Verificar preferencia guardada
    const currentTheme = localStorage.getItem('theme') || 'light';
    
    // Aplicar tema guardado
    if(currentTheme === 'dark') {
        themeStyle.href = "css/noticia_detalle2.css";
        themeToggle.innerHTML = '<i class="fas fa-sun"></i> Modo Claro';
    } else {
        themeStyle.href = "css/noticia_detalle.css";
        themeToggle.innerHTML = '<i class="fas fa-moon"></i> Modo Oscuro';
    }
    
    // Manejar clic del botón
    themeToggle.addEventListener('click', function() {
        if(themeStyle.href.includes('noticia_detalle.css')) {
            themeStyle.href = "css/noticia_detalle2.css";
            localStorage.setItem('theme', 'dark');
            themeToggle.innerHTML = '<i class="fas fa-sun"></i> Modo Claro';
            document.body.classList.add('dark');
        } else {
            themeStyle.href = "css/noticia_detalle.css";
            localStorage.setItem('theme', 'light');
            themeToggle.innerHTML = '<i class="fas fa-moon"></i> Modo Oscuro';
            document.body.classList.remove('dark');
        }
    });
});