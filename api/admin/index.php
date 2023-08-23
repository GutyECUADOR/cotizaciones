<?php

use App\Controllers\AdminController;
use Dotenv\Dotenv;

header('Content-Type: application/json');
date_default_timezone_set('America/Lima');
session_start();

require_once '../../vendor/autoload.php';
$dotenv = Dotenv::createImmutable('../../');
$dotenv->load();

$adminController = new AdminController();

  try{
    $HTTPaction = isset($_GET["action"]) ? $_GET["action"] : '';

    switch ($HTTPaction) {

        case 'getPerfiles':
          $respuesta = $adminController->getPerfiles();
          if ($respuesta) {
            $rawdata = array('status' => 'OK', 'message' => 'respuesta correcta', 'perfiles' => $respuesta);
          }else{
            http_response_code(400);
            $rawdata = array('status' => 'ERROR', 'message' => 'No se ha indicado parámetros de búsqueda.');
          }
          echo json_encode($rawdata);

        break;

        case 'getAccesosPerfil':
          if (isset($_GET['perfilID'])) {
            $id = trim($_GET['perfilID']);
            $respuesta = $adminController->getAccesosPerfil($id);
            $rawdata = array('status' => 'OK', 'message' => 'respuesta correcta', 'modulos' => $respuesta);
          }else{
            http_response_code(400);
            $rawdata = array('status' => 'ERROR', 'message' => 'No se ha indicado parámetros de búsqueda.');
          }
          echo json_encode($rawdata);

        break;

        case 'getModulos':
          if (isset($_GET['busqueda'])) {
            $busqueda = json_decode($_GET['busqueda']);
            $respuesta = $adminController->getModulos($busqueda);
            $rawdata = array('status' => 'OK', 'message' => 'respuesta correcta', 'modulos' => $respuesta);
          }else{
            http_response_code(400);
            $rawdata = array('status' => 'ERROR', 'message' => 'No se ha indicado parámetros de búsqueda.');
          }
          echo json_encode($rawdata);

        break;

        case 'addNewPermiso':
          if (isset($_POST['permiso'])) {
            $permiso = json_decode($_POST['permiso']);
            $rawdata = $adminController->addNewPermiso($permiso);
          }else{
            http_response_code(400);
            $rawdata = array('status' => 'ERROR', 'message' => 'No se ha indicado parámetros.');
          }
          echo json_encode($rawdata);
        break;

        case 'removePermiso':
          if (isset($_POST['permiso'])) {
            $permiso = json_decode($_POST['permiso']);
            $rawdata = $adminController->removePermiso($permiso);
          }else{
            http_response_code(400);
            $rawdata = array('status' => 'ERROR', 'message' => 'No se ha indicado parámetros.');
          }
          echo json_encode($rawdata);
        break;

        case 'getVariables':
          $respuesta = $adminController->getVariables();
          if ($respuesta) {
            $rawdata = $respuesta;
          }else{
            http_response_code(400);
            $rawdata = array('status' => 'ERROR', 'message' => 'No se ha indicado parámetros de búsqueda.');
          }
          echo json_encode($rawdata);

        break;


        case 'updateVariable':
          if (isset($_POST['variable'])) {
            $variable = json_decode($_POST['variable']);
            $rawdata = $adminController->updateVariable($variable);
          }else{
            http_response_code(400);
            $rawdata = array('status' => 'ERROR', 'message' => 'No se ha indicado parámetros.');
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


