<?php namespace App\Models;

use Tavo\ValidadorEc;

/* LOS MODELOS del MVC retornaran unicamente arrays PHP sin serializar*/

class WinfenixModel extends Conexion  {

    public $defaulDataBase;
    public $valesModel;
    
    public function __construct() {
        parent::__construct();
        $this->defaulDataBase = (!isset($_SESSION["empresaAUTH".APP_UNIQUE_KEY])) ? DEFAULT_DBName : $_SESSION["empresaAUTH".APP_UNIQUE_KEY];
        $this->valesModel = new ValesPerdidaModel();
        $this->valesModel->setDbname($this->defaulDataBase);
        $this->valesModel->conectarDB();
    }

    /*Retorna busqueda de productos */
    public function Sp_INVCONARTWAN(object $busqueda) {
        $query = "exec Sp_INVCONARTWAN :texto,'', :gestion,'N', :bodega,'', :cantidad,'0','0','1','99','','','','','','','','','','0'";
        $stmt = $this->instancia->prepare($query); 
        $stmt->bindValue(':texto', '%'.$busqueda->texto); 
        $stmt->bindValue(':gestion', $busqueda->gestion); 
        $stmt->bindValue(':bodega', $busqueda->bodega); 
        $stmt->bindValue(':cantidad', $busqueda->cantidad); 
        $stmt->execute();

            if($stmt->execute()){
                $resulset = $stmt->fetchAll( \PDO::FETCH_ASSOC);
            }else{
                $resulset = false;
            }
        return $resulset;  

    }
    
   
    public function Sp_PAGCONPRO(object $busqueda){

        $query = "exec Sp_PAGCONPRO ?,'',?";
        $stmt = $this->instancia->prepare($query);
        $stmt->bindValue(1, $busqueda->termino); 
        $stmt->bindValue(2, $busqueda->campo); 
      
            if($stmt->execute()){
                $resulset = $stmt->fetchAll( \PDO::FETCH_ASSOC );
            }else{
                $resulset = false;
            }
        return $resulset;  
    }


