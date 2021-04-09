<div class="modal fade" id="modalBuscarProducto" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Busqueda de Producto</h4>
        </div>
        <div class="modal-body">
            
            <div class="input-group select-group">
                <input type="text" id="terminoBusquedaModalProducto" placeholder="Codigo o Nombre del producto..." class="form-control"/>
               
                <div class="input-group-btn">
                    <button id="searchProductoModal" type="button" class="btn btn-primary" aria-label="Help">
                        <span class="glyphicon glyphicon-search"></span> Buscar
                    </button>
                </div> 
            </div>

            <div class="panel panel-default"> 
                <div class="panel-heading">Resultados</div> 
                    <div class="responsibetable"> 
                        <table id="tblResultadosBusquedaProductos" class="table"> 
                            <thead>
                                <tr> 
                                    <th>#</th> 
                                    <th>Codigo</th> 
                                    <th>Nombre</th> 
                                    <th>Precio</th>
                                    <th>Stock</th>
                                    <th>Accion</th> 
                                </tr>
                            </thead> 
                            
                            <tbody>
                                <!-- Los resultados de la busqueda se desplegaran aqui-->
                                <div id="loaderProductos">
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