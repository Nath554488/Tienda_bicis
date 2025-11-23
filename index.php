<?php
session_start();
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

    .bgimg {
      background-position: center;
      background-size: cover;
      /* Luego cambias esta imagen por una de bicis tuya */
      background-image: url("https://www.w3schools.com/w3images/bike.jpg");
      min-height: 75%;
    }

    .menu {
      display: none;
    }
  </style>
</head>
<body>

<!-- Barra de navegación -->
<div class="w3-top">
  <div class="w3-row w3-padding w3-black">
    <div class="w3-col s3">
      <a href="#home" class="w3-button w3-block w3-black">INICIO</a>
    </div>
    <div class="w3-col s3">
      <a href="#about" class="w3-button w3-block w3-black">NOSOTROS</a>
    </div>
    <div class="w3-col s3">
      <a href="#catalogo" class="w3-button w3-block w3-black">CATÁLOGO</a>
    </div>
    <div class="w3-col s3">
      <a href="#contacto" class="w3-button w3-block w3-black">CONTACTO</a>
    </div>

    <div class="w3-col s3">
        <?php if (isset($_SESSION['usuario_id'])): ?>
            <a href="logout.php" class="w3-button w3-block w3-black">
        CERRAR SESIÓN (<?php echo $_SESSION['usuario_nombre']; ?>)
            </a>
        <?php else: ?>
            <a href="login.php" class="w3-button w3-block w3-black">LOGIN</a>
        <?php endif; ?>
    </div>
  </div>
</div>

<!-- Header con imagen -->
<header class="bgimg w3-display-container w3-grayscale-min" id="home">
  <div class="w3-display-middle w3-center">
    <span class="w3-text-white" style="font-size:70px">Tienda<br>Bicicletas</span>
  </div>
  <div class="w3-display-bottomright w3-center w3-padding-large">
    <span class="w3-text-white">Ciudad de México</span>
  </div>
</header>

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
          <div class="w3-col s6 tablink">Bicicletas</div>
        </a>
        <a href="javascript:void(0)" onclick="openMenu(event, 'Accesorios');">
          <div class="w3-col s6 tablink">Accesorios</div>
        </a>
      </div>

      <div id="Bicis" class="w3-container menu w3-padding-48 w3-card">
        <h5>Bici de Montaña</h5>
        <p class="w3-text-grey">Ideal para terracería y senderos.</p><br>

        <h5>Bici de Ruta</h5>
        <p class="w3-text-grey">Ligera y rápida para carretera.</p><br>

        <h5>Bici Urbana</h5>
        <p class="w3-text-grey">Perfecta para la ciudad y trayectos diarios.</p>
      </div>

      <div id="Accesorios" class="w3-container menu w3-padding-48 w3-card">
        <h5>Casco</h5>
        <p class="w3-text-grey">Protección certificada para tus rodadas.</p><br>

        <h5>Guantes</h5>
        <p class="w3-text-grey">Mayor agarre y comodidad.</p><br>

        <h5>Luces</h5>
        <p class="w3-text-grey">Para que siempre seas visible.</p>
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
    for (i = 0; i < x.length; i++) {
      tablinks[i].className = tablinks[i].className.replace(" w3-dark-grey", "");
    }
    document.getElementById(menuName).style.display = "block";
    evt.currentTarget.firstElementChild.className += " w3-dark-grey";
  }
  document.getElementById("myLink").click();
</script>

</body>
</html>
