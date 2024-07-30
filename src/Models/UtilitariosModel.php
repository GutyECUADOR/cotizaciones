<?php namespace App\Models;

/* LOS MODELOS del MVC retornaran unicamente arrays PHP sin serializar*/

class UtilitariosModel extends Conexion  {
    
    public function __construct() {
        parent::__construct();
    }

    public function getDataBases() {
        $query = "SELECT * FROM KAO_wssp.dbo.databases_info WHERE activo = 1 ORDER BY nombre";
        $stmt = $this->instancia->prepare($query); 
        $stmt->execute();
            if($stmt->execute()){
                $resulset = $stmt->fetchAll( \PDO::FETCH_ASSOC);
            }else{
                $resulset = false;
            }
        return $resulset;  

    }

    public function getNextNumClienteWF(){
        try{
            $stmt = $this->instancia->prepare("SELECT TOP 1 CODIGO +1 as NEXTCODIGO FROM dbo.COB_CLIENTES ORDER BY CODIGO DESC"); 
            $stmt->execute();
          
            $newCodLimpio = $stmt->fetch(\PDO::FETCH_ASSOC);
            $newCodLimpio =  $newCodLimpio['NEXTCODIGO'];
            
            $newCod = $this->instancia->query("select RIGHT('00000000' + Ltrim(Rtrim('$newCodLimpio')),8) as newcod");
            $codigoConFormato = $newCod->fetch(\PDO::FETCH_ASSOC);
            $codigoConFormato = $codigoConFormato['newcod'];
            return $codigoConFormato;

        }catch(\PDOException $exception){
            return array('status' => 'ERROR', 'message' => $exception->getMessage() );
        }

    }

    public function getNextNumCodClienteWF(){
        try{
            $stmt = $this->instancia->prepare("SP_CONTADOR_TABLA 'Cob_clientes'"); 
            $stmt->execute();
            
            $newCodLimpio = $stmt->fetch(\PDO::FETCH_ASSOC);
            $newCodLimpio =  $newCodLimpio['NEXTID'];
            
            $newCod = $this->instancia->query("select RIGHT('00000000' + Ltrim(Rtrim('$newCodLimpio')),8) as newcod");
            $codigoConFormato = $newCod->fetch(\PDO::FETCH_ASSOC);
            $codigoConFormato = $codigoConFormato['newcod'];
            return $codigoConFormato;

        }catch(\PDOException $exception){
            return array('status' => 'ERROR', 'message' => $exception->getMessage() );
        }

    }

