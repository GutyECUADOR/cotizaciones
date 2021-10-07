<?php 

    use Dotenv\Dotenv;
    session_start();

require_once '../vendor/autoload.php';
$dotenv = Dotenv::createImmutable('../');
$dotenv->load();

$ajax = new App\Models\WinfenixModel();
$ajax->setDbname('MODELO');
$ajax->conectarDB();

/* $tipoDOC = 'COT';
$datosEmpresa =  $ajax->getDatosEmpresa();
$numeroDOC =  $ajax->SP_contador($tipoDOC, 'VEN'); 
$new_cod_VENCAB = $datosEmpresa['Oficina'].$datosEmpresa['Ejercicio'].$tipoDOC.$numeroDOC; */

/* $tipoDOC = 'COT';
$serieDocs =  $ajax->getVenTipos($tipoDOC)['Serie']; 
var_dump($serieDocs);
*/

/* $ajax = new App\Models\CotizacionesModel();
$ajax->setDbname('MODELO');
$ajax->conectarDB();

$response = $ajax->SQL_getProductoRelacionado('00000841', 'ML')['CODIGO'];
var_dump($response); */


/* $VEN_MOV =  $ajax->SQL_getVENMOV('992014COT00023344');
echo (json_encode($VEN_MOV)); */

$VEN_CAB = $ajax->SQL_getVENCAB('992014COT00023345');
echo (json_encode($VEN_CAB));