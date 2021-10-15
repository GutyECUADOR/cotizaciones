<?php namespace App\Controllers;
use App\Models\RouteModel;

class RouteController {
    
    public $routeModel;
    
    public function __construct() {
        $this->defaulDataBase = (!isset($_SESSION["empresaAUTH".APP_UNIQUE_KEY])) ? DEFAULT_DBName : $_SESSION["empresaAUTH".APP_UNIQUE_KEY] ;
        $this->routeModel = new RouteModel();
        $this->routeModel->setDbname($this->defaulDataBase);
        $this->routeModel->conectarDB();
    }
    
    public function loadtemplate(){
        include 'views/baseTemplate.php';
    }
    
    public function actionCatcherController(){
        if (isset($_GET['action'])){
           $action = $_GET['action'];
           $modulo = $this->routeModel->actionCatcherModel($action);
           
           include $modulo; 
        }else{
           $action = 'default';
           $modulo = $this->routeModel->actionCatcherModel($action);
           include $modulo; 
        }
       
    }

    public function getMenus(){
        return $this->routeModel->getMenus();
    }
}
