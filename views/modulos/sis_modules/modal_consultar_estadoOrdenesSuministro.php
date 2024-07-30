<!-- Modal -->
<div class="modal fade" id="modal_consultar_estadoOrdenesSuministro" tabindex="-1" role="dialog" >
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Lista de Solicitudes - Ordenes de Compra</h4>
      </div>
      <div class="modal-body">
        <div class="table-responsive">        
          <table class="table table-bordered tableExtras">
              <thead>
                  <tr>
                      <th style="width: 90%;" class="text-center headerTablaProducto">Solicitud</th>
                      <th style="width: 10%;" class="text-center headerTablaProducto">Estado</th>
                      
                  </tr>
              </thead>
              <tbody>
                  <tr v-for="producto in solicitudesAprobacion">
                      <td>{{ producto.id }} - {{ producto.title }} solicitado por {{ producto.usuario_id }} el {{ producto.fecha }}</td>
                      <td>{{ producto.estado | checkStatus }}</td>
                      
                  </tr>
              </tbody>
              
          </table>
        </div>
            
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>