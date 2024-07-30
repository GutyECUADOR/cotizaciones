<?php

use App\Controllers\DocumentoController;
use App\Models\EstadoVehiculoModel;
use Dotenv\Dotenv;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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

      case 'generaReportePDF_Cotizacion':
        if (isset($_GET['ID'])) {
          $ID = $_GET['ID'];
        
          $PDFDocument = $documentoController->getPDF_Cotizacion($ID);
          echo $PDFDocument;
          
        }else{
          http_response_code(400);
          $rawdata = array('status' => 'ERROR', 'message' => 'No se ha indicado parámetros.');
          echo json_encode($rawdata);
        }
      
      break;

      case 'generaExcel_SRIDocs':

        if (isset($_POST['arrayDocumentos'])) {
          $arrayDocumentos = json_decode($_POST['arrayDocumentos']);
          $spreadsheet = $documentoController->generaExcel_SRIDocs($arrayDocumentos);
          header('Content-Type: application/vnd.ms-excel');
          header('Content-Disposition: attachment;filename="'. 'documentosSRI-' . date('Ymd') .'.xls"'); 
          header('Cache-Control: max-age=0');
          $writer = new Xlsx($spreadsheet);
          $writer->save('php://output');
          
        }else{
          http_response_code(400);
          $rawdata = array('status' => 'ERROR', 'message' => 'No se ha indicado parámetros.');
          echo json_encode($rawdata);
        }

        
      
      break;

      case 'generaRIDE_SriDocs':
        if (isset($_POST['documento'])) {
          $documento = json_decode($_POST['documento']);
        
          $PDFDocument = $documentoController->generaRIDE_SriDocs($documento);
          echo $PDFDocument;
          
        }else{
          http_response_code(400);
          $rawdata = array('status' => 'ERROR', 'message' => 'No se ha indicado parámetros.');
          echo json_encode($rawdata);
        }
      
      break;

      case 'generaPDF_ValePerdida':
        if (isset($_GET['ID'])) {
          $ID = $_GET['ID'];
        
          $PDFDocument = $documentoController->generaPDF_ValePerdida($ID);
          echo $PDFDocument;
          
        }else{
          http_response_code(400);
          $rawdata = array('status' => 'ERROR', 'message' => 'No se ha indicado parámetros.');
          echo json_encode($rawdata);
        }
      
      break;
      
      default:
        http_response_code(400);
        $rawdata = array('status' => 'error', 'message' =>'El API no ha podido responder la solicitud, revise el tipo de action');
        echo json_encode($rawdata);
      break;
    }
    
  } catch (Exception $ex) {
    //Return error message
    $rawdata = array('status' => 'error');
    $rawdata['error'] = TRUE;
    $rawdata['$message'] = $ex->getMessage();
    http_response_code(400);
    echo json_encode($rawdata);
  }


