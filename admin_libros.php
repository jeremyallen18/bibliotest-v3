<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login_admin.php");
    exit;
}
require 'db.php';

$resultado = $conexion->query("SELECT * FROM libros");
$libros = $resultado->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Administrar Libros</title>
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="css/estilos_admin.css">
    <link rel="icon" href="icono/favicon.ico" type="image/x-icon">
</head>

<body>
    <div class="admin-container">
        <!-- Barra de navegacion -->
        <aside class="sidebar">
            <div class="admin-profile">
                <div class="admin-avatar">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div class="admin-info">
                    <h3>Administrador</h3>
                    <p>Bibliotest</p>
                </div>
            </div>

            <ul class="menu-admin">
                <li><a href="admin_panel.php"><i class="fas fa-tachometer-alt"></i> Inicio</a></li>
                <li><a href="admin_libros.php" class="active"><i class="fas fa-book"></i> Libros</a></li>
                <li><a href="admin_noticias.php"><i class="fas fa-newspaper"></i> Noticias</a></li>
                <li><a href="admin_prestamos.php"><i class="fas fa-exchange-alt"></i> Préstamos</a></li>
                <li><a href="admin_usuarios.php"><i class="fas fa-users"></i> Usuarios</a></li>
            </ul>

            <button class="logout-btn" onclick="location.href='logout.php'">
                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
            </button>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="header">
                <h1 class="page-title"><i class="fas fa-book"></i> Administrar Libros</h1>
            </div>

            <div class="button-group">
                <a href="agregar_libro.php" class="button">
                    <i class="fas fa-plus-circle"></i> Agregar Libro
                </a>
                <a href="agregar_libro_digital.php" class="button">
                    <i class="fas fa-file-pdf"></i> Agregar Libro Digital
                </a>
                <a href="admin_panel.php" class="button secondary">
                    <i class="fas fa-arrow-left"></i> Volver al Panel
                </a>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Imagen</th>
                            <th>Descripción</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($libros as $libro): ?>
                            <tr>
                                <td><?= htmlspecialchars($libro['titulo']) ?></td>
                                <td>
                                    <?php if (!empty($libro['imagen'])): ?>
                                        <img src="<?= htmlspecialchars($libro['imagen']) ?>" alt="Portada del libro"
                                            class="book-image">
                                    <?php else: ?>
                                        <div class="book-placeholder"><i class="fas fa-book"></i></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="book-description"
                                        title="<?= htmlspecialchars($libro['descripcion'] ?? 'Sin descripción') ?>">
                                        <?= htmlspecialchars($libro['descripcion'] ?? 'Sin descripción') ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="editar_libro.php?id=<?= $libro['id'] ?>" class="action-btn edit-btn">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
                                        <a href="eliminar_libro.php?id=<?= $libro['id'] ?>" class="action-btn delete-btn"
                                            onclick="return confirm('¿Estás seguro de eliminar este libro?')">
                                            <i class="fas fa-trash-alt"></i> Eliminar
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <script src="js/scripts_admin.js"></script>
</body>

</html>