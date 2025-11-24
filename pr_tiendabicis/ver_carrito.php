<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['usuario_id'];

// Traer items del carrito + datos del producto
$sql = "SELECT c.id_carrito, c.id_producto, c.cantidad,
               p.nombre, p.precio, p.foto_url
        FROM carrito c
        INNER JOIN productos p ON c.id_producto = p.id_producto
        WHERE c.id_usuario = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();

$items = [];
$total = 0;

while ($row = $result->fetch_assoc()) {
    $subtotal = $row['precio'] * $row['cantidad'];
    $row['subtotal'] = $subtotal;
    $total += $subtotal;
    $items[] = $row;
}

$stmt->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Mi carrito</title>
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/5/w3.css">
</head>
<body class="w3-light-grey">

<div class="w3-container" style="max-width:900px; margin-top:40px;">
  <div class="w3-card w3-white w3-padding">

    <h2 class="w3-center">Mi carrito</h2>

    <?php if (empty($items)): ?>
      <p class="w3-center w3-text-grey">Tu carrito está vacío.</p>
      <p class="w3-center">
        <a href="index.php" class="w3-button w3-black">Volver a la tienda</a>
      </p>
    <?php else: ?>

      <?php foreach ($items as $item): ?>
        <div class="w3-row w3-margin-bottom w3-border-bottom w3-padding-16">
          <div class="w3-col s3">
            <?php if (!empty($item['foto_url'])): ?>
              <img src="<?php echo htmlspecialchars($item['foto_url']); ?>" 
                   class="w3-image" style="max-height:120px;">
            <?php endif; ?>
          </div>
          <div class="w3-col s9">
            <h4><?php echo htmlspecialchars($item['nombre']); ?></h4>
            <p>
              Cantidad: <?php echo $item['cantidad']; ?><br>
              Precio: $<?php echo number_format($item['precio'], 2); ?><br>
              <strong>Subtotal: $<?php echo number_format($item['subtotal'], 2); ?></strong>
            </p>

            <!-- FORMULARIO PARA EDITAR / ELIMINAR -->
            <form method="post" action="actualiza_carrito.php" class="w3-margin-top">
  <input type="hidden" name="id_carrito" value="<?php echo (int)$item['id_carrito']; ?>">

  <label>Cambiar cantidad:</label>
  <input class="w3-input w3-border"
         type="number" name="cantidad" min="1"
         value="<?php echo (int)$item['cantidad']; ?>"
         style="max-width:120px; display:inline-block;">

  <button type="submit" class="w3-button w3-small w3-black">
    Actualizar
  </button>

  <!-- Botón eliminar -->
  <button type="submit" formaction="eliminar_carrito.php"
          class="w3-button w3-small w3-red">
    Quitar del carrito
  </button>
</form>

        
            </form>
          </div>
        </div>
      <?php endforeach; ?>

      <h3 class="w3-right">Total: $<?php echo number_format($total, 2); ?></h3>
      <div class="w3-clear"></div>

      <div class="w3-center" style="margin-top:30px;">
        <a href="index.php" class="w3-button w3-light-grey">Seguir comprando</a>
        <a href="checkout.php" class="w3-button w3-black">Finalizar compra</a>
      </div>

    <?php endif; ?>

  </div>
</div>

</body>
</html>
