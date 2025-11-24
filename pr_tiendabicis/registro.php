<?php
session_start();
require 'conexion.php';

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre           = trim($_POST['nombre'] ?? '');
    $email            = trim($_POST['email'] ?? '');
    $password         = $_POST['password']  ?? '';
    $password2        = $_POST['password2'] ?? '';
    $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? null;
    $num_tarjeta      = $_POST['num_tarjeta'] ?? null;
    $direccion_postal = trim($_POST['direccion_postal'] ?? '');
    $tipo_usuario     = "cliente";  // por defecto

    // Validaciones básicas
    if ($nombre === "" || $email === "" || $password === "" || $password2 === "") {
        $mensaje = "Todos los campos marcados con * son obligatorios.";
    } elseif ($password !== $password2) {
        $mensaje = "Las contraseñas no coinciden.";
    } else {
        // Verificar si ya existe ese correo
        $stmt = $mysqli->prepare("SELECT id_usuario FROM usuarios WHERE email = ?");
        if (!$stmt) {
            die("Error en la consulta: " . $mysqli->error);
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $mensaje = "Ya existe una cuenta con ese correo.";
        } else {
            // Insertar nuevo usuario
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt->close();

            $stmt = $mysqli->prepare("
                INSERT INTO usuarios
                  (nombre, email, password_hash, fecha_nacimiento, num_tarjeta, direccion_postal, tipo_usuario, fecha_registro)
                VALUES
                  (?, ?, ?, ?, ?, ?, ?, NOW())
            ");

            if (!$stmt) {
                die("Error al preparar INSERT: " . $mysqli->error);
            }

            $stmt->bind_param(
                "sssssss",
                $nombre,
                $email,
                $password_hash,
                $fecha_nacimiento,
                $num_tarjeta,
                $direccion_postal,
                $tipo_usuario
            );

            if ($stmt->execute()) {
                // Iniciar sesión automáticamente
                $_SESSION['usuario_id']     = $stmt->insert_id;
                $_SESSION['usuario_nombre'] = $nombre;

                header("Location: index.php");
                exit;
            } else {
                $mensaje = "Error al registrar usuario: " . $stmt->error;
            }
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro - Tienda de Bicicletas</title>
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/5/w3.css">
</head>
<body style="background-color: white;">

<!-- CONTENEDOR QUE CENTRA TODO -->
<div style="
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
">

    <!-- TARJETA -->
    <div class="w3-card w3-white w3-padding" style="
        max-width: 450px;
        width: 100%;
        border-radius: 8px;
    ">

        <h2 class="w3-center">Crear cuenta</h2>

        <!-- AQUI VA TU FORMULARIO -->
        <form action="registro.php" method="post" class="w3-container">

            <p>
                <label>Nombre *</label>
                <input class="w3-input w3-border" type="text" name="nombre" required>
            </p>

            <p>
                <label>Correo electrónico *</label>
                <input class="w3-input w3-border" type="email" name="email" required>
            </p>

            <p>
                <label>Contraseña *</label>
                <input class="w3-input w3-border" type="password" name="password" required>
            </p>

            <p>
                <label>Repetir contraseña *</label>
                <input class="w3-input w3-border" type="password" name="password2" required>
            </p>

            <p>
                <label>Fecha de nacimiento</label>
                <input class="w3-input w3-border" type="date" name="fecha_nac">
            </p>

            <p>
                <label>Número de tarjeta</label>
                <input class="w3-input w3-border" type="text" name="tarjeta">
            </p>

            <p>
                <label>Dirección postal</label>
                <input class="w3-input w3-border" type="text" name="direccion">
            </p>

            <p class="w3-center" style="margin-top: 20px;">
                <button class="w3-button w3-black w3-block" type="submit">Registrarme</button>
            </p>

            <p class="w3-center" style="margin-top: 10px;">
                ¿Ya tienes cuenta?
                <a href="login.php">Inicia sesión</a>
            </p>

        </form>

    </div>

</div>

</body>
</html>
