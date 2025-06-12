<?php
// recuperar.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Contraseña</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="icon" href="icono/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="css/recuperar.css">
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
        <div class="content">
            <h2>Recuperar Contraseña</h2>
            <form action="enviar_token.php" method="post">
                <label for="email">Correo electrónico:</label>
                <input type="email" name="email" id="email" required>
                <button type="submit">Enviar enlace</button>
            </form>
            <div class="nav-buttons">
                <a href="/biblioteca/index.html" class="nav-button">Volver al inicio</a>
            </div>
        </div>
    </div>
</body>
</html>
