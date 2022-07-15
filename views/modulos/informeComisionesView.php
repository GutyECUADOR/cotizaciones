<?php

use App\Controllers\CotizacionesController;
use App\Middleware\RouteMiddleware;
use App\Controllers\RouteController;

$routeController = new RouteController();
$menus = $routeController->getMenus();

$routeMiddleware = new RouteMiddleware();
$routeMiddleware->checkisLogin(); 

$cotizacion = new CotizacionesController();
$vendedores = $cotizacion->getVendedores();

?>
    <!-- CSS Propios -->
    <link rel="stylesheet" href="<?php echo ROOT_PATH; ?>assets\css\cotizacionStyles.css">
    
    <div id="wrapper">
        <!-- Sidebar -->
        <?php include './views/modulos/sis_modules/main_sidebar.php'?>

        <!-- Page Content -->
        <div id="page-content-wrapper">
        <?php include './views/modulos/sis_modules/header_main_admin.php'?>

            <div class="container-fluid">
            <form id="app" v-on:submit.prevent="getInforme" >
               
                <ol class="breadcrumb">
                    <li><a href="?action=inicio">Inicio</a></li>
                    <li><a href="#">Ventas</a></li>
                    <li class="active">{{ titulo }}</li>
                </ol>
            
            
                <div class="container-fluid card">
                    <!-- Hidden Inputs-->
                    <input id="hiddenBodegaDefault" type="hidden" value="<?php echo $_SESSION["bodegaDefault".APP_UNIQUE_KEY]?>">


                    <!-- Row Modal Buscar Documento-->
                    <div class="row">
                        

                        <div class="col col-lg-9">
                            <div class="col col-lg-4">
                                <div class="form-group">
                                    <div class="input-group">
                                            <span class="input-group-addon">Tipo</span>
                                            <select v-model="documento.tipoDOC" class="form-control input-sm">
                                                <?php
                                                    foreach ($tiposDOC as $tipodoc => $row) {

                                                        $codigo = trim($row['CODIGO']);
                                                        $texto= $row['NOMBRE']; 
                                                        
                                                        echo "<option value='$codigo'>$texto</option>";
                                                    }
                                                
                                                ?>
                                            </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col col-lg-4">
                                <div class="form-group">
                                    <div class="input-group">
                                            <span class="input-group-addon">Número</span>
                                            <input type="number" class="form-control text-center input-sm" v-model="documento.numero">       
                                            <span class="input-group-btn">
                                                <button class="btn btn-default btn-sm" type="button"><span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span></button>
                                            </span>
                                    </div>
                                </div>
                            </div>

                            <div class="col col-lg-4">
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-addon">Fecha</span>
                                        <input type="date" class="form-control text-center input-sm" v-model="documento.fecha">
                                    </div>
                                </div>
                            </div>

                            <div class="col col-lg-6">
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-addon">Fecha de Corte</span>
                                        <input type="date" class="form-control text-center input-sm" v-model="documento.fechaCorte">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col col-lg-6">
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-addon">Vendedor</span>
                                        <select class="form-control input-sm" v-model="documento.vendedor">
                                            <option value=''>Seleccione por favor</option>
                                            <?php
                                                $bodega_default = $_SESSION["bodegaDefault".APP_UNIQUE_KEY];
                                                foreach ($vendedores as $vendedor => $row) {

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
                                </div>
                            </div>

                        </div>

                        <div class="col col-lg-3">
                            <div class="col ">
                                <div class="form-group">
                                    <div class="well text-center wellextra" >
                                        <span id="welltotal">$ {{ documento.totalComision }}</span>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn btn-block" :disabled="search_informe.isloading"  >
                                        <i class="fa" :class="[{'fa-spin fa-refresh': search_informe.isloading}, {  'fa-search' : !search_informe.isloading  }]" ></i> Buscar
                                    </button>
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
                                <h4 class="panel-title pull-left" style="padding-top: 7.5px;"><i class="fa fa-list" aria-hidden="true"></i> Lista de Transacciones</h4>
                                <div class="btn-group pull-right">
                                </div>
                                </div>

                                <div class="panel-body">
                                    <div class="table-responsive">        
                                        <table class="table table-bordered tableExtras">
                                            <thead>
                                                <tr>
                                                    <th style="width: 10%; min-width: 80px;" class="text-center headerTablaProducto">Cliente</th>
                                                    <th style="width: 20%; min-width: 300px;" class="text-center headerTablaProducto">Nombre Cliente</th>
                                                    <th style="width: 5%; min-width: 120px;" class="text-center headerTablaProducto">Documento</th>
                                                    <th style="width: 5%; min-width: 100px;" class="text-center headerTablaProducto">F. Emisión</th>
                                                    <th style="width: 5%; min-width: 100px;" class="text-center headerTablaProducto">F. Vencimiento</th>
                                                    <th style="width: 10%; min-width: 220px;" class="text-center headerTablaProducto">Grupo</th>
                                                    <th style="width: 10%; min-width: 90px;" class="text-center headerTablaProducto">Marca</th>
                                                    <th style="width: 10%; min-width: 90px;" class="text-center headerTablaProducto">Nombre Articulo</th>
                                                    <th style="width: 10%; min-width: 70px;" class="text-center headerTablaProducto">Cod Tabla Comi</th>
                                                    <th style="width: 10%; min-width: 90px;" class="text-center headerTablaProducto">Monto Total</th>
                                                    <th style="width: 10%; min-width: 90px;" class="text-center headerTablaProducto">Descuento</th>
                                                    <th style="width: 10%; min-width: 90px;" class="text-center headerTablaProducto">Neto</th>
                                                    <th style="width: 10%; min-width: 90px;" class="text-center headerTablaProducto">Por Comision Vita</th>
                                                    <th style="width: 10%; min-width: 90px;" class="text-center headerTablaProducto">Por Comision Desc</th>
                                                    <th style="width: 10%; min-width: 100px;" class="text-center headerTablaProducto">F. Deposito</th>
                                                    <th style="width: 10%; min-width: 90px;" class="text-center headerTablaProducto">Dias Credito</th>
                                                    <th style="width: 10%; min-width: 90px;" class="text-center headerTablaProducto">Por Comision Cobro</th>
                                                    <th style="width: 10%; min-width: 90px;" class="text-center headerTablaProducto">Valor Comision Cobro</th>
                                                    <th style="width: 10%; min-width: 90px;" class="text-center headerTablaProducto">Total Comision</th>
                                                    <th style="width: 10%; min-width: 160px;" class="text-center headerTablaProducto">NumRel</th>
                                                    
                                                </tr>
                                            </thead>
                                            <tbody id="tablaProductos">
                                                <tr v-for="row in documento.movimientos">
                                                    <td><input type="text" class="form-control text-center input-sm" v-model="row.CLIENTE" disabled></td>
                                                    <td><input type="text" class="form-control text-center input-sm" v-model="row.Nombre_cliente" disabled></td>
                                                    <td><input type="text" class="form-control text-center input-sm" v-model="row.Documento" disabled></td>
                                                    <td><input type="text" class="form-control text-center input-sm" v-model="row.Fecha_emision" disabled></td>
                                                    <td><input type="text" class="form-control text-center input-sm" v-model="row.Fecha_Vencimiento" disabled></td>
                                                    <td><input type="text" class="form-control text-center input-sm" v-model="row.GRUPO" disabled></td>
                                                    <td><input type="text" class="form-control text-center input-sm" v-model="row.Marca" disabled></td>
                                                    <td><input type="text" class="form-control text-center input-sm" v-model="row.Nombre_articulo" disabled></td>
                                                    <td><input type="text" class="form-control text-center input-sm" v-model="row.CODTABLACOMI" disabled></td>
                                                    <td><input type="text" class="form-control text-center input-sm" v-model="row.Monto_total" disabled></td>
                                                    <td><input type="text" class="form-control text-center input-sm" v-model="row.Descuento" disabled></td>
                                                    <td><input type="text" class="form-control text-center input-sm" v-model="row.NETO" disabled></td>
                                                    <td><input type="text" class="form-control text-center input-sm" v-model="row.Por_comision_Vta" disabled></td>
                                                    <td><input type="text" class="form-control text-center input-sm" v-model="row.VALOR_COMISION_DESC" disabled></td>
                                                    <td><input type="text" class="form-control text-center input-sm" v-model="row.fecha_deposito" disabled></td>
                                                    <td><input type="text" class="form-control text-center input-sm" v-model="row.DiasCredito" disabled></td>
                                                    <td><input type="text" class="form-control text-center input-sm" v-model="row.POR_COMISION_COBRO" disabled></td>
                                                    <td><input type="text" class="form-control text-center input-sm" v-model="row.VALOR_COMISION_COBRO" disabled></td>
                                                    <td><input type="text" class="form-control text-center input-sm" v-model="row.Total_comision" disabled></td>
                                                    <td><input type="text" class="form-control text-center input-sm" v-model="row.Numrel" disabled></td>
                                                 
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="16"></td>
                                                    <td colspan="2" colspan="3"class="text-center" style="vertical-align: middle;"><b>Total Notas Credito</b></td>
                                                    <td colspan="3">
                                                    <input type="text" class="form-control text-center" readonly></td>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="16"></td>
                                                    <td colspan="2" class="text-center" style="vertical-align: middle;"><b>Notas debito</b></td>
                                                    <td colspan="6">
                                                    <input type="text"  class="form-control text-center" readonly></td>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="16"></td>
                                                    <td colspan="2" class="text-center" style="vertical-align: middle;"><b>Total comision</b></td>
                                                    <td colspan="3">
                                                    <input type="text" v-model="documento.getTotalComision()" class="form-control text-center" readonly></td>
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
                                    <button type="button" class="btn btn-danger btn-lg" @click="cancelSubmit()"><span class="glyphicon glyphicon-refresh" aria-hidden="true"></span> Cancelar</button>
                                </div>
                        
                            </div>
                        </div>
                    </div>    

                    <!-- Modal Info sesion -->
                    <?php require_once 'sis_modules/modal_info_session.php'?>

                  

                
                
                </div>
            </form>    

            </div>    
            <!-- container-fluid -->
        </div>
        <!-- /#page-content-wrapper -->

        
        
    </div>

    <!-- USO JQUERY, y Bootstrap CDN-->
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\libs\vue.js"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\libs\jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\libs\bootstrap.min.js"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\libs\popper.min.js"></script>
    
    <!-- Extra Libs-->
    <script src="https://cloud.tinymce.com/5/tinymce.min.js?apiKey=ubmvgme7f7n7likjbniglty12b9m92um98w9m75mdtnphwqp"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\libs\tinymce.js"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\libs\pnotify.custom.min.js"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\libs\sweetalert.min.js"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\libs\moment.min.js"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\pages\inicio.js?<?php echo date('Ymdhiiss')?>"></script>
    
    <!-- JS Propio-->
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>assets\js\pages\informeComisiones.js"></script>
