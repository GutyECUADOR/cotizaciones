<div class="modal fade" id="modal_proveedor" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">Busqueda de Proveedor</h4>
        </div>

        <div class="modal-body">
            <div class="input-group select-group">
                <input type="text" v-model="search_proveedor.text" placeholder="Término de búsqueda" class="form-control" value="%" style="width: 75%;"/>
                <select v-model="search_proveedor.campo" class="form-control input-group-addon" style="width: 25%;">
                    <option value="RUC">Cedula / RUC</option>
                    <option value="NOMBRE">Nombre</option>
                    
                </select>
                <div class="input-group-btn">
                    <button @click="getProveedores" type="button" class="btn btn-primary" :disabled="search_proveedor.isloading"  >
                        <i class="fa" :class="[{'fa-spin fa-refresh': search_proveedor.isloading}, {  'fa-search' : !search_proveedor.isloading  }]" ></i> Buscar
                    </button>
                </div> 
            </div>

            <div class="panel panel-default"> 
                <div class="panel-heading">Resultados</div> 
                    <div class="responsibetable"> 
                        <table id="tblResultadosBusquedaClientes" class="table"> 
                            <thead>
                                <tr> 
                                    <th>Codigo</th> 
                                    <th>Cliente/Distribuidor</th> 
                                    <th>RUC</th> 
                                    <th>Seleccionar</th> 
                                </tr>
                            </thead> 
                            
                            <tbody>
                                <tr v-for="cliente in search_proveedor.results">
                                    <td>{{cliente.Codigo}}</td>
                                    <td>{{cliente.Nombre.trim()}}</td>
                                    <td>{{cliente.Ruc}}</td>
                                    <td>
                                        <button type="button" class="btn btn-primary btn-sm btn-block" @click="selectProveedor(cliente.Codigo)">
                                            <i class="fa fa-check" aria-hidden="true"></i>
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