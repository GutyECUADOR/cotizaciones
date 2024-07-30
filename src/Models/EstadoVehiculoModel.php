<?php namespace App\Models;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EstadoVehiculoModel extends Conexion { 
   
    public $defaulDataBase;

    public function __construct() {
        $this->defaulDataBase = (!isset($_SESSION["empresaAUTH".APP_UNIQUE_KEY])) ? DEFAULT_DBName : $_SESSION["empresaAUTH".APP_UNIQUE_KEY] ;
        $this->setDbname($this->defaulDataBase);
        $this->conectarDB();
    }
    
    public function getDocumentos(string $fechaINI, string $fechaFIN, string $busqueda){
        $busquedaString = '%'.$busqueda.'%';
        $query = "
            SELECT TOP 1000 * FROM wssp.dbo.CAB_estado_vehiculo
        ";
       
        try{
            $stmt = $this->instancia->prepare($query); 
            //$stmt->bindParam(':busqueda', $busquedaString);
            $stmt->execute();
            return $stmt->fetchAll( \PDO::FETCH_ASSOC );
        }catch(PDOException $exception){
            http_response_code(400);
            return array('status' => 'error', 'mensaje' => $exception->getMessage() );
        }
    }

    public function getAllProductos(string $busqueda){
        $busquedaString = '%'.$busqueda.'%';
        $query = "
        SELECT TOP 100 * 
            FROM wssp.dbo.ITEMS_estado_vehiculos 
        WHERE descripcion LIKE :busqueda AND tipo = 'ODP'
        ";
       
        try{
            $stmt = $this->instancia->prepare($query); 
            $stmt->bindParam(':busqueda', $busquedaString);
            $stmt->execute();
            return $stmt->fetchAll( \PDO::FETCH_ASSOC );
        }catch(PDOException $exception){
            return array('status' => 'error', 'mensaje' => $exception->getMessage() );
        }
    }

    public function getProducto(string $busqueda){
        $query = "
        SELECT TOP 1 * 
            FROM wssp.dbo.ITEMS_estado_vehiculos 
        WHERE codigo = :busqueda AND tipo = 'ODP'
        ";
       
        try{
            $stmt = $this->instancia->prepare($query); 
            $stmt->bindParam(':busqueda', $busqueda );
            $stmt->execute();
            return $stmt->fetch( \PDO::FETCH_ASSOC );
        }catch(PDOException $exception){
            return array('status' => 'error', 'mensaje' => $exception->getMessage() );
        }
    }

    public function getDBNameByCodigo($codigoDB){
        $query = "SELECT TOP 1 NameDatabase, Codigo FROM SBIOKAO.dbo.Empresas_WF WHERE Codigo = :codigo"; 
        $stmt = $this->instancia->prepare($query); 
        $stmt->bindParam(':codigo', $codigoDB); 
       
            if($stmt->execute()){
                $resulset = $stmt->fetch( \PDO::FETCH_ASSOC );
            }else{
                $resulset = false;
            }
        return $resulset;  
    }

    public function getNewCodigo($tipo='EST'){
        $query = "SELECT '$tipo'+RIGHT('000000'+ISNULL(CONVERT (Varchar , (SELECT COUNT(*)+1 FROM wssp.dbo.CAB_estado_vehiculo)),''),6) as codigo";
        $stmt = $this->instancia->prepare($query); 

        try{
            $stmt->execute();
            return $stmt->fetch( \PDO::FETCH_ASSOC );
        }catch(PDOException $exception){
            return array('status' => 'error', 'mensaje' => $exception->getMessage() );
        }
    }

    public function getEmpresas(){
        $query = "SELECT * FROM wssp.dbo.databases_info";
        $stmt = $this->instancia->prepare($query); 

        try{
            $stmt->execute();
            return $stmt->fetchAll( \PDO::FETCH_ASSOC );
        }catch(PDOException $exception){
            return array('status' => 'error', 'mensaje' => $exception->getMessage() );
        }
    }

    public function getItems($tipo){
        $query = "SELECT top 5 * FROM wssp.dbo.ITEMS_estado_vehiculos WHERE activo='1' and tipo='$tipo'";
        $stmt = $this->instancia->prepare($query); 

        try{
            $stmt->execute();
            return $stmt->fetchAll( \PDO::FETCH_ASSOC );
        }catch(PDOException $exception){
            return array('status' => 'error', 'mensaje' => $exception->getMessage() );
        }
    }

    public function getEmpleadoByID($cedula){
        $query = "SELECT Codigo, Nombre FROM dbo.USUARIOS WHERE Codigo = :Codigo";
       
        try{
            $stmt = $this->instancia->prepare($query); 
            $stmt->bindParam(':Codigo', $cedula);
            $stmt->execute();
            return $stmt->fetch( \PDO::FETCH_ASSOC );
        }catch(PDOException $exception){
            return array('status' => 'error', 'mensaje' => $exception->getMessage() );
        }
    }

    public function getVehiculoByPlaca($placa){
       
        $query = "SELECT * FROM dbo.ACT_ARTICULOS WHERE Codigo='$placa'";
        $stmt = $this->instancia->prepare($query); 
        try{
            $stmt->execute();
            return $stmt->fetch( \PDO::FETCH_ASSOC );
        }catch(PDOException $exception){
            return array('status' => 'error', 'mensaje' => $exception->getMessage() );
        }
    }

    public function saveSolicitud($solicitud){
        $newCod = $this->getNewCodigo('EST')["codigo"];
        $query = "
            INSERT INTO 
                wssp.dbo.CAB_estado_vehiculo 
            VALUES ('$newCod','$solicitud->empresa','$solicitud->vehiculo','$solicitud->kilometraje','','$solicitud->empleado','$solicitud->fecha','$solicitud->observacion',0)
        ";

        try{
            $stmt = $this->instancia->prepare($query); 
            $stmt->execute();
            return $this->saveSolicitudMOV($solicitud->items, $newCod);
            
        }catch(PDOException $exception){
            return array('error' => TRUE, 'status' => 'error', 'mensaje' => $exception->getMessage() );
        }

    }

    public function saveOrdenPedido($solicitud){
        $newCod = $this->getNewCodigo('ODP')["codigo"];
       
        try{
            $this->instancia->beginTransaction();
       
            $query = "
                INSERT INTO 
                    wssp.dbo.CAB_estado_vehiculo 
                VALUES (
                    :codigo, 
                    :empresa, 
                    :placas, 
                    :kilometraje,
                    '', 
                    :supervisor, 
                    :fecha, 
                    :fechaMantenimiento, 
                    :proximaFechaMantenimiento, 
                    :fechamatricula, 
                    :proximafechaMatricula, 
                    :observacion,
                    0)
            ";

            $stmt = $this->instancia->prepare($query); 
            $stmt->bindValue(':codigo', $newCod);
            $stmt->bindValue(':empresa', $this->defaulDataBase);
            $stmt->bindValue(':placas', $solicitud->vehiculo->placas);
            $stmt->bindValue(':kilometraje', $solicitud->vehiculo->kilometraje);
            $stmt->bindValue(':supervisor', $solicitud->supervisor->codigo);
            $stmt->bindValue(':fecha', date('Ymd'));
            $stmt->bindValue(':fechaMantenimiento', $solicitud->vehiculo->fechaMantenimiento);
            $stmt->bindValue(':proximaFechaMantenimiento', $solicitud->vehiculo->proximaFechaMantenimiento);
            $stmt->bindValue(':fechamatricula', $solicitud->vehiculo->fechamatricula);
            $stmt->bindValue(':proximafechaMatricula', $solicitud->vehiculo->proximafechaMatricula);
            $stmt->bindValue(':observacion', $solicitud->observacion);
            $stmt->execute();

            foreach ($solicitud->items as $item) {
                $query = "
                INSERT INTO 
                    wssp.dbo.MOV_estado_vehiculo 
                VALUES ('$newCod','$item->codigo','')
                ";
            
                $stmt = $this->instancia->prepare($query); 
                $stmt->execute();
            }

            $commit = $this->instancia->commit();
            
            return array('status' => 'OK', 'commit' => $commit, 'newcod'=> $newCod);
         
        }catch(PDOException $exception){
            return array('error' => TRUE, 'status' => 'error', 'mensaje' => $exception->getMessage() );
        }

        
    }

    public function saveNewProduct($producto){
      
        $query = "
            INSERT INTO dbo.ITEMS_pagos_vehiculo VALUES ('$producto->codigoMaster','$producto->codigo','$producto->descripcion');
            ";

        try{
            $stmt = $this->instancia->prepare($query); 
            $stmt->execute();
            return array('error' => FALSE, 'message' => 'Registro correcto');
           
        }catch(PDOException $exception){
            return array('error' => TRUE, 'status' => 'error', 'message' => $exception->getMessage() );
        }

        
    }

    public function saveSolicitudMOV($arrayItems, $codigoCAB){
        $cont = 0;
        foreach ($arrayItems as $item) {
            $query = "
            INSERT INTO 
                wssp.dbo.MOV_estado_vehiculo 
            VALUES ('$codigoCAB','$item->codigo','$item->valor')
            ";
           
            $stmt = $this->instancia->prepare($query); 
            $stmt->execute();
            $cont++;
        }

        if ($cont>=1){
            return array('error' => FALSE, 'message' => 'Registro correcto', 'nuevoregistro' => $codigoCAB, 'itemsregistrados' => $cont );
           
        }
    }

    public function saveWinfenixCOM_CAB($solicitud){
        try{ 
        /*Parametro de registro */
        $tipoDOC = 'ORD';
        $datosEmpresa = $this->getDatosEmpresaFromWINFENIX($this->defaulDataBase);
        //Crea mos nuevo codigo de COB_CAB (secuencial)
        $newCodigoWith0 = $this->getNextNumDocWINFENIX($tipoDOC, $this->defaulDataBase); // Recuperamos secuencial de SP de Winfenix
        $new_cod_VENCAB = $datosEmpresa['Oficina'].$datosEmpresa['Ejercicio'].$tipoDOC.$newCodigoWith0;
        $oficina = $datosEmpresa['Oficina'];
        $ejercicio = $datosEmpresa['Ejercicio'];
        $PCID = php_uname('n');
        $fechaActual = date('Ymd');

        $proveedor = $solicitud->proveedor;
        $productos = $solicitud->productos;
       
        foreach ($productos as $producto) {
            $query = "
                INSERT INTO wssp.dbo.COMMOV_estado_vehiculos VALUES ('$solicitud->IDDocument','$producto->codigo','$producto->cantidad','$producto->valsubtotal')
            ";

            $stmt = $this->instancia->prepare($query); 
            $status = $stmt->execute();
        }

            $query = "
                exec Sp_comgracab 'I','ADMINWSSP','$PCID','$oficina','$ejercicio','$tipoDOC','$newCodigoWith0','$fechaActual','$proveedor->codigo','BSG','DOL','1.00','$proveedor->formaPago','$proveedor->diasPago','0','0','E','0','0','0','$solicitud->comentario','','$fechaActual','$solicitud->subtotal','0.00','$solicitud->iva','0.00','$solicitud->total','0.00','','','','','','$solicitud->totalBienes','$solicitud->totalServicios','0.00','0.00','0.00','0.00','0.00','0.00','0.00','01','','','','','','01','$fechaActual','0','0.00','N','0.00','$solicitud->iva','0.00','','12:00:00','','','$fechaActual','','','','','','0','0','0','','','','','0','','','','','','','','0','0','','','','','','0.00','$solicitud->subtotal','0.00','0.00','0.00','0.00'
            ";

            $stmt = $this->instancia->prepare($query); 
            $status = $stmt->execute();

            if ($status) {

                if ($solicitud->totalServicios != 0) {
                    /*Servicios */
                        $query = "
                        exec Sp_comgramov 'I','$oficina','$ejercicio','$tipoDOC','$newCodigoWith0','$fechaActual','$proveedor->codigo','BSG','E','0','SERC-027','UND','1.0000','0.00','$solicitud->totalServicios','0.0000000','$solicitud->totalServicios','12.0000','','$fechaActual','0','5','$solicitud->IVAServicios','0.0000','0.0000','0','0','0','','0','0','0','0','0','0.00','0.00','0.00','0.00','0.00','0.00','0.00','','','','0.00','0','0','','0','','0','0','0','0','','0','0','0','0','','','','0.000000','1','0','','','','T1S'
                    ";
                    $stmt = $this->instancia->prepare($query); 
                    $stmt->execute();
                }
                

                if ($solicitud->totalBienes != 0) {
                    /*Bienes o Productos */
                    $query = "
                        exec Sp_comgramov 'I','$oficina','$ejercicio','$tipoDOC','$newCodigoWith0','$fechaActual','$proveedor->codigo','BSG','E','0','COMC-016','UND','1.0000','0.00','$solicitud->totalBienes','0.0000000','$solicitud->totalBienes','12.0000','','$fechaActual','0','5','$solicitud->IVABienes','0.0000','0.0000','0','0','0','','0','0','0','0','0','0.00','0.00','0.00','0.00','0.00','0.00','0.00','','','','0.00','0','0','','0','','0','0','0','0','','0','0','0','0','','','','0.000000','1','0','','','','T1S'
                    ";

                    $stmt = $this->instancia->prepare($query); 
                    $stmt->execute();
                
                }

                

                
                return array(
                    'error' => FALSE,
                    'status' => $status, 
                    'message'  => 'Registro realizado exitosamente.',
                    'newdocument' => $new_cod_VENCAB
                    ); 
        

            }else {
                return array(
                    'error' => TRUE,
                    'status' => $status, 
                    'message'  => 'Registro no se realizo de forma correcta.',
                    'newdocument' => $new_cod_VENCAB
                    ); 
            }
            
            
          
            
        }catch(PDOException $exception){
            return array('error' => TRUE, 'status' => 'error', 'mensaje' => $exception->getMessage() );
        }

        
    }

    /*Retorna array con informacion de la empresa que se indique*/
    public function getDatosEmpresaFromWINFENIX(){
        
        $query = "SELECT NomCia, Oficina, Ejercicio, DirCia, TelCia, RucCia, Ciudad  FROM dbo.DatosEmpresa";
        $stmt = $this->instancia->prepare($query); 

        try{
            $stmt->execute();
            return $stmt->fetch( \PDO::FETCH_ASSOC );
        }catch(PDOException $exception){
            return array('status' => 'error', 'mensaje' => $exception->getMessage() );
        }
    }

    /*Retorna array asociativo con informacion del cliente que se indique*/
    public function getDatosDocumentsWINFENIXByTypo ($tipoDOC){
        $query = "SELECT CODIGO, NOMBRE, Serie FROM dbo.VEN_TIPOS WHERE CODIGO = '$tipoDOC'";
        $stmt = $this->instancia->prepare($query); 

        if($stmt->execute()){
            return $stmt->fetch( \PDO::FETCH_ASSOC );
        }else{
            return false;
        }
    }

    public function getNextNumDocWINFENIX ($tipoDOC){
        $query = "
            SET NOCOUNT ON  
            exec Sp_Contador 'COM','99','','$tipoDOC',''
        ";  // Final del Query SQL 


        $stmt = $this->instancia->prepare($query); 
        try{
            $stmt->execute();
            $resulset = $stmt->fetch( \PDO::FETCH_ASSOC );
            $input = $resulset['NExtID'];
            return str_pad($input, 8, "0", STR_PAD_LEFT);
             

        }catch(PDOException $exception){
            return array('status' => 'error', 'mensaje' => $exception->getMessage() );
        }

        
    }

    /*Retorna el secuencial de WinFenix en formato 0000XXXX - Winfenix*/
    public function formatoNextNumDocWINFENIX ($secuencialWinfenix, $empresa='008'){
        $newCod = $this->instancia->query("select RIGHT('00000000' + Ltrim(Rtrim('$secuencialWinfenix')),8) as newcod");
        $codigoConFormato = $newCod->fetch(\PDO::FETCH_ASSOC);
        $codigoConFormatoSingle = $codigoConFormato['newcod'];
        return $codigoConFormatoSingle;
    }

    public function getAllVehiculos ($busqueda=''){
        $query = "
        SELECT TOP 100
            vehiculo.Nombre as nombreVehiculo,
            vehiculo.Marca as marcaVehiculo,
            vehiculo.feccompra as fechaCompraVehiculo,
            SBIO.Nombre as nombreAsignadoA,
            wssp.*
        FROM
            ACT_ARTICULOS as vehiculo
        INNER JOIN wssp.dbo.CAB_estado_vehiculo as wssp on vehiculo.Codigo = wssp.placa COLLATE Modern_Spanish_CI_AS
        INNER JOIN dbo.USUARIOS as SBIO on SBIO.Codigo = wssp.asignadoA
        WHERE wssp.placa LIKE '".$busqueda."%'
        ORDER BY fecha DESC
        
        ";

        try{
            $stmt = $this->instancia->prepare($query); 
            $stmt->execute();
            return $stmt->fetchAll( \PDO::FETCH_ASSOC );
        }catch(PDOException $exception){
            return array('status' => 'error', 'mensaje' => $exception->getMessage() );
        }
    }

    public function get_CAB_estado_vehiculo ($codigo, $empresa='008'){

        $query = "
            
            SELECT 
                CAB.id,
                CAB.codigo,
                Activos.Codigo as placa,
                Activos.Nombre as detalleVehiculo,
                CAB.empresa as empresaCod,
                CAB.empresa as empresaName,
                CAB.kilometraje,
                CAB.aprobadoPor,
                CAB.asignadoA,
                SBIO.Nombre as nombreAsignado,
                CAB.fecha,
                CAB.fechaMantenimiento,
                CAB.proximaFechaMantenimiento,
                CAB.fechamatricula,
                CAB.proximafechaMatricula,
                CAB.observacion,
                CAB.estado
            FROM 
                dbo.ACT_ARTICULOS as Activos
                INNER JOIN wssp.dbo.CAB_estado_vehiculo as CAB on CAB.placa = Activos.Codigo COLLATE Modern_Spanish_CI_AS
                INNER JOIN dbo.usuarios as SBIO on SBIO.Codigo = CAB.asignadoA
               
            WHERE 
                CAB.codigo ='$codigo'
        
        ";

        try{
            $stmt = $this->instancia->prepare($query); 
            $stmt->execute();
            return $stmt->fetch( \PDO::FETCH_ASSOC );
        }catch(PDOException $exception){
            return array('status' => 'error', 'mensaje' => $exception->getMessage() );
        }
    }

    public function get_MOV_estado_vehiculo ($codigo){

        $query = "
        SELECT
            MOV.id,
            MOV.codigo as codOrden,
            MOV.item as codItem,
            ITEMS.descripcion as descripcionItem,
            MOV.valor
        FROM 
            wssp.dbo.MOV_estado_vehiculo as MOV
            INNER JOIN wssp.dbo.ITEMS_estado_vehiculos AS ITEMS ON ITEMS.codigo = MOV.item
        WHERE MOV.codigo = '$codigo'
        
        ";

        try{
            $stmt = $this->instancia->prepare($query); 
            $stmt->execute();
            return $stmt->fetchAll( \PDO::FETCH_ASSOC );
        }catch(PDOException $exception){
            return array('status' => 'error', 'mensaje' => $exception->getMessage() );
        }
    }

    public function get_COMMOV_estado_vehiculo ($codigo){

        $query = "
        SELECT
            MOV.id,
            MOV.codigoDocumento,
            MOV.codigoProducto,
            ITEMS.descripcion,
            MOV.cantidad,
            MOV.valor
        FROM 
            wssp.dbo.COMMOV_estado_vehiculos as MOV
            INNER JOIN wssp.dbo.ITEMS_pagos_vehiculo AS ITEMS ON ITEMS.codigoItem = MOV.codigoProducto
        WHERE MOV.codigoDocumento = '$codigo'

        
        ";

        try{
            $stmt = $this->instancia->prepare($query); 
            $stmt->execute();
            return $stmt->fetchAll( \PDO::FETCH_ASSOC );
        }catch(PDOException $exception){
            return array('status' => 'error', 'mensaje' => $exception->getMessage() );
        }
    }

    public function generaReporte($codigo, $outputMode = 'S'){

        $empresaData = $this->getInfoEmpresa();
        $CAB_estado_vehiculo = $this->get_CAB_estado_vehiculo($codigo, $this->defaulDataBase);
        $MOV_estado_vehiculo = $this->get_MOV_estado_vehiculo($codigo, $this->defaulDataBase);

        $COMMOV_estado_vehiculo = $this->get_COMMOV_estado_vehiculo($codigo);
        $tipoDOC = substr($codigo, 0,3);
        $codEstado = $CAB_estado_vehiculo['estado'];

        function titulo ($tipoDOC){
            if ($tipoDOC == 'ODP') {
                return 'ORDEN DE PEDIDO';
            }else{
                return 'INFORME DE ESTADO DEL VEHICULO';
            }
        }

        function valoracion($tipoDOC, $valor){
            switch ($tipoDOC) {
                case 'ODP':
                    if ($valor == 1) {
                       return 'Realizar';
                    }else{
                        return 'Pendiente';
                    }
                    break;
                
                case 'EST':
                    if ($valor == 5) {
                       return 'Excelente';
                    }elseif ($valor == 4){
                        return 'Muy Bueno';
                    }elseif ($valor == 3){
                        return 'Bueno';
                    }elseif ($valor == 2){
                        return 'Regular';
                    }else{
                        return 'No dispone';
                    }
                    
                    break;
                

                default:
                    return 'Sin definir';
                    
                   
                    break;
            }
        }

        function estadoDocument($codEstado){
            if ($codEstado == 0) {
                return 'Pendiente aprobacion';
            }elseif ($codEstado == 1) {
                return 'Aprobado';
            }else{
                return 'Por definir';
            }
        }
        
         $html = '
             
            <div style="width: 100%;">
         
                <div style="float: right; width: 75%;">
                    <div id="informacion">    
                    <h3>'. titulo($tipoDOC) .'</h3>
                    <h4>'.$empresaData["NomCia"].'</h4>
                    <h4>Direccion: '.$empresaData["DirCia"].'</h4>
                    <h4>Telefono: '.$empresaData["TelCia"].'</h4>
                    <h4>RUC: '.$empresaData["RucCia"].'</h4>
                    </div>
                </div>
               
                <div id="logo" style="float: left; width: 20%;">
                    <img src="../../assets/img/logo.png" alt="Logo">
                </div>
         
            </div>
         
             <div id="infoCliente" class="rounded">
                 <div class="cabecera"><b>Fecha del Documento:</b> '. $CAB_estado_vehiculo["fecha"].'</div>
                 <div class="cabecera"><b>Vehiculo:</b> '. $CAB_estado_vehiculo["detalleVehiculo"] .'( '. $CAB_estado_vehiculo["placa"].')</div>
                 <div class="cabecera"><b>Fecha Matricula:</b> '. $CAB_estado_vehiculo["fechamatricula"].'</div>
                 <div class="cabecera"><b>Fecha próxima Matrícula:</b> '. $CAB_estado_vehiculo["proximafechaMatricula"].'</div>
                 <div class="cabecera"><b>Fecha Mantenimiento:</b> '. $CAB_estado_vehiculo["fechaMantenimiento"].'</div>
                 <div class="cabecera"><b>Fecha próximo Mantenimiento:</b> '. $CAB_estado_vehiculo["proximaFechaMantenimiento"].'</div>
                 <div class="cabecera"><b>Encargado:</b> '. $CAB_estado_vehiculo["nombreAsignado"] .'( '. $CAB_estado_vehiculo["asignadoA"].')</div>
                 <div class="cabecera"><b>Empresa:</b> '. $CAB_estado_vehiculo["empresaName"] .'( '. $CAB_estado_vehiculo["empresaCod"].')</div>
                 
             </div>
             <span>Items reportados</span>
         
            <table class="items" width="100%" style="font-size: 9pt; border-collapse: collapse; " cellpadding="8">
                 <thead>
                     <tr>
                        <td width="5%">#</td>
                        <td width="15%">Item</td>
                        <td width="50%">Descripcion</td>
                        <td width="20%">Valoracion</td>
                         
                     </tr>
                 </thead>
             <tbody>
         
             <!-- ITEMS HERE -->
             ';
                 $cont = 1;
                 foreach($MOV_estado_vehiculo as $row){
                    
                     $html .= '
         
                     <tr>
                         <td align="center">'.$cont.'</td>
                         <td>'.$row["codItem"].'</td>
                         <td>'.$row["descripcionItem"].'</td>
                         <td>'.valoracion($tipoDOC, $row["valor"]).'</td>
                        
                         
                     </tr>';
                     $cont++;
                     }
         
                   

             $html .= ' 
             
         
             <!-- END ITEMS HERE -->
                 
             </tbody>
             </table>';

                if ($tipoDOC == 'ODP') {
                    $html .= '
                    <span>Items Comprados</span>


                    <table class="items" width="100%" style="font-size: 9pt; border-collapse: collapse; " cellpadding="8">
                        <thead>
                            <tr>
                                <td width="5%">#</td>
                                <td width="15%">Item</td>
                                <td width="50%">Descripcion</td>
                                <td width="10%">Cant</td>
                                <td width="20%">Costo</td>
                                
                            </tr>
                        </thead>
                    <tbody>
                
                    <!-- ITEMS HERE -->
                    ';
                        $cont = 1;
                        foreach($COMMOV_estado_vehiculo as $row){
                            
                            $html .= '
                
                            <tr>
                                <td align="center">'.$cont.'</td>
                                <td>'.$row["codigoProducto"].'</td>
                                <td>'.$row["descripcion"].'</td>
                                <td>'.$row["cantidad"].'</td>
                                <td>'.$row["valor"].'</td>
                                
                                
                            </tr>';
                            $cont++;
                            }
                
                        

                    $html .= ' 
                    
                
                    <!-- END ITEMS HERE -->
                        
                    </tbody>
                    </table>';

                    
                }

             $html .= ' 
            
 
             <div style="width: 100%;">
                 <p id="observacion">Observacion:'. $CAB_estado_vehiculo["observacion"] .'</p> 
            
             </div>
         
             
         ';
 
         //==============================================================
         //==============================================================
         //==============================================================
 
         $mpdf = new \Mpdf\Mpdf();
 
         // LOAD a stylesheet
         $stylesheet = file_get_contents('../../assets/css/reportesStyles.css');
        
         $mpdf->WriteHTML($stylesheet,1);	// The parameter 1 tells that this is css/style only and no body/html/text
 
         $html = mb_convert_encoding($html, 'UTF-8', 'UTF-8');
         $mpdf->SetTitle("Reporte Generado");
         $mpdf->SetHTMLHeader('
           <div id="cod">
                <h5 class="myheader">'.$CAB_estado_vehiculo["codigo"].'</h5>  
                <h5 class="myheader">Página: {PAGENO} de {nbpg}</h5>  
                <h5 class="myheader" style="color:red">'. estadoDocument($codEstado) .'</h5>  
           </div> ');
         $mpdf->WriteHTML($html);
         
         return $mpdf->Output('doc.pdf', $outputMode);
 
         //==============================================================
         //==============================================================
         //==============================================================
 
    }

    public function getInfoEmpresa(){
    
        $query = "SELECT NomCia, Oficina, Ejercicio, DirCia, TelCia, RucCia, Ciudad  FROM dbo.DatosEmpresa";
        $stmt = $this->instancia->prepare($query); 
        try{
            $stmt->execute();
            return $stmt->fetch( \PDO::FETCH_ASSOC );
        }catch(PDOException $exception){
            return array('status' => 'error', 'mensaje' => $exception->getMessage() );
        }
    }

    public function aprobarOrden($codOrden) {
        $query = "UPDATE wssp.dbo.CAB_estado_vehiculo SET estado = '1' WHERE codigo='$codOrden'";
        $stmt = $this->instancia->prepare($query); 
        try{
            $stmt->execute();
            return $stmt->rowCount();
        }catch(PDOException $exception){
            return array('status' => 'error', 'mensaje' => $exception->getMessage() );
        }
    }

    public function canDoPago($codOrden) {
        $query = "SELECT * FROM wssp.dbo.COMMOV_estado_vehiculos WHERE codigoDocumento = '$codOrden'";
        $stmt = $this->instancia->prepare($query); 
        try{
            $stmt->execute();
            $exist = $stmt->rowCount();
            if ($exist == 0) {
                return TRUE;
            }else{
                return FALSE;
            }
        }catch(PDOException $exception){
            return array('status' => 'error', 'mensaje' => $exception->getMessage() );
        }
    }

    public function generateICalendar($codigoDocumento) {
        $CAB_estado_vehiculo = $this->get_CAB_estado_vehiculo($codigoDocumento, $this->defaulDataBase);
        
        $ics = new ICS(array(
        'description' => 'Mantenimiento segun Orden de Pedido '. $codigoDocumento ,
        'dtstart' => date('Ymd', strtotime($CAB_estado_vehiculo["proximaFechaMantenimiento"])),
        'dtend' => date('Ymd', strtotime($CAB_estado_vehiculo["proximaFechaMantenimiento"])),
        'summary' => 'Mantenimiento de Vehiculo: '. $CAB_estado_vehiculo["placa"]
        ));

        return $ics->to_string();

    }

    public function sendEmail($codDocument, $email=DEFAULT_EMAIL_ADMINISTRACION_VEHICULOS, $addcotizacionPDF=true){
       
        $correoCliente = $email;

        //Correo de sender
        
        $smtpserver = DEFAULT_SMTP;
        $userEmail = DEFAULT_SENDER_EMAIL;
        $pwdEmail = DEFAULT_EMAILPASS; 

        $mail = new PHPMailer(true);  // Passing `true` enables exceptions
        try {
            //Server settings
            $mail->SMTPDebug = false;                                 // Enable verbose debug output 0->off 2->debug
            $mail->isSMTP();                                      // Set mailer to use SMTP
            $mail->Host = $smtpserver;  // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                               // Enable SMTP authentication
            $mail->Username = $userEmail;                 // SMTP username
            $mail->Password = $pwdEmail;                           // SMTP password
            $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
            $mail->Port = 587;                                    // TCP port to connect to

            //Recipients
            $mail->setFrom($userEmail);
            $mail->addAddress($correoCliente, 'Cliente');     // Add a recipient
            
            //Content
            $mail->CharSet = "UTF-8";
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = 'Orden de Pedido - ' . $codDocument;
            $mail->Body    = 'Se adjunta orden de pedido';
           
            // Adjuntos
            if ($addcotizacionPDF) {
                $mail->addStringAttachment($this->generaReporte($codDocument), 'orden_'.$codDocument.'.pdf');
                $mail->addStringAttachment($this->generateICalendar($codDocument), 'proximo_mantenimiento.ics');
            }
            
            $mail->send();
            $detalleMail = 'Correo ha sido enviado a : '. $correoCliente;
           
                $pcID = php_uname('n'); // Obtiene el nombre del PC

                $log  = "User: ".' - '.date("F j, Y, g:i a").PHP_EOL.
                "PCid: ".$pcID.PHP_EOL.
                "Detail: ".$detalleMail.PHP_EOL.
                "-------------------------".PHP_EOL;
                //Save string to log, use FILE_APPEND to append.

                file_put_contents('../../logs/logMailOK.txt', $log, FILE_APPEND );
            
            return array('status' => 'ok', 'message' => $detalleMail ); 

        } catch (Exception $e) {

                $pcID = php_uname('n'); // Obtiene el nombre del PC
                $log  = "User: ".' - '.date("F j, Y, g:i a").PHP_EOL.
                "PCid: ".$pcID.PHP_EOL.
                "Detail: ".$mail->ErrorInfo .' No se pudo enviar correo a: ' . $correoCliente . PHP_EOL.
                "-------------------------".PHP_EOL;
                //Save string to log, use FILE_APPEND to append.
                file_put_contents('../../logs/logMailError.txt', $log, FILE_APPEND);
                $detalleMail = 'Error al enviar el correo. Mailer Error: '. $mail->ErrorInfo;
                http_response_code(400);
                return array('status' => 'false', 'message' => $detalleMail ); 
            
        }

    }

    public function getInfoProveedor($RUC) {

        //Query de consulta con parametros para bindear si es necesario.
        $query = " 
        
        SELECT 
            RTRIM(Proveedor.CODIGO) as CODIGO,
            RTRIM(Proveedor.RUC) as RUC,
            RTRIM(Proveedor.NOMBRE) as NOMBRE,
            RTRIM(Proveedor.EMAIL) as EMAIL,
            RTRIM(Proveedor.DIRECCION1) as DIRECCION,
            RTRIM(Proveedor.TELEFONO1) as TELEFONO,
            RTRIM(Proveedor.TIPOIVA) AS TIPOIVA,
            RTRIM(Proveedor.DIVISA) as DIVISA,
            RTRIM(Proveedor.Fpago) as FPAGO,
            RTRIM(Proveedor.diaspago) as DIASPAGO
        FROM dbo.PAG_PROVEEDORES as Proveedor
        WHERE 
            RUC='$RUC'

        
        ";  // Final del Query SQL 

       

        $stmt = $this->instancia->prepare($query); 
        try{
            if($stmt->execute()){
                return $resulset = $stmt->fetch( \PDO::FETCH_ASSOC );
                
            }else{
                return $resulset = false;
            }
            
        }catch(PDOException $exception){
            return array('status' => 'error', 'mensaje' => $exception->getMessage() );
        }
   
    }

    public function getInfoProducto($codigoProducto) {

        //Query de consulta con parametros para bindear si es necesario.
        $query = " 
            SELECT * FROM wssp.dbo.ITEMS_pagos_vehiculo WHERE codigoItem = '$codigoProducto'
        ";  // Final del Query SQL 

        
        $stmt = $this->instancia->prepare($query); 
        try{
                if($stmt->execute()){
                    return $resulset = $stmt->fetch( \PDO::FETCH_ASSOC );
                    
                }else{
                    return $resulset = false;
                }

        }catch(PDOException $exception){
            return array('status' => 'error', 'mensaje' => $exception->getMessage() );
        }
   
    }

    public function searchProducto($termino) {

        //Query de consulta con parametros para bindear si es necesario.
        $query = " 
            SELECT * FROM wssp.dbo.ITEMS_pagos_vehiculo WHERE codigoItem LIKE '$termino%' OR descripcion LIKE '$termino%'
        ";  // Final del Query SQL 

        
        $stmt = $this->instancia->prepare($query); 
        try{
                if($stmt->execute()){
                    return $resulset = $stmt->fetchAll( \PDO::FETCH_ASSOC );
                    
                }else{
                    return $resulset = false;
                }

        }catch(PDOException $exception){
            return array('status' => 'error', 'mensaje' => $exception->getMessage() );
        }
   
    }

    public function getProveedoresWinfenix($busqueda='%', $tipo='NOMBRE') {

        //Query de consulta con parametros para bindear si es necesario.
        $query = " 
            exec Sp_PAGCONPRO '$busqueda','','$tipo'
        ";  // Final del Query SQL 

        
        $stmt = $this->instancia->prepare($query); 
        try{
                if($stmt->execute()){
                    return $resulset = $stmt->fetchAll( \PDO::FETCH_ASSOC );
                    
                }else{
                    return $resulset = array();
                }

        }catch(PDOException $exception){
            return array('status' => 'error', 'mensaje' => $exception->getMessage() );
        }
   
    }

} 