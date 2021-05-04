<div class="modal fade" id="modalBuscarDocumento" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"> Buscar Documento </h4>
        </div>
        <div class="modal-body">
            
            <div class="input-group input-daterange">
                <input type="date" v-model="search_documentos.busqueda.fechaINI" class="form-control">
                <div class="input-group-addon">hasta</div>
                <input type="date" v-model="search_documentos.busqueda.fechaFIN" class="form-control">
            </div>

            <div class="input-group select-group">
                <input type="text" v-model="search_documentos.busqueda.texto" placeholder="Código de documento" class="form-control" value="%"/>
                <select id="tipoBusquedaModalProducto" class="form-control input-group-addon">
                    <option value="">TODOS</option>
                </select>
                <div class="input-group-btn">
                    <button @click="getDocumentos" type="button" class="btn btn-primary" :disabled="search_documentos.isloading"  >
                        <i class="fa" :class="[{'fa-spin fa-refresh': search_documentos.isloading}, {  'fa-search' : !search_documentos.isloading  }]" ></i> Buscar
                    </button>
                </div> 
            </div>

            <div class="panel panel-default"> 
                <div class="panel-heading">Resultados</div> 
                    <div class="responsibetable"> 
                        <table class="table"> 
                            <thead>
                                <tr> 
                                    <th>Codigo</th> 
                                    <th>Empresa</th> 
                                    <th>Fecha</th> 
                                    <th>Número</th>
                                    <th>Opciones</th>
                                </tr>
                            </thead> 
                            
                            <tbody>
                            <tr v-for="documento in search_documentos.results">
                                <td>{{documento.codigo}}</td>
                                <td>{{documento.empresa}}</td>
                                <td>{{documento.fecha}}</td>
                                <td>{{documento.placa}}</td>
                                <td>{{documento.observacion.substring(20,0)+  '...'}}</td>
                                <td>
                                    <button type="button" class="btn btn-primary btn-sm btn-block">
                                        <span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>
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