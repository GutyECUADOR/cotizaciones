<?php namespace App\Controllers;

use App\Models\VenCabClass;
use App\Models\VenMovClass;
use App\Models\CotizacionesModel;
use App\Models\WinfenixModel;

class CotizacionesController  {

    private $defaulDataBase;
    private $model;
    private $winfenixModel;

    public function __construct() {
        $this->defaulDataBase = (!isset($_SESSION["empresaAUTH".APP_UNIQUE_KEY])) ? DEFAULT_DBName : $_SESSION["empresaAUTH".APP_UNIQUE_KEY] ;
        $this->model = new CotizacionesModel();
        $this->model->setDbname($this->defaulDataBase);
        $this->model->conectarDB();
        $this->winfenixModel = new WinfenixModel();
        $this->winfenixModel->setDbname($this->defaulDataBase);
        $this->winfenixModel->conectarDB();
    }

    public function getDocumentos(object $busqueda) {
        $response = $this->winfenixModel->sql_buscarDocumentos($busqueda);
        return $response;
    }

    public function getClientes(object $busqueda) {
        $response = $this->winfenixModel->SP_COBCONCLI($busqueda);
        return $response;
    }

    public function getCliente(string $RUC){
        $response = $this->model->SQL_getCliente($RUC);
        return $response;
    }

    public function saveNuevoCliente(object $nuevoCliente){
        $newCodigo = $this->model->getNextNumClienteWF();
        return $this->model->insertNuevoCliente($nuevoCliente, $newCodigo);
    }

    public function getProductos(object $busqueda){
        return $this->winfenixModel->Sp_INVCONARTWAN($busqueda);
    }

    public function getProducto(string $codigo) {
        return $this->model->getProducto($codigo);
    }

    public function getStock(object $busqueda) {
        $response = array('stock' => $this->model->SQL_getStock($busqueda->texto),
                          'stockComponentes' => $this->model->SQL_getStockComponentes($busqueda),
                          'stockRetazos' => $this->model->SQL_getStockRetazos($busqueda->texto)
                        );
        return $response; 
    }

    public function getBodegas(){
        $bodegas =  $this->winfenixModel->sql_getBodegasWF();
        return $bodegas;
    }

    public function getVendedores(){
        $bodegas =  $this->winfenixModel->sql_getVendedoresWF();
        return $bodegas;
    }

    public function getFormasPago(){
        $response =  $this->winfenixModel->sql_getFormasPagoWF();
        return $response;
    }

    public function getVenTiposDOCWF(){
        $response =  $this->winfenixModel->sql_getVenTiposDOCWF();
        return $response;
    }

    public function getTiposPagoTarjeta(){
        $response =  $this->winfenixModel->sql_getTiposPagoTarjetaWF();
        return $response;
    }

    public function getGrupos(){
        $response =  $this->winfenixModel->sql_getGruposClientesWF();
        return $response;
    }

    public function getCantones(){
        $response =  $this->winfenixModel->sql_getCantonesWF();
        return $response;
    }

