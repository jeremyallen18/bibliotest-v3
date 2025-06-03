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
    <link rel="stylesheet" href="css/biblioteca.css">
    <link rel="icon" href="icono/favicon.ico" type="image/x-icon">
    <style>
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(69, 243, 255, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(69, 243, 255, 0); }
            100% { box-shadow: 0 0 0 0 rgba(69, 243, 255, 0); }
        }
        
        @keyframes pulse-red {
            0% { box-shadow: 0 0 0 0 rgba(255, 39, 112, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(255, 39, 112, 0); }
            100% { box-shadow: 0 0 0 0 rgba(255, 39, 112, 0); }
        }
        
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-5px); }
            100% { transform: translateY(0px); }
        }
        
        @keyframes inputFocus {
            0% { box-shadow: 0 0 0 0 rgba(69, 243, 255, 0.7); }
            100% { box-shadow: 0 0 0 5px rgba(69, 243, 255, 0.3); }
        }

        body {
            background-color: #15151f;
            color: #fff;
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
        }

        .box {
            padding: 30px;
        }

        .books-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 25px;
            margin-top: 30px;
        }

        .book-card {
            background-color: #1e1e28;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border: 1px solid #333;
            height: 100%;
        }

        .book-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.4);
        }

        .book-image-container {
            width: 100%;
            height: 280px;
            position: relative;
            overflow: hidden;
            background: #2d2d39;
        }

        .book-image {
            width: 100%;
            height: 100%;
            object-fit: contain;
            transition: transform 0.5s ease;
        }

        .book-card:hover .book-image {
            transform: scale(1.05);
        }

        .book-info {
            padding: 15px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .book-info h3 {
            font-size: 1rem;
            color: #45f3ff;
            margin: 0 0 10px 0;
            line-height: 1.3;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .description {
            font-size: 0.8rem;
            color: #ddd;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            line-height: 1.4;
            margin-bottom: 15px;
        }

        .book-actions {
            padding: 12px 15px;
            background-color: #1e1e28;
            border-top: 1px solid #333;
        }

        .action-button {
            display: block;
            width: 100%;
            padding: 12px;
            border-radius: 5px;
            text-align: center;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .action-button::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 5px;
            height: 5px;
            background: rgba(255, 255, 255, 0.5);
            opacity: 0;
            border-radius: 100%;
            transform: scale(1, 1) translate(-50%);
            transform-origin: 50% 50%;
        }

        .action-button:focus:not(:active)::after {
            animation: ripple 1s ease-out;
        }

        @keyframes ripple {
            0% {
                transform: scale(0, 0);
                opacity: 0.5;
            }
            100% {
                transform: scale(20, 20);
                opacity: 0;
            }
        }

        .solicitar-button {
            background-color: #ff2770;
            color: white;
        }

        .solicitar-button:hover {
            animation: pulse-red 1.5s infinite;
            transform: translateY(-2px);
        }

        .devolver-button {
            background-color: #45f3ff;
            color: #111;
        }

        .devolver-button:hover {
            animation: pulse 1.5s infinite;
            transform: translateY(-2px);
        }

        .search-bar {
            display: flex;
            gap: 10px;
            margin: 20px auto;
            max-width: 600px;
        }

        .search-bar input {
            flex-grow: 1;
            padding: 12px 15px;
            border-radius: 30px;
            border: 2px solid #45f3ff;
            background: #2d2d39;
            color: white;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .search-bar input:focus {
            outline: none;
            animation: inputFocus 0.5s ease forwards;
        }

        .search-button {
            padding: 12px 25px;
            background: #45f3ff;
            color: #111;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .search-button:hover {
            animation: pulse 1.5s infinite;
            transform: translateY(-2px);
        }

        .menu {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .menu-button {
            background-color: #45f3ff;
            color: #111;
            padding: 10px 20px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .menu-button:hover {
            animation: pulse 1.5s infinite;
            transform: translateY(-3px);
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.2);
        }

        h1 {
            text-align: center;
            color: #fff;
            margin-bottom: 20px;
            font-size: 2.2rem;
            background: linear-gradient(90deg, #45f3ff, #ff2770);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: float 3s ease-in-out infinite;
        }
    </style>
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