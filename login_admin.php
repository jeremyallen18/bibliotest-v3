<?php
// Habilitar reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include 'db.php';

$error = ''; // Variable para almacenar errores

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validación de usuario
    if (empty($username) || empty($password)) {
        $error = "Todos los campos son obligatorios.";
    } else {
        // Consulta para obtener los datos del administrador
        $stmt = $conexion->prepare("SELECT * FROM administradores WHERE email = ?");
        if (!$stmt) {
            $error = "Error en la preparación de la consulta: " . $conexion->error;
        } else {
            $stmt->bind_param("s", $username);
            if (!$stmt->execute()) {
                $error = "Error al ejecutar la consulta: " . $stmt->error;
            } else {
                $result = $stmt->get_result();

                if ($result->num_rows === 1) {
                    $admin = $result->fetch_assoc();

                    // Verificación de la contraseña
                    if (password_verify($password, $admin['password'])) {
                        // Guardar datos de sesión
                        $_SESSION['admin_id'] = $admin['id'];
                        $_SESSION['email'] = $admin['email'];

                        // Redirigir al panel de administración
                        header("Location: admin_panel.php");
                        exit();
                    } else {
                        $error = "Contraseña incorrecta.";
                    }
                } else {
                    $error = "Usuario no encontrado.";
                }
            }
            $stmt->close();
        }
        $conexion->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Login Administrador</title>
  <link rel="stylesheet" href="css/estilo.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>

<div class="box">
  <div class="login">
    <!-- Texto inicial que desaparecerá -->
    <div class="initial-text">
      <p>Acceso exclusivo para administradores</p>
      <div class="arrow-down">
        <i class="fas fa-chevron-down"></i>
      </div>
    </div>

    <!-- Contenido principal que permanece visible -->
    <div class="login-content">
      <h2 class="login-title">
        <i class="fa-solid fa-user-shield"></i>
        Acceso Administrador
        <i class="fa-solid fa-key"></i>
      </h2>

      <?php if (!empty($error)): ?>
        <div class="error-msg">
          <i class="fas fa-exclamation-circle"></i>
          <?php echo htmlspecialchars($error); ?>
        </div>
      <?php endif; ?>

      <div class="loginBx">
        <form action="login_admin.php" method="POST">
          <div class="input-group">
            <i class="fas fa-envelope"></i>
            <input type="text" name="email" placeholder="Correo administrativo" required>
          </div>
          <div class="input-group">
            <i class="fas fa-lock"></i>
            <input type="password" name="password" placeholder="Contraseña de administrador" required>
          </div>
          <input type="submit" value="Ingresar como Admin">
        </form>

        <div class="group">
          <a href="index.html">Volver a usuario normal</a>
        </div>
      </div>
    </div>

  </div>
</div>

</body>
</html>