<?php namespace App\Models;

class CotizacionClass extends Conexion {
    
    public function __construct(){
        parent::__construct();
    }

    public function getTop100InvArticulos() {
 
        //Query de consulta con parametros para bindear si es necesario.
        $query = "
            SELECT top 1* FROM INV_ARTICULOS
        ";  // Final del Query SQL 

        $stmt = $this->instancia->prepare($query); 
    
        $arrayResultados = array();

            if($stmt->execute()){

                while ($row = $stmt->fetch( \PDO::FETCH_ASSOC )) {
                    array_push($arrayResultados, $row);
                }
               
                return $arrayResultados;
            }else{
                return false;
                
            }
        
    }


    public function getInfoEmpresa(){

        $query = "SELECT TOP 1 * FROM dbo.DatosEmpresa";  // Final del Query SQL 

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

    public function getBodegasWF(){

        $query  = "SELECT CODIGO, NOMBRE FROM dbo.INV_BODEGAS";
      
        try{
            $stmt = $this->instancia->prepare($query); 
    
            $arrayResultados = array();

            if($stmt->execute()){

                while ($row = $stmt->fetch( \PDO::FETCH_ASSOC )) {
                    array_push($arrayResultados, $row);
                }
               
                return $arrayResultados;
            }else{
                return false;
                
            }
        

        }catch(PDOException $exception){
            return array('status' => 'error', 'mensaje' => $exception->getMessage() );
        }
           

    }

    public function getVendedoresWF(){

        $query  = "SELECT CODIGO, NOMBRE FROM dbo.COB_VENDEDORES";
      
        try{
            $stmt = $this->instancia->prepare($query); 
    
            $arrayResultados = array();

            if($stmt->execute()){

                while ($row = $stmt->fetch( \PDO::FETCH_ASSOC )) {
                    array_push($arrayResultados, $row);
                }
               
                return $arrayResultados;
            }else{
                return false;
                
            }
        

        }catch(PDOException $exception){
            return array('status' => 'error', 'mensaje' => $exception->getMessage() );
        }
           

    }

    public function getFormasPagoWF(){

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
            return array('status' => 'error', 'mensaje' => $exception->getMessage() );
        }
           

    }

    public function getVenTiposDOCWF(){

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
            return array('status' => 'error', 'mensaje' => $exception->getMessage() );
        }
           

    }

    public function getTiposPagoTarjetaWF(){

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
            return array('status' => 'error', 'mensaje' => $exception->getMessage() );
        }
           

    }

    public function getGruposClientesWF(){

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
            return array('status' => 'error', 'mensaje' => $exception->getMessage() );
        }
           

    }

    public function getCantonesWF(){

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
            return array('status' => 'error', 'mensaje' => $exception->getMessage() );
        }
           

    }


}
