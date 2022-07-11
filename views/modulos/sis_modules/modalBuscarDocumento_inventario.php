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
                <select class="form-control input-group-addon">
                    <option value="">TODOS</option>
                </select>
                <div class="input-group-btn">
                    <button @click="getDocumentos" type="button" class="btn btn-primary" :disabled="search_documentos.isloading"  >
                        <i class="fa" :class="[{'fa-spin fa-refresh': search_documentos.isloading}, {  'fa-search' : !search_documentos.isloading  }]" ></i> Buscar
                    </button>
                </div> 
            </div>

            <div class="panel panel-default"> 
                <div class="panel-heading">Resultados: <b>{{ search_documentos.results.length}}</b> documentos</div> 
                    <div class="responsibetable" style="padding-bottom: 20px;"> 
                        <table class="table"> 
                            <thead>
                                <tr> 
                                    <th>ID</th> 
                                    <th>Tipo</th> 
                                    <th>Numero</th> 
                                    <th>Fecha</th> 
                                    <th>Bodega</th>
                                    <th>Total</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead> 
                            
                            <tbody>
                            <tr v-for="documento in search_documentos.results">
                                <td>{{documento.ID}}</td>
                                <td>{{documento.TIPO}}</td>
                                <td>{{documento.NUMERO}}</td>
                                <td>{{documento.FECHA}}</td>
                                <td>{{documento.BODEGA}}</td>
                                <td>{{documento.total}}</td>
                                
                                <td>
                                    <button type="button" @click="generaPDF(documento.ID)" class="btn btn-primary btn-sm btn-block">
                                        <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
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