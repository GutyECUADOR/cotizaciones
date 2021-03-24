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
    $HTTPaction = $_GET["action"];

    switch ($HTTPaction) {

        case 'searchProductos':
          if (isset($_GET['busqueda'])) {
            $busqueda = json_decode($_GET['busqueda']);
            $respuesta = $InventarioController->searchProductos($busqueda);
            $rawdata = array('status' => 'ok', 'mensaje' => 'respuesta correcta', 'data' => $respuesta);
          }else{
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
            $rawdata = array('status' => 'error', 'message' => 'No se ha recibido extra data.');
          }
        
          echo json_encode($rawdata);

        break;

        case 'saveInventario':
          if (isset($_POST['documento'])) {
            $extraData = json_decode($_POST['documento']);
            $respuesta = $InventarioController->saveInventario($extraData);
            $rawdata = array('status' => 'OK', 'transaction' => $respuesta);
            
          }else{
            $rawdata = array('status' => 'ERROR', 'mensaje' => 'No se ha recibido objeto de inventario requerido, revise estructura de JS.');
          }
        
          echo json_encode($rawdata);

        break;

        case 'test':
            $rawdata = array('status' => 'OK', 'mensaje' => 'Respuesta correcta');
            echo json_encode($rawdata);

        break;

        default:
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


