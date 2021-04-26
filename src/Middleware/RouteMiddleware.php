<?php namespace App\Middleware;

class RouteMiddleware {

    public function checkisLogin(){
        if (!isset($_SESSION["usuarioRUC".APP_UNIQUE_KEY])){
            $preaction = $_GET['action'];
            header("Location:index.php?&action=login&preaction=$preaction");  
        }
    }
}