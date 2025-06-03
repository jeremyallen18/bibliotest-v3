<?php

require_once 'db.php';

$token_valido = false;
$error_message = '';
$token = '';

if (!isset($_GET['token']) || empty(trim($_GET['token']))) {
    $error_message = "Token no proporcionado o vacío. Por favor, utiliza el enlace enviado a tu correo.";
} else {
    $token = trim($_GET['token']);

    if (!$conexion) {
        error_log("Error de conexión a la base de datos en cambiar_password.php");
        $error_message = "Error crítico del sistema. No se pudo conectar a la base de datos. Inténtalo más tarde.";
    } else {
        // Verificar si el token es válido y no ha expirado
        $stmt = $conexion->prepare("SELECT id FROM usuarios WHERE token_recuperacion = ? AND token_expira > NOW()");
        if ($stmt === false) {
            error_log("Error al preparar la consulta SELECT en cambiar_password.php: " . $conexion->error);
            $error_message = "Error del sistema al verificar el token. Por favor, inténtalo más tarde.";
        } else {
            $stmt->bind_param("s", $token);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $token_valido = true;
                // $user = $result->fetch_assoc(); // Podrías obtener el ID aquí si lo necesitaras
            } else {
                $error_message = "El enlace de recuperación es inválido, ha expirado o ya ha sido utilizado. Por favor, solicita un nuevo enlace.";
            }
            $stmt->close();
        }
    }
}

// Si no es un token válido, no se debe mostrar el formulario.
// El cierre de la conexión $conexion se manejará al final del script si se abrió.

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar Contraseña</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="icon" href="icono/favicon.ico" type="image/x-icon">
    <style>
        :root {
            --a: 0deg; /* Para la animación del borde */
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            min-height: 100vh; /* Usar min-height para permitir scroll si el contenido es largo */
            background: #252432;
            font-family: 'Poppins', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px; /* Espacio por si el box es muy grande en pantallas pequeñas */
        }
        .box {
            position: relative;
            width: 100%;
            max-width: 400px;
            padding: 2px; /* Grosor del borde animado */
            border-radius: 20px;
            background: repeating-conic-gradient(from var(--a), #ff2770 0%, #ff2770 5%, transparent 5%, transparent 40%, #ff2770 50%);
            animation: rotate 4s linear infinite;
            filter: drop-shadow(0 15px 50px #000);
        }
        .box::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 20px;
            background: repeating-conic-gradient(from var(--a), #45f3ff 0%, #45f3ff 5%, transparent 5%, transparent 40%, #45f3ff 50%);
            animation: rotate 4s linear infinite;
            animation-delay: -1s;
        }
        .box::after { /* Contenedor interior estático del borde */
            content: '';
            position: absolute;
            inset: 4px; /* Debe ser mayor que el padding de .box para crear el efecto de borde interno */
            background: #2d2d39; /* Color de fondo del área interna */
            border-radius: 15px; /* Menor que el de .box para que se vea el borde */
            z-index: 0;
        }
        @keyframes rotate {
            0% { --a: 0deg; }
            100% { --a: 360deg; }
        }
        .content-wrapper { /* Renombrado de form-container para claridad */
            position: relative;
            z-index: 1; /* Encima del ::after */
            padding: 40px 30px;
            color: #fff;
            text-align: center;
            background: #2d2d39; /* Mismo color que ::after para un fondo sólido del contenido */
            border-radius: 15px; /* Igual que ::after */
        }
        .content-wrapper h2 {
            margin-bottom: 25px;
            font-weight: 600;
            color: #fff;
        }
        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }
        label {
            display: block;
            margin-bottom: 8px;
            color: #ccc;
            font-size: 0.9em;
        }
        input[type="password"], input[type="text"] /* Para el token si no fuera hidden */ {
            width: 100%;
            padding: 12px 15px;
            border-radius: 8px;
            border: 1px solid #444; /* Un borde sutil */
            outline: none;
            background: #1f1f27;
            color: #fff;
            font-size: 1em;
        }
        input[type="password"]:focus {
            border-color: #45f3ff;
        }
        .submit-button {
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            background-color: #45f3ff;
            color: #1f1f27; /* Texto oscuro para contraste */
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s, color 0.3s;
            width: 100%;
            font-size: 1em;
            margin-top: 10px;
        }
        .submit-button:hover {
            background-color: #2e88d0;
            color: #fff;
        }
        .error-message p, .info-message p {
            color: #ff6b6b; /* Rojo para errores */
            margin-bottom: 15px;
            line-height: 1.6;
        }
        .info-message p {
            color: #45f3ff; /* Azul/Cian para información */
        }
        .message-link {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 15px;
            background-color: #45f3ff;
            color: #1f1f27;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 500;
        }
        .message-link:hover {
            background-color: #2e88d0;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="box">
        <div class="content-wrapper">
            <?php if ($token_valido): ?>
                <h2>Restablecer Contraseña</h2>
                <form id="resetForm" action="actualizar_password.php" method="post">
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

                    <div class="form-group">
                        <label for="password">Nueva Contraseña:</label>
                        <input type="password" name="password" id="password" required minlength="8" placeholder="Mínimo 8 caracteres">
                    </div>

                    <div class="form-group">
                        <label for="password_confirm">Confirmar Nueva Contraseña:</label>
                        <input type="password" name="password_confirm" id="password_confirm" required minlength="8" placeholder="Repite la contraseña">
                    </div>
                    <p id="passwordError" class="error-message" style="display:none; margin-bottom: 15px; font-size: 0.9em;"></p>
                    <button type="submit" class="submit-button">Actualizar Contraseña</button>
                </form>
            <?php else: ?>
                <h2>Enlace Inválido o Expirado</h2>
                <div class="error-message">
                    <p><?php echo htmlspecialchars($error_message); ?></p>
                </div>
                <a href="/biblioteca/recuperar.php" class="message-link">Solicitar Nuevo Enlace</a>
                <a href="/biblioteca/index.html" class="message-link" style="margin-left:10px;">Volver al Inicio</a>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($token_valido): ?>
    <script>
        document.getElementById('resetForm').addEventListener('submit', function(event) {
            const password = document.getElementById('password').value;
            const passwordConfirm = document.getElementById('password_confirm').value;
            const passwordError = document.getElementById('passwordError');
            
            // Validar longitud mínima (aunque el HTML ya lo hace, es bueno reforzar)
            if (password.length < 8) {
                passwordError.textContent = 'La contraseña debe tener al menos 8 caracteres.';
                passwordError.style.display = 'block';
                event.preventDefault();
                return;
            }

            if (password !== passwordConfirm) {
                passwordError.textContent = 'Las contraseñas no coinciden.';
                passwordError.style.display = 'block';
                event.preventDefault(); // Detener el envío del formulario
            } else {
                passwordError.style.display = 'none';
            }
        });
    </script>
    <?php endif; ?>
</body>
</html>
<?php
if (isset($conexion) && $conexion instanceof mysqli) {
    $conexion->close();
}
?>