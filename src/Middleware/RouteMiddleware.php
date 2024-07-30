<?php namespace App\Middleware;

class RouteMiddleware {

    public $ultimoDiaVales;
    public $ultimoDiaEvaluaEGG;
    public $finMantenimiento;
    public $modulos;

    public function __construct() {
        $this->loadXML();
    }

    public function loadXML(){
        if(file_exists("./config/configuraciones.xml") && $XML = simplexml_load_file('./config/configuraciones.xml')){
            $this->ultimoDiaVales = (string) $XML->variables->xpath('variable[@id="ultimoDiaVales"]')[0]->valor;
          
            //echo "carga correcta".  $ultimoDiaValep;
        }else{
            throw new \Exception("Error no se pudo cargar el archivo de configuraciones XML, informe a sistemas.");
           
        }
    }

    public function checkisLogin(){
        if (!isset($_SESSION["usuarioRUC".APP_UNIQUE_KEY])){
            $preaction = $_GET['action'];
            header("Location:index.php?&action=login&preaction=$preaction");  
        }

        $this->hasPermissionToAction();
    }

    public function checkIsSupervidor(){
        if (!$_SESSION["isSupervisor".APP_UNIQUE_KEY] == 1){
            header("Location:index.php?&action=inicio");  
        }
    }

    public function isImportKAO(){
        $importkaoDBs = array("IMPORKAO_V7", "MODELOIMPK_V7", "IMPORKAO_2023", "MODELOIMPK");
        if (!in_array(trim($_SESSION["empresaAUTH".APP_UNIQUE_KEY]), $importkaoDBs)){
            header("Location:index.php?&action=inicio");  
        }
    }


    public function hasPermissionToAction(){
        if (!in_array(trim($_GET['action']), $_SESSION["arrayModulosAccess".APP_UNIQUE_KEY])) {
            header("Location:index.php?&action=inicio");
        }
    }

    public function validateAccessVales() {
        $fechaActual = date('Y-m-d');
        $fechainicio = $this->first_month_day();
        $fechafinal = $this->ultimo_dia_vales($this->ultimoDiaVales);
        return (($fechaActual >= $fechainicio) && ($fechaActual <= $fechafinal));
    }
    

    public function check_in_range($start_date, $end_date, $evaluame) {
        $start_ts = strtotime($start_date);
        $end_ts = strtotime($end_date);
        $user_ts = strtotime($evaluame);
        return (($user_ts >= $start_ts) && ($user_ts <= $end_ts));
    }
        
    
    public function ultimo_dia_vales($ultimoDiaVales=30){
        $year = date('Y');
        $month = date('m');
        $day = $ultimoDiaVales;
        return date('Y-m-d', mktime(0,0,0, $month, $day, $year));
    } 

    function last_month_day() { 
        $month = date('m');
        $year = date('Y');
        $day = date("d", mktime(0,0,0, $month+1, 0, $year));
   
        return date('Y-m-d', mktime(0,0,0, $month, $day, $year));
  }
  
    public function first_month_day() {
            $month = date('m');
            $year = date('Y');
            return date('Y-m-d', mktime(0,0,0, $month, 1, $year));
    }
}