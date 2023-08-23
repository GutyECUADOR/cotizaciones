<?php
use App\Middleware\RouteMiddleware;

$routeMiddleware = new RouteMiddleware();
$routeMiddleware->checkisLogin();

?>

<!-- CSS Propios -->
<link rel="stylesheet" href="<?php echo ROOT_PATH; ?>assets\css\cotizacionStyles.css">
<link rel="stylesheet" href="<?php echo ROOT_PATH; ?>assets\css\admin.css">


<div id="wrapper">
    <!-- Sidebar -->
    <?php include './views/modulos/sis_modules/main_sidebar_admin.php'?>

    <!-- Page Content -->
    <div id="page-content-wrapper">
      <?php include './views/modulos/sis_modules/header_main_admin.php'?>

        <div class="container-fluid">
        
          <ol class="breadcrumb">
              <li><a href="?action=inicio">Inicio</a></li>
              <li><a href="#">Vehiculos</a></li>
              <li class="active">Crear pago de vehiculo (Admin)</li>
          </ol>

        
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control" id="inputRUC" placeholder="RUC del proveedor" required>
                            <input type="hidden" class="form-control" id="inputIDDocument" value="<?php echo $_GET['codOrden']?>" required>
                            <span class="input-group-btn">
                                <button class="btn btn-default" type="button" data-toggle="modal" data-target="#modalBuscarCliente"><span class="glyphicon glyphicon-search"></span> Buscar</button>
                            </span>
                        </div>
                        <div class="input-group input-group-sm">
                         <span class="input-group-addon">Proveedor: </span>
                            <input id="inputNombre" type="text" class="form-control" readonly>
                        </div>
                    </div>
                </div>

                <!-- agregar productos-->
        
                <div class="row">
                    <div class="col-md-12">
                    <div class="panel panel-default">
                        <!-- Default panel contents -->
                    
                        <div class="panel-heading clearfix">
                        <h4 class="panel-title pull-left" style="padding-top: 7.5px;">Nuevo Item</h4>
                        <div class="btn-group pull-right">
                            <button type="button" class="btn btn-primary btn-sm" id="btnAgregarProdToList"><span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span> Agregar item</button>
                        </div>
                        </div>

                        <div class="panel-body">
                            <div id="">        
                            <table id="tablaAgregaNuevo" class="table table-bordered tableExtras">
                                <thead>
                                <tr>
                                    <th style="width: 5%; font-size: 12px;" class="text-center headerTablaProducto">Codigo</th>
                                    <th style="width: 10%; font-size: 12px;" class="text-center headerTablaProducto">Nombre del Articulo</th>
                                    <th style="width: 2%; font-size: 12px;"  class="text-center headerTablaProducto">Cantidad</th>
                                    <th style="width: 5%; font-size: 12px;" class="text-center headerTablaProducto">Precio</th>

                                    <th style="width: 5%; font-size: 12px;" class="text-center headerTablaProducto">Subtotal</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="input-group">
                                            <input type="text" id="inputNuevoCodProducto" class="form-control text-center" placeholder="Cod Producto...">
                                            <span class="input-group-btn">
                                                <button class="btn btn-default" type="button" data-toggle="modal" data-target="#modalBuscarProducto"><span class="glyphicon glyphicon-search"></span> Buscar</button>
                                            </span>
                                            
                                            </div><!-- /input-group -->
                                        </td>
                                        <td>
                                            <input type="text" id="inputNuevoProductoNombre" class="form-control text-center" readonly>
                                        </td>
                                        <td><input type="number" id="inputNuevoProductoCantidad" class="form-control text-center" value="0"></td>
                                        <td>
                                            <input type="number" id="inputNuevoProductoPrecioUnitario" class="form-control text-center">
                                            
                                        </td>
                                        
                                        <td><input type="text"  id="inputNuevoProductoSubtotal" class="form-control text-center importe_linea" readonly></td>
                                      
                                        </td>
                                    </tr>

                                    
                                      
                                </tbody>
                            </table>

                            </div>
                        </div>

                    </div>
                    </div>
                </div> 

                <!-- items en lista-->

                <div class="row">
                    <div class="col-md-12">
                    <div class="panel panel-default">
                        <!-- Default panel contents -->
                    
                        <div class="panel-heading clearfix">
                        <h4 class="panel-title pull-left" style="padding-top: 7.5px;">Items en lista</h4>
                        <div class="btn-group pull-right">
                        </div>
                        </div>

                        <div class="panel-body">
                            <div id="responsibetable" style="height: auto; !important">        
                            <table id="tablaProductos" class="table table-bordered tableExtras">
                                <thead>
                                <tr>
                                    <th style="width: 10%; font-size: 12px;" class="text-center headerTablaProducto">Codigo</th>
                                    <th style="width: 20%; font-size: 12px;" class="text-center headerTablaProducto">Nombre del Articulo</th>
                                    <th style="width: 3%; font-size: 12px;"  class="text-center headerTablaProducto">Cantidad</th>
                                    <th style="width: 5%; font-size: 12px;" class="text-center headerTablaProducto">Precio</th>
                                    <th style="width: 10%; font-size: 12px;" class="text-center headerTablaProducto">Subtotal</th>
                                    <th style="width: 5%; font-size: 12px;" class="text-center headerTablaProducto">IVA</th>
                                    <th style="width: 5%; font-size: 12px;" class="text-center headerTablaProducto">Eliminar</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <!--Resultados de busqueda aqui -->
                                </tbody>
                            </table>

                            </div>
                        </div>

                    </div>
                    </div>
                </div>

                <!-- fila de resumen de pago-->
                <div class="row">
                    <div class="col-md-12">
                    <div class="panel panel-default">
                        <!-- Default panel contents -->
                    
                        <div class="panel-heading clearfix">
                        <h4 class="panel-title pull-left" style="padding-top: 7.5px;">Resumen</h4>
                        </div>

                        <div class="panel-body">
                            <div id="responsibetable" style="height: auto; !important">        
                                <table class="table table-bordered tableExtras">
                                <thead>
                                    <th style="width: 5%; font-size: 12px;" class="text-center headerTablaProducto">Unidades</th>
                                    <th style="width: 10%; font-size: 12px;" class="text-center headerTablaProducto">IVA Bienes</th>
                                    <th style="width: 5%; font-size: 12px;" class="text-center headerTablaProducto">% ICE</th>
                                    <th style="width: 10%; font-size: 12px;" class="text-center headerTablaProducto">Base ICE</th>
                                    <th style="width: 20%; font-size: 12px;" class="text-center headerTablaProducto">Subtotal</th>
                                    <th style="width: 10%; font-size: 12px;" class="text-center headerTablaProducto">Descuento</th>
                                    <th style="width: 5%; font-size: 12px;" class="text-center headerTablaProducto">ICE</th>
                                    <th style="width: 10%; font-size: 12px;" class="text-center headerTablaProducto">Impuesto</th>
                                    <th style="width: 10%; font-size: 12px;" class="text-center headerTablaProducto">Gastos</th>
                                    <th style="width: 20%; font-size: 12px;" class="text-center headerTablaProducto">Total</th>
                                </thead>
                                <tbody>
                                <tr>
                                    <td><input type="text" class="form-control text-center" id="txt_unidadesProd"></td>
                                    <td><input type="text" class="form-control text-center" id="txt_ivaBienes" readonly></td>
                                    <td><select class="form-control input-sm centertext"></select></td>
                                    <td><input type="text" class="form-control text-center" readonly></td>
                                    <td><input type="text" class="form-control text-center" id="txt_subtotal" value="0" readonly></td>
                                    <td><input type="text" class="form-control text-center" id="txt_descuentoResumen" readonly></td>
                                    <td><input type="text" class="form-control text-center" readonly></td>
                                    <td><input type="text" class="form-control text-center" id="txt_impuesto" readonly></td>
                                    <td><input type="text" class="form-control text-center" id="txt_gastos" readonly></td>
                                    <td><input type="text" class="form-control text-center" id="txt_totalPagar" readonly></td>
                                    
                                </tr>
                            
                                </tbody>
                                </table>

                            </div>
                        </div>

                    </div>
                    </div>
                </div> 

                <div class="row">
                    <div class="col-md-12" style="padding-bottom:10px">
                        <div class="btn-group btn-group-justified" role="group" aria-label="...">
                        
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-primary btn-lg" id="btnGuardar"><span class="glyphicon glyphicon-floppy-saved" aria-hidden="true"></span> Guardar</button>
                            </div>

                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-danger btn-lg" id="btnCancel"><span class="glyphicon glyphicon-floppy-remove" aria-hidden="true"></span> Cancelar</button>
                            </div>
                    
                        </div>
                    </div>
                </div>    

         
        </div>  <!-- container-fluid -->
    </div>
    <!-- /#page-content-wrapper -->
    
    <?php require_once './views/modulos/sis_modules/modalBuscarCliente.php';?>
    <?php require_once './views/modulos/sis_modules/modalBuscarProducto.php';?>
      
   
</div>

<!-- USO JQUERY, y Bootstrap CDN-->
<script src="assets\js\vue.js"></script>
<script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\bootstrap-datepicker.js"></script>

<script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\popper.min.js"></script>

<!-- JS Propio-->

<script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\admin.js"></script>
<script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\pages\admin\crearPago-admin.js"></script>