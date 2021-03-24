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

        <!-- Row datos de proveedor-->
        <div class="row">

            <div class="col-lg-6 col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Egresa Componentes & Ingresa KITs</div>
                    <div class="panel-body">
                        <div class="input-group input-group-sm">
                            <span class="input-group-addon">Número</span>
                            <input type="text" v-model="search_proveedor.text" class="form-control" placeholder="Codigo">
                            <span class="input-group-btn">
                                <button class="btn btn-default" type="button" data-toggle="modal" data-target="#modal_buscardocumento">
                                    <span class="glyphicon glyphicon-search" aria-hidden="true"></span>
                                </button>
                            </span>
                            
                            
                        </div>

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

                        <div class="input-group input-group-sm">
                            <span class="input-group-addon"> # Contable Egreso</span>
                            <input type="text" class="form-control text-center" :value="documento.proveedor.telefono">
                        </div>

                        <div class="input-group input-group-sm">
                            <span class="input-group-addon"> # Contable Egreso</span>
                            <input type="text" class="form-control text-center" :value="documento.proveedor.telefono">
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-md-12">
                <div class="panel panel-default">
                <div class="panel-heading">Información de Bodegas</div>
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


                        <div class="form-group">
                            <textarea class="form-control" rows="2" id="comment" name="comment" maxlength="100" placeholder="Comentario de hasta maximo 100 caracteres..."></textarea>
                        </div>
                        
                    </div>
                </div>
                
            </div>
        </div>


        <!-- Egreso de items-->
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
                                <th style="width: 2%; min-width: 80px;"  class="text-center headerTablaProducto">Unidad</th>
                                <th style="width: 5%; min-width: 100px;" class="text-center headerTablaProducto">Stock</th>
                                <th style="width: 2%; min-width: 80px;"  class="text-center headerTablaProducto">Cantidad</th>
                                <th style="width: 5%; min-width: 120px;" class="text-center headerTablaProducto">Costo</th>
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
                                        <select v-model='nuevo_producto.unidad' @change="getCostoProducto()" class="form-control input-sm">
                                            <option v-for="unidad in unidades_medida" :value="unidad.Unidad.trim()">
                                            {{unidad.Unidad}}
                                            </option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" v-model="nuevo_producto.stock" class="form-control text-center input-sm" readonly>
                                    </td>
                                    <td>
                                        <input type="number" @change="nuevo_producto.setCantidad($event.target.value)" :value="nuevo_producto.cantidad" class="form-control text-center input-sm" min="1" oninput="validity.valid||(value=1);"></td>
                                    </td>
                                    <td>
                                        <input type="number" v-model="nuevo_producto.precio" class="form-control text-center input-sm" min="0" value="0">
                                    </td>
                                    <td>
                                        <input type="text" v-model="nuevo_producto.getSubtotal()" class="form-control text-center input-sm importe_linea" readonly>
                                    </td>
                                </tr>

                                
                                
                            </tbody>
                        </table>
                            <button type="button" @click="addToIngresoList" class="btn btn-success btn-sm"><span class="glyphicon glyphicon-arrow-down"></span> Agregar</button>
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
                    <h4 class="panel-title pull-left" style="padding-top: 7.5px; padding-bottom: 7.5px;"><i class="fa fa-list" aria-hidden="true"></i> Transformación de KITs</h4>
                        <div class="btn-group pull-right">
                            
                        </div>
                    </div>

                    <div class="panel-body">
                        <div class="responsibetable">        
                            <table class="table table-bordered tableExtras">
                                <thead>
                                    <tr>
                                        <th style="width: 10%; min-width: 100px;" class="text-center headerTablaProducto">Codigo</th>
                                        <th style="width: 20%; min-width: 200px;" class="text-center headerTablaProducto">Nombre del Articulo</th>
                                        <th style="width: 3%" class="text-center headerTablaProducto">Cantidad</th>
                                        <th style="width: 5%; min-width: 100px;" class="text-center headerTablaProducto">Precio</th>
                                        <th style="width: 5%; min-width: 90px;" class="text-center headerTablaProducto">Stock</th>
                                        <th style="width: 10%; min-width: 90px;" class="text-center headerTablaProducto">Subtotal</th>
                                        <th style="width: 5%" class="text-center headerTablaProducto">Eliminar</th>
                                    </tr>
                                </thead>
                                <tbody id="tablaProductosEgreso">
                                    <tr v-for="producto in documento.productos_egreso.items">
                                        <td><input type="text" class="form-control text-center input-sm" v-model="producto.codigo" disabled></td>
                                        <td><input type="text" class="form-control text-center input-sm"  v-model="producto.nombre" readonly></td>
                                        <td><input type="number" class="form-control text-center input-sm" @change="producto.setCantidad($event.target.value)" :value="producto.cantidad" min="1" oninput="validity.valid||(value=1);"></td>
                                        <td>
                                            <input type="text" class="form-control text-center input-sm" v-model="producto.precio" readonly>
                                        </td>
                                        <td><input type="text" class="form-control text-center input-sm" v-model="producto.stock" disabled></td>
                                        <td><input type="text" class="form-control text-center input-sm" v-model="producto.getSubtotal()" readonly></td>
                                        <td><button type="button" @click="removeEgresoItem(producto.codigo)" class="btn btn-danger btn-sm btn-block"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Eliminar</button>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4"></td>
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
                    <h4 class="panel-title pull-left" style="padding-top: 7.5px; padding-bottom: 7.5px"><i class="fa fa-list" aria-hidden="true"></i> Detalle del KIT</h4>
                        
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
                                        <th style="width: 5%; min-width: 90px;" class="text-center headerTablaProducto">Stock</th>
                                        <th style="width: 10%; min-width: 70px;" class="text-center headerTablaProducto">Costo</th>
                                       
                                    </tr>
                                </thead>
                                <tbody id="tablaProductosIngreso">
                                    <tr v-for="producto in documento.productos_ingreso.items">
                                        <td><input type="text" class="form-control text-center input-sm" v-model="producto.codigo" disabled></td>
                                        <td><input type="text" class="form-control text-center input-sm"  v-model="producto.nombre" readonly></td>
                                        <td><input type="number" class="form-control text-center input-sm" @change="producto.setCantidad($event.target.value)" :value="producto.cantidad" min="1" oninput="validity.valid||(value='1');"></td>
                                        <td>
                                            <input type="text" class="form-control text-center input-sm" v-model="producto.precio">
                                        </td>
                                        <td><input type="text" class="form-control text-center input-sm" v-model="producto.stock" disabled></td>
                                        <td><input type="text" class="form-control text-center input-sm" v-model="producto.getSubtotal()" readonly></td>
                                        <td><button type="button" @click="removeIngresoItem(producto.codigo)" class="btn btn-danger btn-sm btn-block"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Eliminar</button>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4"></td>
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

        <!-- Modal Producto -->
        <?php require_once 'sis_modules/modal_producto.php'?>

       
     
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
    
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\pages\creacionReceta.js?<?php echo date('Ymdhiiss')?>"></script>
