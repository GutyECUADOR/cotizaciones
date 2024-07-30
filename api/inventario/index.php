<?php
use App\Controllers\InventarioController;
use Dotenv\Dotenv;

header('Content-Type: application/json');
date_default_timezone_set('America/Lima');
session_start();

require_once '../../vendor/autoload.php';
$dotenv = Dotenv::createImmutable('../../');
$dotenv->load();

try {
    $ajax = new InventarioController(); 
    $HTTPaction = isset($_GET["action"]) ? $_GET["action"] : '';

    switch ($HTTPaction) {

      case 'getInfoInitForm_actualizarNombreProducto':
        $respuesta = $ajax->getInfoInitForm_actualizarNombreProducto();
        $rawdata = array('status' => 'OK', 'message' => 'respuesta correcta', 'data' => $respuesta);
        echo json_encode($rawdata);

      break;
      
      case 'getInfoInitForm_actualizarPrecioProducto':
        $respuesta = $ajax->getInfoInitForm_actualizarPrecioProducto();
        $rawdata = array('status' => 'OK', 'message' => 'respuesta correcta', 'data' => $respuesta);
        echo json_encode($rawdata);

      break;

      case 'getInfoInitForm_actualizarColeccionProducto':
        $respuesta = $ajax->getInfoInitForm_actualizarColeccionProducto();
        $rawdata = array('status' => 'OK', 'message' => 'respuesta correcta', 'data' => $respuesta);
        echo json_encode($rawdata);

      break;

      case 'getInfoInitForm_actualizarMarcaProducto':
        $respuesta = $ajax->getInfoInitForm_actualizarMarcaProducto();
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

      case 'getSuministros':
        if (isset($_GET['busqueda'])) {
          $busqueda = json_decode($_GET['busqueda']);
          $respuesta = $ajax->getSuministros($busqueda);
          $rawdata = array('status' => 'OK', 'message' => 'respuesta correcta', 'productos' => $respuesta);
        }else{
          http_response_code(400);
          $rawdata = array('status' => 'ERROR', 'message' => 'No se ha indicado parámetros de búsqueda.');
        }
        
        echo json_encode($rawdata);

      break;

      case 'getSuministro':
        if (isset($_GET['codigo'])) {
          $codigo = $_GET['codigo'];
          $respuesta = $ajax->getSuministro($codigo);
          $rawdata = array('status' => 'OK', 'message' => 'Busqueda finalizada', 'data' => $respuesta);
          
        }else{
          http_response_code(400);
          $rawdata = array('status' => 'ERROR', 'message' => 'No se ha recibido extra data.');
        }
      
        echo json_encode($rawdata);

      break;

      case 'saveSolicitudSuministros':
        if (isset($_POST['documento'])) {
          $documento = json_decode($_POST['documento']);
          $rawdata = $ajax->saveSolicitudSuministros($documento);
         
        }else{
          http_response_code(400);
          $rawdata = array('status' => 'ERROR', 'message' => 'No se ha indicado parámetros.');
        }
      
        echo json_encode($rawdata);

      break;

      case 'getSolicitudesCompra':
        if (isset($_GET['busqueda'])) {
          $busqueda = json_decode($_GET['busqueda']);
          $respuesta = $ajax->getSolicitudesCompra($busqueda);
          $rawdata = array('status' => 'OK', 'message' => 'respuesta correcta', 'productos' => $respuesta);
        }else{
          http_response_code(400);
          $rawdata = array('status' => 'ERROR', 'message' => 'No se ha indicado parámetros de búsqueda.');
        }
        
        echo json_encode($rawdata);

      break;

      case 'getSolicitudesCompraPorAprobar':
        if (isset($_GET['busqueda'])) {
          $busqueda = json_decode($_GET['busqueda']);
          $respuesta = $ajax->getSolicitudesCompraPorAprobar($busqueda);
          $rawdata = array('status' => 'OK', 'message' => 'respuesta correcta', 'productos' => $respuesta);
        }else{
          http_response_code(400);
          $rawdata = array('status' => 'ERROR', 'message' => 'No se ha indicado parámetros de búsqueda.');
        }
        
        echo json_encode($rawdata);

      break;

      case 'getSolicitudesCompraAll':
        if (isset($_GET['busqueda'])) {
          $busqueda = json_decode($_GET['busqueda']);
          $respuesta = $ajax->getSolicitudesCompraAll($busqueda);
          $rawdata = array('status' => 'OK', 'message' => 'respuesta correcta', 'productos' => $respuesta);
        }else{
          http_response_code(400);
          $rawdata = array('status' => 'ERROR', 'message' => 'No se ha indicado parámetros de búsqueda.');
        }
        
        echo json_encode($rawdata);

      break;

      /* Guarda en VEN_CAB crea documento SPY */
      case 'saveOrdenCompraSuministros':
        if (isset($_POST['documento'])) {
          $documento = json_decode($_POST['documento']);
          $rawdata = $ajax->saveOrdenCompraSuministros($documento);
         
        }else{
          http_response_code(400);
          $rawdata = array('status' => 'ERROR', 'message' => 'No se ha indicado parámetros.');
        }
      
        echo json_encode($rawdata);

      break;

      /* Guarda en CAB_OrdenCompraSuministros previa aprobacion */
      case 'saveOrdenCompraSuministros_Aprobacion':
        if (isset($_POST['documento'])) {
          $documento = json_decode($_POST['documento']);
          $rawdata = $ajax->saveOrdenCompraSuministros_Aprobacion($documento);
         
        }else{
          http_response_code(400);
          $rawdata = array('status' => 'ERROR', 'message' => 'No se ha indicado parámetros.');
        }
      
        echo json_encode($rawdata);

      break;

      case 'updateEstadoSolicitudesCompra':
        if (isset($_POST['id'])) {
          $id = $_POST['id'];
          $respuesta = $ajax->updateEstadoSolicitudesCompra($id);
          $rawdata = array('status' => 'OK', 'message' => 'Se han actualizado el status de la solicitud.', 'response' => $respuesta);
          
        }else{
          http_response_code(400);
          $rawdata = array('status' => 'ERROR', 'message' => 'No se ha indicado parámetros.');
        }
      
        echo json_encode($rawdata);
      break;  

      case 'updateNombreProductos':
        if (isset($_POST['productos']) && isset($_POST['database'])) {
          $productos = json_decode($_POST['productos']);
          $database = json_decode($_POST['database']);
          $respuesta = $ajax->updateNombreProductos($productos, $database);
          $rawdata = array('status' => 'OK', 'message' => 'Se han actualizado los productos.', 'response' => $respuesta);
          
        }else{
          http_response_code(400);
          $rawdata = array('status' => 'ERROR', 'message' => 'No se ha indicado parámetros.');
        }
      
        echo json_encode($rawdata);
      break;  

      case 'updatePrecioProductos':
        if (isset($_POST['productos']) && isset($_POST['database'])) {
          $productos = json_decode($_POST['productos']);
          $database = json_decode($_POST['database']);
          $respuesta = $ajax->updatePrecioProductos($productos, $database);
          $rawdata = array('status' => 'OK', 'message' => 'Se han actualizado los productos.', 'response' => $respuesta);
          
        }else{
          http_response_code(400);
          $rawdata = array('status' => 'ERROR', 'message' => 'No se ha indicado parámetros.');
        }
      
        echo json_encode($rawdata);
      break;  

      case 'updateColeccionProductos':
        if (isset($_POST['productos']) && isset($_POST['database'])) {
          $productos = json_decode($_POST['productos']);
          $database = json_decode($_POST['database']);
          $respuesta = $ajax->updateColeccionProductos($productos, $database);
          $rawdata = array('status' => 'OK', 'message' => 'Se han actualizado los productos.', 'response' => $respuesta);
          
        }else{
          http_response_code(400);
          $rawdata = array('status' => 'ERROR', 'message' => 'No se ha indicado parámetros.');
        }
      
        echo json_encode($rawdata);
      break;  

      case 'updateMarcaProductos':
        if (isset($_POST['productos']) && isset($_POST['database'])) {
          $productos = json_decode($_POST['productos']);
          $database = json_decode($_POST['database']);
          $respuesta = $ajax->updateMarcaProductos($productos, $database);
          $rawdata = array('status' => 'OK', 'message' => 'Se han actualizado los productos.', 'response' => $respuesta);
          
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


