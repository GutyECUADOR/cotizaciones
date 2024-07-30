<div class="modal fade" id="modalBuscarVales" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"> Buscar vales por pérdida </h4>
        </div>
        <div class="modal-body">
            <div class="input-group input-group-sm input-daterange">
                <input type="date" v-model="search_documentos.busqueda.fechaINI" class="form-control">
                <div class="input-group-addon">hasta</div>
                <input type="date" v-model="search_documentos.busqueda.fechaFIN" class="form-control">
            </div>

            <div class="input-group input-group-sm select-group">
                <input type="text" id="terminoBusquedaModalDocument" placeholder="Codigo de Vale..." class="form-control" value="" style="width: 75%;"/>
                <select v-model="search_documentos.busqueda.tipoDOC" class="form-control input-group-addon" style="width: 25%; text-align: left;">
                    <option value="" disabled>Seleccione el tipo de documento</option>
                    <option v-for="tipoDOC in tiposDOC" :value="tipoDOC.CODIGO.trim()">
                    {{ tipoDOC.CODIGO }} - {{tipoDOC.NOMBRE}}
                    </option>
                    
                </select>
                <div class="input-group-btn">
                    <button @click="getDocumentos" type="button" class="btn btn-primary" :disabled="search_documentos.isloading"  >
                        <i class="fa" :class="[{'fa-spin fa-refresh': search_documentos.isloading}, {  'fa-search' : !search_documentos.isloading  }]" ></i> Buscar
                    </button>
                </div> 
            </div>

            <div class="panel panel-default"> 
                <div class="panel-heading">Resultados</div> 
                    <div class="responsibetable" style="padding-bottom: 65px;">  
                        <table class="table"> 
                            <thead>
                                <tr> 
                                    <th>Codigo de Vale</th>
                                    <th>Solicitante</th>
                                    <th>Empresa</th>
                                    <th>Bodega</th>
                                    <th>Fecha Pagos</th>
                                    <th>Total</th>
                                    <th>Aprobación Supervisor Winfenix</th>
                                    <th>Aprobación Administración Intranet</th>
                                    <th style="min-width: 80px;">Acciones.</th>
                                </tr>
                            </thead> 
                            
                            <tbody>
                                <tr v-for="documento in search_documentos.results">
                                    <td>{{documento.cod_valep}}</td>
                                    <td>{{documento.ci_solicitante}}</td>
                                    <td>{{documento.empresa}}</td>
                                    <td>{{documento.BODEGA}}</td>
                                    <td>{{documento.fechaPagos}}</td>
                                    <td>{{documento.total}}</td>
                                    <td v-bind:style ="documento.aprobadoSupervisor == 1 ? 'color: green' : ''">{{ documento.aprobadoSupervisor | checkStatusVale}}</td>
                                    <td v-bind:style ="documento.estado == 1 ? 'color: green' : ''">{{ documento.estado | checkStatusVale}}</td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-primary btn-sm btn-block dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <span class="glyphicon glyphicon-cog" aria-hidden="true"></span> Opciones <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu pull-right">
                                                <li><a href="#" @click="generaPDF(documento)"> <span class="glyphicon glyphicon-save-file" aria-hidden="true"></span> Generar PDF</a></li>
                                               
                                            </ul>
                                        </div>
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