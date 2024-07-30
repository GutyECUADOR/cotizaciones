<?php namespace App\Controllers;
use App\Models\EstadoVehiculoModel;

class VehiculoController  {

    public $defaulDataBase;
    public $vehiculoModel;

    public function __construct() {
        $this->defaulDataBase = $_SESSION["empresaAUTH".APP_UNIQUE_KEY];
        $this->vehiculoModel = new EstadoVehiculoModel();
        $this->vehiculoModel->setDbname($this->defaulDataBase);
        $this->vehiculoModel->conectarDB();
    }
    
}