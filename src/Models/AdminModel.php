<?php namespace App\Models;

use App\Models\Conexion;

class AdminModel extends Conexion  {
    
    public function __construct() {
        parent::__construct();
    }

    public function getAccesosPerfil(string $id){
        $query = " 
            SELECT 
                permisos.codigoGrupo,
                sys_menus.id,
                sys_menus.nombre,
                sys_menus.modulo,
                sys_menus.lv_acceso 
            FROM 
                wssp.dbo.sys_permisos as permisos
                INNER JOIN wssp.dbo.sys_menus on sys_menus.id = permisos.id_menu
            WHERE codigoGrupo = :codigoGrupo
            ORDER BY sys_menus.modulo
        ";  
        try{
            $stmt = $this->instancia->prepare($query); 
            $stmt->bindParam(':codigoGrupo', $id);
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

    public function getModulos(object $busqueda){
        $texto = '%'.$busqueda->texto.'%';

        $query = " 
            SELECT TOP 100
                id,
                nombre,
                modulo,
                descripcion
            FROM wssp.dbo.sys_menus
            WHERE nombre LIKE :texto OR modulo LIKE :texto2
        ";  
        try{
            $stmt = $this->instancia->prepare($query); 
            $stmt->bindParam(':texto', $texto);
            $stmt->bindParam(':texto2', $texto);
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

    public function addNewPermiso(object $permiso){
        try{

            $this->instancia->beginTransaction();
            $query = " 
                INSERT INTO wssp.dbo.sys_permisos VALUES (:codigoGrupo, :id_menu)
            ";  

            $stmt = $this->instancia->prepare($query); 
            $stmt->bindParam(':codigoGrupo', $permiso->perfilID); 
            $stmt->bindParam(':id_menu', $permiso->moduloID);
            $stmt->execute();
            return $this->instancia->commit();
            
        }catch(\PDOException $exception){
            $this->instancia->rollBack();
            return array('status' => 'error', 'mensaje' => $exception->getMessage() );
        }
    }

    public function removePermiso(object $permiso){
        try{

            $this->instancia->beginTransaction();
            $query = " 
                DELETE FROM wssp.dbo.sys_permisos WHERE codigoGrupo = :codigoGrupo AND id_menu= :id_menu
            ";  

            $stmt = $this->instancia->prepare($query); 
            $stmt->bindParam(':codigoGrupo', $permiso->perfilID); 
            $stmt->bindParam(':id_menu', $permiso->moduloID);
            $stmt->execute();
            return $this->instancia->commit();
            
        }catch(\PDOException $exception){
            $this->instancia->rollBack();
            return array('status' => 'error', 'mensaje' => $exception->getMessage() );
        }
    }

    

   

    
}



   
    
