<?php
session_start();
require 'conexion.php'; // cambia el nombre si tu archivo se llama distinto

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // 1. Leer datos del formulario
    $email    = trim($_POST["email"] ?? '');
    $password = $_POST["password"] ?? '';

    if ($email === "" || $password === "") {
        $mensaje = "Ingresa tu correo y contraseña.";
    } else {

        // 2. Preparar consulta: TRAEMOS es_admin TAMBIÉN
        $stmt = $mysqli->prepare("
            SELECT id_usuario, nombre, password_hash, es_admin
            FROM usuarios
            WHERE email = ?
        ");

        if (!$stmt) {
            $mensaje = "Error en la consulta: " . $mysqli->error;
        } else {
            // 3. Enlazar parámetro y ejecutar
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->bind_result($id, $nombre, $password_hash, $es_admin);

            // 4. Ver si existe el usuario
            if ($stmt->fetch()) {
                // 5. Validar contraseña
                if (password_verify($password, $password_hash)) {

                    // 6. Guardar sesión
                    $_SESSION["usuario_id"]     = $id;
                    $_SESSION["usuario_nombre"] = $nombre;
                    $_SESSION["es_admin"]       = $es_admin; // AQUÍ YA QUEDA BIEN

                    // 7. Redirigir a la página principal
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
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Tienda de Bicicletas</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/5/w3.css">
</head>
<body class="w3-light-grey">

<!-- CONTENEDOR QUE CENTRA TODO -->
<div style="
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
">
    <!-- TARJETA -->
    <div class="w3-card w3-white w3-padding" style="max-width:400px; width:100%;">

        <h2 class="w3-center">Iniciar sesión</h2>

        <?php if (!empty($mensaje)): ?>
            <p class="w3-text-red w3-center"><?php echo $mensaje; ?></p>
        <?php endif; ?>

        <form method="post" action="login.php" class="w3-container">
            <p>
                <label>Correo electrónico</label>
                <input class="w3-input w3-border" type="email" name="email" required>
            </p>
            <p>
                <label>Contraseña</label>
                <input class="w3-input w3-border" type="password" name="password" required>
            </p>

            <p class="w3-center">
                <button class="w3-button w3-black" type="submit">Entrar</button>
                <a href="index.php" class="w3-button w3-light-grey">Volver</a>
            </p>

            <p class="w3-center" style="margin-top:10px;">
                ¿No tienes cuenta?
                <a href="registro.php">Regístrate aquí</a>
            </p>

        </form>

    </div>
</div>

</body>
</html>
