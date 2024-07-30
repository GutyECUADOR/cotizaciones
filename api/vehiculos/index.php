<?php
use App\Models\EstadoVehiculoModel;
use Dotenv\Dotenv;

header('Content-Type: application/json');
date_default_timezone_set('America/Lima');
session_start();

require_once '../../vendor/autoload.php';
$dotenv = Dotenv::createImmutable('../../');
$dotenv->load();

class ajax{
  private $ajax;
  
    public function __construct() {
      $this->ajax = new EstadoVehiculoModel();
    }

    public function getDocumentos(string $fechaINI, string $fechaFIN, string $busqueda) {
      return $this->ajax->getDocumentos($fechaINI,  $fechaFIN, $busqueda);
    }

    public function getAllProductos($busqueda){
      return $this->ajax->getAllProductos($busqueda);
    }
    
    public function getProducto($busqueda){
      return $this->ajax->getProducto($busqueda);
    }

    public function getEmpleadoByID($cedula){
      return $this->ajax->getEmpleadoByID($cedula);
    }

    public function getVehiculoByPlaca($placa){
      return $this->ajax->getVehiculoByPlaca($placa);
    }

    public function saveWinfenixCOM($solicitud){
      return $this->ajax->saveWinfenixCOM_CAB($solicitud);
    }

    public function saveSolicitud($solicitud){
      return $this->ajax->saveSolicitud($solicitud);
    }

    public function saveOrdenPedido($solicitud){
      return $this->ajax->saveOrdenPedido($solicitud);
    }

    public function saveNewProduct($producto){
      return $this->ajax->saveNewProduct($producto);
    }

    public function getAllVehiculos($busqueda){
      return $this->ajax->getAllVehiculos($busqueda);
    }

    public function aprobarOrden($codOrden){
      return $this->ajax->aprobarOrden($codOrden);
    }

    public function sendEmailWithOrden($mail, $IDDocument){
      return $this->ajax->sendEmail($IDDocument, $mail, true);
    }

    public function canDoPago($codOrden){
      return $this->ajax->canDoPago($codOrden);
    }

    public function getInfoProveedor($RUC){
      return $this->ajax->getInfoProveedor($RUC);
    }

    public function getInfoProducto($codigoProducto) {
      return $this->ajax->getInfoProducto($codigoProducto);
    }

    public function searchProducto($termino) {
      return $this->ajax->searchProducto($termino);
    }

    

