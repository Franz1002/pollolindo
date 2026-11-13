<script>
  const base_url = "<?= base_url(); ?>";
  const minutos_inactividad = <?= MINUTOS_INACTIVIDAD; ?>;
  const csrf_token = "<?= csrfToken(); ?>";
</script>

<!-- Incluir jQuery (Debe ir antes que Bootstrap y Bootstrap Select) -->
<script src="<?= base_url(); ?>/Assets/js/librerias/jquery-3.6.0.min.js"></script>

<!-- Cierre de sesión por inactividad -->
<script src="<?= media(); ?>/js/sesion.js"></script>

<!-- Bootstrap JS -->
<script src="<?= base_url(); ?>/Assets/js/librerias/bootstrap.min.js"></script>
<!-- Bootstrap Select -->
<script src="<?= base_url(); ?>/Assets/js/librerias/bootstrap-select.min.js"></script>

<!-- SweetAlert2 -->
<script src="<?= base_url(); ?>/Assets/js/librerias/sweetalert2.min.js"></script>

<!-- Scripts adicionales -->
<script src="<?= base_url_template(); ?>/assets/js/off-canvas.js"></script>
<script src="<?= base_url_template(); ?>/assets/js/hoverable-collapse.js"></script>

<script src="<?= base_url_template(); ?>/assets/js/settings.js"></script>
<script src="<?= base_url_template(); ?>/assets/js/todolist.js"></script>

<!-- DataTables -->
<script src="<?= base_url_template(); ?>/assets/DataTables/datatables.min.js"></script>
<!-- Textos en español de las tablas (archivo local, sin internet) -->
<script src="<?= media(); ?>/js/datatables-es.js"></script>

<!-- Funciones JS de la página -->
<script src="<?= media(); ?>/js/<?= $data['page_functions_js']; ?>"></script>