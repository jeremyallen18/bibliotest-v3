<?php
session_start();
include 'db.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.html");
    exit();
}

$libro_id = $_POST['id_libro'];
$usuario_id = $_SESSION['usuario_id'];
$calificacion = $_POST['calificacion'];
$comentario = trim($_POST['comentario']);

// Validar si ya hay una reseña del mismo usuario
$stmt = $conexion->prepare("SELECT id FROM resenas WHERE id_libro = ? AND id_usuario = ?");
$stmt->bind_param("ii", $libro_id, $usuario_id);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    $stmt->close();
    echo "<script>alert('Ya has dejado una reseña para este libro.'); window.history.back();</script>";
    exit();
}
$stmt->close();

// Insertar nueva reseña
$stmt = $conexion->prepare("INSERT INTO resenas (id_libro, id_usuario, calificacion, comentario) VALUES (?, ?, ?, ?)");
$stmt->bind_param("iiis", $libro_id, $usuario_id, $calificacion, $comentario);
$stmt->execute();

header("Location: detalle_libro.php?id=" . $libro_id);
?>
