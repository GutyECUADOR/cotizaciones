<?php namespace App\Models;

class LoginModel extends Conexion {
 
    public function __construct() {
        parent::__construct();
    }

    public function validaIngreso($arrayDatos, $dataBaseName){
        $this->setDbname($dataBaseName);
        $this->conectarDB();
        
        $usuario = $arrayDatos['usuario'];
        $password = $arrayDatos['password'];

        $query = "
        SELECT 
            Codigo,
            Grupo,
            Nombre,
            Supervisor,
            CONVERT(VARCHAR(20),DECRYPTBYPASSPHRASE('OnlySoft2009*',xPassWord)) As xPassWord,
            CONVERT(VARCHAR(20),DECRYPTBYPASSPHRASE('IsaacBetito',xPassWord)) As xPassWord1 
        FROM USUARIOS WITH(NOLOCK)
        WHERE Codigo = :codigo AND CONVERT(VARCHAR(20),DECRYPTBYPASSPHRASE('OnlySoft2009*',xPassWord)) = :password
            OR Codigo = :codigo1 AND CONVERT(VARCHAR(20),DECRYPTBYPASSPHRASE('IsaacBetito',xPassWord)) = :password1
        ORDER BY codigo
        
        ";
        $stmt = $this->instancia->prepare($query); 
        $stmt->bindParam(':codigo', $usuario); 
        $stmt->bindParam(':password', $password); 
        $stmt->bindParam(':codigo1', $usuario); 
        $stmt->bindParam(':password1', $password); 
       
            if($stmt->execute()){
                $resulset = $stmt->fetch( \PDO::FETCH_ASSOC );
            }else{
                $resulset = false;
            }

        return $resulset;
           
        
    }

    public function validaTramacoKEYS($codigoDB){

        $query = "
       
        SELECT TOP 1 * FROM wssp.dbo.usuarios_tramaco WHERE empresa = :empresa
 
        ";
        $stmt = $this->instancia->prepare($query); 
        $stmt->bindParam(':empresa', $codigoDB); 
        
    
            if($stmt->execute()){
                $resulset = $stmt->fetch( \PDO::FETCH_ASSOC );
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
        $query = "
            SELECT nombre, dbname FROM wssp.dbo.databases_info WHERE activo = 1 order by nombre ASC
        "; 
        $stmt = $this->instancia->prepare($query); 
            if($stmt->execute()){
              return $stmt->fetchAll( \PDO::FETCH_ASSOC);
            }else{
                $resulset = false;
            }
        return $resulset;  
    }
    
}
