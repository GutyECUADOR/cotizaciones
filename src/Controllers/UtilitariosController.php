<?php namespace App\Controllers;
use App\Models\UtilitariosModel;

class UtilitariosController  {

    public $defaulDataBase;
    public $ajaxModel;
   
    public function __construct() {
        $this->defaulDataBase = (!isset($_SESSION["empresaAUTH".APP_UNIQUE_KEY])) ? DEFAULT_DBName : $_SESSION["empresaAUTH".APP_UNIQUE_KEY];
        $this->ajaxModel = new UtilitariosModel();
        $this->ajaxModel->setDbname($this->defaulDataBase);
        $this->ajaxModel->conectarDB();

    }
    

    public function getInfoInitForm_cargaNuevosClientes(){
        $databases = $this->ajaxModel->getDataBases();
        return array('databases'=> $databases);
    }

    public function getProducto(string $codigo){
        //$response = $this->ajaxModel->getProducto($codigo);
        return false;
    }

    public function getProductosSinEAN(object $busqueda){
        $response = $this->ajaxModel->getProductosSinEAN($busqueda);
        return $response;
    }

    public function getNuevoEAN13(){
       
        $contador = $this->ajaxModel->SQL_getContadorEAN();

        if (!$contador) {
            throw new \Exception("No se pudo obtener el contador para la base de datos", 404);
        }
        
        do {
            $nuevoCodigoEAN = $this->genrateEAN13($contador);
            $existeEAN = $this->ajaxModel->verificaExisteEAN13($nuevoCodigoEAN);
            $contador++;
        } while ($existeEAN == true);

        return  array('nuevoCodigoEAN' => $nuevoCodigoEAN, 'contador' => $contador);
    }

    public function saveNuevoEAN13(object $producto){
       
      
        $nuevoCodigoEAN = $this->getNuevoEAN13();

        if (trim($nuevoCodigoEAN['nuevoCodigoEAN']) != trim($producto->codalt)) {
            throw new \Exception("El contador de codigo EAN ha cambiado, genere nuevo codigo EAN: ". 'EAN contador: '. $nuevoCodigoEAN['nuevoCodigoEAN'] . ' EAN producto: '.$producto->codalt);
        }

        $existeEAN = $this->ajaxModel->verificaExisteEAN13($producto->codalt);
        if ($existeEAN) {
            throw new \Exception("El codigo EAN ya existe en el producto". $existeEAN['Nombre'], 404);
        }
        
        $response = $this->ajaxModel->SQL_saveNuevoEAN13($producto, $nuevoCodigoEAN['contador']);

        return $response;
    }

    public function genrateEAN13(string $number, string $prefix = '200'): string {
        if (strlen($number) + strlen($prefix) > 12) {
            throw new \Exception('Valor de EAN demasiado largo');
        }

        $code = $prefix . str_pad($number, 12 - strlen($prefix), '0', STR_PAD_LEFT);
        $weightFlag = true;
        $sum = 0;

        for ($i = strlen($code) - 1; $i >= 0; $i--) {
            $sum += (int)$code[$i] * ($weightFlag ? 3 : 1);
            $weightFlag = !$weightFlag;
        }

        $code .= (10 - ($sum % 10)) % 10;

        return $code;
    }

    public function save_cargaNuevosClientes(array $productos, string $database){
        $utilitariosModel = new UtilitariosModel();
        $utilitariosModel->setDbname($database);
        $utilitariosModel->conectarDB();
        $response = $utilitariosModel->save_cargaNuevosClientes($productos);
       
        return $response;
    }

   
    
   


}
