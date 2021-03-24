<?php namespace App\Controllers;

use App\Models\InventarioModel;
use App\Models\winfenixModel;

class InventarioController  {

    public $defaulDataBase;
    public $inventarioModel;

    public function __construct() {
        $this->defaulDataBase = (!isset($_SESSION["empresaAUTH".APP_UNIQUE_KEY])) ? DEFAULT_DBName : $_SESSION["empresaAUTH".APP_UNIQUE_KEY] ;
        $this->inventarioModel = new InventarioModel();
        $this->inventarioModel->setDbname($this->defaulDataBase);
        $this->inventarioModel->conectarDB();
        $this->winfenixModel = new winfenixModel();
        $this->winfenixModel->setDbname($this->defaulDataBase);
        $this->winfenixModel->conectarDB();
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

    function getCostoProducto(object $busqueda) {
        $response = $this->inventarioModel->getCostoProducto($busqueda);
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


}
