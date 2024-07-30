<div class="modal fade" id="modalColleccionesProducto" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <ul class="list-group">
                    <li class="list-group-item" v-for="coleccion in productoActivo.coleccionShopify">{{ coleccion?.title }}</li>
                </ul>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-block" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</div>