<div class="modal fade" id="modal_generaguia" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"> Generacion de Guia - Tramaco </h4>
            </div>
            <div class="modal-body">
                
                <!-- Row datos remitente-->
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading"><strong>Datos del Remitente - KAO</strong></div>
                            <div class="panel-body">
                                <div class="input-group input-group-sm">
                                
                                    <span class="input-group-addon" style="min-width: 217px;">Ciudad de Origen</span>
                                    <select class="form-control input-sm">
                                        <option value="211">PICHINCHA - QUITO</option>
                                    </select>
                                
                                    <span class="input-group-addon" style="min-width: 217px;">Contrato</span>
                                    <select class="form-control input-sm" id="contrato">
                                        <option v-for="contrato in contratos" :value="contrato.id">
                                        {{ contrato.numero }}
                                        </option>
                                    </select>
                                </div>

                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon" style="min-width: 217px;">Localidad Origen</span>
                                    <select class="form-control input-sm" id="localidades">
                                        <option v-for="localidad in localidades" :value="localidad.id">
                                        {{ localidad.id }} - ({{localidad.nombre}})
                                        </option>
                                    </select>

                                    <span class="input-group-addon" style="min-width: 217px;">Centro de Costo</span>
                                    <select  class="form-control input-sm">
                                        <option> --- </option>
                                    </select>
                                </div>

                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon" style="min-width: 217px;">Sucursal</span>
                                    <input type="text" class="form-control" value=" --- " readonly>

                                    <span class="input-group-addon" style="min-width: 217px;">Punto de Venta</span>
                                    <input type="text" class="form-control" value=" --- " readonly>
                                </div>

                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon" style="min-width: 217px;">Quien envia</span>
                                    <input type="text" v-model="guia.remitente.nombres" class="form-control">

                                    <span class="input-group-addon" style="min-width: 217px;"><span class="glyphicon glyphicon-earphone" aria-hidden="true"></span> Telefono</span>
                                    <input type="text" v-model="guia.remitente.telefono" class="form-control">
                                </div>

                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon" style="min-width: 217px;">Calle Primaria</span>
                                    <input type="text" v-model="guia.remitente.callePrimaria" class="form-control">

                                    <span class="input-group-addon" style="min-width: 217px;">Calle Secundaria</span>
                                    <input type="text" v-model="guia.remitente.calleSecundaria" class="form-control">
                                </div>

                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon" style="min-width: 217px;">Numero</span>
                                    <input type="text" v-model="guia.remitente.numero" class="form-control">

                                    <span class="input-group-addon" style="min-width: 217px;">Referencia</span>
                                    <input type="text" v-model="guia.remitente.referencia" class="form-control">
                                </div>

                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon" style="min-width: 217px;">Provincia</span>
                                    <input type="text" class="form-control" :value="guia.remitente.provincia" readonly>

                                    <span class="input-group-addon" style="min-width: 217px;">Canton</span>
                                    <input type="text" class="form-control" :value="guia.remitente.canton" readonly>
                                </div>

                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon" style="min-width: 217px;">Parroquia</span>
                                    <input type="text" class="form-control" :value="guia.remitente.parroquia" readonly>

                                    <span class="input-group-addon" style="min-width: 217px;">Codigo Postal</span>
                                    <input type="text" v-model="guia.remitente.codigoPostal" class="form-control">
                                </div>

                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon" style="min-width: 217px;">Descripcion real del contenido</span>
                                    <input type="text" class="form-control" v-model="guia.lstCargaDestino[0].carga.descripcion">
                                </div>

                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon" style="min-width: 217px;">
                                        Retornar Adjuntos <input type="checkbox"> 
                                    </span>
                                    <span class="input-group-addon" style="min-width: 217px;" >Codigo Adjuntos</span>
                                    <input type="text" class="form-control">
                                    <span class="input-group-addon" >Referencia Terceros</span>
                                    <input type="text" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Row datos parroquia envio-->
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading"><strong>Parroquia del destinatario (Código Tramaco)</strong></div>
                            <div class="panel-body">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon" style="min-width: 217px;">Provincia</span>
                                    <select v-model="localidad_envio.provincia" @change="getCantonesTramaco" class="form-control" :disabled="codigoParroquia">
                                        <option value=''>Seleccione por favor</option>
                                        <option v-for="provincia in search_codigoEnvio.provincias" :value="provincia.PROVINCIA">
                                            {{provincia.PROVINCIA}}
                                        </option>
                                    </select>
                                </div>

                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon" style="min-width: 217px;">Cantón</span>
                                    <select v-model="localidad_envio.canton" @change="getParroquiasTramaco" class="form-control" :disabled="codigoParroquia">
                                        <option value=''>Seleccione por favor</option>
                                        <option v-for="canton in search_codigoEnvio.cantones" :value="canton.CANTON">
                                            {{canton.CANTON}}
                                        </option>
                                    </select>
                                </div>

                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon" style="min-width: 217px;">Parroquia</span>
                                    <select v-model="localidad_envio.parroquia" @change="getCodigoEnvioTramaco" class="form-control" :disabled="codigoParroquia">
                                        <option value=''>Seleccione por favor</option>
                                        <option v-for="parroquia in search_codigoEnvio.parroquias" :value="parroquia.PARROQUIA">
                                            {{parroquia.PARROQUIA}}
                                        </option>
                                    </select>
                                </div>

                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon" style="min-width: 217px;">Codigo de destino</span>
                                    <input type="text" v-model="codigoParroquia" class="form-control" readonly>
                                </div>
                            
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Row datos destinatario-->
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading"><strong>Datos del Destinatario - Cliente</strong></div>
                            <div class="panel-body">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon" style="min-width: 217px;">Cedula / RUC</span>
                                    <input type="text" v-model="guia.lstCargaDestino[0].destinatario.ciRuc" class="form-control">
                                </div>

                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon" style="min-width: 217px;">Nombres</span>
                                    <input type="text" v-model="guia.lstCargaDestino[0].destinatario.nombres" class="form-control">
                                </div>

                                <div class="input-group input-group-sm" >
                                    <span class="input-group-addon" style="min-width: 217px;">Apellidos</span>
                                    <input type="text" v-model="guia.lstCargaDestino[0].destinatario.apellidos" class="form-control">
                                </div>

                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon" style="min-width: 217px;">Ciudad</span>
                                    <select v-model="guia.lstCargaDestino[0].destinatario.ciudad" class="form-control">
                                        <option v-for="cuidad in cuidades" :value="cuidad.NOMBRE">
                                            {{cuidad.NOMBRE}}
                                        </option>
                                    </select>

                                    <span class="input-group-addon">Parroquia</span>
                                    <select v-model="guia.lstCargaDestino[0].destinatario.parroquia" class="form-control">
                                        <option v-for="parroquia in parroquias" :value="parroquia.NOMBRE">
                                            {{parroquia.NOMBRE}}
                                        </option>
                                    </select>
                                </div>

                                <div class="input-group input-group-sm" style="min-width: 217px;">
                                    <span class="input-group-addon" style="min-width: 217px;">Calle Primaria</span>
                                    <input type="text" v-model="guia.lstCargaDestino[0].destinatario.callePrimaria" class="form-control">
                                </div>

                                <div class="input-group input-group-sm" style="min-width: 217px;">
                                    <span class="input-group-addon" style="min-width: 217px;">Calle Secundaria</span>
                                    <input type="text" v-model="guia.lstCargaDestino[0].destinatario.calleSecundaria" class="form-control">
                                </div>

                                <div class="input-group input-group-sm" style="min-width: 217px;">
                                    <span class="input-group-addon" style="min-width: 217px;">Numero</span>
                                    <input type="text" v-model="guia.lstCargaDestino[0].destinatario.numero" class="form-control">
                                </div>

                                <div class="input-group input-group-sm" style="min-width: 217px;">
                                    <span class="input-group-addon" style="min-width: 217px;">Referencia</span>
                                    <input type="text" v-model="guia.lstCargaDestino[0].destinatario.referencia" class="form-control">
                                </div>

                                <div class="input-group input-group-sm" style="min-width: 217px;">
                                    <span class="input-group-addon" style="min-width: 217px;">Telefono</span>
                                    <input type="text" v-model="guia.lstCargaDestino[0].destinatario.telefono" class="form-control">
                                </div>

                                <div class="input-group input-group-sm" style="min-width: 217px;">
                                    <span class="input-group-addon" style="min-width: 217px;">Codigo Postal</span>
                                    <input type="text" v-model="guia.lstCargaDestino[0].destinatario.codigoPostal" class="form-control">
                                </div>

                                <div class="input-group input-group-sm" style="min-width: 217px;">
                                    <span class="input-group-addon" style="min-width: 217px;">Observaciones</span>
                                    <input type="text" class="form-control">
                                </div>
                            
                            </div>
                        </div>
                    </div>
                </div>
        
              

                <!-- Row Informacion de Carga-->
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading"><strong>Información de Carga</strong></div>
                            <div class="panel-body">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon" style="min-width: 217px;">Producto</span>
                                    <select class="form-control input-sm" v-model="guia.lstCargaDestino[0].carga.producto">
                                        <option v-for="contrato in productosContrato" :value="contrato.id">
                                        {{ contrato.nombre }}
                                        </option>
                                    </select>
                                </div>

                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon" style="min-width: 217px;">Peso(Kgs)</span>
                                    <input type="text" v-model="guia.lstCargaDestino[0].carga.peso" class="form-control">
                                
                                    <span class="input-group-addon" style="min-width: 217px;">Bultos</span>
                                    <input type="text" v-model="guia.lstCargaDestino[0].carga.bultos" class="form-control">
                                </div>

                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon" style="min-width: 217px;">Cajas</span>
                                    <input type="text" class="form-control" v-model="guia.lstCargaDestino[0].carga.cajas">

                                    <span class="input-group-addon">Valor Asegurado</span>
                                    <input type="text" class="form-control" v-model="guia.lstCargaDestino[0].carga.valorAsegurado">
                                </div>

                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon" style="min-width: 217px;">Largo (cm)</span>
                                    <input type="text" class="form-control" v-model="guia.lstCargaDestino[0].carga.largo">

                                    <span class="input-group-addon">Alto (cm)</span>
                                    <input type="text" class="form-control" v-model="guia.lstCargaDestino[0].carga.alto">

                                    <span class="input-group-addon">Ancho (cm)</span>
                                    <input type="text" class="form-control" v-model="guia.lstCargaDestino[0].carga.ancho">
                                </div>

                               

                            
                            </div>
                        </div>
                    </div>
                </div>
        

                <div class="modal-footer">
                    <button type="button" class="btn btn-success" @click="generarGuia">Generar Guia</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>    
    </div>
</div>