<?php
session_start();
if (!isset($_SESSION['email'])) {
    header('Location: index.html');
    exit();
}

include 'db.php';

if (isset($_POST['confirmar'])) {
    $libro_id = $_POST['libro_id'];
    $usuario_id = $_SESSION['usuario_id'];
    $duracion = $_POST['duracion'];
    $fecha_devolucion = date('Y-m-d', strtotime("+$duracion months"));

    // Verificar si hay disponibilidad del libro
    $stmt = $conexion->prepare("SELECT cantidad_disponible FROM libros WHERE id = ?");
    $stmt->bind_param('i', $libro_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $libro = $result->fetch_assoc();
    $stmt->close();

    if ($libro && $libro['cantidad_disponible'] > 0) {
        // Hay ejemplares disponibles, proceder con el préstamo
        $conexion->begin_transaction();

        try {
            // Insertar el préstamo
            $stmt = $conexion->prepare("INSERT INTO prestamos (usuario_id, libro_id, fecha_prestamo, fecha_devolucion, estado) VALUES (?, ?, NOW(), ?, 'Pendiente')");
            $stmt->bind_param('iis', $usuario_id, $libro_id, $fecha_devolucion);
            $stmt->execute();
            $stmt->close();

            // Actualizar cantidad del libro
            $stmt = $conexion->prepare("UPDATE libros SET cantidad_disponible = cantidad_disponible - 1 WHERE id = ?");
            $stmt->bind_param('i', $libro_id);
            $stmt->execute();
            $stmt->close();

            $conexion->commit();
            $mensaje = "<p class='mensaje exito'>Préstamo solicitado con éxito. Fecha de devolución: $fecha_devolucion.</p>";
        } catch (Exception $e) {
            $conexion->rollback();
            $mensaje = "<p class='mensaje error'>Error al procesar el préstamo.</p>";
        }
    } else {
        $mensaje = "<p class='mensaje error'>El libro no está disponible actualmente. Por favor, intente más tarde.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Confirmar Préstamo</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="icon" href="icono/favicon.ico" type="image/x-icon">
    <style>
        /* Reset y tipografía */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: #252432;
            padding: 20px;
        }

        /* Animación del borde */
        @property --a {
            syntax: "<angle>";
            inherits: false;
            initial-value: 0deg;
        }

        .box {
            position: relative;
            width: 450px;
            background: repeating-conic-gradient(from var(--a), #ff2770 0%, #ff2770 5%, transparent 5%, transparent 40%, #ff2770 50%);
            filter: drop-shadow(0 15px 50px #000);
            animation: rotating 4s linear infinite;
            border-radius: 20px;
            padding: 2px;
        }

        .box::before {
            content: "";
            position: absolute;
            width: 100%;
            height: 100%;
            background: repeating-conic-gradient(from var(--a), #45f3ff 0%, #45f3ff 5%, transparent 5%, transparent 40%, #45f3ff 50%);
            filter: drop-shadow(0 15px 50px #000);
            border-radius: 20px;
            animation: rotating 4s linear infinite;
            animation-delay: -1s;
        }

        .box::after {
            content: "";
            position: absolute;
            inset: 4px;
            background: #2d2d39;
            border-radius: 15px;
            border: 8px solid #25252b;
            z-index: 0;
        }

        @keyframes rotating {
            0% { --a: 0deg; }
            100% { --a: 360deg; }
        }

        /* Contenido */
        .content {
            position: relative;
            z-index: 1;
            padding: 30px;
            color: white;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #fff;
        }

        h3 {
            color: #45f3ff;
            margin-bottom: 10px;
        }

        p {
            margin-bottom: 20px;
        }

        label, select {
            display: block;
            margin-bottom: 10px;
        }

        select {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: none;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #45f3ff;
            border: none;
            border-radius: 5px;
            color: #000;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover {
            background-color: #2e88d0;
            color: #fff;
        }

        .mensaje {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: center;
        }

        .exito {
            background-color: #28a745;
            color: #fff;
        }

        .error {
            background-color: #dc3545;
            color: #fff;
        }

        .nav-buttons {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .nav-button {
            text-align: center;
            padding: 10px 15px;
            background-color: #ff2770;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: background 0.3s;
        }

        .nav-button:hover {
            background-color: #d91b5c;
        }
    </style>
</head>
<body>
    <div class="box">
        <div class="content">
            <h1>Confirmar Préstamo</h1>

            <?php
            if (isset($mensaje)) echo $mensaje;

            if (isset($_GET['libro_id'])) {
                $libro_id = $_GET['libro_id'];
                $stmt = $conexion->prepare("SELECT titulo, descripcion FROM libros WHERE id = ?");
                $stmt->bind_param('i', $libro_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $libro = $result->fetch_assoc();
                $stmt->close();

                if ($libro) {
                    echo "<h3>Título: " . htmlspecialchars($libro['titulo']) . "</h3>";
                    echo "<p>Descripción: " . htmlspecialchars($libro['descripcion']) . "</p>";
                } else {
                    echo "<p class='mensaje error'>El libro no se encuentra.</p>";
                }
            } else {
                echo "<p class='mensaje error'>Error: No se ha recibido el ID del libro.</p>";
            }
            ?>

            <!-- Formulario de confirmación -->
            <form method="POST" action="">
                <input type="hidden" name="libro_id" value="<?= htmlspecialchars($libro_id ?? '') ?>">
                <label for="duracion">Seleccionar duración del préstamo:</label>
                <select name="duracion" id="duracion" required>
                    <option value="1">1 mes</option>
                    <option value="2">2 meses</option>
                </select>
                <button type="submit" name="confirmar">Confirmar Solicitud</button>
            </form>

            <!-- Botones de navegación -->
            <div class="nav-buttons">
                <a href="/biblioteca/contenido.php" class="nav-button">Inicio</a>
                <a href="/biblioteca/devolver_libro.php" class="nav-button">Devolver Libro</a>
            </div>
        </div>
    </div>
</body>
</html>