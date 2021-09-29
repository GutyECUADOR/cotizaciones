<?php

use App\Controllers\CotizacionController;
use App\Middleware\RouteMiddleware;

$routeMiddleware = new RouteMiddleware();
$routeMiddleware->checkisLogin(); 

$cotizacion = new CotizacionController();
$bodegas = $cotizacion->getBodegas();
$vendedores = $cotizacion->getVendedores();
$formasPago = $cotizacion->getFormasPago();
$tiposTarjeta = $cotizacion->getTiposPagoTarjeta();
$grupos = $cotizacion->getGrupos(); //Para la creacion de nuevo cliente en modal
$cantones = $cotizacion->getCantones();  //Para la creacion de nuevo cliente en modal
$tiposDOC = $cotizacion->getVenTiposDOCWF();

?>
 <!-- CSS Propios -->
 <link rel="stylesheet" href="<?php echo ROOT_PATH; ?>assets\css\cotizacionStyles.css">

 <?php include 'sis_modules/header_main.php'?>

    <form id="app" v-on:submit.prevent="saveDocumento">
        <div class="container">
            <ol class="breadcrumb">
                <li><a href="?action=inicio">Inicio</a></li>
                <li><a href="#">Ventas</a></li>
                <li class="active">{{ titulo }}</li>
            </ol>
        </div>
    
        <div class="container card">
            <!-- Hidden Inputs-->
            <input id="hiddenBodegaDefault" type="hidden" value="<?php echo $_SESSION["bodegaDefault".APP_UNIQUE_KEY]?>">

            <!-- Row de cabecera-->
            <div class="row">
            
                <div class="form-group formextra col-lg-6">
                    <div class="input-group">
                            <span class="input-group-addon"  id="testButton">Tipo Doc</span>
                            <select class="form-control input-sm">
                                <option>Cotizacion</option>
                            </select>
                    </div>
                </div>

                <div class="form-group formextra col-lg-6 pull-right hidden-md hidden-sm hidden-xs">
                    <div class="input-group">
                        <input type="text" class="form-control input-sm" placeholder="Estado">
                        <span class="input-group-addon">Estado</span>
                    </div>
                </div>
            </div>

            <!-- Row Modal Buscar Documento-->
            <div class="row">
                <div class="form-group formextra col-lg-4">
                    <span class="input-group-addon bordederecho">Buscar</span>
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control">
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="button" data-toggle="modal" data-target="#modalBuscarDocumento">
                                <span class="glyphicon glyphicon-triangle-bottom" aria-hidden="true">
                                </span>
                            </button>
                        </span>
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="button"><span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span></button>
                        </span>
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="button"><span class="glyphicon glyphicon-duplicate" aria-hidden="true"></span></button>
                        </span>
                        
                    </div>
                </div>

                <div class="form-group formextra col-lg-2">
                    <span class="input-group-addon bordederecho">Fecha Emisión</span>
                    <input type="date" class="form-control text-center" v-model="documento.fecha">
                </div>

                <div class="form-group formextra col-lg-2">
                    <span class="input-group-addon bordederecho">Bodega/Almacen</span>
                    <select class="form-control input-sm" v-model="documento.bodega">
                        <?php
                            $bodega_default = $_SESSION["bodegaDefault".APP_UNIQUE_KEY];
                            foreach ($bodegas as $bodega => $row) {

                                $codigo = $row['CODIGO'];
                                $texto= $row['NOMBRE']; 
                                
                                if($bodega_default == $codigo ){
                                    echo "<option value='$codigo' selected>$texto</option>";
                                }else{
                                    echo "<option value='$codigo'>$texto</option>";
                                }

                                
                            }
                        
                        ?>
                    </select>
                </div>
                    
                <div class="form-group formextra col-lg-4 col-md-12">
                    <div class="well text-center wellextra" >
                        <span id="welltotal">$ 0.00</span>
                    </div>
                </div>
            </div>
        
            <!-- Row datos-->
            <div class="row">

                <div class="col-lg-4 col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">Datos del Cliente</div>
                        <div class="panel-body">
                            <div class="input-group input-group-sm">
                                <span class="input-group-addon">Cliente</span>
                                <input type="text" class="form-control" id="inputRUC">
                                <span class="input-group-btn">
                                    <button class="btn btn-default" type="button" data-toggle="modal" data-target="#modalBuscarCliente">
                                        <span class="glyphicon glyphicon-search" aria-hidden="true"></span>
                                    </button>
                                </span>
                                
                                <span class="input-group-btn">
                                    <button class="btn btn-default" type="button" data-toggle="modal" data-target="#modalClienteNuevo">
                                        <span class="glyphicon glyphicon-user" aria-hidden="true"></span>
                                    </button>
                                </span>
                                <input type="text" class="form-control" id="inputCodigo" readonly>
                                
                            </div>

                            <div class="input-group input-group-sm">
                                <span class="input-group-addon" id="sizing-addon3">Nombre</span>
                                <input type="text" class="form-control" placeholder="Nombre Cliente" id="inputNombre" readonly>
                            </div>

                            <div class="input-group input-group-sm">
                                <span class="input-group-addon" id="sizing-addon3">Razon Social</span>
                                <input type="text" class="form-control" placeholder="Razon Social" id="inputRSocial" readonly>
                            </div>

                            <div class="input-group input-group-sm">
                                <span class="input-group-addon" id="sizing-addon3">Correo</span>
                                <input type="mail" class="form-control" placeholder="Correo" id="inputCorreo" readonly>
                            </div>

                            <div class="input-group input-group-sm">
                                <span class="input-group-addon"><span class="glyphicon glyphicon-earphone" aria-hidden="true"></span> Telf.</span>
                                <input type="text" class="form-control text-center" placeholder="Telefono" id="inputTelefono" readonly>
                                <span class="input-group-addon">Dias Pago</span>
                                <input type="text" class="form-control" placeholder="DiasPago" id="inputDiasPago" readonly>
                            </div>

                            <div class="input-group input-group-sm">
                                <span class="input-group-addon" id="sizing-addon3">Vendedor</span>
                                <input type="text" class="form-control" placeholder="Vendedor" id="inputVendedor" readonly>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-12">
                    <div class="panel panel-default">
                    <div class="panel-heading">Datos de Cotizaciones</div>
                        <div class="panel-body">
                            
                            <div class="input-group input-group-sm">
                                <span class="input-group-addon">Forma Pago</span>
                                <select id='formaPago' class="form-control input-sm">
                                        <?php
                                        foreach ($formasPago as $grupo => $row) {

                                            $codigo = trim($row['CODIGO']);
                                            $texto= $row['NOMBRE']; 
                                            
                                            echo "<option value='$codigo'>$texto</option>";
                                        }
                                        
                                        ?>
                                </select>
                            </div>

                            <div class="input-group input-group-sm">
                                <span class="input-group-addon">Condiciones Pago</span>
                                <select id='condicionPago' class="form-control input-sm">
                                        <?php
                                        foreach ($tiposTarjeta as $grupo => $row) {

                                            $codigo = trim($row['CODIGO']);
                                            $texto= $row['NOMBRE']; 
                                            
                                            echo "<option value='$codigo'>$texto</option>";
                                        }
                                        
                                        ?>
                                </select>
                            </div>

                            
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">Detalle / Observacion del documento</div>
                        <div class="panel-body">
                            <div class="form-group">
                                <textarea class="form-control" rows="2" id="comment" name="comment" maxlength="100" placeholder="Comentario de hasta maximo 100 caracteres..."></textarea>
                            </div>

                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-12">
                    

                    <div class="panel panel-default">
                        <div class="panel-heading">Detalle extras del envio</div>
                        <div class="panel-body">
                            <div class="form-group">
                                <textarea class="form-control" rows="5" id="comment_envio" name="comment_envio" maxlength="300" placeholder="Comentario de hasta maximo 300 caracteres..." disabled></textarea>
                            </div>
                            <div class="input-group input-group-sm">
                                <span class="input-group-addon" id="sizing-addon3">Tipo Precio Cliente</span>
                                <input type="text" id="inputTipoPrecioCli" class="form-control" disabled>
                            </div>
            

                        </div>
                    
                    </div>
                </div>
            </div>
            
            <!-- agregar productos-->
            
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                    <!-- Default panel contents -->
                
                    <div class="panel-heading clearfix">
                        <h4 class="panel-title pull-left" style="padding-top: 7.5px;"><i class="fa fa-plus-circle" aria-hidden="true"></i> Nuevo Item</h4>
                        
                    </div>
                                        
                    <div class="panel-body">
                        <div class="responsibetable">     
                            <table id="tablaAgregaNuevo" class="table table-bordered tableExtras">
                                <thead>
                                <tr>
                                    <th style="width: 5%; min-width: 170px;" class="text-center headerTablaProducto">Codigo</th>
                                    <th style="width: 10%; min-width: 200px;" class="text-center headerTablaProducto">Nombre del Articulo</th>
                                    <th style="width: 2%; min-width: 80px;"  class="text-center headerTablaProducto">Cantidad</th>
                                    <th style="width: 2%; min-width: 80px;"  class="text-center headerTablaProducto">Vendedor</th>
                                    <th style="width: 5%; min-width: 80px;" class="text-center headerTablaProducto">Precio</th>
                                    <th style="width: 5%; min-width: 80px;" class="text-center headerTablaProducto">Peso (Kg)</th>
                                    <th style="width: 5%; min-width: 80px;" class="text-center headerTablaProducto">Sotck local</th>
                                    <th style="width: 5%; min-width: 110px;" class="text-center headerTablaProducto">Cod. Promocion</th>
                                    <th style="width: 5%; min-width: 100px;" class="text-center headerTablaProducto">Fecha Validez</th>
                                    <th style="width: 5%; min-width: 60px;" class="text-center headerTablaProducto">% Desc</th>
                                    <th style="width: 5%; min-width: 120px;" class="text-center headerTablaProducto">Subtotal</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="input-group">
                                            <input type="text" id="inputNuevoCodProducto" class="form-control text-center input-sm" placeholder="Cod Producto...">
                                            <span class="input-group-btn">
                                                <button id="btnSeachProductos" class="btn btn-default input-sm" type="button" data-toggle="modal" data-target="#modalBuscarProducto">
                                                    <span class="glyphicon glyphicon-search" aria-hidden="true"></span>
                                                </button>
                                            </span>
                                            </div><!-- /input-group -->
                                        </td>
                                        <td>
                                            <input type="text" id="inputNuevoProductoNombre" class="form-control text-center input-sm" readonly>
                                        </td>
                                        <td>
                                            <input type="number" id="inputNuevoProductoCantidad" class="form-control text-center input-sm" value="1"  min="1" oninput="validity.valid||(value='1');"></td>
                                        </td>
                                        <td>
                                            <input type="number" id="inputNuevoVendedor" class="form-control text-center input-sm" min="0" value="0" readonly></td>
                                        <td>
                                            <input type="text" id="inputNuevoProductoPrecioUnitario" class="form-control text-center input-sm" readonly>
                                        </td>
                                        <td>
                                            <input type="text" id="inputNuevoProductoPesoUnitario" class="form-control text-center input-sm" readonly>
                                        </td>
                                        <td>
                                            <input type="text" id="inputNuevoProductoStockLocal" class="form-control text-center input-sm" readonly>
                                        </td>
                                        <td>
                                            <div class="input-group">
                                            <input type="text" id="inputNuevoProductoCodProm" class="form-control text-center input-sm" readonly>
                                            <span class="input-group-btn">
                                                <button id="btnDetallePromo" class="btn btn-default input-sm" type="button">
                                                    <span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>
                                                </button>
                                            </span>
                                            </div><!-- /input-group -->
                                        </td>
                                        <td>
                                            <input type="text" id="inputNuevoProductoValidezProm" class="form-control text-center input-sm" readonly>
                                        </td>
                                        <td>
                                            <input type="text" id="inputNuevoProductoDesc" class="form-control text-center input-sm" readonly>
                                        </td>
                                        <td>
                                            <input type="text"  id="inputNuevoProductoSubtotal" class="form-control text-center input-sm importe_linea" readonly></td>
                                        </>
                                    </tr>

                                    
                                    
                                </tbody>
                            </table>
                            <button type="button" class="btn btn-primary btn-sm" id="btnAgregarProdToList"><span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span> Agregar item</button>
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
                        <h4 class="panel-title pull-left" style="padding-top: 7.5px;"><i class="fa fa-shopping-cart" aria-hidden="true"></i> Items a facturar</h4>
                        <div class="btn-group pull-right">
                        </div>
                        </div>

                        <div class="panel-body">
                            <div class="responsibetable">        
                                <table class="table table-bordered tableExtras">
                                    <thead>
                                        <tr>
                                            <th style="width: 10%; min-width: 110px;" class="text-center headerTablaProducto">Codigo</th>
                                            <th style="width: 20%; min-width: 250px;" class="text-center headerTablaProducto">Nombre del Articulo</th>
                                            <th style="width: 3%"  class="text-center headerTablaProducto">Cantidad</th>
                                            <th style="width: 3%"  class="text-center headerTablaProducto">Vendedor</th>
                                            <th style="width: 5%; min-width: 70px;" class="text-center headerTablaProducto">Precio</th>
                                            <th style="width: 5%; min-width: 90px;" class="text-center headerTablaProducto">Peso (Kg)</th>
                                            <th style="width: 5%; min-width: 90px;" class="text-center headerTablaProducto">Stock</th>
                                            <th style="width: 5%; min-width: 70px;" class="text-center headerTablaProducto">% Desc</th>
                                            <th style="width: 10%; min-width: 70px;" class="text-center headerTablaProducto">Subtotal</th>
                                            <th style="width: 5%; min-width: 70px;" class="text-center headerTablaProducto">IVA</th>
                                            <th style="width: 5%" class="text-center headerTablaProducto">Eliminar</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tablaProductos">
                                        <!--Resultados de busqueda aqui -->
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="8"></td>
                                            <td class="text-center" style="vertical-align: middle;"><b>Subtotal Productos</b></td>
                                            <td colspan="2">
                                            <input type="text" id="inputSubTotalProductos" class="form-control text-center" readonly></td>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="8"></td>
                                            <td class="text-center" style="vertical-align: middle;"><b>IVA Productos</b></td>
                                            <td colspan="2">
                                            <input type="text" id="inputIVAProductos" class="form-control text-center" readonly></td>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="8"></td>
                                            <td class="text-center" style="vertical-align: middle;"><b>Total Productos</b></td>
                                            <td colspan="2">
                                            <input type="text" id="inputTotalProductos" class="form-control text-center" readonly></td>
                                            </td>
                                        </tr>

                                    </tfoot>
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
                    <h4 class="panel-title pull-left">Resumen</h4>
                    </div>

                    <div class="panel-body">
                        <div class="responsibetable">        
                            <table class="table table-bordered tableExtras">
                            <thead>
                                <th style="width: 5%; min-width: 80px;" class="text-center headerTablaProducto">Unidades</th>
                                <th style="width: 10%; min-width: 100px;" class="text-center headerTablaProducto">Descuento</th>
                                <th style="width: 20%; min-width: 100px;" class="text-center headerTablaProducto">Subtotal</th>
                                <th style="width: 10%; min-width: 100px;" class="text-center headerTablaProducto">IVA</th>
                                <th style="width: 20%; min-width: 150px;" class="text-center headerTablaProducto">Total</th>
                            </thead>
                            <tbody>
                            <tr>
                                <td><input type="text" class="form-control text-center" id="txt_unidadesProd" readonly></td>
                                <td><input type="text" class="form-control text-center" id="txt_descuentoResumen" readonly></td>
                                <td><input type="text" class="form-control text-center" id="txt_subtotal" value="0" readonly></td>
                                <td><input type="text" class="form-control text-center" id="txt_impuesto" readonly></td>
                                <td><input type="text" class="form-control text-center" id="txt_totalPagar" readonly></td>
                                
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
                            <button type="submit" class="btn btn-primary btn-lg" id="btnGuardar"><span class="glyphicon glyphicon-floppy-saved" aria-hidden="true"></span> Registrar</button>
                        </div>

                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-danger btn-lg" id="btnCancel"><span class="glyphicon glyphicon-floppy-remove" aria-hidden="true"></span> Cancelar</button>
                        </div>
                
                    </div>
                </div>
            </div>    

            <!-- Modal Info sesion -->
            <?php require_once 'sis_modules/modal_info_session.php'?>

            <!-- Modal Buscar Documento -->
            <?php require_once 'sis_modules/modalBuscarDocumento.php'?>

            <!-- Modal Cliente -->
            <?php require_once 'sis_modules/modalBuscarCliente.php'?>

                <!-- Modal Cliente Nuevo -->
            <?php require_once 'sis_modules/modal_cliente_nuevo.php'?>

            <!-- Modal Producto -->
            <?php require_once 'sis_modules/modal_producto-cotizaciones.php'?>

            <!-- Modal Producto -->
            <?php require_once 'sis_modules/modal_detalle_promo.php'?>

           

            <!-- Modal Enviar Email Personalizado -->
            <?php require_once 'sis_modules/modal_SendEmail.php'?>

            <!-- Modal agregar fotos y detalles extra -->
            <?php require_once 'sis_modules/modal_addExtraDetailProduct.php'?>
        
        </div>
    </form>

    <!-- USO JQUERY, y Bootstrap CDN-->
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\vue.js"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\jquery-3.3.1.min.js"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\bootstrap-datepicker.js"></script>
   
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\bootstrap.min.js"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\popper.min.js"></script>
  
    <!-- JS Propio-->
    
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\pnotify.custom.min.js"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\sweetalert2@8.js"></script>

    

    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\moment.min.js"></script>
    
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\pages\puntodeVenta.js"></script>
