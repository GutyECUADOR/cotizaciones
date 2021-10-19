<?php namespace App\Models;

class RouteModel extends Conexion {
    
    public function actionCatcherModel($action){
        switch ($action) {
            case 'inicio':
                $contenido = "views/modulos/inicioView.php";
                break;

            /* MODULO DE VENTAS */
            case 'ventas':
                $contenido = "views/modulos/ventasView.php";
                break;
            
            case 'puntodeVenta':
                $contenido = "views/modulos/puntodeVentaView.php";
                break;

            case 'informeComisiones':
                $contenido = "views/modulos/informeComisionesView.php";
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

    public function getMenus(){
        $query = "SELECT * FROM wssp.dbo.sys_menus ORDER BY orden";
        $stmt = $this->instancia->prepare($query); 
        $stmt->execute();
            if($stmt->execute()){
                $resulset = $stmt->fetchAll( \PDO::FETCH_ASSOC);
            }else{
                $resulset = false;
            }
        return $resulset;  
    }
}
