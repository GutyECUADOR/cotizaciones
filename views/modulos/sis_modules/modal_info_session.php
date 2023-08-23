<!-- Modal -->
<div class="modal" id="modal_info_sesion" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Informacion de Sesion</h4>
      </div>
      <div class="modal-body">

        <div class="panel panel-default">
            <div class="panel-heading">Conexion por defecto</div>
            <div class="panel-body">
            Empresa: <?php echo $_SESSION["empresaAUTH".APP_UNIQUE_KEY] ?>
            <br> Usuario:<?php echo $_SESSION["usuarioRUC".APP_UNIQUE_KEY] ?>
            <br> Supervisor:<?php echo $_SESSION["isSupervisor".APP_UNIQUE_KEY] ?>
            <br> RUC:<?php echo $_SESSION["empresaRUC".APP_UNIQUE_KEY]  ?>
            <br> Grupo: <?php echo $_SESSION["usuarioGRUPO".APP_UNIQUE_KEY]  ?>
            <br> Bodega_Default: <?php echo $_SESSION["bodegaDefault".APP_UNIQUE_KEY]  ?>
            
            
            <br> Actions: <?php var_dump(($_SESSION["arrayModulosAccess".APP_UNIQUE_KEY]))  ?>
            <?php 
            /* if (in_array(trim($_GET['action']), $_SESSION["arrayModulosAccess".APP_UNIQUE_KEY])) {
                echo "Existe Action";
            }else{
              echo 'No existe';
            } */
            ?>
            </div>
        </div>

        <!-- <div class="panel panel-default">
            <div class="panel-heading">Tramaco Session</div>
            <div class="panel-body">
            <?php  
              echo $_SESSION["DEFAULT_TRAMACO_SERVER".APP_UNIQUE_KEY];
              echo ' - ' . $_SESSION["DEFAULT_TRAMACO_USER".APP_UNIQUE_KEY];
            ?>
            </div>
        </div> -->

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>