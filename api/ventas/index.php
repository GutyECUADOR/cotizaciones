<?php

use App\Controllers\CotizacionesController;
use App\Controllers\EmailController;
use Dotenv\Dotenv;

header('Content-Type: application/json');
date_default_timezone_set('America/Lima');
session_start();

require_once '../../vendor/autoload.php';
$dotenv = Dotenv::createImmutable('../../');
$dotenv->load();

$cotizacionesController = new CotizacionesController();
$emailController = new EmailController();

  try{
    $HTTPaction = isset($_GET["action"]) ? $_GET["action"] : '';

    switch ($HTTPaction) {

        case 'getDocumentos':
          if (isset($_GET['busqueda'])) {
            $busqueda = json_decode($_GET['busqueda']);
            $respuesta = $cotizacionesController->getDocumentos($busqueda);
            $rawdata = array('status' => 'OK', 'message' => 'respuesta correcta', 'documentos' => $respuesta);
          }else{
            http_response_code(400);
            $rawdata = array('status' => 'ERROR', 'message' => 'No se ha indicado parámetros de búsqueda.');
          }
          echo json_encode($rawdata);

        break;

        case 'getClientes':
          if (isset($_GET['busqueda'])) {
            $busqueda = json_decode($_GET['busqueda']);
            $respuesta = $cotizacionesController->getClientes($busqueda);
            $rawdata = array('status' => 'OK', 'message' => 'respuesta correcta', 'clientes' => $respuesta);
          }else{
            http_response_code(400);
            $rawdata = array('status' => 'ERROR', 'message' => 'No se ha indicado parámetros de búsqueda..');
          }
          
          echo json_encode($rawdata);

        break;

        case 'getCliente':
          if (isset($_GET['RUC'])) {
            $RUC = $_GET['RUC'];
            $respuesta = $cotizacionesController->getCliente($RUC);
            $rawdata = array('status' => 'OK', 'message' => 'respuesta correcta', 'data' => $respuesta);
          }else{
            http_response_code(400);
            $rawdata = array('status' => 'ERROR', 'message' => 'No se ha indicado parámetros.');
          }
          
          echo json_encode($rawdata);

        break;

        case 'saveNuevoCliente':
          if (isset($_POST['nuevoCliente'])) {
            $nuevoCliente = json_decode($_POST['nuevoCliente']);
            $rawdata = $cotizacionesController->saveNuevoCliente($nuevoCliente);
          }else{
            http_response_code(400);
            $rawdata = array('status' => 'ERROR', 'message' => 'No se ha indicado parámetros.');
          }
        
          echo json_encode($rawdata);

        break;

        case 'getProductos':
          if (isset($_GET['busqueda'])) {
            $busqueda = json_decode($_GET['busqueda']);
            $respuesta = $cotizacionesController->getProductos($busqueda);
            $rawdata = array('status' => 'OK', 'message' => 'respuesta correcta', 'productos' => $respuesta);
          }else{
            http_response_code(400);
            $rawdata = array('status' => 'ERROR', 'message' => 'No se ha indicado parámetros de búsqueda.');
          }
          
          echo json_encode($rawdata);

        break;

        case 'getProducto':
          if (isset($_GET['busqueda'])) {
            $busqueda = $_GET['busqueda'];
            $respuesta = $cotizacionesController->getProducto($busqueda);
            $rawdata = array('status' => 'OK', 'message' => 'Busqueda finalizada', 'data' => $respuesta);
            
          }else{
            http_response_code(400);
            $rawdata = array('status' => 'ERROR', 'message' => 'No se ha recibido extra data.');
          }
        
          echo json_encode($rawdata);

        break;

        case 'getStock':
          if (isset($_GET['busqueda'])) {
            $busqueda = json_decode($_GET['busqueda']);
            $respuesta = $cotizacionesController->getStock($busqueda);
            $rawdata = array('status' => 'OK', 'message' => 'respuesta correcta', 'data' => $respuesta);
          }else{
            http_response_code(400);
            $rawdata = array('status' => 'ERROR', 'message' => 'No se ha indicado parámetros de búsqueda.');
          }
          
          echo json_encode($rawdata);

        break;

        case 'saveCotizacion':
          if (isset($_POST['documento'])) {
            $formData = json_decode($_POST['documento']);
            $rawdata = $cotizacionesController->saveCotizacion($formData);
            
          }else{
            http_response_code(400);
            $rawdata = array('status' => 'ERROR', 'message' => 'No se ha indicado parámetros.');
          }
        
          echo json_encode($rawdata);

        break;
       
        case 'getVENCAB':
          if (isset($_GET['IDDocument'])) {
            $IDDocument = $_GET['IDDocument'];
            $respuesta = $cotizacionesController->getVENCAB($IDDocument);
            $rawdata = array('status' => 'OK', 'message' => 'respuesta correcta', 'data' => $respuesta);
          }else{
            http_response_code(400);
            $rawdata = array('status' => 'ERROR', 'message' => 'No se ha indicado parámetros.');
          }
          
          echo json_encode($rawdata);

        break;

        case 'getVENMOV':
          if (isset($_GET['IDDocument'])) {
            $IDDocument = $_GET['IDDocument'];
            $respuesta = $cotizacionesController->getVENMOV($IDDocument);
            $rawdata = array('status' => 'OK', 'message' => 'respuesta correcta', 'data' => $respuesta);
          }else{
            http_response_code(400);
            $rawdata = array('status' => 'ERROR', 'message' => 'No se ha indicado parámetros.');
          }
          
          echo json_encode($rawdata);

        break;

        case 'generaProforma':
          if (isset($_GET['IDDocument'])) {
            $IDDocument = $_GET['IDDocument'];
          
            $PDFDocument = $ajax->generaProforma($IDDocument);
            //$rawdata = array('status' => 'OK', 'message' => 'respuesta correcta', 'data' => $respuesta);
            echo $PDFDocument;
          }else{
            http_response_code(400);
            $rawdata = array('status' => 'ERROR', 'message' => 'No se ha indicado parámetros.');
            echo json_encode($rawdata);
          }
        
        

        break;

        case 'getInfoProducto':

          if (isset($_GET['codigo']) && isset($_GET['clienteRUC'])) {
            $codigoProducto = $_GET['codigo'];
            $clienteRUC =  $_GET['clienteRUC'];
            $respuesta = $ajax->getInfoProducto($codigoProducto, $clienteRUC);
            $rawdata = array('status' => 'OK', 'message' => 'respuesta correcta', 'data' => $respuesta);
          }else{
            http_response_code(400);
            $rawdata = array('status' => 'ERROR', 'message' => 'No se ha indicado parámetros.');
          }
          
        
          echo json_encode($rawdata);

        break;

        case 'getInfoPromocion':

          if (isset($_GET['codPromo']) ) {
            $codPromo = $_GET['codPromo'];
            $respuesta = $ajax->getInfoPromocion($codPromo);
            $rawdata = array('status' => 'OK', 'message' => 'respuesta correcta', 'data' => $respuesta);
          }else{
            http_response_code(400);
            $rawdata = array('status' => 'ERROR', 'message' => 'No se ha indicado parámetros.');
          }
          echo json_encode($rawdata);

        break;

        case 'getInfoPromocionByCod':

          if (isset($_GET['codPromo']) && isset($_GET['formaPago']) ) {
            $codPromo = $_GET['codPromo'];
            $formaPago = $_GET['formaPago'];
            $respuesta = $ajax->getInfoPromocionByCod($codPromo, $formaPago);
            $rawdata = array('status' => 'OK', 'message' => 'respuesta correcta', 'data' => $respuesta);
          }else{
            http_response_code(400);
            $rawdata = array('status' => 'ERROR', 'message' => 'No se ha indicado parámetros.');
          }
          echo json_encode($rawdata);

        break;

        case 'getVendedor':

          if (isset($_GET['codVendedor']) ) {
            $codVendedor = $_GET['codVendedor'];
            $respuesta = $ajax->getVendedorByCod($codVendedor);
            $rawdata = array('status' => 'OK', 'message' => 'respuesta correcta', 'data' => $respuesta);
          }else{
            http_response_code(400);
            $rawdata = array('status' => 'ERROR', 'message' => 'No se ha indicado parámetros.');
          }
          echo json_encode($rawdata);

        break;
        
        case 'saveExtraData':
          if (isset($_POST['extraData'])) {
            $extraData = json_decode($_POST['extraData']);
            $respuesta = $ajax->saveExtraData($extraData);
            $rawdata = array('status' => 'OK', 'message' => 'salvar extra data', 'respuesta' => $respuesta);
            
          }else{
            http_response_code(400);
            $rawdata = array('status' => 'ERROR', 'message' => 'No se ha recibido extra data.');
          }
        
          echo json_encode($rawdata);

        break;

        case 'sendEmail':
          if (isset($_GET['email']) ) {
            $email = json_decode($_GET['email']);
            $rawdata = $emailController->sendCotizacion($email);
          }else{
            http_response_code(400);
            $rawdata = array('status' => 'ERROR', 'message' => 'No se ha indicado parámetros.' );
          }  
          
          echo json_encode($rawdata);

        break;

        default:
            $rawdata = array('status' => 'ERROR', 'message' =>'El API no ha podido responder la solicitud, revise el tipo de action');
            echo json_encode($rawdata);
        break;
    }
    
  } catch (Exception $ex) {
    $rawdata = array();
    $rawdata['status'] = "ERROR";
    $rawdata['message'] = $ex->getMessage();
    http_response_code(400);
    echo json_encode($rawdata);
  }


