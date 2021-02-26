<div class="modal fade" id="modalClienteNuevo" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Nuevo Cliente</h4>
        </div>

        <div class="modal-body">
            <form id='registerNewUser'>
                
            <div class="input-group select-group" style="min-width: 217px;">
                <input type="number" id="RUCnuevoCliente"  placeholder="Numero de Cedula o RUC" class="form-control" style="width: 75%;"/>
                <select id="tipoIdentificacion" class="form-control input-group-addon" style="width: 25%;">
                    <option value="R">RUC</option>
                    <option value="C">Cedula</option>
                    <option value="P">Pasaporte</option>
                </select>
               
            </div>

            <div class="input-group">
                <span class="input-group-addon" style="min-width: 217px;">Nombre del Cliente o Contacto</span>
                <input type="text" id="nombreCliente" class="form-control" pattern="[1-3]{3}" >
            </div>

            <div class="input-group">
                <span class="input-group-addon" style="min-width: 217px;">Grupo</span>
                <select id="grupoCliente" class="form-control">
                    <?php
                    foreach ($grupos as $grupo => $row) {

                            $codigo = trim($row['CODIGO']);
                            $texto= $row['NOMBRE']; 
                            
                            echo "<option value='$codigo'>$texto</option>";
                        }
                        
                    ?>
                </select>
            </div>

            <div class="input-group">
                <span class="input-group-addon" style="min-width: 217px;">Tipo Cliente</span>
                <select id="tipoCliente" class="form-control">
                    <option value="01">Persona Natural</option>
                    <option value="02">Sociedad</option>
                </select>
            </div>

            <div class="input-group">
                <span class="input-group-addon" style="min-width: 217px;">Email</span>
                <input type="text" id="emailCliente" class="form-control" >
            </div>

            <div class="input-group">
                <span class="input-group-addon" style="min-width: 217px;">Cantón</span>
                <select id="cantonCliente" class="form-control">
                    <option value="UIO">QUITO</option>
                    <?php
                    foreach ($cantones as $canton => $row) {

                            $codigo = trim($row['codigo']);
                            $texto= $row['nombre']; 
                            
                            echo "<option value='$codigo'>$texto</option>";
                        }
                        
                    ?>
                </select>
            </div>

            <div class="input-group">
                <span class="input-group-addon" style="min-width: 217px;">Direccion</span>
                <input type="text" id="direccionCliente" class="form-control" >
            </div>

            <div class="input-group">
                <span class="input-group-addon" style="min-width: 217px;">Telefono</span>
                <input type="text" id="telefonoCliente" class="form-control" >
            </div>

            <div class="input-group">
                <span class="input-group-addon" style="min-width: 217px;">Codigo del Vendedor</span>
                <input type="number" id="vendedorCliente" class="form-control">
            </div>

             <div class="input-group">
                <span class="input-group-addon" style="min-width: 217px;">Nombre del Vendedor</span>
                <input type="text" id="vendedorClienteName" class="form-control" disabled>
            </div>

            </form>
        </div>
        <div class="modal-footer">
            <button type="submit" id="btnGuardarNuevoCliente" class="btn btn-primary">Guardar</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        </div>
        </div>
    </div>
</div>