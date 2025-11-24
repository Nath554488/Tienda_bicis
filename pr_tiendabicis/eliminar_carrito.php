<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['usuario_id'];
$id_carrito = intval($_POST['id_carrito'] ?? 0);

if ($id_carrito <= 0) {
    header("Location: ver_carrito.php");
    exit();
}

$sql = "DELETE FROM carrito 
        WHERE id_carrito = ? AND id_usuario = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("ii", $id_carrito, $id_usuario);
$stmt->execute();
$stmt->close();

header("Location: ver_carrito.php");
exit();