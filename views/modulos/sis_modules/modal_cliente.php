<div class="modal fade" id="modalBuscarCliente" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">Busqueda de Clientes</h4>
        </div>

        <div class="modal-body">
            <div class="input-group select-group">
                <input type="text" id="terminoBusquedaModalCliente" placeholder="Termino de busqueda..." class="form-control" value="%" style="width: 75%;"/>
                <select id="tipoBusquedaModalCliente" class="form-control input-group-addon" style="width: 25%;">
                    <option value="RUC">Cedula / RUC</option>
                    <option value="NOMBRE">Nombre</option>
                    
                </select>
                <div class="input-group-btn">
                    <button id="searchClienteModal" type="button" class="btn btn-primary" aria-label="Help">
                        <span class="glyphicon glyphicon-search"></span> Buscar
                    </button>
                </div> 
            </div>

            <div class="panel panel-default"> 
                <div class="panel-heading">Resultados</div> 
                    <div class="responsibetable"> 
                        <table id="tblResultadosBusquedaClientes" class="table"> 
                            <thead>
                                <tr> 
                                    <th>#</th> 
                                    <th>RUC</th> 
                                    <th>Cliente</th> 
                                    <th>Seleccionar</th> 
                                </tr>
                            </thead> 
                            
                            <tbody>
                                <!-- Los resultados de la busqueda se desplegaran aqui-->
                                <div id="loaderClientes">
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