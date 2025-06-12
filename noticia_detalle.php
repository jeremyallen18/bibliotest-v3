<?php
require 'db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: noticias_tec.php");
    exit();
}

$id = intval($_GET['id']);
$noticia = null;
$error = null;

try {
    $query = "SELECT id, titulo, contenido, imagen_path, fecha 
              FROM noticias 
              WHERE id = ?";

    $stmt = $conexion->prepare($query);
    if ($stmt === false) {
        throw new Exception("Error en la preparación de la consulta: " . $conexion->error);
    }

    $stmt->bind_param("i", $id);

    if (!$stmt->execute()) {
        throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
    }

    $result = $stmt->get_result();
    $noticia = $result->fetch_assoc();

    if (!$noticia) {
        header("Location: noticias_listado.php");
        exit();
    }

} catch (Exception $e) {
    error_log("Error en noticia_detalle.php: " . $e->getMessage());
    $error = "Error al cargar la noticia. Por favor intente más tarde.";
}

$conexion->close();

// Función para formatear fecha segura
function formatFecha($fecha)
{
    try {
        $date = new DateTime($fecha);
        return $date->format('d M Y');
    } catch (Exception $e) {
        error_log("Error formateando fecha ($fecha): " . $e->getMessage());
        return date('d M Y');
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($noticia['titulo']) ?> - Bibliotest</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family=PT+Serif:wght@400;700&family=Roboto:wght@300;400;500&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="css/noticia_detalle2.css" id="theme-style">
</head>

<body>
    <div class="nyt-article-container">
        <!-- Encabezado estilo NYT -->
        <header class="nyt-article-header">
            <div class="nyt-article-header-content">
                <div class="nyt-logo-small">
                    <span>Bibliotest</span>
                </div>
                <div class="nyt-article-date">
                    <?php
                    setlocale(LC_TIME, 'es_ES.utf8', 'es_ES', 'es');
                    echo utf8_encode(strftime('%A, %d de %B de %Y'));
                    ?>
                </div>
            </div>

            <nav class="nyt-article-nav">
                <a href="noticias_tec.php" class="nyt-article-back">
                    <i class="fas fa-arrow-left"></i>
                    Volver a noticias
                </a>
                <button id="theme-toggle" class="nyt-theme-toggle">
                    <i class="fas fa-moon"></i> Modo Oscuro
                </button>
            </nav>
        </header>

        <!-- Contenido principal -->
        <main class="nyt-article-main">
            <?php if (isset($error)): ?>
                <div class="nyt-article-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p><?= htmlspecialchars($error) ?></p>
                </div>
            <?php else: ?>
                <article class="nyt-article">
                    <!-- Cabecera del artículo -->
                    <header class="nyt-article-head">
                        <h1 class="nyt-article-title"><?= htmlspecialchars($noticia['titulo']) ?></h1>
                        <div class="nyt-article-meta">
                            <span class="nyt-article-date">
                                <i class="far fa-calendar-alt"></i>
                                Publicado el <?= formatFecha($noticia['fecha']) ?>
                            </span>
                            <span class="nyt-article-category">Bibliotest</span>
                        </div>
                    </header>

                    <!-- Imagen destacada -->
                    <?php if (!empty($noticia['imagen_path'])): ?>
                        <figure class="nyt-article-image">
                            <img src="/biblioteca/<?= htmlspecialchars($noticia['imagen_path']) ?>"
                                alt="<?= htmlspecialchars($noticia['titulo']) ?>" class="nyt-article-img">
                            <figcaption class="nyt-article-caption">Imagen de la la noticia</figcaption>
                        </figure>
                    <?php endif; ?>

                    <!-- Contenido del artículo -->
                    <div class="nyt-article-content">
                        <?= nl2br(htmlspecialchars($noticia['contenido'])) ?>

                        <!-- Elementos adicionales estilo NYT -->
                        <div class="nyt-article-actions">
                            <!-- Botón Compartir - modificado -->
                            <button class="nyt-article-share shareBtn" id="shareBtn">
                                <i class="fas fa-share-alt"></i> Compartir
                            </button>
                            <div class="shareOptions" id="shareOptions" style="display:none;">
                                <a href="#" class="whatsappShare" id="whatsappShare" target="_blank">WhatsApp</a> |
                                <a href="#" class="facebookShare" id="facebookShare" target="_blank">Facebook</a>
                            </div>

                            <!-- Botón Guardar - modificado -->
                            <button class="nyt-article-save saveBtn" id="saveBtn" data-id="<?= $id ?? 'noticia' ?>">
                                <i class="fas fa-download"></i> Guardar PDF
                            </button>
                        </div>

                        <!-- Pie de artículo -->
                        <footer class="nyt-article-footer">
                            <div class="nyt-article-tags">
                                <span>Etiquetas:</span>
                                <a href="#" class="nyt-tag">Biblioteca</a>
                                <a href="#" class="nyt-tag">Actualización</a>
                                <a href="#" class="nyt-tag">Digital</a>
                            </div>
                        </footer>
                </article>
            <?php endif; ?>

        </main>

        <!-- Pie de página -->
        <footer class="nyt-article-page-footer">
            <div class="nyt-footer-content">
                <p>&copy; <?= date('Y') ?> Biblioteca Digital. Todos los derechos reservados.</p>
                <div class="nyt-social">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </footer>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script src="js/compartir.js"></script>
    <script src="js/cambiar_tema.js"></script>
</body>

</html>