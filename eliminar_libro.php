<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login_admin.php");
    exit;
}
require 'db.php';

if (!isset($_GET['id'])) {
    header("Location: admin_libros.php");
    exit;
}

$id = $_GET['id'];

// Verificar si el libro existe
$stmt = $conexion->prepare("SELECT id FROM libros WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    header("Location: admin_libros.php");
    exit;
}
$stmt->close();

// Eliminar el libro
$stmt = $conexion->prepare("DELETE FROM libros WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $_SESSION['success_message'] = "Libro eliminado correctamente";
} else {
    $_SESSION['error_message'] = "Error al eliminar el libro";
}
$stmt->close();

header("Location: admin_libros.php");
exit;
?>