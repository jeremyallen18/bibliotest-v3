<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login_admin.php");
    exit;
}
require 'db.php';

$result = $conexion->query("SELECT * FROM noticias");
$noticias = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Noticias</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/admin_noticias.css">
    <link rel="icon" href="icono/favicon.ico" type="image/x-icon">
</head>
<body>
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
            <li><a href="admin_noticias.php" class="active"><i class="fas fa-newspaper"></i> Noticias</a></li>
            <li><a href="admin_prestamos.php"><i class="fas fa-exchange-alt"></i> Préstamos</a></li>
            <li><a href="admin_usuarios.php"><i class="fas fa-users"></i> Usuarios</a></li>
        </ul>

        <button class="logout-btn" onclick="window.location.href='logout.php'">
            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
        </button>
        
        <div class="menu-toggle">
            <i class="fas fa-bars"></i>
        </div>
    </div>
    
    <!-- Contenido Principal -->
    <div class="main-content">
        <div class="header">
            <h2 class="page-title">
                <i class="fas fa-newspaper"></i> Administrar Noticias
            </h2>
        </div>

        <div class="button-group">
            <a class="button" href="agregar_noticia.php">
                <i class="fas fa-plus"></i> Agregar Noticia
            </a>
            <a class="button secondary" href="admin_panel.php">
                <i class="fas fa-home"></i> Menú Principal
            </a>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($noticias)): ?>
                        <tr>
                            <td colspan="3" class="no-data">No hay noticias registradas</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($noticias as $noticia): ?>
                        <tr>
                            <td><?= htmlspecialchars($noticia['titulo']) ?></td>
                            <td><?= date('d/m/Y', strtotime($noticia['fecha'])) ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a class="action-btn edit-btn" href="editar_noticia.php?id=<?= $noticia['id'] ?>">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    <a class="action-btn delete-btn" href="eliminar_noticia.php?id=<?= $noticia['id'] ?>" onclick="return confirmDelete()">
                                        <i class="fas fa-trash"></i> Eliminar
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

    <script src="js/admin_noticias.js"></script>
</body>
</html>