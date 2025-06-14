<!-- solicitar_libro.php -->
<?php
session_start();
if (!isset($_SESSION['email'])) {
  header("Location: index.html");
  exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Solicitar Nuevo Libro</title>
  <link rel="stylesheet" href="css/formulario.css"> <!-- Usa tu CSS o crea uno nuevo -->
</head>
<body>
  <h2>Solicitar un nuevo libro</h2>
  <form action="procesar_solicitud.php" method="POST">
    <label for="titulo">Título del libro:</label>
    <input type="text" id="titulo" name="titulo" required>

    <label for="autor">Autor:</label>
    <input type="text" id="autor" name="autor" required>

    <label for="genero">Género:</label>
    <input type="text" id="genero" name="genero">

    <label for="comentario">¿Por qué quieres este libro?</label>
    <textarea id="comentario" name="comentario" rows="4"></textarea>

    <button type="submit">Enviar solicitud</button>
  </form>
</body>
</html>
