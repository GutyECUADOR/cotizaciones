<?php namespace App\Controllers;

use App\Models\InventarioModel;
use App\Models\WinfenixModel;
use App\Models\AjaxModel;
use Mpdf\Mpdf;

class DocumentoController  {

    public $defaulDataBase;
    public $inventarioModel;
    public $ajaxModel;

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
                                        <td style="font-weight: bold;" class="text-right" colspan="2">IVA 12%:</td>
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

   

}