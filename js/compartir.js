document.addEventListener('DOMContentLoaded', function() {
    // Configuración del botón de compartir (se mantiene igual)
    const shareBtn = document.getElementById('shareBtn');
    const shareOptions = document.getElementById('shareOptions');
    
    if (shareBtn && shareOptions) {
        shareBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            shareOptions.style.display = shareOptions.style.display === 'none' ? 'block' : 'none';
            
            const currentUrl = encodeURIComponent(window.location.href);
            const title = encodeURIComponent(document.title);
            
            document.getElementById('whatsappShare').href = `https://wa.me/?text=${title}%20${currentUrl}`;
            document.getElementById('facebookShare').href = `https://www.facebook.com/sharer/sharer.php?u=${currentUrl}`;
        });

        document.addEventListener('click', function(e) {
            if (!shareOptions.contains(e.target) && e.target !== shareBtn) {
                shareOptions.style.display = 'none';
            }
        });
    }

    // Configuración del botón de imprimir (nueva versión)
    const saveBtn = document.getElementById('saveBtn');
    if (saveBtn) {
        saveBtn.innerHTML = '<i class="fas fa-print"></i> Imprimir'; // Cambiar icono y texto
        saveBtn.addEventListener('click', function() {
            // Cambiar estilos temporalmente para la impresión
            const originalStyles = prepareForPrint();
            
            // Activar impresión del navegador
            window.print();
            
            // Restaurar estilos después de un breve delay
            setTimeout(() => {
                restoreAfterPrint(originalStyles);
            }, 500);
        });
    }

    // Función para preparar la página para impresión
    function prepareForPrint() {
        const styles = {};
        const elementsToHide = [
            '.nyt-article-back',
            '.nyt-article-actions',
            '.nyt-article-page-footer',
            '.shareOptions'
        ];
        
        // Ocultar elementos no deseados
        elementsToHide.forEach(selector => {
            const elements = document.querySelectorAll(selector);
            elements.forEach(el => {
                styles[selector] = styles[selector] || [];
                styles[selector].push({
                    element: el,
                    originalDisplay: el.style.display
                });
                el.style.display = 'none';
            });
        });
        
        return styles;
    }

    // Función para restaurar los estilos después de imprimir
    function restoreAfterPrint(originalStyles) {
        for (const selector in originalStyles) {
            originalStyles[selector].forEach(item => {
                item.element.style.display = item.originalDisplay;
            });
        }
    }
});