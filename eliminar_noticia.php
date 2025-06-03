<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login_admin.php");
    exit;
}
require 'db.php';

$id = (int)$_GET['id'];
$conexion->query("DELETE FROM noticias WHERE id = $id");
header("Location: admin_noticias.php");
exit;
?>
