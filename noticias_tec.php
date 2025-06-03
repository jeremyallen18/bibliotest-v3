<?php
require 'db.php';

// Verificar conexión a la base de datos
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Consulta optimizada incluyendo imagen_path
$noticias = [];
$error = null;

try {
    $query = "SELECT id, titulo, imagen_path, contenido, fecha 
              FROM noticias 
              ORDER BY fecha DESC 
              LIMIT 12";
    
    $stmt = $conexion->prepare($query);
    if ($stmt === false) {
        throw new Exception("Error en la preparación de la consulta: " . $conexion->error);
    }
    
    if (!$stmt->execute()) {
        throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $noticias = $result->fetch_all(MYSQLI_ASSOC);
    
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
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family=PT+Serif:wght@400;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="css/noticiasuser.css">
</head>
<body>
    <div class="nyt-container">
        <header class="nyt-header">
            <div class="nyt-header-content">
                <div class="nyt-logo">
                    <span class="nyt-logo-text">Biblioteca Digital</span>
                </div>
                <div class="nyt-date"><?php echo date('l, F j, Y'); ?></div>
            </div>
            
            <nav class="nyt-nav">
                <ul>
                    <li><a href="contenido.php"><i class="fas fa-home"></i> Inicio</a></li>
                    <li><a href="#"><i class="fas fa-newspaper"></i> Noticias</a></li>
                    <li><a href="perfil.php"><i class="fas fa-user"></i> Mi Cuenta</a></li>
                </ul>
            </nav>
        </header>

        <main class="nyt-main">
            <section class="nyt-hero">
                <h1 class="nyt-main-title">
                    <i class="fas fa-newspaper"></i>
                    Últimas Noticias
                </h1>
                <p class="nyt-subtitle">Mantente informado sobre las novedades de nuestra biblioteca</p>
            </section>

            <div class="nyt-news-grid">
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
                    <?php foreach ($noticias as $noticia): ?>
                    <article class="nyt-card">
                        <a href="noticia_detalle.php?id=<?= $noticia['id'] ?>" class="nyt-card-link">
                            <?php if (!empty($noticia['imagen_path'])): ?>
                            <div class="nyt-card-image">
                                <img src="/biblioteca/<?= htmlspecialchars($noticia['imagen_path']) ?>" 
                                     alt="<?= htmlspecialchars($noticia['titulo']) ?>"
                                     loading="lazy">
                            </div>
                            <?php endif; ?>
                            <div class="nyt-card-content">
                                <div class="nyt-card-category">Biblioteca Digital</div>
                                <h2 class="nyt-card-title"><?= htmlspecialchars($noticia['titulo']) ?></h2>
                                <p class="nyt-card-excerpt"><?= htmlspecialchars(substr($noticia['contenido'], 0, 150)) ?>...</p>
                                <div class="nyt-card-meta">
                                    <span class="nyt-card-date"><?= date('M d, Y', strtotime($noticia['fecha'])) ?></span>
                                    <span class="nyt-card-more">Leer más <i class="fas fa-chevron-right"></i></span>
                                </div>
                            </div>
                        </a>
                    </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </main>

        <footer class="nyt-footer">
            <div class="nyt-footer-content">
                <p>&copy; <?php echo date('Y'); ?> Biblioteca Digital. Todos los derechos reservados.</p>
                <div class="nyt-social">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </footer>
    </div>

    <script src="js/noticias.js"></script>
</body>
</html>