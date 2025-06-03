<?php
session_start();
if (!isset($_SESSION['email'])) {
    header('Location: index.html');
    exit();
}

include 'db.php';

$usuario_id = $_SESSION['usuario_id'];
$correo = $_SESSION['email'];

// Obtener préstamos del usuario
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
    <title>Mis Préstamos</title>
    <link rel="stylesheet" href="css/devolver_libro.css">
    <link rel="icon" href="icono/favicon.ico" type="image/x-icon">
</head>
<body>
    <div class="container">
        <div class="login">
            <!-- Menú de navegación -->
            <div class="menu">
                <a href="contenido.php" class="menu-button">Inicio</a>
                <a href="buscar.php" class="menu-button">Buscar y Solicitar Libros</a>
                <a href="devolver_libro.php" class="menu-button active">Regresar libros</a>
            </div>

            <!-- Contenido principal -->
            <div class="content">
                <h1>Mis Préstamos</h1>
                
                <div class="usuario-info">
                    <p><strong>ID de usuario:</strong> <?= htmlspecialchars($usuario_id) ?></p>
                    <p><strong>Correo:</strong> <?= htmlspecialchars($correo) ?></p>
                </div>

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
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($prestamos as $prestamo): ?>
                                    <tr class="fade-in-row">
                                        <td><?= htmlspecialchars($prestamo['titulo']) ?></td>
                                        <td><?= htmlspecialchars($prestamo['fecha_prestamo']) ?></td>
                                        <td><?= htmlspecialchars($prestamo['fecha_devolucion']) ?></td>
                                        <td><?= htmlspecialchars($prestamo['estado']) ?></td>
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
