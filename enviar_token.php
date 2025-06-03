<?php
// Iniciar sesión si planeas usar mensajes flash o variables de sesión en páginas relacionadas.
// session_start(); // Descomentar si es necesario

require_once 'db.php'; // Este archivo debe definir y configurar $conexion

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
// SMTP ya está importado por autoload, no se necesita 'use PHPMailer\PHPMailer\SMTP;' explícitamente a menos que accedas a sus constantes directamente sin el namespace completo.
require 'vendor/autoload.php';

// --- INICIO DE LA FUNCIÓN RENDERMESSAGEBOX ---
// Es buena práctica definir las funciones antes de su primer uso o al principio del script.
function renderMessageBox($title, $message, $buttonText, $buttonLink, $showReturnToHome = true) {
    $homeLinkHtml = $showReturnToHome ? '<a href="/biblioteca/index.html" class="link">Volver al inicio</a>' : '';
    $html = <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>$title</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            height: 100vh;
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
            max-width: 400px;
            padding: 2px; /* Ajustado para el borde animado */
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
            inset: 4px; /* Espacio para el borde */
            background: #2d2d39;
            border-radius: 15px; /* Redondeado interior */
            /* border: 8px solid #25252b; Esta línea puede ser redundante o conflictiva con el efecto de borde animado */
            z-index: 0;
        }
        @property --a {
            syntax: '<angle>';
            inherits: false;
            initial-value: 0deg;
        }
        @keyframes rotate {
            0% { --a: 0deg; }
            100% { --a: 360deg; }
        }
        .content {
            position: relative;
            z-index: 1; /* Asegura que el contenido esté sobre el pseudo-elemento ::after */
            padding: 40px 30px;
            color: #fff;
            text-align: center;
            background: #2d2d39; /* Fondo para el contenido dentro del borde animado */
            border-radius: 15px; /* Coincide con el borde interior de ::after */
        }
        h2 {
            margin-bottom: 20px;
            color: #fff;
            font-size: 24px;
        }
        p {
            color: #ccc;
            margin-bottom: 25px;
            line-height: 1.6;
        }
        a.button {
            display: inline-block;
            padding: 12px 25px;
            background-color: #45f3ff;
            color: #000;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
        a.button:hover {
            background-color: #2e88d0;
            color: #fff;
            transform: translateY(-2px);
        }
        a.link {
            display: block;
            margin-top: 15px;
            color: #ff2770;
            text-decoration: none;
            transition: color 0.3s;
        }
        a.link:hover {
            color: #ff6090;
        }
        @media (max-width: 480px) {
            .content {
                padding: 30px 20px;
            }
            h2 {
                font-size: 20px;
            }
            a.button {
                padding: 10px 20px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="box">
        <div class="content">
            <h2>$title</h2>
            <p>$message</p>
            <a href="$buttonLink" class="button">$buttonText</a>
            $homeLinkHtml
        </div>
    </div>
</body>
</html>
HTML;
    echo $html;
}


// Configuración de Gmail (IMPORTANTE: usa variables de entorno en producción)
define('GMAIL_USER', 'jeremi.narvaez@gmail.com'); // Reemplaza con tu correo real
define('GMAIL_PASS', 'hmtz crcs ifll xztu'); // Reemplaza con tu contraseña de aplicación
define('GMAIL_FROM', 'jeremi.narvaez@gmail.com'); // Puede ser el mismo que GMAIL_USER
define('GMAIL_FROM_NAME', 'Biblioteca digital');
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_SECURE_METHOD', PHPMailer::ENCRYPTION_STARTTLS); // 'tls' o PHPMailer::ENCRYPTION_STARTTLS

// Validar conexión a la base de datos (asumiendo que db.php define $conexion)
if (!$conexion) {
    error_log("Error de conexión a la base de datos en recuperar_contraseña.php"); // Log para el admin
    renderMessageBox(
        "Error del Sistema",
        "No se pudo conectar a la base de datos. Por favor, inténtalo más tarde.",
        "Volver al inicio",
        "/biblioteca/index.html",
        false // No mostrar el segundo enlace "Volver al inicio"
    );
    exit;
}

// Verificar si la solicitud es POST y tiene email
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    renderMessageBox(
        "Acceso Denegado",
        "Método de solicitud no permitido.",
        "Ir a Recuperación",
        "/biblioteca/recuperar.php"
    );
    exit;
}

if (!isset($_POST['email']) || empty(trim($_POST['email']))) {
    renderMessageBox(
        "Información Faltante",
        "No se proporcionó una dirección de correo electrónico.",
        "Intentar de nuevo",
        "/biblioteca/recuperar.php"
    );
    exit;
}

$email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
if (!$email) {
    renderMessageBox(
        "Correo inválido",
        "La dirección de correo electrónico proporcionada no es válida.",
        "Intentar de nuevo",
        "/biblioteca/recuperar.php"
    );
    exit;
}

// Buscar usuario en la base de datos
$query = "SELECT id, email FROM usuarios WHERE email = ?"; // Asumiendo que el nombre del campo es 'email'
$stmt = $conexion->prepare($query);

if ($stmt === false) {
    error_log("Error al preparar la consulta SELECT: " . $conexion->error);
    renderMessageBox(
        "Error del Sistema",
        "Ocurrió un error al procesar tu solicitud. Por favor, inténtalo más tarde.",
        "Intentar de nuevo",
        "/biblioteca/recuperar.php"
    );
    exit;
}

$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row) {
    $token = bin2hex(random_bytes(32));
    $token_expira = date('Y-m-d H:i:s', strtotime('+1 hour'));

    // Determinar el protocolo y host dinámicamente
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'];
    $enlace = "$protocol://$host/biblioteca/cambiar_password.php?token=$token"; // Asegúrate que esta ruta sea correcta

    $nombreUsuario = htmlspecialchars($row['email']); // O algún campo 'nombre' si lo tienes
    $subject = "Instrucciones para restablecer tu contraseña - Biblioteca";

    // Plantilla de correo mejorada
    $messageBody = "
        <!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <title>$subject</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f4f4f4; margin: 0; padding: 0; }
                .container { max-width: 600px; margin: 20px auto; padding: 20px; background-color: #fff; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
                .header { background-color: #007bff; color: white; padding: 20px; text-align: center; border-top-left-radius: 8px; border-top-right-radius: 8px; }
                .header h2 { margin: 0; color: white; }
                .content { padding: 20px; }
                .content p { margin-bottom: 15px; }
                .button { display: inline-block; padding: 12px 25px; background-color: #007bff; color: white !important; text-decoration: none; border-radius: 5px; margin: 20px 0; text-align: center; }
                .button:hover { background-color: #0056b3; }
                .footer { margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee; font-size: 12px; color: #777; text-align: center; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>Restablecimiento de Contraseña</h2>
                </div>
                <div class='content'>
                    <p>Hola $nombreUsuario,</p>
                    <p>Hemos recibido una solicitud para restablecer la contraseña de tu cuenta en nuestro sistema de Biblioteca.</p>
                    <p>Por favor, haz clic en el siguiente botón para continuar con el proceso:</p>
                    <p style='text-align: center;'><a href='$enlace' class='button'>Restablecer Contraseña</a></p>
                    <p>Si no puedes hacer clic en el botón, copia y pega la siguiente URL en tu navegador:</p>
                    <p><small style='word-break: break-all;'>$enlace</small></p>
                    <p>Este enlace de restablecimiento es válido por 1 hora. Si no solicitaste este cambio, puedes ignorar este mensaje de forma segura.</p>
                </div>
                <div class='footer'>
                    <p>Este es un mensaje automático, por favor no respondas a este correo.</p>
                    <p>&copy; " . date('Y') . " Sistema de Biblioteca. Todos los derechos reservados.</p>
                </div>
            </div>
        </body>
        </html>";

    $mail = new PHPMailer(true);

    try {
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = GMAIL_USER;
        $mail->Password = GMAIL_PASS; // Usa una contraseña de aplicación si tienes 2FA en Gmail
        $mail->SMTPSecure = SMTP_SECURE_METHOD;
        $mail->Port = SMTP_PORT;

        // Configuración adicional para mejorar la entrega y seguridad
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => true, // Mantenido por defecto, pero Gmail puede no requerirlo estrictamente para STARTTLS
                'verify_peer_name' => true,
                'allow_self_signed' => false, // Importante que sea false
                // 'cafile' => '/etc/ssl/certs/ca-certificates.crt', // Podría ser necesario en algunos entornos
            ]
        ];
        // Es posible que para Gmail con STARTTLS en el puerto 587, no necesites configurar 'ssl' en SMTPOptions,
        // o podrías necesitar ajustarlo si hay problemas de certificado.
        // PHPMailer maneja bien la negociación TLS.

        $mail->CharSet = 'UTF-8';

        // Remitente y destinatario
        $mail->setFrom(GMAIL_FROM, GMAIL_FROM_NAME);
        $mail->addAddress($email); // El nombre es opcional
        $mail->addReplyTo(GMAIL_FROM, GMAIL_FROM_NAME); // Dirección a la que responder

        // Contenido del correo
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $messageBody;
        $mail->AltBody = strip_tags(preg_replace('/<br\s*\/?>/i', "\n", $messageBody));


        $mail->send();

        // Guardar token en la base de datos
        $updateQuery = "UPDATE usuarios SET token_recuperacion = ?, token_expira = ? WHERE email = ?";
        $stmtUpdate = $conexion->prepare($updateQuery);
        if ($stmtUpdate === false) {
            throw new Exception('Error al preparar la consulta de actualización: ' . $conexion->error);
        }
        $stmtUpdate->bind_param("sss", $token, $token_expira, $email);
        $stmtUpdate->execute();

        if ($stmtUpdate->affected_rows === 0) {
            // Esto podría pasar si el email fue eliminado entre la selección y la actualización,
            // o si el token y la expiración ya son los mismos (poco probable).
            throw new Exception('No se pudo actualizar el token en la base de datos. El usuario podría no existir o los datos ya estaban actualizados.');
        }
        $stmtUpdate->close();

        renderMessageBox(
            "¡Correo enviado!",
            "Hemos enviado un enlace para restablecer tu contraseña a <strong>" . htmlspecialchars($email) . "</strong>.
             Por favor, revisa tu bandeja de entrada y también la carpeta de spam. El enlace es válido por 1 hora.",
            "Abrir Gmail (si es tu proveedor)",
            "https://mail.google.com/"
        );

    } catch (Exception $e) {
        error_log("Error al enviar correo de recuperación para $email: " . $mail->ErrorInfo . " | Excepción: " . $e->getMessage());
        renderMessageBox(
            "Error al Enviar",
            "Ocurrió un error al intentar enviar el correo de recuperación. Es posible que haya un problema con la configuración del servidor de correo o la dirección proporcionada. Por favor, inténtalo de nuevo más tarde o contacta al administrador.",
            "Intentar de nuevo",
            "/biblioteca/recuperar.php"
        );
    }
} else {
    renderMessageBox(
        "Correo no encontrado",
        "No encontramos ninguna cuenta asociada a <strong>" . htmlspecialchars($email) . "</strong>. Verifica que esté escrito correctamente.",
        "Intentar de nuevo",
        "/biblioteca/recuperar.php"
    );
}

// Cerrar conexiones
if (isset($stmt) && $stmt instanceof mysqli_stmt) {
    $stmt->close();
}
if (isset($conexion) && $conexion instanceof mysqli) {
    $conexion->close();
}

exit; // Asegura que el script termine aquí.
?>