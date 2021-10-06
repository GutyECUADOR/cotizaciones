<?php

use App\Controllers\CotizacionesController;
use App\Middleware\RouteMiddleware;

$routeMiddleware = new RouteMiddleware();
$routeMiddleware->checkisLogin(); 

$cotizacion = new CotizacionesController();
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

    <form id="app" v-on:submit.prevent="saveDocumento" >
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
                        <span id="welltotal">$ {{ documento.total }}</span>
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
                                <span class="input-group-addon">Cliente RUC</span>
                                <input type="text" class="form-control" @keyup="getCliente" v-model="search_cliente.busqueda.texto">
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
                               
                            </div>

                            <div class="input-group input-group-sm">
                                <span class="input-group-addon" id="sizing-addon3">Nombre</span>
                                <input type="text" class="form-control" placeholder="Nombre Cliente" v-model="documento.cliente.nombre" readonly>
                            </div>

                            <div class="input-group input-group-sm">
                                <span class="input-group-addon" id="sizing-addon3">Razon Social</span>
                                <input type="text" class="form-control" placeholder="Razon Social" v-model="documento.cliente.empresa" readonly>
                            </div>

                            <div class="input-group input-group-sm">
                                <span class="input-group-addon" id="sizing-addon3">Correo</span>
                                <input type="mail" class="form-control" placeholder="Correo" v-model="documento.cliente.email" readonly>
                            </div>

                            <div class="input-group input-group-sm">
                                <span class="input-group-addon"><span class="glyphicon glyphicon-earphone" aria-hidden="true"></span> Telf.</span>
                                <input type="text" class="form-control text-center" placeholder="Telefono" v-model="documento.cliente.telefono" readonly>
                                <span class="input-group-addon">Dias Pago</span>
                                <input type="text" class="form-control" placeholder="DiasPago" v-model="documento.cliente.diasPago" readonly>
                            </div>

                            <div class="input-group input-group-sm">
                                <span class="input-group-addon" id="sizing-addon3">Vendedor</span>
                                <input type="text" class="form-control" placeholder="Vendedor" :value="documento.cliente.codVendedor + '-' + documento.cliente.vendedor" readonly>
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
                                <select v-model="documento.formaPago" @change="validaFormaPago" class="form-control input-sm">
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
                                <select v-model="documento.condicionPago" class="form-control input-sm" :disabled="validaFormaPago()">
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
                    
                </div>
                
                <div class="col-lg-4 col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">Detalle extras</div>
                        <div class="panel-body">
                            <div class="form-group">
                                <textarea class="form-control" rows="2" v-model="documento.comentario" name="comment" maxlength="100" placeholder="Comentario de hasta maximo 100 caracteres..."></textarea>
                            </div>
                            <div class="input-group input-group-sm">
                                <span class="input-group-addon">Tipo Precio Cliente</span>
                                <input type="text" :value="documento.cliente.tipoPrecio" class="form-control" disabled>
                            </div>
            

                        </div>
                    
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                    <!-- Default panel contents -->
                
                    <div class="panel-heading clearfix">
                        <h4 class="panel-title pull-left" style="padding-top: 7.5px;"><i class="fa fa-address-book" aria-hidden="true"></i></i> Búsqueda de nuevo item</h4>
                        <div class="btn-group pull-right">
                            <button type="button" @click="showDetailStock" class="btn btn-success btn-sm"><i class="fa fa-cubes"></i> Verificar Stock</button>
                        </div>
                    </div>
                                        
                    <div class="panel-body">
                        <div class="responsibetable">     
                            <table id="tablaAgregaNuevo" class="table table-bordered tableExtras">
                                <thead>
                                <tr>
                                    <th style="width: 5%; min-width: 135px;" class="text-center headerTablaProducto">Codigo</th>
                                    <th style="width: 10%; min-width: 200px;" class="text-center headerTablaProducto">Nombre del Articulo</th>
                                    <th style="width: 2%; min-width: 80px;"  class="text-center headerTablaProducto">Unidad</th>
                                    <th style="width: 5%; min-width: 80px;" class="text-center headerTablaProducto">Stock</th>
                                    <th style="width: 2%; min-width: 90px;"  class="text-center headerTablaProducto">Cantidad</th>
                                    <th style="width: 5%; min-width: 90px;" class="text-center headerTablaProducto">Costo / {{ nuevoProducto.unidad }}</th>
                                    <th style="width: 2%; min-width: 90px;"  class="text-center headerTablaProducto">Vendedor</th>
                                    <th style="width: 5%; min-width: 110px;" class="text-center headerTablaProducto">Cod. Promocion</th>
                                    <th style="width: 5%; min-width: 100px;" class="text-center headerTablaProducto">Fecha Validez</th>
                                    <th style="width: 5%; min-width: 80px;" class="text-center headerTablaProducto">% Desc</th>
                                    <th style="width: 5%; min-width: 80px;" class="text-center headerTablaProducto">IVA</th>
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
                                            <input type="text" v-model="nuevoProducto.nombre" class="form-control text-center input-sm" readonly>
                                        </td>
                                        <td>
                                            <input type="text" v-model="nuevoProducto.unidad" class="form-control text-center input-sm" readonly>
                                        </td>
                                        <td>
                                            <input type="text" v-model="nuevoProducto.stock" class="form-control text-center input-sm" readonly>
                                        </td>
                                        <td>
                                            <input type="number" @change="nuevoProducto.setCantidad($event.target.value);" :value="nuevoProducto.cantidad" class="form-control text-center input-sm" step=".0001" min="0" oninput="validity.valid||(value=1);"></td>
                                        </td>
                                        <td>
                                            <input type="number" v-model="nuevoProducto.precio" class="form-control text-center input-sm" min="0" value="0" step=".0001" readonly>
                                        </td>
                                        <td>
                                            <input type="number" v-model="nuevoProducto.vendedor" class="form-control text-center input-sm" min="0" value="0">
                                        </td>
                                        <td>
                                            <div class="input-group">
                                            <input type="text" id="inputNuevoProductoCodProm" class="form-control text-center input-sm" readonly>
                                            <span class="input-group-btn">
                                                <button @click="showDetailPromo" class="btn btn-default input-sm" type="button">
                                                    <span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>
                                                </button>
                                            </span>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="text" id="inputNuevoProductoValidezProm" class="form-control text-center input-sm" readonly>
                                        </td>
                                        <td>
                                            <input type="text" v-model="nuevoProducto.descuento" class="form-control text-center input-sm" readonly>
                                        </td>
                                        <td>
                                            <input type="text" v-model="nuevoProducto.getIVA()" class="form-control text-center input-sm" readonly>
                                        </td>
                                        <td>
                                            <input type="text" v-model="nuevoProducto.getSubtotal()" class="form-control text-center input-sm importe_linea" readonly>
                                        </td>
                                    </tr>

                                    
                                    
                                </tbody>
                            </table>
                            <button type="button" class="btn btn-primary btn-sm" @click="addToListProductos"><span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span> Agregar item</button>
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
                                            <th style="width: 10%; min-width: 80px;" class="text-center headerTablaProducto">Codigo</th>
                                            <th style="width: 20%; min-width: 200px;" class="text-center headerTablaProducto">Nombre del Articulo</th>
                                            <th style="width: 5%; min-width: 80px;" class="text-center headerTablaProducto">Unidad</th>
                                            <th style="width: 5%; min-width: 90px;" class="text-center headerTablaProducto">Stock</th>
                                            <th style="width: 3%" class="text-center headerTablaProducto">Cantidad</th>
                                            <th style="width: 5%; min-width: 100px;" class="text-center headerTablaProducto">Costo</th>
                                            <th style="width: 5%; min-width: 100px;" class="text-center headerTablaProducto">Vendedor</th>
                                            <th style="width: 10%; min-width: 90px;" class="text-center headerTablaProducto">Subtotal</th>
                                            <th style="width: 5%" class="text-center headerTablaProducto">Eliminar</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tablaProductos">
                                        <tr v-for="producto in documento.productos">
                                            <td><input type="text" class="form-control text-center input-sm" v-model="producto.codigo" disabled></td>
                                            <td><input type="text" class="form-control text-center input-sm"  v-model="producto.nombre" readonly></td>
                                            <td><input type="text" class="form-control text-center input-sm" v-model="producto.unidad" disabled></td>
                                            <td><input type="text" class="form-control text-center input-sm" v-model="producto.stock" disabled></td>
                                            <td><input type="number" class="form-control text-center input-sm" @change="producto.setCantidad($event.target.value)" :value="producto.cantidad" step=".0001" min="0" oninput="validity.valid||(value=1);"></td>
                                            <td>
                                                <input type="text" class="form-control text-center input-sm" v-model="producto.precio" readonly>
                                            </td>
                                            <td><input type="number" class="form-control text-center input-sm" v-model="producto.vendedor"></td>
                                            <td><input type="text" class="form-control text-center input-sm" v-model="producto.getSubtotal()" readonly></td>
                                            <td><button type="button" @click="removeItemFromList(producto.codigo)" class="btn btn-danger btn-sm btn-block"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="6"></td>
                                            <td class="text-center" style="vertical-align: middle;"><b>Subtotal</b></td>
                                            <td colspan="3">
                                            <input type="text" :value="documento.getSubTotalProductos()" class="form-control text-center" readonly></td>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="6"></td>
                                            <td class="text-center" style="vertical-align: middle;"><b>IVA</b></td>
                                            <td colspan="3">
                                            <input type="text" :value="documento.getIVAProductos()" class="form-control text-center" readonly></td>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="6"></td>
                                            <td class="text-center" style="vertical-align: middle;"><b>Total</b></td>
                                            <td colspan="3">
                                            <input type="text" :value="documento.getTotalProductos()" class="form-control text-center" readonly></td>
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
                            <button type="submit" class="btn btn-primary btn-lg"><span class="glyphicon glyphicon-floppy-saved" aria-hidden="true"></span> Registrar</button>
                        </div>

                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-danger btn-lg" @click="cancelSubmit()"><span class="glyphicon glyphicon-floppy-remove" aria-hidden="true"></span> Cancelar</button>
                        </div>
                
                    </div>
                </div>
            </div>    

            <!-- Modal Info sesion -->
            <?php require_once 'sis_modules/modal_info_session.php'?>

            <!-- Modal Buscar Documento -->
            <?php require_once 'sis_modules/modalBuscarDocumento.php'?>

            <!-- Modal Busqueda Cliente -->
            <?php require_once 'sis_modules/modalBuscarCliente.php'?>

            <!-- Modal Cliente Nuevo -->
            <?php require_once 'sis_modules/modal_cliente_nuevo.php'?>

            <!-- Modal Producto -->
            <?php require_once 'sis_modules/modalBuscarProducto.php'?>

            <!-- Modal Stock -->
            <?php require_once 'sis_modules/modalBuscarStockProductos.php'?>

            <!-- Modal Detalle Promo -->
            <?php require_once 'sis_modules/modal_detalle_promo.php'?>

           
        
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
