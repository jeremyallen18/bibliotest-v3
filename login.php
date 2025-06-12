<?php
session_start();
include 'db.php';

$error = 'Error en la base de datos, verifica el codigo bro';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Correo inválido.";
    } else {
        $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if ($user['bloqueado'] == 1) {
                $error = "Tu cuenta está bloqueada. Contacta al administrador.";
            } elseif (password_verify($password, $user['password'])) {
                $_SESSION['usuario_id'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['bloqueado'] = $user['bloqueado'];

                header("Location: contenido.php");
                exit;
            } else {
                $error = "Contraseña incorrecta.";
            }
        } else {
            $error = "Correo electrónico no registrado.";
        }

        $stmt->close();
        $conexion->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Login Biblioteca</title>
  <link rel="stylesheet" href="css/estilo.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link rel="preload" href="videos/background.mp4" as="video" type="video/mp4">
</head>
<body>
<div class="corner-images">
    <img src="img\test.png" alt="Imagen izquierda" class="corner-img left-img">
    <img src="img\isc.png" alt="Imagen derecha" class="corner-img right-img">
  </div>
  <div class="video-background">
    <video autoplay muted loop playsinline>
      <source src="videos/background.mp4" type="video/mp4">
      <!-- Fallback para navegadores antiguos -->
      <source src="videos/background.webm" type="video/webm">
    </video>
  </div>
<div class="box">
  <div class="login">
    <div class="loginBx">
      <h2>
        <i class="fa-solid fa-right-to-bracket"></i>
        Iniciar Sesión
        <i class="fa-solid fa-heart"></i>
      </h2>

      <?php if (!empty($error)): ?>
        <div class="error-msg" style="color: red; margin-bottom: 10px;">
          <?php echo htmlspecialchars($error); ?>
        </div>
      <?php endif; ?>

      <form action="login.php" method="POST">
        <input type="email" name="email" placeholder="Correo electrónico" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <input type="submit" value="Ingresar">
      </form>

      <div class="group">
        <a href="recover.html">¿Olvidaste tu contraseña?</a>
        <a href="registro.php">Registrarse</a>
      </div>
    </div>
  </div>
</div>

</body>
</html>
