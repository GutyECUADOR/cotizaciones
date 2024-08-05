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
        

        }catch(\PDOException $exception){
            return array('status' => 'error', 'mensaje' => $exception->getMessage() );
        }
           

    }

    public function getTiposDocsDiponiblesByUserName(string $username){

        $query  = "
            SELECT 
                CODIGO, NOMBRE,TIPMOV, TIPODOC, BODEGA FROM ven_tipos 
            WHERE codigo in (SELECT dato FROM XASIGDATOUSER WHERE  USERCOD = :username) and TIPODOC='c'
        ";
      
        try{
            $stmt = $this->instancia->prepare($query); 
            $stmt->bindValue(':username', $username); 
    
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

    public function getBodegasDiponiblesByUsername(string $username){

        $query  = "
            SELECT 
                DATO as CODIGO,
                NOMBRE,
                USERCOD,
                FILTRO,
                BodegaPorDefault
            FROM XASIGDATOUSER where FILTRO = 'bod' AND USERCOD = :username
        ";
      
        try{
            $stmt = $this->instancia->prepare($query); 
            $stmt->bindValue(':username', $username); 
    
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
        

        }catch(\PDOException $exception){
            return array('status' => 'error', 'mensaje' => $exception->getMessage() );
        }
           

    }
}
