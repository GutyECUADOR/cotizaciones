<div class="modal fade" id="modalBuscarPedidosShopify" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document" style="width: 90%;">
        <div class="modal-content">
            <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"> Buscar Pedidos de Shopify </h4>
            </div>
            <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <strong>Información de búsqueda</strong>
                                </div>
                                <div class="panel-body">


                                    <div class="col-lg-8">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-addon">Cargar documentos hasta: </span>
                                            <input type="date" class="form-control text-center" v-model="documento.fechaFIN">
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <button @click="getOrders()" type="button" class="btn btn-primary btn-block btn-sm" :disabled="search_orders.isloading"  >
                                            <i class="fa" :class="[{'fa-spin fa-refresh': search_orders.isloading}, {  'fa-search' : !search_orders.isloading  }]" ></i> Cargar pedidos de Shopify
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <strong>Pedidos de Shopify - Solo status PAID</strong>
                                    
                                </div>
                            
                                <div class="panel-body">
                                    <div class="table-responsive">        
                                        <table class="table table-bordered table-hover table-striped table-sm" style="font-size: 12px">
                                            <thead>
                                                <tr>
                                                    <th style="width: 10%; min-width: 80px;" class="text-center headerTablaProducto">Pedido / ID</th>
                                                    <th style="width: 20%;" class="text-center headerTablaProducto">Fecha</th>
                                                    <th style="width: 20%; min-width: 101px;" class="text-center headerTablaProducto">Cliente</th>
                                                    <th style="width: 20%; min-width: auto;" class="text-center headerTablaProducto">Email/Telefono</th>
                                                    <th style="width: 20%;" class="text-center headerTablaProducto">Total Pagado</th>
                                                    <th style="width: 20%; min-width: 100px;" class="text-center headerTablaProducto">RUC</th>
                                                    <th style="width: 20%; min-width: 100px;" class="text-center headerTablaProducto">Gateway</th>
                                                    
                                                    <th style="width: 20%; min-width: 100px;" class="text-center headerTablaProducto">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tablaProductos">
                                                <tr v-for="order in documento.ordersShopify">
                                                    <td class="text-center">{{ order.name }} </br> <span style="font-weight: bold;"> {{ order.id }} </span></td>
                                                    <td class="text-center">{{ order.created_at | formatDate }}</td>
                                                    <td class="text-center">{{ order.customer.first_name + ' ' + order.customer.last_name }}</td>
                                                    <td class="text-center">{{ order.email }} </br> <span style="font-weight: bold;"> {{ order.shipping_address.phone }} </span> </td>
                                                    <td class="text-center">{{ order.total_price_usd }}</td>
                                                    <td class="text-center">{{ order.shipping_address.company }}</td>
                                                    <td class="text-center">{{ order.gateway }}</td>
                                                    <td class="text-center"><button type="button" @click="handleCreateDocument(order)" class="btn btn-primary btn-sm">Crear Documento SPY</button></td>
                                                
                                                    
                                                </tr>
                                            </tbody>
                                        
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>    
    </div>
</div>