<?php namespace App\Controllers;

class FTPController  {

    private $documentosController;

    public function __construct() {
        $this->documentosController = new DocumentoController();
    }

    public function uploadFtpFile(object $datosWhatsApp){
       
        $this->documentosController->getPDF_Cotizacion($datosWhatsApp->idDocumento, 'F');
        $file = "../../assets/docs/$datosWhatsApp->idDocumento.pdf"; 
        $remote_file = "./public_html/docs/$datosWhatsApp->idDocumento.pdf";
        $ftp_server = $_ENV['FTP_SERVER'];
        $ftp_user_name = $_ENV['FTP_USER_NAME'];
        $ftp_user_pass = $_ENV['FTP_USER_PASS'];

        // establecer una conexión básica

            $conn_id = ftp_connect($ftp_server);
            // iniciar sesión con nombre de usuario y contraseña
            $login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);

            ftp_pasv($conn_id, true);

            // cargar un archivo
            if (ftp_put($conn_id, $remote_file, $file, FTP_BINARY )) {
                $response = array('status' => 'OK', 'message'=> "Se ha cargado $datosWhatsApp->idDocumento con éxito.");
            } else {
                $response = array('status' => 'ERROR', 'message'=> "Hubo un problema durante la transferencia de $datosWhatsApp->idDocumento");
            }
            // cerrar la conexión ftp
            ftp_close($conn_id);

            return $response;
    }
}