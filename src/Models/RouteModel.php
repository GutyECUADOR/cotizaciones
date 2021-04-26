<?php namespace App\Models;

class RouteModel {
    
    public function actionCatcherModel($action){
        switch ($action) {
            case 'inicio':
                $contenido = "views/modulos/inicioView.php";
                break;

            
            case 'cotizaciones':
                $contenido = "views/modulos/puntodeVentaView.php";
                break;

            /* Módulo de Inventario */

            case 'formularioCortes':
                $contenido = "views/modulos/formularioCortesView.php";
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
                $contenido = "views/modulos/inicioView.php";
                break;
        }
        
       
        return $contenido;
        
    }
}
