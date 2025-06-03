<?php
session_start();
if (!isset($_SESSION['email']) || !isset($_SESSION['usuario_id'])) {
    header('Location: index.html');
    exit();
}

include 'db.php';

$usuario_id = $_SESSION['usuario_id'];

if (isset($_POST['limpiar'])) {
    $stmt = $conexion->prepare("DELETE FROM prestamos WHERE usuario_id = ?");
    $stmt->bind_param('i', $usuario_id);

    if ($stmt->execute()) {
        header("Location: devolver_libro.php?msg=eliminado");
        exit();
    } else {
        echo "Error al eliminar registros.";
    }

    $stmt->close();
}
?>
