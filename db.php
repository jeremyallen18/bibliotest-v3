<?php
$host = '127.0.0.1';  // servidor de base de datos
$usuario = 'root';    // usuario de la base de datos
$clave = '';          // contraseña de la base de datos
$base_de_datos = 'biblioteca';  // nombre de la base de datos

// Crear la conexión
$conexion = new mysqli($host, $usuario, $clave, $base_de_datos);

// Verificar si hay error en la conexión
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}
// No ejecutar ninguna consulta ni cerrar la conexión aquí.
?>
