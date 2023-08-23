<?php
use App\Middleware\RouteMiddleware;
use App\Controllers\RouteController;

$routeController = new RouteController();
$menus = $routeController->getMenus();
$routeMiddleware = new RouteMiddleware();
$routeMiddleware->checkisLogin();
$routeMiddleware->checkIsSupervidor();

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
            <form id="app" v-on:submit.prevent="">
              
                <ol class="breadcrumb">
                    <li><a href="?action=inicio">Inicio</a></li>
                    <li> admin </li>
                    <li> {{ title }} </li>
                </ol>
              
                <div class="container-fluid wrap" style="padding: 20px 15px 20px 15px; border-radius: 5px;">
                  
                    <div class="row"> 
                        <div class="col-md-12">
                            <div class="panel panel-default">
                                <div class="panel-heading font-weight-bold clearfix">
                                    <i class="fa fa-list" aria-hidden="true"></i> Lista de variables de entorno
                                    <div class="btn-group pull-right">
                                        <button type="button" data-toggle="modal" data-target="#modalBuscarSysModule" class="btn btn-primary btn-sm"><i class="fa fa-plus" aria-hidden="true"></i> Agregar nueva variable</button>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <div class="table-responsive">
                                        <table class="table table-condensed table-striped table-hover " id="tbl_productos">
                                            <thead>
                                                <tr>
                                                <th>Nombre</th>
                                                <th>Descripcion</th>
                                                <th>Valor</th>
                                                <th class="text-right">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="variable in variables">
                                                    <td>{{ variable.nombre }}</td>
                                                    <td>{{ variable.descripcion }}</td>
                                                    <td>
                                                        <input type="text" class="form-control" v-model="variable.valor">
                                                    </td>
                                                    <td class="text-right">
                                                        <button @click="updateVariable(variable)" class="btn btn-success btn-sm" type="button">
                                                            <span class="fa fa-sync" aria-hidden="true"></span> Actualizar Variable
                                                        </button>
                                                        
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                            
                        
                        </div>
                        
                    </div>
                   
                    <!-- Modals -->
                    <!-- Modal Info sesion -->
                    <?php require_once './views/modulos/sis_modules/modal_info_session.php'?>
                   

                </div>

            </form>
        </div>    
        <!-- container-fluid -->
    </div>
   

</div>

<!-- USO JQUERY, y Bootstrap CDN-->
<!-- USO JQUERY, y Bootstrap CDN-->
<script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\libs\vue.js"></script>
<script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\libs\jquery.min.js"></script>
<script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\libs\bootstrap-datepicker.js"></script>

<script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\libs\bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\libs\popper.min.js"></script>

<!-- JS Propio-->
<script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\pages\admin\variables.js"></script>