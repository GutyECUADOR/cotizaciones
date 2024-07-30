<?php
use App\Controllers\SRIController;
use Dotenv\Dotenv;

header('Content-Type: application/json');
date_default_timezone_set('America/Lima');
session_start();

require_once '../../vendor/autoload.php';
$dotenv = Dotenv::createImmutable('../../');
$dotenv->load();

try {
    $ajax = new SRIController(); 
    $HTTPaction = isset($_GET["action"]) ? $_GET["action"] : '';

    switch ($HTTPaction) {

      case 'autorizacionComprobante':
        if (isset($_GET['claveAutorizacion'])) {
          $claveAutorizacion = $_GET['claveAutorizacion'];
          $respuesta = $ajax->autorizacionComprobante($claveAutorizacion);
          $rawdata = array('status' => 'OK', 'message' => 'respuesta correcta', 'data' => $respuesta);
        }else{
          http_response_code(400);
          $rawdata = array('status' => 'error', 'message' => 'No se ha indicado parámetros.');
        }
        echo json_encode($rawdata);
      break;

      case 'download_autorizacionComprobante':
          if (isset($_GET['claveAutorizacion'])) {
            $claveAutorizacion = $_GET['claveAutorizacion'];
            $respuesta = $ajax->autorizacionComprobante($claveAutorizacion);
            //$rawdata = array('status' => 'OK', 'message' => 'respuesta correcta', 'data' => $respuesta);
            $xmlstring = $respuesta['sri_data']->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->comprobante;
          
            $dom = new \DOMDocument();
            $dom->loadXML($xmlstring);
            header('Content-Disposition: attachment;filename=myfile.xml');
            header('Content-Type: text/xml');
            echo $dom->saveXML();
          }else{
            http_response_code(400);
            $rawdata = array('status' => 'error', 'message' => 'No se ha indicado parámetros.');
            echo json_encode($rawdata);
          }
        
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


