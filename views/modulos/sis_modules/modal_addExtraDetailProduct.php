<div class="modal fade" id="modalAddExtraDetail" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title"> Informacion extra del producto </h4>
        </div>
        <div class="modal-body">
            
        <form method="post" id="fileinfo" name="fileinfo">
            <div class="input-group">
                <span class="input-group-addon" id="basic-addon1">Imagenes:</span>
                <input type="file" class="form-control" name="file" id="file" accept=".jpg,.png">
            </div>
            
            <div class="form-group">
                <label for="comment">Detalle:</label>
                <textarea class="form-control tiny" rows="5" id="extraDetailContent"></textarea>
            </div>

        </form>
            
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        </div>
        </div>
    </div>
</div>
