<?php
use App\Controllers\ShopifyController;
use Dotenv\Dotenv;

header('Content-Type: application/json');
date_default_timezone_set('America/Lima');
session_start();

require_once '../../vendor/autoload.php';
$dotenv = Dotenv::createImmutable('../../');
$dotenv->load();

$shopifyController = new ShopifyController();

  try{
    $HTTPaction = isset($_GET["action"]) ? $_GET["action"] : '';

    switch ($HTTPaction) {

        case 'getProducto':
          if (isset($_GET['codigo'])) {
            $codigo = $_GET['codigo'];
            $respuesta = $shopifyController->getProducto($codigo);
            echo $respuesta;
          }else{
            http_response_code(400);
            $rawdata = array('status' => 'ERROR', 'message' => 'No se ha indicado codigo del producto.');
            echo json_encode($rawdata);
          }
         

        break;

        /* Create products in INV_ARTICULOS_SHOPIFYMASTER */
        case 'syncProductosShopify':
          $respuesta = $shopifyController->syncProductosShopify();
          echo json_encode($respuesta);
  
        break;  
       
        case 'syncProductPricesShopify':
          $respuesta = $shopifyController->syncProductPricesShopify();
          echo json_encode($respuesta);
  
        break;  

        case 'syncProductStocksShopify':
          $respuesta = $shopifyController->syncProductStocksShopify();
          echo json_encode($respuesta);
  
        break;  

         /* Create products in Shopify API */
         case 'syncCreateProductInShopify':
          if (isset($_POST['product'])) {
            $product = $_POST['product']; /* Producto segun la especificacion de Shopify*/
            $rawdata = $shopifyController->syncCreateProductInShopify($product);
          }else{
            http_response_code(400);
            $rawdata = array('status' => 'error', 'message' => 'No se ha indicado parámetros.');
          }
          
          echo json_encode($rawdata);

  
        break;  
        
        

        case 'getAllProductos_Shopy_Master':
          if (isset($_GET['busqueda'])) {
            $busqueda = json_decode($_GET['busqueda']);
            $respuesta = $shopifyController->getAllProductos_Shopy_Master($busqueda);
            $rawdata = array('status' => 'success', 'mensaje' => 'respuesta correcta', 'busqueda'=> $busqueda, 'productos' => $respuesta);
          }else{
            http_response_code(400);
            $rawdata = array('status' => 'error', 'mensaje' => 'No se ha indicado parámetros.');
          }
          
          echo json_encode($rawdata);

        break;


        
        case 'getAllProductosSinSincronizar':
          if (isset($_GET['busqueda'])) {
            $busqueda = json_decode($_GET['busqueda']);
            $respuesta = $shopifyController->getAllProductosSinSincronizar($busqueda);
            $rawdata = array('status' => 'success', 'message' => 'respuesta correcta', 'busqueda'=> $busqueda, 'productos' => $respuesta);
          }else{
            http_response_code(400);
            $rawdata = array('status' => 'error', 'mensaje' => 'No se ha indicado parámetros.');
          }
          
          echo json_encode($rawdata);

        break;
  
        case 'getAllProductos_Shopy_Master_WithVariants':
          if (isset($_GET['busqueda'])) {
            $busqueda = json_decode($_GET['busqueda']);
            $respuesta = $shopifyController->getAllProductos_Shopy_Master_WithVariants($busqueda);
            $rawdata = array('status' => 'success', 'mensaje' => 'respuesta correcta', 'busqueda'=> $busqueda, 'productos' => $respuesta);
          }else{
            http_response_code(400);
            $rawdata = array('status' => 'error', 'mensaje' => 'No se ha indicado parámetros.');
          }
          
          echo json_encode($rawdata);

        break;
  
        case 'getCollections_ShopifyByID':
          if (isset($_GET['id'])) {
            $id = $_GET['id'];
            echo $shopifyController->getCollections_ShopifyByID($id);
          }else{
            http_response_code(400);
            throw new Exception("Error no se envio el ID del producto de shopify");
          }
        break;
  
        case 'getInfoCollection_ShopifyByID':
          if (isset($_GET['id'])) {
            $id = $_GET['id'];
            echo $shopifyController->getInfoCollection_ShopifyByID($id);
          }else{
            http_response_code(400);
            throw new Exception("Error no se envio el ID del producto de shopify");
          }
        break;

        case 'getInfoProductoShopify':
          if (isset($_GET['id'])) {
            $id = $_GET['id'];
            echo $shopifyController->getInfoProductoShopify($id);
          }else{
            http_response_code(400);
            throw new Exception("Error no se envio el ID del producto de shopify");
          }
          
        break;
  
        case 'getProductVariantInfo_ShopifyByID':
          if (isset($_GET['id'])) {
            $id = $_GET['id'];
            echo $shopifyController->getProductVariantInfo_ShopifyByID($id);
          }else{
            http_response_code(400);
            throw new Exception("Error no se envio el ID de la variante de shopify");
          }
          
        break;
          
        case 'getAllProductos':
          if (isset($_GET['busqueda'])) {
            $busqueda = $_GET['busqueda'];
            $respuesta = $shopifyController->getAllProductos($busqueda);
            $rawdata = array('status' => 'OK', 'mensaje' => 'respuesta correcta', 'productos' => $respuesta);
          }else{
            $rawdata = array('status' => 'ERROR', 'mensaje' => 'No se ha indicado termino para busqueda del producto.');
          }
          
          echo json_encode($rawdata);

        break;
  
        case 'getInfoInitForm':
          $respuesta = $shopifyController->getInfoInitForm();
          $rawdata = array('status' => 'success', 'mensaje' => 'respuesta correcta', 'data'=> $respuesta);
          echo json_encode($rawdata);

        break;

        case 'postAddNewProducto_Shopy_Master':
          if (isset($_POST['productos'])) {
            $productos = json_decode($_POST['productos']);
            $rawdata = $shopifyController->postAddNewProducto_Shopy_Master($productos);
          }else{
            http_response_code(400);
            $rawdata = array('status' => 'error', 'message' => 'No se ha indicado parámetros.');
          }
          
          echo json_encode($rawdata);

        break;

        case 'postActualizaProducto_Shopy_Master':
          if (isset($_POST['producto'])) {
            $producto = json_decode($_POST['producto']);
            $respuesta = $shopifyController->postActualizaProducto_Shopy_Master($producto);
            $rawdata = array('status' => 'success', 'mensaje' => 'Producto Actualizado Correctamente', 'producto' => $producto, 'respuesta' => $respuesta);
          }else{
            http_response_code(400);
            $rawdata = array('status' => 'error', 'mensaje' => 'No se ha indicado parámetros.');
          }
          
          echo json_encode($rawdata);

        break;

        case 'getOrders':
          if (isset($_GET['busqueda'])) {
            $busqueda = json_decode($_GET['busqueda']);
            $respuesta = $shopifyController->getOrders($busqueda->fechaFIN);
            $rawdata = array('status' => 'OK', 'message' => 'respuesta correcta', 'orders' => $respuesta);
          }else{
            http_response_code(400);
            $rawdata = array('status' => 'ERROR', 'message' => 'No se ha indicado termino para busqueda del producto.');
          }
          
          echo json_encode($rawdata);

        break;


        case 'postGenerarDocumentoSPY':
          if (isset($_POST['order'])) {
            $order = json_decode($_POST['order']);
            $rawdata = $shopifyController->postGenerarDocumentoSPY($order);
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


