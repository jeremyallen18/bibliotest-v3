<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login_admin.php");
    exit;
}
require 'db.php';

$query = "SELECT p.*, u.email as usuario_email, l.titulo as libro_titulo 
          FROM prestamos p 
          JOIN usuarios u ON p.usuario_id = u.id
          JOIN libros l ON p.libro_id = l.id
          WHERE p.estado != 'Devuelto'";

$result = $conexion->query($query);
if (!$result) {
    die("Error en la consulta: " . $conexion->error);
}

$prestamos = $result->fetch_all(MYSQLI_ASSOC);
$result->free();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Préstamos y Bloqueo de Usuarios</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/admin_prestamos.css">
    <link rel="icon" href="icono/favicon.ico" type="image/x-icon">
</head>
<body>

<!-- Sidebar integrado directamente -->
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="admin-profile">
            <div class="admin-avatar">
                <i class="fas fa-user-cog"></i>
            </div>
            <div class="admin-info">
                <h3><?= htmlspecialchars($_SESSION['admin_name'] ?? 'Administrador') ?></h3>
                <p>Administrador</p>
            </div>
        </div>

        <ul class="menu-admin">
            <li><a href="admin_panel.php"><i class="fas fa-tachometer-alt"></i> Inicio</a></li>
            <li><a href="admin_libros.php"><i class="fas fa-book"></i> Libros</a></li>
            <li><a href="admin_noticias.php"><i class="fas fa-newspaper"></i> Noticias</a></li>
            <li><a href="admin_prestamos.php"><i class="fas fa-exchange-alt"></i> Préstamos</a></li>
            <li><a href="admin_usuarios.php" class="active"><i class="fas fa-users"></i> Usuarios</a></li>
        </ul>

        <button class="logout-btn" onclick="window.location.href='logout.php'">
            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
        </button>
        
        <div class="menu-toggle">
            <i class="fas fa-bars"></i>
        </div>
    </div>
<!-- Fin del sidebar -->

<div class="main-content">
    <div class="header">
        <h2 class="page-title">
            <i class="fas fa-exchange-alt"></i> Préstamos y Bloqueo de Usuarios
        </h2>
    </div>

    <div class="button-group">
        <a class="button secondary" href="admin_panel.php">
            <i class="fas fa-arrow-left"></i> Volver al Menú
        </a>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Libro</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($prestamos)): ?>
                    <tr>
                        <td colspan="4" class="no-data">No hay préstamos registrados</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($prestamos as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['usuario_email']) ?></td>
                        <td><?= htmlspecialchars($p['libro_titulo']) ?></td>
                        <td>
                            <span class="status-badge <?= strtolower($p['estado']) ?>">
                                <?= htmlspecialchars($p['estado']) ?>
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a class="action-btn block-btn" href="bloquear_usuario.php?id=<?= $p['usuario_id'] ?>" onclick="return confirmBlock()">
                                    <i class="fas fa-lock"></i> Bloquear Usuario
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="js/admin_prestamos.js"></script>
</body>
</html>
