<!-- Modal Cliente -->
<div class="modal fade modal_left20" id="modalCrearProducto" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">Nuevo Producto/Item</h4>
        </div>

        <div class="modal-body">
            <div class="form-group">
                <input type="text" class="form-control" id="modal_itemCodigo" placeholder="Codigo del producto"  maxlength="10"/>
            </div>
            <div class="form-group">
                <select class="form-control" id="modal_itemCategoria">
                    <option value="COMC-016">COMC-016 (Producto)</option>
                    <option value="SERC-027">SERC-027 (Servicio)</option>
                </select>
           </div>
           <div class="form-group">
                <input type="text" class="form-control" id="modal_itemDescripcion" placeholder="Descripcion del producto"  maxlength="50"/>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" id="btn_modalGuardarProducto" class="btn btn-primary">Guardar Producto</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        </div>
        </div>
    </div>
</div>