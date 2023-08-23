<?php  namespace App\Models;

class LoginModel extends Conexion {
 
    public function __construct() {
        parent::__construct();
    }


    public function validaIngreso($arrayDatos, $dataBaseName='MODELO'){

        $this->setDbname($dataBaseName);
        $this->conectarDB();
        
        $usuario = $arrayDatos['usuario'];
        $password = $arrayDatos['password'];

        $query = "SELECT TOP 1 * FROM dbo.USUARIOS WHERE Codigo = :cedula"; 
        $stmt = $this->instancia->prepare($query); 
        $stmt->bindParam(':cedula', $usuario); 
    
            if($stmt->execute()){
                $resulset = $stmt->fetch( \PDO::FETCH_ASSOC );
            }else{
                $resulset = false;
            }

        return $resulset;
           
        
    }


    public function validaMail($mail){
        $query = "SELECT ruc, nombre, email, password FROM tbl_cliente WHERE email = :mail"; 
        $stmt = $this->instancia->prepare($query); 
        $stmt->bindParam(':mail', $mail); 
        $stmt->execute(); 
       
        $resulset = $stmt->fetch();
        return $resulset;
    }


    /* Retorna el nombre array con la clave NameDatabase para el nombre de la DB, para ser usada en la conexion*/ 
    public function getDBNameByCodigo($codigoDB){
        $query = "SELECT TOP 1 NameDatabase, Codigo FROM SBIOKAO.dbo.Empresas_WF WHERE Codigo = :codigo"; 
        $stmt = $this->instancia->prepare($query); 
        $stmt->bindParam(':codigo', $codigoDB); 
       
            if($stmt->execute()){
                $resulset = $stmt->fetch( \PDO::FETCH_ASSOC );
            }else{
                $resulset = false;
            }
        return $resulset;  
    }

    /* Retorna el nombre array con la clave NameDatabase y Codigo para el nombre de la DB, para ser usada en la conexion*/ 
    public function getCodeDBByName($nombreDB){
        $query = "SELECT TOP 1 NameDatabase, Codigo FROM SBIOKAO.dbo.Empresas_WF WHERE NameDatabase = :NameDatabase"; 
        $stmt = $this->instancia->prepare($query); 
        $stmt->bindParam(':NameDatabase', $nombreDB); 
    
            if($stmt->execute()){
                $resulset = $stmt->fetch( \PDO::FETCH_ASSOC );
            }else{
                $resulset = false;
            }
        return $resulset;  
    }

    /* Retorna el nombre array con la clave Codigo, Nombre para el nombre de la DB, para ser usada en la conexion*/ 
    public function getAllDataBaseList(){
        $query = "SELECT nombre, dbname FROM wssp.dbo.databases_info WHERE activo ='1'"; 
        $stmt = $this->instancia->prepare($query); 
            if($stmt->execute()){
              return $stmt->fetchAll( \PDO::FETCH_ASSOC);
            }else{
                $resulset = false;
            }
        return $resulset;  
    }

     /* Retorna el nombre array con la lista de rutas de los modulos disponibles para el tipo de usuario*/ 
     public function getModulosAccess($usuarioWinf){
        $query = "
            SELECT 
                sys_menus.action 
            FROM 
                wssp.dbo.sys_permisos as permisos
                INNER JOIN wssp.dbo.sys_menus on sys_menus.id = permisos.id_menu
            WHERE codigoGrupo IN (:codigoGrupo, :username)
            ORDER BY sys_menus.modulo
        "; 
        $stmt = $this->instancia->prepare($query); 
        $stmt->bindValue(':codigoGrupo', trim($usuarioWinf['Grupo'])); 
        $stmt->bindValue(':username', trim($usuarioWinf['Codigo'])); 
       
            if($stmt->execute()){
                $resulset = $stmt->fetchAll( \PDO::FETCH_COLUMN );
            }else{
                $resulset = false;
            }
        return $resulset;  
    }

    

    public function test(){
        return 'OK desde loginModel';
    }
}