    public function sql_getIngresosEgresos (object $busqueda) {
        $query = "
        SELECT 
            TIPO,
            NUMERO,
            CONVERT(CHAR(10),FECHA,102) as FECHA, 
            Doc_Relacion = ISNULL(Numrel,''''),
            BODEGA,total,
            DIVISA,
            OFI,
            (CASE ANULADO WHEN 1 THEN 'AN' ELSE '' END) AS ANULADO,
            ID FROM INV_CAB WITH(NOLOCK)  
        WHERE TIPO IN ('EPC','IPC') AND fecha BETWEEN :fechaINI  AND :fechaFIN  order by fecha,tipo,numero,doc_relacion
        ";
        $stmt = $this->instancia->prepare($query);
        $stmt->bindValue(':fechaINI', date("Ymd", strtotime($busqueda->fechaINI))); 
        $stmt->bindValue(':fechaFIN', date("Ymd", strtotime($busqueda->fechaFIN))); 
       
            if($stmt->execute()){
                $resulset = $stmt->fetchAll( \PDO::FETCH_ASSOC );
            }else{
                $resulset = false;
            }
        return $resulset;  
    }

    public function sql_getCreacionReceta (object $busqueda) {
        $query = "
        SELECT 
            TIPO,
            NUMERO,
            CONVERT(CHAR(10),FECHA,102) AS FECHA,
            Doc_Relacion = ISNULL(Numrel,''),
            BODEGA,
            total,
            DIVISA,OFI,
            (CASE ANULADO WHEN 1 THEN 'AN' ELSE '' END) AS ANULADO,
            ID FROM INV_CAB WITH(NOLOCK) 
        WHERE TIPO = 'STK' AND fecha BETWEEN :fechaINI AND :fechaFIN
        order by 
        fecha,
        tipo,
        numero,
        doc_relacion
        ";
        $stmt = $this->instancia->prepare($query);
        $stmt->bindValue(':fechaINI', date("Ymd", strtotime($busqueda->fechaINI))); 
        $stmt->bindValue(':fechaFIN', date("Ymd", strtotime($busqueda->fechaFIN))); 
       
            if($stmt->execute()){
                $resulset = $stmt->fetchAll( \PDO::FETCH_ASSOC );
            }else{
                $resulset = false;
            }
        return $resulset;  
    }

    public function sql_getINV_CAB(string $ID) {
        //Query de consulta con parametros para bindear si es necesario.
        $query = "
            SELECT * FROM INV_CAB WITH (NOLOCK) WHERE ID= :id
            ";  // Final del Query SQL 

        $stmt = $this->instancia->prepare($query);
        $stmt->bindParam(':id', $ID); 
      
            if($stmt->execute()){
                $resulset = $stmt->fetch( \PDO::FETCH_ASSOC );
            }else{
                $resulset = false;
            }
        return $resulset;  
    }

    public function SP_INVSelectMov(string $ID){

        $query = "exec SP_INVSelectMov ?";
        $stmt = $this->instancia->prepare($query);
        $stmt->bindValue(1, $ID); 
      
            if($stmt->execute()){
                $resulset = $stmt->fetchAll( \PDO::FETCH_ASSOC );
            }else{
                $resulset = false;
            }
        return $resulset;  
    }


    public function sql_buscarDocumentos(object $busqueda) {

        $fechaINI = date('Ymd', strtotime($busqueda->fechaINI));
        $fechaFIN = date('Ymd', strtotime($busqueda->fechaFIN));
        //Query de consulta con parametros para bindear si es necesario.
        $query = "
            SELECT 
                VEN.TIPO,
                VEN.NUMERO,
                RTRIM(VEN.SERIE)+'-'+RTRIM(LTRIM(VEN.SECUENCIA)) AS NFIS,
                CONVERT(CHAR(10),
                VEN.FECHA,102) AS FECHA,
                RTRIM(CLI.NOMBRE) AS CLIENTE,
                VEN.BODEGA,
                VEN.total,
                VEN.DIVISA,(
                CASE VEN.ANULADO WHEN 1 THEN 'AN' ELSE '' END) AS ANULADO,
                ven.id, 
                CANCELADA = ISNULL((SELECT TOP 1 mov.tipo+'-'+MOV.NUMERO FROM COB_MOV MOV WITH (NOLOCK) INNER JOIN COB_CAB CAB WITH (NOLOCK) ON (cab.ofi=mov.ofi and cab.eje=mov.eje and cab.tipo=mov.tipo and cab.numero=mov.numero) 
            WHERE LEFT(MOV.IDDOC,17) = VEN.ID AND ISNULL(CAB.ANULADO,0)=0 ORDER BY MOV.CREADODATE DESC),'')	
            FROM VEN_CAB VEN LEFT OUTER JOIN  COB_CLIENTES CLI ON (CLI.CODIGO = VEN.CLIENTE)  WHERE VEN.TIPO IN (:tipoDOC) AND VEN.OFI = '99'  AND Ven.fecha BETWEEN :fechaINI  AND :fechaFIN
            ORDER BY VEN.TIPO,VEN.NUMERO,VEN.FECHA
        ";
        $stmt = $this->instancia->prepare($query); 
        $stmt->bindParam(':fechaINI', $fechaINI);
        $stmt->bindParam(':fechaFIN', $fechaFIN);
        $stmt->bindParam(':tipoDOC', $busqueda->tipoDOC);
       
        $stmt->execute();

            if($stmt->execute()){
                return $stmt->fetchAll( \PDO::FETCH_ASSOC );
            }else{
                $resulset = false;
            }
        return $resulset;  

   
    }

    public function SP_COBCONCLI(object $busqueda){
      
        $query = "exec Sp_COBCONCLI :texto, '','NO'";
        $stmt = $this->instancia->prepare($query);
        $stmt->bindValue(':texto', $busqueda->texto); 
      
            if($stmt->execute()){
                $resulset = $stmt->fetchAll( \PDO::FETCH_ASSOC );
            }else{
                $resulset = false;
            }
        return $resulset;  
    }
    
    public function SP_COBCONCLIWAN(object $busqueda){
      
        $query = "exec Sp_COBCONCLIWAN :texto,'','NO','','50','0','0'";
        $stmt = $this->instancia->prepare($query);
        $stmt->bindValue(':texto', $busqueda->texto); 
      
            if($stmt->execute()){
                $resulset = $stmt->fetchAll( \PDO::FETCH_ASSOC );
            }else{
                $resulset = false;
            }
        return $resulset;  
    }

    public function sql_getBodegasWF(){
        $query  = "SELECT CODIGO, NOMBRE FROM dbo.INV_BODEGAS WITH(NOLOCK) ";
        try{
            $stmt = $this->instancia->prepare($query); 
            if($stmt->execute()){
                $resulset = $stmt->fetchAll( \PDO::FETCH_ASSOC );
            }else{
                $resulset = false;
            }
        return $resulset;  
    
        }catch(\PDOException $exception){
            return array('status' => 'ERROR', 'message' => $exception->getMessage() );
        }
           

    }

    public function sql_getVendedoresWF(){
        $query  = "SELECT CODIGO, NOMBRE FROM dbo.COB_VENDEDORES WITH(NOLOCK) ORDER BY NOMBRE";
        try{
            $stmt = $this->instancia->prepare($query); 
            if($stmt->execute()){
                $resulset = $stmt->fetchAll( \PDO::FETCH_ASSOC );
            }else{
                $resulset = false;
            }
        return $resulset;  

        }catch(\PDOException $exception){
            return array('status' => 'ERROR', 'message' => $exception->getMessage() );
        }
           

    }

    public function sql_getFormasPagoWF(){
        $query  = "SELECT * From dbo.FORMAPAGO WITH(NOLOCK) ORDER BY CODIGO";
        try{
            $stmt = $this->instancia->prepare($query); 
            if($stmt->execute()){
                $resulset = $stmt->fetchAll( \PDO::FETCH_ASSOC );
            }else{
                $resulset = false;
            }
        return $resulset;  

        }catch(\PDOException $exception){
            return array('status' => 'ERROR', 'message' => $exception->getMessage() );
        }
           

    }

    public function sql_getVenTiposDOCWF(string $tiposDocumentos='C'){
        $query  = "
                SELECT CODIGO, NOMBRE 
                FROM VEN_TIPOS 
                WITH(NOLOCK) 
                WHERE 
                    TIPODOC IN (:TIPODOC)
                ORDER BY CODIGO";
        try{
            $stmt = $this->instancia->prepare($query); 
            $stmt->bindValue(':TIPODOC', $tiposDocumentos);
             if($stmt->execute()){
                    $resulset = $stmt->fetchAll( \PDO::FETCH_ASSOC );
                }else{
                    $resulset = false;
                }
            return $resulset;  
        

        }catch(\PDOException $exception){
            return array('status' => 'ERROR', 'message' => $exception->getMessage() );
        }
           

    }

    public function sql_getTiposPagoTarjetaWF(){
        $query  = "SELECT * From dbo.VEN_MANTAR WITH(NOLOCK) ORDER BY Codigo";
        try{
            $stmt = $this->instancia->prepare($query); 
    
             if($stmt->execute()){
                    $resulset = $stmt->fetchAll( \PDO::FETCH_ASSOC );
                    $row = array('CODIGO' => 'CRE', 'NOMBRE' => 'CREDITO');
                    array_unshift($resulset, $row);
                    $row = array('CODIGO' => 'EFE', 'NOMBRE' => 'EFECTIVO');
                    array_unshift($resulset, $row);
                    $row = array('CODIGO' => 'SIN', 'NOMBRE' => 'SIN DESCUENTOS');
                    array_unshift($resulset, $row);
                }else{
                    $resulset = false;
                }
            return $resulset;  
        

        }catch(\PDOException $exception){
            return array('status' => 'ERROR', 'message' => $exception->getMessage() );
        }
           

    }

    public function sql_getGruposClientesWF(){
        $query  = "SELECT * From dbo.COB_Grupos WITH(NOLOCK) ORDER BY CODIGO";
        try{
            $stmt = $this->instancia->prepare($query); 
            if($stmt->execute()){
                $resulset = $stmt->fetchAll( \PDO::FETCH_ASSOC );
            }else{
                $resulset = false;
            }
        return $resulset;  
        

        }catch(\PDOException $exception){
            return array('status' => 'ERROR', 'message' => $exception->getMessage() );
        }
           

    }

    public function sql_getCantonesWF(){
        $query  = "SELECT * From dbo.TAB_CANTONES WITH(NOLOCK) ORDER BY Nombre";
        try{
            $stmt = $this->instancia->prepare($query); 
    
            if($stmt->execute()){
                $resulset = $stmt->fetchAll( \PDO::FETCH_ASSOC );
            }else{
                $resulset = false;
            }
            return $resulset;  
        }catch(\PDOException $exception){
            return array('status' => 'ERROR', 'message' => $exception->getMessage() );
        }
           

    }

    public function getDatosEmpresa (){
        $query = "SELECT NomCia, DirCia, TelCia, RucCia, Oficina, Ejercicio FROM dbo.DatosEmpresa WITH(NOLOCK)";
        $stmt = $this->instancia->prepare($query); 
        try{
            $stmt->execute();
            return $stmt->fetch( \PDO::FETCH_ASSOC );
        }catch(\PDOException $exception){
            return array('status' => 'ERROR', 'message' => $exception->getMessage() );
        }

    }

    public function getVenTipos ($tipoDOC){
        $query = "SELECT CODIGO, NOMBRE, Serie FROM dbo.VEN_TIPOS WITH(NOLOCK) WHERE CODIGO = '$tipoDOC'";
        $stmt = $this->instancia->prepare($query); 

        if($stmt->execute()){
            return $stmt->fetch( \PDO::FETCH_ASSOC );
        }else{
            return false;
        }
    }

    public function SQL_getVENCAB($IDDocument) {
        $query = " 
        SELECT 
            RTRIM(LTRIM(REPLACE(CLIENTE.NOMBRE, NCHAR(0x00A0), ''))) as NOMBRE,
            CLIENTE.RUC,
            CLIENTE.DIRECCION1,
            CLIENTE.TELEFONO1,
            RTRIM(LTRIM(REPLACE(CLIENTE.EMAIL, NCHAR(0x00A0), ''))) as EMAIL,
            VENDEDOR.CODIGO as CodigoVendedor,
            VENDEDOR.NOMBRE as VendedorName,
			BODEGA.NOMBRE as BodegaName,
            VEN_CAB.*
        FROM 
            dbo.VEN_CAB WITH(NOLOCK)
            INNER JOIN dbo.COB_CLIENTES as CLIENTE  WITH (NOLOCK)  on CLIENTE.CODIGO = VEN_CAB.CLIENTE
            LEFT JOIN dbo.INV_BODEGAS as BODEGA  WITH (NOLOCK)  on BODEGA.CODIGO = VEN_CAB.BODEGA
            LEFT JOIN dbo.COB_VENDEDORES as VENDEDOR  WITH (NOLOCK)  on VENDEDOR.CODIGO = VEN_CAB.CODVEN	
            WHERE ID='$IDDocument'
        ";  // Final del Query SQL 

        try{
            $stmt = $this->instancia->prepare($query); 
    
                if($stmt->execute()){
                    $resulset = $stmt->fetch( \PDO::FETCH_ASSOC );
                    
                }else{
                    $resulset = false;
                }
            return $resulset;  

        }catch(\PDOException $exception){
            return array('status' => 'error', 'message' => $exception->getMessage() );
        }
   
    }

    public function SQL_getVENMOV($IDDocument) {

       //Query de consulta con parametros para bindear si es necesario.
       $query = "
       SELECT
            ARTICULO.Nombre,
            VEN_MOV.*
            
        FROM 
            dbo.VEN_MOV WITH(NOLOCK)
            INNER JOIN dbo.INV_ARTICULOS as ARTICULO ON ARTICULO.Codigo = VEN_MOV.CODIGO
        WHERE 
            ID = '$IDDocument'
       ";  // Final del Query SQL 

       $stmt = $this->instancia->prepare($query); 
   
           if($stmt->execute()){
                $resulset = $stmt->fetchAll( \PDO::FETCH_ASSOC );
               
           }else{
               $resulset = false;
           }
       return $resulset; 
   
    }

    public function SP_contador ($tipoDOC, $tipoMOV, $ejercicio=''){
        
        try{
            $stmt = $this->instancia->prepare("SET NOCOUNT ON exec SP_CONTADOR :tipoMOV,'99', :ejercicio, :tipoDOC,''"); 
            $stmt->bindValue(':tipoMOV', $tipoMOV);
            $stmt->bindValue(':tipoDOC', $tipoDOC);
            $stmt->bindValue(':ejercicio', $ejercicio);
            $stmt->execute();
            
            $newCodLimpio = $stmt->fetch(\PDO::FETCH_ASSOC);
            $newCodLimpio =  $newCodLimpio['NEXTID'];
            
            $newCod = $this->instancia->query("select RIGHT('00000000' + Ltrim(Rtrim('$newCodLimpio')),8) as newcod");
            $codigoConFormato = $newCod->fetch(\PDO::FETCH_ASSOC);
            $codigoConFormato = $codigoConFormato['newcod'];
            return $codigoConFormato;

        }catch(\PDOException $exception){
            return array('status' => 'ERROR', 'message' => $exception->getMessage() );
        }

        
    }

    public function SP_VENGRACAB (object $documento) {
        try{
            $this->instancia->beginTransaction();

            $tipoDOC = $documento->tipoDOC;
            //Obtenemos informacion de la empresa
            $datosEmpresa =  $this->getDatosEmpresa();
            $serieDocs =  $this->getVenTipos($tipoDOC)['Serie'];
        
            //Creamos nuevo codigo de VEN_CAB (secuencial)
            if ($this->SP_contador($tipoDOC, 'VEN')) {
                $numeroDOC =  $this->SP_contador($tipoDOC, 'VEN'); 
            }else{
                throw new \Exception("No se ha podido obtener el contador del tipo de documento");
            }
            
            $new_cod_VENCAB = $datosEmpresa['Oficina'].$datosEmpresa['Ejercicio'].$tipoDOC.$numeroDOC;
            
            $query = "
                exec Sp_vengracab 'I ', :userID, :pcID, :oficina, :ejercicio, :tipoDOC, :numeroDOC,'', :fecha, :codCliente,
                :bodega,'DOL','1.00','0.00', :baseIVA,'0.00','0.00','0.00','0.00','0.00', :subtotal,'0.00', :impuesto,
                '0.00', :total, :formaPago,'0','0','0','S','0','1','0','0','','', :codVendedor,' ',' ', :observacion, :serie, 
                :secuencia,'','','','','0.00','0.00','0.00','','','','','','','','','','0','P','','','','','','0','','','','','0', :montoIVA,'0.00','0.00','0.00','0','1113431809','0','','','','','','','','','','  ', :fechaEntrega,''
            
            ";

            $stmt = $this->instancia->prepare($query); 
            $stmt->bindValue(':userID', $_SESSION["usuarioRUC".APP_UNIQUE_KEY]);
            $stmt->bindValue(':pcID', php_uname('n'));
            $stmt->bindValue(':oficina', $datosEmpresa['Oficina']);
            $stmt->bindValue(':ejercicio', $datosEmpresa['Ejercicio']);
            $stmt->bindValue(':tipoDOC', $tipoDOC);
            $stmt->bindValue(':numeroDOC', $numeroDOC);
            $stmt->bindValue(':fecha', date('Ymd'));
            $stmt->bindValue(':codCliente', $documento->cliente->codigo);

            $stmt->bindValue(':bodega', $documento->bodega);
            $stmt->bindValue(':baseIVA', $documento->subtotal);
            $stmt->bindValue(':subtotal', $documento->subtotal);
            $stmt->bindValue(':impuesto', $documento->IVA);

            $stmt->bindValue(':total', $documento->total);
            $stmt->bindValue(':formaPago', $documento->formaPago);
            $stmt->bindValue(':codVendedor', $documento->cliente->codVendedor);
            $stmt->bindValue(':observacion', $documento->comentario);
            $stmt->bindValue(':serie', $serieDocs);

            $stmt->bindValue(':secuencia', $numeroDOC);
            $stmt->bindValue(':montoIVA', $documento->IVA);
            $stmt->bindValue(':fechaEntrega', date('Ymd'));
            $stmt->execute();

            foreach ($documento->productos as $producto) {
                $query = "exec Sp_vengramov :OPCION, :OFI, :EJE, :TIPO, :NUMERO, :FECHA, :CLIENTE, :BODEGA, :TIPMOV, :ACTINV, :ACTPEN, :CODIGO, :UNIDAD, :CANTIDAD, :TIPOPRECIO, :PRECIO, :DESCU, :IVA, :PRECIOTOT, :CADUCIDA, :NOMBRE_EX, :CANDEV, :COSTO, :PRETRANS, :CENTRO, :ORDEN_P, :FLETE_NAVIERA, :FLETE_AGENTE, :CODVEN, :DESC2, :DESC3, :ITEMAGREGADO, :IDDOCINV, :ITEMESOBSEQUI, :CODREL, :CENTRO1, :CENTRO4, :TIPOIVA, :LOTE, :CODPROMO, :CODBONI";
                
                $stmt = $this->instancia->prepare($query);            
                $stmt->bindValue(':OPCION', 'I');
                $stmt->bindValue(':OFI', $datosEmpresa['Oficina']);
                $stmt->bindValue(':EJE', $datosEmpresa['Ejercicio']);
                $stmt->bindValue(':TIPO', $tipoDOC);
                $stmt->bindValue(':NUMERO', $numeroDOC);
                $stmt->bindValue(':FECHA', date('Ymd'));
                $stmt->bindValue(':CLIENTE', $documento->cliente->codigo);
                $stmt->bindValue(':BODEGA', $documento->bodega);
                $stmt->bindValue(':TIPMOV', 'S');
                $stmt->bindValue(':ACTINV', 0);
                $stmt->bindValue(':ACTPEN', 0);
                $stmt->bindValue(':CODIGO', $producto->codigo);
                $stmt->bindValue(':UNIDAD', $producto->unidad);
                $stmt->bindValue(':CANTIDAD', $producto->cantidad);
                $stmt->bindValue(':TIPOPRECIO', $documento->cliente->tipoPrecio);
                $stmt->bindValue(':PRECIO', $producto->precio);
                $stmt->bindValue(':DESCU', 0);
                $stmt->bindValue(':IVA', $producto->valorIVA);
                $stmt->bindValue(':PRECIOTOT', $producto->subtotal);
                $stmt->bindValue(':CADUCIDA', date('Ymd'));
                $stmt->bindValue(':NOMBRE_EX', '');
                $stmt->bindValue(':CANDEV', 0);
                $stmt->bindValue(':COSTO', 0);
                $stmt->bindValue(':PRETRANS', 0);
                $stmt->bindValue(':CENTRO', '');
                $stmt->bindValue(':ORDEN_P', '');
                $stmt->bindValue(':FLETE_NAVIERA', 0);
                $stmt->bindValue(':FLETE_AGENTE', 0);
                $stmt->bindValue(':CODVEN', '');
                $stmt->bindValue(':DESC2', 0);
                $stmt->bindValue(':DESC3', 0);
                $stmt->bindValue(':ITEMAGREGADO', 0);
                $stmt->bindValue(':IDDOCINV', '');
                $stmt->bindValue(':ITEMESOBSEQUI', 0);
                $stmt->bindValue(':CODREL', '');
                $stmt->bindValue(':CENTRO1', '');
                $stmt->bindValue(':CENTRO4', '');
                $stmt->bindValue(':TIPOIVA', $producto->tipoIVA);
                $stmt->bindValue(':LOTE', '');
                $stmt->bindValue(':CODPROMO', '');
                $stmt->bindValue(':CODBONI', '');
                $stmt->execute();
            }


                
            $commit = $this->instancia->commit();
            return array('status' => 'OK', 'commit' => $commit, 'message'=> "Se registro correctamente la cotización: #$new_cod_VENCAB");
            
        }catch(\PDOException $exception){
            $this->instancia->rollBack();
            http_response_code(400);
            return array('status' => 'ERROR', 'message' => $exception->getMessage() );
        }
   
    }

    public function SP_VENGRACAB_SHOPIFY (object $order) {

        try{
            $this->instancia->beginTransaction();

            // Verificamos si existe el codigo en VEN_CABSHOPIFY para evitar duplicados
            if ($this->SQL_getVEN_CABSHOPIFY($order->id)) {
                throw new \Exception("El ID $order->id para la orden $order->name de Shopify ya existe en la tabla VEN_CABSHOPIFY");
            }

            // Verificamos si existe la cedula/ruc creado, se guardo el RUC en el campo company del checkout Shopify
            if (!$this->SQL_getClienteBy($order->shipping_address->company)) {
                $rucShopy = $order->shipping_address->company;
                if ($this->validateCedulaRUC($rucShopy)) {
                    $newCliente = new \stdClass;
                    $newCliente->nombre = $order->shipping_address->name;
                    $newCliente->vendedor = 'SPY';
                    $newCliente->grupo = '6.01';
                    $newCliente->empresa =  $order->shipping_address->name;
                    $newCliente->RUC = $order->shipping_address->company;
                    $newCliente->direccion =  $order->shipping_address->address1;
                    $newCliente->telefono = $order->shipping_address->phone;
                    $newCliente->email = $order->customer->email;
                    $newCliente->tipoIdentificacion = 'C';
                    $newCliente->fecha = date('Ymd');

                    $responsecliente = $this->SQL_insertCliente($newCliente);
                    if ($responsecliente['status'] == 'ERROR') {
                        throw new \Exception('No se pudo crear el cliente. '. $responsecliente['message']);
                    }
                        
                }else{
                    throw new \Exception("El RUC/DNI $rucShopy no existe en COB_CLIENTES y no ha pasado el proceso de validación, registre el cliente manualmente.");
                }
            }
           

            $tipoDOC = 'SPY';
            //Obtenemos informacion de la empresa
            $datosEmpresa =  $this->getDatosEmpresa();
            if (isset($this->getVenTipos($tipoDOC)['Serie'])) {
                $serieDocs =  $this->getVenTipos($tipoDOC)['Serie'];
            }else{
                throw new \Exception("No se ha podido obtener el numero de serie de VEN_TIPOS");
            }

            //Obtenemos codigo del cliente, se utiliza company como  input del DNI en shopify
            if (isset($this->SQL_getClienteBy($order->shipping_address->company)['CODIGO'])) {
                $codCliente = $this->SQL_getClienteBy($order->shipping_address->company)['CODIGO'];
            }else{
                throw new \Exception("No se ha podido obtener el código del cliente");
            }

            //Creamos nuevo codigo de VEN_CAB (secuencial)
            $numeroDOC =  $this->SP_contador($tipoDOC, 'VEN'); 
            $new_cod_VENCAB = $datosEmpresa['Oficina'].$datosEmpresa['Ejercicio'].$tipoDOC.$numeroDOC;
                
            $query = "
                exec Sp_vengracab 'I ', :userID, :pcID, :oficina, :ejercicio, :tipoDOC, :numeroDOC,'', :fecha, :codCliente,
                :bodega,'DOL','1.00','0.00', :baseIVA,'0.00','0.00','0.00','0.00','0.00', :subtotal,'0.00', :impuesto,
                '0.00', :total, :formaPago,'0','0','0','S','0','1','0','0','','', :codVendedor,' ',' ', :observacion, :serie, 
                :secuencia,'','','','','0.00','0.00','0.00','','','','','','','','','','0','P','','','','','','0','','','','','0', :montoIVA,'0.00','0.00','0.00','0','1113431809','0','','','','','','','','','','  ', :fechaEntrega,''
            
            ";

            $stmt = $this->instancia->prepare($query); 
            $stmt->bindValue(':userID', $_SESSION["usuarioRUC".APP_UNIQUE_KEY]);
            $stmt->bindValue(':pcID', php_uname('n'));
            $stmt->bindValue(':oficina', $datosEmpresa['Oficina']);
            $stmt->bindValue(':ejercicio', $datosEmpresa['Ejercicio']);
            $stmt->bindValue(':tipoDOC', $tipoDOC);
            $stmt->bindValue(':numeroDOC', $numeroDOC);
            $stmt->bindValue(':fecha', date('Ymd'));
            $stmt->bindValue(':codCliente', $codCliente);

            $stmt->bindValue(':bodega', 'SPY');
            $stmt->bindValue(':baseIVA', $order->subtotal_price);
            $stmt->bindValue(':subtotal', $order->subtotal_price);
            $stmt->bindValue(':impuesto', $order->total_tax);

            $stmt->bindValue(':total', $order->total_price_usd);
            $stmt->bindValue(':formaPago', 'CON');
            $stmt->bindValue(':codVendedor', 'SPY');
            $stmt->bindValue(':observacion', $order->name); //Shopify guarda como name el #order
            $stmt->bindValue(':serie', $serieDocs);

            $stmt->bindValue(':secuencia', $numeroDOC);
            $stmt->bindValue(':montoIVA', $order->total_tax);
            $stmt->bindValue(':fechaEntrega', date('Ymd'));
            $stmt->execute();

            // INSERT EN VEN_CABSHOPIFY Y VEN_MOV_SHOPIFY
            $query = " 
            INSERT INTO VEN_CABSHOPIFY (ID_SHOPIFY, ID, PROCESADODATE, PROCESADOPOR, ESTADO_ORDEN, ESTADO_ITEMS, ESTADO_FINANCIERO, ANULADO, ESTADO) 
            VALUES (?, ?, ?, null, '', '', '', '0', '001')
            "; 
        
            $fecha_procesado = date('Ymd'); 

            $stmt = $this->instancia->prepare($query); 
            $stmt->bindParam(1, $order->id); 
            $stmt->bindParam(2, $new_cod_VENCAB); 
            $stmt->bindParam(3, $fecha_procesado); 
            $stmt->execute();

            // INSERT DE LOS ARTICULOS VEN_MOV

            foreach ($order->line_items as $producto) {

                //Obtenemos codigo del producto segun el ID de variante de shopify
                if (isset($this->SQL_getProductoShopiWFBy($producto->variant_id)['CODIGO_WF'])) {
                    $codProducto = $this->SQL_getProductoShopiWFBy($producto->variant_id);
                }else{
                    throw new \Exception("No se ha podido obtener el código de la variable del producto");
                }
               
                $query = "
                exec Sp_vengramov 'I', :oficina, :ejercicio ,:tipoDOC, :numeroDOC, :fecha, :codCliente, :bodega,'S','0','0',
                                    :codProducto, :unidad, :cantidad, :tipoPrecio, :precio, '0.0000', :porcentIVA, :precioTotal,
                                    :fechaCaducidad,'','0.00','0.0000000','0','','','0','0','','0','0','0','','0',:tipoIVA
                ";

                    $stmt = $this->instancia->prepare($query);
                    $stmt->bindValue(':oficina', $datosEmpresa['Oficina']);
                    $stmt->bindValue(':ejercicio', $datosEmpresa['Ejercicio']);
                    $stmt->bindValue(':tipoDOC', $tipoDOC);
                    $stmt->bindValue(':numeroDOC', $numeroDOC);
                    $stmt->bindValue(':fecha', date('Ymd'));
                    $stmt->bindValue(':codCliente', $codCliente);
                    $stmt->bindValue(':bodega', 'SPY');

                    $stmt->bindValue(':codProducto', $codProducto['CODIGO_WF']);
                    $stmt->bindValue(':unidad', 'UND');
                    $stmt->bindValue(':cantidad', $producto->quantity);
                    $stmt->bindValue(':tipoPrecio', 'A');
                    $stmt->bindValue(':precio', $producto->price / 1.12);
                    $stmt->bindValue(':porcentIVA', 12);
                    $stmt->bindValue(':precioTotal', $producto->price / 1.12);

                    $stmt->bindValue(':fechaCaducidad', date('Ymd'));
                    $stmt->bindValue(':tipoIVA', 'T12');
                   
                    $stmt->execute();

                    // INSERT EN VEN_MOV_SHOPIFY
                    $query = " 
                    INSERT INTO VEN_MOVSHOPIFY (ID, CODIGO, COLOR, TALLA, CODIGO_MASTER, CANTIDAD) 
                    VALUES (?, ?, ?, ?, ?,'1')
                    "; 
        
                    $stmt = $this->instancia->prepare($query); 
                    $stmt->bindParam(1, $new_cod_VENCAB); 
                    $stmt->bindParam(2, $codProducto['CODIGO_WF']); 
                    $stmt->bindParam(3, $codProducto['COLOR']); 
                    $stmt->bindParam(4, $codProducto['TALLA']); 
                    $stmt->bindParam(5, $codProducto['CODIGO_SHOPIFYMASTER']); 
                    $stmt->execute();
            }

            // Registro del TTR-01 envio tramaco
            foreach ($order->shipping_lines as $producto) {
                $query = "
                exec Sp_vengramov 'I', :oficina, :ejercicio ,:tipoDOC, :numeroDOC, :fecha, :codCliente, :bodega,'S','0','0',
                                    :codProducto, :unidad, :cantidad, :tipoPrecio, :precio, '0.0000', :porcentIVA, :precioTotal,
                                    :fechaCaducidad,'','0.00','0.0000000','0','','','0','0','','0','0','0','','0',:tipoIVA
                ";

                    $stmt = $this->instancia->prepare($query);
                    $stmt->bindValue(':oficina', $datosEmpresa['Oficina']);
                    $stmt->bindValue(':ejercicio', $datosEmpresa['Ejercicio']);
                    $stmt->bindValue(':tipoDOC', $tipoDOC);
                    $stmt->bindValue(':numeroDOC', $numeroDOC);
                    $stmt->bindValue(':fecha', date('Ymd'));
                    $stmt->bindValue(':codCliente', $codCliente);
                    $stmt->bindValue(':bodega', 'SPY');

                    $stmt->bindValue(':codProducto', 'TTR-01');
                    $stmt->bindValue(':unidad', 'UND');
                    $stmt->bindValue(':cantidad', 1);
                    $stmt->bindValue(':tipoPrecio', 'A');
                    $stmt->bindValue(':precio', $producto->price / 1.12);
                    $stmt->bindValue(':porcentIVA', 12);
                    $stmt->bindValue(':precioTotal', $producto->price / 1.12);

                    $stmt->bindValue(':fechaCaducidad', date('Ymd'));
                    $stmt->bindValue(':tipoIVA', 'T12');
                   
                $stmt->execute();
            }

            // INSERT SHOPIFY_COBROS
            $query = " 
            INSERT INTO SHOPIFY_COBROS VALUES ( ?, ?, ?, ?, ?, ?, ?, 
                ?, ?, ?, ?, ?, ?, ?, ?, 
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 
            ?, ?, ?, ?) 
            "; 
        
            $stmt = $this->instancia->prepare($query); 
            $stmt->bindValue(1, $order->id); 
            $stmt->bindValue(2, $order->checkout_id);  //No definido es automatico?
            $stmt->bindValue(3, 0); 
            $stmt->bindValue(4, date('Ymd H:i:s')); 
            $stmt->bindValue(5, ''); 
            $stmt->bindValue(6, 1); 
            $stmt->bindValue(7, '');

            $stmt->bindValue(8, date('Ymd H:i:s'));
            $stmt->bindValue(9, '');
            $stmt->bindValue(10, $order->shipping_address->country_code);
            $stmt->bindValue(11, '');
            $stmt->bindValue(12, $order->shipping_address->company); //Se definio company como campo en shopify para el registro del UC
            $stmt->bindValue(13, '');
            $stmt->bindValue(14, '');
            $stmt->bindValue(15, '');
           
            $stmt->bindValue(16, 0);
            $stmt->bindValue(17, $order->shipping_address->last_name);
            $stmt->bindValue(18, $order->total_tax);
            $stmt->bindValue(19, $order->total_price_usd);
            $stmt->bindValue(20, '');
            $stmt->bindValue(21, date('Ymd H:i:s'));
            $stmt->bindValue(22, '');
            $stmt->bindValue(23, 'PAGO_ACEPTADO');
            $stmt->bindValue(24, '');
            $stmt->bindValue(25, 0);
            $stmt->bindValue(26, 0);
            $stmt->bindValue(27, 0);
            $stmt->bindValue(28, 'USD');
            $stmt->bindValue(29, '');
            $stmt->bindValue(30, '');

            $stmt->bindValue(31, '');
            $stmt->bindValue(32, '');
            $stmt->bindValue(33, '');
            $stmt->bindValue(34, $order->checkout_id); // Id creado con la fecha+?
            $stmt->bindValue(35, '');
            $stmt->bindValue(36, $order->shipping_address->phone);
            $stmt->bindValue(37, 0);
            $stmt->bindValue(38, '');
            $stmt->bindValue(39, 0);
            $stmt->bindValue(40, '');
            $stmt->bindValue(41, $order->shipping_address->first_name);
            $stmt->bindValue(42, '');
            $stmt->bindValue(43, '');
            $stmt->bindValue(44, '');
            
            $stmt->bindValue(45, $order->total_price_usd); // Precios en shopify incluyen iva
            $stmt->bindValue(46, $order->customer->email);
            $stmt->bindValue(47, $order->shipping_address->address1);
            $stmt->bindValue(48, $order->shipping_address->city);

            $stmt->execute();


            // INSERT SHOPIFY_DATAFAST
            $query = " 
            INSERT INTO SHOPIFY_DATAFAST (idShopify, 
                        id, 
                        paymentType, 
                        paymentBrand, 
                        cardBin, 
                        cardBinCounty, 
                        cardLast4Digits,
                        cardHolder) 
            VALUES ( ?, ? , '', '', '', '', '','')
            "; 
           
            $stmt = $this->instancia->prepare($query); 
            $stmt->bindParam(1, $order->id);
            $stmt->bindParam(2, $order->id);

            $stmt->execute();
            
            $commit = $this->instancia->commit();
            return array('status' => 'OK', 'commit' => $commit, 'message'=> "Se registro correctamente el documento: #$new_cod_VENCAB");
            
        }catch(\PDOException $exception){
            $this->instancia->rollBack();
            http_response_code(400);
            return array('status' => 'ERROR', 'message' => $exception->getMessage() );
        }
   
    }

    public function SP_VENGRACAB_ventasPorMayor (object $documento) {
        try{
            $this->instancia->beginTransaction();

            $tipoDOC = $documento->tipoDOC;
            //Obtenemos informacion de la empresa
            $datosEmpresa =  $this->getDatosEmpresa();
            $serieDocs =  $this->getVenTipos($tipoDOC)['Serie'];
        
            //Creamos nuevo codigo de VEN_CAB (secuencial)
            $numeroDOC =  $this->SP_contador($tipoDOC, 'VEN'); 
            
            $new_cod_VENCAB = $datosEmpresa['Oficina'].$datosEmpresa['Ejercicio'].$tipoDOC.$numeroDOC;
           
            $query = "
                exec Sp_vengracab 'I ', :userID, :pcID, :oficina, :ejercicio, :tipoDOC, :numeroDOC,'', :fecha, :codCliente,
                :bodega,'DOL','1.00','0.00', :baseIVA,'0.00','0.00','0.00','0.00','0.00', :subtotal,'0.00', :impuesto,
                '0.00', :total, :formaPago, :diasPago, :numPagos, :entrePagos,'S','0','1','0','0','','', :codVendedor,' ',' ', :observacion, :serie,
                :secuencia,'','','','','0.00','0.00','0.00','','','','','','','','','','0','P','','','','','','0','','','','','0', :montoIVA,'0.00','0.00','0.00','0','1112764636','0','','','','','','','','','','', :fechaEntrega,''
            
                ";

            $stmt = $this->instancia->prepare($query); 
            $stmt->bindValue(':userID', $_SESSION["usuarioRUC".APP_UNIQUE_KEY]);
            $stmt->bindValue(':pcID', php_uname('n'));
            $stmt->bindValue(':oficina', $datosEmpresa['Oficina']);
            $stmt->bindValue(':ejercicio', $datosEmpresa['Ejercicio']);
            $stmt->bindValue(':tipoDOC', $tipoDOC);
            $stmt->bindValue(':numeroDOC', $numeroDOC);
            $stmt->bindValue(':fecha', date('Ymd'));
            $stmt->bindValue(':codCliente', $documento->cliente->codigo);

            $stmt->bindValue(':bodega', $documento->bodega);
            $stmt->bindValue(':baseIVA', $documento->subtotal);
            $stmt->bindValue(':subtotal', $documento->subtotal);
            $stmt->bindValue(':impuesto', $documento->IVA);

            $stmt->bindValue(':total', $documento->total);
            $stmt->bindValue(':formaPago', $documento->cliente->formaPago);
            $stmt->bindValue(':diasPago', $documento->cliente->diasPago);
            $stmt->bindValue(':numPagos', $documento->cliente->numPagos);
            $stmt->bindValue(':entrePagos', $documento->cliente->entrePagos);
            $stmt->bindValue(':codVendedor', $documento->cliente->codVendedor);
            $stmt->bindValue(':observacion', $documento->comentario);
            $stmt->bindValue(':serie', $serieDocs);

            $stmt->bindValue(':secuencia', $numeroDOC);
            $stmt->bindValue(':montoIVA', $documento->IVA);
            $stmt->bindValue(':fechaEntrega', date('Ymd'));
            $stmt->execute();

            foreach ($documento->productos as $producto) {
               
                $query = "
                exec Sp_vengramov 'I', :oficina, :ejercicio ,:tipoDOC, :numeroDOC, :fecha, :codCliente, :bodega,'S','0','0',
                                    :codProducto, :unidad, :cantidad, :tipoPrecio, :precio, :porcentDescuento, :porcentIVA, :precioTotal,
                                    :fechaCaducidad,'','0.00','0.0000000','0','','','0','0','','0','0','0','','0',:tipoIVA

                ";

                    $stmt = $this->instancia->prepare($query);
                    $stmt->bindValue(':oficina', $datosEmpresa['Oficina']);
                    $stmt->bindValue(':ejercicio', $datosEmpresa['Ejercicio']);
                    $stmt->bindValue(':tipoDOC', $tipoDOC);
                    $stmt->bindValue(':numeroDOC', $numeroDOC);
                    $stmt->bindValue(':fecha', date('Ymd'));
                    $stmt->bindValue(':codCliente', $documento->cliente->codigo);
                    $stmt->bindValue(':bodega', $documento->bodega);

                    $stmt->bindValue(':codProducto', $producto->codigo);
                    $stmt->bindValue(':unidad', $producto->unidad);
                    $stmt->bindValue(':cantidad', $producto->cantidad);
                    $stmt->bindValue(':tipoPrecio', $documento->cliente->tipoPrecio);
                    $stmt->bindValue(':precio', $producto->precio);
                    $stmt->bindValue(':porcentDescuento', $producto->porcentDescuento);
                    $stmt->bindValue(':porcentIVA', $producto->valorIVA);
                    $stmt->bindValue(':precioTotal', $producto->subtotal);

                    $stmt->bindValue(':fechaCaducidad', date('Ymd'));
                    $stmt->bindValue(':tipoIVA', $producto->tipoIVA);
                   
                $stmt->execute();
            }


                
            $commit = $this->instancia->commit();
            return array('status' => 'OK', 'commit' => $commit, 'message'=> "Se registro correctamente la cotización: #$new_cod_VENCAB");
            
        }catch(\PDOException $exception){
            $this->instancia->rollBack();
            http_response_code(400);
            return array('status' => 'ERROR', 'message' => $exception->getMessage() );
        }
   
    }

    public function SP_VENGRAMOV ($VEN_MOV_obj){
        
        $VEN_MOV = new VenMovClass();
        $VEN_MOV = $VEN_MOV_obj;

        $query = "
        
        exec dbo.SP_VENGRAMOV 'I','$VEN_MOV->oficina','$VEN_MOV->ejercicio','$VEN_MOV->tipoDoc','$VEN_MOV->numeroDoc','$VEN_MOV->fecha','$VEN_MOV->cliente','$VEN_MOV->bodega','S','0','0','$VEN_MOV->codProducto','UND','$VEN_MOV->cantidad','$VEN_MOV->tipoPrecio','$VEN_MOV->precioProducto','$VEN_MOV->porcentajeDescuentoProd','$VEN_MOV->porcentajeIVA','$VEN_MOV->precioTOTAL','$VEN_MOV->fecha','','0.00','0.0000000','0','1.01.11','','1','1','$VEN_MOV->vendedor','0.0000','0.0000','0','','0','$VEN_MOV->tipoIVA' 
        
        ";

        $stmt = $this->instancia->prepare($query); 
       
        try{
            $rowsAfected = $this->instancia->execute($query);
           return array('status' => 'ok', 'message' => $rowsAfected. ' fila afectada(s)' ); //true;
           
        }catch(\PDOException $exception){
            return array('status' => 'ERROR', 'message' => $exception->getMessage() );
        }


    }

    public function SP_VENINFCOMADF (object $busqueda) {
        try{
            $this->instancia->beginTransaction();

            $query = "
                exec SP_VENINFCOMADF :fecha, :codVendedor
            ";

            $stmt = $this->instancia->prepare($query); 
            $stmt->bindValue(':fecha', date("Ymd", strtotime($busqueda->fecha)));
            $stmt->bindValue(':codVendedor', $busqueda->vendedor);
           
            $stmt->execute();
            if($stmt->execute()){
                $resulset = $stmt->fetchAll( \PDO::FETCH_ASSOC );
            }else{
                $resulset = false;
            }
  
            $this->instancia->commit();
            return $resulset;
            
        }catch(\PDOException $exception){
            $this->instancia->rollBack();
            http_response_code(400);
            return array('status' => 'ERROR', 'message' => $exception->getMessage() );
        }
   
    }

    public function SQL_getBodegaDefault(string $usuario, $dataBaseName){
        $this->setDbname($dataBaseName);
        $this->conectarDB();
        
        $query = " 
            SELECT * From dbo.INV_Bodegas WITH(NOLOCK) WHERE 
            (CASE WHEN NOT EXISTS(SELECT DATO FROM XASIGDATOUSER  X WHERE USERCOD = :usuario  AND FILTRO = 'BOD') THEN 1 
            ELSE (CASE WHEN EXISTS(SELECT DATO FROM XASIGDATOUSER X WHERE X.DATO = INV_BODEGAS.CODIGO AND USERCOD = :usuario_1 AND FILTRO = 'BOD') 
            THEN 1 ELSE 0 END)END) = 1 AND ISNULL(NOVENTA,0)=0 AND ISNULL(NOFAC,0)=0 ORDER BY CODIGO
        ";  
        try{
            $stmt = $this->instancia->prepare($query); 
            $stmt->bindParam(':usuario', $usuario);
            $stmt->bindParam(':usuario_1', $usuario);
    
                if($stmt->execute()){
                    $resulset = $stmt->fetch( \PDO::FETCH_ASSOC );
                    
                }else{
                    $resulset = false;
                }
            return $resulset;  

        }catch(\PDOException $exception){
            return array('status' => 'error', 'message' => $exception->getMessage() );
        }
    }

    public function SQL_getTiposDOC_TransaccionesVenta(string $usuario){
       
        $query = " 
            SELECT * From dbo.VEN_TIPOS WITH(NOLOCK) 
            WHERE 
                (CASE WHEN NOT EXISTS(
                    SELECT DATO FROM XASIGDATOUSER X WITH(NOLOCK) WHERE USERCOD = :usuario AND FILTRO = 'TIP' AND GESTION='VEN') 
                THEN 1 ELSE (CASE WHEN EXISTS(SELECT DATO FROM XASIGDATOUSER X WITH(NOLOCK) 
                    WHERE X.DATO = VEN_TIPOS.CODIGO AND USERCOD = :usuario_1  AND FILTRO = 'TIP' AND GESTION='VEN') 
                    THEN 1 ELSE 0 END)END) = 1 AND NOT tipodoc='C' ORDER BY nombre
        ";  
        try{
            $stmt = $this->instancia->prepare($query); 
            $stmt->bindParam(':usuario', $usuario);
            $stmt->bindParam(':usuario_1', $usuario);
    
                if($stmt->execute()){
                    $resulset = $stmt->fetchAll( \PDO::FETCH_ASSOC );
                    
                }else{
                    $resulset = false;
                }
            return $resulset;  

        }catch(\PDOException $exception){
            return array('status' => 'error', 'message' => $exception->getMessage() );
        }
    }

    public function SQL_getBodegas_TransaccionesVenta(string $usuario){
       
        $query = " 
            SELECT * From dbo.INV_Bodegas WITH(NOLOCK) 
            Where (CASE WHEN NOT EXISTS(
                SELECT DATO FROM XASIGDATOUSER  X WITH(NOLOCK) WHERE USERCOD = :usuario  AND FILTRO = 'BOD')
            THEN 1 ELSE (CASE WHEN EXISTS(SELECT DATO FROM XASIGDATOUSER X WITH(NOLOCK) WHERE X.DATO = INV_BODEGAS.CODIGO AND USERCOD = :usuario_1 AND FILTRO = 'BOD') 
            THEN 1 ELSE 0 END)END) = 1 AND ISNULL(NOVENTA,0)=0 AND ISNULL(NOFAC,0)=0 ORDER BY CODIGO
        ";  
        try{
            $stmt = $this->instancia->prepare($query); 
            $stmt->bindParam(':usuario', $usuario);
            $stmt->bindParam(':usuario_1', $usuario);
    
                if($stmt->execute()){
                    $resulset = $stmt->fetchAll( \PDO::FETCH_ASSOC );
                    
                }else{
                    $resulset = false;
                }
            return $resulset;  

        }catch(\PDOException $exception){
            return array('status' => 'error', 'message' => $exception->getMessage() );
        }
    }

    public function SP_DIMESALDOCLI2(string $RUC){
      
        $query = "Select DBO.DIMESALDOCLI2(:RUC,'','') AS Saldo";
        $stmt = $this->instancia->prepare($query);
        $stmt->bindValue(':RUC', $RUC); 
      
            if($stmt->execute()){
                $resulset = $stmt->fetch( \PDO::FETCH_ASSOC );
            }else{
                $resulset = false;
            }
        return $resulset;  
    }

    public function SQL_getDocsPendientes(string $codigo){
       
        $query = " 
        SELECT 
            COUNT(DPen.numero) as Cantidad 
        FROM 
            (Select numero,VALOR,ABONO=Dbo.DimeAbonosDoc(ID+RECIBO,CLIENTE) 
                FROM COB_CAB with (NOLOCK) 
                WHERE cliente = :cliente and tipmov ='D' 
                AND fecha < GETDATE() and ISNULL(anulado,0) = 0) Dpen 
        WHERE (DPen.Valor-DPen.Abono)> 0
        ";  
        try{
            $stmt = $this->instancia->prepare($query); 
            $stmt->bindParam(':cliente', $codigo);
          
                if($stmt->execute()){
                    $resulset = $stmt->fetch( \PDO::FETCH_ASSOC );
                    
                }else{
                    $resulset = false;
                }
            return $resulset;  

        }catch(\PDOException $exception){
            return array('status' => 'error', 'message' => $exception->getMessage() );
        }
    }

    public function SQL_getUSUGRUPOS(){
       
        $query = " 
            SELECT Codigo, Nombre FROM USUGRUPOS WITH(NOLOCK)
                UNION
            SELECT Codigo, Nombre FROM USUARIOS WITH(NOLOCK) WHERE Estatus = '1'
        ";  
        try{
            $stmt = $this->instancia->prepare($query); 
          
                if($stmt->execute()){
                    $resulset = $stmt->fetchAll( \PDO::FETCH_ASSOC );
                }else{
                    $resulset = false;
                }
            return $resulset;  

        }catch(\PDOException $exception){
            return array('status' => 'error', 'message' => $exception->getMessage() );
        }
    }

    public function SQL_saveOrdenCompraSuministros_Aprobacion (object $documento) {
        try{
            $this->instancia->beginTransaction();

            foreach ($documento->productosByProveedor as $producto) { 
                $stmt = $this->instancia->query("SELECT 'PED'+RIGHT('000000'+ISNULL(CONVERT (Varchar , (SELECT COUNT(*)+1 FROM KAO_wssp.dbo.CAB_OrdenCompraSuministros)),''),6) as codigo");
                $stmt->execute();
                $newCod = $stmt->fetch()[0];
                

                $query = "
                    INSERT INTO KAO_wssp.dbo.CAB_OrdenCompraSuministros VALUES 
                    (:codigo, :empresa, :userID, :fecha, :codProveedor, :subtotal, :IVA, :total, 0)
                ";

                $stmt = $this->instancia->prepare($query); 
                $stmt->bindValue(':codigo', $newCod);
                $stmt->bindValue(':empresa', $_SESSION["empresaAUTH".APP_UNIQUE_KEY]);
                $stmt->bindValue(':userID', $_SESSION["usuarioRUC".APP_UNIQUE_KEY]);
                $stmt->bindValue(':fecha', date('Ymd H:i:s'));
                $stmt->bindValue(':codProveedor', trim($producto->title)); // El codigo de proveedor esta como title de la categoria agrupada
                $stmt->bindValue(':subtotal', $producto->subtotal);
                $stmt->bindValue(':IVA', $producto->IVA);
                $stmt->bindValue(':total', $producto->total);
                $stmt->execute();

                foreach ($producto->items as $item) {
                
                    $query = "
                        INSERT INTO KAO_wssp.dbo.MOV_OrdenCompraSuministros VALUES (:CAB_id, :codProveedor, :fechaPedido, :codigo, :bodega, :cantidad, :precio, :IVA, :subtotal)
                    ";

                        $stmt = $this->instancia->prepare($query);
                        
                        $stmt->bindValue(':CAB_id', $newCod);
                        $stmt->bindValue(':codProveedor', trim($producto->title)); // El codigo de proveedor esta como title de la categoria agrupada
                        $stmt->bindValue(':fechaPedido', $item->fechaPedido.'-01');

                        $stmt->bindValue(':codigo', $item->codigo);
                        $stmt->bindValue(':bodega', $item->bodega);
                     
                        $stmt->bindValue(':cantidad', $item->cantidad);
                        $stmt->bindValue(':precio', $item->precio);
                        $stmt->bindValue(':IVA', $item->IVA);
                        $stmt->bindValue(':subtotal', $item->subtotal);

                    
                    $stmt->execute();
                }
            }
             
            $commit = $this->instancia->commit();
            return array('status' => 'OK', 'commit' => $commit, 'message'=> "Se registro correctamente la solicitud de ordenes de compra, espere su aprobacion por Administracion");
            
        }catch(\PDOException $exception){
            $this->instancia->rollBack();
            http_response_code(400);
            return array('status' => 'ERROR', 'message' => $exception->getMessage() );
        }
   
    }

    public function SP_COMGRACAB (object $documento) {
        try{
            $this->instancia->beginTransaction();

            $tipoDOC = $documento->tipoDOC;
            //Obtenemos informacion de la empresa
            $datosEmpresa =  $this->getDatosEmpresa();
          
            foreach ($documento->productosByProveedor as $producto) {
                $numeroDOC =  $this->SP_contador($tipoDOC, 'COM'); 
                            
                            
                $query = "
                    exec SP_COMGRACAB 'I', :userID , :pcID, :oficina, :ejercicio, :tipoDOC, :numeroSecuencial, :fecha, :codProveedor, :bodega,'DOL','1.00','CON','60',
                    '0','0','E','0','0','0','','', :fechaPrometida, :subtotal,'0.00', :impuesto,'0.00', :total,'0.00','','','','','','1.00','0.00','0.00','0.00','0.00',
                    '0.00','0.00','0.00','0.00','01','','','','','','01', :fechaActual,'0','0.00','N','0.12','0.00','0.00','', :hora,'','CCT', :fechaActual2,'','','','','',
                    '0','0','0','','','','','0','','','','','','','','0','0','','','','','','0.00','1.00','0.00','0.00','0.00','0.00'

                ";

                $stmt = $this->instancia->prepare($query); 
                $stmt->bindValue(':userID', $_SESSION["usuarioRUC".APP_UNIQUE_KEY]);
                $stmt->bindValue(':pcID', php_uname('n'));
                $stmt->bindValue(':oficina', $datosEmpresa['Oficina']);
                $stmt->bindValue(':ejercicio', $datosEmpresa['Ejercicio']);
                $stmt->bindValue(':tipoDOC', $tipoDOC);
                $stmt->bindValue(':numeroSecuencial', $numeroDOC);
                $stmt->bindValue(':fecha', date('Ymd'));
                $stmt->bindValue(':codProveedor', trim($producto->codProveedor)); // El codigo de proveedor esta como title de la categoria agrupada
                $stmt->bindValue(':bodega', $_SESSION["bodegaDefault".APP_UNIQUE_KEY]);

                $fechaPrometida = date('Y-m-d');
                $stmt->bindValue(':fechaPrometida', date('Ymd', strtotime($fechaPrometida. ' + 60 days')));
                $stmt->bindValue(':subtotal', $producto->subtotal);
                $stmt->bindValue(':impuesto', $producto->IVA);
                $stmt->bindValue(':total', $producto->total);

                $stmt->bindValue(':fechaActual', date('Ymd H:i:s'));
                $stmt->bindValue(':hora', date('H:i:s'));
                $stmt->bindValue(':fechaActual2', date('Ymd H:i:s'));

                $stmt->execute();

                foreach ($producto->items as $item) {
                
                    $query = "
                        exec SP_COMGRAMOV 'I', :oficina, :ejercicio, :tipoDOC, :numeroDOC, :fecha, :codProveedor, :bodega,'E','0',
                        :codProducto, :unidad, :cantidad,'0.00', :costo,'0.0000000', :costoTotal, :porcentIVA,'', :fechaCaducidad,'0',
                        '1', :valIVA,'0.0000','0.0000','0','0','0','','0','0','0','0','0', :precioANT,'0.00','0.00','0.00','0.00',
                        '0.00','0.00','','','','0.00','0','0','','0','','0','0','0','0','','0','0','0','0','','','','0.000000','1',
                        '0','','','','T12'

                    ";

                        $stmt = $this->instancia->prepare($query);
                        $stmt->bindValue(':oficina', $datosEmpresa['Oficina']);
                        $stmt->bindValue(':ejercicio', $datosEmpresa['Ejercicio']);
                        $stmt->bindValue(':tipoDOC', $tipoDOC);
                        $stmt->bindValue(':numeroDOC', $numeroDOC);
                        $stmt->bindValue(':fecha', date('Ymd'));
                        $stmt->bindValue(':codProveedor', trim($producto->codProveedor)); // El codigo de proveedor esta como title de la categoria agrupada
                        $stmt->bindValue(':bodega', $_SESSION["bodegaDefault".APP_UNIQUE_KEY]);

                        $stmt->bindValue(':codProducto', $item->codigo);
                        $stmt->bindValue(':unidad', $item->unidad);
                        $stmt->bindValue(':cantidad', $item->cantidad);
                        $stmt->bindValue(':costo', $item->precio);
                        $stmt->bindValue(':costoTotal', $item->subtotal);
                        $stmt->bindValue(':porcentIVA', $item->valorIVA);

                        $stmt->bindValue(':fechaCaducidad', date('Ymd'));
                        $stmt->bindValue(':valIVA', $item->IVA);
                        $stmt->bindValue(':precioANT', $item->precio);
                    
                    $stmt->execute();
                }

                $query = "
                UPDATE KAO_wssp.dbo.CAB_OrdenCompraSuministros SET estado = 1 WHERE id = :id
                ";
    
                $stmt = $this->instancia->prepare($query); 
                $stmt->bindValue(':id', $producto->id);
                $stmt->execute();
    

            }
            
           
                
            $commit = $this->instancia->commit();
            return array('status' => 'OK', 'commit' => $commit, 'message'=> "Se registro correctamente las ordenes de compra # $numeroDOC");
            
        }catch(\PDOException $exception){
            $this->instancia->rollBack();
            http_response_code(400);
            return array('status' => 'ERROR', 'message' => $exception->getMessage() );
        }
   
    }


    public function SQL_getClienteBy($value, $column='RUC') {
        $query = " 
            SELECT * FROM dbo.COB_CLIENTES WITH(NOLOCK) WHERE $column='$value'
        ";  // Final del Query SQL 

        try{
            $stmt = $this->instancia->prepare($query); 
    
                if($stmt->execute()){
                    $resulset = $stmt->fetch( \PDO::FETCH_ASSOC );
                    
                }else{
                    $resulset = false;
                }
            return $resulset;  

        }catch(\PDOException $exception){
            return array('status' => 'error', 'message' => $exception->getMessage() );
        }
   
    }

    public function SQL_insertCliente(object $cliente) {
        
        try{
        $stmt = $this->instancia->prepare("Sp_Contador'CLI','99','','',''"); 
        $stmt->execute();
        $stmt->nextRowset();
        
        $newCodLimpio = $stmt->fetch(\PDO::FETCH_ASSOC);
        $newCodLimpio =  $newCodLimpio['NExtID'];
        
        $newCod = $this->instancia->query("select RIGHT('00000000' + Ltrim(Rtrim('$newCodLimpio')),8) as newcod");
        $newcodigo = $newCod->fetch(\PDO::FETCH_ASSOC);
        $newcodigo = $newcodigo['newcod'];

        $query = "
            INSERT INTO COB_CLIENTES
                (OFICINA,CODIGO,NOMBRE,VENDEDOR,CODVENOLD,GRUPO,CONTACTO,EMPRESA,CUENTA,CUENTA2,ESVARIOS,RUC,DIRECCION1,DIRECCION2,TELEFONO1,TELEFONO2,TELEFONO3,CODPOS,FAX,FAXPED,EMAIL,EMAIL2,PAGWEB,DIVISA,IDIOMA,NOTA,FPAGO,DIASPAGO,TIPOPRECIO,PORDES,TIPOCLI,LIMITECRED,DIRENV,DIRENV1,TELENV,GNOMBRE,GCEDULA,GEMPRESA,GDIRECCION,GTELEFONO1,GTELEFONO2,CELULAR,GNOTA,PWD,WEBDERECHO,WEBAVISO,WEBNOTA,LIBRE,CURSO,ESTUDIA,SALDO,ULTIMAVENTA,ULTIMOCOBRO,ESTADO,CREADOPOR,EDITADOPOR,ANULADOPOR,CREADODATE,EDITADODATE,ANULADODATE,PCID,CLASE,NEGOCIO,PAIS,PROVINCIA,CANTON,REQ_ANTICIPO,TIPOIDENT,PROMOCION,CODCOMRELA,DIVISION,ACTIVIDAD1,ACTIVIDAD2,REPRESENTA,CEDREPRESENTA,RECAUDADOR,CONDICION,NUMPAG,ENTREPAG,TIPOPAGO,CONDPAGO,PROVCLIENTE,FECEXPIRAN,CONTACTO1,MAILCON1,EXTCON1,CELCON1,CONTACTO2,MAILCON2,EXTCON2,CELCON2,CONTACTO3,MAILCON3,EXTCON3,CELCON3,SOBRECUPO,DIASGRACIA,DIADEPAGO,HORADEPAGO,GARANTIAS,CODTARJETA,EMITARJETA,VENTARJETA,PORTARJETA,PORTARJETAEFE,PORTARJETACHE,PORTARJETATAR,PORANTICIPO,CONTACTOPAGO,FECHAALTA,WEBENVEMAIL,PARTEREL,TIPCLI) 
            VALUES('99','$newcodigo','$cliente->nombre','$cliente->vendedor','','$cliente->grupo','','$cliente->nombre','','','','$cliente->RUC','$cliente->direccion','','$cliente->telefono','','','','','','$cliente->email','','','DOL','ESP','','CON','0','A',0.00,'',0.00,'','','','','','','','','','','','',0.00,'','','',0.00,'0',0.00,'        ','        ','1','','','','        ','        ','        ','','','','593','217','21701','0','$cliente->tipoIdentificacion','0','','','','','','','$cliente->vendedor','0',0.00,0.00,'','','','        ','','','','','','','','','','','','',0.00,0.00,'','','','','        ','        ',0.00,0.00,0.00,0.00,0.00,'','$cliente->fecha','0','NO','01')
        ";

            
            if($rowsAfected = $this->instancia->exec($query)){
                $resulset = array('status' => 'OK', 'newCodigo' => $newcodigo, 'message' => $rowsAfected. ' fila afectada(s)' ); //true;
            }else{
                throw new \Exception("El RUC/DNI $cliente->RUC no se pudo registrar. Registre el cliente manualmente y reintente crear el documento.");
            }
            return $resulset; 
            
        }catch(\PDOException $exception){
            return array('status' => 'ERROR', 'message' => $exception->getMessage() );
        }
    }

    /* Tabla exclusiva para productos de Winfenix/Shopify contiene codigo master*/
    public function SQL_getProductoShopiWFBy($value, $column='CODIGO_SHOPIFY') {
        $query = " 
            SELECT * FROM INV_ARTICULOS_SHOPIFY_WF WITH(NOLOCK) WHERE $column='$value'
        ";  // Final del Query SQL 

        try{
            $stmt = $this->instancia->prepare($query); 
    
                if($stmt->execute()){
                    $resulset = $stmt->fetch( \PDO::FETCH_ASSOC );
                    
                }else{
                    $resulset = false;
                }
            return $resulset;  

        }catch(\PDOException $exception){
            return array('status' => 'error', 'message' => $exception->getMessage() );
        }
   
    }

    public function SQL_getVEN_CABSHOPIFY($id_shopify) {
        $query = " 
            SELECT TOP 1  * FROM dbo.VEN_CABSHOPIFY WITH(NOLOCK) WHERE ID_SHOPIFY='$id_shopify'
        ";  // Final del Query SQL 

        try{
            $stmt = $this->instancia->prepare($query); 
    
                if($stmt->execute()){
                    $resulset = $stmt->fetch( \PDO::FETCH_ASSOC );
                    
                }else{
                    $resulset = false;
                }
            return $resulset;  

        }catch(\PDOException $exception){
            return array('status' => 'error', 'message' => $exception->getMessage() );
        }
   
    }

    public function validateCedulaRUC($dni) {
        $validador = new ValidadorEc;
        if ($validador->validarCedula($dni)) {
            return true;
        }

        // validar RUC persona natural
        if ($validador->validarRucPersonaNatural($dni)) {
            return true;
        }
        
        // validar RUC sociedad privada
        if ($validador->validarRucSociedadPrivada($dni)) {
            return true;
        }
        
        // validar RUC sociedad pública
        if ($validador->validarRucSociedadPublica($dni)) {
            return true;
        }

        return false;
        
    }

    public function saveValePerdida(Object $documento) {
        try{
            $this->instancia->beginTransaction();

            $tipoDOC = $documento->tipoDOC;
            //Obtenemos informacion de la empresa
            $datosEmpresa =  $this->getDatosEmpresa();
            if (isset($this->getVenTipos($tipoDOC)['Serie'])) {
                $serieDocs =  $this->getVenTipos($tipoDOC)['Serie'];
            }else{
                throw new \Exception("No se ha podido obtener el numero de serie de VEN_TIPOS");
            }
           
            //Creamos nuevo codigo de VEN_CAB (secuencial)
            if ($this->SP_contador($tipoDOC, 'VEN')) {
                $numeroDOC =  $this->SP_contador($tipoDOC, 'VEN'); 
            }else{
                throw new \Exception("No se ha podido obtener el contador del tipo de documento");
            }
            
            $new_cod_VENCAB = $datosEmpresa['Oficina'].$datosEmpresa['Ejercicio'].$tipoDOC.$numeroDOC;
            

            /* INSERT EN KAO_WSSP */

            $query = "
                INSERT INTO 
                    KAO_wssp.dbo.vales_perdida 
                VALUES ( :new_cod_VENCAB, :tipoDOC, :fecha, :solicitante, :supervisor, :empresa, :bodega, :cuotasPagos,
                        :fechaPagos, :subtotal, :descuento, :iva, :total, :estado, :observacion)
            ";

            $stmt = $this->instancia->prepare($query); 
                $stmt->bindValue(':new_cod_VENCAB', $new_cod_VENCAB);
                $stmt->bindValue(':tipoDOC', $tipoDOC);
                $stmt->bindValue(':fecha', date('Ymd'));
                $stmt->bindValue(':solicitante', $documento->solicitante);
                $stmt->bindValue(':supervisor', $documento->tipoDOC);
                $stmt->bindValue(':empresa', $_SESSION["empresaAUTH".APP_UNIQUE_KEY]);
                $stmt->bindValue(':bodega', $documento->bodega);
                $stmt->bindValue(':cuotasPagos', $documento->cuotasPagos);
                $stmt->bindValue(':fechaPagos', $documento->fechaPagos);
                $stmt->bindValue(':subtotal', $documento->subtotalSinDescuento); // No graban IVA asi es el subtotal es igual al total
                $stmt->bindValue(':descuento', $documento->descuento);
                $stmt->bindValue(':iva', $documento->iva);
                $stmt->bindValue(':total', $documento->total);
                $stmt->bindValue(':estado', 0);
                $stmt->bindValue(':observacion', $documento->observacion);
            $stmt->execute();

            foreach ($documento->productos as $producto) {
                $query = "INSERT INTO KAO_wssp.dbo.detalle_valep 
                            VALUES ( :new_cod_VENCAB, :empresa, :codigo, :nombre, :cantidad, :precio, :descuento, :precioTotal)";
                
                $stmt = $this->instancia->prepare($query);            
                $stmt->bindValue(':new_cod_VENCAB', $new_cod_VENCAB);
                $stmt->bindValue(':empresa', $_SESSION["empresaAUTH".APP_UNIQUE_KEY]);
                $stmt->bindValue(':codigo', $producto->codigo);
                $stmt->bindValue(':nombre', $producto->nombre);
                $stmt->bindValue(':cantidad', $producto->cantidad);
                $stmt->bindValue(':precio', $producto->precio);
                $stmt->bindValue(':descuento', $producto->descuento);
                $stmt->bindValue(':precioTotal', $producto->precioTotal);
                $stmt->execute();
            }

            foreach ($documento->empleados as $empleado) {
                $query = "INSERT INTO 
                            KAO_wssp.dbo.recargo_valep 
                        VALUES (:new_cod_VENCAB, :empresa, :empleado, :codigoWinfenix, :porcentaje, :recargo)";
                
                $stmt = $this->instancia->prepare($query);            
                $stmt->bindValue(':new_cod_VENCAB', $new_cod_VENCAB);
                $stmt->bindValue(':empresa', $_SESSION["empresaAUTH".APP_UNIQUE_KEY]);
                $stmt->bindValue(':empleado', $empleado->ruc);
                $stmt->bindValue(':codigoWinfenix', $empleado->ruc);
                $stmt->bindValue(':porcentaje', $empleado->porcentaje);
                $stmt->bindValue(':recargo', $empleado->recargo);
              
                $stmt->execute();
            }

            /* INSERT EN WINFENIX */
            $query = "
                    exec Sp_vengracab 'I ', :userID, :pcID, :oficina, :ejercicio, :tipoDOC, :numeroDOC,'', :fecha, :codCliente,
                    :bodega,'DOL','1.00','0.00', :baseIVA,'0.00','0.00','0.00','0.00','0.00', :subtotal, :descuento, :impuesto,
                    '0.00', :total, :formaPago,'0','0','0','S','0','1','0','0','','', :codVendedor,' ',' ', :observacion, :serie, 
                    :secuencia,'','','','','0.00','0.00','0.00','','','','','','','','','','0','P','','','','','','0','','','','','0', :montoIVA,'0.00','0.00','0.00','0','1113431809','0','','','','','','','','','','  ', :fechaEntrega,''
                
                ";

                $stmt = $this->instancia->prepare($query); 
                $stmt->bindValue(':userID', $_SESSION["usuarioRUC".APP_UNIQUE_KEY]);
                $stmt->bindValue(':pcID', php_uname('n'));
                $stmt->bindValue(':oficina', $datosEmpresa['Oficina']);
                $stmt->bindValue(':ejercicio', $datosEmpresa['Ejercicio']);
                $stmt->bindValue(':tipoDOC', $tipoDOC);
                $stmt->bindValue(':numeroDOC', $numeroDOC);
                $stmt->bindValue(':fecha', date('Ymd'));
                $stmt->bindValue(':codCliente', $documento->cliente->codigo);

                $stmt->bindValue(':bodega', $documento->bodega);
                $stmt->bindValue(':baseIVA', 0); // En vales no existe iva por lo que el subtotal es igual al total
                $stmt->bindValue(':subtotal', $documento->subtotalSinDescuento);
                $stmt->bindValue(':descuento', $documento->descuento);
                $stmt->bindValue(':impuesto', $documento->iva);

                $stmt->bindValue(':total', $documento->total);
                $stmt->bindValue(':formaPago', $documento->formaPago);
                $stmt->bindValue(':codVendedor', $documento->cliente->codVendedor);
                $stmt->bindValue(':observacion', $documento->observacion);
                $stmt->bindValue(':serie', $serieDocs);

                $stmt->bindValue(':secuencia', $numeroDOC);
                $stmt->bindValue(':montoIVA', $documento->iva);
                $stmt->bindValue(':fechaEntrega', date('Ymd'));
                $stmt->execute();

                    foreach ($documento->productos as $producto) {
                        $query = "exec Sp_vengramov :OPCION, :OFI, :EJE, :TIPO, :NUMERO, :FECHA, :CLIENTE, :BODEGA, :TIPMOV, :ACTINV, :ACTPEN, :CODIGO, :UNIDAD, :CANTIDAD, :TIPOPRECIO, :PRECIO, :DESCU, :IVA, :PRECIOTOT, :CADUCIDA, :NOMBRE_EX, :CANDEV, :COSTO, :PRETRANS, :CENTRO, :ORDEN_P, :FLETE_NAVIERA, :FLETE_AGENTE, :CODVEN, :DESC2, :DESC3, :ITEMAGREGADO, :IDDOCINV, :ITEMESOBSEQUI, :CODREL, :CENTRO1, :CENTRO4, :TIPOIVA, :LOTE, :CODPROMO, :CODBONI";
                        
                        $stmt = $this->instancia->prepare($query);            
                        $stmt->bindValue(':OPCION', 'I');
                        $stmt->bindValue(':OFI', $datosEmpresa['Oficina']);
                        $stmt->bindValue(':EJE', $datosEmpresa['Ejercicio']);
                        $stmt->bindValue(':TIPO', $tipoDOC);
                        $stmt->bindValue(':NUMERO', $numeroDOC);
                        $stmt->bindValue(':FECHA', date('Ymd'));
                        $stmt->bindValue(':CLIENTE', $documento->cliente->codigo);
                        $stmt->bindValue(':BODEGA', $documento->bodega);
                        $stmt->bindValue(':TIPMOV', 'S');
                        $stmt->bindValue(':ACTINV', 0);
                        $stmt->bindValue(':ACTPEN', 0);
                        $stmt->bindValue(':CODIGO', $producto->codigo);
                        $stmt->bindValue(':UNIDAD', $producto->unidad);
                        $stmt->bindValue(':CANTIDAD', $producto->cantidad);
                        $stmt->bindValue(':TIPOPRECIO', $documento->cliente->tipoPrecio);
                        $stmt->bindValue(':PRECIO', $producto->precio);
                        $stmt->bindValue(':DESCU', $documento->descuento);
                        $stmt->bindValue(':IVA', $producto->iva);
                        $stmt->bindValue(':PRECIOTOT', $producto->precioTotal);
                        $stmt->bindValue(':CADUCIDA', date('Ymd'));
                        $stmt->bindValue(':NOMBRE_EX', '');
                        $stmt->bindValue(':CANDEV', 0);
                        $stmt->bindValue(':COSTO', 0);
                        $stmt->bindValue(':PRETRANS', 0);
                        $stmt->bindValue(':CENTRO', '');
                        $stmt->bindValue(':ORDEN_P', '');
                        $stmt->bindValue(':FLETE_NAVIERA', 0);
                        $stmt->bindValue(':FLETE_AGENTE', 0);
                        $stmt->bindValue(':CODVEN', $documento->cliente->codVendedor);
                        $stmt->bindValue(':DESC2', 0);
                        $stmt->bindValue(':DESC3', 0);
                        $stmt->bindValue(':ITEMAGREGADO', 0);
                        $stmt->bindValue(':IDDOCINV', '');
                        $stmt->bindValue(':ITEMESOBSEQUI', 0);
                        $stmt->bindValue(':CODREL', '');
                        $stmt->bindValue(':CENTRO1', '');
                        $stmt->bindValue(':CENTRO4', '');
                        $stmt->bindValue(':TIPOIVA', $producto->tipoIVA);
                        $stmt->bindValue(':LOTE', '');
                        $stmt->bindValue(':CODPROMO', '');
                        $stmt->bindValue(':CODBONI', '');
                        $stmt->execute();
                    }
            
            /*UPDATE ORG_DOCUMENTOS */

            $query = "
                UPDATE dbo.ORG_DOCUMENTOS 
                SET
                    RTF = 'Sirvase aprobar',
                    Asunto = 'Solicitud Vale'
                WHERE 
                    IDDoc = :id
            ";

            $stmt = $this->instancia->prepare($query); 
            $stmt->bindValue(':id', $new_cod_VENCAB);
            $stmt->execute();
                
            $commit = $this->instancia->commit();
            return array(
                        'status' => 'OK', 
                        'commit' => $commit, 
                        'newcod' => $new_cod_VENCAB, 
                        'message'=> "Se registro correctamente el vale por perdida: #$new_cod_VENCAB");
            
        }catch(\PDOException $exception){
            $this->instancia->rollBack();
            http_response_code(400);
            return array('status' => 'ERROR', 'message' => $exception->getMessage() );
        }
    }

    public function SQL_aprobarValePerdida(String $ID) {
        try{
            $this->instancia->beginTransaction();

            $datosEmpresa = (object) $this->getDatosEmpresa();
            $tipoDOC = 'SPE';
            $vales_CAB = (object) $this->valesModel->SQL_getValesPerdida_CAB($ID);
            //$vales_MOV = $this->valesModel->SQL_getValesPerdida_MOV($ID);
            $personalReportado = $this->valesModel->SQL_getPersonalReportadoVales($ID);

            /* Por cada empleado reportado*/
            foreach ($personalReportado as $empleado) {
                $empleado = (object) $empleado;

                if ($numeroDOC = $this->SP_contador($tipoDOC, 'ROL')) {
                }else{
                    throw new \Exception("No se ha podido obtener el contador del tipo de documento");
                }

                  /* INSERT EN SP_ROLGRACAB */
                $query = "
                    exec dbo.SP_ROLGRACAB 'I','ADMIN','TS # WSSP','99', :ejercicio, :tipoDOC, :numero, :fecha, :solicitante, :nota,
                    :observacion, :valor, :tasa, :numpagos, :entrePagos, :fechaPagos, :numRel,''
                ";

                /* Calculo de Cuota unica o no */
                if ($empleado->RECARGO > 10){
                    $numpagos = 3;
                }else{
                    $numpagos = 1;
                }
                
                $stmt = $this->instancia->prepare($query); 
                $stmt->bindValue(':ejercicio', $datosEmpresa->Ejercicio);
                $stmt->bindValue(':tipoDOC', $tipoDOC);
                $stmt->bindValue(':numero', $numeroDOC);
                $stmt->bindValue(':fecha', date('Ymd'));
                $stmt->bindValue(':solicitante', $empleado->RUC); //
                $stmt->bindValue(':nota', 'WSSP -'.$ID);

                $stmt->bindValue(':observacion', '');
                $stmt->bindValue(':valor', $empleado->RECARGO);
                $stmt->bindValue(':tasa', 0);
                $stmt->bindValue(':numpagos', $numpagos);
                $stmt->bindValue(':entrePagos', 12);
                $stmt->bindValue(':fechaPagos', date('Ymd'));
                $stmt->bindValue(':numRel', '');
                $stmt->execute();

                $fecha = date_create(date('Ymd'));
                $valor_pendiente = $empleado->RECARGO;

                /* INSERT EN SP_ROLGRAMOV y SP_ROLGRACXC */
                $cuotas = ceil($empleado->RECARGO / $numpagos); //11.84  Calculo del valor a pagar por el empleado para num de cuotas del vale
                for ($contador=1; $contador <= $numpagos;$contador++) {
                    if ($valor_pendiente < $cuotas) {
                        $valor_rol = $valor_pendiente;
                    }else{
                        $valor_rol = $cuotas;
                    }
                    
                    $valor_pendiente =  round($valor_pendiente - $valor_rol, 2); 
                    if ($contador == $numpagos && ($valor_pendiente > 0)) {
                        $valor_rol += $valor_pendiente;
                    }


                    if ($contador==1) {
                        $fechaadd30days = date_format($fecha, 'Ymd');
                    }else{
                        $fechaadd30days = date_format(date_add($fecha, date_interval_create_from_date_string('30 days')), 'Ymd'); // Agregara X dias y devuelve formado Y-m-d
                    }

                    $query = "
                        SELECT RIGHT('00' + Ltrim(Rtrim($contador)),2) as RECIBO
                    ";
                    $stmt = $this->instancia->prepare($query); 
                    if($stmt->execute()){
                        $recibo = $stmt->fetch( \PDO::FETCH_ASSOC )['RECIBO'];
                    }else{
                        $recibo = '000';
                    }
                  

                    $query = "
                        exec dbo.SP_ROLGRAMOV 'I','99', :ejercicio, :tipoDOC, :numero, :recibo,
                                            :fechaadd30days, :capital,'0', :dividendo
                    ";
        
                    $stmt = $this->instancia->prepare($query); 
                    $stmt->bindValue(':ejercicio', $datosEmpresa->Ejercicio);
                    $stmt->bindValue(':tipoDOC', $tipoDOC);
                    $stmt->bindValue(':numero', $numeroDOC);
                    $stmt->bindValue(':recibo', $recibo);
                    $stmt->bindValue(':fechaadd30days', $fechaadd30days);
                    $stmt->bindValue(':capital', $valor_rol);
                    $stmt->bindValue(':dividendo', $valor_rol);
                    $stmt->execute();

                    $query = "
                        exec dbo.SP_ROLGRACXC 'I','ADMINWSSP', :PCID,'99', :ejercicio, :tipoDOC, :numero, :recibo,
                        :fecha, :fechaadd30days, :empleado, :nota,'D', :valorVale,'0.00'
                    ";

                    $stmt = $this->instancia->prepare($query); 
                    $stmt->bindValue(':PCID', php_uname('n'));
                    $stmt->bindValue(':ejercicio', $datosEmpresa->Ejercicio);
                    $stmt->bindValue(':tipoDOC', $tipoDOC);
                    $stmt->bindValue(':numero', $numeroDOC);
                    $stmt->bindValue(':recibo', $recibo);
                    $stmt->bindValue(':fecha', date('Ymd'));
                    $stmt->bindValue(':fechaadd30days', $fechaadd30days);
                    $stmt->bindValue(':empleado', $empleado->RUC);
                    $stmt->bindValue(':nota', 'WSSP -'.$ID);
                    $stmt->bindValue(':valorVale', $valor_rol);
                    $stmt->execute();
                }

                $query = "UPDATE KAO_wssp.dbo.vales_perdida SET estado = 1 WHERE cod_valep = :idDocumento"; //Query
                $stmt = $this->instancia->prepare($query); 
                $stmt->bindValue(':idDocumento', $ID);
                $stmt->execute();
    

            }
        
            $commit = $this->instancia->commit();
            return array(
                        'status' => 'OK', 
                        'commit' => $commit, 
                        'message'=> "Se aprobo correctamente el vale por perdida: #$ID");
            
        }catch(\PDOException $exception){
            $this->instancia->rollBack();
            http_response_code(400);
            return array('status' => 'ERROR', 'message' => $exception->getMessage() );
        }
    }
    


    
   



    

    
}



   
    