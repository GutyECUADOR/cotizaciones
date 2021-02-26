<?php namespace App\Controllers;

    use Dotenv\Dotenv;
    require_once './vendor/autoload.php';

    date_default_timezone_set('America/Lima');
    @ob_start();
    session_start();

    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    
    $app = new RouteController();
    $app->loadtemplate();
   

    