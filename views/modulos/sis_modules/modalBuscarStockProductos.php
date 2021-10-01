<div class="modal fade" id="modalBuscarStockProductos" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Consulta de Stock Bodegas</h4>
            <span>Resultados para: {{ search_stock.busqueda.texto }}</h5>
        </div>
        <div class="modal-body">
            
            <div class="input-group select-group">
                <input type="text" v-model="search_stock.busqueda.texto" placeholder="Código del Producto" class="form-control"/>
                <select class="form-control input-group-addon" v-model="search_stock.busqueda.bodega">
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
                <div class="input-group-btn">
                    <button @click="getStock" type="button" class="btn btn-primary" :disabled="search_stock.isloading"  >
                        <i class="fa" :class="[{'fa-spin fa-refresh': search_stock.isloading}, {  'fa-search' : !search_stock.isloading  }]" ></i> Buscar
                    </button>
                </div> 
            </div>

            <div class="panel panel-default"> 
                <div class="panel-heading text-center"><strong>Stock Rollos</strong></div> 
                <div class="responsibetable"> 
                    <table id="tblResultadosBusquedaProductos" class="table"> 
                        <thead>
                            <tr> 
                                <th>Codigo</th> 
                                <th>Nombre</th> 
                                <th>Stock</th>
                                
                            </tr>
                        </thead> 
                        
                        <tbody>
                        <tr v-for="row in search_stock.results.stock">
                            <td>{{row.CODIGO}}</td>
                            <td>{{row.NOMBRE}}</td>
                            <td>{{row.STOCK}}</td>
                            
                        </tr>
                        </tbody>
                    </table>
                </div>
                    
            </div>

            <div class="panel panel-default"> 
                <div class="panel-heading text-center"><strong>Stock Componentes</strong></div> 
                <div class="responsibetable"> 
                    <table id="tblResultadosBusquedaProductos" class="table"> 
                        <thead>
                            <tr> 
                                <th>Codigo</th> 
                                <th>Nombre</th> 
                                <th>Bodega</th>
                                <th>Stock</th>
                                
                            </tr>
                        </thead> 
                        
                        <tbody>
                        <tr v-for="row in search_stock.results.stockComponentes">
                            <td>{{row.COD_ARTICULO}}</td>
                            <td>{{row.NOM_ARTICULO}}</td>
                            <td>{{row.COD_BODEGA}}</td>
                            <td>{{row.STOCK}}</td>
                            
                        </tr>
                        </tbody>
                    </table>
                </div>
                    
            </div>

            <div class="panel panel-default"> 
                <div class="panel-heading text-center"><strong>Stock Retazos</strong></div> 
                <div class="responsibetable"> 
                    <table id="tblResultadosBusquedaProductos" class="table"> 
                        <thead>
                            <tr> 
                                <th>Bodega</th> 
                                <th>Largo</th> 
                                <th>Ancho</th>
                                <th>Precio</th>
                                
                            </tr>
                        </thead> 
                        
                        <tbody>
                        <tr v-for="row in search_stock.results.stockRetazos">
                            <td>{{row.BODEGA}}</td>
                            <td>{{row.LARGO}}</td>
                            <td>{{row.ANCHO}}</td>
                            <td>{{row.PRECIO}}</td>
                            
                            
                        </tr>
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