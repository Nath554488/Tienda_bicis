<?php
session_start();
require 'conexion.php';  // aquí se define $mysqli

// 1. Verificar que el usuario esté logueado
if (!isset($_SESSION['usuario_id'])) {
    // si quieres puedes mandar un mensaje por GET
    header("Location: login.php");
    exit();
}

$id_usuario  = $_SESSION['usuario_id'];
$id_producto = intval($_POST['id_producto'] ?? 0);
$cantidad    = intval($_POST['cantidad'] ?? 1);

if ($id_producto <= 0 || $cantidad <= 0) {
    header("Location: index.php");
    exit();
}

// 2. Revisar si ya existe ese producto en el carrito del usuario
$sql = "SELECT cantidad FROM carrito WHERE id_usuario = ? AND id_producto = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("ii", $id_usuario, $id_producto);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    // Ya existe → actualizamos cantidad
    $nuevaCantidad = $row['cantidad'] + $cantidad;

    $sqlUpdate = "UPDATE carrito SET cantidad = ? WHERE id_usuario = ? AND id_producto = ?";
    $stmtUpdate = $mysqli->prepare($sqlUpdate);
    $stmtUpdate->bind_param("iii", $nuevaCantidad, $id_usuario, $id_producto);
    $stmtUpdate->execute();
    $stmtUpdate->close();
} else {
    // No existe → insertamos
    $sqlInsert = "INSERT INTO carrito (id_usuario, id_producto, cantidad)
                  VALUES (?, ?, ?)";
    $stmtInsert = $mysqli->prepare($sqlInsert);
    $stmtInsert->bind_param("iii", $id_usuario, $id_producto, $cantidad);
    $stmtInsert->execute();
    $stmtInsert->close();
}

$stmt->close();

// 3. Redirigir de vuelta al catálogo o al carrito
header("Location: ver_carrito.php");
exit();