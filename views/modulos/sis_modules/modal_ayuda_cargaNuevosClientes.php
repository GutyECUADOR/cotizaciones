<!-- Modal -->
<div class="modal fade" id="modal_ayuda_actualizarCostoProducto" tabindex="-1" role="dialog" >
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Ayuda</h4>
      </div>
      <div class="modal-body">
        <p>Descargue la siguiente plantilla de Excel y complete todos los datos de las columnas segun se indique:</p>
        <p>-Las cabeceras o titulos de columna deben estar en MAYUSCULAS:</p>
        <p>-Las cabeceras EMITARJETA y VENTARJETA deben tener el formato de fecha 2022-31-12 00:00:00.000 (Año, dia, mes)</p>


       <!--  <img src="<?php echo AYUDA_CARGA_NUEVOS_CLIENTES?>" class="img-fluid"> -->
        <a class="btn btn-success btn-block" href="<?php echo PLANTILLA_CARGA_NUEVOS_CLIENTES; ?>" role="button">
        <i class="fa fa-download" aria-hidden="true"></i>    
        Descargar Plantilla</a>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>