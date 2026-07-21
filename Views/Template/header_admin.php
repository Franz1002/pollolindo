<!DOCTYPE html>
<html lang="en">


<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">

  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <title>Chicken lindo</title>
  <!-- plugins:css -->
  <link rel="stylesheet" href="<?= base_url_template(); ?>/assets/vendors/mdi/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="<?= base_url_template(); ?>/assets/vendors/css/vendor.bundle.base.css">


  <link href="<?= base_url_template(); ?>/assets/DataTables/datatables.min.css" rel="stylesheet">
  <link href="<?= base_url(); ?>/Assets/css/bootstrap.min.css" rel="stylesheet">

  
 <link href="<?=base_url(); ?>/Assets/css/bootstrap-select.min.css" rel="stylesheet">

  <link href="<?= base_url(); ?>/Assets/css/sweetalert2.min.css" rel="stylesheet">


  <!-- endinject -->
  <!-- Plugin css for this page -->
  <!-- End Plugin css for this page -->
  <!-- inject:css -->
  <!-- endinject -->
  <!-- Layout styles -->
  <link rel="stylesheet" href="<?= base_url_template(); ?>/assets/css/style.css">
  <!-- personalisado para ventas:css -->
  <link rel="stylesheet" href="<?= base_url_template(); ?>/assets/css/estilos.css">
  <link rel="stylesheet" href="<?= base_url_template(); ?>/assets/css/animate.min.css">
  <!-- End layout styles -->
  <link rel="shortcut icon" href="<?= base_url_template(); ?>/assets/images/favicon.png" />
</head>

<body>
  <div class="container-scroller">
    <!-- partial:<?= base_url_template(); ?>/partials/_sidebar.html -->
    <nav class="sidebar sidebar-offcanvas" id="sidebar">
      <div class="sidebar-brand-wrapper d-none d-lg-flex align-items-center justify-content-center fixed-top">
        <a class="sidebar-brand brand-logo" href="<?= base_url(); ?>/inicio"><img src="<?= base_url_template(); ?>/assets/images/logo.svg" alt="logo" /></a>
        <a class="sidebar-brand brand-logo-mini" href="<?= base_url(); ?>/inicio"><img src="<?= base_url_template(); ?>/assets/images/logo_mini.svg" alt="logo" /></a>
      </div>
      <ul class="nav">
        <li class="nav-item profile">
          <div class="profile-desc">
            <div class="profile-pic">
              <div class="count-indicator">
                <img class="img-xs rounded-circle " src="<?= base_url_template(); ?>/assets/images/faces/user.png" alt="user">
                <span class="count bg-success"></span>
              </div>
              <div class="profile-name">
                <h5 style="color: whitesmoke; font-size:large" class="mb-0 font-weight-normal"><?= $_SESSION['idUsuario']['nombre']; ?> <?= $_SESSION['idUsuario']['apellido']; ?></h5>
                <span style="font-size:larger;"><?= $_SESSION['idUsuario']['nombre_tipo']; ?></span>
              </div>
            </div>
            <a href="#" id="profile-dropdown" data-toggle="dropdown"><i class="mdi mdi-dots-vertical"></i></a>
            <div class="dropdown-menu dropdown-menu-right sidebar-dropdown preview-list" aria-labelledby="profile-dropdown">
              <a href="#" class="dropdown-item preview-item">
                <div class="preview-thumbnail">
                  <div class="preview-icon bg-dark rounded-circle">
                    <i class="mdi mdi-settings text-primary"></i>
                  </div>
                </div>
                <div class="preview-item-content">
                  <p class="preview-subject ellipsis mb-1 text-small">Account settings</p>
                </div>
              </a>
              <div class="dropdown-divider"></div>
              <a href="#" class="dropdown-item preview-item">
                <div class="preview-thumbnail">
                  <div class="preview-icon bg-dark rounded-circle">
                    <i class="mdi mdi-onepassword  text-info"></i>
                  </div>
                </div>
                <div class="preview-item-content">
                  <p class="preview-subject ellipsis mb-1 text-small">Change Password</p>
                </div>
              </a>
              <div class="dropdown-divider"></div>
              <a href="#" class="dropdown-item preview-item">
                <div class="preview-thumbnail">
                  <div class="preview-icon bg-dark rounded-circle">
                    <i class="mdi mdi-calendar-today text-success"></i>
                  </div>
                </div>
                <div class="preview-item-content">
                  <p class="preview-subject ellipsis mb-1 text-small">To-do list</p>
                </div>
              </a>
            </div>
          </div>
        </li>
        <?php require_once("nav_admin.php"); ?>