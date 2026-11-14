<!doctype html>
<html lang="eS">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="google-signin-client_id" content="YOUR_CLIENT_ID.apps.googleusercontent.com">



  <!-- Tipografías del sistema de diseño (locales, sin internet) -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/Assets/css/fonts.css">

  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/Assets/css/tokens.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/Assets/css/login.css">
  <link rel="icon" href="<?php echo BASE_URL; ?>/template/assets/images/favicon.png" type="image/png">

  <title>Iniciar sesión · Chicken Lindo</title>
</head>

<body>

  <div class="wrapper fadeInDown">
    <div id="formContent">
      <!-- Tabs Titles -->

      <!-- Icon -->
      <div class="fadeIn first">
        <img src="<?php echo BASE_URL; ?>/Assets/images/logoPollo2.png" id="icon" alt="Logo de Chicken Lindo" />
        <h1>Iniciar sesión</h1>
      </div>

      <!-- Login Form -->

      <form id="formLogin" name="formLogin" action="" method="post">
        <input type="hidden" name="csrf_token" value="<?= csrfToken(); ?>">
        <div class="form-group">
          <label for="user">Usuario</label>
          <input type="text" id="user" name="user" placeholder="Tu usuario del sistema" autocomplete="username">
        </div>
        <div class="form-group">
          <label for="password">Contraseña</label>
          <input type="password" id="password" class="fadeIn second" name="password" placeholder="Tu contraseña" autocomplete="current-password">
        </div>
        <?php if (!empty($data['expirada'])) { ?>
          <div class="alert alert-danger text-center" id="alerta" role="alert">Tu sesión se cerró por inactividad. Vuelve a entrar.</div>
        <?php } else { ?>
          <div class="alert alert-danger text-center d-none" id="alerta" role="alert"></div>
        <?php } ?>
        <div class="fadeIn fourth">
          <button class="button-login" type="submit" onclick="enterLogin(event);">Entrar</button>
        </div>

        <!-- Remind Passowrd -->
        <div id="formFooter">
          <a class="underlineHover" href="#">¿Olvidaste tu contraseña?</a>
        </div>
    </div>

    </form>

  </div>
  </div>


  <!-- Optional JavaScript -->
  <!-- jQuery first, then Popper.js, then Bootstrap JS -->
  <!-- Required Jquery -->

  <script>
    const baseUrl = "<?php echo BASE_URL; ?>";
  </script>

  <script src="<?php echo BASE_URL; ?>/Alert/dist/sweetalert2.all.min.js"></script>

  <!-- funciones js -->
  <script src="<?php echo BASE_URL; ?>/Assets/js/functions_login.js"></script>
</body>

</html>