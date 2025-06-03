<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login_admin.php");
    exit;
}
session_regenerate_id(true); // Seguridad extra
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Panel de Administrador</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="css/admin_panel.css">
    <script src="js/admin_panel.js" defer></script>
    <link rel="icon" href="icono/favicon.ico" type="image/x-icon">
</head>

<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="admin-profile">
                <div class="admin-avatar">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div class="admin-info">
                    <h3>Administrador</h3>
                    <p>Rol: Super Admin</p>
                </div>
            </div>

            <ul class="admin-menu">
                <li><a href="#" class="active"><i class="fas fa-tachometer-alt"></i> Inicio</a></li>
                <li><a href="admin_libros.php"><i class="fas fa-book"></i> Libros</a></li>
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
                <h1 class="page-title"><i class="fas fa-tachometer-alt"></i> Panel de Administración</h1>
                <div class="menu-toggle">
                    <i class="fas fa-bars"></i>
                </div>
            </div>

            <?php
            include("db.php"); // <-- nombre correcto del archivo de conexión
            
            // Libros registrados
            $librosQuery = mysqli_query($conexion, "SELECT COUNT(*) AS total FROM libros");
            $libros = mysqli_fetch_assoc($librosQuery)['total'];

            // Préstamos activos
            $prestamosQuery = mysqli_query($conexion, "SELECT COUNT(*) AS total FROM prestamos WHERE estado = 'pendiente'");
            $prestamos = mysqli_fetch_assoc($prestamosQuery)['total'];

            // Noticias publicadas
            $noticiasQuery = mysqli_query($conexion, "SELECT COUNT(*) AS total FROM noticias");
            $noticias = mysqli_fetch_assoc($noticiasQuery)['total'];
            ?>


            <!-- Stats Cards -->
            <div class="stats-cards">
                <div class="stat-card">
                    <h3><i class="fas fa-book"></i> Libros Registrados</h3>
                    <div class="value"><?= $libros ?></div>
                </div>
                <div class="stat-card">
                    <h3><i class="fas fa-exchange-alt"></i> Préstamos Activos</h3>
                    <div class="value"><?= $prestamos ?></div>
                </div>
                <div class="stat-card">
                    <h3><i class="fas fa-newspaper"></i> Noticias Publicadas</h3>
                    <div class="value"><?= $noticias ?></div>
                </div>
            </div>


            <!-- Quick Actions -->
            <div class="quick-actions">
                <h2 class="section-title"><i class="fas fa-bolt"></i> Acciones Rápidas</h2>
                <div class="action-buttons">
                    <a href="agregar_libro.php?action=add" class="action-btn">
                        <i class="fas fa-plus-circle"></i>
                        <span>Agregar Libro</span>
                    </a>
                    <a href="agregar_noticia.php?action=add" class="action-btn">
                        <i class="fas fa-plus-circle"></i>
                        <span>Crear Noticia</span>
                    </a>
                    <a href="admin_usuarios.php?action=add" class="action-btn">
                        <i class="fas fa-user-lock"></i>
                        <span>Control de Usuario</span>
                    </a>
                    <a href="admin_prestamos.php" class="action-btn">
                        <i class="fas fa-search"></i>
                        <span>Ver Préstamos</span>
                    </a>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="quick-actions">
                <h2 class="section-title"><i class="fas fa-history"></i> Actividad Reciente</h2>
                <p class="recent-placeholder">Aquí iría un listado de actividades recientes...</p>
            </div>
        </main>
    </div>
</body>

</html>