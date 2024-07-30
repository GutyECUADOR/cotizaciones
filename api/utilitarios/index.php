<?php
use App\Controllers\UtilitariosController;
use Dotenv\Dotenv;

header('Content-Type: application/json');
date_default_timezone_set('America/Lima');
session_start();

require_once '../../vendor/autoload.php';
$dotenv = Dotenv::createImmutable('../../');
$dotenv->load();

try {
    $ajax = new UtilitariosController(); 
    $HTTPaction = isset($_GET["action"]) ? $_GET["action"] : '';

    switch ($HTTPaction) {

      case 'getInfoInitForm_cargaNuevosClientes':
        $respuesta = $ajax->getInfoInitForm_cargaNuevosClientes();
        $rawdata = array('status' => 'OK', 'message' => 'respuesta correcta', 'data' => $respuesta);
        echo json_encode($rawdata);

      break;

      case 'getProducto':
        if (isset($_GET['codigo'])) {
          $codigo = $_GET['codigo'];
          $respuesta = $ajax->getProducto($codigo);
          $rawdata = array('status' => 'OK', 'message' => 'respuesta correcta', 'producto' => $respuesta);
        }else{
          http_response_code(400);
          $rawdata = array('status' => 'error', 'message' => 'No se ha indicado parámetros.');
        }
        
        echo json_encode($rawdata);

      break;

      case 'getProductosSinEAN':
        if (isset($_GET['busqueda'])) {
          $busqueda = json_decode($_GET['busqueda']);
          $respuesta = $ajax->getProductosSinEAN($busqueda);
          $rawdata = array('status' => 'OK', 'message' => 'respuesta correcta', 'productos' => $respuesta);
        }else{
          http_response_code(400);
          $rawdata = array('status' => 'error', 'message' => 'No se ha indicado parámetros.');
        }
        
        echo json_encode($rawdata);

      break;

      case 'getNuevoEAN13':
          $respuesta = $ajax->getNuevoEAN13();
          $rawdata = array('status' => 'OK', 'message' => 'respuesta correcta', 'EAN13' => $respuesta);
          echo json_encode($rawdata);

      break;

      case 'saveNuevoEAN13':
        if (isset($_POST['producto'])) {
          $producto = json_decode($_POST['producto']);
          $respuesta = $ajax->saveNuevoEAN13($producto);
          $rawdata = array('status' => 'OK', 'message' => 'Respuesta correcta, EAN registrado con éxito', 'commit' => $respuesta);
        }else{
          http_response_code(400);
          $rawdata = array('status' => 'error', 'message' => 'No se ha indicado parámetros.');
        }
        
        echo json_encode($rawdata);

      break;

      case 'save_cargaNuevosClientes':
        if (isset($_POST['clientes']) && isset($_POST['database'])) {
          $clientes = json_decode($_POST['clientes']);
          $database = json_decode($_POST['database']);
          $respuesta = $ajax->save_cargaNuevosClientes($clientes, $database);
          $rawdata = array('status' => 'OK', 'message' => 'Se han actualizado los registros.', 'response' => $respuesta);
          
        }else{
          http_response_code(400);
          $rawdata = array('status' => 'ERROR', 'message' => 'No se ha indicado parámetros.');
        }
      
        echo json_encode($rawdata);
      break;  

      default:
          $rawdata = array('status' => 'error', 'message' =>'El API no ha podido responder la solicitud, revise el tipo de action');
          echo json_encode($rawdata);
      break;
    }
    
  } catch (Exception $ex) {
    //Return error message
    $rawdata = array();
    $rawdata['status'] = "error";
    $rawdata['message'] = $ex->getMessage();
    http_response_code(400);
    echo json_encode($rawdata);
  }


