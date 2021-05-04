<div class="modal fade" id="modalBuscarProducto" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Búsqueda de Producto</h4>
        </div>
        <div class="modal-body">
            
            <div class="input-group select-group">
                <input type="text" @keyup.enter="getProductos" v-model="search_producto.busqueda.texto" placeholder="Codigo o Nombre del producto..." class="form-control"/>
               
                <div class="input-group-btn">
                    <button @click="getProductos" type="button" class="btn btn-primary" :disabled="search_producto.isloading"  >
                        <i class="fa" :class="[{'fa-spin fa-refresh': search_producto.isloading}, {  'fa-search' : !search_producto.isloading  }]" ></i> Buscar
                    </button>
                </div> 
            </div>

            <div class="panel panel-default"> 
                <div class="panel-heading">Resultados</div> 
                    <div class="responsibetable"> 
                        <table id="tblResultadosBusquedaProductos" class="table"> 
                            <thead>
                                <tr> 
                                    <th>Codigo</th> 
                                    <th>Nombre</th> 
                                    <th>Precio</th>
                                    <th>Stock</th>
                                    <th>Accion</th> 
                                </tr>
                            </thead> 
                            
                            <tbody>
                            <tr v-for="producto in search_producto.results">
                                <td>{{producto.Codigo}}</td>
                                <td>{{producto.Nombre.trim()}}</td>
                                <td>{{parseFloat(producto.PreaA.trim()).toFixed(2)}}</td>
                                <td>{{parseFloat(producto.Stock.trim())}}</td>
                                <td>
                                    <button type="button" class="btn btn-primary btn-sm btn-block" @click="selectProduct(producto.Codigo)">
                                        <span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span>
                                    </button>
                                </td>
                                
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