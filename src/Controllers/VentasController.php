<?php namespace App\Controllers;

use App\Models\VentasModel;
use App\Models\WinfenixModel;

class VentasController  {

    public $defaulDataBase;
    public $ventasModel;
    public $winfenixModel;

    public function __construct() {
        $this->defaulDataBase = (!isset($_SESSION["empresaAUTH".APP_UNIQUE_KEY])) ? DEFAULT_DBName : $_SESSION["empresaAUTH".APP_UNIQUE_KEY] ;
        $this->ventasModel = new VentasModel();
        $this->ventasModel->setDbname($this->defaulDataBase);
        $this->ventasModel->conectarDB();
        $this->winfenixModel = new WinfenixModel();
        $this->winfenixModel->setDbname($this->defaulDataBase);
        $this->winfenixModel->conectarDB();
    }

    public function getInformeComisionesVendedores(object $busqueda){
        $response  = $this->winfenixModel->SP_VENINFCOMADF($busqueda);
        return $response;
    }

   

    


}
