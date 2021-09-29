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
                <input type="text" id="terminoBusquedaModalDocument" placeholder="Termino de busqueda..." class="form-control" value="%" style="width: 75%;"/>
                <select v-model="search_documentos.busqueda.tipoDOC" class="form-control input-group-addon" style="width: 25%;">
                    <?php
                    foreach ($tiposDOC as $grupo => $row) {

                        $codigo = trim($row['CODIGO']);
                        $texto= $row['NOMBRE']; 
                        
                        echo "<option value='$codigo'>$texto - $codigo</option>";
                    }
                    
                    ?>
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
                                <tr v-for="documento in search_documentos.results">
                                    <td>{{documento.TIPO}}</td>
                                    <td>{{documento.FECHA}}</td>
                                    <td>{{documento.CLIENTE}}</td>
                                    <td>{{documento.BODEGA}}</td>
                                    <td>{{documento.total}}</td>
                                    <td>{{documento.id}}</td>
                                    <td>
                                        <button type="button" @click="generaPDF(documento.id)" class="btn btn-primary btn-sm btn-block">
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