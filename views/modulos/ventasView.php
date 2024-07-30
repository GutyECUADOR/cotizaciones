<?php
use App\Middleware\RouteMiddleware;
use App\Controllers\RouteController;

$routeController = new RouteController();
$menus = $routeController->getMenus();
$routeMiddleware = new RouteMiddleware();
$routeMiddleware->checkisLogin();

?>

<!-- CSS Propios -->
<link rel="stylesheet" href="<?php echo ROOT_PATH; ?>assets\css\admin.css">

<div id="wrapper">
    <!-- Sidebar -->
    <?php include './views/modulos/sis_modules/main_sidebar.php'?>

    <!-- Page Content -->
    <div id="page-content-wrapper">
      <?php include './views/modulos/sis_modules/header_main_admin.php'?>

        <div class="container-fluid">
          <h2>Módulo de Ventas</h2>
          
          <div class="row">
            <?php 
              unset($menus[0]);
              foreach ($menus as $menu) {

                if (trim($menu["activo"])) {
                 
            ?>

            <div class="col-md-4">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title"><?php echo trim($menu["nombre"])?></h4>
                  <h6 class="card-subtitle mb-2 text-muted"><?php echo trim(ucfirst($menu["modulo"]))?></h6>
                  <p class="card-text"><?php echo trim($menu["descripcion"])?></p>
                  <a href="?action=<?php echo trim($menu["action"])?>" class="btn btn-primary" role="button">Ir al Formulario</a>
                </div>
              </div> 
            </div> <!-- end col -->
            
            <?php } 
            } ?>
           
            
          </div>
            
         
        </div>    
        <!-- container-fluid -->
    </div>
    <!-- /#page-content-wrapper -->

    
    
</div>

    <!-- USO JQUERY, y Bootstrap CDN-->
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\libs\vue.js"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\libs\jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\libs\bootstrap.min.js"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\libs\moment.min.js"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\pages\inicio.js?<?php echo date('Ymdhiiss')?>"></script>
  