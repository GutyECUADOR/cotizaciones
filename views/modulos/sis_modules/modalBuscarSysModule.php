<!-- Modal Cliente -->
<div class="modal fade" id="modalBuscarSysModule" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Búsqueda de Módulos del Sistema</h4>
        </div>

        <div class="modal-body">
            <div class="input-group select-group">
            <input type="text" @keyup.enter="getModulos" v-model="search_modulos.busqueda.texto" placeholder="Nombre del Módulo o Formulario" class="form-control"/>
                <select id="tipoBusquedaModalCliente" class="form-control input-group-addon">
                    <option value="Nombre">Nombre</option>
                </select>
                <div class="input-group-btn">
                    <button @click="getModulos" type="button" class="btn btn-primary" :disabled="search_modulos.isloading"  >
                        <i class="fa" :class="[{'fa-spin fa-refresh': search_modulos.isloading}, {  'fa-search' : !search_modulos.isloading  }]" ></i> Buscar
                    </button>
                </div> 
            </div>

            <div class="panel panel-default"> 
                <div class="panel-heading">Resultados: <strong>{{search_modulos.results.length}}</strong></div> 
                <div class="responsibetable">  
                    <table class="table"> 
                        <thead>
                            <tr> 
                                <th>Codigo</th>
                                <th>Nombre</th>
                                <th>Módulo</th>
                                <th style="min-width: 80px;">Acciones.</th>
                            </tr>
                        </thead> 
                        
                        <tbody>
                            <tr v-for="modulo in search_modulos.results">
                                <td>{{modulo.id}}</td>
                                <td>
                                    <strong>{{modulo.nombre}}: </strong>
                                    <small>{{modulo.descripcion}}</small>
                                </td>
                                <td>{{modulo.modulo}}</td>
                                <td>
                                    <button type="button" @click="addNewPermiso(modulo)" class="btn btn-primary btn-sm btn-block">
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