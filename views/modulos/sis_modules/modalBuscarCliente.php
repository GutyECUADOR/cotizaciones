<!-- Modal Cliente -->
<div class="modal fade" id="modalBuscarCliente" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">Busqueda de Clientes</h4>
        </div>

        <div class="modal-body">
            <div class="input-group select-group">
            <input type="text" @keyup.enter="getClientes" v-model="search_producto.busqueda.texto" placeholder="RUC o Nombre del cliente" class="form-control"/>
                <select id="tipoBusquedaModalCliente" class="form-control input-group-addon">
                    <option value="NOMBRE">Nombre</option>
                    <option value="RUC">Cedula / RUC</option>
                </select>
                <div class="input-group-btn">
                    <button @click="getClientes" type="button" class="btn btn-primary" :disabled="search_producto.isloading"  >
                        <i class="fa" :class="[{'fa-spin fa-refresh': search_producto.isloading}, {  'fa-search' : !search_producto.isloading  }]" ></i> Buscar
                    </button>
                </div> 
            </div>

            <div class="panel panel-default"> 
                <div class="panel-heading">Resultados</div> 
                    <table id="tblResultadosBusquedaClientes" class="table"> 
                        <thead>
                            <tr> 
                                <th style="font-size: 12px;">#</th> 
                                <th style="font-size: 12px;">RUC</th> 
                                <th style="font-size: 12px;">Cliente</th> 
                                <th style="font-size: 12px;">Seleccionar</th> 
                            </tr>
                        </thead> 
                        
                        <tbody>
                            <!-- Los resultados de la busqueda se desplegaran aqui-->
                            
                        </tbody>
                    </table>
                </div>

        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        </div>
        </div>
    </div>
</div>