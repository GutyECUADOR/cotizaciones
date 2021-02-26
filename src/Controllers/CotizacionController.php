<?php namespace App\Controllers;

use App\Models\CotizacionClass;

class CotizacionController  {

    public $defaulDataBase;
    public $cotizacion;

    public function __construct() {
        $this->defaulDataBase = $_SESSION["empresaAUTH".APP_UNIQUE_KEY];
        $this->cotizacion = new CotizacionClass();
        $this->cotizacion->setDbname($this->defaulDataBase);
        $this->cotizacion->conectarDB();
    }
    
    public function getStatusDataBase (){
        $infoEmpresa = $this->cotizacion->getInfoEmpresa();
        if ($infoEmpresa) {
            return '
                <div class="alert alert-info alertExtra" role="alert">
                <span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
                <span class="sr-only"></span>
                    Conexion a: <strong>'. $infoEmpresa['NomCia'] .' </strong>
                </div>';
        }else {
            return '
                <div class="alert alert-danger alertExtra" role="alert">
                <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                <span class="sr-only">Error:</span>
                    No se ha podido establecer conexion a la base de datos.
                </div>
            ';
             
        }
    }

    public function getBodegas(){
        $bodegas =  $this->cotizacion->getBodegasWF();
        return $bodegas;
    }

    public function getVendedores(){
        $bodegas =  $this->cotizacion->getVendedoresWF();
        return $bodegas;
    }

    public function getFormasPago(){
        $response =  $this->cotizacion->getFormasPagoWF();
        return $response;
    }

    public function getVenTiposDOCWF(){
        $response =  $this->cotizacion->getVenTiposDOCWF();
        return $response;
    }

    public function getTiposPagoTarjeta(){
        $response =  $this->cotizacion->getTiposPagoTarjetaWF();
        return $response;
    }

    public function getGrupos(){
        $response =  $this->cotizacion->getGruposClientesWF();
        return $response;
    }

    public function getCantones(){
        $response =  $this->cotizacion->getCantonesWF();
        return $response;
    }
}