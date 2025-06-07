<?php
// inicio_sesion(); // Opcional: si planeas usar mensajes flash y redirecciones más complejas
require_once 'db.php';

$page_title = "Resultado de Actualización";
$message = "";
$message_type = "error"; // 'error' o 'success'

if (!$conexion) {
    error_log("Error de conexión a la base de datos en actualizar_password.php");
    $message = "Error crítico del sistema. No se pudo conectar a la base de datos. Inténtalo más tarde.";
    // No se puede continuar sin conexión a BD
} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = isset($_POST["token"]) ? trim($_POST["token"]) : '';
    $password = isset($_POST["password"]) ? $_POST["password"] : ''; // No hacer trim a la contraseña en sí
    $password_confirm = isset($_POST["password_confirm"]) ? $_POST["password_confirm"] : '';

    // 1. Validación de datos de entrada
    if (empty($token) || empty($password) || empty($password_confirm)) {
        $message = "Todos los campos son obligatorios (token, contraseña y confirmación). Por favor, vuelve a intentarlo desde el enlace de tu correo.";
    } elseif (strlen($password) < 8) {
        $message = "La nueva contraseña debe tener al menos 8 caracteres. <a href='cambiar_password.php?token=" . htmlspecialchars($token) . "'>Volver a intentar</a>.";
    } elseif ($password !== $password_confirm) {
        $message = "Las contraseñas no coinciden. <a href='cambiar_password.php?token=" . htmlspecialchars($token) . "'>Volver a intentar</a>.";
    } else {
        // 2. Re-verificación del Token (seguridad adicional)
        $checkStmt = $conexion->prepare("SELECT id FROM usuarios WHERE token_recuperacion = ? AND token_expira > NOW()");
        if ($checkStmt === false) {
            error_log("Error al preparar la consulta SELECT en actualizar_password.php: " . $conexion->error);
            $message = "Error del sistema al verificar el token. Inténtalo más tarde.";
        } else {
            $checkStmt->bind_param("s", $token);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();

            if ($checkResult->num_rows === 0) {
                $message = "El token es inválido, ha expirado o ya fue utilizado. Por favor, <a href='/biblioteca/recuperar.php'>solicita un nuevo enlace de recuperación</a>.";
            } else {
                // Token válido, proceder a hashear la contraseña y actualizar
                $user = $checkResult->fetch_assoc(); // Obtenemos el id del usuario
                $user_id = $user['id'];
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // 3. Actualizar la contraseña e invalidar el token
                // Usar el user_id para mayor precisión en la actualización, aunque el token también es único en este punto.
                // Actualizamos usando el token para asegurar que este token específico se use y se invalide.
                $updateStmt = $conexion->prepare("UPDATE usuarios SET password = ?, token_recuperacion = NULL, token_expira = NULL WHERE token_recuperacion = ? AND id = ?");
                if ($updateStmt === false) {
                    error_log("Error al preparar la consulta UPDATE en actualizar_password.php: " . $conexion->error);
                    $message = "Error del sistema al intentar actualizar la contraseña. Inténtalo más tarde.";
                } else {
                    $updateStmt->bind_param("ssi", $hashed_password, $token, $user_id);
                    $updateStmt->execute();

                    if ($updateStmt->affected_rows > 0) {
                        $message = "Contraseña actualizada correctamente. Ahora puedes <a href='/biblioteca/index.html'>iniciar sesión</a> con tu nueva contraseña.";
                        $message_type = "success";
                        $page_title = "Contraseña Actualizada";
                    } else {
                        // Esto podría ocurrir si el token fue usado/invalidado entre la verificación y la actualización,
                        // o si hubo un error no capturado.
                        error_log("Error al actualizar contraseña para token $token, user_id $user_id. Affected_rows: " . $updateStmt->affected_rows . ", Error: " . $updateStmt->error);
                        $message = "No se pudo actualizar la contraseña en este momento. Es posible que el enlace ya no sea válido. Por favor, <a href='/biblioteca/recuperar.php'>intenta solicitar un nuevo enlace</a>.";
                    }
                    $updateStmt->close();
                }
            }
            $checkStmt->close();
        }
    }
} else {
    // Si no es POST, redirigir al formulario de cambio o a la página de recuperación.
    // Esto previene el acceso directo al script sin datos.
    header("Location: /biblioteca/recuperar.php"); // O a una página de error general
    exit();
}

if (isset($conexion) && $conexion instanceof mysqli) {
    $conexion->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="icon" href="icono/favicon.ico" type="image/x-icon">
    <style>
        :root { --a: 0deg; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            min-height: 100vh;
            background: #252432;
            font-family: 'Poppins', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .box {
            position: relative;
            width: 100%;
            max-width: 450px; /* Un poco más ancho para mensajes */
            padding: 2px;
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
        .box::after {
            content: '';
            position: absolute;
            inset: 4px;
            background: #2d2d39;
            border-radius: 15px;
            z-index: 0;
        }
        @keyframes rotate {
            0% { --a: 0deg; }
            100% { --a: 360deg; }
        }
        .content-wrapper { /* Renombrado de form-container */
            position: relative;
            z-index: 1;
            padding: 40px 30px;
            color: #fff;
            text-align: center;
            background: #2d2d39;
            border-radius: 15px;
        }
        .content-wrapper h2 {
            margin-bottom: 25px;
            font-weight: 600;
            color: #fff;
        }
        .message-area p {
            margin-top: 10px;
            line-height: 1.6;
            font-size: 1.05em;
        }
        .message-area p.success {
            color: #a7dda7; /* Verde claro para éxito */
        }
        .message-area p.error {
            color: #ff8a8a; /* Rojo claro para error */
        }
        .message-area a {
            color: #45f3ff;
            text-decoration: none;
            font-weight: 500;
        }
        .message-area a:hover {
            text-decoration: underline;
            color: #89faff;
        }
        .action-link { /* Botón estilizado como enlace */
            display: inline-block;
            margin-top: 25px;
            padding: 10px 20px;
            background-color: #45f3ff;
            color: #1f1f27;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s, color 0.3s;
        }
        .action-link:hover {
            background-color: #2e88d0;
            color: #fff;
        }

    </style>
</head>
<body>
    <div class="box">
        <div class="content-wrapper">
            <h2><?php echo htmlspecialchars($page_title); ?></h2>
            <div class="message-area">
                <p class="<?php echo $message_type; ?>"><?php echo $message; /* El HTML en $message ya está construido, así que no se usa htmlspecialchars aquí */ ?></p>
            </div>
            <?php if ($message_type === 'error' && !empty($token) && (strpos($message, 'Volver a intentar') !== false) ): ?>
                <?php elseif ($message_type === 'error'): ?>
                <a href="/biblioteca/recuperar.php" class="action-link">Solicitar Nuevo Enlace</a>
            <?php endif; ?>
             <a href="/biblioteca/index.html" class="action-link" style="margin-left: <?php echo ($message_type === 'error' && empty($token) ) ? '0' : '10px'; ?>">Ir a Inicio</a>
        </div>
    </div>
</body>
</html>