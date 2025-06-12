<?php
require_once 'php_utils.php';
require 'db.php';

// Verificar conexión a la base de datos
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Consultas para diferentes secciones
$noticias = [];
$destacadas = [];
$tendencias = [];
$error = null;

try {
    // Noticias principales (lo último)
    $query = "SELECT id, titulo, imagen_path, contenido, fecha 
              FROM noticias 
              ORDER BY fecha DESC 
              LIMIT 12";
    $stmt = $conexion->prepare($query);
    if (!$stmt->execute())
        throw new Exception("Error al ejecutar consulta principal");
    $result = $stmt->get_result();
    $noticias = $result->fetch_all(MYSQLI_ASSOC);

    // Noticias destacadas (podrían ser las más visitadas o manualmente destacadas)
    $query_destacadas = "SELECT id, titulo, imagen_path, contenido, fecha 
                         FROM noticias 
                         ORDER BY visitas DESC 
                         LIMIT 3";
    $stmt_destacadas = $conexion->prepare($query_destacadas);
    if (!$stmt_destacadas->execute())
        throw new Exception("Error al ejecutar consulta destacadas");
    $result_destacadas = $stmt_destacadas->get_result();
    $destacadas = $result_destacadas->fetch_all(MYSQLI_ASSOC);

    // Tendencias (podrían ser las más comentadas o con más interacciones)
    $query_tendencias = "SELECT id, titulo, imagen_path, contenido, fecha 
                         FROM noticias 
                         ORDER BY RAND() 
                         LIMIT 4";
    $stmt_tendencias = $conexion->prepare($query_tendencias);
    if (!$stmt_tendencias->execute())
        throw new Exception("Error al ejecutar consulta tendencias");
    $result_tendencias = $stmt_tendencias->get_result();
    $tendencias = $result_tendencias->fetch_all(MYSQLI_ASSOC);

} catch (Exception $e) {
    error_log("Error en noticias_listado.php: " . $e->getMessage());
    $error = "Error al cargar las noticias. Por favor intente más tarde.";
}

$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Noticias - Biblioteca Digital</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family=PT+Serif:wght@400;700&family=Roboto:wght@300;400;500&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="css/noticiasuser.css" id="theme-style">
</head>

