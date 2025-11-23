<?php
session_start();
require 'conexion.php';

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    $email = trim($_POST["email"] ?? '');
    $password = $_POST["password"] ?? '';

    if ($email === "" || $password === "") {
        $mensaje = "Ingresa tu correo y contraseña.";
    } else {
        $stmt = $mysqli->prepare("SELECT id_usuario, nombre, password_hash FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($id, $nombre, $password_hash);

        if ($stmt->fetch()) {
            if (password_verify($password, $password_hash)) {

                $_SESSION["usuario_id"] = $id;
                $_SESSION["usuario_nombre"] = $nombre;

                header("Location: index.php");
                exit;

            } else {
                $mensaje = "Contraseña incorrecta.";
            }
        } else {
            $mensaje = "No existe una cuenta con ese correo.";
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/5/w3.css">
</head>
<body class="w3-sand">

<div class="w3-container" style="max-width:500px; margin-top:60px;">
  <h2 class="w3-center">Iniciar sesión</h2>

  <?php if ($mensaje !== ""): ?>
    <div class="w3-panel w3-red"><p><?= $mensaje ?></p></div>
  <?php endif; ?>

  <form method="POST">
    <p>
      <label>Correo</label>
      <input class="w3-input w3-border" type="email" name="email">
    </p>

    <p>
      <label>Contraseña</label>
      <input class="w3-input w3-border" type="password" name="password">
    </p>

    <p><button class="w3-button w3-black w3-block">Entrar</button></p>

    <p class="w3-center">¿No tienes cuenta? <a href="registro.php">Regístrate</a></p>
  </form>

</div>
</body>
</html>