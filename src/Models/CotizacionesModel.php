<?php namespace App\Models;

use App\Models\Conexion;

class CotizacionesModel extends Conexion  {
    
    public function __construct() {
        parent::__construct();
    }

    public function SQL_getCliente(string $RUC) {

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
            RUC='$RUC'";  

        try{
            $stmt = $this->instancia->prepare($query); 
    
                if($stmt->execute()){
                    $resulset = $stmt->fetch( \PDO::FETCH_ASSOC );
                    
                }else{
                    $resulset = false;
                }
            return $resulset;  

        }catch(\PDOException $exception){
            return array('status' => 'ERROR', 'message' => $exception->getMessage() );
        }
   
    }

    public function getNextNumClienteWF(){
        try{
            $stmt = $this->instancia->prepare("Sp_Contador'CLI','99','','',''"); 
            $stmt->execute();
            $stmt->nextRowset();
            
            $newCodLimpio = $stmt->fetch(\PDO::FETCH_ASSOC);
            $newCodLimpio =  $newCodLimpio['NExtID'];
            
            $newCod = $this->instancia->query("select RIGHT('00000000' + Ltrim(Rtrim('$newCodLimpio')),8) as newcod");
            $codigoConFormato = $newCod->fetch(\PDO::FETCH_ASSOC);
            $codigoConFormato = $codigoConFormato['newcod'];
            return $codigoConFormato;

        }catch(\PDOException $exception){
            return array('status' => 'ERROR', 'message' => $exception->getMessage() );
        }

    }

    public function insertNuevoCliente($formData, $newcodigo){
        $fecha = date('Ymd');
        $query = "
            INSERT INTO COB_CLIENTES
                (OFICINA,CODIGO,NOMBRE,VENDEDOR,CODVENOLD,GRUPO,CONTACTO,EMPRESA,CUENTA,CUENTA2,ESVARIOS,RUC,DIRECCION1,DIRECCION2,TELEFONO1,TELEFONO2,TELEFONO3,CODPOS,FAX,FAXPED,EMAIL,EMAIL2,PAGWEB,DIVISA,IDIOMA,NOTA,FPAGO,DIASPAGO,TIPOPRECIO,PORDES,TIPOCLI,LIMITECRED,DIRENV,DIRENV1,TELENV,GNOMBRE,GCEDULA,GEMPRESA,GDIRECCION,GTELEFONO1,GTELEFONO2,CELULAR,GNOTA,PWD,WEBDERECHO,WEBAVISO,WEBNOTA,LIBRE,CURSO,ESTUDIA,SALDO,ULTIMAVENTA,ULTIMOCOBRO,ESTADO,CREADOPOR,EDITADOPOR,ANULADOPOR,CREADODATE,EDITADODATE,ANULADODATE,PCID,CLASE,NEGOCIO,PAIS,PROVINCIA,CANTON,REQ_ANTICIPO,TIPOIDENT,PROMOCION,CODCOMRELA,DIVISION,ACTIVIDAD1,ACTIVIDAD2,REPRESENTA,CEDREPRESENTA,RECAUDADOR,CONDICION,NUMPAG,ENTREPAG,TIPOPAGO,CONDPAGO,PROVCLIENTE,FECEXPIRAN,CONTACTO1,MAILCON1,EXTCON1,CELCON1,CONTACTO2,MAILCON2,EXTCON2,CELCON2,CONTACTO3,MAILCON3,EXTCON3,CELCON3,SOBRECUPO,DIASGRACIA,DIADEPAGO,HORADEPAGO,GARANTIAS,CODTARJETA,EMITARJETA,VENTARJETA,PORTARJETA,PORTARJETAEFE,PORTARJETACHE,PORTARJETATAR,PORANTICIPO,CONTACTOPAGO,FECHAALTA,WEBENVEMAIL,PARTEREL,TIPCLI) 
            VALUES('99','$newcodigo','$formData->nombre','$formData->vendedor','','$formData->grupo','','$formData->nombre','','','','$formData->RUC','$formData->direccion','','$formData->telefono','','','','','','$formData->email','','','DOL','ESP','','CON','0','A',0.00,'',0.00,'','','','','','','','','','','','',0.00,'','','',0.00,'0',0.00,'        ','        ','1','','','','        ','        ','        ','','','','593','217','21701','0','$formData->tipoIdentificacion','0','','','','','','','$formData->vendedor','0',0.00,0.00,'','','','        ','','','','','','','','','','','','',0.00,0.00,'','','','','        ','        ',0.00,0.00,0.00,0.00,0.00,'','$fecha','0','NO','01')
        ";
        
        try{
            $rowsAfected = $this->instancia->exec($query);
           return array('status' => 'OK', 'message' => $rowsAfected. ' fila afectada(s)' ); //true;
           
        }catch(\PDOException $exception){
            http_response_code(400);
            return array('status' => 'ERROR', 'message' => $exception->getMessage() );
        }

    }

