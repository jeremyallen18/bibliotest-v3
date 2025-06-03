<?php
session_start();
if (!isset($_SESSION['email'])) {
    header('Location: index.html');
    exit();
}

include 'db.php';

$usuario_id = $_SESSION['usuario_id'];

$stmt = $conexion->prepare("SELECT p.id, l.titulo, p.fecha_prestamo, p.fecha_devolucion, p.estado 
                            FROM prestamos p
                            JOIN libros l ON p.libro_id = l.id
                            WHERE p.usuario_id = ?");
$stmt->bind_param('i', $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

$prestamos = [];
while ($row = $result->fetch_assoc()) {
    $prestamos[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Devolver Libros</title>
    <link rel="stylesheet" href="css/devolver_libro.css">
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
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes ripple {
            0% {
                transform: scale(0);
                opacity: 0.5;
            }
            100% {
                transform: scale(20);
                opacity: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login">
            <!-- Menú de navegación -->
            <div class="menu">
                <a href="contenido.php" class="menu-button">Inicio</a>
                <a href="buscar.php" class="menu-button">Buscar y Solicitar Libros</a>
                <a href="perfil.php" class="menu-button active">Mi perfil</a>
                <form method="POST" action="limpiar_prestamos.php" onsubmit="return confirm('¿Estás seguro de que deseas eliminar todos los préstamos? Esta acción no se puede deshacer.');">
    <button type="submit" class="menu-button logout" name="limpiar">Limpiar Registros</button>
</form>
            </div>
            
            <!-- Contenido principal -->
            <div class="content">
                <h1>Mis Préstamos</h1>

                <?php if (empty($prestamos)): ?>
                    <p class="no-prestamos">No tienes préstamos registrados.</p>
                <?php else: ?>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Título</th>
                                    <th>Fecha de Préstamo</th>
                                    <th>Fecha de Devolución</th>
                                    <th>Estado</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($prestamos as $prestamo): ?>
                                    <tr class="fade-in-row">
                                        <td><?= htmlspecialchars($prestamo['titulo']) ?></td>
                                        <td><?= htmlspecialchars($prestamo['fecha_prestamo']) ?></td>
                                        <td><?= htmlspecialchars($prestamo['fecha_devolucion']) ?></td>
                                        <td><?= htmlspecialchars($prestamo['estado']) ?></td>
                                        <td>
                                            <?php if ($prestamo['estado'] === 'Pendiente'): ?>
                                                <form method="POST" action="procesar_devolucion.php">
                                                    <input type="hidden" name="prestamo_id" value="<?= $prestamo['id'] ?>">
                                                    <button type="submit" class="btn-action" name="devolver">Devolver Libro</button>
                                                </form>
                                            <?php else: ?>
                                                <button class="btn-disabled" disabled>Devuelto</button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>