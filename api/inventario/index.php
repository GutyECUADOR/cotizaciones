<?php

use App\Controllers\InventarioController;
use Dotenv\Dotenv;

header('Content-Type: application/json');
date_default_timezone_set('America/Lima');
session_start();

require_once '../../vendor/autoload.php';
$dotenv = Dotenv::createImmutable('../../');
$dotenv->load();

$InventarioController = new InventarioController();

  try{
    $HTTPaction = isset($_GET["action"]) ? $_GET["action"] : '';

    switch ($HTTPaction) {

      case 'searchDocumentos_IngresosEgresos':
        if (isset($_GET['busqueda'])) {
          $busqueda = json_decode($_GET['busqueda']);
          $rawdata = $InventarioController->searchDocumentos_EgresosIngresos($busqueda);
        }else{
          http_response_code(400);
          $rawdata = array('status' => 'error', 'mensaje' => 'No se ha indicado parámetros.');
        }
        echo json_encode($rawdata);
      break;

      case 'searchDocumentos_CreacionReceta':
        if (isset($_GET['busqueda'])) {
          $busqueda = json_decode($_GET['busqueda']);
          $rawdata = $InventarioController->searchDocumentos_CreacionReceta($busqueda);
        }else{
          http_response_code(400);
          $rawdata = array('status' => 'error', 'mensaje' => 'No se ha indicado parámetros.');
        }
        echo json_encode($rawdata);
      break;

        case 'searchProductos':
          if (isset($_GET['busqueda'])) {
            $busqueda = json_decode($_GET['busqueda']);
            $respuesta = $InventarioController->searchProductos($busqueda);
            $rawdata = array('status' => 'ok', 'mensaje' => 'respuesta correcta', 'data' => $respuesta);
          }else{
            http_response_code(400);
            $rawdata = array('status' => 'error', 'mensaje' => 'No se ha indicado parámetros.');
          }
          echo json_encode($rawdata);
        break;



        case 'getProducto':
          if (isset($_GET['busqueda'])) {
            $busqueda = $_GET['busqueda'];
            $respuesta = $InventarioController->getProducto($busqueda);
            $rawdata = array('status' => 'OK', 'message' => 'Busqueda finalizada', 'data' => $respuesta);
            
          }else{
            http_response_code(400);
            $rawdata = array('status' => 'error', 'message' => 'No se ha recibido extra data.');
          }
        
          echo json_encode($rawdata);

        break;

        case 'getComposicionProducto':
          if (isset($_GET['busqueda'])) {
            $busqueda = $_GET['busqueda'];
            $respuesta = $InventarioController->getComposicionProducto($busqueda);
            $rawdata = array('status' => 'OK', 'message' => 'Busqueda finalizada', 'data' => $respuesta);
            
          }else{
            http_response_code(400);
            $rawdata = array('status' => 'error', 'message' => 'No se ha recibido extra data.');
          }
        
          echo json_encode($rawdata);

        break;

        case 'getProductos':
          if (isset($_GET['busqueda'])) {
            $busqueda = $_GET['busqueda'];
            $respuesta = $InventarioController->getProductos($busqueda);
            $rawdata = array('status' => 'OK', 'message' => 'Busqueda finalizada', 'data' => $respuesta);
            
          }else{
            http_response_code(400);
            $rawdata = array('status' => 'error', 'message' => 'No se ha recibido extra data.');
          }
        
          echo json_encode($rawdata);

        break;

        case 'getCostoProducto':
          if (isset($_GET['busqueda'])) {
            $busqueda = json_decode($_GET['busqueda']);
            $respuesta = $InventarioController->getCostoProducto($busqueda);
            $rawdata = array('status' => 'OK', 'message' => 'Busqueda finalizada', 'data' => $respuesta);
            
          }else{
            http_response_code(400);
            $rawdata = array('status' => 'error', 'message' => 'No se ha recibido extra data.');
          }
        
          echo json_encode($rawdata);

        break;

        case 'getCantidadByFactor':
          if (isset($_GET['busqueda'])) {
            $busqueda = json_decode($_GET['busqueda']);
            $respuesta = $InventarioController->getCantidadByFactor($busqueda);
            $rawdata = array('status' => 'OK', 'message' => 'Busqueda finalizada', 'data' => $respuesta);
            
          }else{
            http_response_code(400);
            $rawdata = array('status' => 'error', 'message' => 'No se ha recibido extra data.');
          }
        
          echo json_encode($rawdata);

        break;

        case 'saveInventario':
          if (isset($_POST['documento'])) {
            $documento = json_decode($_POST['documento']);
            $respuesta = $InventarioController->saveInventario($documento);
            $rawdata = array('status' => 'OK', 'transaction' => $respuesta);
            
          }else{
            http_response_code(400);
            $rawdata = array('status' => 'ERROR', 'mensaje' => 'No se ha recibido objeto de inventario requerido, revise estructura de JS.');
          }
        
          echo json_encode($rawdata);

        break;

        case 'saveCreacionReceta':
          if (isset($_POST['documento'])) {
            $documento = json_decode($_POST['documento']);
            $respuesta = $InventarioController->saveCreacionReceta($documento);
            $rawdata = array('status' => 'OK', 'transaction' => $respuesta);
            
          }else{
            http_response_code(400);
            $rawdata = array('status' => 'ERROR', 'mensaje' => 'No se ha recibido objeto de inventario requerido, revise estructura de JS.');
          }
        
          echo json_encode($rawdata);

        break;

        case 'saveCostoTeorico':
          if (isset($_POST['documento'])) {
            $documento = json_decode($_POST['documento']);
            $rawdata = $InventarioController->saveCostoTeorico($documento);
         
          }else{
            http_response_code(400);
            $rawdata = array('status' => 'ERROR', 'mensaje' => 'No se ha recibido objeto de inventario requerido, revise estructura de JS.');
          }
        
          echo json_encode($rawdata);

        break;

        case 'saveTransformacionKITS':
          if (isset($_POST['documento'])) {
            $documento = json_decode($_POST['documento']);
            $respuesta = $InventarioController->saveTransformacionKITS($documento);
            $rawdata = $respuesta;
            
          }else{
            http_response_code(400);
            $rawdata = array('status' => 'ERROR', 'mensaje' => 'No se ha recibido objeto de inventario requerido, revise estructura de JS.');
          }
        
          echo json_encode($rawdata);

        break;

        case 'test':
            $rawdata = array('status' => 'OK', 'mensaje' => 'Respuesta correcta');
            echo json_encode($rawdata);

        break;

        default:
          http_response_code(404);
            $rawdata = array('status' => 'error', 'mensaje' =>'El API no ha podido responder la solicitud, revise el tipo de action');
            echo json_encode($rawdata);
        break;
    }
    
  } catch (Exception $ex) {
    //Return error message
    $rawdata = array();
    $rawdata['status'] = "error";
    $rawdata['mensaje'] = $ex->getMessage();
    echo json_encode($rawdata);
  }


