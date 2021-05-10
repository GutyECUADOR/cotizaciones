<?php

session_start();
require_once '../vendor/autoload.php';
$dotenv = \Dotenv::createImmutable(__DIR__);
$dotenv->load();

$ajax = new App\Models\AjaxModel();
$ajax->setDbname('MODELO1');
$ajax->conectarDB();

$stmt = $this->ajax->prepare("exec Sp_Contador 'INV','99','','STK',''"); 
$stmt->execute();

$newCodLimpio = $stmt->fetch(\PDO::FETCH_ASSOC);
$STK_ID =  str_pad($newCodLimpio['NExtID'], 8, '0', STR_PAD_LEFT);

var_dump($STK_ID);