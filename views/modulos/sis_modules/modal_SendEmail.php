<div class="modal fade" id="modalSendEmail" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title"> Envio de Correo </h4>
        </div>
        <div class="modal-body">
            
        <form>
            <div class="input-group">
                <span class="input-group-addon" id="basic-addon1">De:</span>
                <input type="text" class="form-control" placeholder="de@email.com" aria-describedby="basic-addon1" value="<?php echo $_SESSION["usuarioNOMBRE".APP_UNIQUE_KEY] ?>" disabled>
            </div>
            <div class="input-group">
                <span class="input-group-addon" id="basic-addon1">Destinatario: </span>
                <input type="text" class="form-control" placeholder="destinataroi@email.com" id="emailDestinatario" aria-describedby="basic-addon1">
            </div>

            <div class="input-group">
                <span class="input-group-addon" id="basic-addon1">CC: </span>
                <input type="text" class="form-control" placeholder="cc@email.com" aria-describedby="basic-addon1" value="<?php echo DEFAULT_EMAIL?>" disabled>
            </div>
            <div class="input-group">
                <span class="input-group-addon" id="basic-addon1">Documento: </span>
                <input type="text" class="form-control" placeholder="#992018PRO000XXXXX" id='emailIDDocument' aria-describedby="basic-addon1" disabled>
            </div>

            </br>

            <div class="form-group">
                <label for="comment">Mensaje por Defecto:</label>
                <textarea class="form-control" rows="2" readonly ><?php echo BODY_EMAIL_TEXT?></textarea>
            </div>

            <div class="form-group">
                <label for="comment">Mensaje:</label>
                <textarea class="form-control tiny" rows="5" id="mailContent"></textarea>
            </div>

        </form>
            
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" id="btnSendCustomEmail">Enviar</button>
            <button type="button" class="btn btn-default" data-dismiss="modal" onclick="location.reload();">Cerrar</button>
        </div>
        </div>
    </div>
</div>