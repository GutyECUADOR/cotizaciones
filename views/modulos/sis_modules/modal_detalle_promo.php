<div class="modal fade" id="modalDetallePromo" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Detalle de promocion <span id=codBuscadoPromo></span></h4>
        </div>

        <div class="modal-body">
            

            <div class="panel panel-default"> 
                <div class="panel-heading">Resultados</div> 
                    <div class="responsibetable"> 
                        <table id="tblResultadosDetallePromo" class="table"> 
                            <thead>
                                <tr> 
                                    <th>Codigo</th> 
                                    <th>Tipo</th> 
                                    <th>Nombre</th> 
                                    <th>Porcentaje %</th> 
                                </tr>
                            </thead> 
                            
                            <tbody>
                                <!-- Los resultados de la busqueda se desplegaran aqui-->
                                <div id="loaderClientes">
                                    <div class="loader" id="loader-4">
                                    <span></span>
                                    <span></span>
                                    <span></span>        
                                </div>
                            </tbody>
                        </table>
                    </div>
                </div>

        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        </div>
        </div>
    </div>
</div>