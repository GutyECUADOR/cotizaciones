<div class="modal fade" id="modalBuscarDocumento" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"> Buscar Documento </h4>
        </div>
        <div class="modal-body">
            
            <div class="input-group input-daterange">
                <input type="text" id="fechaINIDoc" class="form-control" value="<?php echo date('Y-m-01');?>">
                <div class="input-group-addon">hasta</div>
                <input type="text" id="fechaFINDoc" class="form-control" value="<?php echo date('Y-m-d');?>">
            </div>

            <div class="input-group select-group">
                <input type="text" id="terminoBusquedaModalDocument" placeholder="Termino de busqueda..." class="form-control" value="%" style="width: 75%;"/>
                <select id="tipoBusquedaModalProducto" class="form-control input-group-addon" style="width: 25%;">
                    <?php
                    foreach ($tiposDOC as $grupo => $row) {

                        $codigo = trim($row['CODIGO']);
                        $texto= $row['NOMBRE']; 
                        
                        echo "<option value='$codigo'>$texto - $codigo</option>";
                    }
                    
                    ?>
                </select>
                <div class="input-group-btn">
                    <button id="searchDocumentModal" type="button" class="btn btn-primary" aria-label="Help">
                        <span class="glyphicon glyphicon-search"></span> Buscar
                    </button>
                </div> 
            </div>

            <div class="panel panel-default"> 
                <div class="panel-heading">Resultados</div> 
                    <div class="">  
                        <table id="tblResultadosBusquedaDocumentos" class="table"> 
                            <thead>
                                <tr> 
                                    <th>#</th> 
                                    <th>Tipo</th>
                                    <th>Fecha</th>
                                    <th>Cliente</th>
                                    <th>Bodega</th>
                                    <th>Total</th>
                                    <th>ID Document.</th>
                                    <th style="min-width: 80px;">Acciones.</th>
                                </tr>
                            </thead> 
                            
                            <tbody>
                                <!-- Los resultados de la busqueda se desplegaran aqui-->
                                <div id="loaderDocumentos">
                                    <div class="loader" id="loader-4">
                                    <span></span>
                                    <span></span>
                                    <span></span>        
                                </div>
                            </tbody>
                        </table>

                    </div>
                  
                </div>

            </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        </div>
        </div>
    </div>
</div>