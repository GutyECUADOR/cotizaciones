<?php

use App\Controllers\ValesPerdidaController;
use Dotenv\Dotenv;

header('Content-Type: application/json');
date_default_timezone_set('America/Lima');
session_start();

require_once '../../vendor/autoload.php';
$dotenv = Dotenv::createImmutable('../../');
$dotenv->load();

$ajax = new ValesPerdidaController();

  try{
    $HTTPaction = isset($_GET["action"]) ? $_GET["action"] : '';

    switch ($HTTPaction) {

      case 'getDocumentos':
        if (isset($_GET['busqueda'])) {
          $busqueda = json_decode($_GET['busqueda']);
          $respuesta = $ajax->getDocumentos($busqueda);
          $rawdata = array('status' => 'OK', 'message' => 'Respuesta correcta', 'documentos' => $respuesta);
        }else{
          http_response_code(400);
          $rawdata = array('status' => 'ERROR', 'message' => 'No se ha indicado parámetros de búsqueda.');
        }
        
        echo json_encode($rawdata);

      break;

        case 'getInfoInitForm':
          $respuesta = $ajax->getInfoInitForm();
          $rawdata = array('status' => 'success', 'message' => 'respuesta correcta', 'data'=> $respuesta);
          echo json_encode($rawdata);

        break;
        
        case 'getSolicitanteByRUC':
          if (isset($_GET['RUC'])) {
            $RUC = $_GET['RUC'];
            $respuesta = $ajax->getSolicitanteByRUC($RUC);
            $rawdata = array('status' => 'OK', 'message' => 'Respuesta correcta', 'empleado' => $respuesta);
          }else{
            http_response_code(400);
            $rawdata = array('status' => 'error', 'message' => 'No se ha indicado parámetros.');
          }
          echo json_encode($rawdata);
        break;

        case 'getSolicitantes':
          if (isset($_GET['busqueda'])) {
            $busqueda = json_decode($_GET['busqueda']);
            $respuesta = $ajax->getSolicitantes($busqueda);
            $rawdata = array('status' => 'OK', 'message' => 'Respuesta correcta', 'empleados' => $respuesta);
          }else{
            http_response_code(400);
            $rawdata = array('status' => 'ERROR', 'message' => 'No se ha indicado parámetros de búsqueda.');
          }
          
          echo json_encode($rawdata);
  
        break;

        case 'getEmpleado':
          if (isset($_GET['RUC'])) {
            $RUC = $_GET['RUC'];
            $respuesta = $ajax->getEmpleado($RUC);
            $rawdata = array('status' => 'OK', 'message' => 'Respuesta correcta', 'empleado' => $respuesta);
          }else{
            http_response_code(400);
            $rawdata = array('status' => 'error', 'message' => 'No se ha indicado parámetros.');
          }
          echo json_encode($rawdata);
        break;

        case 'saveValePerdida':
          if (isset($_POST['documento'])) {
            $documento = json_decode($_POST['documento']);
            $rawdata = $ajax->saveValePerdida($documento);
            echo json_encode($rawdata);
            
          }else{
            http_response_code(400);
            $rawdata = array('status' => 'ERROR', 'message' => 'No se ha indicado parámetros.');
            echo json_encode($rawdata);
          }
      
        break;

        case 'getValesPendientesRevision':
          if (isset($_GET['busqueda'])) {
            $busqueda = json_decode($_GET['busqueda']);
            $respuesta = $ajax->getValesPendientesRevision($busqueda);
            $rawdata = array('status' => 'OK', 'message' => 'Respuesta correcta', 'documentos' => $respuesta);
          }else{
            http_response_code(400);
            $rawdata = array('status' => 'ERROR', 'message' => 'No se ha indicado parámetros de búsqueda.');
          }
          
          echo json_encode($rawdata);
  
        break;

        case 'aprobarVale':
          if (isset($_GET['idDocumento'])) {
            $idDocumento = $_GET['idDocumento'];
            $rawdata = $ajax->aprobarVale($idDocumento);
          }else{
            http_response_code(400);
            $rawdata = array('status' => 'error', 'message' => 'No se ha indicado parámetros.');
          }
          echo json_encode($rawdata);
        break;

        case 'anularVale':
          if (isset($_GET['idDocumento'])) {
            $idDocumento = $_GET['idDocumento'];
            $rawdata = $ajax->anularVale($idDocumento);
          }else{
            http_response_code(400);
            $rawdata = array('status' => 'error', 'message' => 'No se ha indicado parámetros.');
          }
          echo json_encode($rawdata);
        break;

        default:
            $rawdata = array('status' => 'ERROR', 'message' =>'El API no ha podido responder la solicitud, revise el tipo de action');
            http_response_code(404);
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


