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

    public function getCliente(string $RUC){
        $response = $this->model->SQL_getCliente($RUC);
        return $response;
    }

    public function saveNuevoCliente(object $nuevoCliente){
        $newCodigo = $this->model->getNextNumClienteWF();
        return $this->model->insertNuevoCliente($nuevoCliente, $newCodigo);
    }

    public function getProductos(object $busqueda){
        return $this->winfenixModel->Sp_INVCONARTWAN($busqueda);
    }

    public function getProducto(string $codigo) {
        return $this->model->getProducto($codigo);
    }

    public function getStock(object $busqueda) {
        $response = array('stock' => $this->model->SQL_getStock($busqueda->texto),
                          'stockComponentes' => $this->model->SQL_getStockComponentes($busqueda),
                          'stockRetazos' => $this->model->SQL_getStockRetazos($busqueda->texto)
                        );
        return $response; 
    }

    public function getBodegas(){
        $bodegas =  $this->winfenixModel->sql_getBodegasWF();
        return $bodegas;
    }

    public function getVendedores(){
        $bodegas =  $this->winfenixModel->sql_getVendedoresWF();
        return $bodegas;
    }

    public function getFormasPago(){
        $response =  $this->winfenixModel->sql_getFormasPagoWF();
        return $response;
    }

    public function getVenTiposDOCWF(){
        $response =  $this->winfenixModel->sql_getVenTiposDOCWF();
        return $response;
    }

    public function getTiposPagoTarjeta(){
        $response =  $this->winfenixModel->sql_getTiposPagoTarjetaWF();
        return $response;
    }

    public function getGrupos(){
        $response =  $this->winfenixModel->sql_getGruposClientesWF();
        return $response;
    }

    public function getCantones(){
        $response =  $this->winfenixModel->sql_getCantonesWF();
        return $response;
    }

    public function getVENCAB(string $ID){
        $response =  $this->winfenixModel->SQL_getVENCAB($ID);
        return $response;
    }

    public function getVENMOV(string $ID){
        $response =  $this->winfenixModel->SQL_getVENMOV($ID);
        return $response;
    }

    public function saveCotizacion(object $documento){
        $response =  $this->winfenixModel->SP_VENGRACAB($documento);
        return $response;

    }
    


}
