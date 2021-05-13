<?php

use App\Controllers\CotizacionController;
use App\Middleware\RouteMiddleware;

$routeMiddleware = new RouteMiddleware();
$routeMiddleware->checkisLogin();

$cotizacion = new CotizacionController();
$bodegas = $cotizacion->getBodegas();


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
        
            <!-- row Egresa Componentes & Ingresa KITs -->
            
            <div class="row" style="margin-top:20px">

                <div class="col-lg-6 col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">Egresa Componentes & Ingresa KITs</div>
                        <div class="panel-body">
                            <div class="input-group input-group-sm">
                                <span class="input-group-addon">Número</span>
                                <input type="text" class="form-control">
                                <span class="input-group-btn">
                                    <button class="btn btn-default" type="button" data-toggle="modal" data-target="#modal_buscardocumento">
                                        <span class="glyphicon glyphicon-search" aria-hidden="true"></span>
                                    </button>
                                </span>
                                
                                
                            </div>

                            <div class="input-group input-group-sm">
                                <span class="input-group-addon">Bodega egreso</span>
                                <select id='bodegaEgreso' v-model="documento.bodega_egreso" class="form-control input-sm" style="background-color: #ffe7e7;">
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
                                <select id='bodegaIngreso' v-model="documento.bodega_ingreso" class="form-control input-sm" style="background-color: #d9f7d9;">
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
                                <input type="text" class="form-control text-center">
                            </div>

                            <div class="input-group input-group-sm">
                                <span class="input-group-addon"> # Contable Egreso</span>
                                <input type="text" class="form-control text-center">
                            </div>

                        </div>
                    </div>
                </div>

                <div class="col-lg-6 col-md-12">
                    <div class="panel panel-default">
                    <div class="panel-heading">Detalle</div>
                        <div class="panel-body">
                            <div class="form-group">
                                <textarea class="form-control" rows="2" id="comment" name="comment" maxlength="100" placeholder="Comentario de hasta maximo 100 caracteres..."></textarea>
                            </div>
                            <div class="form-group">
                            <label>Se puede producir: </label>
                                <div class="well text-center wellextra" >
                                    <span id="welltotal_produccion">{{ documento.kit.getMaximaProduccion() }} {{documento.kit.unidad}}</span>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                    
                </div>
            </div>


            <!--  Row busqueda del KIT -->
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                    <!-- Default panel contents -->
                
                    <div class="panel-heading clearfix">
                        <h4 class="panel-title pull-left" style="padding-top: 7.5px;"><i class="fa fa-address-book" aria-hidden="true"></i></i> Búsqueda de KIT</h4>
                        
                    </div>
                                        
                    <div class="panel-body">
                        <div class="responsibetable">     
                            <table id="tablaAgregaNuevo" class="table table-bordered tableExtras">
                                <thead>
                                <tr>
                                    <th style="width: 5%; min-width: 120px;" class="text-center headerTablaProducto">Codigo</th>
                                    <th style="width: 10%; min-width: 200px;" class="text-center headerTablaProducto">Nombre del Articulo</th>
                                    <th style="width: 5%; min-width: 90px;" class="text-center headerTablaProducto">Stock</th>
                                    <th style="width: 3%; min-width: 90px;" class="text-center headerTablaProducto">Cantidad</th>
                                    <th style="width: 5%; min-width: 80px;"  class="text-center headerTablaProducto">Unidad</th>
                                    <th style="width: 5%; min-width: 120px;" class="text-center headerTablaProducto">Costo</th>
                                    <th style="width: 5%; min-width: 120px;" class="text-center headerTablaProducto">Subtotal</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="input-group">
                                            <input type="text" @change="setKit(search_producto.busqueda.texto)" v-model="search_producto.busqueda.texto" class="form-control text-center input-sm" placeholder="Codigo de Producto">
                                            <span class="input-group-btn">
                                                <button id="btnSeachProductos" class="btn btn-default input-sm" type="button" data-toggle="modal" data-target="#modalBuscarProducto">
                                                    <span class="glyphicon glyphicon-search" aria-hidden="true"></span>
                                                </button>
                                            </span>
                                            </div><!-- /input-group -->
                                        </td>
                                        <td>
                                            <input type="text" v-model="documento.kit.nombre" class="form-control text-center input-sm" readonly>
                                        </td>
                                        <td><input type="text" class="form-control text-center input-sm" v-model="documento.kit.stock" disabled></td>
                                        <td><input type="number" class="form-control text-center input-sm" @change="documento.kit.setCantidad($event.target.value)" :value="documento.kit.cantidad" step=".0001" min="0" oninput="validity.valid||(value='0');"></td>
                                        <td>
                                            <select v-model='documento.kit.unidad' @change="documento.kit.getPrecio()" class="form-control input-sm" disabled>
                                                <option v-for="unidad in documento.kit.unidades_medida" :value="unidad.Unidad.trim()">
                                                {{unidad.Unidad}}
                                                </option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" v-model="documento.kit.getPrecio()" class="form-control text-center input-sm" min="0" value="0" readonly>
                                        </td>
                                        <td>
                                            <input type="text" v-model="documento.kit.getSubtotal()" class="form-control text-center input-sm importe_linea" readonly>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>

          
            <!-- Row composicion del KIT -->
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <!-- Default panel contents -->
                    
                        <div class="panel-heading clearfix">
                        <h4 class="panel-title pull-left" style="padding-top: 7.5px; padding-bottom: 7.5px;"><i class="fa fa-list" aria-hidden="true"></i> Detalle del KIT (Composición)</h4>
                            <div class="btn-group pull-right">
                                <button type="button" @click="setKit(documento.kit.codigo)" class="btn btn-success btn-sm"><span class="glyphicon glyphicon-refresh"></span> Recargar Composicion</button>
                                <button type="button" @click="saveReceta" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar cambios en Receta</button>
                                <button type="button"  data-toggle="modal" data-target="#modal_producto_composicion" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-search"></span> Agregar Item a Composicion</button>
                            </div>
                        </div>

                        <div class="panel-body">
                            <div class="responsibetable">        
                                <table class="table table-bordered tableExtras">
                                    <thead>
                                        <tr>
                                            <th style="width: 5%; min-width: 100px;" class="text-center headerTablaProducto">Codigo</th>
                                            <th style="width: 10%; min-width: 170px;" class="text-center headerTablaProducto">Nombre del Articulo</th>
                                            <th style="width: 5%; min-width: 90px;" class="text-center headerTablaProducto">Stock</th>
                                            <th style="width: 3%; min-width: 90px;" class="text-center headerTablaProducto">Cantidad</th>
                                            <th style="width: 2%; min-width: 80px;" class="text-center headerTablaProducto">Unidad</th>
                                            <th style="width: 5%; min-width: 110px;" class="text-center headerTablaProducto">Costo</th>
                                            <th style="width: 10%; min-width: 90px;" class="text-center headerTablaProducto">Costo Total</th>
                                            <th style="width: 10%; min-width: 90px;" class="text-center headerTablaProducto">Costo Teórico</th>
                                            <th style="width: 10%; min-width: 90px;" class="text-center headerTablaProducto">% Merma</th>
                                            <th style="width: 10%; min-width: 90px;" class="text-center headerTablaProducto">Valor Merma</th>
                                            <th style="width: 10%; min-width: 90px;" class="text-center headerTablaProducto">Costo Unitario Teórico</th>
                                            <th style="width: 10%; min-width: 90px;" class="text-center headerTablaProducto">Costo Total Teórico</th>
                                            <th style="width: 5%" class="text-center headerTablaProducto">Eliminar</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tablaProductos">
                                        <tr v-for="producto in documento.kit.composicion">
                                            <td><input type="text" class="form-control text-center input-sm" v-model="producto.codigo" disabled></td>
                                            <td><input type="text" class="form-control text-center input-sm"  v-model="producto.nombre" readonly></td>
                                            <td><input type="text" class="form-control text-center input-sm" v-model="producto.stock" readonly></td>
                                            <td><input type="number" class="form-control text-center input-sm" @change="producto.setCantidad($event.target.value)" :value="producto.cantidad" step=".0001" min="0" oninput="validity.valid||(value=1);" readonly></td>
                                            
                                            <td>
                                                <select v-model="producto.unidad" @change="getCostoProducto(producto)" class="form-control input-sm" disabled>
                                                    <option v-for="unidad in producto.unidades_medida" :value="unidad.Unidad.trim()" readonly>
                                                    {{unidad.Unidad}}
                                                    </option>
                                                </select>
                                            </td>
                                            
                                            <td>
                                                <input type="number" class="form-control text-center input-sm" v-model="producto.precio" readonly>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control text-center input-sm" v-model="producto.getSubtotal()" readonly></td>
                                            </td>
                                            <td><input type="number" class="form-control text-center input-sm" @change="producto.setCostoTeorico($event.target.value)" :value="producto.costoTeorico" step=".0001" min="0" oninput="validity.valid||(value=1);"></td>
                                            <td><input type="number" class="form-control text-center input-sm" @change="producto.setPorcentMerma($event.target.value)" :value="producto.porcentajeMerma" step=".01" min="0" oninput="validity.valid||(value=1);"></td>
                                            <td>
                                                <input type="text" class="form-control text-center input-sm" v-model="producto.getValorMerma()" readonly></td>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control text-center input-sm" v-model="producto.getCostoTeoricoUnitario()" readonly></td>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control text-center input-sm" v-model="producto.getCostoTeoricoTotal()" readonly></td>
                                            </td>
                                            <td>
                                                <button type="button" @click="removeEgresoItem(producto.codigo)" class="btn btn-danger btn-sm btn-block"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Eliminar</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <!-- FILA DE TOTALES-->
                                        <tr>
                                            <td colspan="3" class="text-center" style="vertical-align: middle;"><b>Total</b></td>
                                            <td><input type="text" v-model="documento.kit.getCantidadItems_Composicion()"  class="form-control text-center" readonly></td>
                                            <td><input type="text" v-model="documento.kit.getCantidadUnidades_Composicion()" class="form-control text-center" readonly></td>
                                            <td><input type="text" v-model="documento.kit.getPrecio()" class="form-control text-center" readonly></td>
                                            <td><input type="text" v-model="documento.kit.getSubtotal()" style="background-color: #d9f7d9;" class="form-control text-center" readonly></td>
                                            <td><input type="text" v-model="documento.kit.getTotalCostoTeorico_Composicion()" class="form-control text-center" readonly></td>
                                            <td><input type="text" v-model="documento.kit.getTotalPorcentajeMerma_Composicion()" class="form-control text-center" readonly></td>
                                            <td><input type="text" v-model="documento.kit.getTotalValorMerma_Composicion()" class="form-control text-center" readonly></td>
                                            <td><input type="text" v-model="documento.kit.getTotalCostoUnitario_Composicion()" class="form-control text-center" readonly></td>
                                            <td colspan="2">
                                                <input type="text" v-model="documento.kit.getTotalCostoTeoricoTotal_Composicion()" style="background-color: #d9f7d9;" class="form-control text-center" readonly>
                                            </td>
                                        </tr>

                                    </tfoot>
                                </table>
                            </div>
                            <button type="button"  data-toggle="modal" data-target="#modalPreparacion" class="btn btn-primary btn-block btn-sm"><span class="glyphicon glyphicon-pencil"></span> Preparación</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row extraButton">
                <div class="col-md-12">
                    <div class="btn-group btn-group-justified" role="group" aria-label="...">
                    

                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-primary btn-lg" @click="saveDocumento" id="btnGuardar"><span class="glyphicon glyphicon-floppy-saved" aria-hidden="true"></span> Registrar {{ title }}</button>
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

             <!-- Modal Producto -->
             <?php require_once 'sis_modules/modal_producto_composicion.php'?>

             <div class="modal fade" id="modalPreparacion" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title"> Preparación de la receta </h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="comment">Detalle:</label>
                            <textarea class="form-control" rows="5" @keyup="setPreparacionToProducts()" v-model="documento.kit.descripcion"></textarea>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    </div>
                    </div>
                </div>
            </div>

       
     
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
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\datepicker.js"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\xlsx.full.min.js"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets/js/sweetalert.min.js"></script>
    
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\pages\costoTeorico.js?<?php echo date('Ymdhiiss')?>"></script>
