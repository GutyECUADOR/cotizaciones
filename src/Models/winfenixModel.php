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
    
    /* Retorna lista de proveedores */
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
    

    
}



   
    
