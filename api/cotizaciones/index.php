<?php

use App\Controllers\AjaxController;
use Dotenv\Dotenv;

header('Content-Type: application/json');
date_default_timezone_set('America/Lima');
session_start();

require_once '../../vendor/autoload.php';
$dotenv = Dotenv::createImmutable('../../');
$dotenv->load();

class ajax{
  private $ajaxController;
   
    public function __construct() {
      $this->ajaxController = new AjaxController();
    }

    public function getAPITramaco_login() {
      return $this->ajaxController->getAPITramaco_login();
    }

    public function getAPITramaco_costo_envio($codParroquiaDest, $peso) {
      return $this->ajaxController->getAPITramaco_costo_envio($codParroquiaDest, $peso);
    }

    public function getCodigoEnvio($provincia, $canton, $parroquia){
      return $this->ajaxController->getCodigoEnvio($provincia, $canton, $parroquia);
    }

    public function getProvincias_tramaco() {
      return $this->ajaxController->getProvincias_tramaco();
    }

    public function getCantones_tramaco($provincia) {
      return $this->ajaxController->getCantones_tramaco($provincia);
    }

    public function getParroquias_tramaco($canton) {
      return $this->ajaxController->getParroquias_tramaco($canton);
    }
    
    public function getInfoCliente($RUC) {
        return $this->ajaxController->getInfoClienteController($RUC);
    }

    public function getInfoVENCAB($IDDocument) {
      return $this->ajaxController->getVEN_CABController($IDDocument);
    }

    public function getInfoVENMOV($IDDocument) {
      return $this->ajaxController->getVEN_MOVController($IDDocument);
    }
    
    public function getAllClientes($terminoBusqueda,  $tipoBusqueda) {
      return $this->ajaxController->getAllClientesController($terminoBusqueda,  $tipoBusqueda);
    }

    public function getAllProductos($terminoBusqueda,  $tipoBusqueda) {
      return $this->ajaxController->getAllProductosController($terminoBusqueda,  $tipoBusqueda);
    }

    public function getAllDocumentos($fechaINI, $fechaFIN, $stringBusqueda, $tipoDOC) {
      return $this->ajaxController->getAllDocumentosController($fechaINI,  $fechaFIN, $stringBusqueda, $tipoDOC);
    }

    public function getInfoProducto($codigoProducto, $clienteRUC) {
        return $this->ajaxController->getInfoProductoController($codigoProducto, $clienteRUC);
    }

    public function getInfoPromocion($codPromo) {
        return $this->ajaxController->getInfoPromocionController($codPromo);
    }

    public function getInfoPromocionByCod($codPromo, $formaPago) {
        return $this->ajaxController->getInfoPromocionByCodController($codPromo, $formaPago);
    }

    public function getVendedorByCod($codVendedor) {
        return $this->ajaxController->getVendedorByCodController($codVendedor);
    }

    public function generaProforma($IDDocument) {
      return $this->ajaxController->generaReporte($IDDocument, 'I');
    }

    public function saveCotizacion($formCotizacion){
      return $this->ajaxController->insertCotizacion($formCotizacion);
    }

    public function saveCotizacionMultiple($formCotizacion){
      $cotizacion1 = $this->ajaxController->insertCotizacion($formCotizacion);
      $cotizacion2 = $this->ajaxController->insertCotizacionDistribuidor($formCotizacion);
      return array('vendedor' => $cotizacion1, 'distribuidor' => $cotizacion2);
    }

    public function saveNuevoCliente($formData){
      return $this->ajaxController->saveNuevoClienteController($formData);
    }

    public function saveExtraData($extraData){
      return $this->ajaxController->insertExtraDataController($extraData);
    }

    public function sendEmail($IDDocument){
      return $this->ajaxController->sendCotizacion($IDDocument);
    }

