<!-- Row datos de proveedor-->
<div class="row">

    <div class="col-lg-6 col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">Datos del Proveedor</div>
            <div class="panel-body">
                <div class="input-group input-group-sm">
                    <span class="input-group-addon">Cédula o RUC</span>
                    <input type="text" @change="getProveedor" @keyup="getProveedor" v-model="search_proveedor.text" class="form-control" placeholder="Cédula o RUC">
                    <span class="input-group-btn">
                        <button class="btn btn-default" type="button" data-toggle="modal" data-target="#modal_proveedor">
                            <span class="glyphicon glyphicon-search" aria-hidden="true"></span>
                        </button>
                    </span>
                    
                    <span class="input-group-btn">
                        <button class="btn btn-default" type="button" data-toggle="modal" data-target="#modalClienteNuevo">
                            <span class="glyphicon glyphicon-user" aria-hidden="true"></span>
                        </button>
                    </span>
                    <input type="text" class="form-control" :value="documento.proveedor.codigo" readonly>
                    
                </div>

                <div class="input-group input-group-sm">
                    <span class="input-group-addon" id="sizing-addon3">Nombre</span>
                    <input type="text" class="form-control" :value="documento.proveedor.nombre" placeholder="Nombre Cliente" readonly>
                </div>

                <div class="input-group input-group-sm">
                    <span class="input-group-addon" id="sizing-addon3"><span class="fa fa-envelope" aria-hidden="true"></span> Correo</span>
                    <input type="mail" class="form-control" :value="documento.proveedor.email" placeholder="Correo" readonly>
                </div>

                <div class="input-group input-group-sm">
                    <span class="input-group-addon"><span class="fa fa-phone" aria-hidden="true"></span> Telf.</span>
                    <input type="text" class="form-control text-center" :value="documento.proveedor.telefono" placeholder="Telefono" readonly>
                    <span class="input-group-addon"><span class="fa fa-calendar" aria-hidden="true"></span> Dias Pago</span>
                    <input type="text" class="form-control" :value="documento.proveedor.diaspago + ',' + documento.proveedor.fpago" placeholder="DiasPago" readonly>
                </div>

            </div>
        </div>
    </div>

    <div class="col-lg-6 col-md-12">
        <div class="panel panel-default">
        <div class="panel-heading">Datos de Pago</div>
            <div class="panel-body">
                
                <div class="input-group input-group-sm">
                    <span class="input-group-addon">Forma Pago</span>
                    <select id='formaPago' class="form-control input-sm">
                            <?php
                            foreach ($formasPago as $grupo => $row) {

                                $codigo = trim($row['CODIGO']);
                                $texto= $row['NOMBRE']; 
                                
                                echo "<option value='$codigo'>$texto</option>";
                            }
                            
                            ?>
                    </select>
                </div>


                <div class="form-group">
                    <textarea class="form-control" rows="2" id="comment" name="comment" maxlength="100" placeholder="Comentario de hasta maximo 100 caracteres..."></textarea>
                </div>
                
            </div>
        </div>
        
    </div>
</div>
