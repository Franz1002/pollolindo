<li class="nav-item nav-category">
  <span class="nav-link">Navigation</span>
</li>
<li class="nav-item menu-items">
  <a class="nav-link" href="<?= base_url(); ?>/inicio">
    <span class="menu-icon">
      <i class=" mdi mdi-view-dashboard "></i>
    </span>
    <span class="menu-title">Dashboard</span>
  </a>
</li>
<li class="nav-item menu-items">
  <a class="nav-link" href="<?= base_url(); ?>/usuarios">
    <span class="menu-icon">
      <i class="mdi mdi-account"></i>
    </span>
    <span class="menu-title">Usuarios</span>
  </a>
</li>
<li class="nav-item menu-items">
  <a class="nav-link" href="<?= base_url(); ?>/categorias">
    <span class="menu-icon">
      <i class="mdi mdi-speedometer"></i>
    </span>
    <span class="menu-title">Categorias</span>
  </a>
</li>
<li class="nav-item menu-items">
  <a class="nav-link" href="<?= base_url(); ?>/menus">
    <span class="menu-icon">
      <i class="mdi mdi-silverware-variant"></i>
    </span>
    <span class="menu-title">Menu</span>
  </a>
</li>
<li class="nav-item menu-items">
  <a class="nav-link" href="<?= base_url(); ?>/inventario">
    <span class="menu-icon">
      <i class="mdi mdi-calendar-text"></i>
    </span>
    <span class="menu-title">Inventario</span>
  </a>
</li>
<li class="nav-item menu-items">
  <a class="nav-link" href="<?= base_url(); ?>/insumos">
    <span class="menu-icon">
      <i class="mdi mdi-food-drumstick"></i>
    </span>
    <span class="menu-title">Insumos</span>
  </a>
</li>
<li class="nav-item menu-items">
  <a class="nav-link" href="<?= base_url(); ?>/recetas">
    <span class="menu-icon">
      <i class="mdi mdi-book-open-variant"></i>
    </span>
    <span class="menu-title">Recetas</span>
  </a>
</li>


<style>
  .nav-link.active {
    background-color: rgba(255, 255, 255, 0.1);
    /* Color de fondo sutil */
    box-shadow: inset 7px 3px 0 0 #ffffff;
    /* Línea izquierda como indicador */
    border-radius: 5px;
    /* Bordes redondeados */
    text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.8);
    /* Sombra oscura en las letras */
    font-weight: bold;
    /* Hace que la letra se vea más gruesa */
  }
</style>

<li class="nav-item menu-items">
  <a class="nav-link" data-toggle="collapse" data-target="#ui-basic" aria-expanded="false" aria-controls="ui-basic">
    <span class="menu-icon">
      <i class="mdi mdi-format-list-bulleted"></i>
    </span>
    <span class="menu-title">Compras y ventas</span>
    <i class="menu-arrow"></i>
  </a>
  <div class="collapse" id="ui-basic">
    <ul class="nav flex-column sub-menu">
      <li class="nav-item">
        <a class="nav-link" href="<?= base_url(); ?>/compras" id="link-compras">
          <span class="menu-icon">
            <i class="mdi mdi-square-inc-cash"></i>
          </span> Registrar compra
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="<?= base_url(); ?>/ventas" id="link-ventas">
          <span class="menu-icon">
            <i class="mdi mdi-square-inc-cash" style="color:darkgoldenrod;"></i>
          </span> Registrar venta
        </a>
      </li>
    </ul>
  </div>
</li>

<script>
  document.addEventListener("DOMContentLoaded", function() {
    let currentUrl = window.location.pathname;

    // Mantiene abierto el menú si estamos en compras o ventas
    if (currentUrl.includes("/compras") || currentUrl.includes("/ventas")) {
      document.getElementById("ui-basic").classList.add("show");
    }

    // Aplica la clase 'active' al enlace correspondiente
    if (currentUrl.includes("/compras")) {
      document.getElementById("link-compras").classList.add("active");
    } else if (currentUrl.includes("/ventas")) {
      document.getElementById("link-ventas").classList.add("active");
    }
  });
</script>

<li class="nav-item menu-items">
  <a class="nav-link" href="<?= base_url(); ?>/tickets">
    <span class="menu-icon">
      <i class="mdi mdi-printer" style="color:hotpink;"></i>
    </span>
    <span class="menu-title">Tickets</span>
  </a>
</li>
<li class="nav-item menu-items">
  <a class="nav-link" href=" <?= base_url(); ?>/reportes/rventas">
    <span class="menu-icon">
      <i class="mdi mdi-poll"></i>
    </span>
    <span class="menu-title">Reporte de ventas</span>
  </a>
</li>

<li class="nav-item menu-items">
  <a class="nav-link" href="<?= base_url(); ?>/login/logout">
    <span class="menu-icon">
      <i class="mdi mdi-logout-variant "></i>
    </span>
    <span class="menu-title">Cerrar sesión</span>
  </a>
</li>
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
          $fechaHoy = $dias[date('w')] . ' ' . date('d') . ' de ' . $meses[(int)date('n')] . ', ' . date('Y');
          ?>
          <span class="fecha-navbar"><i class="mdi mdi-calendar-blank"></i> <?= $fechaHoy; ?></span>
        </li>
      </ul>
      <ul class="navbar-nav navbar-nav-right">
        <li class="nav-item dropdown">
          <a class="nav-link" id="profileDropdown" href="#" data-toggle="dropdown">
            <div class="navbar-profile">
              <p class="mb-0 d-none d-sm-block navbar-profile-name"><?= $_SESSION['idUsuario']['nombre']; ?> <?= $_SESSION['idUsuario']['apellido']; ?></p>
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