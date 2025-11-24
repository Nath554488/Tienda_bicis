<?php
session_start();
require 'conexion.php'; // aquí se define $mysqli

// Solo admins
if (!isset($_SESSION['es_admin']) || $_SESSION['es_admin'] != 1) {
    header("Location: index.php");
    exit();
}

$mensaje = "";

// Si enviaron el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre       = $_POST['nombre'] ?? '';
    $descripcion  = $_POST['descripcion'] ?? '';
    $precio       = $_POST['precio'] ?? 0;
    $cantidad     = $_POST['cantidad_almacen'] ?? 0;
    $fabricante   = $_POST['fabricante'] ?? '';
    $origen       = $_POST['origen'] ?? '';
    $id_categoria = $_POST['id_categoria'] ?? 1;
    $activo       = isset($_POST['activo']) ? 1 : 0;

    // Manejo de imagen
    $foto_url = null;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $carpetaDestino = 'imagenes_productos/';
        if (!is_dir($carpetaDestino)) {
            mkdir($carpetaDestino, 0777, true);
        }

        $nombreArchivo = time() . '_' . basename($_FILES['foto']['name']);
        $rutaDestino   = $carpetaDestino . $nombreArchivo;

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $rutaDestino)) {
            $foto_url = $rutaDestino;
        }
    }

    // INSERT sencillo usando los nombres de tu tabla
    $sql = "
        INSERT INTO productos
        (nombre, descripcion, foto_url, precio, cantidad_almacen, fabricante, origen, id_categoria, activo)
        VALUES (
            '".$mysqli->real_escape_string($nombre)."',
            '".$mysqli->real_escape_string($descripcion)."',
            ".($foto_url ? "'".$mysqli->real_escape_string($foto_url)."'" : "NULL").",
            ".floatval($precio).",
            ".intval($cantidad).",
            '".$mysqli->real_escape_string($fabricante)."',
            '".$mysqli->real_escape_string($origen)."',
            ".intval($id_categoria).",
            ".intval($activo)."
        )
    ";

    if ($mysqli->query($sql)) {
        $mensaje = "Producto agregado correctamente.";
    } else {
        $mensaje = "Error al guardar: " . $mysqli->error;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Admin productos</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/5/w3.css">
</head>
<body class="w3-light-grey">

<div class="w3-container" style="max-width:700px; margin-top:40px;">
    <div class="w3-card w3-white w3-padding">

        <h2 class="w3-center">Panel de administración - Productos</h2>

        <?php if ($mensaje): ?>
            <p class="w3-center w3-text-green"><?php echo $mensaje; ?></p>
        <?php endif; ?>

        <form class="w3-container" method="post" enctype="multipart/form-data">
            <p>
                <label>Nombre del producto</label>
                <input class="w3-input w3-border" type="text" name="nombre" required>
            </p>
            <p>
                <label>Descripción</label>
                <textarea class="w3-input w3-border" name="descripcion" required></textarea>
            </p>
            <p>
                <label>Precio</label>
                <input class="w3-input w3-border" type="number" step="0.01" name="precio" required>
            </p>
            <p>
                <label>Cantidad en almacén</label>
                <input class="w3-input w3-border" type="number" name="cantidad_almacen" required>
            </p>
            <p>
                <label>Fabricante</label>
                <input class="w3-input w3-border" type="text" name="fabricante">
            </p>
            <p>
                <label>Origen</label>
                <input class="w3-input w3-border" type="text" name="origen">
            </p>
            <p>
                <label>Categoría</label>
                <select class="w3-input w3-border" name="id_categoria" required>
                    <option value="1">Bicicletas</option>
                    <option value="2">Accesorios</option>
                    <option value="3">Ropa</option>
                </select>
            </p>
        
            <p>
                <label>Foto del producto</label>
                <input class="w3-input" type="file" name="foto" accept="image/*">
            </p>
            <p>
                <label>
                    <input class="w3-check" type="checkbox" name="activo" checked>
                    Activo
                </label>
            </p>
            <p class="w3-center">
                <button class="w3-button w3-black" type="submit">Guardar producto</button>
                <a href="index.php" class="w3-button w3-light-grey">Volver a la tienda</a>
            </p>
        </form>

    </div>
</div>

</body>
</html>
