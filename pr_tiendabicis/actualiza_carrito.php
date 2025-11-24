<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['usuario_id'];
$id_carrito = intval($_POST['id_carrito'] ?? 0);
$cantidad   = intval($_POST['cantidad'] ?? 1);

if ($id_carrito <= 0 || $cantidad <= 0) {
    header("Location: ver_carrito.php");
    exit();
}

$sql = "UPDATE carrito 
        SET cantidad = ? 
        WHERE id_carrito = ? AND id_usuario = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("iii", $cantidad, $id_carrito, $id_usuario);
$stmt->execute();
$stmt->close();

header("Location: ver_carrito.php");
exit();