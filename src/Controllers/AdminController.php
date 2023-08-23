<?php namespace App\Controllers;

use App\Models\AdminModel;
use App\Models\WinfenixModel;

class AdminController  {

    public $defaulDataBase;
    public $adminModel;
    public $ventasModel;
    public $winfenixModel;

    public function __construct() {
        $this->defaulDataBase = (!isset($_SESSION["empresaAUTH".APP_UNIQUE_KEY])) ? DEFAULT_DBName : $_SESSION["empresaAUTH".APP_UNIQUE_KEY] ;
        $this->adminModel = new AdminModel();
        $this->adminModel->setDbname($this->defaulDataBase);
        $this->adminModel->conectarDB();
        $this->winfenixModel = new WinfenixModel();
        $this->winfenixModel->setDbname($this->defaulDataBase);
        $this->winfenixModel->conectarDB();
    }

    public function getPerfiles(){
        $response  = $this->winfenixModel->SQL_getUSUGRUPOS();
        return $response;
    }

    public function getAccesosPerfil(string $id){
        $response  = $this->adminModel->getAccesosPerfil($id);
        return $response;
    }

    public function getModulos(object $busqueda){
        $response  = $this->adminModel->getModulos($busqueda);
        return $response;
    }

    public function addNewPermiso(object $permiso){
        $response  = $this->adminModel->addNewPermiso($permiso);
        return $response;
    }

    public function removePermiso(object $permiso){
        $response  = $this->adminModel->removePermiso($permiso);
        return $response;
    }

    public function getVariables(){
        $XML_path = "../../config/configuraciones.xml";
        $XML = simplexml_load_file($XML_path);
        $data = $XML->variables;
        return array('status'=> 'OK', "message"=>'XML Cargado', "data"=>$data);
    }

    public function updateVariable($variable){
        $XML_path = "../../config/configuraciones.xml";
        $XML_file = simplexml_load_file($XML_path);
        // update
        $nodo = $XML_file->variables->xpath('variable[@id="'.$variable->id.'"]');
        var_dump($nodo);
        $nodo[0]->valor = $variable->valor;
        // save the updated document
            $XML_file->asXML($XML_path);
        return array("status"=>'OK', "message"=>'XML Actualizado');
    }

  


    
    

}
