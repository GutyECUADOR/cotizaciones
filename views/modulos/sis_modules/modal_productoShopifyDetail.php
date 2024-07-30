<div class="modal fade" id="modal_productoShopifyDetail" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title"> Editar registro : {{ producto_activo.nombre }} </h4>
        </div>
        <div class="modal-body">
            
        <form>
           
            <div class="input-group">
                <span class="input-group-addon">Nombre: </span>
                <input type="text" class="form-control" id="producto_nombre" v-model="producto_activo.title" >
            </div>

            <div class="input-group">
                <span class="input-group-addon">Etiquetas: </span>
                <input type="text" class="form-control"  id="producto_etiquetas" v-model="producto_activo.tags" >
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
                        <i class="fa fa-list-alt" aria-hidden="true"></i> Variantes de producto - {{ producto_activo?.variants?.length }}
                            
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
                                        <th>Peso (Gramos)</th>
                                        <th>Stock</th>
                                        <th>Precio</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(variante, index) in producto_activo.variants">
                                        <td>
                                            <span>{{ index + 1 }}</span>
                                        </td>
                                        <td>
                                            <input type="text" style="font-size: 10.5px;" class="form-control text-center input-sm" :value="variante.sku" readonly>
                                        </td>
                                        <td><input type="text" style="font-size: 10.5px;" class="form-control text-center input-sm" :value="variante.title"  readonly></td>
                                        <td>
                                            <select v-model="variante.option1" class="form-control input-sm">
                                                <option v-for="talla in tallas" :value="talla.NOMBRE.trim()">
                                                {{ talla.CODIGO }} ({{talla.NOMBRE }}) 
                                                </option>
                                            </select>
                                        </td>
                                        <td>
                                            <select v-model="variante.option2" class="form-control input-sm">
                                                <option v-for="color in colores" :value="color.nombre.trim()">
                                                {{ color.codigo }} ({{color.nombre}})
                                                </option>
                                            </select>
                                        </td>
                                        <td>
                                        <input type="number" step=".01" class="form-control text-center input-sm" v-model="variante.grams">
                                        </td>
                                        <td>
                                        <input type="number" step=".01" class="form-control text-center input-sm" v-model="variante.inventory_quantity">
                                        </td>
                                        <td>
                                        <input type="number" step=".01" class="form-control text-center input-sm" :value="variante.price" readonly>
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
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        </div>
        </div>
    </div>
</div>