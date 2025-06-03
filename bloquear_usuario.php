<?php
session_start();
require 'db.php';
$id = $_GET['id'];
$conexion->prepare("UPDATE usuarios SET bloqueado = TRUE WHERE id = ?")->execute([$id]);
header("Location: admin_usuarios.php");
