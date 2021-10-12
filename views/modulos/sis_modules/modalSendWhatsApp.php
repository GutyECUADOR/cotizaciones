<div class="modal fade" id="modalSendWhatsApp" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title"> Envio de WhatsApp <i class="fa fa-whatsapp" aria-hidden="true"></i> </h4>
        </div>
        <div class="modal-body">
            
        <form>
            <div class="input-group">
                <span class="input-group-addon" id="basic-addon1">Teléfono: </span>
                <input type="tel" pattern="[+]{1}[0-9]{11,14}" class="form-control" placeholder="+593 999887766" v-model="whatsApp.destinatario" aria-describedby="basic-addon1">
            </div>
            <div class="input-group">
                <span class="input-group-addon" id="basic-addon1">Documento: </span>
                <input type="text" class="form-control" placeholder="#992018PRO000XXXXX" v-model="whatsApp.idDocumento" disabled>
            </div>

            </br>


            <div class="form-group">
                <label for="comment">Mensaje:</label>
                <textarea class="form-control" rows="5" v-model="whatsApp.mensaje"></textarea>
            </div>

        </form>
            
        </div>
        <div class="modal-footer">
            <button @click="openWhatsAppUI(whatsApp.idDocumento)" type="button" class="btn btn-primary" :disabled="email.isloading"  >
                <i class="fa" :class="[{'fa-spin fa-refresh': email.isloading}, {  'fa-whatsapp' : !email.isloading  }]" ></i> Enviar
            </button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        </div>
        </div>
    </div>
</div>