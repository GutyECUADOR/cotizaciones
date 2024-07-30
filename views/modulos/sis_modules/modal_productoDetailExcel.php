<div class="modal fade" id="modal_productoDetailExcel" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title"> Editar registro : {{ producto_activo.nombre }} </h4>
        </div>
        <div class="modal-body">
            
        <form>
           
            <div class="input-group">
                <span class="input-group-addon">Nombre: </span>
                <input type="text" class="form-control" id="producto_nombre" v-model="producto_activo.nombre" >
            </div>

            <div class="input-group">
                <span class="input-group-addon">Etiquetas: </span>
                <input type="text" class="form-control"  id="producto_etiquetas" v-model="producto_activo.etiquetas" >
            </div>
            
            <div class="input-group" style="width: 100%;">
                <span class="input-group-addon">Grupo</span>
                    <select v-model="producto_activo.grupo" class="form-control">
                    <option value="">Seleccione por favor</option>
                    <option v-for="grupo in grupos" :value="grupo.NOMBRE">
                    {{grupo.NOMBRE}}
                    </option>
                </select>

                <span class="input-group-addon">Marca</span>
                <select v-model="producto_activo.marca" class="form-control">
                    <option value="">Seleccione por favor</option>
                    <option v-for="marca in marcas" :value="marca.CODIGO">
                    {{marca.NOMBRE}}
                    </option>
                </select>
            </div>

            </br>


            <div class="form-group">
                <label for="comment">Descripcion:</label>
                <textarea class="form-control tiny" rows="5" id="producto_descripcion"></textarea>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <!-- Default panel contents -->
                
                        <div class="panel-heading clearfix">
                        <i class="fa fa-list-alt" aria-hidden="true"></i> Variantes de producto - {{ producto_activo?.variantes?.length }}
                            
                        </div>
                                            
                        <div class="panel-body">
                            <div class="table-responsive ">     
                                <table class="table table-striped table-hover table-condensed">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th style="min-width: 150px;">Codigo</th>
                                        <th style="min-width: 270px;">Articulo</th>
                                        <th>Talla</th>
                                        <th>Color</th>
                                        <th>Peso (KGs)</th>
                                        <th>Precio</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(variante, index) in producto_activo.variantes">
                                        <td>
                                            <span>{{ index + 1 }}</span>
                                        </td>
                                        <td>
                                            <input type="text" style="font-size: 10.5px;" class="form-control text-center input-sm" :value="variante.codigo" readonly>
                                        </td>
                                        <td><input type="text" style="font-size: 10.5px;" class="form-control text-center input-sm" :value="variante.nombre"  readonly></td>
                                        <td>
                                            <select v-model="variante.talla" class="form-control input-sm">
                                                <option v-for="talla in tallas" :value="talla.CODIGO.trim()">
                                                {{ talla.CODIGO }} ({{talla.NOMBRE }}) 
                                                </option>
                                            </select>
                                        </td>
                                        <td>
                                            <select v-model="variante.color" class="form-control input-sm">
                                                <option v-for="color in colores" :value="color.codigo.trim()">
                                                {{ color.codigo }} ({{color.nombre}})
                                                </option>
                                            </select>
                                        </td>
                                        <td>
                                        <input type="number" step=".01" class="form-control text-center input-sm" v-model="variante.peso">
                                        </td>
                                        <td>
                                        <input type="number" step=".01" class="form-control text-center input-sm" :value="variante.precio" readonly>
                                        </td>
                                        
                                    </tr>
                                    </tbody>
                                </table>
                            </div>

                            
                        </div>
                    </div>
                </div>
            </div>

        </form>
            
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-success" @click="actualizarProducto">Actualizar</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        </div>
        </div>
    </div>
</div>