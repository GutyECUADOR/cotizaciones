<?php namespace App\Models;

class RouteModel {
    
    public function actionCatcherModel($action){
        switch ($action) {
            case 'inicio':
                $contenido = "views/modulos/inicio.php";
                break;

            case 'cotizaciones':
                $contenido = "views/modulos/cotizacionesView.php";
                break;

            case 'inventario':
                $contenido = "views/modulos/inventarioView.php";
                break;

            case 'login':
            $contenido = "views/modulos/loginView.php";
                break;    
         
            case 'logout':
            $contenido = "views/modulos/cerrarSesion.php";
                break; 

            default:
                $contenido = "views/modulos/inicio.php";
                break;
        }
        
       
        return $contenido;
        
    }
}
