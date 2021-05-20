<?php

use App\Controllers\DocumentoController;

use Dotenv\Dotenv;

header('Content-Type: application/json');
date_default_timezone_set('America/Lima');
session_start();

require_once '../../vendor/autoload.php';
$dotenv = Dotenv::createImmutable('../../');
$dotenv->load();

$documentoController = new DocumentoController();

  try{
    $HTTPaction = isset($_GET["action"]) ? $_GET["action"]: '';

    switch ($HTTPaction) {

        case 'generaReportePDF_IngresosEgresos':
          if (isset($_GET['ID'])) {
            $ID = $_GET['ID'];
          
            $PDFDocument = $documentoController->getPDF_ingresosEgresos($ID);
            echo $PDFDocument;
            
          }else{
            http_response_code(400);
            $rawdata = array('status' => 'ERROR', 'message' => 'No se ha indicado parámetros.');
            echo json_encode($rawdata);
          }
        
        break;

        case 'generaReportePDF_CreacionReceta':
          if (isset($_GET['ID'])) {
            $ID = $_GET['ID'];
          
            $PDFDocument = $documentoController->getPDF_CreacionReceta($ID);
            echo $PDFDocument;
            
          }else{
            http_response_code(400);
            $rawdata = array('status' => 'ERROR', 'message' => 'No se ha indicado parámetros.');
            echo json_encode($rawdata);
          }
        
        break;


        default:
          http_response_code(400);
          $rawdata = array('status' => 'error', 'mensaje' =>'El API no ha podido responder la solicitud, revise el tipo de action');
          echo json_encode($rawdata);
        break;
    }
    
  } catch (Exception $ex) {
    //Return error message
    $rawdata = array('status' => 'error');
    $rawdata['error'] = TRUE;
    $rawdata['mensaje'] = $ex->getMessage();
    http_response_code(400);
    echo json_encode($rawdata);
  }
