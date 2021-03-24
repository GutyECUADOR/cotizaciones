<?php namespace App\Models;

/* LOS MODELOS del MVC retornaran unicamente arrays PHP sin serializar*/

class winfenixModel extends Conexion  {
    
    public function __construct() {
        parent::__construct();
    }

    /*Retorna busqueda de productos */
    public function Sp_INVCONARTWAN(object $busqueda) {
        $query = "exec Sp_INVCONARTWAN :texto,'', :gestion,'N', :bodega,'', :cantidad,'0','0','1','99','','','','','','','','','','0'";
        $stmt = $this->instancia->prepare($query); 
        $stmt->bindValue(':texto', $busqueda->texto); 
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

    
}



   
    
