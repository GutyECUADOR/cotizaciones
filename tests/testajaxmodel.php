<?php

use App\Models\AjaxModel;

session_start();
require_once '../vendor/autoload.php';

$ajax = new AjaxModel();
$ajax->setDbname('MODELO1');
$ajax->conectarDB();

$NextID = $ajax->getNextNumDocWINFENIX('COT');

$response = $ajax->formatoNextNumDocWINFENIX('MODELO1', $NextID);

var_dump($response);