<body>
    <div class="nyt-container">
        <header class="nyt-header">
            <div class="nyt-logo">Bibliotest</div>
            
            <div class="theme-switcher">
                <button id="theme-toggle" class="theme-toggle-btn">
                    <i class="fas fa-moon"></i> Modo Oscuro
                </button>
            </div>

            <div class="nyt-article-date">
                <?php
                setlocale(LC_TIME, 'es_ES.utf8', 'es_ES', 'es');
                echo utf8_encode(strftime('%A, %d de %B de %Y'));
                ?>
            </div>

            <nav class="nyt-nav">
                <ul>
                    <li><a href="contenido.php">Inicio</a></li>
                    <li><a href="#" class="active">Noticias</a></li>
                    <li><a href="#">Libros</a></li>
                    <li><a href="#">Autores</a></li>
                    <li><a href="#">Eventos</a></li>
                    <li><a href="perfil.php">Mi Cuenta</a></li>
                </ul>
            </nav>
        </header>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const themeToggle = document.getElementById('theme-toggle');
                const themeStyle = document.getElementById('theme-style');
                
                // Verificar si hay una preferencia guardada en localStorage
                const currentTheme = localStorage.getItem('theme') || 'light';
                
                // Aplicar el tema guardado
                if(currentTheme === 'dark') {
                    themeStyle.href = "css/noticiasuser2.css";
                    themeToggle.innerHTML = '<i class="fas fa-sun"></i> Modo Claro';
                } else {
                    themeStyle.href = "css/noticiasuser.css";
                    themeToggle.innerHTML = '<i class="fas fa-moon"></i> Modo Oscuro';
                }
                
                // Manejar el clic del botón
                themeToggle.addEventListener('click', function() {
                    if(themeStyle.href.includes('noticiasuser.css')) {
                        themeStyle.href = "css/noticiasuser2.css";
                        localStorage.setItem('theme', 'dark');
                        themeToggle.innerHTML = '<i class="fas fa-sun"></i> Modo Claro';
                    } else {
                        themeStyle.href = "css/noticiasuser.css";
                        localStorage.setItem('theme', 'light');
                        themeToggle.innerHTML = '<i class="fas fa-moon"></i> Modo Oscuro';
                    }
                });
            });
        </script>

        <main class="nyt-main">
            <section class="nyt-hero">
                <h1 class="nyt-main-title">
                    <i class="fas fa-newspaper"></i>
                    Noticias del Tecnologico
                </h1>
                <p class="nyt-subtitle">Mantente informado sobre las novedades, eventos y tendencias en la institución</p>
            </section>

            <!-- Sección Destacadas -->
            <?php if (!empty($destacadas)): ?>
                <section class="nyt-featured-section">
                    <h2 class="nyt-section-title">Destacadas</h2>
                    <div class="nyt-featured">
                        <div class="nyt-featured-main">
                            <?php $main_featured = array_shift($destacadas); ?>
                            <article class="nyt-card">
                                <a href="noticia_detalle.php?id=<?= $main_featured['id'] ?>">
                                    <?php if (!empty($main_featured['imagen_path'])): ?>
                                        <div class="nyt-card-image">
                                            <img src="<?= htmlspecialchars($main_featured['imagen_path']) ?>"
                                                alt="<?= htmlspecialchars($main_featured['titulo']) ?>">
                                        </div>
                                    <?php endif; ?>
                                    <div class="nyt-card-content">
                                        <div class="nyt-card-category">Artículo Destacado</div>
                                        <h2 class="nyt-card-title"><?= htmlspecialchars($main_featured['titulo']) ?></h2>
                                        <p class="nyt-card-excerpt">
                                            <?= generar_extracto_inteligente($main_featured['contenido'], 200) ?></p>
                                        <div class="nyt-card-meta">
                                            <span
                                                class="nyt-card-date"><?= formatear_fecha_relativa($main_featured['fecha']) ?></span>
                                        </div>
                                    </div>
                                </a>
                            </article>
                        </div>

                        <div class="nyt-featured-side">
                            <?php foreach ($destacadas as $destacada): ?>
                                <article class="nyt-card">
                                    <a href="noticia_detalle.php?id=<?= $destacada['id'] ?>">
                                        <?php if (!empty($destacada['imagen_path'])): ?>
                                            <div class="nyt-card-image">
                                                <img src="<?= htmlspecialchars($destacada['imagen_path']) ?>"
                                                    alt="<?= htmlspecialchars($destacada['titulo']) ?>">
                                            </div>
                                        <?php endif; ?>
                                        <div class="nyt-card-content">
                                            <div class="nyt-card-category">Destacado</div>
                                            <h2 class="nyt-card-title"><?= htmlspecialchars($destacada['titulo']) ?></h2>
                                            <p class="nyt-card-excerpt">
                                                <?= generar_extracto_inteligente($destacada['contenido'], 100) ?></p>
                                            <div class="nyt-card-meta">
                                                <span
                                                    class="nyt-card-date"><?= formatear_fecha_relativa($destacada['fecha']) ?></span>
                                            </div>
                                        </div>
                                    </a>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>
            <?php endif; ?>

            <!-- Sección Tendencias -->
            <?php if (!empty($tendencias)): ?>
                <section class="nyt-trending-section">
                    <h2 class="nyt-section-title">Tendencias</h2>
                    <div class="nyt-trending">
                        <?php foreach ($tendencias as $tendencia): ?>
                            <article class="nyt-card">
                                <a href="noticia_detalle.php?id=<?= $tendencia['id'] ?>">
                                    <?php if (!empty($tendencia['imagen_path'])): ?>
                                        <div class="nyt-card-image">
                                            <img src="<?= htmlspecialchars($tendencia['imagen_path']) ?>"
                                                alt="<?= htmlspecialchars($tendencia['titulo']) ?>">
                                        </div>
                                    <?php endif; ?>
                                    <div class="nyt-card-content">
                                        <div class="nyt-card-category">Popular</div>
                                        <h2 class="nyt-card-title"><?= htmlspecialchars($tendencia['titulo']) ?></h2>
                                        <div class="nyt-card-meta">
                                            <span
                                                class="nyt-card-date"><?= formatear_fecha_relativa($tendencia['fecha']) ?></span>
                                        </div>
                                    </div>
                                </a>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

            <!-- Sección Lo Último -->
            <section class="nyt-latest-section">
                <h2 class="nyt-section-title">Lo Último</h2>
                <?php if (isset($error)): ?>
                    <div class="nyt-error-message">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p><?= htmlspecialchars($error) ?></p>
                    </div>
                <?php elseif (empty($noticias)): ?>
                    <div class="nyt-no-news">
                        <i class="fas fa-info-circle"></i>
                        <p>No hay noticias disponibles</p>
                    </div>
                <?php else: ?>
                    <div class="nyt-latest">
                        <?php foreach ($noticias as $noticia): ?>
                            <article class="nyt-card">
                                <a href="noticia_detalle.php?id=<?= $noticia['id'] ?>">
                                    <?php if (!empty($noticia['imagen_path'])): ?>
                                        <div class="nyt-card-image">
                                            <img src="<?= htmlspecialchars($noticia['imagen_path']) ?>"
                                                alt="<?= htmlspecialchars($noticia['titulo']) ?>" loading="lazy">
                                        </div>
                                    <?php endif; ?>
                                    <div class="nyt-card-content">
                                        <div class="nyt-card-category">Biblioteca Digital</div>
                                        <h2 class="nyt-card-title"><?= htmlspecialchars($noticia['titulo']) ?></h2>
                                        <p class="nyt-card-excerpt">
                                            <?= generar_extracto_inteligente($noticia['contenido'], 120) ?></p>
                                        <div class="nyt-card-meta">
                                            <span
                                                class="nyt-card-date"><?= formatear_fecha_relativa($noticia['fecha']) ?></span>
                                            <span class="nyt-card-more">Leer más <i class="fas fa-chevron-right"></i></span>
                                        </div>
                                    </div>
                                </a>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
        </main>

        <footer class="nyt-footer">
            <div class="nyt-footer-content">
                <p>&copy; <?php echo date('Y'); ?> Bibliotest. Todos los derechos reservados.</p>
                <div class="nyt-social">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
        </footer>

    </div>

    <script src="js/noticias.js"></script>
</body>

</html>