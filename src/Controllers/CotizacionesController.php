<?php namespace App\Controllers;

use App\Models\CotizacionesModel;
use App\Models\WinfenixModel;

class CotizacionesController  {

    private $defaulDataBase;
    private $model;
    private $winfenixModel;

    public function __construct() {
        $this->defaulDataBase = (!isset($_SESSION["empresaAUTH".APP_UNIQUE_KEY])) ? DEFAULT_DBName : $_SESSION["empresaAUTH".APP_UNIQUE_KEY] ;
        $this->model = new CotizacionesModel();
        $this->model->setDbname($this->defaulDataBase);
        $this->model->conectarDB();
        $this->winfenixModel = new WinfenixModel();
        $this->winfenixModel->setDbname($this->defaulDataBase);
        $this->winfenixModel->conectarDB();
    }

    
    public function getDocumentos(object $busqueda) {
        $response = $this->winfenixModel->sql_buscarDocumentos($busqueda);
        return $response;
    }

    public function getClientes(object $busqueda) {
        $response = $this->winfenixModel->SP_COBCONCLI($busqueda);
        return $response;
    }
    


}
