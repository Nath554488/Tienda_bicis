<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['usuario_id'];
$mensaje = "";

// 1. Traer los productos del carrito del usuario
function obtenerItemsCarrito($mysqli, $id_usuario) {
    $items = [];

    $sql = "SELECT c.id_producto, c.cantidad, p.precio
            FROM carrito c
            INNER JOIN productos p ON c.id_producto = p.id_producto
            WHERE c.id_usuario = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        // id_producto, cantidad, precio
        $items[] = $row;
    }

    $stmt->close();
    return $items;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre    = trim($_POST['nombre'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $telefono  = trim($_POST['telefono'] ?? '');
    $metodo    = trim($_POST['metodo_pago'] ?? '');

    // 2. Obtener items del carrito
    $items = obtenerItemsCarrito($mysqli, $id_usuario);

    if (empty($items)) {
        $mensaje = "Tu carrito está vacío, no se registró ninguna compra.";
    } else {

        // Iniciar transacción
        $mysqli->begin_transaction();

        try {
            // 3. Preparar INSERT en historial_compras
            $sqlInsert = "INSERT INTO historial_compras
                          (id_usuario, id_producto, cantidad, precio_unitario, fecha_compra)
                          VALUES (?, ?, ?, ?, NOW())";
            $stmtInsert = $mysqli->prepare($sqlInsert);

            // 4. Preparar UPDATE de inventario
            $sqlUpdateStock = "UPDATE productos
                               SET cantidad_almacen = cantidad_almacen - ?
                               WHERE id_producto = ? AND cantidad_almacen >= ?";
            $stmtStock = $mysqli->prepare($sqlUpdateStock);

            foreach ($items as $item) {
                $id_producto    = (int)$item['id_producto'];
                $cantidad       = (int)$item['cantidad'];
                $precioUnitario = (float)$item['precio'];

                // 4.1. Registrar en historial_compras
                $stmtInsert->bind_param("iiid",
                    $id_usuario,
                    $id_producto,
                    $cantidad,
                    $precioUnitario
                );
                $stmtInsert->execute();

                // 4.2. Actualizar inventario
                $stmtStock->bind_param("iii",
                    $cantidad,      // restar esta cantidad
                    $id_producto,   // producto
                    $cantidad       // sólo si hay al menos esta cantidad
                );
                $stmtStock->execute();

                // Si no se actualizó ninguna fila, significa que no había stock suficiente
                if ($stmtStock->affected_rows === 0) {
                    throw new Exception("No hay inventario suficiente para el producto $id_producto");
                }
            }

            $stmtInsert->close();
            $stmtStock->close();

            // 5. Vaciar carrito del usuario
            $sqlDelete = "DELETE FROM carrito WHERE id_usuario = ?";
            $stmtDel = $mysqli->prepare($sqlDelete);
            $stmtDel->bind_param("i", $id_usuario);
            $stmtDel->execute();
            $stmtDel->close();

            // Todo ok
            $mysqli->commit();

            $mensaje = "¡Gracias por tu compra, $nombre! Tu pedido ha sido registrado.";
        } catch (Exception $e) {
            // Algo falló → revertimos todo
            $mysqli->rollback();
            $mensaje = "Ocurrió un error al registrar la compra: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Finalizar compra</title>
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/5/w3.css">
</head>
<body class="w3-light-grey">

<div class="w3-container" style="max-width:600px; margin-top:40px;">
  <div class="w3-card w3-white w3-padding">

    <h2 class="w3-center">Finalizar compra</h2>

    <?php if ($mensaje): ?>
      <p class="w3-center w3-text-green"><?php echo htmlspecialchars($mensaje); ?></p>
      <p class="w3-center">
        <a href="index.php" class="w3-button w3-black">Volver a la tienda</a>
      </p>
    <?php else: ?>

      <form method="post" class="w3-container">
        <p>
          <label>Nombre completo</label>
          <input class="w3-input w3-border" type="text" name="nombre" required>
        </p>
        <p>
          <label>Dirección de envío</label>
          <input class="w3-input w3-border" type="text" name="direccion" required>
        </p>
        <p>
          <label>Teléfono de contacto</label>
          <input class="w3-input w3-border" type="text" name="telefono" required>
        </p>
        <p>
          <label>Método de pago</label>
          <select class="w3-input w3-border" name="metodo_pago" required>
            <option value="tarjeta">Tarjeta de crédito / débito</option>
            <option value="transferencia">Transferencia</option>
            <option value="efectivo">Efectivo al recoger</option>
          </select>
        </p>

        <p class="w3-center" style="margin-top:20px;">
          <button type="submit" class="w3-button w3-black">Confirmar compra</button>
          <a href="ver_carrito.php" class="w3-button w3-light-grey">Volver al carrito</a>
        </p>
      </form>

    <?php endif; ?>

  </div>
</div>

</body>
</html>
