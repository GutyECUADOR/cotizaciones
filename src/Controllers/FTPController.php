<?php namespace App\Controllers;

use phpseclib3\Net\SFTP;

class FTPController  {

    private $documentosController;

    public function __construct() {
        $this->documentosController = new DocumentoController();
    }

    public function uploadFtpFile(object $datosWhatsApp){
       
        $this->documentosController->getPDF_Cotizacion($datosWhatsApp->idDocumento, 'F');
        $file = "../../assets/docs/$datosWhatsApp->idDocumento.pdf"; 
        $remote_file = "/home/kao/public_html/docs/$datosWhatsApp->idDocumento.pdf";
        $ftp_server = $_ENV['FTP_SERVER'];
        $ftp_user_name = $_ENV['FTP_USER_NAME'];
        $ftp_user_pass = $_ENV['FTP_USER_PASS'];

        // establecer una conexión básica

            $sftp = new SFTP($ftp_server, 8888);
           
            if (!$sftp->login($ftp_user_name, $ftp_user_pass)) {
                throw new \Exception('Login failed in SMTP.');
            }

            // cargar un archivo
            if ($sftp->put($remote_file,  $file, SFTP::SOURCE_LOCAL_FILE)) {
                $response = array('status' => 'OK', 'message'=> "Se ha cargado $datosWhatsApp->idDocumento con éxito.");
            } else {
                $response = array('status' => 'ERROR', 'message'=> "Hubo un problema durante la transferencia de $datosWhatsApp->idDocumento");
            }
          

            return $response;
    }
}