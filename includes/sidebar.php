<?php
// Verificación de sesión para seguridad
if (!isset($_SESSION['admin_id'])) {
    header("Location: login_admin.php");
    exit;
}
?>

<!-- Sidebar -->
  <link rel="stylesheet" href="css/admin_style.css">
  <link rel="icon" href="icono/favicon.ico" type="image/x-icon">
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

    <ul class="admin-menu">
        <li><a href="admin_panel.php"><i class="fas fa-tachometer-alt"></i> Panel</a></li>
        <li><a href="admin_noticias.php"><i class="fas fa-newspaper"></i> Noticias</a></li>
        <li><a href="admin_libros.php"><i class="fas fa-book"></i> Libros</a></li>
        <li><a href="admin_usuarios.php"><i class="fas fa-users"></i> Usuarios</a></li>
        <li><a href="admin_prestamos.php"><i class="fas fa-exchange-alt"></i> Préstamos</a></li>
    </ul>

    <button class="logout-btn" onclick="window.location.href='logout_admin.php'">
        <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
    </button>
    
    <div class="menu-toggle">
        <i class="fas fa-bars"></i>
    </div>
</div>