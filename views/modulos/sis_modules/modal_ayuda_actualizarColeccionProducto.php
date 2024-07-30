<!-- Modal -->
<div class="modal fade" id="modal_ayuda_actualizarColeccionProducto" tabindex="-1" role="dialog" >
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Ayuda</h4>
      </div>
      <div class="modal-body">
        <p>Considerar los siguientes aspectos para una carga correcta de los artículos, el archivo excel debe cumplir con lo siguiente:</p>
        <ol>
        <li>El archivo puede tener varias hojas, pero se leerá la primera hoja unicamente para la carga de productos. Asegúrese de que la primera hoja de su archivo sea el listado de productos a actualizar.</li>
        <li>Debe tener una sola columna "CODIGO" en mayusculas el mismo que indica el codigo del producto en Winfenix.</li>
        <li>Debe tener una sola columna "NUEVONOMBRE" en mayusculas la misma que indica el nuevo nombre del producto, si desea mantener el nombre anterior coloque el nombre anterior aqui.</li>
        <li>Debe tener una sola columna "NUEVACOLECCION" en mayusculas la misma que indica el código de la la nueva coleccion del producto, si desea mantener la coleccion anterior coloque el codigo de la coleccion anterior aqui.</li>
        
        <li> Pueden existir otras columnas, pero las columnas obligatorias son CODIGO, NUEVONOMBRE y NUEVACOLECCION.</li>

          
        </ol>

        <img src="<?php echo AYUDA_ACTUALIZAR_COLECCION_PRODUCTOS?>" class="img-fluid">
        <a class="btn btn-success btn-block" href="<?php echo PLANTILLA_ACTUALIZAR_COLECCION_PRODUCTOS; ?>" role="button">
        <i class="fa fa-download" aria-hidden="true"></i>    
        Descargar Plantilla de Ejemplo</a>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>