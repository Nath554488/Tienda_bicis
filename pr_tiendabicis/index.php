<?php
session_start();
require 'conexion.php'; // aquí se crea $mysqli

function obtenerProductosCategoria($mysqli, $idCategoria) {
    $productos = [];

    // 🔹 Ahora también traemos id_producto
    $sql = "SELECT id_producto, nombre, descripcion, foto_url, precio 
            FROM productos 
            WHERE id_categoria = ? AND activo = 1";

    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $idCategoria);
    $stmt->execute();
    $resultado = $stmt->get_result();

    while ($row = $resultado->fetch_assoc()) {
        $productos[] = $row;
    }

    $stmt->close();
    return $productos;
}

// 1 = Bicicletas, 2 = Accesorios, 3 = Ropa
$productosBicis      = obtenerProductosCategoria($mysqli, 1);
$productosAccesorios = obtenerProductosCategoria($mysqli, 2);
$productosRopa       = obtenerProductosCategoria($mysqli, 3);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <title>Tienda de Bicicletas</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/5/w3.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inconsolata">

  <style>
    body, html {
      height: 100%;
      font-family: "Inconsolata", sans-serif;
    }

    /* ============ SLIDER MANUAL ============ */
    .manual-slider {
      position: relative;
      width: 100%;
      height: 90vh;
      overflow: hidden;
      margin-top: 60px;
    }

    .slide-container {
      width: 100%;
      height: 100%;
      position: relative;
    }

    .slide {
      width: 100%;
      height: 100%;
      object-fit: cover;
      position: absolute;
      top: 0;
      left: 0;
      opacity: 0;
      transition: opacity .6s ease;
    }

    .slide.active {
      opacity: 1;
    }

    .prev, .next{
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      background-color: rgba(0,0,0,0.3);
      color: white;
      border: none;
      padding: 25px;
      cursor: pointer;
      z-index: 20;
      border-radius: 50%;
    }

    .prev:hover, .next:hover{
      background-color: rgba(0,0,0,0.6);
    }

    .prev{
      left: 20px;
    }

    .next {
      right: 20px;
    }

    .hero-logo{
      position: absolute;
      top: 20px;
      left: 20px;
      z-index: 30;
    }

    .hero-logo img {
      height: 80px;
      filter: drop-shadow(2px 2px 6px black);
    }
  </style>

</head>
<body>

<!-- Barra de navegación -->
<div class="w3-top">
  <div class="w3-row w3-padding w3-black">
    <div class="w3-col s2">
      <a href="#home" class="w3-button w3-block w3-black">INICIO</a>
    </div>
    <div class="w3-col s2">
      <a href="#about" class="w3-button w3-block w3-black">NOSOTROS</a>
    </div>
    <div class="w3-col s2">
      <a href="#catalogo" class="w3-button w3-block w3-black">CATÁLOGO</a>
    </div>
    <div class="w3-col s2">
      <a href="#contacto" class="w3-button w3-block w3-black">CONTACTO</a>
    </div>
    <div class="w3-col s2">
      <a href="ver_carrito.php" class="w3-button w3-block w3-black">CARRITO</a>
    </div>
    <div class="w3-col s2">
      <?php if (isset($_SESSION['usuario_id'])): ?>
        <a href="logout.php" class="w3-button w3-block w3-black">
          CERRAR SESIÓN (<?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?>)
        </a>
      <?php else: ?>
        <a href="login.php" class="w3-button w3-block w3-black">LOGIN</a>
      <?php endif; ?>

      <?php if (isset($_SESSION['es_admin']) && $_SESSION['es_admin'] == 1): ?>
        <a href="admin.php" class="w3-button w3-block w3-dark-grey" style="margin-top:4px;">ADMIN</a>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- SLIDER MANUAL -->
