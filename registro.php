<?php
// PHP aquí arriba del HTML
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email = $_POST['email'];
  $password = $_POST['password'];
  $confirm_password = $_POST['confirm_password'];

  if ($password !== $confirm_password) {
    $mensaje = "Las contraseñas no coinciden.";
  } else {
    $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
      $mensaje = "El correo ya está registrado.";
    } else {
      $password_hash = password_hash($password, PASSWORD_DEFAULT);
      $stmt = $conexion->prepare("INSERT INTO usuarios (email, password) VALUES (?, ?)");
      $stmt->bind_param("ss", $email, $password_hash);

      if ($stmt->execute()) {
        $mensaje = "Registro exitoso. Ahora puedes iniciar sesión.";
      } else {
        $mensaje = "Error al registrar el usuario.";
      }
    }
    $stmt->close();
  }
  $conexion->close();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Registro Biblioteca</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
  <link rel="icon" href="icono/favicon.ico" type="image/x-icon">
  <link rel="stylesheet" href="css/estilo.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link rel="preload" href="videos/background.mp4" as="video" type="video/mp4">
</head>

<body>
  <style>
    .loginBx h2,
    .group p,
    .group a {
      color: palevioletred;
    }

    .logo {
      display: block;
      margin: 0 auto 20px;
      width: 100px;
      /* Ajusta el tamaño si lo necesitas */
    }
  </style>
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
        <div class="titulo-registro" style="display: flex; align-items: center; gap: 10px; justify-content: center; color: #ff2770;">
  <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#ff2770" viewBox="0 0 24 24">
  <path d="M21 4H7C5.9 4 5 4.9 5 6v14c0 .55.45 1 1 1s1-.45 1-1V6h14v14c0 .55.45 1 1 1s1-.45 1-1V6c0-1.1-.9-2-2-2zm-9 2H3c-1.1 0-2 .9-2 2v12c0 .55.45 1 1 1s1-.45 1-1V8h10V6z"/>
</svg>
  <h2 style="margin: 0;">Registro</h2>
</div>
        <?php if (isset($mensaje))
          echo "<p>$mensaje</p>"; ?>

        <form action="registro.php" method="POST">
          <input type="email" name="email" placeholder="Correo electrónico" required>
          <input type="password" name="password" placeholder="Contraseña" required>
          <input type="password" name="confirm_password" placeholder="Confirmar Contraseña" required>
          <input type="submit" value="Registrar">
        </form>

        <div class="group">
          <p>¿Ya tienes cuenta? <a href="index.html">Inicia sesión</a></p>
        </div>
      </div>
    </div>
  </div>
</body>

</html>