    public function sendEmailByCustomEmail($arrayEmails, $IDDocument, $customMessage){
      return $this->ajaxController->sendCotizacionToEmails($arrayEmails, $IDDocument, $customMessage);
    }

    

}

  /* Cuerpo del API */

  try{
    $ajax = new ajax(); //Instancia que controla las acciones
    $HTTPaction = isset($_GET["action"]) ? $_GET["action"] : '';

    switch ($HTTPaction) {

       /* Obtiene array de informacion del cliente*/ 
        case 'getAPITramaco_login':
          echo $ajax->getAPITramaco_login();
        break;

        case 'getAPITramaco_costo_envio':
          if (isset($_GET['codParroquiaDest']) && isset($_GET['peso']) ) {
            $codParroquiaDest = $_GET['codParroquiaDest'];
            $peso = $_GET['peso'];
            $responsetramaco = $ajax->getAPITramaco_costo_envio($codParroquiaDest, $peso);
            $responsetramacoDecode = json_decode($responsetramaco);
            
            if (isset($responsetramacoDecode->salidaCalcularPrecioGuiaWs)) {
              $totalCalculadoTramaco = $responsetramacoDecode->salidaCalcularPrecioGuiaWs->lstGuias[0]->totalCalculado;
              $envioKAO = round($totalCalculadoTramaco * COSTO_EXTRA_ENVIO_PORCENT, 2);
              $rawdata = array('responsetramaco' => $responsetramacoDecode, 'envioTramaco' => $totalCalculadoTramaco, 'envioKAO' => $envioKAO,);
            }else{
              $rawdata = $responsetramacoDecode;
            }

          }else{
            $rawdata = array('status' => 'ERROR', 'mensaje' => 'No se ha indicado parámetros.');
          }
          echo json_encode($rawdata);
        break;

        case 'getCodigoEnvio':
          if (isset($_GET['provincia']) && isset($_GET['canton']) && isset($_GET['parroquia'])) {
            $provincia = $_GET['provincia'];
            $canton = $_GET['canton'];
            $parroquia = $_GET['parroquia'];
            $respuesta = $ajax->getCodigoEnvio($provincia, $canton, $parroquia);
            $rawdata = array('status' => 'OK', 'mensaje' => 'Realizado', 'data' => $respuesta);
            
          }else{
            $rawdata = array('status' => 'ERROR', 'mensaje' => 'No se ha indicado parámetros.');
          }
        
          echo json_encode($rawdata);
        break;

        case 'getProvinciasTramaco':
          $respuesta = $ajax->getProvincias_tramaco();
          $rawdata = array('status' => 'OK', 'mensaje' => 'Realizado', 'data' => $respuesta);
        
          echo json_encode($rawdata);
        break;

        case 'getCantonesTramaco':
          $provincia = $_GET['provincia'];
          $respuesta = $ajax->getCantones_tramaco($provincia);
          $rawdata = array('status' => 'OK', 'mensaje' => 'Realizado', 'data' => $respuesta);
        
          echo json_encode($rawdata);
        break;

        case 'getParroquiasTramaco':
          $canton = $_GET['canton'];
          $respuesta = $ajax->getParroquias_tramaco($canton);
          $rawdata = array('status' => 'OK', 'mensaje' => 'Realizado', 'data' => $respuesta);
        
          echo json_encode($rawdata);
        break;

        case 'getProvinciasTramaco':
            $respuesta = $ajax->getProvincias_tramaco();
            $rawdata = array('status' => 'OK', 'mensaje' => 'Realizado', 'data' => $respuesta);
          
          echo json_encode($rawdata);
        break;
       
        case 'saveCotizacion':
          if (isset($_POST['formData'])) {
            $formData = json_decode($_POST['formData']);
            $respuesta = $ajax->saveCotizacion($formData);
            $rawdata = array('status' => 'OK', 'mensaje' => 'Realizado', 'data' => $respuesta, 'formDataSended' => $formData);
            
          }else{
            $rawdata = array('status' => 'ERROR', 'mensaje' => 'No se ha indicado parámetros.');
          }
        
          echo json_encode($rawdata);

        break;

        case 'saveCotizacionMultiple':
          if (isset($_POST['formData'])) {
            $formData = json_decode($_POST['formData']);
            $respuesta = $ajax->saveCotizacionMultiple($formData);
            $rawdata = array('status' => 'OK', 'mensaje' => 'Realizado', 'data' => $respuesta, 'formDataSended' => $formData);
            
          }else{
            $rawdata = array('status' => 'ERROR', 'mensaje' => 'No se ha indicado parámetros.');
          }
        
          echo json_encode($rawdata);

        break;

        case 'saveNuevoCliente':
          if (isset($_POST['formData'])) {
            $formData = json_decode($_POST['formData']);
            $respuesta = $ajax->saveNuevoCliente($formData);
            $rawdata = array('status' => 'success', 'mensaje' => 'Cliente registrado con éxito', 'data' => $respuesta, 'formDataSended' => $formData);
            
          }else{
            $rawdata = array('status' => 'ERROR', 'mensaje' => 'No se ha indicado parámetros.');
          }
        
          echo json_encode($rawdata);

        break;


        /* Obtiene array de informacion del cliente*/ 
        case 'getInfoCliente':
          if (isset($_GET['ruc'])) {
            $RUC = $_GET['ruc'];
            $respuesta = $ajax->getInfoCliente($RUC);
            $rawdata = array('status' => 'OK', 'mensaje' => 'respuesta correcta', 'data' => $respuesta);
          }else{
            $rawdata = array('status' => 'ERROR', 'mensaje' => 'No se ha indicado parámetros.');
          }
          
          echo json_encode($rawdata);

        break;

        /* Obtiene array de informacion del cliente*/ 
        case 'getInfoVENCAB':
          if (isset($_GET['IDDocument'])) {
            $IDDocument = $_GET['IDDocument'];
            $respuesta = $ajax->getInfoVENCAB($IDDocument);
            $rawdata = array('status' => 'OK', 'mensaje' => 'respuesta correcta', 'data' => $respuesta);
          }else{
            $rawdata = array('status' => 'ERROR', 'mensaje' => 'No se ha indicado parámetros.');
          }
          
          echo json_encode($rawdata);

        break;

        /* Obtiene array de informacion de movimientos del cliente*/ 
        case 'getInfoVENMOV':
          if (isset($_GET['IDDocument'])) {
            $IDDocument = $_GET['IDDocument'];
            $respuesta = $ajax->getInfoVENMOV($IDDocument);
            $rawdata = array('status' => 'OK', 'mensaje' => 'respuesta correcta', 'data' => $respuesta);
          }else{
            $rawdata = array('status' => 'ERROR', 'mensaje' => 'No se ha indicado parámetros.');
          }
          
          echo json_encode($rawdata);

        break;

        /* Obtiene array de los cliente*/ 
        case 'searchClientes':
          if (isset($_GET['terminoBusqueda']) && isset($_GET['tipoBusqueda'])) {
            $terminoBusqueda = $_GET['terminoBusqueda'];
            $tipoBusqueda = $_GET['tipoBusqueda'];
            $respuesta = $ajax->getAllClientes($terminoBusqueda,  $tipoBusqueda);
            $rawdata = array('status' => 'OK', 'mensaje' => 'respuesta correcta', 'data' => $respuesta);
          }else{
            $rawdata = array('status' => 'ERROR', 'mensaje' => 'No se ha indicado parámetros.');
          }
          
          echo json_encode($rawdata);

        break;

        /* Obtiene array de los productos*/ 
        case 'searchProductos':
          if (isset($_GET['terminoBusqueda']) && isset($_GET['tipoBusqueda'])) {
            $terminoBusqueda = $_GET['terminoBusqueda'];
            $tipoBusqueda = $_GET['tipoBusqueda'];
            $respuesta = $ajax->getAllProductos($terminoBusqueda,  $tipoBusqueda);
            $rawdata = array('status' => 'OK', 'mensaje' => 'respuesta correcta', 'data' => $respuesta);
          }else{
            $rawdata = array('status' => 'ERROR', 'mensaje' => 'No se ha indicado parámetros.');
          }
          
          echo json_encode($rawdata);

        break;

        /* Obtiene array de los documentos SP Winfenix*/ 
        case 'searchDocumentos':
          if (isset($_GET['fechaINI']) && isset($_GET['fechaFIN']) && isset($_GET['stringBusqueda']) && isset($_GET['tipoDOC']) ) {
            $fechaINI = date("Ymd", strtotime($_GET['fechaINI']));
            $fechaFIN = date("Ymd", strtotime($_GET['fechaFIN']));
            $stringBusqueda = $_GET['stringBusqueda'];
            $tipoDOC = $_GET['tipoDOC'];

            $respuesta = $ajax->getAllDocumentos($fechaINI,  $fechaFIN, $stringBusqueda, $tipoDOC);
            $rawdata = array('status' => 'OK', 'mensaje' => 'respuesta correcta', 'data' => $respuesta);
          }else{
            $rawdata = array('status' => 'ERROR', 'mensaje' => 'No se ha indicado parámetros.');
          }
          
          echo json_encode($rawdata);

        break;

          /* Obtiene array de los documentos SP Winfenix*/ 
        case 'generaProforma':
          if (isset($_GET['IDDocument'])) {
            $IDDocument = $_GET['IDDocument'];
          
            $PDFDocument = $ajax->generaProforma($IDDocument);
            //$rawdata = array('status' => 'OK', 'mensaje' => 'respuesta correcta', 'data' => $respuesta);
            echo $PDFDocument;
          }else{
            $rawdata = array('status' => 'ERROR', 'mensaje' => 'No se ha indicado parámetros.');
            echo json_encode($rawdata);
          }
        
        

        break;


        /* Obtiene array de informacion del producto*/ 
        case 'getInfoProducto':

          if (isset($_GET['codigo']) && isset($_GET['clienteRUC'])) {
            $codigoProducto = $_GET['codigo'];
            $clienteRUC =  $_GET['clienteRUC'];
            $respuesta = $ajax->getInfoProducto($codigoProducto, $clienteRUC);
            $rawdata = array('status' => 'OK', 'mensaje' => 'respuesta correcta', 'data' => $respuesta);
          }else{
            $rawdata = array('status' => 'ERROR', 'mensaje' => 'No se ha indicado parámetros.');
          }
          
        
          echo json_encode($rawdata);

        break;

         /* Obtiene array de informacion de la promociones*/ 
        case 'getInfoPromocion':

          if (isset($_GET['codPromo']) ) {
            $codPromo = $_GET['codPromo'];
            $respuesta = $ajax->getInfoPromocion($codPromo);
            $rawdata = array('status' => 'OK', 'mensaje' => 'respuesta correcta', 'data' => $respuesta);
          }else{
            $rawdata = array('status' => 'ERROR', 'mensaje' => 'No se ha indicado parámetros.');
          }
          echo json_encode($rawdata);

        break;

        /* Obtiene array de informacion de la promocion del producto*/ 
        case 'getInfoPromocionByCod':

          if (isset($_GET['codPromo']) && isset($_GET['formaPago']) ) {
            $codPromo = $_GET['codPromo'];
            $formaPago = $_GET['formaPago'];
            $respuesta = $ajax->getInfoPromocionByCod($codPromo, $formaPago);
            $rawdata = array('status' => 'OK', 'mensaje' => 'respuesta correcta', 'data' => $respuesta);
          }else{
            $rawdata = array('status' => 'ERROR', 'mensaje' => 'No se ha indicado parámetros.');
          }
          echo json_encode($rawdata);

        break;

        /* Obtiene array de informacion de la promocion del producto*/ 
        case 'getVendedor':

          if (isset($_GET['codVendedor']) ) {
            $codVendedor = $_GET['codVendedor'];
            $respuesta = $ajax->getVendedorByCod($codVendedor);
            $rawdata = array('status' => 'OK', 'mensaje' => 'respuesta correcta', 'data' => $respuesta);
          }else{
            $rawdata = array('status' => 'ERROR', 'mensaje' => 'No se ha indicado parámetros.');
          }
          echo json_encode($rawdata);

        break;
        

        /* Utiliza PHPMailer para el envio de correo, utiliza los correos del cliente indicados en la tabla*/ 
        case 'sendEmail':

          if (isset($_GET['IDDocument']) ) {
            $IDDocument = $_GET['IDDocument'];
            $respuesta = $ajax->sendEmail($IDDocument);
            $rawdata = array('status' => 'OK', 'mensaje' => 'respuesta correcta', 'data' => $respuesta);
          }else{
            $rawdata = array('status' => 'ERROR', 'mensaje' => 'No se ha indicado parámetros.' );
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
            $rawdata = array('status' => 'OK', 'mensaje' => 'respuesta correcta', 'data' => $respuesta);
          }else{
            $rawdata = array('status' => 'ERROR', 'mensaje' => 'No se ha indicado parámetros.' );
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

                      $obj->mensaje = 'Archivo cargado: ' . $array_files["name"][$cont] . ', nuevo nombre asignado: '.$newname;
                      array_push($archivosCargados, $obj);
                  }
              }
              }
              $contador++;
            }

            $rawdata = array('status' => 'OK', 'mensaje' => 'Carga completa', 'resultados' => $archivosCargados,  'errores' => $errores);
            echo json_encode($rawdata);

          }else {
            $rawdata = array('status' => 'FAIL', 'mensaje' => 'Sin archivos seleccionados o codigo de Orden no indicada.');
            echo json_encode($rawdata);
          }
        
        break;

        case 'saveExtraData':
          if (isset($_POST['extraData'])) {
            $extraData = json_decode($_POST['extraData']);
            $respuesta = $ajax->saveExtraData($extraData);
            $rawdata = array('status' => 'OK', 'mensaje' => 'salvar extra data', 'respuesta' => $respuesta);
            
          }else{
            $rawdata = array('status' => 'ERROR', 'mensaje' => 'No se ha recibido extra data.');
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


