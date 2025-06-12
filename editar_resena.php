<?php
session_start();
include 'db.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.html");
    exit();
}

$resena_id = $_POST['resena_id'];
$libro_id = $_POST['libro_id'];
$usuario_id = $_SESSION['usuario_id'];

$stmt = $conexion->prepare("SELECT calificacion, comentario FROM resenas WHERE id = ? AND id_usuario = ?");
$stmt->bind_param("ii", $resena_id, $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$resena = $result->fetch_assoc();
$stmt->close();

if (!$resena) {
    echo "<script>alert('No se encontró la reseña.'); window.location='detalle_libro.php?id=$libro_id';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Reseña</title>
    <link rel="stylesheet" href="css/editar_resena.css">
</head>
<body>
    <h2>Editar tu reseña</h2>
    <form action="actualizar_resena.php" method="POST">
        <input type="hidden" name="resena_id" value="<?= $resena_id ?>">
        <input type="hidden" name="libro_id" value="<?= $libro_id ?>">

        <label>Calificación:</label>
        <select name="calificacion" required>
            <?php for ($i = 5; $i >= 1; $i--): ?>
                <option value="<?= $i ?>" <?= $i == $resena['calificacion'] ? 'selected' : '' ?>><?= str_repeat("⭐", $i) ?></option>
            <?php endfor; ?>
        </select><br><br>

        <label>Comentario:</label><br>
        <textarea name="comentario" rows="4" cols="50" required><?= htmlspecialchars($resena['comentario']) ?></textarea><br><br>

        <button type="submit">Actualizar Reseña</button>
    </form>
    <br>
    <a href="detalle_libro.php?id=<?= $libro_id ?>">Volver al libro</a>
</body>
</html>
