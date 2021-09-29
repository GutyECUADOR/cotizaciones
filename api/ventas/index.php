<?php

use App\Controllers\CotizacionesController;
use Dotenv\Dotenv;

header('Content-Type: application/json');
date_default_timezone_set('America/Lima');
session_start();

require_once '../../vendor/autoload.php';
$dotenv = Dotenv::createImmutable('../../');
$dotenv->load();

$cotizacionesController = new CotizacionesController();

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
            $rawdata = array('status' => 'ERROR', 'message' => 'No se ha indicado parámetros de búsqueda..');
          }
          
          echo json_encode($rawdata);

        break;

        case 'getProductos':
          if (isset($_GET['terminoBusqueda']) && isset($_GET['tipoBusqueda'])) {
            $terminoBusqueda = $_GET['terminoBusqueda'];
            $tipoBusqueda = $_GET['tipoBusqueda'];
            $respuesta = $ajax->getAllProductos($terminoBusqueda,  $tipoBusqueda);
            $rawdata = array('status' => 'OK', 'message' => 'respuesta correcta', 'data' => $respuesta);
          }else{
            http_response_code(400);
            $rawdata = array('status' => 'ERROR', 'message' => 'No se ha indicado parámetros.');
          }
          
          echo json_encode($rawdata);

        break;

       
        case 'saveCotizacion':
          if (isset($_POST['formData'])) {
            $formData = json_decode($_POST['formData']);
            $respuesta = $ajax->saveCotizacion($formData);
            $rawdata = array('status' => 'OK', 'message' => 'Realizado', 'data' => $respuesta, 'formDataSended' => $formData);
            
          }else{
            $rawdata = array('status' => 'ERROR', 'message' => 'No se ha indicado parámetros.');
          }
        
          echo json_encode($rawdata);

        break;

        case 'saveCotizacionMultiple':
          if (isset($_POST['formData'])) {
            $formData = json_decode($_POST['formData']);
            $respuesta = $ajax->saveCotizacionMultiple($formData);
            $rawdata = array('status' => 'OK', 'message' => 'Realizado', 'data' => $respuesta, 'formDataSended' => $formData);
            
          }else{
            $rawdata = array('status' => 'ERROR', 'message' => 'No se ha indicado parámetros.');
          }
        
          echo json_encode($rawdata);

        break;

        case 'saveNuevoCliente':
          if (isset($_POST['formData'])) {
            $formData = json_decode($_POST['formData']);
            $respuesta = $ajax->saveNuevoCliente($formData);
            $rawdata = array('status' => 'success', 'message' => 'Cliente registrado con éxito', 'data' => $respuesta, 'formDataSended' => $formData);
            
          }else{
            $rawdata = array('status' => 'ERROR', 'message' => 'No se ha indicado parámetros.');
          }
        
          echo json_encode($rawdata);

        break;

        /* Obtiene array de informacion del cliente*/ 
        case 'getInfoCliente':
          if (isset($_GET['ruc'])) {
            $RUC = $_GET['ruc'];
            $respuesta = $ajax->getInfoCliente($RUC);
            $rawdata = array('status' => 'OK', 'message' => 'respuesta correcta', 'data' => $respuesta);
          }else{
            $rawdata = array('status' => 'ERROR', 'message' => 'No se ha indicado parámetros.');
          }
          
          echo json_encode($rawdata);

        break;

        /* Obtiene array de informacion del cliente*/ 
        case 'getInfoVENCAB':
          if (isset($_GET['IDDocument'])) {
            $IDDocument = $_GET['IDDocument'];
            $respuesta = $ajax->getInfoVENCAB($IDDocument);
            $rawdata = array('status' => 'OK', 'message' => 'respuesta correcta', 'data' => $respuesta);
          }else{
            $rawdata = array('status' => 'ERROR', 'message' => 'No se ha indicado parámetros.');
          }
          
          echo json_encode($rawdata);

        break;

        /* Obtiene array de informacion de movimientos del cliente*/ 
        case 'getInfoVENMOV':
          if (isset($_GET['IDDocument'])) {
            $IDDocument = $_GET['IDDocument'];
            $respuesta = $ajax->getInfoVENMOV($IDDocument);
            $rawdata = array('status' => 'OK', 'message' => 'respuesta correcta', 'data' => $respuesta);
          }else{
            $rawdata = array('status' => 'ERROR', 'message' => 'No se ha indicado parámetros.');
          }
          
          echo json_encode($rawdata);

        break;

       
        

          /* Obtiene array de los documentos SP Winfenix*/ 
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


        /* Obtiene array de informacion del producto*/ 
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

         /* Obtiene array de informacion de la promociones*/ 
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

        /* Obtiene array de informacion de la promocion del producto*/ 
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

        /* Obtiene array de informacion de la promocion del producto*/ 
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
        

        /* Utiliza PHPMailer para el envio de correo, utiliza los correos del cliente indicados en la tabla*/ 
        case 'sendEmail':

          if (isset($_GET['IDDocument']) ) {
            $IDDocument = $_GET['IDDocument'];
            $respuesta = $ajax->sendEmail($IDDocument);
            $rawdata = array('status' => 'OK', 'message' => 'respuesta correcta', 'data' => $respuesta);
          }else{
            http_response_code(400);
            $rawdata = array('status' => 'ERROR', 'message' => 'No se ha indicado parámetros.' );
          }

          echo json_encode($rawdata);

        break; 

        /* Utiliza PHPMailer para el envio de correo, permite editar los emails que seran enviados*/ 
        case 'sendEmailByCustomEmail':

          if (isset($_GET['email']) && isset($_GET['IDDocument']) ) {
            $arrayEmails = $_GET['email'];
            $IDDocument = $_GET['IDDocument'];
            $customMessage = isset($_GET['message']) ? $_GET['message'] : '';
            $respuesta = $ajax->sendEmailByCustomEmail($arrayEmails, $IDDocument, $customMessage);
            $rawdata = array('status' => 'OK', 'message' => 'respuesta correcta', 'data' => $respuesta);
          }else{
            http_response_code(400);
            $rawdata = array('status' => 'ERROR', 'message' => 'No se ha indicado parámetros.' );
          }  
          
        
          echo json_encode($rawdata);

        break;

        case 'uploadFile':

          if (isset($_FILES['file']) && !empty($_FILES['file']) && isset($_POST['codOrden']) && !empty($_POST['codOrden']) && isset($_POST['codProducto']) && !empty($_POST['codProducto']) && isset($_POST['descripcion'])) { // file es el nombre del input o clave en formData

            $codOrden = $_POST['codOrden']; //'992018PRO00012217'; // Defile el nombre unico que tedra la imagen
            $codProducto = $_POST['codProducto']; //'00000008'; // Defile el nombre unico que tedra la imagen
            $descripcion = $_POST['descripcion']; //'Descripcion extra del producto

            $contador = 0; // Define el numero de imagen relacionado al codigo de la orden
            $location = $_SERVER['DOCUMENT_ROOT'].'/'."uploadsCotizaciones/"; // Root del directorio a guardar (debe estar creado)
            
            $total_files = count($_FILES["file"]['name']);
            $array_files = $_FILES["file"];
            $archivosCargados = array();
            $errores = array();

            for ($cont = 0; $cont < $total_files; $cont++) {
              $newname = $codOrden."_$codProducto"."_$contador".".jpg"; // Asignamos nombre unico
              $extension = pathinfo($array_files["name"][$cont], PATHINFO_EXTENSION);

              if($extension=='jpg' || $extension=='jpeg' || $extension=='png') // Comprobacion del tipo
              {

                if ($array_files["error"][$cont] > 0) {
                  array_push($errores, $array_files["error"][$cont]);
              } else {
                  if (file_exists($location.$newname)) { // Revision si existe el archivo en el directorio
                      array_push($archivosCargados, 'El archivo ya existe: '.$newname);
                  } else {
                      move_uploaded_file($array_files["tmp_name"][$cont], $location.$newname); // Carga en si
                      // Aqui posible carga a la DB
                      $obj = new stdClass;
                      $obj->codDocumento = $codOrden;
                      $obj->codProducto = $codProducto;
                      $obj->nombreImagen = $newname;
                      $obj->descripcion = $descripcion;

                      $obj->message = 'Archivo cargado: ' . $array_files["name"][$cont] . ', nuevo nombre asignado: '.$newname;
                      array_push($archivosCargados, $obj);
                  }
              }
              }
              $contador++;
            }

            $rawdata = array('status' => 'OK', 'message' => 'Carga completa', 'resultados' => $archivosCargados,  'errores' => $errores);
            echo json_encode($rawdata);

          }else {
            $rawdata = array('status' => 'FAIL', 'message' => 'Sin archivos seleccionados o codigo de Orden no indicada.');
            echo json_encode($rawdata);
          }
        
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

        default:
            $rawdata = array('status' => 'error', 'message' =>'El API no ha podido responder la solicitud, revise el tipo de action');
            echo json_encode($rawdata);
        break;
    }
    
  } catch (Exception $ex) {
    $rawdata = array();
    $rawdata['status'] = "error";
    $rawdata['message'] = $ex->getMessage();
    http_response_code(400);
    echo json_encode($rawdata);
  }


