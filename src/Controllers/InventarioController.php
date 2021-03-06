<?php namespace App\Controllers;

use App\Models\InventarioModel;
use App\Models\WinfenixModel;

class InventarioController  {

    public $defaulDataBase;
    public $inventarioModel;

    public function __construct() {
        $this->defaulDataBase = (!isset($_SESSION["empresaAUTH".APP_UNIQUE_KEY])) ? DEFAULT_DBName : $_SESSION["empresaAUTH".APP_UNIQUE_KEY] ;
        $this->inventarioModel = new InventarioModel();
        $this->inventarioModel->setDbname($this->defaulDataBase);
        $this->inventarioModel->conectarDB();
        $this->winfenixModel = new WinfenixModel();
        $this->winfenixModel->setDbname($this->defaulDataBase);
        $this->winfenixModel->conectarDB();
    }

    public function searchDocumentos_EgresosIngresos(object $busqueda){
        $response  = $this->winfenixModel->sql_getIngresosEgresos($busqueda);
        return $response;
    }

    public function searchDocumentos_CreacionReceta(object $busqueda){
        $response  = $this->winfenixModel->sql_getCreacionReceta($busqueda);
        return $response;
    }

    public function searchProductos(object $busqueda){
        $response = $this->winfenixModel->Sp_INVCONARTWAN($busqueda);
        return $response;
    }

    function getProveedor(string $busqueda) {
        $response = $this->inventarioModel->getProveedor($busqueda);
        return $response;
    }

    function getProducto(string $busqueda) {
        $producto = $this->inventarioModel->getProducto($busqueda);
        $unidades_medida = $this->inventarioModel->getUnidadesMedida($busqueda);
        return array('producto' => $producto, 'unidades_medida' => $unidades_medida);
    }

    function getComposicionProducto(string $codigo) {
        $response = $this->inventarioModel->getComposicionProducto($codigo);
        return $response;
    }

    function getCostoProducto(object $busqueda) {
        $response = $this->inventarioModel->getCostoProducto($busqueda);
        return $response;
    }

    function getCantidadByFactor(object $busqueda) {
        $response = $this->inventarioModel->getCantidadByFactor($busqueda);
        return $response;
    }

    function getProductos(string $busqueda) {
        $response = $this->inventarioModel->getProductos($busqueda);
        return $response;
    }

    function saveInventario(object $documento) {
        $egreso = $this->inventarioModel->Winfenix_SaveEgreso($documento);
        $ingreso = $this->inventarioModel->Winfenix_SaveIngreso($documento);
      
        return  array('egreso'=> $egreso, 'ingreso'=> $ingreso);
    }

    function saveCreacionReceta(object $documento) {
        $response = $this->inventarioModel->Winfenix_SaveCreacionReceta($documento);
        return $response;
    }

    function saveCostoTeorico(object $documento) {
        $response = $this->inventarioModel->WSSP_SaveCostoTeorico($documento);
        return $response;
    }

    function saveTransformacionKITS(object $documento) {
        $response = $this->inventarioModel->Winfenix_saveTransformacionKITS($documento);
        return $response;
    }

    


}