    public function getProducto(string $busqueda) {
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
                INV_ARTICULOS.TipoIVA,
                RTRIM(IVA.VALOR) as ValorIVA
            FROM INV_ARTICULOS 
            INNER JOIN dbo.INV_IVA AS IVA on IVA.CODIGO = INV_ARTICULOS.TipoIva
            WHERE INV_ARTICULOS.Codigo = :codigo"; 

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

    public function SQL_getStock(string $codigo) {
        $query = " 
        SELECT 
            X.CODIGO,
            X.NOMBRE,
            X.STOCK 
        FROM 
            (SELECT A.CODIGO,A.NOMBRE,STOCK = (DBO.DimeStockFis('99' , :codigo ,'', A.CODIGO)) 
        FROM INV_BODEGAS A WITH(NOLOCK) ) X 
        WHERE X.STOCK > 0 
        ORDER BY x.codigo";

        try{
            $stmt = $this->instancia->prepare($query); 
            $stmt->bindValue(':codigo', $codigo);

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

    public function SQL_getStockComponentes(object $busqueda) {
        $query = " 
        SELECT 
            X.COD_ARTICULO,
            X.NOM_ARTICULO,
            X.COD_BODEGA,
            X.STOCK 
        FROM (
            SELECT 
                A.CODIGO AS COD_BODEGA,
                A.NOMBRE AS NOM_BODEGA,
                ART.CODIGO AS COD_ARTICULO,
                ART.NOMBRE AS NOM_ARTICULO,STOCK = (DBO.DimeStockFis('99' ,ART.CODIGO,'',A.CODIGO)) 
            FROM INV_BODEGAS A WITH(NOLOCK) 
                INNER JOIN INV_ARTICULOS ART WITH(NOLOCK) ON 1 = 1 
                INNER JOIN INV_ARTICULOS MAE WITH(NOLOCK) ON MAE.CODIGO = ART.CODKAO 
            WHERE MAE.CODIGO = :codigo AND CASE WHEN ISNULL( :bodega1 ,'') = '' THEN '' ELSE A.CODIGO END = ISNULL( :bodega2 ,'')) X 
        WHERE X.STOCK > 0 
        ORDER BY X.NOM_ARTICULO
        ";

        try{
            $stmt = $this->instancia->prepare($query); 
            $stmt->bindParam(':codigo', $busqueda->codigo);
            $stmt->bindParam(':bodega1', $busqueda->bodega);
            $stmt->bindParam(':bodega2', $busqueda->bodega);

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

    public function SQL_getStockRetazos(string $codigo) {
        $query = " 
        SELECT 
        BODEGA,
        LARGO,
        ANCHO,
        PRECIO FROM (
                SELECT 
                    KAR.BODEGA,
                    KAR.LARGO,
                    KAR.ANCHO,
                    SUM(CASE WHEN KAR.TIPMOV = 'E' THEN 1 ELSE -1 END * CANTIDAD) AS STOCK,
                    RET.PRECIO FROM INV_KARDEXRETAZOS KAR WITH(NOLOCK) 
                    INNER JOIN INV_RETAZOS RET WITH(NOLOCK) ON KAR.SECUENCIA = RET.SECUENCIA 
                    WHERE RET.CODIGO = :codigo 
                        AND ISNULL(KAR.ANULADO,0) = 0 
                        AND NOT EXISTS(SELECT VRT.SECUENCIA FROM VEN_MOVRETAZOS VRT WITH(NOLOCK) INNER JOIN VEN_CAB VCA WITH(NOLOCK) ON VRT.ID = VCA.ID 
                        WHERE VRT.SECUENCIA = KAR.SECUENCIA AND ISNULL(ANULADO,0) = 0) AND CASE WHEN ISNULL('' ,'') = '' THEN '' ELSE KAR.BODEGA END = ISNULL('' ,'') 
                GROUP BY KAR.BODEGA,KAR.LARGO,KAR.ANCHO,RET.PRECIO)DET 
                WHERE STOCK > 0
        ";

        try{
            $stmt = $this->instancia->prepare($query); 
            $stmt->bindParam(':codigo', $codigo);

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


    
    
}



   
    
