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

    public function getPDF_Cotizacion($IDDocument, $outputMode = 'I'){

        $empresaData = $this->winfenixModel->getInfoEmpresaController();
        $VEN_CAB = $this->winfenixModel->getVEN_CABController($IDDocument);
        $VEN_MOV = $this->winfenixModel->getVEN_MOVController($IDDocument);
        
        
         
         $html = '
             
             <div style="width: 100%;">
         
                 <div style="float: right; width: 75%;">
                     <div id="informacion">
                         <h4>'.$empresaData["NomCia"].'</h4>
                         <h4>Direccion: '.$empresaData["DirCia"].'</h4>
                         <h4>Telefono: '.$empresaData["TelCia"].'</h4>
                         <h4>RUC: '.$empresaData["RucCia"].'</h4>
                         <h4>PROFORMA #  '.$VEN_CAB["ID"].' </h4>
                     </div>
                 </div>
         
                 <div id="logo" style="float: left; width: 20%;">
                     <img src="../../../assets/img/logo.png" alt="Logo">
                 </div>
         
             </div>
         
             <div id="infoCliente" class="rounded">
                 <div class="cabecera"><b>Fecha:</b> '. date('Y-m-d').'</div>
                 <div class="cabecera"><b>Cliente:</b> '.$VEN_CAB["NOMBRE"].'</div>
                 <div class="cabecera"><b>Direccion: </b> '.$VEN_CAB["DIRECCION1"].' </div>
                 <div class="cabecera"><b>Telefono: </b> '.$VEN_CAB["TELEFONO1"].' </div>
                 <div class="cabecera"><b>Email: </b> '.$VEN_CAB["EMAIL"].' </div>
                 <div class="cabecera"><b>Vendedor: </b> '.$VEN_CAB["VendedorName"].' </div>
             </div>
         
             <table class="items" width="100%" style="font-size: 9pt; border-collapse: collapse; " cellpadding="8">
                 <thead>
                     <tr>
                         <td width="5%">Item</td>
                         <td width="11%">Cod.</td>
                         <td width="7%">Cant.</td>
                         <td width="45%">Descripcion</td>
                         <td width="6%">IVA</td>
                         <td width="15%">P. Unit.</td>
                         <td width="10%">% Desc.</td>
                         <td width="10%">V. Desc.</td>
                         <td width="15%">P. Total</td>
                     </tr>
                 </thead>
             <tbody>
         
             <!-- ITEMS HERE -->
             ';
                 $cont = 1;
                 foreach($VEN_MOV as $row){
                    
                     $html .= '
         
                     <tr>
                         <td align="center">'.$cont.'</td>
                         <td align="center">'.$row["CODIGO"].'</td>
                         <td align="center">'.$row["CANTIDAD"].'</td>
                         <td>'.$row["Nombre"].'</td>
                         <td>'.$row["tipoiva"].'</td>
                         <td>'.$row["PRECIO"].'</td>
                         <td>'.$row["DESCU"].'</td>
                         <td class="cost"> '.$row["DESCU"].' </td>
                         <td class="cost"> '.$row["PRECIOTOT"].'</td>
                     </tr>';
                     $cont++;
                     }
         
             $html .= ' 
             
         
             <!-- END ITEMS HERE -->
                 <tr>
                 <td class="blanktotal" colspan="6" rowspan="6"></td>
                 <td class="totals" colspan="2">Imponible 0%:</td>
                 <td class="totals cost">'.$VEN_CAB["BASCERO"].'</td>
                 </tr>
         
             
                 <tr>
                 <td class="totals" colspan="2">Imponible 12%:</td>
                 <td class="totals cost">'.$VEN_CAB["BASIVA"].'</td>
                 </tr>
         
                 <tr>
                 <td class="totals" colspan="2">Subtotal:</td>
                 <td class="totals cost">'.$VEN_CAB["SUBTOTAL"].'</td>
                 </tr>
         
                 <tr>
                 <td class="totals" colspan="2">Base Imponible:</b></td>
                 <td class="totals cost">0</td>
                 </tr>
         
                 <tr>
                 <td class="totals" colspan="2">IVA:</td>
                 <td class="totals cost">'.$VEN_CAB["IMPUESTO"].'</td>
                 </tr>
         
                 <tr>
                 <td class="totals" colspan="2"><b>Total Pagar:</b></td>
                 <td class="totals cost"><b>'.$VEN_CAB["TOTAL"].'</b></td>
                 </tr>
         
             </tbody>
             </table>
 
             <div style="width: 100%;">
                 <p id="observacion">Observacion: '.$VEN_CAB["OBSERVA"].'</p> 
             </div>
         
             <div style="width: 100%;">
                 <p>Imagenes del documento: '.$IDDocument.' </p>
                 '. $this->getLinkImagesByDocument($IDDocument) .' 
             </div>
         ';
 
         //==============================================================
         //==============================================================
         //==============================================================
 
         /* require_once '../../../vendor/autoload.php'; */
         $mpdf = new mPDF();
 
         // LOAD a stylesheet
         $stylesheet = file_get_contents('../../../assets/css/reportesStyles.css');
         
         $mpdf->WriteHTML($stylesheet,1);	// The parameter 1 tells that this is css/style only and no body/html/text
 
         $mpdf->WriteHTML($html);
         
         return $mpdf->Output('doc.pdf', $outputMode);
 
         //==============================================================
         //==============================================================
         //==============================================================
 
    }

    public function getPDF_Cotizacion_Ejemplo(string $ID, $outputMode = 'I') {

        $empresaData = $this->ajaxModel->getAllInfoEmpresaModel();
        $CAB = $this->winfenixModel->sql_getINV_CAB($ID);
        $MOV = $this->winfenixModel->SP_INVSelectMov($ID);

        $mpdf = new Mpdf();
        $html = '
            <div style="width: 100%;">
                <div style="float: right; width: 75%;">
                    <div id="informacion">
                        <h4>'.$empresaData["NomCia"].'</h4>
                        <h4>Direccion: '.$empresaData["DirCia"].'</h4>
                        <h4>Telefono: '.$empresaData["TelCia"].'</h4>
                        <h4>RUC: '.$empresaData["RucCia"].'</h4>
                        <h4>Documento #'.$ID.'</h4>
                    </div>
                </div>
        
                <div id="logo" style="float: left; width: 20%;">
                    <img src="../../assets/img/logo.png" alt="Logo">
                </div>
        
            </div>
        
            <div id="infoCliente" class="rounded">
                <div class="cabecera"><b>Fecha:</b> '. $CAB['FECHA'].'</div>
                <div class="cabecera"><b>Tipo:</b> '. $CAB['TIPO'].'</div>
                <div class="cabecera"><b>Bodega:</b>'. $CAB['BODEGA'].' </div>
            </div>
        
            <table class="items" width="100%" style="font-size: 9pt; border-collapse: collapse; " cellpadding="8">
                <thead>
                    <tr>
                        <td width="15%">Cod.</td>
                        <td width="30%">Nombre</td>
                        <td width="15%">Unidad.</td>
                        <td width="20%">Cantidad</td>
                        <td width="20%">Costo</td>
                    </tr>
                </thead>
            <tbody>
            <!-- ITEMS HERE -->
            ';
                $cont = 1;
                foreach($MOV as $row){
                   
                    $html .= '
        
                    <tr>
                        <td align="center">'.$row["Codigo"].'</td>
                        <td>'.$row["Nombre"].'</td>
                        <td>'.$row["Unidad"].'</td>
                        <td>'.$row["Cantidad"].'</td>
                        <td class="cost"> '.$row["Costo"].'</td>
                    </tr>';
                    $cont++;
                    }
        
            $html .= ' 
            
            </tbody>
            </table>

        
        ';
        // LOAD a stylesheet
        $stylesheet = file_get_contents('../../assets/css/reportesStyles.css');
    
        $mpdf->WriteHTML($stylesheet,1);	// The parameter 1 tells that this is css/style only and no body/html/text

        $html = mb_convert_encoding($html, 'UTF-8', 'UTF-8');
        $mpdf->SetTitle("Reporte ".$ID);
        $mpdf->SetHTMLHeader('
        <div id="cod">
            <h5 class="myheader">Página: {PAGENO} de {nbpg}</h5>  
        </div> ');
        $mpdf->WriteHTML($html);
        
        return $mpdf->Output('doc.pdf', $outputMode);
    }

    

}