    /*Carga Nuevos Clientes */
    public function save_cargaNuevosClientes(array $clientes) {

        set_time_limit(36500);
        try{
            $this->instancia->beginTransaction();
            $contador_actualizados = 0;
            $contador_creados = 0;
          
            foreach ($clientes as $cliente) {

                // Verificacion si existe cliente by RUC
                $query = "SELECT CODIGO, NOMBRE, RUC FROM COB_CLIENTES WITH(NOLOCK) WHERE RUC = :RUC";
                $stmt = $this->instancia->prepare($query); 
                $stmt->bindParam(':RUC', $cliente->RUC); 
                $stmt->execute();
                $resulset_cliente = $stmt->fetch( \PDO::FETCH_ASSOC);

                if ($resulset_cliente) {
                    //Actualizar
                    $query = " 
                        UPDATE COB_CLIENTES SET
                            OFICINA = :oficina,
                            NOMBRE = :nombre,
                            VENDEDOR = :vendedor,
                            GRUPO = :grupo,
                            CONTACTO = :contacto,
                            EMPRESA = :empresa,
                            DIRECCION1 = :direccion1,
                            TELEFONO1 = :telefono1,
                            EMAIL = :email,
                            FPAGO = :fpago,
                            DIASPAGO = :diaspago,
                            IDIOMA = :idioma,
                            DIVISA = :divisa,
                            PAIS = :pais,
                            PROVINCIA = :provincia,
                            CANTON = :canton,
                            fpagodefault = :fpagodefault,
                            Transportista = :Transportista,
                            tipoident = :tipoident
                        WHERE 
                            RUC = :RUC
                    "; 

                    $stmt = $this->instancia->prepare($query); 
                    $stmt->bindValue(':oficina', '99');
                    $stmt->bindValue(':nombre', $cliente->nombre);
                    $stmt->bindValue(':vendedor', $cliente->vendedor);
                    $stmt->bindValue(':grupo', $cliente->grupo);
                    $stmt->bindValue(':contacto', $cliente->contacto);
                    $stmt->bindValue(':empresa', $cliente->empresa);
                    $stmt->bindValue(':direccion1', $cliente->direccion1);
                    $stmt->bindValue(':telefono1', $cliente->telefono1);
                    $stmt->bindValue(':email', $cliente->email);
                    $stmt->bindValue(':fpago', $cliente->fPago);
                    $stmt->bindValue(':diaspago', $cliente->diasPago);
                    $stmt->bindValue(':idioma', $cliente->idioma);
                    $stmt->bindValue(':divisa', $cliente->divisa);
                    $stmt->bindValue(':pais', $cliente->pais);
                    $stmt->bindValue(':provincia', $cliente->provincia);
                    $stmt->bindValue(':canton', $cliente->canton);
                    $stmt->bindValue(':fpagodefault', $cliente->fpagodefault);
                    $stmt->bindValue(':Transportista', $cliente->Transportista);
                    $stmt->bindValue(':tipoident', $cliente->tipoIdent);
                   
                    $stmt->bindValue(':RUC', $cliente->RUC); 
                    $stmt->execute();
                    $contador_actualizados++;
                }else {
                    //Insertar
                    $newCodigo = $this->getNextNumClienteWF();
                    $newCodCliente = $this->getNextNumCodClienteWF();
                  
                    
                    $query = " 
                    INSERT INTO dbo.COB_Clientes 
                        (OFICINA,CODIGO,NOMBRE,VENDEDOR,GRUPO,FECHAALTA,RUC ,EMPRESA, DIRECCION1, FPAGO, NUMPAG, IDIOMA, DIVISA, PAIS, PROVINCIA, CANTON, fpagodefault, Transportista, CLIENTE, TIPOIDENT) 
                        VALUES (:OFICINA, :CODIGO, :NOMBRE, :VENDEDOR, :GRUPO, :FECHAALTA, :RUC, :EMPRESA, :DIRECCION1, :FPAGO, :NUMPAG, :IDIOMA, :DIVISA, :PAIS, :PROVINCIA, :CANTON, :fpagodefault, :Transportista, :CLIENTE, :TIPOIDENT
                        )	
                    "; 
                        
                        $stmt = $this->instancia->prepare($query); 
                        $stmt->bindValue(':OFICINA', '99');
                        $stmt->bindValue(':CODIGO', $newCodigo); // DE DONDE SALE?
                        $stmt->bindValue(':NOMBRE', $cliente->nombre);
                        $stmt->bindValue(':VENDEDOR', $cliente->vendedor);
                        $stmt->bindValue(':GRUPO', $cliente->grupo);
                        $stmt->bindValue(':FECHAALTA', date("Ymd", strtotime($cliente->fechaAlta)));
                        $stmt->bindValue(':RUC', trim($cliente->RUC));
                        $stmt->bindValue(':EMPRESA', $cliente->empresa);
                        $stmt->bindValue(':DIRECCION1', $cliente->direccion1);
                        $stmt->bindValue(':FPAGO', $cliente->fPago);
                        $stmt->bindValue(':NUMPAG', $cliente->numPag);
                        $stmt->bindValue(':IDIOMA', $cliente->idioma);
                        $stmt->bindValue(':DIVISA', $cliente->divisa);
                        $stmt->bindValue(':PAIS', $cliente->pais);
                        $stmt->bindValue(':PROVINCIA', $cliente->provincia);
                        $stmt->bindValue(':CANTON', $cliente->canton);
                        $stmt->bindValue(':fpagodefault', $cliente->fpagodefault);
                        $stmt->bindValue(':Transportista', $cliente->Transportista);
                        $stmt->bindValue(':TIPOIDENT', $cliente->tipoIdent);
                        

                        $stmt->bindValue(':CLIENTE', $newCodCliente);
                        
                    $stmt->execute();
                    $contador_creados++;
                }

            }
            
            $commit = $this->instancia->commit();
            $response = array('status' => 'OK', 'message' => 'Proceso completado', 'commit' => $commit, 'creados' => $contador_creados, 'actualizados' => $contador_actualizados );
            return $response;
            
        }catch(\PDOException $exception){
            $this->instancia->rollBack();
            return array('status' => 'error', 'message' => $exception->getMessage(), 'id'=> $cliente->RUC );
        }
   
    }

