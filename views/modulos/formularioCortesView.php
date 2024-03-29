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

    <form id="app" v-on:submit.prevent>

        <div class="container">
            <ol class="breadcrumb">
                <li><a href="?action=inicio">Inicio</a></li>
                <li><a href="#">Inventario</a></li>
                <li class="active">{{ title }}</li>
            </ol>
        </div>

        <div class="container card">
            <!-- Hidden Inputs-->
            <input id="hiddenBodegaDefault" type="hidden" value="<?php echo $_SESSION["bodegaDefault".APP_UNIQUE_KEY]?>">

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

            <!-- Busqueda de items-->
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
                                    <th style="width: 5%; min-width: 100px;" class="text-center headerTablaProducto">Stock</th>
                                    <th style="width: 2%; min-width: 80px;"  class="text-center headerTablaProducto">Cantidad</th>
                                    <th style="width: 2%; min-width: 80px;"  class="text-center headerTablaProducto">Unidad</th>
                                    <th style="width: 5%; min-width: 120px;" class="text-center headerTablaProducto">Costo / {{ nuevo_producto.unidad }}</th>
                                    <th style="width: 5%; min-width: 120px;" class="text-center headerTablaProducto">Subtotal</th>
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
                                        <td>
                                            <input type="text" v-model="nuevo_producto.stock" class="form-control text-center input-sm" readonly>
                                        </td>
                                        <td>
                                            <input type="number" @change="nuevo_producto.setCantidad($event.target.value);" :value="nuevo_producto.cantidad" class="form-control text-center input-sm" step=".0001" min="0" oninput="validity.valid||(value=1);"></td>
                                        </td>
                                        <td>
                                            <select v-model='nuevo_producto.unidad' @change="getCostoProducto(nuevo_producto)" class="form-control input-sm">
                                                <option v-for="unidad in unidades_medida" :value="unidad.Unidad.trim()">
                                                {{unidad.Unidad}}
                                                </option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" v-model="nuevo_producto.precio" class="form-control text-center input-sm" min="0" value="0" step=".0001" >
                                        </td>
                                        <td>
                                            <input type="text" v-model="nuevo_producto.getSubtotal()" class="form-control text-center input-sm importe_linea" readonly>
                                        </td>
                                    </tr>

                                    
                                    
                                </tbody>
                            </table>
                                <button type="button" @click="addToEgresoList" :disabled="documento.productos_egreso.items.length>=1" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-arrow-up"></span> Agregar item a Egreso </button>
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
                        <h4 class="panel-title pull-left" style="padding-top: 7.5px; padding-bottom: 7.5px;"><i class="fa fa-list" aria-hidden="true"></i> Lista de items a egresar</h4>
                            <div class="btn-group pull-right">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon">Bodega egreso</span>
                                    <select id='bodegaEgreso' v-model="documento.productos_egreso.bodega" class="form-control input-sm" style="background-color: #ffe7e7;">
                                            <?php
                                            foreach ($bodegas as $bodega => $row) {

                                                $codigo = trim($row['CODIGO']);
                                                $texto= $row['NOMBRE']; 
                                                
                                                echo "<option value='$codigo'>$texto</option>";
                                            }
                                            
                                            ?>
                                    </select>
                                </div>   
                            </div>
                        </div>

                        <div class="panel-body">
                            <div class="responsibetable">        
                                <table class="table table-bordered tableExtras">
                                    <thead>
                                        <tr>
                                            <th style="width: 10%;" class="text-center headerTablaProducto">Codigo</th>
                                            <th style="width: 20%; min-width: 200px;" class="text-center headerTablaProducto">Nombre del Articulo</th>
                                            <th style="width: 5%; min-width: 90px;" class="text-center headerTablaProducto">Stock</th>
                                            <th style="width: 3%" class="text-center headerTablaProducto">Cantidad</th>
                                            <th style="width: 5%; min-width: 80px;" class="text-center headerTablaProducto">Unidad</th>
                                            <th style="width: 5%; min-width: 100px;" class="text-center headerTablaProducto">Costo</th>
                                            <th style="width: 10%; min-width: 90px;" class="text-center headerTablaProducto">Subtotal</th>
                                            <th style="width: 5%" class="text-center headerTablaProducto">Eliminar</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tablaProductosEgreso">
                                        <tr v-for="producto in documento.productos_egreso.items">
                                            <td><input type="text" class="form-control text-center input-sm" v-model="producto.codigo" disabled></td>
                                            <td><input type="text" class="form-control text-center input-sm"  v-model="producto.nombre" readonly></td>
                                            <td><input type="text" class="form-control text-center input-sm" v-model="producto.stock" disabled></td>
                                            <td><input type="number" class="form-control text-center input-sm" @change="producto.setCantidad($event.target.value)" :value="producto.cantidad" step=".0001" min="0" oninput="validity.valid||(value=1);"></td>
                                            <td>
                                                <select v-model="producto.unidad" @change="getCostoProducto(producto)" class="form-control input-sm" disabled>
                                                    <option v-for="unidad in producto.unidades_medida" :value="unidad.Unidad.trim()">
                                                    {{unidad.Unidad}}
                                                    </option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control text-center input-sm" v-model="producto.precio">
                                            </td>
                                            
                                            <td><input type="text" class="form-control text-center input-sm" v-model="producto.getSubtotal()" readonly></td>
                                            <td><button type="button" @click="removeEgresoItem(producto.codigo)" class="btn btn-danger btn-sm btn-block"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" class="text-center" style="vertical-align: middle;"><b>Total</b></td>
                                            <td><input type="text" v-model="documento.getCantidadItems_Egresos()" class="form-control text-center" readonly></td>
                                            <td><input type="text" v-model="documento.getCantidadUnidades_Egresos()" class="form-control text-center" readonly></td>
                                            <td><input type="text" v-model="documento.getTotal_Egresos()" class="form-control text-center" readonly></td>
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
                        <h4 class="panel-title pull-left" style="padding-top: 7.5px; padding-bottom: 7.5px"><i class="fa fa-list" aria-hidden="true"></i> Lista de items a ingresar</h4>
                            <div class="btn-group pull-right">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon">Bodega ingreso</span>
                                    <select id='bodegaIngreso' v-model="documento.productos_ingreso.bodega" class="form-control input-sm" style="background-color: #d9f7d9;">
                                            <?php
                                            foreach ($bodegas as $bodega => $row) {

                                                $codigo = trim($row['CODIGO']);
                                                $texto= $row['NOMBRE']; 
                                                
                                                echo "<option value='$codigo'>$texto</option>";
                                            }
                                            
                                            ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="panel-body">
                            <div class="responsibetable">        
                                <table class="table table-bordered tableExtras">
                                    <thead>
                                        <tr>
                                            <th style="width: 10%;" class="text-center headerTablaProducto">Codigo</th>
                                            <th style="width: 20%; min-width: 200px;" class="text-center headerTablaProducto">Nombre del Articulo</th>
                                            <th style="width: 5%; min-width: 90px;" class="text-center headerTablaProducto">Stock</th>
                                            <th style="width: 3%; min-width: 90px;" class="text-center headerTablaProducto">Cantidad</th>
                                            <th style="width: 5%; min-width: 80px;"  class="text-center headerTablaProducto">Unidad</th>
                                            <th style="width: 5%; min-width: 70px;" class="text-center headerTablaProducto">Costo</th>
                                            <th style="width: 10%; min-width: 70px;" class="text-center headerTablaProducto">Subtotal</th>
                                            <th style="width: 5%" class="text-center headerTablaProducto">Observ.</th>
                                            <th style="width: 5%" class="text-center headerTablaProducto">Eliminar</th>
                                            
                                        </tr>
                                    </thead>
                                    <tbody id="tablaProductosIngreso">
                                        <tr v-for="producto in documento.productos_ingreso.items">
                                            <td><input type="text" class="form-control text-center input-sm" v-model="producto.codigo" disabled></td>
                                            <td><input type="text" class="form-control text-center input-sm"  v-model="producto.nombre" readonly></td>
                                            <td><input type="text" class="form-control text-center input-sm" v-model="producto.stock" disabled></td>
                                            <td><input type="number" class="form-control text-center input-sm" @change="producto.setCantidad($event.target.value)" :value="producto.cantidad" step=".0001" min="0" oninput="validity.valid||(value='0');"></td>
                                            <td>
                                                <select v-model="producto.unidad" @change="getCostoProductoByCostoEgresos(producto)" class="form-control input-sm" disabled>
                                                    <option v-for="unidad in producto.unidades_medida" :value="unidad.Unidad.trim()">
                                                    {{unidad.Unidad}}
                                                    </option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control text-center input-sm" v-model="producto.precio">
                                            </td>
                                            <td><input type="text" class="form-control text-center input-sm" v-model="producto.getSubtotal()" readonly></td>
                                            <td><button type="button" @click="showDescriptionModal(producto)" class="btn btn-primary btn-sm btn-block"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>
                                            <td><button type="button" @click="removeIngresoItem(producto.codigo)" class="btn btn-danger btn-sm btn-block"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
                                           
                                                <div class="modal fade" :id="'modalAddExtraDetail_'+producto.codigo" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog">
                                                    <div class="modal-dialog modal-lg" role="document">
                                                        <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title"> Observacion del Producto: {{producto.nombre}}</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <label for="comment">Fecha de Caducidad:</label>
                                                            <div class="input-group w-100">
                                                                <input type="number" class="w-50 form-control text-center input-sm" @change="producto.getFechaCaducidad()" v-model="producto.diasCaducidad">
                                                                <input type="date" class="w-50 form-control text-center input-sm" format="YYYY-MM-DD" v-model="producto.fechaCaducidad" >
                                                            </div>
                                                            
                                                            <div class="form-group">
                                                                <label for="comment">Observacion:</label>
                                                                <textarea class="form-control" rows="5" maxlength="250" v-model="producto.observacion"></textarea>
                                                            </div>

                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                                        </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" class="text-center" style="vertical-align: middle;"><b>Total</b></td>
                                            <td><input type="text" v-model="documento.getCantidadItems_Ingresos()" class="form-control text-center" readonly></td>
                                            <td><input type="text" v-model="documento.getCantidadUnidades_Ingresos()" class="form-control text-center" readonly></td>
                                            <td><input type="text" v-model="documento.getTotal_Ingresos()" class="form-control text-center" readonly></td>
                                            <td colspan="3">
                                            <input type="number" v-model="documento.getTotal_Ingresos()" class="form-control text-center" readonly></td>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="5"></td>
                                            <td colspan="2" class="text-center" style="vertical-align: middle;"><b>Diferencia <span class="glyphicon glyphicon-arrow-right"></span></b></td>
                                            <td colspan="3">
                                                <input style="background-color: #ffe7e7" type="text" v-model="documento.getDiferencia_IngresosEgresos()" class="form-control text-center" readonly></td>
                                            </td>
                                        </tr>

                                    </tfoot>
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
                            <button type="button" class="btn btn-primary btn-lg" @click="saveDocumento()" id="btnGuardar"><span class="glyphicon glyphicon-floppy-saved" aria-hidden="true"></span> Registrar</button>
                        </div>

                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-danger btn-lg" id="btnCancel"><span class="glyphicon glyphicon-floppy-remove" aria-hidden="true"></span> Cancelar</button>
                        </div>
                
                    </div>
                </div>
            </div>    

            <!-- Modal Info sesion -->
            <?php require_once 'sis_modules/modal_info_session.php'?>

            <!-- Modal Proveedor -->
           
            <!-- Modal Cliente Nuevo -->
            <?php require_once 'sis_modules/modal_cliente_nuevo.php'?>

            <!-- Modal Producto -->
            <?php require_once 'sis_modules/modal_producto.php'?>

            <!-- Modal Producto -->
            <?php require_once 'sis_modules/modal_detalle_promo.php'?>

            <!-- Modal Buscar Documento -->
            <?php require_once 'sis_modules/modalBuscarDocumento_inventario.php'?>

            <!-- Modal Enviar Email Personalizado -->
            <?php require_once 'sis_modules/modal_SendEmail.php'?>

            <!-- Modal agregar fotos y detalles extra -->
            <?php require_once 'sis_modules/modal_addExtraDetailProduct.php'?>
        
        </div>
    </form>

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
    <!-- <script src="https://cloud.tinymce.com/5/tinymce.min.js?apiKey=ubmvgme7f7n7likjbniglty12b9m92um98w9m75mdtnphwqp"></script> -->
    <!-- <script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\tinymce.js"></script> -->
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\datepicker.js"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\xlsx.full.min.js"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets/js/sweetalert.min.js"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\moment.min.js"></script>
    
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\pages\formularioCortes.js?<?php echo date('Ymdhiiss')?>"></script>
