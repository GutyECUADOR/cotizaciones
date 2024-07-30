<?php

use App\Models\EstadoVehiculoModel;

if (!isset($_SESSION["usuarioRUC".APP_UNIQUE_KEY])){
    $preaction = $_GET['action'];
    header("Location:index.php?&action=login&preaction=$preaction");  
 } 

$estadoVehiculo = new EstadoVehiculoModel();

?>

    <!-- CSS Propios -->
    <link rel="stylesheet" href="<?php echo ROOT_PATH; ?>assets\css\cotizacionStyles.css">
 
    <?php include 'sis_modules/header_main.php'?>

    <form id="app" v-on:submit.prevent="saveDocumento">
        <div class="container">
            <ol class="breadcrumb">
                <li><a href="?action=inicio">Inicio</a></li>
                <li><a href="#">Vehiculos</a></li>
                <li class="active"> {{ title }} </li>
            </ol>
        </div>

        <div class="container wrap">
            
            <div class="row" style="margin-top:20px">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">Búsqueda de Documentos</div>
                        <div class="panel-body">

                       
                           
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control">
                                <span class="input-group-btn">
                                    <button class="btn btn-default" type="button" data-toggle="modal" data-target="#modalBuscarDocumento">
                                        <span class="glyphicon glyphicon-search" aria-hidden="true">
                                        </span>
                                    </button>
                                </span>
                            </div>
                       

                        </div>
                    </div>
                </div>
            </div>


            <div class="row">
                <div class="col-lg-8 ">
                    <div class="panel panel-default">
                        <div class="panel-heading">Datos del Registro</div>
                        <div class="panel-body">

                            <div class="input-group input-group-sm">
                                <span class="input-group-addon">Código del Supervisor</span>
                                <input type="text" class="form-control" v-model="documento.supervisor.search_text" @change="getSupervisor" placeholder="Ingrese nombre de usuario de Winfenix" required>
                            </div>

                            <div class="input-group input-group-sm">
                                <span class="input-group-addon">Supervisor</span>
                                <input type="text" class="form-control" :value="documento.supervisor.nombre" placeholder="(Empleado)" readonly>
                            </div>         
                
                            <div class="input-group input-group-sm">
                                <span class="input-group-addon">Placas del Vehiculo</span>
                                <input type="text" class="form-control" v-model="documento.vehiculo.search_text" @change="getVehiculo" placeholder="Ingrese la placa del Vehiculo" required>
                            
                                <span class="input-group-addon">Vehiculo</span>
                                <input type="text" class="form-control" :value="documento.vehiculo.nombre" placeholder="(Vehiculo)" readonly>
                            </div>

                            <div class="input-group input-group-sm">
                                <span class="input-group-addon">Kilometraje</span>
                                <input type="number" v-model="documento.vehiculo.kilometraje" class="form-control text-center"  placeholder="Kilometraje" required>
                            </div> 
                            
                            <div class="input-group input-group-sm">
                                <span class="input-group-addon">Fecha de Matricula</span>
                                <input type="date" class="form-control" v-model="documento.vehiculo.fechamatricula" required>
                            
                                <span class="input-group-addon">Fecha Proxima Matrícula</span>
                                <input type="date" class="form-control" v-model="documento.vehiculo.proximafechaMatricula" required>
                            </div>

                            <div class="input-group input-group-sm">
                                <span class="input-group-addon">Fecha de Mantenimiento</span>
                                <input type="date" class="form-control" v-model="documento.vehiculo.fechaMantenimiento"  required>
                            
                                <span class="input-group-addon">Fecha Proximo Mantenimiento</span>
                                <input type="date" class="form-control" v-model="documento.vehiculo.proximaFechaMantenimiento" required>
                            </div>


                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-12 hidden-sm hidden-xs">
                    <div class="panel panel-default">
                        <div class="panel-heading">Detalle & Observaciones</div>
                        <div class="panel-body">
                            <div class="form-group">
                                <textarea class="form-control" rows="2" v-model="documento.observacion" maxlength="250" placeholder="Comentario de hasta maximo 250 caracteres."></textarea>
                            </div>

                        </div>
                    </div>
                </div>
                
            </div>

        
            <!--  items-->
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                    <!-- Default panel contents -->
                
                    <div class="panel-heading clearfix">
                        <h4 class="panel-title pull-left" style="padding-top: 7.5px;"><i class="fa fa-address-book" aria-hidden="true"></i></i> Búsqueda de nuevo item</h4>
                        
                    </div>
                                        
                    <div class="panel-body">
                        <div class="responsibetable">     
                            <table id="tablaAgregaNuevo" class="table table-bordered tableExtras">
                                <thead>
                                <tr>
                                    <th style="width: 5%; min-width: 150px;" class="text-center headerTablaProducto">Codigo</th>
                                    <th style="width: 10%; min-width: 200px;" class="text-center headerTablaProducto">Nombre del Articulo</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="input-group">
                                            <input type="text" @change="getProducto" v-model="search_producto.busqueda.texto" class="form-control text-center input-sm" placeholder="Codigo de Producto">
                                            <span class="input-group-btn">
                                                <button id="btnSeachProductos" class="btn btn-default input-sm" type="button" data-toggle="modal" data-target="#modalBuscarProducto">
                                                    <span class="glyphicon glyphicon-search" aria-hidden="true"></span>
                                                </button>
                                            </span>
                                            </div><!-- /input-group -->
                                        </td>
                                        <td>
                                            <input type="text" v-model="nuevo_producto.nombre" class="form-control text-center input-sm" readonly>
                                        </td>
                                    </tr>

                                    
                                    
                                </tbody>
                            </table>
                                <button type="button" @click="addToList" class="btn btn-success btn-sm"><span class="glyphicon glyphicon-arrow-down"></span> Agregar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- items en lista KIT -->

            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <!-- Default panel contents -->
                    
                        <div class="panel-heading clearfix">
                        <h4 class="panel-title pull-left" style="padding-top: 7.5px; padding-bottom: 7.5px;"><i class="fa fa-list" aria-hidden="true"></i> Lista de Items</h4>
                            <div class="btn-group pull-right">
                                
                            </div>
                        </div>

                        <div class="panel-body">
                            <div class="responsibetable">        
                                <table class="table table-bordered tableExtras">
                                    <thead>
                                        <tr>
                                            <th style="width: 5%; min-width: 50px;" class="text-center headerTablaProducto">Codigo</th>
                                            <th style="width: 20%; min-width: 200px;" class="text-center headerTablaProducto">Nombre del Articulo</th>
                                            
                                            <th style="width: 5%" class="text-center headerTablaProducto">Eliminar</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tablaProductos">
                                        <tr v-for="producto in documento.items">
                                            <td><input type="text" class="form-control text-center input-sm"  v-model="producto.codigo" readonly></td>
                                            <td><input type="text" class="form-control text-center input-sm"  v-model="producto.nombre" readonly></td>
                                            <td><button type="button" @click="removeItem(producto.codigo)" class="btn btn-danger btn-sm btn-block"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Eliminar</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                    
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row extraButton">
                <div class="col-md-12">
                    <div class="btn-group btn-group-justified" role="group" aria-label="...">
                    

                        <div class="btn-group" role="group">
                            <button type="submit" class="btn btn-primary btn-lg"><span class="glyphicon glyphicon-floppy-saved" aria-hidden="true"></span> Registrar</button>
                        </div>

                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-danger btn-lg" id="btnCancel"><span class="glyphicon glyphicon-floppy-remove" aria-hidden="true"></span> Cancelar</button>
                        </div>
                
                    </div>
                </div>
            </div>    

        </div>

        

        <!-- Modal Info sesion -->
        <?php require_once 'sis_modules/modal_info_session.php'?>
        <!-- Modal Info sesion -->
        <?php require_once 'sis_modules/modal_producto_vehiculos.php'?>
         <!-- Modal Buscar Documento -->
         <?php require_once 'sis_modules/modalBuscarDocumento.php'?>

    </form>

    
    <!-- USO JQUERY, y Bootstrap CDN-->
    <script src="assets\js\vue.js"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\jquery-3.3.1.min.js"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\bootstrap-datepicker.js"></script>

    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\bootstrap.min.js"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\popper.min.js"></script>

    <!-- Toastr script -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\pnotify.custom.min.js"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\sweetalert.min.js"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\pages\ordenPedidoVehiculo.js"></script>
        
