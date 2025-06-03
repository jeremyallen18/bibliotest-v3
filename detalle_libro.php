<?php
session_start();

if (!isset($_SESSION['email']) || !isset($_SESSION['usuario_id'])) {
    header('Location: index.html');
    exit();
}

include 'db.php';

$libro_id = $_GET['id'] ?? 0;
$usuario_id = $_SESSION['usuario_id'];

// Obtener detalles del libro
$stmt = $conexion->prepare("SELECT id, titulo, imagen, descripcion FROM libros WHERE id = ?");
$stmt->bind_param('i', $libro_id);
$stmt->execute();
$result = $stmt->get_result();
$libro = $result->fetch_assoc();
$stmt->close();

// Verificar préstamo actual
$stmt = $conexion->prepare("SELECT id, fecha_prestamo FROM prestamos WHERE usuario_id = ? AND libro_id = ? AND estado = 'Prestado'");
$stmt->bind_param('ii', $usuario_id, $libro_id);
$stmt->execute();
$result = $stmt->get_result();
$prestamo = $result->fetch_assoc();
$stmt->close();

if (!$libro) {
    header('Location: contenido.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($libro['titulo']) ?> - Biblioteca Digital</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="css/detalle_libro.css">
    <link rel="icon" href="icono/favicon.ico" type="image/x-icon">
</head>
<body>
    <div class="library-container">
        <!-- Header animado con GSAP -->
        <header class="library-header" id="animated-header">
            <div class="library-title">
                <i class="fas fa-book-open"></i>
                <span>Biblioteca Digital</span>
            </div>
            <nav class="nav-buttons">
                <a href="contenido.php" class="nav-button">
                    <i class="fas fa-home"></i>
                    <span>Inicio</span>
                </a>
                <a href="buscar.php" class="nav-button">
                    <i class="fas fa-search"></i>
                    <span>Buscar</span>
                </a>
                <a href="logout.php" class="nav-button">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Salir</span>
                </a>
            </nav>
        </header>

        <!-- Contenido principal con animaciones -->
        <main class="book-detail-container">
            <div class="book-detail-card" id="book-card">
                <div class="book-cover-container">
                    <span class="book-badge pulse">Disponible</span>
                    <img src="<?= htmlspecialchars($libro['imagen']) ?>" alt="<?= htmlspecialchars($libro['titulo']) ?>" class="book-cover" id="book-cover">
                </div>
                
                <div class="book-info-container">
                    <h1 class="book-title fade-in"><?= htmlspecialchars($libro['titulo']) ?></h1>
                    
                    <?php if (!empty($libro['autor'])): ?>
                    <div class="book-author slide-in">
                        <i class="fas fa-user-pen"></i>
                        <span><?= htmlspecialchars($libro['autor']) ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <div class="book-meta">
                        <?php if ($prestamo): ?>
                        <div class="meta-item pop-in" style="--delay: 0.2s">
                            <i class="fas fa-calendar-check"></i>
                            <span>Prestado el: <?= date('d/m/Y', strtotime($prestamo['fecha_prestamo'])) ?></span>
                        </div>
                        <?php endif; ?>
                        <div class="meta-item pop-in" style="--delay: 0.3s">
                            <i class="fas fa-star"></i>
                            <span>4.5 (128 valoraciones)</span>
                        </div>
                    </div>
                    
                    <div class="book-description fade-in" style="--delay: 0.5s">
                        <?= nl2br(htmlspecialchars($libro['descripcion'])) ?>
                    </div>
                    
                    <div class="book-actions">
                        <?php if ($prestamo): ?>
                            <a href="devolver_libro.php?prestamo_id=<?= $prestamo['id'] ?>" class="action-button secondary-button bounce">
                                <i class="fas fa-rotate-left"></i>
                                <span>Devolver Libro</span>
                            </a>
                        <?php else: ?>
                            <a href="confirmar_prestamo.php?libro_id=<?= $libro['id'] ?>" class="action-button primary-button pulse">
                                <i class="fas fa-hand-holding-hand"></i>
                                <span>Solicitar Préstamo</span>
                            </a>
                        <?php endif; ?>
                        
                        <a href="contenido.php" class="action-button outline-button slide-in">
                            <i class="fas fa-arrow-left"></i>
                            <span>Volver al catálogo</span>
                        </a>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="js/detalle_libro.js"></script>
</body>
</html>

<?php
$conexion->close();
?>