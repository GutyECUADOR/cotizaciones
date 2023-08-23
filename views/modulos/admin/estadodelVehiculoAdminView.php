<?php
use App\Middleware\RouteMiddleware;

$routeMiddleware = new RouteMiddleware();
$routeMiddleware->checkisLogin();

?>

<div id="wrapper">
    <!-- Sidebar -->
    <?php include './views/modulos/sis_modules/main_sidebar_admin.php'?>

    <!-- Page Content -->
    <div id="page-content-wrapper">
      <?php include './views/modulos/sis_modules/header_main_admin.php'?>

        <div class="container-fluid">
        
          <ol class="breadcrumb">
              <li><a href="?action=inicio">Inicio</a></li>
              <li>Vehiculos</li>
              <li class="active">Estado del Vehiculo (Admin)</li>
          </ol>

          <div class="row">
            <div class="col-sm-12">
              <div class="input-group input-group-sm">
                  <input type="text" class="form-control" id="txt_busqueda" placeholder="Placas del Vehiculo" required>
                  <span class="input-group-btn">
                      <button class="btn btn-primary btn-sm" type="button" id="btn_busqueda"><span class="glyphicon glyphicon-search"></span> Buscar</button>
                      <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Opciones <span class="caret"></span></button>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li><a href="?action=ordenPedidoVehiculo" target="_blank"><span class="glyphicon glyphicon-file"></span> Nuevo Orden Pedido</a></li>
                            <li class="divider"></li>
                            <li><a data-toggle="modal" data-target="#modalCrearProducto"><span class="glyphicon glyphicon-plus"></span> Agregar producto</a></li>
                        </ul>
                  </span>
              </div><!-- /input-group -->
            </div><!-- /.col-lg-6 -->
          </div><!-- /.row -->
       
          <div class="row">
              <div class="col-lg-12">
                <div id="responsibetable">
                  <!-- Resultados AJAX-->
                    <div class="resultados">
                      <table class="table table-condensed table-striped table-hover">
                          <thead>
                              <tr>
                              <th class="text-left">Codigo</th>
                              <th class="text-left">Placas</th>
                              <th class="text-left">Vehiculo</th>
                              <th class="text-left">Creado por</th>
                              <th class="text-left">Tipo</th>
                              <th class="text-left">Fecha</th>
                              <th class="text-left">Kilometraje</th>
                              <th class="text-left">Estado</th>
                              
                              </tr>
                          </thead>
                          <tbody id='tbodyresults'>
                          </tbody>
                      </table>
                    </div>
                </div>
              </div>
          </div>
        </div>  <!-- container-fluid -->
    </div>
    <!-- /#page-content-wrapper -->

    <?php require_once './views/modulos/sis_modules/modalCrearProducto_vehiculos.php'; ?>
    
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