<?php
// Menú lateral según el rol: el cajero solo ve lo que usa; el admin ve todo, por grupos
$esAdminMenu = !empty($_SESSION['idUsuario']['tipo']) && $_SESSION['idUsuario']['tipo'] == 1;

if ($esAdminMenu) {
  $gruposMenu = [
    'Día a día' => [
      ['Inicio', '/inicio', 'mdi-view-dashboard'],
      ['Vender', '/ventas', 'mdi-cart'],
      ['Caja', '/caja', 'mdi-cash-register'],
      ['Tickets', '/tickets', 'mdi-printer'],
    ],
    'Inventario' => [
      ['Insumos', '/insumos', 'mdi-food-variant'],
      ['Compras', '/compras', 'mdi-truck-delivery'],
      ['Recetas', '/recetas', 'mdi-book-open-variant'],
      ['Stock', '/inventario', 'mdi-package-variant'],
    ],
    'Carta' => [
      ['Productos', '/menus', 'mdi-silverware-variant'],
      ['Categorías', '/categorias', 'mdi-tag-multiple'],
    ],
    'Control' => [
      ['Reportes', '/reportes/rventas', 'mdi-poll'],
    ],
    'Administración' => [
      ['Usuarios', '/usuarios', 'mdi-account-multiple'],
    ],
  ];
} else {
  $gruposMenu = [
    'Día a día' => [
      ['Vender', '/ventas', 'mdi-cart'],
      ['Caja', '/caja', 'mdi-cash-register'],
    ],
  ];
}
?>
<?php foreach ($gruposMenu as $tituloGrupo => $enlaces) { ?>
<li class="nav-item nav-category">
  <span class="nav-link"><?= $tituloGrupo; ?></span>
</li>
<?php foreach ($enlaces as $enlace) { ?>
<li class="nav-item menu-items">
  <a class="nav-link" href="<?= base_url() . $enlace[1]; ?>">
    <span class="menu-icon">
      <i class="mdi <?= $enlace[2]; ?>"></i>
    </span>
    <span class="menu-title"><?= $enlace[0]; ?></span>
  </a>
</li>
<?php } ?>
<?php } ?>

<li class="nav-item nav-category">
  <span class="nav-link">Sesión</span>
</li>
<li class="nav-item menu-items">
  <a class="nav-link" href="<?= base_url(); ?>/login/logout">
    <span class="menu-icon">
      <i class="mdi mdi-logout-variant"></i>
    </span>
    <span class="menu-title">Cerrar sesión</span>
  </a>
</li>

<script>
  // Marca la opción de la pantalla actual comparando la ruta completa,
  // para que Vender (/ventas) no marque también Reportes (/reportes/rventas)
  window.addEventListener("load", function () {
    var limpiar = function (ruta) { return ruta.replace(/\/+$/, "").toLowerCase(); };
    var actual = limpiar(window.location.pathname);

    document.querySelectorAll("#sidebar .menu-items > .nav-link").forEach(function (enlace) {
      var ruta = limpiar(enlace.pathname);
      var esActual = actual === ruta || actual.indexOf(ruta + "/") === 0;
      enlace.classList.toggle("active", esActual);
      enlace.parentElement.classList.toggle("active", esActual);
    });
  });
</script>
</ul>
</nav>
<!-- partial -->
<div class="container-fluid page-body-wrapper">
  <!-- partial:<?= base_url_template(); ?>/partials/_navbar.html -->
  <nav class="navbar p-0 fixed-top d-flex flex-row">
    <div class="navbar-brand-wrapper d-flex d-lg-none align-items-center justify-content-center">
      <a class="navbar-brand brand-logo-mini" href="<?= base_url_template(); ?>/inicio"><img
          src="<?= base_url_template(); ?>/assets/images/logo-mini.svg" alt="logo" /></a>
    </div>
    <div class="navbar-menu-wrapper flex-grow d-flex align-items-stretch">
      <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
        <span class="mdi mdi-menu"></span>
      </button>
      <ul class="navbar-nav w-100">
        <li class="nav-item w-100">
          <?php
          $dias = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
          $meses = ['', 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
          $fechaHoy = $dias[date('w')] . ' ' . date('d') . ' de ' . $meses[(int) date('n')] . ', ' . date('Y');
          ?>
          <span class="fecha-navbar"><i class="mdi mdi-calendar-blank"></i> <?= $fechaHoy; ?></span>
        </li>
      </ul>
      <ul class="navbar-nav navbar-nav-right">
        <li class="nav-item dropdown">
          <a class="nav-link" id="profileDropdown" href="#" data-toggle="dropdown">
            <div class="navbar-profile">
              <p class="mb-0 d-none d-sm-block navbar-profile-name"><?= $_SESSION['idUsuario']['nombre']; ?>
                <?= $_SESSION['idUsuario']['apellido']; ?></p>
              <i class="mdi mdi-menu-down d-none d-sm-block"></i>
            </div>
          </a>
          <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="profileDropdown">
            <h6 class="p-3 mb-0"><?= $_SESSION['idUsuario']['nombre_tipo']; ?></h6>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item preview-item" href="<?= base_url(); ?>/login/logout">
              <div class="preview-thumbnail">
                <div class="preview-icon bg-dark rounded-circle">
                  <i class="mdi mdi-logout text-danger"></i>
                </div>
              </div>
              <div class="preview-item-content">
                <p class="preview-subject mb-1">Cerrar sesión</p>
              </div>
            </a>
          </div>
        </li>
      </ul>
      <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button"
        data-toggle="offcanvas">
        <span class="mdi mdi-format-line-spacing"></span>
      </button>
    </div>
  </nav>