    public function saveCotizacion_old(object $formData){
        $VEN_CAB = new VenCabClass();
        $tipoDOC = 'COT';

        if (!empty($formData)) {

            try {
               //Obtenemos informacion de la empresa
                $datosEmpresa =  $this->winfenixModel->getDatosEmpresa();
                $serieDocs =  $this->winfenixModel->getVenTipos($tipoDOC)['Serie'];
            
                //Informacion extra del cliente
                $datosCliente = $this->model->SQL_getCliente($formData->cliente->RUC);

                //Creamos nuevo codigo de VEN_CAB (secuencial)
                $newCodigoWith0 =  $this->winfenixModel->SP_contador($tipoDOC, 'VEN'); 
               
                $new_cod_VENCAB = $datosEmpresa['Oficina'].$datosEmpresa['Ejercicio'].$tipoDOC.$newCodigoWith0;
                
                /* NOTA SE ESTABLECE DESCUENTO EN 0 TANTO PARA CABECERA COMO DETALLE */

                $VEN_CAB->setCliente($datosCliente['CODIGO']);
                $VEN_CAB->setTipoPrecio($datosCliente['TIPOPRECIO']);
                $VEN_CAB->setVendedor($datosCliente['VENDEDOR']);
                $VEN_CAB->setPorcentDescuento(0);
                $VEN_CAB->setPcID(php_uname('n'));
                $VEN_CAB->setOficina($datosEmpresa['Oficina']);
                $VEN_CAB->setEjercicio($datosEmpresa['Ejercicio']);
                $VEN_CAB->setTipoDoc($tipoDOC);
                $VEN_CAB->setNumeroDoc($newCodigoWith0);
                $VEN_CAB->setFecha(date('Ymd'));
                
                $VEN_CAB->setBodega('B01');
                $VEN_CAB->setDivisa('DOL');
                $VEN_CAB->setProductos($formData->productos);
                $VEN_CAB->setSubtotal($VEN_CAB->calculaSubtotal());
                $VEN_CAB->setsubtotalBase0($VEN_CAB->calculaSubtotalOfItemsWithIVA0());
                $VEN_CAB->setImpuesto($VEN_CAB->calculaIVA());
                $VEN_CAB->setTotal($VEN_CAB->calculaTOTAL());
                $VEN_CAB->setFormaPago($formData->cliente->formaPago);
                $VEN_CAB->setSerie($serieDocs); 
                $VEN_CAB->setSecuencia('0'.$newCodigoWith0); //Agregar 0 extra segun winfenix
                $VEN_CAB->setObservacion('WebForms, ' . $formData->comentario);
                
                //Registro en VEN_CAB y MOV mantenimientosEQ
                $response_VEN_CAB =  $this->winfenixModel->SP_VENGRACAB($VEN_CAB);

                $arrayVEN_MOVinsets = array();

                if (!empty($VEN_CAB->getProductos())) {

                    foreach ($VEN_CAB->getProductos() as $producto) {
                        $VEN_MOV = new VenMovClass();
                      
                        $VEN_MOV->setCliente($datosCliente['CODIGO']);
                      
                        $VEN_MOV->setOficina($datosEmpresa['Oficina']);
                        $VEN_MOV->setEjercicio($datosEmpresa['Ejercicio']);
                        $VEN_MOV->setTipoDoc($tipoDOC);
                        $VEN_MOV->setTipoPrecio($datosCliente['TIPOPRECIO']);
                        $VEN_MOV->setVendedor($datosCliente['VENDEDOR']);
                        $VEN_MOV->setNumeroDoc($newCodigoWith0);
                        $VEN_MOV->setFecha(date('Ymd h:i:s'));
                        $VEN_MOV->setBodega('B01');
                        $VEN_MOV->setCodProducto(strtoupper($producto->codigo));
                        $VEN_MOV->setCantidad($producto->cantidad);
                        $VEN_MOV->setPrecioProducto($producto->precio);
                        $VEN_MOV->setPorcentajeDescuentoProd(0);
                        $VEN_MOV->setTipoIVA('T00');
                        $VEN_MOV->setPorcentajeIVA($producto->valorIVA);
                        $VEN_MOV->setPrecioTOTAL($VEN_MOV->calculaPrecioTOTAL());
                        $VEN_MOV->setObservacion('');
                        
                        $response_VEN_MOV =  $this->winfenixModel->SP_VENGRAMOV($VEN_MOV);
                        
                        array_push($arrayVEN_MOVinsets, $response_VEN_MOV);

                    }
                }
            } catch (\Exception $e) {
                return array('status' => 'ERROR', 
                    'mensaje'  => 'No se pudo completar la operacion'. $e->getMessage(),
                ); 
            }
            
            return array('status' => 'OK', 
                    'mensaje'  => 'Documento registrado.',
                    'new_cod_VENCAB' => $new_cod_VENCAB,
                    'newCodigoWith0' => $newCodigoWith0,
                    'response_VEN_CAB' => $response_VEN_CAB,
                    'arrayVEN_MOVinsets' => $arrayVEN_MOVinsets
                ); 

        }
       
        

        
        
    }

    public function saveCotizacion(object $documento){
        $response =  $this->winfenixModel->SP_VENGRACAB($documento);
        return $response;

        
        
    }
    


}
