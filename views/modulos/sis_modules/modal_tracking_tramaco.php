<!-- Modal -->
<div class="modal fade" id="modal_tracking_tramaco" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Tracking Tramaco</h4>
      </div>
      <div class="modal-body">

        <div class="panel panel-default" v-for="tracking in trackinglist">
            <div class="panel-heading"><strong>{{ tracking.fechaHora }}</strong> - {{ tracking.estado }} </div>
              <div class="panel-body">
                <strong>{{ tracking.descripcion }}</strong>
                <p> <strong> Transportista: </strong> {{ tracking.transportista }}</p>
                <p> <strong> Transporte: </strong> {{ tracking.transporte }}</p>
              </div>
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>