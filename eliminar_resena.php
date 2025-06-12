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

$stmt = $conexion->prepare("DELETE FROM resenas WHERE id = ? AND id_usuario = ?");
$stmt->bind_param("ii", $resena_id, $usuario_id);
$stmt->execute();
$stmt->close();

header("Location: detalle_libro.php?id=$libro_id");
