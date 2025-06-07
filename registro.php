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
  <link rel="stylesheet" href="css/estilo2.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link rel="preload" href="videos/background.mp4" as="video" type="video/mp4">
  <!-- <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: "Poppins", sans-serif;
    }

    body {
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      background: #252432;
    }

    @property --a {
      syntax: "<angle>";
      inherits: false;
      initial-value: 0deg;
    }

    .box {
      position: relative;
      width: 500px;
      height: 700px;
      background: repeating-conic-gradient(
        from var(--a),
        #ff2770 0%,
        #ff2770 5%,
        transparent 5%,
        transparent 40%,
        #ff2770 50%
      );
      filter: drop-shadow(0 15px 50px #000);
      animation: rotating 4s linear infinite;
      border-radius: 20px;
      display: flex;
      justify-content: center;
      align-items: center;
      transition: 0.5s;
    }

    @keyframes rotating {
      0% {
        --a: 0deg;
      }
      100% {
        --a: 360deg;
      }
    }

    .box::before {
      content: "";
      position: absolute;
      width: 100%;
      height: 100%;
      background: repeating-conic-gradient(
        from var(--a),
        #45f3ff 0%,
        #45f3ff 5%,
        transparent 5%,
        transparent 40%,
        #45f3ff 50%
      );
      filter: drop-shadow(0 15px 50px #000);
      border-radius: 20px;
      animation: rotating 4s linear infinite;
      animation-delay: -1s;
    }

    .box::after {
      content: "";
      position: absolute;
      inset: 4px;
      background: #2d2d39;
      border-radius: 15px;
      border: 8px solid #25252b;
    }

    .login {
      position: absolute;
      inset: 60px;
      display: flex;
      justify-content: center;
      align-items: center;
      border-radius: 10px;
      flex-direction: column;
      background: rgba(0, 0, 0, 0.2);
      z-index: 1000;
      box-shadow: inset 0 10px 20px rgba(0, 0, 0, 0.5);
      border-bottom: 2px solid rgba(255, 255, 255, 0.5);
      transition: 0.5s;
      color: #fff;
      overflow: hidden;
    }

    .loginBx {
      position: relative;
      display: flex;
      justify-content: center;
      align-items: center;
      flex-direction: column;
      transform: translateY(30px);
      gap: 20px;
      width: 70%;
      transition: 0.5s;
    }

    .loginBx h2 {
      text-transform: uppercase;
      letter-spacing: 0.2em;
      font-weight: 600;
    }

    .loginBx h2 i {
      color: #ff2770;
      text-shadow: 0 0 5px #ff2770, 0 0 30px #ff2770;
    }

    .loginBx input,
    .loginBx select {
      width: 100%;
      padding: 10px 20px;
      outline: none;
      font-size: 1em;
      color: #fff;
      background: rgba(0, 0, 0, 0.1);
      border: 2px solid #fff;
      border-radius: 30px;
    }

    .loginBx input::placeholder {
      color: #999;
    }

    .loginBx select option {
      color: #000;
    }

    .loginBx input[type="submit"] {
      background: #45f3ff;
      border: none;
      font-weight: 500;
      color: #111;
      cursor: pointer;
      transition: 0.5s;
    }

    .loginBx input[type="submit"]:hover {
      box-shadow: 0 0 10px #45f3ff, 0 0 60px #45f3ff;
    }

    .group {
      display: flex;
      width: 100%;
      justify-content: space-between;
    }

    .group a {
      color: #fff;
      text-decoration: none;
    }

    .group a:nth-child(2) {
      color: #ff2770;
      font-weight: 600;
    }

    p {
      text-align: center;
      color: #ff2770;
      font-weight: 500;
      margin-top: -10px;
    }
  </style> -->
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
        <h2>Registro</h2>
        <?php if (isset($mensaje)) echo "<p>$mensaje</p>"; ?>

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