<?php namespace App\Controllers;

use App\Models\InventarioModel;
use App\Models\WinfenixModel;
use App\Models\AjaxModel;
use App\Models\ValesPerdidaModel;
use Mpdf\Mpdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class DocumentoController  {

    public $defaulDataBase;
    public $inventarioModel;
    public $winfenixModel;
    public $ajaxModel;
    public $valesModel;

    public function __construct() {
        $this->defaulDataBase = (!isset($_SESSION["empresaAUTH".APP_UNIQUE_KEY])) ? DEFAULT_DBName : $_SESSION["empresaAUTH".APP_UNIQUE_KEY] ;
        $this->inventarioModel = new InventarioModel();
        $this->inventarioModel->setDbname($this->defaulDataBase);
        $this->inventarioModel->conectarDB();
        $this->winfenixModel = new WinfenixModel();
        $this->winfenixModel->setDbname($this->defaulDataBase);
        $this->winfenixModel->conectarDB();
        $this->ajaxModel = new AjaxModel();
        $this->ajaxModel->setDbname($this->defaulDataBase);
        $this->ajaxModel->conectarDB();
        $this->valesModel = new ValesPerdidaModel();
        $this->valesModel->setDbname($this->defaulDataBase);
        $this->valesModel->conectarDB();
    }

    public function getPDF_Cotizacion($ID, $outputMode = 'I'){

        $empresaData = $this->winfenixModel->getDatosEmpresa();
        $VEN_CAB = $this->winfenixModel->SQL_getVENCAB($ID);
        $VEN_MOV = $this->winfenixModel->SQL_getVENMOV($ID);
        
        $html = '
        <div class="container-fluid">
            <div class="row">
                <div class="col text-center">
                    <img src="../../assets/img/logo.png" alt="Logo" style="width: 150px;">
                    <h5 style="font-weight: bold;">www.kaosport.com</strong></h5>
                    <h5>'.$empresaData["NomCia"].'</h5>
                    <h5>Direccion: '.$empresaData["DirCia"].'</h5>
                    <h5>Telefono: '.$empresaData["TelCia"].'</h5>
                    <h4 style="font-weight: bold;">PROFORMA</strong></h4>
                </div>
                
            </div>

            <div class="row" style="border:1px solid #000000; border-radius: 4px; padding: 5px; font-size: 12px;">
                <div class="col-xs-6 text-left">
                    <div><span style="font-weight: bold; text-align: right;">LOCAL:</span>('. $VEN_CAB["BODEGA"].')'.$VEN_CAB["BodegaName"].'</div>
                    <div><span style="font-weight: bold;">CLIENTE:</span> '.$VEN_CAB["NOMBRE"].'</div>
                    <div><span style="font-weight: bold;">RUC:</span> '.$VEN_CAB["RUC"].'</div>
                    <div><span style="font-weight: bold;">DIRECCION:</span> '.$VEN_CAB["DIRECCION1"].' </div>
                    <div><span style="font-weight: bold;">TELEFONO:</span> '.$VEN_CAB["TELEFONO1"].' </div>
                    
                </div>
                <div class="col-xs-4 text-left">
                    <div><span style="font-weight: bold;">SISTEMA # </span> '.$VEN_CAB["TIPO"].'-'.$VEN_CAB["NUMERO"].' </div>
                    <div><span style="font-weight: bold;">FECHA # </span> '.$VEN_CAB["CREADODATE"].' </div>
                    <div><span style="font-weight: bold;">VENDEDOR:</span>('.$VEN_CAB["CodigoVendedor"].')'. $VEN_CAB["VendedorName"].' </div>
                </div>
            </div>

            <div class="row" style="padding-top:10px;">
                <div class="col">
                    <table style="border-collapse: collapse;" class="table" cellpadding="8">
                        <thead>
                            <tr>
                                <td style="font-weight: bold;" class="text-center" width="20%">Codigo</td>
                                <td style="font-weight: bold;" class="text-center" width="55%">Descripcion</td>
                                <td style="font-weight: bold;" class="text-right" width="7%">Cant.</td>
                                <td style="font-weight: bold;" class="text-right" width="15%">Precio</td>
                                <td style="font-weight: bold;" class="text-right" width="10%">% Desc.</td>
                                <td style="font-weight: bold;" class="text-right" width="15%">P. Total</td>
                            </tr>
                        </thead>
                        <tbody>
            
                            <!-- ITEMS HERE -->
                            ';
                            $cont = 1;
                            foreach($VEN_MOV as $row){
                                $html .= '
                    
                                <tr>
                                    <td class="text-left">'.$row["CODIGO"].'</td>
                                    <td class="text-left">'.$row["Nombre"].'</td>
                                    <td class="text-right">'.$row["CANTIDAD"].'</td>
                                    <td class="text-right">'.round($row["PRECIO"],2).'</td>
                                    <td class="text-right">'.round($row["DESCU"],2).'</td>
                                    <td class="text-right"> '.round($row["PRECIOTOT"],2).'</td>
                                </tr>';
                                $cont++;
                                }
                
                                $html .= ' 
                               
                                    <tr>
                                        <td colspan="3" rowspan="5">
                                            <p><span style="font-weight: bold;">Observaciones:</span> '.$VEN_CAB["OBSERVA"].'</p> 
                                        </td>
                                        <td style="font-weight: bold;" class="text-right" colspan="2">Subtotal:</td>
                                        <td class="text-right">'.round($VEN_CAB["BASIVA"],2).'</td>
                                    
                                    </tr>
                            
                                    <tr>
                                        <td style="font-weight: bold;" class="text-right" colspan="2">Descuento:</td>
                                        <td class="text-right">'.round($VEN_CAB["DESCUENTO"],2).'</td>
                                    </tr>
                            
                                    <tr>
                                        <td style="font-weight: bold;" class="text-right" colspan="2">Base Imp:</td>
                                        <td class="text-right">'.round($VEN_CAB["SUBTOTAL"],2).'</td>
                                    </tr>
                            
                                    <tr>
                                        <td style="font-weight: bold;" class="text-right" colspan="2">IVA 15%:</td>
                                        <td class="text-right">'.round($VEN_CAB["IMPUESTO"],2).'</td>
                                    </tr>
                            
                                    <tr>
                                        <td style="font-weight: bold;" class="text-right" colspan="2"><b>Total a Pagar:</b></td>
                                        <td class="text-right"><b>'.round($VEN_CAB["TOTAL"],2).'</b></td>
                                    </tr>
                        
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="row" style="border:1px solid #000000; border-radius: 4px; padding: 5px; font-size: 12px;">
                <div class="col-xs-3 text-center">
                   <span style="font-weight: bold;">Aprobado por</span>
                </div>
                <div class="col-xs-3 text-center">
                    <span style="font-weight: bold;">Recibi conforme</span>
                </div>
                <div class="col-xs-4 text-center">
                    <div><span style="font-weight: bold;">FAVOR EMITIR EL CHEQUE A NOMBRE '.$empresaData["NomCia"].'</span> </div>
                    <div><span>Estos precios NO incluyen costo de flete, Cotización Válida por 8 dias.</span> </div>
                </div>
            </div> 

            <div class="row">
                <div class="col-xs-12 text-center">
                    <h5 style="font-weight: bold;">ES UN PLACER ATENDERLE</h5>
                </div>
            </div>
          
                ';
            
 
         //==============================================================
         //==============================================================
         //==============================================================
 
         $mpdf = new mPDF();
         $stylesheet = file_get_contents('../../assets/css/bootstrap.min.css');
         $stylesheet2 = file_get_contents('../../assets/css/reportesStyles.css');
         $mpdf->WriteHTML($stylesheet,1);	// The parameter 1 tells that this is css/style only and no body/html/text
         $mpdf->WriteHTML($stylesheet2,1);	
         $html = mb_convert_encoding($html, 'UTF-8', 'UTF-8');
         $mpdf->SetTitle("Cotizacion ".$ID);
         $mpdf->SetHTMLHeader('
         <div style=" text-align: right;">
             <h5 style="font-size: 10px;">Página: {PAGENO} de {nbpg}</h5>  
         </div> ');
         $mpdf->WriteHTML($html);
         if ($outputMode == 'F') {
             $ID = "../../assets/docs./$ID";
         }
         
         return $mpdf->Output($ID.'.pdf', $outputMode);
 
         //==============================================================
         //==============================================================
         //==============================================================
 
    }

    public function generaExcel_SRIDocs(array $arrayDocumentos){

        $spreadsheet = new Spreadsheet();
        
        $worksheet = $spreadsheet->getActiveSheet()->setTitle('Facturas');
        $spreadsheet->setActiveSheetIndex(0);
        $worksheet->setCellValue('A1', 'ID RECEPTOR')
            ->setCellValue('B1', 'TIPO ID RECEPTOR')
            ->setCellValue('C1', 'RAZÓN SOCIAL RECEPTOR')
            ->setCellValue('D1', 'ID EMISOR')
            ->setCellValue('E1', 'RAZÓN SOCIAL EMISIOR')
            ->setCellValue('F1', 'TIPO EMI.')
            ->setCellValue('G1', 'ESTB.')
            ->setCellValue('H1', 'PUNTO EMI')
            ->setCellValue('I1', 'SECUENCIAL')
            ->setCellValue('J1', 'FECHA EMISION')
            ->setCellValue('K1', 'FECHA DE AUTORIZACIÓN')
            ->setCellValue('L1', 'CLAVE DE ACCESO')
            ->setCellValue('M1', 'BASE NO OBJETO DE IVA')
            ->setCellValue('N1', 'BASE IVA 0%')
            ->setCellValue('O1', 'BASE IMP EXENTA DE IVA')
            ->setCellValue('P1', 'BASE 12')
            ->setCellValue('Q1', 'MONTO ICE')
            ->setCellValue('R1', 'TOTAL SIN IMPUESTO')
            ->setCellValue('S1', 'TOTAL DESCUENTO')
            ->setCellValue('T1', 'IMPORTE TOTAL');

            $spreadsheet->getActiveSheet()->getStyle('A')->getNumberFormat()
            ->setFormatCode('0');
            $spreadsheet->getActiveSheet()->getStyle('D')->getNumberFormat()
            ->setFormatCode('0');
            
            $spreadsheet->getActiveSheet()->setAutoFilter('A1:N1');

            /* Config Sheet */
            for ($i = 'A'; $i !=  $spreadsheet->getActiveSheet()->getHighestColumn(); $i++) {
                $spreadsheet->getActiveSheet()->getColumnDimension($i)->setAutoSize(TRUE);
            }

        $spreadsheet->createSheet()->setTitle('Retenciones');
        $spreadsheet->setActiveSheetIndex(1);
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet->setCellValue('A1', 'ID RECEPTOR')
            ->setCellValue('B1', 'TIPO ID RECEPTOR')
            ->setCellValue('C1', 'RAZÓN SOCIAL RECEPTOR')
            ->setCellValue('D1', 'ID EMISOR')
            ->setCellValue('E1', 'RAZÓN SOCIAL EMISIOR')
            ->setCellValue('F1', 'TIPO EMI')
            ->setCellValue('G1', 'ESTABLECIMIENTO RETENCIÓN')
            ->setCellValue('H1', 'PUNTO EMISIÓN RETENCIÓN')
            ->setCellValue('I1', 'SECUENCIAL RETENCIÓN')
            ->setCellValue('J1', 'FECHA DE EMISION')
            ->setCellValue('K1', 'FECHA DE AUTORIZACIÓN')
            ->setCellValue('L1', 'CLAVE DE ACCESO')
            ->setCellValue('M1', 'ESTB. DOC. SUSTENTO')
            ->setCellValue('N1', 'PUNTO. EMI. DOC. SUSTENTO')
            ->setCellValue('O1', 'SECUENCIAL DOC. SUSTENTO')
            ->setCellValue('P1', 'FECHA EMI DOC SUSTENTO')
            ->setCellValue('Q1', 'BASE RET. IVA 10%')
            ->setCellValue('R1', 'Retención IVA 10%')
            ->setCellValue('S1', 'BASE RET. IVA 20%')
            ->setCellValue('T1', 'Retención IVA 20%')
            ->setCellValue('U1', 'BASE RET. IVA 30%')
            ->setCellValue('V1', 'Retención IVA 30%')
            ->setCellValue('W1', 'BASE RET. IVA 50%')
            ->setCellValue('X1', 'Retención IVA 50%')
            ->setCellValue('Y1', 'BASE RET. IVA 70%')
            ->setCellValue('Z1', 'Retención IVA 70%')
            ->setCellValue('AA1', 'BASE RET. IVA 100%')
            ->setCellValue('AB1', 'Retención IVA 100%')
            ->setCellValue('AC1', 'Total Retenciones IVA')
            ->setCellValue('AD1', 'COD. RETENCIÓN RENTA')
            ->setCellValue('AE1', 'BASE IMPONIBLE RET.')
            ->setCellValue('AF1', '% RET.')
            ->setCellValue('AG1', 'VALOR DE RET.')
            ->setCellValue('AH1', 'COD. RETENCIÓN ISD.')
            ->setCellValue('AI1', 'BASE IMPONIBLE RET ISD.')
            ->setCellValue('AJ1', '% RET.')
            ->setCellValue('AK1', 'VALOR DE RET ISD.')
            ;

            $spreadsheet->getActiveSheet()->getStyle('A')->getNumberFormat()
            ->setFormatCode('0');
            $spreadsheet->getActiveSheet()->getStyle('D')->getNumberFormat()
            ->setFormatCode('0');
            $spreadsheet->getActiveSheet()->getStyle('O')->getNumberFormat()
            ->setFormatCode('0');

            $spreadsheet->setActiveSheetIndex(1);
            $spreadsheet->getActiveSheet(1)->setAutoFilter('A1:P1');

            for ($i = 'A'; $i !=  $spreadsheet->getActiveSheet(1)->getHighestColumn(); $i++) {
                $spreadsheet->getActiveSheet()->getColumnDimension($i)->setAutoSize(TRUE);
            }
        
        $spreadsheet->createSheet()->setTitle('Notas de Débito');
        $spreadsheet->setActiveSheetIndex(2);
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet->setCellValue('A1', 'ID RECEPTOR')
            ->setCellValue('B1', 'TIPO ID RECEPTOR')
            ->setCellValue('C1', 'RAZÓN SOCIAL RECEPTOR')
            ->setCellValue('D1', 'ID EMISOR')
            ->setCellValue('E1', 'RAZÓN SOCIAL EMISIOR')
            ->setCellValue('F1', 'TIPO EMI.')
            ->setCellValue('G1', 'ESTB.')
            ->setCellValue('H1', 'PUNTO EMI')
            ->setCellValue('I1', 'SECUENCIAL')
            ->setCellValue('J1', 'FECHA EMISION')
            ->setCellValue('K1', 'FECHA DE AUTORIZACIÓN')
            ->setCellValue('L1', 'CLAVE DE ACCESO')
            ->setCellValue('M1', 'NUM DOC MODIFICADO')
            ->setCellValue('N1', 'TOTAL SIN IMPUESTO')
            ->setCellValue('O1', 'Fecha Emision DocSustento')
            ->setCellValue('P1', 'IMPORTE TOTAL');

            $spreadsheet->getActiveSheet(2)->getStyle('A')->getNumberFormat()
            ->setFormatCode('0');
            $spreadsheet->getActiveSheet(2)->getStyle('D')->getNumberFormat()
            ->setFormatCode('0');
            $spreadsheet->getActiveSheet(2)->getStyle('O')->getNumberFormat()
            ->setFormatCode('0');

            $spreadsheet->setActiveSheetIndex(2);
            $spreadsheet->getActiveSheet()->setAutoFilter('A1:P1');

            for ($i = 'A'; $i !=  $spreadsheet->getActiveSheet(2)->getHighestColumn(); $i++) {
                $spreadsheet->getActiveSheet()->getColumnDimension($i)->setAutoSize(TRUE);
            }

        $row_facturas = 2;
        $row_retenciones = 2;
        $row_ndebito = 2;
        foreach($arrayDocumentos as $documento){
            
            switch ($documento->comprobante_xml->infoTributaria->codDoc) {
                case '01': // Factura
                    $worksheet =  $spreadsheet->setActiveSheetIndex(0);
                    $worksheet->setCellValue('A'.$row_facturas, $documento->comprobante_xml->infoFactura->identificacionComprador)
                        ->setCellValue('B'.$row_facturas,  '04')
                        ->setCellValue('C'.$row_facturas, $documento->comprobante_xml->infoFactura->razonSocialComprador)
                        ->setCellValue('D'.$row_facturas, $documento->comprobante_xml->infoTributaria->ruc)
                        ->setCellValue('E'.$row_facturas, $documento->comprobante_xml->infoTributaria->razonSocial)
                        ->setCellValue('F'.$row_facturas, $documento->comprobante_xml->infoTributaria->tipoEmision)
                        ->setCellValue('G'.$row_facturas, $documento->comprobante_xml->infoTributaria->estab)
                        ->setCellValue('H'.$row_facturas, $documento->comprobante_xml->infoTributaria->ptoEmi)
                        ->setCellValue('I'.$row_facturas, $documento->comprobante_xml->infoTributaria->secuencial)
                        ->setCellValue('J'.$row_facturas, $documento->comprobante_xml->infoFactura->fechaEmision)
                        ->setCellValue('K'.$row_facturas, $documento->sri_data->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->fechaAutorizacion)
                        ->setCellValue('L'.$row_facturas, $documento->sri_data->RespuestaAutorizacionComprobante->claveAccesoConsultada)
                        ->setCellValue('M'.$row_facturas, '') //BASE NO OBJETO DE IVA
                        ->setCellValue('N'.$row_facturas, '') // BASE IVA 0%
                        ->setCellValue('O'.$row_facturas, '') //BASE IMP EXENTA DE IVA
                        ->setCellValue('P'.$row_facturas, '') //BASE 12
                        ->setCellValue('Q'.$row_facturas, '') //MONTO ICE
                        ->setCellValue('R'.$row_facturas, $documento->comprobante_xml->infoFactura->totalSinImpuestos)
                        ->setCellValue('S'.$row_facturas, $documento->comprobante_xml->infoFactura->totalDescuento)
                        ->setCellValue('T'.$row_facturas, $documento->comprobante_xml->infoFactura->importeTotal);
                       
                    
                    $row_facturas++;
                break;

                case '07': // Retenciones  
                    $worksheet =  $spreadsheet->setActiveSheetIndex(1);
                    $worksheet->setCellValue('A'.$row_retenciones, $documento->comprobante_xml->infoCompRetencion->identificacionSujetoRetenido)
                        ->setCellValue('B'.$row_retenciones, $documento->comprobante_xml->infoCompRetencion->tipoIdentificacionSujetoRetenido)
                        ->setCellValue('C'.$row_retenciones, $documento->comprobante_xml->infoCompRetencion->razonSocialSujetoRetenido)
                        ->setCellValue('D'.$row_retenciones, $documento->comprobante_xml->infoTributaria->ruc)
                        ->setCellValue('E'.$row_retenciones, $documento->comprobante_xml->infoTributaria->razonSocial)
                        ->setCellValue('F'.$row_retenciones, $documento->comprobante_xml->infoTributaria->tipoEmision)
                        ->setCellValue('G'.$row_retenciones, $documento->comprobante_xml->infoTributaria->estab)
                        ->setCellValue('H'.$row_retenciones, $documento->comprobante_xml->infoTributaria->ptoEmi)
                        ->setCellValue('I'.$row_retenciones, $documento->comprobante_xml->infoTributaria->secuencial)
                        ->setCellValue('J'.$row_retenciones, $documento->comprobante_xml->infoCompRetencion->fechaEmision)
                        ->setCellValue('K'.$row_retenciones, $documento->sri_data->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->fechaAutorizacion)
                        ->setCellValue('L'.$row_retenciones, $documento->sri_data->RespuestaAutorizacionComprobante->claveAccesoConsultada)
                        ->setCellValue('M'.$row_retenciones, $documento->comprobante_xml->infoTributaria->estab)
                        ->setCellValue('N'.$row_retenciones, $documento->comprobante_xml->infoTributaria->ptoEmi);
                        
                        // Para docs Version 1.0 
                        if (isset($documento->comprobante_xml->impuestos)) {

                            if (\is_array($documento->comprobante_xml->impuestos->impuesto)) {
                                foreach ($documento->comprobante_xml->impuestos->impuesto as $impuesto) {
                                    $worksheet->setCellValue('O'.$row_retenciones, isset($impuesto->numDocSustento) ? $impuesto->numDocSustento : '') //Documento Sustento
                                    ->setCellValue('P'.$row_retenciones, isset($impuesto->fechaEmisionDocSustento) ? $impuesto->fechaEmisionDocSustento : ''); 
                                    
                                    switch ($impuesto->codigo) {
                                        case '1': // Retencion en la fuente
                                            $worksheet->setCellValue('AD'.$row_retenciones, $impuesto->codigoRetencion) //BASE Retención RENTA
                                                    ->setCellValue('AE'.$row_retenciones, $impuesto->baseImponible)
                                                    ->setCellValue('AF'.$row_retenciones, $impuesto->porcentajeRetener)
                                                    ->setCellValue('AG'.$row_retenciones, $impuesto->valorRetenido); //Retención RENTA 
                                        break;
        
                                        case '2': // Retencion IVA
                                            switch ($impuesto->porcentajeRetener) {
                                                case '10':
                                                    $worksheet->setCellValue('Q'.$row_retenciones, $impuesto->baseImponible) //BASE Retención IVA 10%
                                                            ->setCellValue('R'.$row_retenciones, $impuesto->valorRetenido); //Retención IVA 10%  
                                                break;
        
                                                case '20':
                                                    $worksheet->setCellValue('S'.$row_retenciones, $impuesto->baseImponible) //BASE Retención IVA 10%
                                                            ->setCellValue('T'.$row_retenciones, $impuesto->valorRetenido); //Retención IVA 10%  
                                                break;
        
                                                case '30':
                                                    $worksheet->setCellValue('U'.$row_retenciones, $impuesto->baseImponible) //BASE Retención IVA 10%
                                                            ->setCellValue('V'.$row_retenciones, $impuesto->valorRetenido); //Retención IVA 10%  
                                                break;
        
                                                case '50':
                                                    $worksheet->setCellValue('W'.$row_retenciones, $impuesto->baseImponible) //BASE Retención IVA 10%
                                                            ->setCellValue('X'.$row_retenciones, $impuesto->valorRetenido); //Retención IVA 10%  
                                                break;
        
                                                case '70':
                                                    $worksheet->setCellValue('Y'.$row_retenciones, $impuesto->baseImponible) //BASE Retención IVA 10%
                                                            ->setCellValue('Z'.$row_retenciones, $impuesto->valorRetenido); //Retención IVA 10%  
                                                break;
                                                
                                                case '100':
                                                    $worksheet->setCellValue('AA'.$row_retenciones, $impuesto->baseImponible) //BASE Retención IVA 10%
                                                            ->setCellValue('AB'.$row_retenciones, $impuesto->valorRetenido); //Retención IVA 10%  
                                                break;
                                                
                                                
                                            }
 
                                        break;
                                        
                                        case '6': // Retencion en la fuente
                                            $worksheet->setCellValue('AH'.$row_retenciones, $impuesto->codigoRetencion) //BASE Retención RENTA
                                                    ->setCellValue('AI'.$row_retenciones, $impuesto->baseImponible)
                                                    ->setCellValue('AJ'.$row_retenciones, $impuesto->porcentajeRetener)
                                                    ->setCellValue('AK'.$row_retenciones, $impuesto->valorRetenido); //Retención RENTA 
                                        break;

                                        default:
                                        break;
                                    }

                                    $worksheet->setCellValue('AC'.$row_retenciones, '=SUM(R'.$row_retenciones.'+T'.$row_retenciones.'+V'.$row_retenciones.'+X'.$row_retenciones.'+Z'.$row_retenciones.'+AB'.$row_retenciones.')');
                                   
                                }
                               
                            }


                            if (\is_object($documento->comprobante_xml->impuestos->impuesto)){
                                $impuesto = $documento->comprobante_xml->impuestos->impuesto;
                                
                                $worksheet->setCellValue('O'.$row_retenciones, isset($impuesto->numDocSustento) ? $impuesto->numDocSustento : '') //Documento Sustento
                                ->setCellValue('P'.$row_retenciones, isset($impuesto->fechaEmisionDocSustento) ? $impuesto->fechaEmisionDocSustento : ''); 
                                
                                switch ($impuesto->codigo) {
                                    case '1': // Retencion en la fuente
                                        $worksheet->setCellValue('AD'.$row_retenciones, $impuesto->codigoRetencion) //BASE Retención RENTA
                                                ->setCellValue('AE'.$row_retenciones, $impuesto->baseImponible)
                                                ->setCellValue('AF'.$row_retenciones, $impuesto->porcentajeRetener)
                                                ->setCellValue('AG'.$row_retenciones, $impuesto->valorRetenido); //Retención RENTA 
                                      
                                    break;
    
                                    case '2': // Retencion IVA
                                        switch ($impuesto->porcentajeRetener) {
                                            case '10':
                                                $worksheet->setCellValue('Q'.$row_retenciones, $impuesto->baseImponible) //BASE Retención IVA 10%
                                                        ->setCellValue('R'.$row_retenciones, $impuesto->valorRetenido); //Retención IVA 10%  
                                            break;
    
                                            case '20':
                                                $worksheet->setCellValue('S'.$row_retenciones, $impuesto->baseImponible) //BASE Retención IVA 10%
                                                        ->setCellValue('T'.$row_retenciones, $impuesto->valorRetenido); //Retención IVA 10%  
                                            break;
    
                                            case '30':
                                                $worksheet->setCellValue('U'.$row_retenciones, $impuesto->baseImponible) //BASE Retención IVA 10%
                                                        ->setCellValue('V'.$row_retenciones, $impuesto->valorRetenido); //Retención IVA 10%  
                                            break;
    
                                            case '50':
                                                $worksheet->setCellValue('W'.$row_retenciones, $impuesto->baseImponible) //BASE Retención IVA 10%
                                                        ->setCellValue('X'.$row_retenciones, $impuesto->valorRetenido); //Retención IVA 10%  
                                            break;
    
                                            case '70':
                                                $worksheet->setCellValue('Y'.$row_retenciones, $impuesto->baseImponible) //BASE Retención IVA 10%
                                                        ->setCellValue('Z'.$row_retenciones, $impuesto->valorRetenido); //Retención IVA 10%  
                                            break;
                                            
                                            case '100':
                                                $worksheet->setCellValue('AA'.$row_retenciones, $impuesto->baseImponible) //BASE Retención IVA 10%
                                                        ->setCellValue('AB'.$row_retenciones, $impuesto->valorRetenido); //Retención IVA 10%  
                                            break;
                                            
                                            
                                        }
                                    break;

                                    case '6': // ISD
                                        $worksheet->setCellValue('AH'.$row_retenciones, $impuesto->codigoRetencion) //BASE Retención RENTA
                                                ->setCellValue('AI'.$row_retenciones, $impuesto->baseImponible)
                                                ->setCellValue('AJ'.$row_retenciones, $impuesto->porcentajeRetener)
                                                ->setCellValue('AK'.$row_retenciones, $impuesto->valorRetenido); //Retención RENTA 
                                    break;
                                    
                                    default:
                                    break;
                                }

                                $worksheet->setCellValue('AC'.$row_retenciones, '=SUM(R'.$row_retenciones.'+T'.$row_retenciones.'+V'.$row_retenciones.'+X'.$row_retenciones.'+Z'.$row_retenciones.'+AB'.$row_retenciones.')');
                                   
                            }
                        }

                        // Para docs Version 2.0 ATS
                        if (isset($documento->comprobante_xml->docsSustento)) {

                            if (\is_object($documento->comprobante_xml->docsSustento->docSustento)){
                                $docSustento = $documento->comprobante_xml->docsSustento->docSustento;
                                
                                $worksheet->setCellValue('O'.$row_retenciones, isset($docSustento->numDocSustento) ? $docSustento->numDocSustento : '') //Documento Sustento
                                ->setCellValue('P'.$row_retenciones, isset($docSustento->fechaEmisionDocSustento) ? $docSustento->fechaEmisionDocSustento : ''); 
                                
                                $retenciones = $documento->comprobante_xml->docsSustento->docSustento->retenciones->retencion;

                                if (\is_array($retenciones)) {
                                    foreach($retenciones as $retencion){
                                        switch ($retencion->codigo) {
                                            case '1': // Retencion en la fuente
                                                $worksheet->setCellValue('AD'.$row_retenciones, $retencion->codigoRetencion) //BASE Retención RENTA
                                                        ->setCellValue('AE'.$row_retenciones, $retencion->baseImponible)
                                                        ->setCellValue('AF'.$row_retenciones, $retencion->porcentajeRetener)
                                                        ->setCellValue('AG'.$row_retenciones, $retencion->valorRetenido); //Retención RENTA 
                                            
                                            break;
            
                                            case '2': // Retencion IVA
                                                switch ($retencion->porcentajeRetener) {
                                                    case '10':
                                                        $worksheet->setCellValue('Q'.$row_retenciones, $retencion->baseImponible) //BASE Retención IVA 10%
                                                                ->setCellValue('R'.$row_retenciones, $retencion->valorRetenido); //Retención IVA 10%  
                                                    break;
            
                                                    case '20':
                                                        $worksheet->setCellValue('S'.$row_retenciones, $retencion->baseImponible) //BASE Retención IVA 10%
                                                                ->setCellValue('T'.$row_retenciones, $retencion->valorRetenido); //Retención IVA 10%  
                                                    break;
            
                                                    case '30':
                                                        $worksheet->setCellValue('U'.$row_retenciones, $retencion->baseImponible) //BASE Retención IVA 10%
                                                                ->setCellValue('V'.$row_retenciones, $retencion->valorRetenido); //Retención IVA 10%  
                                                    break;
            
                                                    case '50':
                                                        $worksheet->setCellValue('W'.$row_retenciones, $retencion->baseImponible) //BASE Retención IVA 10%
                                                                ->setCellValue('X'.$row_retenciones, $retencion->valorRetenido); //Retención IVA 10%  
                                                    break;
            
                                                    case '70':
                                                        $worksheet->setCellValue('Y'.$row_retenciones, $retencion->baseImponible) //BASE Retención IVA 10%
                                                                ->setCellValue('Z'.$row_retenciones, $retencion->valorRetenido); //Retención IVA 10%  
                                                    break;
                                                    
                                                    case '100':
                                                        $worksheet->setCellValue('AA'.$row_retenciones, $retencion->baseImponible) //BASE Retención IVA 10%
                                                                ->setCellValue('AB'.$row_retenciones, $retencion->valorRetenido); //Retención IVA 10%  
                                                    break;
                                                    
                                                    
                                                }
                                            break;
    
                                            case '6': // ISD
                                                $worksheet->setCellValue('AH'.$row_retenciones, $retencion->codigoRetencion) //BASE Retención RENTA
                                                        ->setCellValue('AI'.$row_retenciones, $retencion->baseImponible)
                                                        ->setCellValue('AJ'.$row_retenciones, $retencion->porcentajeRetener)
                                                        ->setCellValue('AK'.$row_retenciones, $retencion->valorRetenido); //Retención RENTA 
                                            break;
                                            
                                            default:
                                            break;
                                        }
                                    }
                                }   

                                if (\is_object($retenciones)) {
                                        $retencion = $retenciones;
                                        switch ($retencion->codigo) {
                                            case '1': // Retencion en la fuente
                                                $worksheet->setCellValue('AD'.$row_retenciones, $retencion->codigoRetencion) //BASE Retención RENTA
                                                        ->setCellValue('AE'.$row_retenciones, $retencion->baseImponible)
                                                        ->setCellValue('AF'.$row_retenciones, $retencion->porcentajeRetener)
                                                        ->setCellValue('AG'.$row_retenciones, $retencion->valorRetenido); //Retención RENTA 
                                            
                                            break;
            
                                            case '2': // Retencion IVA
                                                switch ($retencion->porcentajeRetener) {
                                                    case '10':
                                                        $worksheet->setCellValue('Q'.$row_retenciones, $retencion->baseImponible) //BASE Retención IVA 10%
                                                                ->setCellValue('R'.$row_retenciones, $retencion->valorRetenido); //Retención IVA 10%  
                                                    break;
            
                                                    case '20':
                                                        $worksheet->setCellValue('S'.$row_retenciones, $retencion->baseImponible) //BASE Retención IVA 10%
                                                                ->setCellValue('T'.$row_retenciones, $retencion->valorRetenido); //Retención IVA 10%  
                                                    break;
            
                                                    case '30':
                                                        $worksheet->setCellValue('U'.$row_retenciones, $retencion->baseImponible) //BASE Retención IVA 10%
                                                                ->setCellValue('V'.$row_retenciones, $retencion->valorRetenido); //Retención IVA 10%  
                                                    break;
            
                                                    case '50':
                                                        $worksheet->setCellValue('W'.$row_retenciones, $retencion->baseImponible) //BASE Retención IVA 10%
                                                                ->setCellValue('X'.$row_retenciones, $retencion->valorRetenido); //Retención IVA 10%  
                                                    break;
            
                                                    case '70':
                                                        $worksheet->setCellValue('Y'.$row_retenciones, $retencion->baseImponible) //BASE Retención IVA 10%
                                                                ->setCellValue('Z'.$row_retenciones, $retencion->valorRetenido); //Retención IVA 10%  
                                                    break;
                                                    
                                                    case '100':
                                                        $worksheet->setCellValue('AA'.$row_retenciones, $retencion->baseImponible) //BASE Retención IVA 10%
                                                                ->setCellValue('AB'.$row_retenciones, $retencion->valorRetenido); //Retención IVA 10%  
                                                    break;
                                                    
                                                    
                                                }
                                            break;
    
                                            case '6': // ISD
                                                $worksheet->setCellValue('AH'.$row_retenciones, $retencion->codigoRetencion) //BASE Retención RENTA
                                                        ->setCellValue('AI'.$row_retenciones, $retencion->baseImponible)
                                                        ->setCellValue('AJ'.$row_retenciones, $retencion->porcentajeRetener)
                                                        ->setCellValue('AK'.$row_retenciones, $retencion->valorRetenido); //Retención RENTA 
                                            break;
                                            
                                            default:
                                            break;
                                        }
                                    
                                }  

                                
                                   

                                   
                            }
                        }

                        $worksheet->setCellValue('AC'.$row_retenciones, '=SUM(R'.$row_retenciones.'+T'.$row_retenciones.'+V'.$row_retenciones.'+X'.$row_retenciones.'+Z'.$row_retenciones.'+AB'.$row_retenciones.')');
                                
                    
                    $row_retenciones++;
                    
            
                break;

                case '05': // Notas de débito
                    $worksheet =  $spreadsheet->setActiveSheetIndex(2);
                    $worksheet->setCellValue('A'.$row_ndebito, $documento->comprobante_xml->infoNotaDebito->identificacionComprador)
                        ->setCellValue('B'.$row_ndebito,  '04')
                        ->setCellValue('C'.$row_ndebito, $documento->comprobante_xml->infoNotaDebito->razonSocialComprador)
                        ->setCellValue('D'.$row_ndebito, $documento->comprobante_xml->infoTributaria->ruc)
                        ->setCellValue('E'.$row_ndebito, $documento->comprobante_xml->infoTributaria->razonSocial)
                        ->setCellValue('F'.$row_ndebito, $documento->comprobante_xml->infoTributaria->tipoEmision)
                        ->setCellValue('G'.$row_ndebito, $documento->comprobante_xml->infoTributaria->estab)
                        ->setCellValue('H'.$row_ndebito, $documento->comprobante_xml->infoTributaria->ptoEmi)
                        ->setCellValue('I'.$row_ndebito, $documento->comprobante_xml->infoTributaria->secuencial)
                        ->setCellValue('J'.$row_ndebito, $documento->comprobante_xml->infoNotaDebito->fechaEmision)
                        ->setCellValue('K'.$row_ndebito, $documento->sri_data->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->fechaAutorizacion)
                        ->setCellValue('L'.$row_ndebito, $documento->sri_data->RespuestaAutorizacionComprobante->claveAccesoConsultada)
                        ->setCellValue('M'.$row_ndebito, $documento->comprobante_xml->infoNotaDebito->numDocModificado)
                        ->setCellValue('N'.$row_ndebito, $documento->comprobante_xml->infoNotaDebito->totalSinImpuestos)
                        ->setCellValue('O'.$row_ndebito, $documento->comprobante_xml->infoNotaDebito->fechaEmisionDocSustento)
                        ->setCellValue('P'.$row_ndebito, $documento->comprobante_xml->infoNotaDebito->valorTotal);
                       
                    
                    $row_ndebito++;
                break;
                   
                    
                default:
                break;
            }
        }
        
        return $spreadsheet;
 
    }

    public function generaRIDE_SriDocs(object $documento, $outputMode = 'I'){

        switch ($documento->comprobante_xml->infoTributaria->codDoc) {
            case '01':
                $html = '
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-xs-5" style="border:0px solid red; padding:0px; padding-right:2px">
                                <div class="text-center" style="height:70px; margin-top:10px">
                                    <span style="font-weight: bold; color:red; font-size:20px;">NO TIENE LOGO</span> 
                                </div>
                                <div style="border:1px solid #000000; border-radius: 4px; padding:10px;">
                                    <p><span style="font-weight: bold;"></span>'.$documento->comprobante_xml->infoTributaria->razonSocial.'</p>
                                    <p><span style="font-weight: bold;"></span>'.$documento->comprobante_xml->infoTributaria->nombreComercial.'</p>
                                    <p><span style="font-weight: bold;">Dirección Matriz: </span>'.$documento->comprobante_xml->infoTributaria->dirMatriz.'</p>
                                    <p><span style="font-weight: bold;">Dirección Sucursal: </span>'.$documento->comprobante_xml->infoFactura->dirEstablecimiento.'</p>
                                    <p><span style="font-weight: bold;">Contribuyente Especial: </span>'.$documento->comprobante_xml->infoFactura->contribuyenteEspecial.'</p>
                                    <p><span style="font-weight: bold;">OBLIGADO A LLEVAR CONTABILIDAD: </span>'.$documento->comprobante_xml->infoFactura->obligadoContabilidad.'</p>
                                </div>
                            </div>
                            <div class="col-xs" style="border:0px solid red; padding:0px">
                                <div style="border:1px solid #000000; border-radius: 4px; padding: 10px;">
                                    <p><span style="font-weight: bold;">RUC: </span>'.$documento->comprobante_xml->infoTributaria->ruc.'</p>
                                    <p style="font-weight: bold; font-size:20px;">'. $this->getTipoDOC_SRI($documento->comprobante_xml->infoTributaria->codDoc).'</p>
                                    <p><span style="font-weight: bold;">No° </span>'
                                        .$documento->comprobante_xml->infoTributaria->estab.'-'
                                        .$documento->comprobante_xml->infoTributaria->ptoEmi.'-'
                                        .$documento->comprobante_xml->infoTributaria->secuencial.
                                    '</p>
                                    <p><span style="font-weight: bold;">NÚMERO DE AUTORIZACIÓN: </span>'.$documento->sri_data->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->numeroAutorizacion.'</p>
                                    <p><span style="font-weight: bold;">FECHA Y HORA DE AUTORIZACIÓN: </span>'.date('d/m/Y h:i:s', strtotime($documento->sri_data->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->fechaAutorizacion)).'</p>
                                    <p><span style="font-weight: bold;">AMBIENTE: </span>'.$documento->sri_data->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->ambiente.'</p>
                                    <p><span style="font-weight: bold;">EMISIÓN: </span>'.$documento->sri_data->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->estado.'</p>
                                    <p><span style="font-weight: bold;">CLAVE DE ACCESO </span></p>
                                    <p>
                                        <barcode size="0.65" height="1.5" code="'.$documento->comprobante_xml->infoTributaria->claveAcceso.'" type="I25" />
                                        '.$documento->comprobante_xml->infoTributaria->claveAcceso.'
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-12" style="border:0px solid red; padding:0px; margin-top:3px">
                                <div style="border:1px solid #000000; border-radius: 4px; padding:10px;">
                                    <p><span style="font-weight: bold;">Razón Social / Nombres y Apellidos: </span>'.$documento->comprobante_xml->infoFactura->razonSocialComprador.'</p>
                                    <p>
                                        <span style="font-weight: bold;">Identificación:</span>'.$documento->comprobante_xml->infoFactura->identificacionComprador.'
                                        <span style="font-weight: bold;">Fecha Emision: </span>'.$documento->comprobante_xml->infoFactura->fechaEmision.'
                                        <span style="font-weight: bold;">Guia Remision: </span>
                                    </p>
                                    <p><span style="font-weight: bold;">Direccion: </span>'.$documento->comprobante_xml->infoAdicional->campoAdicional[1].'</p>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                
                ';
                break;
            
            default:
                $html = '';
                break;
        }

        
            
 
         //==============================================================
         //==============================================================
         //==============================================================
 
        $mpdf = new mPDF(['mode' => 'utf-8','format' => 'A4','margin_left' => 2,'margin_right' => 2,'margin_top' => 9,'margin_bottom' => 3,'margin_header' => 3,'margin_footer' => 0]);
        $stylesheet = file_get_contents('../../assets/css/bootstrap.min.css');
        $stylesheet2 = file_get_contents('../../assets/css/reportesStyles.css');
        $mpdf->WriteHTML($stylesheet,1);	// The parameter 1 tells that this is css/style only and no body/html/text
        $mpdf->WriteHTML($stylesheet2,1);	
        $html = mb_convert_encoding($html, 'UTF-8', 'UTF-8');
        $mpdf->SetTitle("RIDE-".$documento->comprobante_xml->infoTributaria->claveAcceso);
        $mpdf->SetHTMLHeader('
        <div style=" text-align: right;">
            <h5 style="font-size: 10px;">Página: {PAGENO} de {nbpg}</h5>  
        </div> ');
        $mpdf->WriteHTML($html);
        if ($outputMode == 'F') {
            $ID = "../../assets/docs./$documento->comprobante_xml->infoTributaria->claveAcceso";
        }
        
        return $mpdf->Output('RIDE'.'.pdf', $outputMode);

        //==============================================================
        //==============================================================
        //==============================================================
 
    }

    public function getTipoDOC_SRI(string $codigoDoc){
        $tipoDocumento = '';
        switch ($codigoDoc) {
            case '01':
                $tipoDocumento = 'FACTURA';
                break;
            case '03':
                $tipoDocumento = 'LIQUIDACIÓN DE COMPRA DE
                BIENES Y PRESTACIÓN DE
                SERVICIOS';
                break;
            case '04':
                $tipoDocumento = 'NOTA DE CRÉDITO ';
                break;
            case '05':
                $tipoDocumento = 'NOTA DE DÉBITO';
                break;
            case '06':
                $tipoDocumento = 'GUÍA DE REMISIÓN';
                break;
            case '07':
                $tipoDocumento = 'COMPROBANTE DE RETENCIÓN';
                break;
        }
        return $tipoDocumento;
    }

    public function generaPDF_ValePerdida($ID, $outputMode = 'I'){

        $empresaData = $this->winfenixModel->getDatosEmpresa();
        $VEN_CAB = $this->winfenixModel->SQL_getVENCAB($ID);
        $VEN_MOV = $this->winfenixModel->SQL_getVENMOV($ID);
        $vales_CAB = (object) $this->valesModel->SQL_getValesPerdida_CAB($ID);
        $personalReportado = $this->valesModel->SQL_getPersonalReportadoVales($ID);
        
        switch ($vales_CAB->estado) 
        {
        case 1:
            $estado_txt = 'Aprobado';
                break;
            
        case 2:
            $estado_txt = 'Anulado';
                break;    
    
        default:
            $estado_txt = 'Pendiente';
            break;
        }

        $html = '
        <div class="container-fluid">
            <div class="row">
                <div class="col text-center">
                    <img src="../../assets/img/logo.png" alt="Logo" style="width: 150px;">
                    <h5 style="font-weight: bold;">www.kaosport.com</strong></h5>
                    <h5>'.$empresaData["NomCia"].'</h5>
                    <h5>Direccion: '.$empresaData["DirCia"].'</h5>
                    <h5>Telefono: '.$empresaData["TelCia"].'</h5>
                    <h4 style="font-weight: bold;">VALE POR PERDIDA | MERCADERIA</strong></h4>
                </div>
                
            </div>

            <div class="row" style="border:1px solid #000000; border-radius: 4px; padding: 5px; font-size: 12px;">
                <div class="col-xs-6 text-left">
                    <div><span style="font-weight: bold; text-align: right;">LOCAL:</span>('. $VEN_CAB["BODEGA"].')'.$VEN_CAB["BodegaName"].'</div>
                    <div><span style="font-weight: bold;">CLIENTE:</span> '.$VEN_CAB["NOMBRE"].'</div>
                    <div><span style="font-weight: bold;">RUC:</span> '.$VEN_CAB["RUC"].'</div>
                    <div><span style="font-weight: bold;">DIRECCION:</span> '.$VEN_CAB["DIRECCION1"].' </div>
                    <div><span style="font-weight: bold;">TELEFONO:</span> '.$VEN_CAB["TELEFONO1"].' </div>
                    
                </div>
                <div class="col-xs-4 text-left">
                    <div><span style="font-weight: bold;">SISTEMA # </span> '.$VEN_CAB["ID"].' </div>
                    <div><span style="font-weight: bold;">FECHA # </span> '.$VEN_CAB["CREADODATE"].' </div>
                    <div><span style="font-weight: bold;">VENDEDOR:</span>('.$VEN_CAB["CodigoVendedor"].')'. $VEN_CAB["VendedorName"].' </div>
                </div>
            </div>

            <div class="row" style="padding-top:10px;">
                <div class="col">
                    <table style="border-collapse: collapse;" class="table" cellpadding="8">
                        <thead>
                            <tr>
                                <td style="font-weight: bold;" class="text-center" width="20%">Codigo</td>
                                <td style="font-weight: bold;" class="text-center" width="55%">Descripcion</td>
                                <td style="font-weight: bold;" class="text-right" width="7%">Cant.</td>
                                <td style="font-weight: bold;" class="text-right" width="15%">Precio</td>
                                <td style="font-weight: bold;" class="text-right" width="10%">% Desc.</td>
                                <td style="font-weight: bold;" class="text-right" width="15%">P. Total</td>
                            </tr>
                        </thead>
                        <tbody>
            
                            <!-- ITEMS HERE -->
                            ';
                            $cont = 1;
                            foreach($VEN_MOV as $row){
                                $html .= '
                    
                                <tr>
                                    <td class="text-left">'.$row["CODIGO"].'</td>
                                    <td class="text-left">'.$row["Nombre"].'</td>
                                    <td class="text-right">'.$row["CANTIDAD"].'</td>
                                    <td class="text-right">'.round($row["PRECIO"],2).'</td>
                                    <td class="text-right">'.round($row["DESCU"],2).'</td>
                                    <td class="text-right"> '.round($row["PRECIOTOT"],2).'</td>
                                </tr>';
                                $cont++;
                                }
                
                                $html .= ' 
                               
                                    <tr>
                                        <td colspan="3" rowspan="5">
                                            <p><span style="font-weight: bold;">Observaciones:</span> '.$VEN_CAB["OBSERVA"].'</p> 
                                        </td>
                                        <td style="font-weight: bold;" class="text-right" colspan="2">Subtotal:</td>
                                        <td class="text-right">'.round($VEN_CAB["BASIVA"],2).'</td>
                                    
                                    </tr>
                            
                                    <tr>
                                        <td style="font-weight: bold;" class="text-right" colspan="2">Descuento:</td>
                                        <td class="text-right">'.round($VEN_CAB["DESCUENTO"],2).'</td>
                                    </tr>
                            
                                    <tr>
                                        <td style="font-weight: bold;" class="text-right" colspan="2">Base Imp:</td>
                                        <td class="text-right">'.round($VEN_CAB["SUBTOTAL"],2).'</td>
                                    </tr>
                            
                                    <tr>
                                        <td style="font-weight: bold;" class="text-right" colspan="2">IVA 15%:</td>
                                        <td class="text-right">'.round($VEN_CAB["IMPUESTO"],2).'</td>
                                    </tr>
                            
                                    <tr>
                                        <td style="font-weight: bold;" class="text-right" colspan="2"><b>Total a Pagar:</b></td>
                                        <td class="text-right"><b>'.round($VEN_CAB["TOTAL"],2).'</b></td>
                                    </tr>
                        
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="row" style="padding-top:10px;">
            <div class="col">
                <table style="border-collapse: collapse;" class="table">
                    <thead>
                        <tr>
                            <td style="font-weight: bold;" class="text-center" width="20%">Cédula</td>
                            <td style="font-weight: bold;" class="text-center" width="40%">Empleado</td>
                            <td style="font-weight: bold;" class="text-right" width="7%"> %.</td>
                            <td style="font-weight: bold;" class="text-right" width="10%"> Valor</td>
                            <td style="font-weight: bold;" class="text-right" width="15%"> Cuota x mes</td>
                            <td style="font-weight: bold;" class="text-right" width="15%"> Firma</td>
                      
                        </tr>
                    </thead>
                    <tbody>
        
                        <!-- ITEMS HERE -->
                        ';
                        $cont = 1;
                        foreach($personalReportado as $row){
                            $valor_emp = $row["RECARGO"];
                            $cuota_emp = 0;
                            $cant_coutas = 1;
                            if ($valor_emp >= 10){
                              $cuota_emp = round($valor_emp/3, 2);
                              $cant_coutas = 3;
                            }else{
                              $cuota_emp =  $valor_emp;
                              $cant_coutas = 1;
                            }


                            $html .= '
                
                            <tr>
                                <td class="text-left">'.$row["RUC"].'</td>
                                <td class="text-left">'.$row["NOMBRE"].'</td>
                                <td class="text-right">'.round($row["PORCENTAJE"],2).'%</td>
                                <td class="text-right">'.round($row["RECARGO"],2).'</td>
                                <td class="text-right">'.$cuota_emp.'</td>
                                <td class="text-right">__________________</td>
                            </tr>';
                            $cont++;
                            }
            
                            $html .= ' 
                           
                    </tbody>
                </table>
            </div>
        </div>

            <div class="row" style="border:1px solid #000000; border-radius: 4px; padding: 5px; font-size: 12px;">
                <div class="col-xs-5 text-center" style="padding-top: 20px">
                   <span style="font-weight: bold;">Aprobado por Supervisor</span>
                </div>
                <div class="col-xs-5 text-center" style="padding-top: 20px"> 
                    <span style="font-weight: bold;">Aprobado por Administración KAO</span>
                </div>
               
            </div> 

                ';
            
 
         //==============================================================
         //==============================================================
         //==============================================================
 
         $mpdf = new mPDF();
         $stylesheet = file_get_contents('../../assets/css/bootstrap.min.css');
         $stylesheet2 = file_get_contents('../../assets/css/reportesStyles.css');
         $mpdf->WriteHTML($stylesheet,1);	// The parameter 1 tells that this is css/style only and no body/html/text
         $mpdf->WriteHTML($stylesheet2,1);	
         $html = mb_convert_encoding($html, 'UTF-8', 'UTF-8');
         $mpdf->SetTitle("Vale por perdida - ".$ID);
         $mpdf->SetHTMLHeader('
         <div style=" text-align: right;">
             <h5 style="font-size: 10px;">Página: {PAGENO} de {nbpg}</h5> 
             <h5 style="font-size: 10px; font-weight: bold;">ESTADO: '.$estado_txt.'</h5>
             <h5 style="font-size: 10px;">'.$ID.'</h5>
         </div> ');
         $mpdf->WriteHTML($html);
         if ($outputMode == 'F') {
             $ID = "../../assets/docs./$ID";
         }
         
         return $mpdf->Output($ID.'.pdf', $outputMode);
 
         //==============================================================
         //==============================================================
         //==============================================================
 
    }

   
}