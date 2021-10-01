<?php namespace App\Models;

/* LOS MODELOS del MVC retornaran unicamente arrays PHP sin serializar*/

class WinfenixModel extends Conexion  {
    
    public function __construct() {
        parent::__construct();
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

    public function sql_getBodegasWF(){
        $query  = "SELECT CODIGO, NOMBRE FROM dbo.INV_BODEGAS";
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
        $query  = "SELECT CODIGO, NOMBRE FROM dbo.COB_VENDEDORES";
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
        $query  = "SELECT * From dbo.FORMAPAGO ORDER BY CODIGO";
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

    public function sql_getVenTiposDOCWF(){
        $query  = "SELECT * FROM VEN_TIPOS WHERE TIPODOC = 'C'";
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

    public function sql_getTiposPagoTarjetaWF(){
        $query  = "SELECT * From dbo.VEN_MANTAR ORDER BY Codigo";
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
        $query  = "SELECT * From dbo.COB_Grupos ORDER BY CODIGO";
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
        $query  = "SELECT * From dbo.TAB_CANTONES ORDER BY Nombre";
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
        $query = "SELECT NomCia, Oficina, Ejercicio FROM dbo.DatosEmpresa";
        $stmt = $this->instancia->prepare($query); 
        try{
            $stmt->execute();
            return $stmt->fetch( \PDO::FETCH_ASSOC );
        }catch(\PDOException $exception){
            return array('status' => 'error', 'message' => $exception->getMessage() );
        }

    }

    public function getVenTipos ($tipoDOC){
        $query = "SELECT CODIGO, NOMBRE, Serie FROM dbo.VEN_TIPOS WHERE CODIGO = '$tipoDOC'";
        $stmt = $this->instancia->prepare($query); 

        if($stmt->execute()){
            return $stmt->fetch( \PDO::FETCH_ASSOC );
        }else{
            return false;
        }
    }


    

    
}



   
    
