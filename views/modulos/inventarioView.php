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
                                <th style="width: 5%; min-width: 80px;" class="text-center headerTablaProducto">Precio</th>
                                <th style="width: 5%; min-width: 80px;" class="text-center headerTablaProducto">Peso (Kg)</th>
                                <th style="width: 5%; min-width: 80px;" class="text-center headerTablaProducto">Stock local</th>
                                <th style="width: 5%; min-width: 120px;" class="text-center headerTablaProducto">Subtotal</th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="input-group">
                                        <input type="text" @change="getProducto" v-model="search_producto.text" class="form-control text-center input-sm" placeholder="Codigo de Producto">
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
                                    <td>
                                        <input type="number" v-model="nuevo_producto.cantidad" class="form-control text-center input-sm" value="1"  min="1" oninput="validity.valid||(value='1');"></td>
                                    </td>
                                    <td>
                                        <input type="number" v-model="nuevo_producto.precio" class="form-control text-center input-sm" min="0" value="0" readonly></td>
                                    <td>
                                        <input type="text" v-model="nuevo_producto.peso" class="form-control text-center input-sm" readonly>
                                    </td>
                                    <td>
                                        <input type="text" v-model="nuevo_producto.stock" class="form-control text-center input-sm" readonly>
                                    </td>
                                    <td>
                                        <input type="text" v-model="nuevo_producto.getSubtotal()" class="form-control text-center input-sm importe_linea" readonly>
                                    </td>
                                </tr>

                                
                                
                            </tbody>
                        </table>
                            <button type="button" @click="addToEgresoList" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-arrow-up"></span> Agregar item a Egreso </button>
                            <button type="button" @click="addToIngresoList" class="btn btn-success btn-sm"><span class="glyphicon glyphicon-arrow-down"></span> Agregar item a Ingreso</button>
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
                    <h4 class="panel-title pull-left" style="padding-top: 7.5px;"><i class="fa fa-list" aria-hidden="true"></i> Lista de items a egresar</h4>
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
                                        <th style="width: 5%; min-width: 70px;" class="text-center headerTablaProducto">Precio</th>
                                        <th style="width: 5%; min-width: 90px;" class="text-center headerTablaProducto">Peso (Kg)</th>
                                        <th style="width: 5%; min-width: 90px;" class="text-center headerTablaProducto">Stock</th>
                                        <th style="width: 5%; min-width: 70px;" class="text-center headerTablaProducto">% Desc</th>
                                        <th style="width: 10%; min-width: 70px;" class="text-center headerTablaProducto">Subtotal</th>
                                        <th style="width: 5%; min-width: 70px;" class="text-center headerTablaProducto">IVA</th>
                                        <th style="width: 5%" class="text-center headerTablaProducto">Eliminar</th>
                                    </tr>
                                </thead>
                                <tbody id="tablaProductosEgreso">
                                    <tr v-for="producto in documento.productos_egreso.items">
                                        <td><input type="text" class="form-control text-center input-sm" v-model="producto.codigo" disabled></td>
                                        <td><input type="text" class="form-control text-center input-sm"  v-model="producto.nombre" readonly></td>
                                        <td><input type="number" class="form-control text-center input-sm rowcantidad" data-codigo="producto.codigo" v-model="producto.cantidad" min="1" oninput="validity.valid||(value='1');"></td>
                                        <td>
                                            <input type="text" class="form-control text-center input-sm" v-model="producto.precio" readonly>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control text-center input-sm rowpeso" step="0.01" v-model="producto.peso" min="0.1" oninput="validity.valid||(value='0.1');" readonly>
                                        </td>
                                        <td><input type="text" class="form-control text-center input-sm" v-model="producto.stock" disabled></td>
                                        <td><input type="text" class="form-control text-center input-sm" v-model="producto.descuento" disabled></td>
                                        <td><input type="text" class="form-control text-center input-sm" v-model="producto.getSubtotal()" readonly></td>
                                        <td><input type="text" class="form-control text-center input-sm" v-model="producto.getIVA()" readonly></td>
                                        <td><button type="button" @click="removeEgresoItem(producto.codigo)" class="btn btn-danger btn-sm btn-block"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="7"></td>
                                        <td class="text-center" style="vertical-align: middle;"><b>Subtotal Productos</b></td>
                                        <td colspan="2">
                                        <input type="text" v-model="documento.getSubTotal_Egresos()" class="form-control text-center" readonly></td>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="7"></td>
                                        <td class="text-center" style="vertical-align: middle;"><b>Peso Egresos</b></td>
                                        <td colspan="2">
                                        <input type="text" v-model="documento.getPeso_Egresos()" class="form-control text-center" readonly></td>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="7"></td>
                                        <td class="text-center" style="vertical-align: middle;"><b>IVA Productos</b></td>
                                        <td colspan="2">
                                        <input type="text"  v-model="documento.getIVA_Egresos()" class="form-control text-center" readonly></td>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="7"></td>
                                        <td class="text-center" style="vertical-align: middle;"><b>Total Productos</b></td>
                                        <td colspan="2">
                                        <input type="text" v-model="documento.getTotal_Egresos()" class="form-control text-center" readonly></td>
                                        </td>
                                    </tr>

                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- items en lista ingreso-->
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <!-- Default panel contents -->
                
                    <div class="panel-heading clearfix">
                    <h4 class="panel-title pull-left" style="padding-top: 7.5px;"><i class="fa fa-list" aria-hidden="true"></i> Lista de items a ingresar</h4>
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
                                        <th style="width: 5%; min-width: 70px;" class="text-center headerTablaProducto">Precio</th>
                                        <th style="width: 5%; min-width: 90px;" class="text-center headerTablaProducto">Peso (Kg)</th>
                                        <th style="width: 5%; min-width: 90px;" class="text-center headerTablaProducto">Stock</th>
                                        <th style="width: 5%; min-width: 70px;" class="text-center headerTablaProducto">% Desc</th>
                                        <th style="width: 10%; min-width: 70px;" class="text-center headerTablaProducto">Subtotal</th>
                                        <th style="width: 5%; min-width: 70px;" class="text-center headerTablaProducto">IVA</th>
                                        <th style="width: 5%" class="text-center headerTablaProducto">Eliminar</th>
                                    </tr>
                                </thead>
                                <tbody id="tablaProductosIngreso">
                                    <tr v-for="producto in documento.productos_ingreso.items">
                                        <td><input type="text" class="form-control text-center input-sm" v-model="producto.codigo" disabled></td>
                                        <td><input type="text" class="form-control text-center input-sm"  v-model="producto.nombre" readonly></td>
                                        <td><input type="number" class="form-control text-center input-sm rowcantidad" data-codigo="producto.codigo" v-model="producto.cantidad" min="1" oninput="validity.valid||(value='1');"></td>
                                        <td>
                                            <input type="text" class="form-control text-center input-sm" v-model="producto.precio" readonly>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control text-center input-sm rowpeso" step="0.01" v-model="producto.peso" min="0.1" oninput="validity.valid||(value='0.1');">
                                        </td>
                                        <td><input type="text" class="form-control text-center input-sm" v-model="producto.stock" disabled></td>
                                        <td><input type="text" class="form-control text-center input-sm" v-model="producto.descuento" disabled></td>
                                        <td><input type="text" class="form-control text-center input-sm" v-model="producto.getSubtotal()" readonly></td>
                                        <td><input type="text" class="form-control text-center input-sm" v-model="producto.getIVA()" readonly></td>
                                        <td><button type="button" @click="removeIngresoItem(producto.codigo)" class="btn btn-danger btn-sm btn-block"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="7"></td>
                                        <td class="text-center" style="vertical-align: middle;"><b>Subtotal Productos</b></td>
                                        <td colspan="2">
                                        <input type="number" v-model="documento.getSubTotal_Ingresos()" class="form-control text-center" readonly></td>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="7"></td>
                                        <td class="text-center" style="vertical-align: middle;"><b>Total Peso Egreso</b></td>
                                        <td colspan="2">
                                        <input type="text" v-model="documento.getPeso_Ingresos()" class="form-control text-center" readonly></td>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="7"></td>
                                        <td class="text-center" style="vertical-align: middle;"><b>IVA Productos</b></td>
                                        <td colspan="2">
                                        <input type="number" v-model="documento.getIVA_Ingresos()" class="form-control text-center" readonly></td>
                                        </td>
                                    </tr>
                                    
                                    <tr>
                                        <td colspan="7"></td>
                                        <td class="text-center" style="vertical-align: middle;"><b>Total Productos</b></td>
                                        <td colspan="2">
                                        <input type="number" v-model="documento.getTotal_Ingresos()" class="form-control text-center" readonly></td>
                                        </td>
                                    </tr>

                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
       

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
                            <th style="width: 10%; min-width: 100px;" class="text-center headerTablaProducto">Peso</th>
                            <th style="width: 20%; min-width: 100px;" class="text-center headerTablaProducto">Subtotal</th>
                            <th style="width: 10%; min-width: 100px;" class="text-center headerTablaProducto">IVA</th>
                            <th style="width: 20%; min-width: 150px;" class="text-center headerTablaProducto">Total</th>
                        </thead>
                        <tbody>
                        <tr>
                            <td><input type="text" v-model="documento.cantidad" class="form-control text-center" readonly></td>
                            <td><input type="text" v-model="documento.peso" class="form-control text-center" readonly></td>
                            <td><input type="text" v-model="documento.subtotal" class="form-control text-center" readonly></td>
                            <td><input type="text" v-model="documento.IVA" class="form-control text-center" readonly></td>
                            <td><input type="text" v-model="documento.total" class="form-control text-center" readonly></td>
                        
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
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets/js/sweetalert.min.js"></script>
    
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\pages\inventario.js?<?php echo date('Ymdhiiss')?>"></script>
