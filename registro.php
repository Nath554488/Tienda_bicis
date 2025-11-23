<?php
session_start();
require 'conexion.php';

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';
    $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? NULL;
    $num_tarjeta = $_POST['num_tarjeta'] ?? NULL;
    $direccion_postal = trim($_POST['direccion_postal'] ?? '');
    $tipo_usuario = "cliente"; 

    if ($nombre === "" || $email === "" || $password === "" || $password2 === "") {
        $mensaje = "Todos los campos obligatorios deben llenarse.";
    } elseif ($password !== $password2) {
        $mensaje = "Las contraseñas no coinciden.";
    } else {
      
        $stmt = $mysqli->prepare("SELECT id_usuario FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $mensaje = "Ya existe una cuenta con ese correo.";
        } 
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $mysqli->prepare("
                INSERT INTO usuarios 
                (nombre, email, password_hash, fecha_nacimiento, num_tarjeta, direccion_postal, tipo_usuario, fecha_registro)
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
            ");

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
                // Iniciar sesión automático
                $_SESSION['usuario_id'] = $stmt->insert_id;
                $_SESSION['usuario_nombre'] = $nombre;

                header("Location: index.php");
                exit;
            } else {
                $mensaje = "Error al registrar usuario.";
            }
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Registro</title>
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/5/w3.css">
</head>
<body class="w3-sand">

<div class="w3-container" style="max-width:500px; margin-top:60px;">
  <h2 class="w3-center">Crear cuenta</h2>

  <?php if ($mensaje !== ""): ?>
    <div class="w3-panel w3-red">
      <p><?= $mensaje ?></p>
    </div>
  <?php endif; ?>

  <form method="post">

    <p>
      <label>Nombre *</label>
      <input class="w3-input w3-border" type="text" name="nombre">
    </p>

    <p>
      <label>Correo electrónico *</label>
      <input class="w3-input w3-border" type="email" name="email">
    </p>

    <p>
      <label>Contraseña *</label>
      <input class="w3-input w3-border" type="password" name="password">
    </p>

    <p>
      <label>Repetir contraseña *</label>
      <input class="w3-input w3-border" type="password" name="password2">
    </p>

    <p>
      <label>Fecha de nacimiento</label>
      <input class="w3-input w3-border" type="date" name="fecha_nacimiento">
    </p>

    <p>
      <label>Número de tarjeta</label>
      <input class="w3-input w3-border" type="text" name="num_tarjeta">
    </p>

    <p>
      <label>Dirección postal</label>
      <input class="w3-input w3-border" type="text" name="direccion_postal">
    </p>

    <p>
      <button class="w3-button w3-black w3-block">Registrarme</button>
    </p>
    
    <p class="w3-center">
      ¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a>
    </p>

  </form>
</div>

</body>
</html>