<div class="manual-slider" id="home">
  <div class="slide-container">
    <img src="imagenes/baner1.png" class="slide active" alt="Imagen 1">
    <img src="imagenes/baner2.png" class="slide" alt="Imagen 2">
    <img src="imagenes/baner3.png" class="slide" alt="Imagen 3">
    <img src="imagenes/baner4.png" class="slide" alt="Imagen 4">
  </div>

  <button class="prev" onclick="moveSlide(-1)">&#10094;</button>
  <button class="next" onclick="moveSlide(1)">&#10095;</button>

  <div class="hero-logo">
    <img src="imagenes/nombre.png" alt="Pedalea">
  </div>
</div>

<!-- Contenido principal -->
<div class="w3-sand w3-grayscale w3-large">

  <!-- Sección NOSOTROS -->
  <div class="w3-container" id="about">
    <div class="w3-content" style="max-width:700px">
      <h5 class="w3-center w3-padding-64">
        <span class="w3-tag w3-wide">SOBRE LA TIENDA</span>
      </h5>
      <p>Somos una tienda especializada en bicicletas de ruta, montaña y urbanas, así como equipo y accesorios para que puedas rodar con seguridad y estilo.</p>
      <p>Manejar bicicleta no solo es un deporte: es una forma de transporte sostenible y una manera de cuidar tu salud.</p>
    </div>
  </div>

  <!-- Sección CATÁLOGO -->
  <div class="w3-container" id="catalogo">
    <div class="w3-content" style="max-width:700px">
      <h5 class="w3-center w3-padding-48">
        <span class="w3-tag w3-wide">CATÁLOGO</span>
      </h5>

      <div class="w3-row w3-center w3-card w3-padding">
        <a href="javascript:void(0)" onclick="openMenu(event, 'Bicis');" id="myLink">
          <div class="w3-col s4 tablink">Bicicletas</div>
        </a>
        <a href="javascript:void(0)" onclick="openMenu(event, 'Accesorios');">
          <div class="w3-col s4 tablink">Accesorios</div>
        </a>
        <a href="javascript:void(0)" onclick="openMenu(event, 'Ropa');">
          <div class="w3-col s4 tablink">Ropa</div>
        </a>
      </div>

      <!-- Bicicletas -->
      <div id="Bicis" class="w3-container menu w3-padding-48 w3-card">
        <?php if (empty($productosBicis)): ?>
          <p class="w3-text-grey">No hay bicicletas registradas por el momento.</p>
        <?php else: ?>
          <?php foreach ($productosBicis as $p): ?>
            <div class="w3-row w3-margin-bottom">
              <div class="w3-col s4 m3">
                <?php if (!empty($p['foto_url'])): ?>
                  <img src="<?php echo htmlspecialchars($p['foto_url']); ?>"
                       class="w3-image" style="max-height:150px;">
                <?php endif; ?>
              </div>
              <div class="w3-col s8 m9">
                <h5><?php echo htmlspecialchars($p['nombre']); ?></h5>
                <p class="w3-text-grey">
                  <?php echo htmlspecialchars($p['descripcion']); ?><br>
                  <strong>$<?php echo number_format($p['precio'], 2); ?></strong>
                </p>
                <form method="post" action="agregar_carrito.php" style="margin-top:8px;">
                  <input type="hidden" name="id_producto" value="<?php echo (int)$p['id_producto']; ?>">
                  <input type="hidden" name="cantidad" value="1">
                  <button type="submit" class="w3-button w3-black w3-small">
                    Agregar al carrito
                  </button>
                </form>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

      <!-- Accesorios -->
      <div id="Accesorios" class="w3-container menu w3-padding-48 w3-card">
        <?php if (empty($productosAccesorios)): ?>
          <p class="w3-text-grey">No hay accesorios registrados por el momento.</p>
        <?php else: ?>
          <?php foreach ($productosAccesorios as $p): ?>
            <div class="w3-row w3-margin-bottom">
              <div class="w3-col s4 m3">
                <?php if (!empty($p['foto_url'])): ?>
                  <img src="<?php echo htmlspecialchars($p['foto_url']); ?>"
                       class="w3-image" style="max-height:150px;">
                <?php endif; ?>
              </div>
              <div class="w3-col s8 m9">
                <h5><?php echo htmlspecialchars($p['nombre']); ?></h5>
                <p class="w3-text-grey">
                  <?php echo htmlspecialchars($p['descripcion']); ?><br>
                  <strong>$<?php echo number_format($p['precio'], 2); ?></strong>
                </p>
                <form method="post" action="agregar_carrito.php" style="margin-top:8px;">
                  <input type="hidden" name="id_producto" value="<?php echo (int)$p['id_producto']; ?>">
                  <input type="hidden" name="cantidad" value="1">
                  <button type="submit" class="w3-button w3-black w3-small">
                    Agregar al carrito
                  </button>
                </form>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

      <!-- Ropa -->
      <div id="Ropa" class="w3-container menu w3-padding-48 w3-card">
        <?php if (empty($productosRopa)): ?>
          <p class="w3-text-grey">No hay productos de ropa registrados por el momento.</p>
        <?php else: ?>
          <?php foreach ($productosRopa as $p): ?>
            <div class="w3-row w3-margin-bottom">
              <div class="w3-col s4 m3">
                <?php if (!empty($p['foto_url'])): ?>
                  <img src="<?php echo htmlspecialchars($p['foto_url']); ?>"
                       class="w3-image" style="max-height:150px;">
                <?php endif; ?>
              </div>
              <div class="w3-col s8 m9">
                <h5><?php echo htmlspecialchars($p['nombre']); ?></h5>
                <p class="w3-text-grey">
                  <?php echo htmlspecialchars($p['descripcion']); ?><br>
                  <strong>$<?php echo number_format($p['precio'], 2); ?></strong>
                </p>
                <form method="post" action="agregar_carrito.php" style="margin-top:8px;">
                  <input type="hidden" name="id_producto" value="<?php echo (int)$p['id_producto']; ?>">
                  <input type="hidden" name="cantidad" value="1">
                  <button type="submit" class="w3-button w3-black w3-small">
                    Agregar al carrito
                  </button>
                </form>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

    </div>
  </div>

  <!-- Sección CONTACTO -->
  <div class="w3-container" id="contacto" style="padding-bottom:32px;">
    <div class="w3-content" style="max-width:700px">
      <h5 class="w3-center w3-padding-48">
        <span class="w3-tag w3-wide">CONTACTO</span>
      </h5>
      <p>Escríbenos para cotizaciones, dudas o pedidos especiales.</p>
      <form method="post" action="#">
        <p><input class="w3-input w3-padding-16 w3-border" type="text" name="nombre" placeholder="Nombre" required></p>
        <p><input class="w3-input w3-padding-16 w3-border" type="email" name="correo" placeholder="Correo electrónico" required></p>
        <p><input class="w3-input w3-padding-16 w3-border" type="text" name="mensaje" placeholder="Mensaje" required></p>
        <p><button class="w3-button w3-black" type="submit">ENVIAR</button></p>
      </form>
    </div>
  </div>

</div>

<footer class="w3-center w3-light-grey w3-padding-48 w3-large">
  <p>Tienda de Bicicletas - Proyecto Programación para Internet</p>
</footer>

<script>
  // Tabs del catálogo
  function openMenu(evt, menuName) {
    var i, x, tablinks;
    x = document.getElementsByClassName("menu");
    for (i = 0; i < x.length; i++) {
      x[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablink");
    for (i = 0; i < tablinks.length; i++) {
      tablinks[i].className = tablinks[i].className.replace(" w3-dark-grey", "");
    }
    document.getElementById(menuName).style.display = "block";
    evt.currentTarget.firstElementChild.className += " w3-dark-grey";
  }
  document.getElementById("myLink").click();

  // Slider manual
  let currentSlide = 0;
  const slides = document.querySelectorAll(".slide");

  function showSlide(index) {
    slides.forEach(slide => slide.classList.remove("active"));
    slides[index].classList.add("active");
  }

  function moveSlide(step) {
    currentSlide += step;
    if (currentSlide >= slides.length) currentSlide = 0;
    if (currentSlide < 0) currentSlide = slides.length - 1;
    showSlide(currentSlide);
  }

  showSlide(currentSlide);
</script>

</body>
</html>

