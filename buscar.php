<?php
session_start();
if (!isset($_SESSION['email']) || !isset($_SESSION['usuario_id'])) {
    header('Location: index.html');
    exit();
}

include 'db.php';

$usuario_id = $_SESSION['usuario_id'];

// Procesar búsqueda
$resultados = [];
if (isset($_GET['buscar'])) {
    $busqueda = '%' . $_GET['buscar'] . '%';

    // Consulta para buscar por título o descripción
    $stmt = $conexion->prepare("SELECT id, titulo, descripcion, imagen FROM libros WHERE titulo LIKE ? OR descripcion LIKE ?");
    $stmt->bind_param('ss', $busqueda, $busqueda);
    $stmt->execute();
    $result = $stmt->get_result();
    $resultados = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Libros</title>
    <link rel="stylesheet" href="css/buscar.css">
    <!-- <link rel="stylesheet" href="css/biblioteca.css"> -->
    <link rel="icon" href="icono/favicon.ico" type="image/x-icon">
</head>
<body>
    <div class="box">
        <div class="login">
            <!-- Menú de navegación -->
            <div class="menu">
                <a href="contenido.php" class="menu-button">Inicio</a>
                <a href="devolver_libro.php" class="menu-button">Ver libros prestados</a>
            </div>

            <h1>Buscar Libros</h1>
            <form method="GET" action="">
                <div class="search-bar">
                    <input type="text" name="buscar" placeholder="Ingrese título o descripción" required>
                    <button type="submit" class="search-button">Buscar</button>
                </div>
            </form>

            <?php if ($resultados): ?>
                <div class="books-grid">
                    <?php foreach ($resultados as $libro): ?>
                        <div class="book-card">
                            <div class="book-image-container">
                                <img src="<?= htmlspecialchars($libro['imagen']) ?>" class="book-image" alt="<?= htmlspecialchars($libro['titulo']) ?>">
                            </div>
                            <div class="book-info">
                                <h3><?= htmlspecialchars($libro['titulo']) ?></h3>
                                <div class="description"><?= htmlspecialchars($libro['descripcion']) ?></div>
                            </div>
                            <div class="book-actions">
                                <?php
                                $stmt = $conexion->prepare("SELECT id FROM prestamos WHERE usuario_id = ? AND libro_id = ? AND estado = 'Prestado'");
                                $stmt->bind_param('ii', $usuario_id, $libro['id']);
                                $stmt->execute();
                                $result = $stmt->get_result();
                                $prestamo = $result->fetch_assoc();
                                $stmt->close();

                                if ($prestamo): ?>
                                    <a href="devolver_libro.php?prestamo_id=<?= $prestamo['id'] ?>" class="action-button devolver-button">Devolver Libro</a>
                                <?php else: ?>
                                    <a href="confirmar_prestamo.php?libro_id=<?= $libro['id'] ?>" class="action-button solicitar-button">Solicitar Préstamo</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php elseif (isset($_GET['buscar'])): ?>
                <p style="text-align: center; color: #fff; margin-top: 20px;">No se encontraron resultados.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>