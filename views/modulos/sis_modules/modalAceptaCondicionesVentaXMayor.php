<!-- Modal Cliente -->
<div class="modal fade" id="modalAceptaCondicionesVentaXMayor" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title">Aceptar condiciones para pedido al por mayor</h4>
        </div>

        <div class="modal-body">
            <div class="panel panel-default"> 
                <div class="panel-heading" style="margin-bottom:10px">Condiciones del cliente:<strong> {{ search_cliente_response?.data.NOMBRE }}</strong></div> 
                <div class="container">
                    <p>Tipo Pago: {{ search_cliente_response?.data.FPAGO | tiposPago }}</p>
                    <p>Dias Pago: {{ search_cliente_response?.data.DIASPAGO || 0}}</p>
                    <p>Número de Pagos: {{ parseInt(search_cliente_response?.data.NUMPAG) || 1 }}</p>
                    <p>Dias entre Pagos: {{ parseInt(search_cliente_response?.data.ENTREPAG) || 30}}</p>
                    <p>Cant. Documentos Vencidos Maximos: {{ search_cliente_response?.data.NumDocPen || 0 }}</p>
                    
                </div>

                <div class="panel-heading" style="margin-bottom:10px">Fechas Vencimiento Facturas</strong></div> 
                <div class="container">
                   
                    <p v-for="(fecha, index) in fechasPago">
                        - {{ fecha }}
                    </p>
                </div>
        
            
            </div>

        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" @click="cancelSubmit()">Cancelar</button>
            <button type="button" class="btn btn-primary" @click="selectCliente()" data-dismiss="modal">Entiendo las condiciones</button>
        </div>
        </div>
    </div>
</div>