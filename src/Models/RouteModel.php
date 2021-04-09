<?php namespace App\Models;

class RouteModel {
    
    public function actionCatcherModel($action){
        switch ($action) {
            case 'inicio':
                $contenido = "views/modulos/inicio.php";
                break;

            
            case 'cotizaciones':
                $contenido = "views/modulos/puntodeVentaView.php";
                break;

            /* Módulo de Inventario */

            case 'inventario':
                $contenido = "views/modulos/inventarioView.php";
                break;

            case 'creacionReceta':
                $contenido = "views/modulos/creacionRecetaView.php";
                break;

            /* Shared Options */

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
