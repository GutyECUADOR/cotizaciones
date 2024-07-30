<?php
use App\Middleware\RouteMiddleware;

$routeMiddleware = new RouteMiddleware();
$routeMiddleware->checkisLogin();

?>

<!-- CSS Propios -->
<link rel="stylesheet" href="<?php echo ROOT_PATH; ?>assets\css\admin.css">

<div id="wrapper">
    <!-- Sidebar -->
    <?php include './views/modulos/sis_modules/main_sidebar_admin.php'?>

    <!-- Page Content -->
    <div id="page-content-wrapper">
      <?php include './views/modulos/sis_modules/header_main_admin.php'?>

        <div class="container-fluid">
        
          <ol class="breadcrumb" style="padding-top:20px">
              <li class="active">Dashboard</li>
          </ol>

        </div>  <!-- container-fluid -->
    </div>
    <!-- /#page-content-wrapper -->

</div>

<!-- USO JQUERY, y Bootstrap CDN-->
<script src="assets\js\vue.js"></script>
<script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\bootstrap-datepicker.js"></script>

<script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\popper.min.js"></script>

<!-- JS Propio-->

<script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\admin.js"></script>
<script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\pages\admin\estado-vehiculo-admin.js"></script>