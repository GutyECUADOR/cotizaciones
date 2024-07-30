<?php
use App\Controllers\GuiasController;
use Dotenv\Dotenv;

header('Content-Type: application/json');
date_default_timezone_set('America/Lima');
session_start();

require_once '../../vendor/autoload.php';
$dotenv = Dotenv::createImmutable('../../');
$dotenv->load();

try {
    $guiasController = new GuiasController(); 
    $HTTPaction = isset($_GET["action"]) ? $_GET["action"] : '';

    switch ($HTTPaction) {

      case 'getAPITramaco_consultarLocalidadContrato':
        echo $guiasController->getAPITramaco_consultarLocalidadContrato();
      break;

      case 'getAPIShopify_consultarOrdenByID':
        if (isset($_GET['ID_SHOPIFY'])) {
          $IDDocument = $_GET['ID_SHOPIFY'];
          $order = json_decode($guiasController->getAPIShopify_consultarOrdenByID($IDDocument));
          $checkout_id = $order->order->checkout_id;
          $payment = json_decode($guiasController->getPaymentData($checkout_id));
          echo json_encode(array('order' => $order, 'payment' => $payment));

        }else{
          $rawdata = array('status' => 'error', 'message' => 'No se ha indicado parámetros.');
          echo json_encode($rawdata);
        }
       
      break;

      case 'getPaymentData':
        if (isset($_GET['checkout_id'])) {
          $checkout_id = $_GET['checkout_id'];
          echo json_encode($guiasController->getPaymentData($checkout_id));
         
        }else{
          $rawdata = array('status' => 'error', 'message' => 'No se ha indicado parámetros.');
          echo json_encode($rawdata);
        }
       
      break;

      case 'getProvinciasTramaco':
        $respuesta = $guiasController->getProvincias_tramaco();
        $rawdata = array('status' => 'OK', 'message' => 'Realizado', 'provincias' => $respuesta);
      
        echo json_encode($rawdata);
      break;

      case 'getCantonesTramaco':
        $provincia = $_GET['provincia'];
        $respuesta = $guiasController->getCantones_tramaco($provincia);
        $rawdata = array('status' => 'OK', 'message' => 'Realizado', 'cantones' => $respuesta);
      
        echo json_encode($rawdata);
      break;

      case 'getParroquiasTramaco':
        if (isset($_GET['busqueda'])) {
          $busqueda = json_decode($_GET['busqueda']);
          $respuesta = $guiasController->getParroquias_tramaco($busqueda);
          $rawdata = array('status' => 'OK', 'message' => 'Realizado', 'parroquias' => $respuesta);
          }else{
            $rawdata = array('status' => 'ERROR', 'message' => 'No se ha indicado parámetros.');
          }
        echo json_encode($rawdata);
      break;

      case 'getCodigoEnvio':
        if (isset($_GET['busqueda'])) {
          $busqueda = json_decode($_GET['busqueda']);
          $respuesta = $guiasController->getCodigoEnvio($busqueda);
          $rawdata = array('status' => 'OK', 'message' => 'Realizado', 'data' => $respuesta);
          
        }else{
          $rawdata = array('status' => 'ERROR', 'message' => 'No se ha indicado parámetros.');
        }
      
        echo json_encode($rawdata);
      break;


      case 'postAPITramaco_generarGuia':
        if (isset($_POST['guia'])) {
          $guia = $_POST['guia'];
         echo $guiasController->postAPITramaco_generarGuia($guia);
         
        }else{
          $rawdata = array('status' => 'error', 'message' => 'No se ha indicado parámetros.');
          echo json_encode($rawdata);
        }
      break;

      case 'postAPITramaco_generarPDF':
        if (isset($_GET['guia'])) {
          $guiaID = $_GET['guia'];
         echo $guiasController->postAPITramaco_generarPDF($guiaID);
         
        }else{
          $rawdata = array('status' => 'error', 'message' => 'No se ha indicado parámetros.');
          echo json_encode($rawdata);
        }
      break;

      case 'consultarTrackingTramaco':
        if (isset($_GET['guia'])) {
          $guiaID = $_GET['guia'];
         echo $guiasController->postAPITramaco_consultarTracking($guiaID);
         
        }else{
          $rawdata = array('status' => 'error', 'message' => 'No se ha indicado parámetros.');
          echo json_encode($rawdata);
        }
      break;

      case 'updateGuiaDocumento':
        if (isset($_POST['guiaGeneradaID']) && isset($_POST['documento'])) {
          $guiaGeneradaID = $_POST['guiaGeneradaID'];
          $documento = $_POST['documento'];
          $respuesta = $guiasController->updateGuiaDocumento($guiaGeneradaID, $documento);
          $rawdata = array('status' => 'success', 'message' => 'respuesta correcta', 'respuesta' => $respuesta);
        }else{
          $rawdata = array('status' => 'error', 'message' => 'No se ha indicado parámetros.');
        }
        
        echo json_encode($rawdata);

      break;

      case 'updateGuiaDocumentoShopify':
        if (isset($_POST['guiaGeneradaID']) && isset($_POST['documento']) && isset($_POST['peso']) && isset($_POST['tracking'])) {
          $documento = $_POST['documento'];
          $peso = $_POST['peso'];
          $tracking = $_POST['tracking'];
          $guia = $_POST['guiaGeneradaID'];
          
          $respuesta = $guiasController->updateGuiaDocumentoShopify($documento, $peso, $tracking, $guia);
          $rawdata = array('status' => 'success', 'message' => 'respuesta correcta', 'respuesta' => $respuesta);
        }else{
          $rawdata = array('status' => 'error', 'message' => 'No se ha indicado parámetros.');
        }
        
        echo json_encode($rawdata);

      break;

      case 'getAllDocumentos':
        if (isset($_GET['busqueda'])) {
          $busqueda = json_decode($_GET['busqueda']);
          $respuesta = $guiasController->getAllDocumentos($busqueda);
          $rawdata = array('status' => 'success', 'message' => 'respuesta correcta', 'busqueda'=> $busqueda, 'documentos' => $respuesta);
        }else{
          $rawdata = array('status' => 'error', 'message' => 'No se ha indicado parámetros.');
        }
        
        echo json_encode($rawdata);

      break;

      case 'getAllDocumentosShopify':
        if (isset($_GET['busqueda'])) {
          $busqueda = json_decode($_GET['busqueda']);
          $respuesta = $guiasController->getAllDocumentosShopify($busqueda);
          $rawdata = array('status' => 'success', 'message' => 'respuesta correcta', 'busqueda'=> $busqueda, 'documentos' => $respuesta);
        }else{
          $rawdata = array('status' => 'error', 'message' => 'No se ha indicado parámetros.');
        }
        
        echo json_encode($rawdata);

      break;

      case 'getDatosCliente':
        if (isset($_GET['codCliente'])) {
          $codCliente = $_GET['codCliente'];
          $respuesta = $guiasController->getDatosCliente($codCliente);
          $rawdata = array('status' => 'OK', 'message' => 'respuesta correcta', 'cliente' => $respuesta);
        }else{
          $rawdata = array('status' => 'ERROR', 'message' => 'No se ha indicado termino para busqueda del cliente.');
        }
        
        echo json_encode($rawdata);

      break;

      case 'getLocalidadEnvio':
        if (isset($_GET['documentoID'])) {
          $documentoID = $_GET['documentoID'];
          $empresa = $_SESSION["empresaAUTH".APP_UNIQUE_KEY];
          $respuesta = $guiasController->getLocalidadEnvio($documentoID, $empresa);
          $rawdata = array('status' => 'OK', 'message' => 'respuesta correcta', 'localidad' => $respuesta);
        }else{
          $rawdata = array('status' => 'ERROR', 'message' => 'No se ha indicado termino para busqueda de la localidad.');
        }
        
        echo json_encode($rawdata);

      break;

      case 'getInfoInitForm':
        $respuesta = $guiasController->getInfoInitForm();
        $rawdata = array('status' => 'success', 'message' => 'respuesta correcta', 'data'=> $respuesta);
        echo json_encode($rawdata);

      break;

      case 'updateStatusTracking':
        if (isset($_POST['idDocumento']) && isset($_POST['tracking']) && isset($_POST['id_order_shopify'])) {
          $documento = $_POST['idDocumento'];
          $tracking = $_POST['tracking'];
          $id_order_shopify = $_POST['id_order_shopify'];
          $respuesta = $guiasController->updateStatusTracking($documento, $tracking, $id_order_shopify);
          $rawdata = array('status' => 'success', 'message' => 'respuesta correcta', 'data' => $respuesta, 'documento'=> $documento);
        }else{
          $rawdata = array('status' => 'error', 'message' => 'No se ha indicado parámetros.');
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


