<?php namespace App\Controllers;

use App\Models\InventarioModel;
use App\Models\WinfenixModel;
use Mpdf\Mpdf;

class DocumentoController  {

    public $defaulDataBase;
    public $inventarioModel;

    public function __construct() {
        $this->defaulDataBase = (!isset($_SESSION["empresaAUTH".APP_UNIQUE_KEY])) ? DEFAULT_DBName : $_SESSION["empresaAUTH".APP_UNIQUE_KEY] ;
        $this->inventarioModel = new InventarioModel();
        $this->inventarioModel->setDbname($this->defaulDataBase);
        $this->inventarioModel->conectarDB();
        $this->winfenixModel = new WinfenixModel();
        $this->winfenixModel->setDbname($this->defaulDataBase);
        $this->winfenixModel->conectarDB();
    }

    public function getPDF_ingresosEgresos(string $ID, $outputMode = 'I') {

        $mpdf = new Mpdf();
        $html = '<h1>Hello world!</h1>';

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