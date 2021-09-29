<!-- Modal Cliente -->
<div class="modal fade" id="modalBuscarCliente" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Búsqueda de Clientes</h4>
        </div>

        <div class="modal-body">
            <div class="input-group select-group">
            <input type="text" @keyup.enter="getClientes" v-model="search_cliente.busqueda.texto" placeholder="RUC o Nombre del cliente" class="form-control"/>
                <select id="tipoBusquedaModalCliente" class="form-control input-group-addon">
                    <option value="NO">Nombre</option>
                    <option value="RU">Cedula / RUC</option>
                    <option value="CO">Código</option>
                </select>
                <div class="input-group-btn">
                    <button @click="getClientes" type="button" class="btn btn-primary" :disabled="search_cliente.isloading"  >
                        <i class="fa" :class="[{'fa-spin fa-refresh': search_cliente.isloading}, {  'fa-search' : !search_cliente.isloading  }]" ></i> Buscar
                    </button>
                </div> 
            </div>

            <div class="panel panel-default"> 
                <div class="panel-heading">Resultados: <strong>{{search_cliente.results.length}}</strong></div> 
                <div class="responsibetable">  
                    <table class="table"> 
                        <thead>
                            <tr> 
                                <th>Codigo</th>
                                <th>Nombre</th>
                                <th>RUC</th>
                                <th style="min-width: 80px;">Acciones.</th>
                            </tr>
                        </thead> 
                        
                        <tbody>
                            <tr v-for="cliente in search_cliente.results">
                                <td>{{cliente.Codigo}}</td>
                                <td>{{cliente.Nombre}}</td>
                                <td>{{cliente.Ruc}}</td>
                                <td>
                                    <button type="button" @click="setRucCliente(cliente.Ruc)" class="btn btn-primary btn-sm btn-block">
                                        <i class="fa fa-arrow-down" aria-hidden="true"></i>
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