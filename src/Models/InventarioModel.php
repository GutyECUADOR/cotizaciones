<?php namespace App\Models;

use App\Models\Conexion;
use App\Models\VenCabClass;
use App\Models\VenMovClass;

/* LOS MODELOS del MVC retornaran unicamente arrays PHP sin serializar*/

class InventarioModel extends Conexion  {
    
    public function __construct() {
        parent::__construct();
    }

    public function getAllInfoEmpresaModel() {

        //Query de consulta con parametros para bindear si es necesario.
        $query = " 
            SELECT NomCia, DirCia, RucCia, TelCia, Ciudad FROM dbo.DATOSEMPRESA    
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
            return array('status' => 'error', 'mensaje' => $exception->getMessage() );
        }
   
    }

    public function getAllProductosWithExtraDescModel($IDDocument) {

        $query = " 
        SELECT 
            INVART.Codigo,
            INVART.Nombre,
            INVART.PrecA,
            extraData.*
        
        FROM INV_ARTICULOS as INVART
        INNER JOIN wssp.dbo.extraData_cotizaciones as extraData
        ON INVART.Codigo COLLATE Modern_Spanish_CI_AS = extraData.codigoProducto
        
        WHERE extraData.IDDocument = '$IDDocument'
        ";  // Final del Query SQL 

        try{
            $stmt = $this->instancia->prepare($query); 
    
                if($stmt->execute()){
                    $resulset = $stmt->fetchAll( \PDO::FETCH_ASSOC );
                    
                }else{
                    $resulset = false;
                }
            return $resulset;  

        }catch(\PDOException $exception){
            return array('status' => 'error', 'mensaje' => $exception->getMessage() );
        }
   
    }

    public function getInfoClienteModel($RUC) {

        //Query de consulta con parametros para bindear si es necesario.
        $query = " 
            
        SELECT 
            CLIENTE.CODIGO, 
            RTRIM(CLIENTE.NOMBRE) as NOMBRE, 
            RTRIM(CLIENTE.EMPRESA) as EMPRESA, 
            RTRIM(CLIENTE.RUC) as RUC, 
            RTRIM(CLIENTE.EMAIL) as EMAIL, 
            RTRIM(CLIENTE.FECHAALTA) as FECHAALTA, 
            RTRIM(CLIENTE.DIRECCION1) as DIRECCION, 
            RTRIM(CLIENTE.TELEFONO1) as TELEFONO, 
            RTRIM(VENDEDOR.CODIGO) as VENDEDOR,
            RTRIM(VENDEDOR.NOMBRE) as VENDEDORNAME,
            RTRIM(CLIENTE.LIMITECRED) as LIMITECRED, 
            RTRIM(CLIENTE.FPAGO) as FPAGO, 
            RTRIM(CLIENTE.DIASPAGO) as DIASPAGO, 
            RTRIM(CLIENTE.TIPOPRECIO) as TIPOPRECIO 
        FROM 
            dbo.COB_CLIENTES as CLIENTE INNER JOIN
            dbo.COB_VENDEDORES as VENDEDOR ON VENDEDOR.CODIGO = CLIENTE.VENDEDOR
        WHERE 
            RUC='$RUC'";  // Final del Query SQL 

        try{
            $stmt = $this->instancia->prepare($query); 
    
                if($stmt->execute()){
                    $resulset = $stmt->fetch( \PDO::FETCH_ASSOC );
                    
                }else{
                    $resulset = false;
                }
            return $resulset;  

        }catch(\PDOException $exception){
            return array('status' => 'error', 'mensaje' => $exception->getMessage() );
        }
   
    }


    public function getInfoUsuarioModel($codigoUsuario) {

        //Query de consulta con parametros para bindear si es necesario.
        $query = " 
            SELECT Correo, Pop3, Smtp, User_Mail, Pwd_Mail FROM dbo.USUARIOS WHERE Codigo = '$codigoUsuario'
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
            return array('status' => 'error', 'mensaje' => $exception->getMessage() );
        }
   
    }


    public function getVENCABByID($IDDocument) {
        $query = " 
        SELECT 
            RTRIM(LTRIM(REPLACE(CLIENTE.NOMBRE, NCHAR(0x00A0), ''))) as NOMBRE,
            CLIENTE.RUC,
            CLIENTE.DIRECCION1,
            CLIENTE.TELEFONO1,
            RTRIM(LTRIM(REPLACE(CLIENTE.EMAIL, NCHAR(0x00A0), ''))) as EMAIL,
            VENDEDOR.CODIGO as CodigoVendedor,
            VENDEDOR.NOMBRE as VendedorName,
            VEN_CAB.*
        FROM 
            dbo.VEN_CAB 
            INNER JOIN dbo.COB_CLIENTES as CLIENTE on CLIENTE.CODIGO = VEN_CAB.CLIENTE
            LEFT JOIN dbo.COB_VENDEDORES as VENDEDOR on VENDEDOR.CODIGO = VEN_CAB.CODVEN	
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
            return array('status' => 'error', 'mensaje' => $exception->getMessage() );
        }
   
    }

    public function getVENMOVByID($IDDocument) {

       //Query de consulta con parametros para bindear si es necesario.
       $query = "
       SELECT
            ARTICULO.Nombre,
            VEN_MOV.*
            
        FROM 
            dbo.VEN_MOV
            INNER JOIN dbo.INV_ARTICULOS as ARTICULO ON ARTICULO.Codigo = VEN_MOV.CODIGO
        WHERE 
            ID = '$IDDocument'
       ";  // Final del Query SQL 

       $stmt = $this->instancia->prepare($query); 
   
       $arrayResultados = array();

           if($stmt->execute()){
               while ($row = $stmt->fetch( \PDO::FETCH_ASSOC )) {
                   array_push($arrayResultados, $row);
               }
               return $arrayResultados;
               
           }else{
               $resulset = false;
           }
       return $resulset; 
   
    }
    

    public function getInfoProductoModel($codigoProducto, $tipoPrecio='A') {

        $tipoPrec = 'Prec'.$tipoPrecio; // Determina el tipo de precio que se devolvera segun el cliente
        //Query de consulta con parametros para bindear si es necesario.
        $query = " 
            SELECT 
                RTRIM(INV_ARTICULOS.CODIGO) as CODIGO, 
                RTRIM(INV_ARTICULOS.NOMBRE) as NOMBRE, 
                INV_ARTICULOS.$tipoPrec as PRECIO,
                RTRIM(INV_ARTICULOS.TipoIva) as TIPOIVA,
                RTRIM(IVA.VALOR) as VALORIVA,
                (select dbo.DIMESTOCKFIS('99','$codigoProducto','','B01')) AS STOCK,
                (select dbo.DIMESTOCKFIS('99','$codigoProducto','','B02')) AS STOCK2,
                (select dbo.DIMESTOCKFIS('99','$codigoProducto','','B04')) AS STOCK3
            FROM 
                dbo.INV_ARTICULOS WITH (NOLOCK)
                INNER JOIN dbo.INV_IVA AS IVA on IVA.CODIGO = INV_ARTICULOS.TipoIva
            
            WHERE INV_ARTICULOS.Codigo='$codigoProducto'";  // Final del Query SQL 

        try{
            $stmt = $this->instancia->prepare($query); 
    
                if($stmt->execute()){
                    $resulset = $stmt->fetch( \PDO::FETCH_ASSOC );
                    
                }else{
                    $resulset = false;
                }
            return $resulset;  

        }catch(\PDOException $exception){
            return array('status' => 'error', 'mensaje' => $exception->getMessage() );
        }
   
    }

    public function getAllClientesModel($terminoBusqueda, $tipoBusqueda='NOMBRE') {

        //Query de consulta con parametros para bindear si es necesario.
        $query = "SELECT TOP 10 RUC, NOMBRE, CODIGO, TIPOPRECIO FROM dbo.COB_CLIENTES WHERE $tipoBusqueda LIKE '$terminoBusqueda%'";  // Final del Query SQL 

        $stmt = $this->instancia->prepare($query); 
    
            if($stmt->execute()){
                return $stmt->fetchAll( \PDO::FETCH_ASSOC );
            }else{
                $resulset = false;
            }
        return $resulset;  
    }


    public function getAllDocumentosModel($fechaINI, $fechaFIN, $stringBusqueda) {

        //Query de consulta con parametros para bindear si es necesario.
        $query = "
        declare @p1 int;
        
        exec sp_prepexec @p1 output,N'@P1 varchar(3),@P2 varchar(2),@P3 varchar(8),@P4 varchar(8), @P5 varchar(25)',
        N'SELECT 
            VEN.TIPO,
            VEN.NUMERO,RTRIM(VEN.SERIE)+''-''+RTRIM(LTRIM(VEN.SECUENCIA)) AS NFIS,
            CONVERT(CHAR(10),VEN.FECHA,102) AS FECHA,
            RTRIM(CLI.NOMBRE) AS CLIENTE,
            VEN.BODEGA,VEN.total,
            VEN.DIVISA,
            (CASE VEN.ANULADO WHEN 1 THEN ''AN'' ELSE '''' END) AS ANULADO
            ,ven.id,Cancelada='''' 	FROM VEN_CAB VEN 
            LEFT OUTER JOIN  COB_CLIENTES CLI ON (CLI.CODIGO = VEN.CLIENTE)  
        WHERE 
            VEN.TIPO = @P1  AND VEN.OFI = @P2  AND Ven.fecha BETWEEN @P3  AND @P4  AND CLI.NOMBRE LIKE @P5
        ORDER BY VEN.TIPO,VEN.NUMERO,VEN.FECHA'
        ,'COT','99','$fechaINI','$fechaFIN','$stringBusqueda%'

        ";
        $stmt = $this->instancia->prepare($query); 
        $stmt->execute();

        $arrayResultados = array();
            if($stmt->execute()){
                return $stmt->fetchAll( \PDO::FETCH_ASSOC );
            }else{
                $resulset = false;
            }
        return $resulset;  

   
    }

    public function getArraysBodegas() {

        //Query de consulta con parametros para bindear si es necesario.
        $query = "SELECT CODIGO as Value, NOMBRE as DisplayText FROM INV_BODEGAS";  // Final del Query SQL 

        $stmt = $this->instancia->prepare($query); 
    
            if($stmt->execute()){
                return $stmt->fetchAll( \PDO::FETCH_ASSOC );
            }else{
                $resulset = false;
            }
        return $resulset;  

   
    }

  
    public function getDatosEmpresaFromWINFENIX ($dataBaseName='wssp'){
       
        $query = "SELECT NomCia, Oficina, Ejercicio FROM dbo.DatosEmpresa";
        $stmt = $this->instancia->prepare($query); 

        try{
            $stmt->execute();
            return $stmt->fetch( \PDO::FETCH_ASSOC );
        }catch(\PDOException $exception){
            return array('status' => 'error', 'mensaje' => $exception->getMessage() );
        }

    }
    
   
    public function getDatosClienteWINFENIXByRUC ($clienteRUC, $dataBaseName='wssp'){
        
        $query = "SELECT * FROM COB_CLIENTES WHERE RUC = '$clienteRUC'";
        $stmt = $this->instancia->prepare($query); 

        try{
            $stmt->execute();
            return $stmt->fetch( \PDO::FETCH_ASSOC );
        }catch(\PDOException $exception){
            return array('status' => 'error', 'mensaje' => $exception->getMessage() );
        }
    }

   
    public function getDatosDocumentsWINFENIXByTypo ($tipoDOC){
        
        $query = "SELECT CODIGO, NOMBRE, Serie FROM dbo.VEN_TIPOS WHERE CODIGO = '$tipoDOC'";
        $stmt = $this->instancia->prepare($query); 

        if($stmt->execute()){
            return $stmt->fetch( \PDO::FETCH_ASSOC );
        }else{
            return false;
        }
    }

    /*Retorna el siguiente secuencial del tipo de documento que se le indiqie - Winfenix*/
    public function getNextNumDocWINFENIX ($tipoDoc){
        
        $gestion = 'VEN';
        $ofi = '99';
        $eje = '';
        $tipo = $tipoDoc;
        $codigo = '';

        try{
            $stmt = $this->instancia->prepare("exec SP_CONTADOR ?, ?, ?, ?, ?"); 
            $stmt->bindValue(1, $gestion); 
            $stmt->bindValue(2, $ofi); 
            $stmt->bindValue(3, $eje); 
            $stmt->bindValue(4, $tipo); 
            $stmt->bindValue(5, $codigo); 
            $stmt->execute();
           
            $newCodLimpio = $stmt->fetch(\PDO::FETCH_ASSOC);
            $newCodLimpio =  $newCodLimpio['NExtID'];
            
            return $newCodLimpio;

        }catch(\PDOException $exception){
            return array('status' => 'error', 'mensaje' => $exception->getMessage() );
        }

        
    }

    /*Retorna el secuencial de WinFenix en formato 0000XXXX - Winfenix*/
    public function formatoNextNumDocWINFENIX ($dataBaseName='wssp', $secuencialWinfenix){
        
        $newCod = $this->instancia->query("select RIGHT('00000000' + Ltrim(Rtrim('$secuencialWinfenix')),8) as newcod");
        $codigoConFormato = $newCod->fetch(\PDO::FETCH_ASSOC);
        $codigoConFormato = $codigoConFormato['newcod'];
        return $codigoConFormato;
    }

    public function insertExtraDataModel($extraDataRow, $dataBaseName='wssp'){

        $query = "
        
        INSERT INTO wssp.dbo.extraData_cotizaciones 
        VALUES ('$extraDataRow->codDocumento','$extraDataRow->nombreImagen','$extraDataRow->codProducto','$extraDataRow->descripcion')
        
        ";
        
        try{
            $rowsAfected = $this->instancia->exec($query);
           return array('status' => 'ok', 'mensaje' => $rowsAfected. ' fila afectada(s)' ); //true;
           
        }catch(\PDOException $exception){
            return array('status' => 'error', 'mensaje' => $exception->getMessage() );
        }

        
    }

    public function insertVEN_CAB($VEN_CAB_obj, $dataBaseName='wssp'){
       
        //$queryExample = "exec dbo.SP_VENGRACAB 'I','ADMINWSSP','TESTOK','99', '2014', 'C02', '00001721','','20181126','00054818','FAL','DOL','1.00','0.00','10','0.00','0.00','0.00','0.00','0.00','10','0.00','2','0.00','12','CON','0','1','0','S','0','1','0','0','','','999',' ',' ','PRUEBAS','001005','00002050','','','','','0.00','0.00','0.00','','','','','','','','','','0','P','','','','','','0','','','','','0','2','0.00','0.00','0.00','0','999999999 ','0','','','','','','EFE','','','','','20181126','',''";
        $VEN_CAB = new VenCabClass();
        $VEN_CAB = $VEN_CAB_obj;
        
        $query = "
        
        exec dbo.SP_VENGRACAB 'I','ADMINWSSP','$VEN_CAB->pcID','$VEN_CAB->oficina', '$VEN_CAB->ejercicio', '$VEN_CAB->tipoDoc', '$VEN_CAB->numeroDoc','','$VEN_CAB->fecha','$VEN_CAB->cliente','$VEN_CAB->bodega','$VEN_CAB->divisa','1.00','$VEN_CAB->subtotalBase0','$VEN_CAB->subtotal','0.00','0.00','0.00','0.00','0.00','$VEN_CAB->subtotal','0.00','$VEN_CAB->impuesto','0.00','$VEN_CAB->total','CON','0','1','0','S','0','1','0','0','','','$VEN_CAB->vendedor',' ',' ','$VEN_CAB->observacion','$VEN_CAB->serie','$VEN_CAB->secuencia','','','','','0.00','0.00','0.00','','','','','','','','','','0','P','','','','','','0','','','','','0','$VEN_CAB->impuesto','0.00','0.00','0.00','0','999999999 ','0','','','','','','$VEN_CAB->formaPago','','','','','$VEN_CAB->fecha','',''"
        
        ;
        
        try{
            $rowsAfected = $this->instancia->exec($query);
           return array('status' => 'ok', 'mensaje' => $rowsAfected. ' fila afectada(s)' ); //true;
           
        }catch(\PDOException $exception){
            return array('status' => 'error', 'mensaje' => $exception->getMessage() );
        }

        
    }

    public function insertVEN_MOV($VEN_MOV_obj, $dataBaseName='wssp'){
        
        $VEN_MOV = new VenMovClass();
        $VEN_MOV = $VEN_MOV_obj;

        $query = "
        
        exec dbo.SP_VENGRAMOV 'I','$VEN_MOV->oficina','$VEN_MOV->ejercicio','$VEN_MOV->tipoDoc','$VEN_MOV->numeroDoc','$VEN_MOV->fecha','$VEN_MOV->cliente','$VEN_MOV->bodega','S','0','0','$VEN_MOV->codProducto','UND','$VEN_MOV->cantidad','$VEN_MOV->tipoPrecio','$VEN_MOV->precioProducto','$VEN_MOV->porcentajeDescuentoProd','$VEN_MOV->porcentajeIVA','$VEN_MOV->precioTOTAL','$VEN_MOV->fecha','','0.00','0.0000000','0','1.01.11','','1','1','$VEN_MOV->vendedor','0.0000','0.0000','0','','0','$VEN_MOV->tipoIVA' 
        
        ";

        $stmt = $this->instancia->prepare($query); 
       
        try{
            $rowsAfected = $this->instancia->exec($query);
           return array('status' => 'ok', 'mensaje' => $rowsAfected. ' fila afectada(s)' ); //true;
           
        }catch(\PDOException $exception){
            return array('status' => 'error', 'mensaje' => $exception->getMessage() );
        }


    }

    public function getProveedor(string $busqueda) {
      
        $query = "
            SELECT 
                Codigo,
                Nombre,
                Contacto,
                Cuenta,
                Ruc,
                Direccion1,
                telefono1,
                Fpago = ISNULL(fpago,''), 
                DiasPago = ISNULL(diaspago,'0'), 
                divisa,
                TIPOCONT,
                email,
                porc_compensa=ISNULL(porc_compensa,0)
            FROM PAG_Proveedores 
            WHERE RUC= :codigo

           ";  

        $stmt = $this->instancia->prepare($query);
        $stmt->bindParam(':codigo', $busqueda); 
       
            if($stmt->execute()){
                $resulset = $stmt->fetch( \PDO::FETCH_ASSOC );
            }else{
                $resulset = false;
            }
        return $resulset;  
    }

    public function getProducto(string $busqueda) {
        //Query de consulta con parametros para bindear si es necesario.
        $query = "
            SELECT TOP 1
                INV_ARTICULOS.Codigo,
                INV_ARTICULOS.Nombre,
                INV_ARTICULOS.Unidad,
                Costo = (SELECT dbo.DimecostoProm('99', :codigoCosto,'') as Costo),
                Stock = (SELECT dbo.DimeStockFis('99', :codigoStock,'' ,'') as Stock),
                INV_ARTICULOS.TipoArticulo,
                INV_ARTICULOS.PrecA,
                INV_ARTICULOS.Peso,
                INV_ARTICULOS.TipoIva,
                RTRIM(IVA.VALOR) as VALORIVA,
                extraData.fechaCaducidad,
                ISNULL(extraData.costoTeorico, 0) as costoTeorico,
                ISNULL( extraData.porcentajeMerma, 0) as porcentajeMerma,
	            extraData.observacion
            FROM INV_ARTICULOS 
            INNER JOIN dbo.INV_IVA AS IVA on IVA.CODIGO = INV_ARTICULOS.TipoIva
            LEFT JOIN wssp.dbo.INV_ARTICULOS_EXTRA_DATA as extraData on extraData.codigo = INV_ARTICULOS.CODIGO
            WHERE INV_ARTICULOS.Codigo = :codigo";  // Final del Query SQL 

        $stmt = $this->instancia->prepare($query);
        $stmt->bindParam(':codigoCosto', $busqueda); 
        $stmt->bindParam(':codigoStock', $busqueda); 
        $stmt->bindParam(':codigo', $busqueda); 
       
            if($stmt->execute()){
                $resulset = $stmt->fetch( \PDO::FETCH_ASSOC );
            }else{
                $resulset = false;
            }
        return $resulset;  
    }

    public function getCostoProducto(object $busqueda) {
        //Query de consulta con parametros para bindear si es necesario.
        $query = "
            SELECT
            DBO.DimeStockFis('99', :codigostock,'' ,:bodega) / factor AS Stock,
            dbo.DimecostoProm('99', :codigocostoPrimedio,'') as costoPromedio,
            ISNULL(factor,1) as factor,
            CostoProducto =  CAST(factor * dbo.DimecostoProm('99', :codigocosto,'') as varchar)
            FROM inv_unifactor WITH(NOLOCK) 
            WHERE codart = :codigo and unidad = :unidad    
        ";

        $stmt = $this->instancia->prepare($query);
        $stmt->bindParam(':codigostock', $busqueda->codigo); 
        $stmt->bindParam(':codigocostoPrimedio', $busqueda->codigo); 
        $stmt->bindParam(':codigocosto', $busqueda->codigo); 
        $stmt->bindParam(':codigo', $busqueda->codigo); 
        $stmt->bindParam(':unidad', $busqueda->unidad); 
        $stmt->bindParam(':bodega', $busqueda->bodega); 
       
            if($stmt->execute()){
                $resulset = $stmt->fetch( \PDO::FETCH_ASSOC );
            }else{
                $resulset = false;
            }
        return $resulset;  
    }

    public function getCantidadByFactor(object $busqueda) {
        //Query de consulta con parametros para bindear si es necesario.
        $query = "
            SELECT
                ISNULL(factor,1) as factor
                FROM inv_unifactor WITH(NOLOCK) 
            WHERE codart = :codigo and unidad = :unidad    
        ";

        $stmt = $this->instancia->prepare($query);
        $stmt->bindParam(':codigo', $busqueda->codigo); 
        $stmt->bindParam(':unidad', $busqueda->unidad); 
       
            if($stmt->execute()){
                $resulset = $stmt->fetch( \PDO::FETCH_ASSOC );
            }else{
                $resulset = false;
            }
        return $resulset;  
    }

    public function getUnidadesMedida(string $busqueda) {
        //Query de consulta con parametros para bindear si es necesario.
        $query = "
            SELECT trim(Unidad) as Unidad FROM INV_UniFactor
            WHERE CodArt = :codigo";  // Final del Query SQL 

        $stmt = $this->instancia->prepare($query);
        $stmt->bindParam(':codigo', $busqueda); 
       
            if($stmt->execute()){
                $resulset = $stmt->fetchAll( \PDO::FETCH_ASSOC );
            }else{
                $resulset = false;
            }
        return $resulset;  
    }

    public function getProductos(string $busqueda) {
        //Query de consulta con parametros para bindear si es necesario.
        $busquedafix = $busqueda.'%';

        $query = "
            SELECT TOP 100 
                Codigo,
                Nombre,
                TipoIva,
                TipoArticulo,
                Unidad,
                PrecA,
                DBO.DimeStockFis('99', Codigo ,'' ,'B01') AS Stock,
                Peso
            FROM INV_ARTICULOS 
            WHERE Codigo = :codigo OR Nombre LIKE :nombre 
            AND TipoArticulo IN ('1','2')
            AND EsKit = 0
            ";  // Final del Query SQL 

        $stmt = $this->instancia->prepare($query);
        $stmt->bindParam(':codigo', $busqueda); 
        $stmt->bindParam(':nombre', $busquedafix); 
    
            if($stmt->execute()){
                $resulset = $stmt->fetchAll( \PDO::FETCH_ASSOC );
            }else{
                $resulset = false;
            }
        return $resulset;  
    }

    public function getComposicionProducto(string $codigo){
        $query = "
        SELECT 
            Mov.Codigo,
            Art.Nombre,
            Mov.Unidad,
            Mov.Cantidad,
            Costo = 0,
            Stock = 0,
            Costotot= 0,
            art.tipoarticulo,
            Mov.NameKitUno,
            Mov.NameKitVar,
            mov.Tipo,
            Mov.PrecioUno,
            Mov.PrecioVar,
            Mov.Preparacion
        FROM INV_KIT MOV WITH (NOLOCK) LEFT OUTER JOIN INV_ARTICULOS ART WITH (NOLOCK) ON MOV.CODIGO = ART.CODIGO
        WHERE MOV.CODIGOKIT = :codigo
            ";  // Final del Query SQL 

        $stmt = $this->instancia->prepare($query);
        $stmt->bindParam(':codigo', $codigo); 
      
            if($stmt->execute()){
                $resulset = $stmt->fetchAll( \PDO::FETCH_ASSOC );
            }else{
                $resulset = false;
            }
        return $resulset;  
    }

    public function Winfenix_SaveIngreso (object $documento) {
        try{
            $this->instancia->beginTransaction();

                // Ejecuta Sp_Contador
                $query = "Sp_Contador 'INV','99','','IPC',''";  
                $stmt = $this->instancia->prepare($query); 
                $stmt->execute();
                $stmt->nextRowset();
                $nextID = $stmt->fetchColumn();
                $NextIDWithFormat = str_pad($nextID, 8, '0', STR_PAD_LEFT);
               

                // Ejecuta Sp_INVgracab para Ingreso de Inventario
                $query = "
                Sp_INVgracab 'I', :usuario, :pcid, '99','2020','IPC', :secuencia, :fecha ,'IIPC', :bodega,' ','DOL','1.00','E','0','','1.0000','','INV','',' ','','',''
                ";  

                    $stmt = $this->instancia->prepare($query); 
                    $stmt->bindValue(':usuario', $_SESSION["usuarioRUC".APP_UNIQUE_KEY]);
                    $stmt->bindValue(':pcid', php_uname('n'));
                    $stmt->bindValue(':secuencia', $NextIDWithFormat);
                    $stmt->bindValue(':fecha', date('Ymd'));
                    $stmt->bindValue(':bodega', $documento->productos_ingreso->bodega);
                
                $stmt->execute();

                // Ejecutamos Sp_invgraMOV para registro de los movimientos

                foreach ($documento->productos_ingreso->items as $producto) {
                    $query = "
                        Sp_invgraMOV 'I','99','2020','IPC', :secuencia, :fecha, :bodega,'E', :codproducto, :unidadproducto, :cantidadproducto, :costoproducto,'0.0000000', :costototal,'',''
                    ";  
                        $stmt = $this->instancia->prepare($query);
                        $stmt->bindValue(':secuencia', $NextIDWithFormat);
                        $stmt->bindValue(':fecha', date('Ymd'));
                        $stmt->bindValue(':bodega', $documento->productos_ingreso->bodega);
                        $stmt->bindValue(':codproducto', $producto->codigo);
                        $stmt->bindValue(':unidadproducto', $producto->unidad);
                        $stmt->bindValue(':cantidadproducto', $producto->cantidad);
                        $stmt->bindValue(':costoproducto', $producto->precio);
                        $stmt->bindValue(':costototal', $producto->precio);
                       
                    $stmt->execute();
                }


                foreach ($documento->productos_ingreso->items as $producto) {
                    $query = "
                        Sp_invgraKardex 'I','99','2020','IPC', :secuencia,'INV', :fecha,'Ingreso Produccion / Cortes   ','INV',' ', :bodega,'E', :codproducto, :unidadproducto , :cantidadproducto, :costoproducto, :costototal, :usuario, :pcid,'DOL','1.0000'
                    ";  
                        $stmt = $this->instancia->prepare($query);
                        $stmt->bindValue(':usuario', $_SESSION["usuarioRUC".APP_UNIQUE_KEY]);
                        $stmt->bindValue(':pcid', php_uname('n'));
                        $stmt->bindValue(':secuencia', $NextIDWithFormat);
                        $stmt->bindValue(':fecha', date('Ymd'));
                        $stmt->bindValue(':bodega', $documento->productos_ingreso->bodega);
                        $stmt->bindValue(':codproducto', $producto->codigo);
                        $stmt->bindValue(':unidadproducto', $producto->unidad);
                        $stmt->bindValue(':cantidadproducto', $producto->cantidad);
                        $stmt->bindValue(':costoproducto', $producto->precio);
                        $stmt->bindValue(':costototal', $producto->precio);
                       
                    $stmt->execute();
                }

                foreach ($documento->productos_ingreso->items as $producto) {
                    $query = "
                        IF EXISTS(SELECT codigo FROM wssp.dbo.INV_ARTICULOS_EXTRA_DATA WHERE codigo= :codigoSelect)
                            UPDATE wssp.dbo.INV_ARTICULOS_EXTRA_DATA 
                                SET fechaCaducidad = :fechaCaducidad_update, 
                                    observacion = :observacion_update  
                            WHERE codigo= :codigo_update
                        ELSE
                            INSERT INTO 
                                wssp.dbo.INV_ARTICULOS_EXTRA_DATA (dbname, codigo, fechaCaducidad, observacion)
                            VALUES ( :dbname, :codigo, :fechaCaducidad, :observacion)
                    ";  
                        $stmt = $this->instancia->prepare($query);
                        $stmt->bindParam(':codigoSelect', $producto->codigo);

                        $stmt->bindParam(':fechaCaducidad_update', $producto->fechaCaducidad);
                        $stmt->bindParam(':observacion_update', $producto->observacion);
                        $stmt->bindParam(':codigo_update', $producto->codigo);

                        $stmt->bindParam(':dbname', $_SESSION["empresaAUTH".APP_UNIQUE_KEY]);
                        $stmt->bindParam(':codigo',  $producto->codigo);
                        $stmt->bindParam(':fechaCaducidad', $producto->fechaCaducidad);
                        $stmt->bindParam(':observacion', $producto->observacion);
                       
                    $stmt->execute();
                }

                
            $commit = $this->instancia->commit();
            return array('status' => 'ok', 'commit' => $commit, 'newcod' => $NextIDWithFormat);
            
        }catch(\PDOException $exception){
            $this->instancia->rollBack();
            http_response_code(400);
            return array('status' => 'error', 'message' => $exception->getMessage() );
        }
   
    }

    public function Winfenix_SaveEgreso (object $documento) {
        try{
            $this->instancia->beginTransaction();

                // Ejecuta Sp_Contador Documento
                $query = "Sp_Contador 'INV','99','','EPC',''";
                
                $stmt = $this->instancia->prepare($query); 
                $stmt->execute();
                $stmt->nextRowset();
                $nextID = $stmt->fetchColumn();
                $NextIDWithFormat = str_pad($nextID, 8, '0', STR_PAD_LEFT);

                // Ejecuta Sp_Contador Documento
                $query = "Sp_Contador 'CNT','99','','DIN',''";
            
                $stmt = $this->instancia->prepare($query); 
                $stmt->execute();
                $stmt->nextRowset();
                $nextIDAsiento = $stmt->fetchColumn();
                $NextIDAsientoWithFormat = '992020DIN'.str_pad($nextIDAsiento, 8, '0', STR_PAD_LEFT);
              
                // Ejecuta Sp_comgracab para Ingreso de Inventario
                $query = "
                    Sp_INVgracab 'I', :usuario, :pcid,'99','2020','EPC', :secuencia, :fecha,'EEPC', :bodega,' ','DOL','1.00','S','1','', :total, :numcnt,'INV','','','','',''
                ";  

                    $stmt = $this->instancia->prepare($query); 
                    $stmt->bindValue(':usuario', $_SESSION["usuarioRUC".APP_UNIQUE_KEY]);
                    $stmt->bindValue(':pcid', php_uname('n'));
                    $stmt->bindValue(':secuencia', $NextIDWithFormat);
                    $stmt->bindValue(':fecha', date('Ymd'));
                    $stmt->bindValue(':bodega', $documento->productos_egreso->bodega);
                    $stmt->bindValue(':total', $documento->productos_egreso->total);
                    $stmt->bindValue(':numcnt', $NextIDAsientoWithFormat);
                
                $stmt->execute();

                foreach ($documento->productos_egreso->items as $producto) {
                    $query = "
                        Sp_invgraMOV 'I','99','2020','EPC', :secuencia, :fecha, :bodega, 'S', :codproducto, :unidadproducto, :cantidadproducto, :costoproducto,'0.0000000', :costototal,'',''
                    ";  
                        $stmt = $this->instancia->prepare($query);
                        $stmt->bindValue(':secuencia', $NextIDWithFormat);
                        $stmt->bindValue(':fecha', date('Ymd'));
                        $stmt->bindValue(':bodega', $documento->productos_egreso->bodega);
                        $stmt->bindValue(':codproducto', $producto->codigo);
                        $stmt->bindValue(':unidadproducto', $producto->unidad);
                        $stmt->bindValue(':cantidadproducto', $producto->cantidad);
                        $stmt->bindValue(':costoproducto', $producto->precio);
                        $stmt->bindValue(':costototal', $producto->precio);
                        
                    $stmt->execute();
                }

                foreach ($documento->productos_egreso->items as $producto) {
                    $query = "
                        Sp_invgraKardex 'I','99','2020','EPC', :secuencia,'INV', :fecha,'Salida Produccion Cortes  ','INV','', :bodega,'S', :codproducto, :unidadproducto, :cantidadproducto, :costoproducto, :costototal, :usuario, :pcid,'DOL','1.0000'
                    "; 
                        $stmt = $this->instancia->prepare($query);
                        $stmt->bindValue(':usuario', $_SESSION["usuarioRUC".APP_UNIQUE_KEY]);
                        $stmt->bindValue(':pcid', php_uname('n'));
                        $stmt->bindValue(':secuencia', $NextIDWithFormat);
                        $stmt->bindValue(':fecha', date('Ymd'));
                        $stmt->bindValue(':bodega', $documento->productos_egreso->bodega);
                        $stmt->bindValue(':codproducto', $producto->codigo);
                        $stmt->bindValue(':unidadproducto', $producto->unidad);
                        $stmt->bindValue(':cantidadproducto', $producto->cantidad);
                        $stmt->bindValue(':costoproducto', $producto->precio);
                        $stmt->bindValue(':costototal', $producto->precio);
                       
                    $stmt->execute();
                }
                
            $commit = $this->instancia->commit();
            return array('status' => 'ok', 'commit' => $commit, 'newcod' => $NextIDWithFormat);
            
        }catch(\PDOException $exception){
            $this->instancia->rollBack();
            http_response_code(400);
            return array('status' => 'error', 'message' => $exception->getMessage(), 'newcod' => $NextIDWithFormat );
        }
   
    }
    
    public function Winfenix_SaveCreacionReceta (object $documento) {
        $kit = $documento->kit;

        try{
            $this->instancia->beginTransaction();
           
            $query = "DELETE INV_KIT  WHERE CodigoKit = :codigoKit";
            $stmt = $this->instancia->prepare($query); 
            $stmt->bindValue(':codigoKit', $kit->codigo);
            $stmt->execute();

            foreach ($kit->composicion as $producto) {
                $query = "
                INSERT INV_KIT 
	                    (Ofi,CODIGOKIT,codigo,unidad,cantidad,COSTO,Tipo,nameKitUno,namekitvar,PrecioUno,PrecioVar,Preparacion, CreadoPor,CreadoDate,PCID)
                VALUES ('99' , :CODIGOKIT, :codigo, :unidad, :cantidad, :COSTO,'CO','','','0','0', :Preparacion, :CreadoPor, GETDATE(), :PCID)
                ";  
                    $stmt = $this->instancia->prepare($query);
                    $stmt->bindValue(':CODIGOKIT', $kit->codigo);
                    $stmt->bindValue(':codigo', $producto->codigo);
                    $stmt->bindValue(':unidad', $producto->unidad);
                    $stmt->bindValue(':cantidad', $producto->cantidad);
                    $stmt->bindValue(':COSTO', $producto->precio);
                    $stmt->bindValue(':Preparacion', $producto->descripcion);
                    $stmt->bindValue(':CreadoPor', $_SESSION["usuarioRUC".APP_UNIQUE_KEY]);
                    $stmt->bindValue(':PCID', php_uname('n'));
                    
                   
                $stmt->execute();
            }
          
           
            $commit = $this->instancia->commit();
            return array('status' => 'ok', 'commit' => $commit, 'kit' => $kit->codigo);
            
        }catch(\PDOException $exception){
            $this->instancia->rollBack();
            http_response_code(400);
            return array('status' => 'error', 'message' => $exception->getMessage());
        }
   
    }

    public function WSSP_SaveCostoTeorico (object $documento){
     
        try{
            $this->instancia->beginTransaction();
            
            foreach ($documento->kit->composicion as $producto) {
                $query = "
                    IF EXISTS(SELECT codigo FROM wssp.dbo.INV_ARTICULOS_EXTRA_DATA WHERE codigo= :codigoSelect)
                        UPDATE wssp.dbo.INV_ARTICULOS_EXTRA_DATA 
                            SET costoTeorico = :costoTeorico_update, 
                            porcentajeMerma = :porcentajeMerma_update  
                        WHERE codigo= :codigo_update
                    ELSE
                        INSERT INTO 
                            wssp.dbo.INV_ARTICULOS_EXTRA_DATA (dbname, codigo, costoTeorico, porcentajeMerma)
                        VALUES ( :dbname, :codigo, :costoTeorico, :porcentajeMerma)
                ";  
                    $stmt = $this->instancia->prepare($query);
                    $stmt->bindParam(':codigoSelect', $producto->codigo);

                    $stmt->bindParam(':costoTeorico_update', $producto->costoTeorico);
                    $stmt->bindParam(':porcentajeMerma_update', $producto->porcentajeMerma);
                    $stmt->bindParam(':codigo_update', $producto->codigo);

                    $stmt->bindParam(':dbname', $_SESSION["empresaAUTH".APP_UNIQUE_KEY]);
                    $stmt->bindParam(':codigo',  $producto->codigo);
                    $stmt->bindParam(':costoTeorico', $producto->costoTeorico);
                    $stmt->bindParam(':porcentajeMerma', $producto->porcentajeMerma);
                   
                $stmt->execute();
            }

            $commit = $this->instancia->commit();
            return array('status' => 'OK', 'commit' => $commit ,'message' => 'Costo Teórico y Porcentaje de merma registrados');
            
        }catch(\PDOException $exception){
            $this->instancia->rollBack();
            http_response_code(400);
            return array('status' => 'error', 'message' => $exception->getMessage() );
        }
    }

    public function Winfenix_saveTransformacionKITS (object $documento) {
        $kit = $documento->kit_obs;

        try{
            $this->instancia->beginTransaction();

            // Registramos en tabla WSSP.INV_ARTICULOS_EXTRA_DATA
            $query = "
                IF EXISTS(SELECT codigo FROM wssp.dbo.INV_ARTICULOS_EXTRA_DATA WHERE codigo= :codigoSelect)
                    UPDATE wssp.dbo.INV_ARTICULOS_EXTRA_DATA 
                        SET fechaCaducidad = :fechaCaducidad_update, 
                            observacion = :observacion_update  
                    WHERE codigo= :codigo_update
                ELSE
                    INSERT INTO 
                        wssp.dbo.INV_ARTICULOS_EXTRA_DATA (dbname, codigo, fechaCaducidad, observacion)
                    VALUES ( :dbname, :codigo, :fechaCaducidad, :observacion)
            ";  
                $stmt = $this->instancia->prepare($query);
                $stmt->bindParam(':codigoSelect', $kit->codigo);

                $stmt->bindParam(':fechaCaducidad_update', $kit->fechaCaducidad);
                $stmt->bindParam(':observacion_update', $kit->observacion);
                $stmt->bindParam(':codigo_update', $kit->codigo);

                $stmt->bindParam(':dbname', $_SESSION["empresaAUTH".APP_UNIQUE_KEY]);
                $stmt->bindParam(':codigo',  $kit->codigo);
                $stmt->bindParam(':fechaCaducidad', $kit->fechaCaducidad);
                $stmt->bindParam(':observacion', $kit->observacion);
                
            $stmt->execute();

            
            // Creacion de NextID STK
            $stmt = $this->instancia->prepare("SET NOCOUNT ON; exec Sp_Contador :gestion,'99','', :tipoDOC,''"); 

            $tipoDoc = 'STK';
            $gestion = 'INV';
            $stmt->bindParam(':tipoDOC', $tipoDoc); 
            $stmt->bindParam(':gestion', $gestion); 
            $stmt->execute();
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            $nextID = $row['NExtID'];
            $STK_secuencia = str_pad($nextID, 8, '0', STR_PAD_LEFT);

            // Creacion de NextID DSK
            $tipoDoc = 'DSK';
            $gestion = 'CNT';
            $stmt->bindParam(':tipoDOC', $tipoDoc); 
            $stmt->bindParam(':gestion', $gestion); 
            $stmt->execute();
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            $nextID = $row['NExtID'];
            $DSK_secuencia = str_pad($nextID, 8, '0', STR_PAD_LEFT);

            // Creacion de NextID DEK
            $tipoDoc = 'DEK';
            $gestion = 'CNT';
            $stmt->bindParam(':tipoDOC', $tipoDoc); 
            $stmt->bindParam(':gestion', $gestion); 
            $stmt->execute();
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            $nextID = $row['NExtID'];
            $DEK_secuencia = str_pad($nextID, 8, '0', STR_PAD_LEFT);
           
           
        
            // Ejecuta Sp_INVgracab STK (Egresos)
            $query = "
                Sp_INVgracab 'I', :usuario, :pcid,'99','2020','STK', :secuencia, :fecha,'SSTK', :bodega_egreso, :bodega_ingreso,'DOL', :factor,'S','0','', :precio, :num_cnt,'INV', :num_rel,' ',' ',' ',' '
            ";

            $stmt = $this->instancia->prepare($query); 
            $stmt->bindValue(':usuario', $_SESSION["usuarioRUC".APP_UNIQUE_KEY]);
            $stmt->bindValue(':pcid', php_uname('n'));
            $stmt->bindValue(':secuencia', $STK_secuencia);
            $stmt->bindValue(':fecha', date('Ymd'));
            $stmt->bindValue(':bodega_egreso', $documento->bodega_egreso);
            $stmt->bindValue(':bodega_ingreso', $documento->bodega_ingreso);
            $stmt->bindValue(':factor', $documento->kit_obs->factor);
            $stmt->bindValue(':precio', $documento->kit_obs->precio);
            $stmt->bindValue(':num_cnt', '992020DSK'.$STK_secuencia);
            $stmt->bindValue(':num_rel', '992020ETK'.$STK_secuencia);
            $stmt->execute();
            // Ejecuta Sp_invgraMOV STK

            //Save inv_gramov
            foreach ($documento->kit_obs->composicion as $producto) {
                $query = "
                    Sp_invgraMOV 'I','99','2020','STK', :secuencia, :fecha, :bodega_egreso,'S', :codproducto, :unidadproducto, :cantidadproducto, :costoproducto,'0', :costototal,'','','','','', :codKIT
                ";  
                    $stmt = $this->instancia->prepare($query);
                    $stmt->bindValue(':secuencia', $STK_secuencia);
                    $stmt->bindValue(':fecha', date('Ymd'));
                    $stmt->bindValue(':bodega_egreso', $documento->bodega_egreso);
                    $stmt->bindValue(':codproducto', $producto->codigo);
                    $stmt->bindValue(':unidadproducto', $producto->unidad);
                    $stmt->bindValue(':cantidadproducto', $producto->cantidad);
                    $stmt->bindValue(':costoproducto', $producto->precio);
                    $stmt->bindValue(':costototal', $producto->subtotal);
                    $stmt->bindValue(':codKIT', $documento->kit_obs->codigo);
                    
                $stmt->execute();
            }
            // Save mov_kardex
            foreach ($documento->kit_obs->composicion as $producto) {
                $query = "
                Sp_invgraKardex 'I','99','2020','STK', :secuencia,'INV', :fecha,' ',' ',' ', :bodega_egreso,'S', :codproducto, :unidadproducto, :cantidadproducto, :costoproducto, :costototal, :usuario, :pcid,'DOL', :factor

                ";  
                    $stmt = $this->instancia->prepare($query);
                    $stmt->bindValue(':secuencia', $STK_secuencia);
                    $stmt->bindValue(':fecha', date('Ymd'));
                    $stmt->bindValue(':bodega_egreso', $documento->bodega_egreso);
                    $stmt->bindValue(':codproducto', $producto->codigo);
                    $stmt->bindValue(':unidadproducto', $producto->unidad);
                    $stmt->bindValue(':cantidadproducto', $producto->cantidad);
                    $stmt->bindValue(':costoproducto', $producto->precio);
                    $stmt->bindValue(':costototal', $producto->subtotal);
                    $stmt->bindValue(':usuario', $_SESSION["usuarioRUC".APP_UNIQUE_KEY]);
                    $stmt->bindValue(':pcid', php_uname('n'));
                    $stmt->bindValue(':factor', $producto->factor);
                    
                $stmt->execute();
            }


            // Ejecuta Sp_INVgracab ETK (Ingresos)
            $query = "
            Sp_INVgracab 'I', :usuario, :pcid,'99','2020','ETK', :secuencia, :fecha,'EETK', :bodega_ingreso, :bodega_egreso,'DOL', :factor,'E','0','', :precio, :num_cnt,' ',' ',' ',' ',' ',' '
            ";

            $stmt = $this->instancia->prepare($query); 
            $stmt->bindValue(':usuario', $_SESSION["usuarioRUC".APP_UNIQUE_KEY]);
            $stmt->bindValue(':pcid', php_uname('n'));
            $stmt->bindValue(':secuencia', $STK_secuencia);
            $stmt->bindValue(':fecha', date('Ymd'));
            $stmt->bindValue(':bodega_egreso', $documento->bodega_egreso);
            $stmt->bindValue(':bodega_ingreso', $documento->bodega_ingreso);
            $stmt->bindValue(':factor', $documento->kit_obs->factor);
            $stmt->bindValue(':precio', $producto->subtotal);
            $stmt->bindValue(':num_cnt', '992020DEK'.$STK_secuencia);
            $stmt->execute();

            // Ejecuta Sp_INVgrmov ETK (Ingresos)
            $producto = $documento->kit_obs;
            $query = "
                Sp_invgraMOV 'I','99','2020','ETK', :secuencia, :fecha, :bodega_ingreso,'E', :codproducto, :unidadproducto, :cantidadproducto, :costoproducto,'', :costototal,''
            ";  
                $stmt = $this->instancia->prepare($query);
                $stmt->bindValue(':secuencia', $STK_secuencia);
                $stmt->bindValue(':fecha', date('Ymd'));
                $stmt->bindValue(':bodega_ingreso', $documento->bodega_ingreso);
                $stmt->bindValue(':codproducto', $producto->codigo);
                $stmt->bindValue(':unidadproducto', $producto->unidad);
                $stmt->bindValue(':cantidadproducto', $producto->cantidad);
                $stmt->bindValue(':costoproducto', $producto->precio);
                $stmt->bindValue(':costototal', $producto->subtotal);
                $stmt->execute();

                
            // Ejecuta Sp_invgraKardex ETK (Ingresos) error aqui
            $query = "
            Sp_invgraKardex 'I','99','2020','ETK', :secuencia,'INV', :fecha,' ',' ',' ', :bodega_ingreso,'E', :codproducto, :unidadproducto, :cantidadproducto, :costoproducto, :costototal, :usuario, :pcid,'DOL', :factor

            ";  
                $stmt = $this->instancia->prepare($query);
                $stmt->bindValue(':secuencia', $STK_secuencia);
                $stmt->bindValue(':fecha', date('Ymd'));
                $stmt->bindValue(':bodega_ingreso', $documento->bodega_ingreso);
                $stmt->bindValue(':codproducto', $producto->codigo);
                $stmt->bindValue(':unidadproducto', $producto->unidad);
                $stmt->bindValue(':cantidadproducto', $producto->cantidad);
                $stmt->bindValue(':costoproducto', $producto->precio);
                $stmt->bindValue(':costototal', $producto->subtotal);
                $stmt->bindValue(':usuario', $_SESSION["usuarioRUC".APP_UNIQUE_KEY]);
                $stmt->bindValue(':pcid', php_uname('n'));
                $stmt->bindValue(':factor', $producto->factor);
                
                $stmt->execute();


            // Ejecuta Sp_cntgracab DSK (Ingresos)
            $query = "
            Sp_cntgracab'I',:usuario, :pcid,'99','2020','DSK', :secuencia, :fecha,'DOL', :factor, :glosa,'','INV','','','',:idDOC
            ";  
                $stmt = $this->instancia->prepare($query);
                $stmt->bindValue(':usuario', $_SESSION["usuarioRUC".APP_UNIQUE_KEY]);
                $stmt->bindValue(':pcid', php_uname('n'));
                $stmt->bindValue(':secuencia', $DSK_secuencia);
                $stmt->bindValue(':fecha', date('Ymd'));
                $stmt->bindValue(':factor', $documento->kit_obs->factor);
                $stmt->bindValue(':glosa', '[Inv] - 992020STK'.$STK_secuencia);
                $stmt->bindValue(':idDOC', '992020STK'.$STK_secuencia);
                $stmt->execute();

            // Save Sp_cntgramov CREDITO
            foreach ($documento->kit_obs->composicion as $producto) {
                $query = "
                Sp_cntgramov'I','99','2020','DSK',:secuencia,'1.1.08.01.001', :detalle,'0.0000', :credito,'','STK', :numref,'','','','','INV','DOL', :factor, :fecha
                ";  
                    $stmt = $this->instancia->prepare($query);
                    $stmt->bindValue(':secuencia', $STK_secuencia);
                    $stmt->bindValue(':detalle', '[Inv] - 992020STK'.$STK_secuencia);
                    $stmt->bindValue(':credito', $producto->subtotal);
                    $stmt->bindValue(':numref', $STK_secuencia);
                    $stmt->bindValue(':factor', $producto->factor);
                    $stmt->bindValue(':fecha', date('Ymd'));
                $stmt->execute();
            }

            // Save Sp_cntgramov DEBITO
            foreach ($documento->kit_obs->composicion as $producto) {
                $query = "
                Sp_cntgramov'I','99','2020','DSK',:secuencia,'1.1.08.01.001', :detalle, :debito, '0.0000','','STK', :numref,'','','','','INV','DOL', :factor, :fecha
                ";  
                    $stmt = $this->instancia->prepare($query);
                    $stmt->bindValue(':secuencia', $STK_secuencia);
                    $stmt->bindValue(':detalle', '[Inv] - 992020STK'.$STK_secuencia);
                    $stmt->bindValue(':debito', $producto->subtotal);
                    $stmt->bindValue(':numref', $STK_secuencia);
                    $stmt->bindValue(':factor', $producto->factor);
                    $stmt->bindValue(':fecha', date('Ymd'));
                $stmt->execute();
            }


            // Ejecuta Sp_cntgracab DEK (Egresos)
            $query = "
                Sp_cntgracab'I', :usuario, :pcid,'99','2020','DEK', :secuencia, :fecha,'DOL', :factor,'','','INV','','','', :idDOC
            ";  
                $stmt = $this->instancia->prepare($query);
                $stmt->bindValue(':usuario', $_SESSION["usuarioRUC".APP_UNIQUE_KEY]);
                $stmt->bindValue(':pcid', php_uname('n'));
                $stmt->bindValue(':secuencia', $DEK_secuencia);
                $stmt->bindValue(':fecha', date('Ymd'));
                $stmt->bindValue(':factor', $documento->kit_obs->factor);
                $stmt->bindValue(':idDOC', '992020ETK'.$STK_secuencia);
                $stmt->execute();

                // Save Sp_cntgramov DEBITO
                $query = "
                    Sp_cntgramov'I','99','2020','DEK', :secuencia,'1.1.08.01.001','', :debito,'0.0000','','ETK', :numref,'','','','','INV','DOL', :factor, :fecha
                ";  
                    $stmt = $this->instancia->prepare($query);
                    $stmt->bindValue(':secuencia', $DEK_secuencia);
                    $stmt->bindValue(':debito', $documento->kit_obs->subtotal);
                    $stmt->bindValue(':numref', $STK_secuencia);
                    $stmt->bindValue(':factor', $documento->kit_obs->factor);
                    $stmt->bindValue(':fecha', date('Ymd'));
                $stmt->execute();

                 // Save Sp_cntgramov CREDITO
                $query = "
                    Sp_cntgramov'I','99','2020','DEK', :secuencia,'1.1.08.01.001','','0.0000', :credito,'','ETK', :numref,'','','','','INV','DOL', :factor, :fecha
                ";  
                    $stmt = $this->instancia->prepare($query);
                    $stmt->bindValue(':secuencia', $DEK_secuencia);
                    $stmt->bindValue(':credito', $documento->kit->subtotal);
                    $stmt->bindValue(':numref', $STK_secuencia);
                    $stmt->bindValue(':factor', $documento->kit->factor);
                    $stmt->bindValue(':fecha', date('Ymd'));
                $stmt->execute();

            
            $commit = $this->instancia->commit();
            return array('status' => 'ok', 
                        'commit' => $commit,
                        'message' => 'Se ha registrado correctamente el documento STK #'. $STK_secuencia,
                        'STK_secuencia' => $STK_secuencia, 
                        'DSK_secuencia' => $DSK_secuencia, 
                        'DEK_secuencia' => $DEK_secuencia);
            
        }catch(\PDOException $exception){
            $this->instancia->rollBack();
            http_response_code(400);
            return array('status' => 'error', 'message' => $exception->getMessage());
        }
   
    }

    

    
    

    
}



   
    
