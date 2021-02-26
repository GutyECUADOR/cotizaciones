<?php

use App\Controllers\CotizacionController;

if (!isset($_SESSION["usuarioRUC".APP_UNIQUE_KEY])){
    header("Location:index.php?&action=login");  
 }   

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

    <div id="app" class="container card">
        <!-- Hidden Inputs-->
        <input id="hiddenBodegaDefault" type="hidden" value="<?php echo $_SESSION["bodegaDefault"]?>">

        <!-- Row de cabecera-->
        <div class="row">
            <div class="form-group formextra col-lg-offset-3 col-lg-6">
                <div class="text-center">
                    <?php echo $cotizacion->getStatusDataBase(); ?>
                </div>
            </div>

            <div class="form-group formextra text-center col-lg-12">
                <h4>{{ title }}</h4>
            </div>
        </div>

        <!-- Egreso de items-->
        
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                <!-- Default panel contents -->
            
                <div class="panel-heading clearfix">
                    <h4 class="panel-title pull-left" style="padding-top: 7.5px;"><i class="fa fa-address-book" aria-hidden="true"></i></i> Egreso de items</h4>
                    
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
                        <button type="button" class="btn btn-danger btn-sm" id="btnAgregarProdToList"><span class="glyphicon glyphicon-shopping-cart"></span> Agregar item </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- items en lista egreso-->

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

        <!-- Ingreso de items-->
        
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                <!-- Default panel contents -->
            
                <div class="panel-heading clearfix">
                    <h4 class="panel-title pull-left" style="padding-top: 7.5px;"><i class="fa fa-plus-circle" aria-hidden="true"></i> Egreso de items</h4>
                    
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

        <!-- items en lista egreso-->


        <!-- fila de resumen-->
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
                            <th style="width: 10%; min-width: 100px;" class="text-center headerTablaProducto">Calculo Tramaco * Factor</th>
                            <th style="width: 10%; min-width: 100px;" class="text-center headerTablaProducto">Valor Seguro de Envio</th>
                            <th style="width: 20%; min-width: 100px;" class="text-center headerTablaProducto">Subtotal</th>
                            <th style="width: 10%; min-width: 100px;" class="text-center headerTablaProducto">IVA</th>
                            <th style="width: 20%; min-width: 150px;" class="text-center headerTablaProducto">Total</th>
                        </thead>
                        <tbody>
                        <tr>
                            <td><input type="text" class="form-control text-center" id="txt_unidadesProd" readonly></td>
                            <td><input type="text" class="form-control text-center" id="txt_descuentoResumen" readonly></td>
                            <td><input type="text" class="form-control text-center" id="txt_valortramaco_envio" value="0" readonly></td>
                            <td><input type="text" class="form-control text-center" id="txt_costo_seguro_envio" readonly></td>
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
                        <button type="button" class="btn btn-primary btn-lg" id="btnGuardar"><span class="glyphicon glyphicon-floppy-saved" aria-hidden="true"></span> Registrar</button>
                    </div>

                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-danger btn-lg" id="btnCancel"><span class="glyphicon glyphicon-floppy-remove" aria-hidden="true"></span> Cancelar</button>
                    </div>
               
                </div>
            </div>
        </div>    

        <!-- Modal Info sesion -->
        <?php require_once 'sis_modules/modal_info_session.php'?>

        <!-- Modal Cliente -->
       <?php require_once 'sis_modules/modal_cliente.php'?>

        <!-- Modal Cliente Nuevo -->
       <?php require_once 'sis_modules/modal_cliente_nuevo.php'?>

        <!-- Modal Producto -->
        <?php require_once 'sis_modules/modal_producto.php'?>

        <!-- Modal Producto -->
        <?php require_once 'sis_modules/modal_detalle_promo.php'?>

        <!-- Modal Buscar Documento -->
        <?php require_once 'sis_modules/modal_buscardoc.php'?>

        <!-- Modal Enviar Email Personalizado -->
        <?php require_once 'sis_modules/modal_SendEmail.php'?>

        <!-- Modal agregar fotos y detalles extra -->
        <?php require_once 'sis_modules/modal_addExtraDetailProduct.php'?>
     
    </div>


    <!-- USO JQUERY, y Bootstrap CDN-->
    <script src="assets\js\vue.js"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\jquery-3.3.1.min.js"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\bootstrap-datepicker.js"></script>
   
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\bootstrap.min.js"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\popper.min.js"></script>
  
     <!-- JS Propio-->
    
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\pnotify.custom.min.js"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\sweetalert2@8.js"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\bootstrap-datepicker.es.min.js"></script>
    <script src="https://cloud.tinymce.com/5/tinymce.min.js?apiKey=ubmvgme7f7n7likjbniglty12b9m92um98w9m75mdtnphwqp"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\tinymce.js"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\datepicker.js"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\xlsx.full.min.js"></script>
    
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\pages\puntodeVenta.js"></script>