    public function getProductosSinEAN(object $busqueda) {
        $query = "SELECT * FROM dbo.INV_ARTICULOS WHERE CodAlt LIKE ''";
        $stmt = $this->instancia->prepare($query); 
        $stmt->execute();
            if($stmt->execute()){
                $resulset = $stmt->fetchAll( \PDO::FETCH_ASSOC);
            }else{
                $resulset = false;
            }
        return $resulset;  

    }

    public function SQL_getContadorEAN() {
        $query = "SELECT valor FROM KAO_wssp.dbo.contadores WHERE nombre = 'EAN13' AND empresa = :empresa";
        $stmt = $this->instancia->prepare($query); 
        $stmt->bindParam(':empresa', $_SESSION["empresaAUTH".APP_UNIQUE_KEY]); 
        $stmt->execute();
            if($stmt->execute()){
                $resulset = $stmt->fetch( \PDO::FETCH_ASSOC)['valor'];
            }else{
                $resulset = false;
            }
        return $resulset;  

    }

    public function SQL_setContadorEAN($contador) {
        $query = "
            UPDATE KAO_wssp.dbo.contadores 
            SET valor = :contador
            WHERE nombre = 'EAN13' AND empresa = :empresa
        ";
        $stmt = $this->instancia->prepare($query); 
        $stmt->bindParam(':contador', $contador); 
        $stmt->bindParam(':empresa', $_SESSION["empresaAUTH".APP_UNIQUE_KEY]); 
            if($stmt->execute()){
                $resulset = true;
            }else{
                $resulset = false;
            }
        return $resulset;  

    }

    public function SQL_saveNuevoEAN13(object $producto, $contador) {
        try{
            $this->instancia->beginTransaction();

            $this->SQL_setContadorEAN($contador);
            
            $query = "
                UPDATE dbo.INV_ARTICULOS 
                SET CodAlt = :codalt
                WHERE Codigo = :codigo
            ";
            $stmt = $this->instancia->prepare($query); 
            $stmt->bindParam(':codalt', $producto->codalt); 
            $stmt->bindParam(':codigo', $producto->codigo); 
            $stmt->execute();
           
            return $this->instancia->commit();
            
        }catch(\PDOException $exception){
            $this->instancia->rollBack();
            return array('status' => 'error', 'message' => $exception->getMessage() );
        }
    }

    public function verificaExisteEAN13(string $codigoEAN) {
        $query = "SELECT * FROM dbo.INV_ARTICULOS WHERE CodAlt = :CodAlt";
        $stmt = $this->instancia->prepare($query); 
        $stmt->bindParam(':CodAlt', $codigoEAN); 
            if($stmt->execute()){
                $resulset = $stmt->fetchAll( \PDO::FETCH_ASSOC);
            }else{
                $resulset = false;
            }
        return $resulset;  

    }

    

    

    
}



   
    