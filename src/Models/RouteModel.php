<?php namespace App\Models;

use App\Middleware\RouteMiddleware;

class RouteModel extends Conexion {

    public $middleware;

    public function __construct() {
        parent::__construct();
        $this->middleware = new RouteMiddleware();
    }
    
    public function actionCatcherModel($action){
        switch ($action) {
            case 'inicio':
                $contenido = "views/modulos/inicioView.php";
                break;

            case 'ordenPedidoVehiculo':
                $contenido = "views/modulos/ordenPedidoVehiculoView.php";
                break;

            case 'valesPerdida' :
                if ($this->middleware->validateAccessVales()) {
                    $contenido = "views/modulos/valesPerdidaView.php";
                }else{
                    $contenido = "views/modulos/valesPerdidaErrorView.php";
                }
                break;

            case 'aprobacionValesPerdida':
                $contenido = "views/modulos/aprobacionValesPerdidaView.php";
                break;

             /* MODULO DE VENTAS */
            case 'ventas':
                $contenido = "views/modulos/ventasView.php";
                break;
            
            case 'puntodeVenta':
                $contenido = "views/modulos/puntodeVentaView.php";
                break;

            case 'ventasPorMayor':
                $contenido = "views/modulos/ventasPorMayorView.php";
                break;

            case 'cotizaciones':
                $contenido = "views/modulos/cotizacionesView.php";
                break;

            case 'cotizacionPorMayor':
                $contenido = "views/modulos/cotizacionPorMayorView.php";
                break;

            case 'consultaEDocs':
                $contenido = "views/modulos/consultaEDocsView.php";
                break;


            /* MODULO DE INVENTARIO */
            case 'inventario':
                $contenido = "views/modulos/inventarioView.php";
                break;
            
            case 'actualizarNombresProductos':
                $contenido = "views/modulos/actualizarNombresProductosView.php";
                break;

            case 'actualizarPreciosProductos':
                $contenido = "views/modulos/actualizarPreciosProductosView.php";
                break;

            case 'actualizarColeccionProductos':
                $contenido = "views/modulos/actualizarColeccionProductosView.php";
                break;

            case 'actualizarMarcaProductos':
                $contenido = "views/modulos/actualizarMarcaProductosView.php";
                break;

            case 'solicitudSuministros':
                $contenido = "views/modulos/solicitudSuministrosOficinaView.php";
                break;
                
            case 'ordenCompraSuministros':
                $contenido = "views/modulos/ordenCompraSuministros.php";
                break;

            case 'ordenCompraSuministros_aprobacion':
                $contenido = "views/modulos/ordenCompraSuministros_aprobacion.php";
                break;
                    

             /* MODULO DE UTILITARIOS */
             case 'utilitarios':
                $contenido = "views/modulos/utilitariosView.php";
                break;
            
            case 'cargaNuevosClientes':
                $contenido = "views/modulos/cargaNuevosClientesView.php";
                break;

            case 'cuestionarios':
                $contenido = "views/modulos/cuestionariosView.php";
                break;

            case 'valesPerdida':
                $contenido = "views/modulos/valesPerdidaView.php";
                break;

            case 'generacionEAN13':
                $contenido = "views/modulos/generacionEAN13View.php";
                break;

             /* MODULO DE SHOPIFY */
             case 'shopify':
                $contenido = "views/modulos/shopifyView.php";
                break;
            
            case 'listaProductosShopify':
                $contenido = "views/modulos/listaProductosShopifyView.php";
                break;

            case 'nuevoProductoShopify':
                $contenido = "views/modulos/nuevoProductoShopifyView.php";
                break;   
                
            case 'cargaProductosShopify':
                $contenido = "views/modulos/cargaProductosShopifyView.php";
                break;

            case 'syncProductosShopify':
                $contenido = "views/modulos/syncProductosShopifyView.php";
                break;
            
            case 'busquedaProductosShopify':
                $contenido = "views/modulos/busquedaProductosShopifyView.php";
                break;

            case 'transaccionCompraSPY':
                $contenido = "views/modulos/transaccionCompraSPYView.php";
                break;

            /* MODULO DE GENERACION DE GUIAS */
             case 'guias':
                $contenido = "views/modulos/guiasView.php";
                break;
            
            case 'guiasLocales':
                $contenido = "views/modulos/guiasLocalesTramacoView.php";
                break;

            case 'guiasShopify':
                $contenido = "views/modulos/guiasShopifyTramacoView.php";
                break;   
           
            // ADMINISTRACION 
            case 'dashboard':
                $contenido = "views/modulos/admin/dashboardView.php";
                break;

            case 'usuarios':
                $contenido = "views/modulos/admin/usuariosView.php";
                break;

            case 'variables':
                $contenido = "views/modulos/admin/variablesView.php";
                break;

            case 'estadodelVehiculoAdmin':
                $contenido = "views/modulos/admin/estadodelVehiculoAdminView.php";
                break;

            case 'crearPagoVehiculo':
                $contenido = "views/modulos/admin/crearPagoVehiculoView.php";
                break;

            case 'login':
            $contenido = "views/modulos/loginView.php";
                break;    
         
            case 'logout':
            $contenido = "views/modulos/cerrarSesion.php";
                break; 

            default:
                $contenido = "views/modulos/loginView.php";
                break;
        }
        
       
        return $contenido;
        
    }

    public function getMenus(string $action){
        $query = "
        SELECT * FROM wssp.dbo.sys_menus 
            WHERE modulo IN (SELECT modulo  FROM wssp.dbo.sys_menus WHERE action = :action)
        ORDER BY orden  
        ";
        $stmt = $this->instancia->prepare($query); 
        $stmt->bindParam(':action', $action); 
        $stmt->execute();
            if($stmt->execute()){
                $resulset = $stmt->fetchAll( \PDO::FETCH_ASSOC);
            }else{
                $resulset = false;
            }
        return $resulset;  
    }
}
