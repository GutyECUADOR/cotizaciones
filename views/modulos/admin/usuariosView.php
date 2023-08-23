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
                    
                    <!-- Row de cabecera-->
                    <div class="row">
                        <div class="col-md-12">
                        <div class="panel panel-default">
                                <div class="panel-heading font-weight-bold">
                                    <i class="fa fa-search" aria-hidden="true"></i> Búsqueda de perfiles de usuario
                                </div>
                                <div class="panel-body">
                                    <div class="form-group">
                                    
                                    <div class="input-group select-group input-group-sm">
                                        <select id="select_perfil" @change="getAccesosPerfil" class="form-control">
                                            <option value="">Seleccione el perfil por favor</option>
                                            <option value="001001">001001 - GRUPO SUPER ADMINISTRADOR TEMPORAL</option>
                                            <option value="1">1 - SUPER ADMINISTRADOR</option>

                                            
                                            <option v-for="perfil in perfiles" :value="perfil.Codigo">
                                                <strong>{{perfil.Codigo}}</strong> - {{perfil.Nombre}}
                                            </option>
                                        </select>

                                        <div class="input-group-btn">
                                            <button @click="getAccesosPerfil()" type="button" class="btn btn-primary">
                                                <span class="glyphicon glyphicon-search"></span> Seleccionar
                                            </button>
                                        </div> 
                                    </div>
                                </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>

                    <div class="row"> 
                        <div class="col-md-12">
                            <div class="panel panel-default">
                                <div class="panel-heading font-weight-bold clearfix">
                                    <i class="fa fa-list" aria-hidden="true"></i> Lista de módulos con acceso
                                    <div class="btn-group pull-right">
                                        <button type="button" data-toggle="modal" data-target="#modalBuscarSysModule" class="btn btn-primary btn-sm"><i class="fa fa-plus" aria-hidden="true"></i> Agregar nuevo permiso</button>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <div class="table-responsive">
                                        <table class="table table-condensed table-striped table-hover " id="tbl_productos">
                                            <thead>
                                                <tr>
                                                <th>ID</th>
                                                <th>Nombre del Formulario</th>
                                                <th>Módulo</th>
                                                <th>Nivel de Acceso</th>
                                                <th class="text-right">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="modulo in modulos_acceso">
                                                    <th scope="row">{{ modulo.id }}</th>
                                                    <td>{{ modulo.nombre }}</td>
                                                    <td>{{ modulo.modulo }}</td>
                                                    <td>{{ modulo.lv_acceso }}</td>
                                                    <td class="text-right">
                                                        <button @click="removePermiso(modulo)" class="btn btn-danger btn-sm" type="button">
                                                            <span class="fa fa-trash" aria-hidden="true"></span> Remover Permiso
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
                    <?php require_once './views/modulos/sis_modules/modalBuscarSysModule.php'?>

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
<script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\pages\admin\usuarios.js"></script>