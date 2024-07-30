<?php namespace App\Models;

class ValesPerdidaModel extends Conexion  {
    
    public function __construct() {
        parent::__construct();
    }

    public function SQL_consultaPersonalHabilitadoByID(String $RUC){
        $query  = "
            SELECT  
                CLIENTE.CODIGO, 
                CLIENTE.RUC, 
                CLIENTE.NOMBRE,
                SBIO.CodDpto as CODDPTO,
                PORTARJETAEFE as DESCUENTO
            FROM 
                dbo.COB_CLIENTES as CLIENTE WITH (NOLOCK) 
            INNER JOIN 
                SBIOKAO.dbo.Empleados AS SBIO ON SBIO.Cedula COLLATE Modern_Spanish_CI_AS = CLIENTE.RUC COLLATE Modern_Spanish_CI_AS 
            WHERE CLIENTE.RUC= :RUC AND CLASE='I' AND CLIENTE.RUC IN (SELECT cedula  FROM dbo.ROL_EMPLEADOS WHERE Estatus = 'A') AND SBIO.CodDpto IN ('EVA','ASI','TEC')
        
        ";
        try{
            $stmt = $this->instancia->prepare($query); 
            $stmt->bindParam(':RUC', $RUC);
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

    public function SQL_getDocumentosValesPerdida(Object $busqueda){
        $query  = "
                SELECT TOP 100
                vales_perdida.*,
                dbo.ORG_DOCUMENTOS.Aprobado as aprobadoSupervisor
            FROM 
                KAO_wssp.dbo.vales_perdida
            INNER JOIN
                dbo.ORG_DOCUMENTOS ON ORG_DOCUMENTOS.IDDoc COLLATE Latin1_General_CI_AS = vales_perdida.cod_valep 
             WHERE empresa = :empresa
             ORDER BY vales_perdida.cod_valep DESC
        ";
        try{
            $stmt = $this->instancia->prepare($query); 
            $stmt->bindParam(':empresa', $_SESSION["empresaAUTH".APP_UNIQUE_KEY]);
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

    public function SQL_consultaPersonalHabilitado(Object $busqueda){
        $query  = "
            SELECT  
                CLIENTE.CODIGO, 
                CLIENTE.RUC, 
                CLIENTE.NOMBRE,
                SBIO.CodDpto as CODDPTO,
                PORTARJETAEFE as DESCUENTO
            FROM 
                dbo.COB_CLIENTES as CLIENTE WITH (NOLOCK) 
            INNER JOIN 
                SBIOKAO.dbo.Empleados AS SBIO ON SBIO.Cedula COLLATE Modern_Spanish_CI_AS = CLIENTE.RUC COLLATE Modern_Spanish_CI_AS 
            WHERE CLASE='I' AND CLIENTE.RUC IN (SELECT cedula  FROM dbo.ROL_EMPLEADOS WHERE Estatus = 'A') AND SBIO.CodDpto IN ('EVA','ASI','TEC')
        
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
            return array('status' => 'ERROR', 'message' => $exception->getMessage() );
        }
           

    }

    public function SQL_consultaEmpleadoByID(String $RUC){
        $query  = "
        SELECT  
            CODIGO,
            NOMBRE,
            RUC
        FROM dbo.COB_CLIENTES WITH (NOLOCK) 
        WHERE COB_CLIENTES.RUC= :RUC AND CLASE='I' AND COB_CLIENTES.RUC  IN  (SELECT CEDULA FROM dbo.ROL_EMPLEADOS WHERE Estatus = 'A')
            
        ";
        try{
            $stmt = $this->instancia->prepare($query); 
            $stmt->bindParam(':RUC', $RUC);
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

    public function SQL_getVenTiposDOCWF(){
        $query  = "
                SELECT CODIGO, NOMBRE 
                FROM VEN_TIPOS 
                WITH(NOLOCK) 
                WHERE 
                    CODIGO IN ('SPA' , 'SPB' , 'SPC' , 'SPD' , 'SPE' , 'SPF')
                ORDER BY CODIGO";
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

    public function SQL_getValesPendientesRevision(Object $busqueda){
        $query  = "
            SELECT TOP 100
                VEN_CAB.ID, 
                VEN_CAB.TIPO, 
                vales.empresa as EMPRESA, 
                Bodegas.NOMBRE as BODEGA, 
                cliente.NOMBRE, 
                CONVERT(varchar, VEN_CAB.FECHA,23) as FECHA, 
                CONVERT(varchar, vales.fechaPagos,23) as FECHAPAGOS, 
                vales.total as TOTAL,
                vales.estado as ESTADO
            FROM dbo.VEN_CAB
                INNER JOIN dbo.COB_CLIENTES as cliente on VEN_CAB.CLIENTE = cliente.CODIGO 
                INNER JOIN KAO_wssp.dbo.vales_perdida as vales on vales.cod_valep COLLATE Modern_Spanish_CI_AS = VEN_CAB.ID COLLATE Modern_Spanish_CI_AS 
                INNER JOIN dbo.INV_BODEGAS as bodegas on vales.BODEGA COLLATE Modern_Spanish_CI_AS = bodegas.CODIGO COLLATE Modern_Spanish_CI_AS 
            WHERE 
                vales.empresa = :empresa
                AND vales.estado = 0 
                AND VEN_CAB.ID IN (SELECT IDDoc FROM dbo.ORG_DOCUMENTOS WHERE Aprobado=1)
            ORDER BY vales.fecha DESC
        ";
        try{
            $stmt = $this->instancia->prepare($query); 
            $stmt->bindParam(':empresa', $_SESSION["empresaAUTH".APP_UNIQUE_KEY]);
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

    public function anularVale(String $idDocumento) {
        try{
            $this->instancia->beginTransaction();

            $query = "UPDATE KAO_wssp.dbo.vales_perdida SET estado = 2 WHERE cod_valep = :idDocumento"; //Query
            $stmt = $this->instancia->prepare($query); 
            $stmt->bindValue(':idDocumento', $idDocumento);
            $stmt->execute();

            $query = "UPDATE dbo.VEN_CAB SET anulado = '1' WHERE ID = :idDocumento"; //Query
            $stmt = $this->instancia->prepare($query); 
            $stmt->bindValue(':idDocumento', $idDocumento);
            $stmt->execute();

            $query = "UPDATE dbo.ORG_DOCUMENTOS  SET Anulado = '1', Negado = '1' WHERE IDDoc = :idDocumento"; //Query
            $stmt = $this->instancia->prepare($query); 
            $stmt->bindValue(':idDocumento', $idDocumento);
            $stmt->execute();

                
            $commit = $this->instancia->commit();
            return array(
                        'status' => 'OK', 
                        'commit' => $commit,
                        'message'=> "Se anuló correctamente el vale por perdida: #$idDocumento");
            
        }catch(\PDOException $exception){
            $this->instancia->rollBack();
            http_response_code(400);
            return array('status' => 'ERROR', 'message' => $exception->getMessage() );
        }
    }

    public function SQL_getValesPerdida_CAB(String $ID){
        $query  = "
        SELECT TOP 1 * 
        FROM 
            KAO_wssp.dbo.vales_perdida 
        WHERE cod_valep = :ID 
           
        ";
        try{
            $stmt = $this->instancia->prepare($query); 
            $stmt->bindParam(':ID', $ID);
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

    public function SQL_getValesPerdida_MOV(String $ID){
        $query  = "
        SELECT * FROM 
            KAO_wssp.dbo.detalle_valep 
        WHERE 
            cod_detalle_valep = :ID 
           
        ";
        try{
            $stmt = $this->instancia->prepare($query); 
            $stmt->bindParam(':ID', $ID);
          
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

    public function SQL_getPersonalReportadoVales(String $ID){
        $query  = "
        SELECT  
            COB_CLIENTES.CODIGO,
            COB_CLIENTES.NOMBRE,
            COB_CLIENTES.RUC,
            VALEP.porcentaje as PORCENTAJE,
            VALEP.valor as RECARGO
        FROM 
            dbo.COB_CLIENTES with (nolock) 
            INNER JOIN KAO_wssp.dbo.recargo_valep as VALEP on 
            dbo.COB_CLIENTES.RUC COLLATE Modern_Spanish_CI_AS = VALEP.ci_empleado_rec COLLATE Modern_Spanish_CI_AS 
        WHERE cod_recargo_valep = :ID and VALEP.empresa  = :empresa
           
        ";
        try{
            $stmt = $this->instancia->prepare($query); 
            $stmt->bindParam(':ID', $ID);
            $stmt->bindParam(':empresa', $_SESSION["empresaAUTH".APP_UNIQUE_KEY]);
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



   
    