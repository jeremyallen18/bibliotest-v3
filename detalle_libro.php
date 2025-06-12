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
                    <img src="<?= htmlspecialchars($libro['imagen']) ?>" alt="<?= htmlspecialchars($libro['titulo']) ?>"
                        class="book-cover" id="book-cover">
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
                        <!-- Mostrar reseñas -->
                        <section class="book-reviews fade-in" style="--delay: 0.6s">
                            <h2><i class="fas fa-comments"></i> Reseñas</h2>
                            <?php
                           $stmt = $conexion->prepare("SELECT r.id, r.calificacion, r.comentario, r.fecha, r.id_usuario, u.email FROM resenas r JOIN usuarios u ON r.id_usuario = u.id WHERE r.id_libro = ? ORDER BY r.fecha DESC");
                            $stmt->bind_param('i', $libro_id);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            if ($result->num_rows === 0) {
                                echo "<p>Aún no hay reseñas para este libro.</p>";
                            } else {
                                while ($resena = $result->fetch_assoc()) {
                                    echo "<div class='review'>";
                                    echo "<strong>" . htmlspecialchars($resena['email']) . "</strong> ";
                                    echo "<span>" . str_repeat("⭐", (int) $resena['calificacion']) . "</span><br>";
                                    echo "<em>" . nl2br(htmlspecialchars($resena['comentario'])) . "</em><br>";
                                    echo "<small>" . date('d/m/Y H:i', strtotime($resena['fecha'])) . "</small>";

                                    if ($_SESSION['usuario_id'] == $resena['id_usuario']) {
                                        echo "<form method='POST' action='editar_resena.php' style='display:inline'>";
                                        echo "<input type='hidden' name='resena_id' value='" . $resena['id'] . "'>";
                                        echo "<input type='hidden' name='libro_id' value='" . $libro_id . "'>";
                                        echo "<button class='action-button outline-button' type='submit'><i class='fas fa-pen'></i> Editar</button>";
                                        echo "</form> ";

                                        echo "<form method='POST' action='eliminar_resena.php' style='display:inline' onsubmit=\"return confirm('¿Seguro que deseas eliminar esta reseña?');\">";
                                        echo "<input type='hidden' name='resena_id' value='" . $resena['id'] . "'>";
                                        echo "<input type='hidden' name='libro_id' value='" . $libro_id . "'>";
                                        echo "<button class='action-button secondary-button' type='submit'><i class='fas fa-trash'></i> Eliminar</button>";
                                        echo "</form>";
                                    }

                                    echo "</div><hr>";

                                }
                            }
                            $stmt->close();
                            ?>
                        </section>

                        <!-- Formulario para dejar reseña -->
                        <section class="review-form fade-in" style="--delay: 0.7s">
                            <h3><i class="fas fa-pen"></i> Escribe tu reseña</h3>
                            <form action="guardar_resena.php" method="POST">
                                <input type="hidden" name="id_libro" value="<?= $libro_id ?>">
                                <label for="calificacion">Calificación:</label>
                                <select name="calificacion" required>
                                    <option value="5">⭐⭐⭐⭐⭐</option>
                                    <option value="4">⭐⭐⭐⭐</option>
                                    <option value="3">⭐⭐⭐</option>
                                    <option value="2">⭐⭐</option>
                                    <option value="1">⭐</option>
                                </select><br><br>
                                <label for="comentario">Comentario:</label><br>
                                <textarea name="comentario" rows="4" cols="50" required></textarea><br><br>
                                <button type="submit" class="action-button primary-button">Enviar Reseña</button>
                            </form>
                        </section>
                    </div>

                    <div class="book-actions">
                        <?php if ($prestamo): ?>
                            <a href="devolver_libro.php?prestamo_id=<?= $prestamo['id'] ?>"
                                class="action-button secondary-button bounce">
                                <i class="fas fa-rotate-left"></i>
                                <span>Devolver Libro</span>
                            </a>
                        <?php else: ?>
                            <a href="confirmar_prestamo.php?libro_id=<?= $libro['id'] ?>"
                                class="action-button primary-button pulse">
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