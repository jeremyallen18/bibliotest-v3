<?php
session_start();
session_destroy(); // Destruir la sesión
header("Location: index.html"); // Redirigir al usuario a la página de login
exit;
?>
