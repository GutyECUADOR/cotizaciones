<?php

use App\Models\AjaxModel;

session_start();
require_once '../vendor/autoload.php';

$ajax = new AjaxModel();
$ajax->setDbname('MODELO1');
$ajax->conectarDB();

$response = $ajax->getVENCABByID('992020COT00000003');

var_dump($response);