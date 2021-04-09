<div class="modal fade" id="modalBuscarDestino" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">{{ titulo }}</h4>
        </div>

        <div class="modal-body">

            <div class="alert alert-info" role="alert">
                El peso total indicado para el envio es de: <strong><span id='infopesocalculo' > 0 </span></strong> Kgs
            </div>
            
            <div class="input-group">
                <span class="input-group-addon" style="min-width: 217px;">Provincia</span>
                <select id="envioProvincia" v-model="localidad.provincia" class="form-control">
                    <option value=''>Seleccione por favor</option>
                </select>
            </div>

            <div class="input-group">
                <span class="input-group-addon" style="min-width: 217px;">Cantón</span>
                <select id="envioCanton" v-model="localidad.canton" class="form-control">
                    <option value=''>Seleccione por favor</option>
                
                </select>
            </div>

            <div class="input-group">
                <span class="input-group-addon" style="min-width: 217px;">Parroquia</span>
                <select id="envioParroquia" v-model="localidad.parroquia" class="form-control">
                    <option value=''>Seleccione por favor</option>
                    
                </select>
            </div>

            <div class="input-group">
                <span class="input-group-addon" style="min-width: 217px;">Codigo de destino</span>
                <input type="text" id="codigoEnvio_detalle" v-model="localidad.codigoDestino" class="form-control" readonly>
            </div>

            <div class="input-group">
                <span class="input-group-addon" style="min-width: 217px;">Calle Primaria</span>
                <input type="text" id="envio_calleprimaria"  v-model="localidad.callePrimaria" maxlength="100" class="form-control" placeholder="Calle Primaria" >
            </div>

            <div class="input-group">
                <span class="input-group-addon" style="min-width: 217px;">Calle Secundaria</span>
                <input type="text" id="envio_callesecundaria" v-model="localidad.calleSecundaria" maxlength="100" class="form-control" placeholder="Calle Secundaria" >
            </div>

            <div class="input-group">
                <span class="input-group-addon" style="min-width: 217px;">Número</span>
                <input type="text" id="envio_numero" v-model="localidad.numero" maxlength="10" class="form-control" placeholder="Número" >
            </div>

            <div class="input-group">
                <span class="input-group-addon" style="min-width: 217px;">Referencia</span>
                <input type="text" id="envio_referencia" v-model="localidad.referencia" maxlength="100" class="form-control" placeholder="Referencia" >
            </div>

            <div class="input-group">
                <span class="input-group-addon" style="min-width: 217px;">Teléfono</span>
                <input type="text" id="envio_telefono" v-model="localidad.telefono" maxlength="13" class="form-control" placeholder="Teléfono" >
            </div>

            <div class="input-group">
                <span class="input-group-addon" style="min-width: 217px;">Codigo Postal</span>
                <input type="text" id="envio_codigoPostal" v-model="localidad.codigoPostal" maxlength="7" class="form-control" placeholder="Codigo Postal (Opcional)" >
            </div>

            <div class="input-group">
                <span class="input-group-addon" style="min-width: 217px;">Observaciones</span>
                <textarea class="form-control" rows="2" id="envio_observacion" name="envio_observacion" v-model="localidad.observaciones" maxlength="300" placeholder="Observaciones de envio (Opcional)"></textarea>
            </div>


            </form>
        </div>
        <div class="modal-footer">
            <button type="button" id="btnAgregarEnvioToList" class="btn btn-success">Agregar costo de envio </button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        </div>
        </div>
    </div>
</div>