    public function getProveedoresWinfenix($busqueda, $tipo){
      return $this->ajax->getProveedoresWinfenix($busqueda, $tipo);
    }

}

  try{
    $ajax = new ajax();
    $HTTPaction = isset($_GET["action"]) ? $_GET["action"] : '';

    switch ($HTTPaction) {

      case 'getDocumentos':
        if (isset($_GET['busqueda']) ) {
          $busqueda = json_decode($_GET['busqueda']);
          $fechaINI = date("Ymd", strtotime($busqueda->fechaINI));
          $fechaFIN = date("Ymd", strtotime($busqueda->fechaFIN));
          $texto = $busqueda->texto;

          $respuesta = $ajax->getDocumentos($fechaINI,  $fechaFIN, $texto);
          http_response_code(200);
          $rawdata = array('status' => 'OK', 'mensaje' => 'respuesta correcta', 'data' => $respuesta);
        }else{
          http_response_code(400);
          $rawdata = array('status' => 'ERROR', 'mensaje' => 'No se ha indicado parámetros.');
        }
        
        echo json_encode($rawdata);

      break;

        case 'searchProductos':
          if (isset($_GET['busqueda'])) {
            $busqueda = $_GET['busqueda'];
            $respuesta = $ajax->getAllProductos($busqueda);
            $rawdata = array('status' => 'OK', 'message' => 'respuesta correcta', 'data' => $respuesta);
          }else{
            http_response_code(400);
            $rawdata = array('status' => 'ERROR', 'message' => 'No se ha indicado parámetros.');
          }
          
          echo json_encode($rawdata);

        break;

        case 'getProducto':
          if (isset($_GET['busqueda'])) {
            $busqueda = $_GET['busqueda'];
            $respuesta = $ajax->getProducto($busqueda);
            $rawdata = array('status' => 'OK', 'message' => 'respuesta correcta', 'data' => $respuesta);
          }else{
            http_response_code(400);
            $rawdata = array('status' => 'ERROR', 'message' => 'No se ha indicado parámetros.');
          }
          
          echo json_encode($rawdata);

        break;
      
        case 'getEmpleadoByID':
          $busqueda = json_decode($_GET['busqueda']);
          $cedula = $busqueda->cedula;
          $respuesta = $ajax->getEmpleadoByID($cedula);
          $rawdata = array('error' => FALSE, 'message' => 'respuesta correcta', 'data' => $respuesta);
          echo json_encode($rawdata);
        break;

        case 'getVehiculoByPlaca':
          $busqueda = json_decode($_GET['busqueda']);
          $placa = $busqueda->placa;
          $respuesta = $ajax->getVehiculoByPlaca($placa);
          $rawdata = array('error' => FALSE, 'message' => 'respuesta correcta', 'data' => $respuesta);
          echo json_encode($rawdata);
        break;

        case 'getAllVehiculos':
          $busqueda = $_GET["busqueda"];
          $respuesta = $ajax->getAllVehiculos($busqueda);
          $rawdata = array('error' => FALSE, 'message' => 'respuesta correcta', 'data' => $respuesta);
          echo json_encode($rawdata);
        break;

        case 'saveWinfenixCOM':
          if (isset($_POST['solicitud'])) {
            $formDataObject = json_decode($_POST['solicitud']);
         
            $respuesta = $ajax->saveWinfenixCOM($formDataObject);
            echo json_encode($respuesta);
            }
          break;

        case 'saveSolicitud':
          if (isset($_POST['solicitud'])) {
            $formDataObject = json_decode($_POST['solicitud']);
            $respuesta = $ajax->saveSolicitud($formDataObject);
            $rawdata = $respuesta;
            echo json_encode($rawdata);
            }
          break;

        case 'saveOrdenPedido':
          if (isset($_POST['solicitud'])) {
            $formDataObject = json_decode($_POST['solicitud']);
            $respuesta = $ajax->saveOrdenPedido($formDataObject);
            $rawdata = $respuesta;
            echo json_encode($rawdata);
            }
          break;

        case 'saveNewProduct':
          if (isset($_POST['producto'])) {
            $formDataObject = json_decode($_POST['producto']);
            $rawdata = $ajax->saveNewProduct($formDataObject);
            echo json_encode($rawdata);
            }
          break;

        case 'aprobarOrden':
          if (isset($_GET['codOrden'])) {
            $codOrden = $_GET['codOrden'];
            $respuesta = $ajax->aprobarOrden($codOrden);
            $rawdata = array('error' => FALSE, 'message' => 'Aprobacion realizada', 'data' => $respuesta);
            echo json_encode($rawdata);
            }
          break;
        
        case 'sendOrden':

          if (isset($_GET['IDDocument']) && isset($_GET['email']) ) {
            $IDDocument = $_GET['IDDocument'];
            $email = $_GET['email'];
            $respuesta = $ajax->sendEmailWithOrden($email, $IDDocument);
            $rawdata = array('status' => 'OK', 'mensaje' => 'respuesta correcta', 'data' => $respuesta);
          }else{
            http_response_code(400);
            $rawdata = array('status' => 'ERROR', 'mensaje' => 'No se ha indicado parámetros.' );
          }

          echo json_encode($rawdata);

          break; 
        
        case 'canDoPago':
          if (isset($_GET['codOrden'])) {
            $codOrden = $_GET['codOrden'];
            if ($ajax->canDoPago($codOrden)) {
              $rawdata = array('error' => FALSE, 'isAvailable' => TRUE, 'message' => 'Validacion correcta.');
            }else{
              $rawdata = array('error' => FALSE, 'isAvailable' => FALSE, 'message' => 'Validacion incorrecta.');
            }
            
            echo json_encode($rawdata);
            }
          break;

          /* Obtiene array de informacion del cliente*/ 
        case 'getInfoProveedor':
          if (isset($_GET['RUC'])) {
            $RUC = $_GET['RUC'];
            $respuesta = $ajax->getInfoProveedor($RUC);
            $rawdata = array('status' => 'OK', 'mensaje' => 'respuesta correcta', 'data' => $respuesta);
          }else{
            $rawdata = array('status' => 'ERROR', 'mensaje' => 'No se ha indicado parámetros.');
          }
          
          echo json_encode($rawdata);

        break;

        /* Obtiene array de informacion del producto*/ 
        case 'getInfoProducto':

          if (isset($_GET['codigo'])) {
            $codigoProducto = $_GET['codigo'];
            $respuesta = $ajax->getInfoProducto($codigoProducto);
            $rawdata = array('status' => 'OK', 'mensaje' => 'respuesta correcta', 'data' => $respuesta);
          }else{
            http_response_code(400);
            $rawdata = array('status' => 'ERROR', 'mensaje' => 'No se ha indicado parámetros.');
          }
          
        
          echo json_encode($rawdata);

        break;

        /* Obtiene array de informacion del producto*/ 
        case 'searchProducto':

          if (isset($_GET['termino'])) {
            $termino = $_GET['termino'];
            $respuesta = $ajax->searchProducto($termino);
            $rawdata = array('status' => 'OK', 'mensaje' => 'respuesta correcta', 'data' => $respuesta);
          }else{
            http_response_code(400);
            $rawdata = array('status' => 'ERROR', 'mensaje' => 'No se ha indicado parámetros.');
          }
          
        
          echo json_encode($rawdata);

        break;

        /* Obtiene array de proveedores segun SP de winfenix*/ 
        case 'getProveedoresWinfenix':

          if (isset($_GET['busqueda']) && isset($_GET['busqueda'])) {
            $busqueda = $_GET['busqueda'];
            $tipo = $_GET['tipo'];
            $respuesta = $ajax->getProveedoresWinfenix($busqueda, $tipo);
            $rawdata = array('status' => 'OK', 'mensaje' => 'respuesta correcta', 'data' => $respuesta);
          }else{
            http_response_code(400);
            $rawdata = array('status' => 'ERROR', 'mensaje' => 'No se ha indicado parámetros.');
          }
          
        
          echo json_encode($rawdata);

        break;

          
        default:
          http_response_code(400);
          $rawdata = array('status' => 'ERROR', 'message' =>'el API no ha podido responder la solicitud, revise el tipo de action');
          echo json_encode($rawdata);
          break;
    }
    
  } catch (Exception $ex) {
    //Return error message
    $rawdata = array();
    $rawdata['error'] = true;
    $rawdata['status'] = "error";
    $rawdata['message'] = $ex->getMessage();
    http_response_code(400);
    echo json_encode($rawdata);
  }


