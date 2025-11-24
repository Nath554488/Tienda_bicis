<?php

$host   = "db";           
$usuario = "usuario";       
$clave   = "Mjn202424";       
$bd      = "tienda_bicicletas"; 


$mysqli = new mysqli($host, $usuario, $clave, $bd);

// Verificar conexión
if ($mysqli->connect_errno) {
    die("Error de conexión a la base de datos: " . $mysqli->connect_error);
}


$mysqli->set_charset("utf8");
?>
