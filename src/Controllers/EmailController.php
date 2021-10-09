<?php namespace App\Controllers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailController  {

    private $documentosController;

    public function __construct() {
        $this->documentosController = new DocumentoController();
    }

    public function sendCotizacion(object $datosEmail){
        
        $arrayEmails = $datosEmail->destinatario; 
        $customMessage = $datosEmail->mensaje; 
        $IDDocument = $datosEmail->idDocumento; 
      
        $arrayCorreos =  explode( ';', $arrayEmails );

        $smtpserver = DEFAULT_SMTP;
        $userEmail = DEFAULT_SENDER_EMAIL;
        $pwdEmail = DEFAULT_EMAILPASS; 

        $mail = new PHPMailer(true);  // Passing `true` enables exceptions
        try {
            //Server settings
            $mail->SMTPDebug = false;           // Enable verbose debug output 0->off 2->debug
            $mail->isSMTP();                    // Set mailer to use SMTP
            $mail->Host = $smtpserver;          // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;             // Enable SMTP authentication
            $mail->Username = $userEmail;       // SMTP username
            $mail->Password = $pwdEmail;        // SMTP password
            $mail->SMTPSecure = 'tls';          // Enable TLS encryption, `ssl` also accepted
            $mail->Port = 587;                  // TCP port to connect to

            //Recipients
            $mail->setFrom($userEmail, $userEmail);

            foreach ($arrayCorreos as $correo) {
                $mail->addAddress($correo, 'Cliente'); // Add a recipient
            }

            $mail->AddCC(DEFAULT_EMAIL);
            //Content
            $mail->CharSet = "UTF-8";
            $mail->isHTML(true);                  // Set email format to HTML
            $mail->Subject = 'Cotizacion #'.$IDDocument;
            $mail->Body    = $customMessage; //$this->getBodyHTMLofEmail($IDDocument, $customMessage);
        
            // Adjuntos
            $mail->addStringAttachment($this->documentosController->getPDF_Cotizacion($IDDocument, 'S'), 'cotizacion-'.$IDDocument.'.pdf');

            $mail->send();
            $detalleMail = 'Correo ha sido enviado a : '. $arrayEmails;
           
            $pcID = php_uname('n'); // Obtiene el nombre del PC
            $ip = 'ninguna';

                $log  = "User: ".$ip.' - '.date("F j, Y, g:i a").PHP_EOL.
                "PCid: ".$pcID.PHP_EOL.
                "Detail: ".$detalleMail.PHP_EOL.
                "-------------------------".PHP_EOL;
                //Save string to log, use FILE_APPEND to append.

                file_put_contents('../../logs/logMailOK.txt', $log, FILE_APPEND );
            
            return array('status' => 'OK', 'message' => $detalleMail ); 

        } catch (Exception $e) {
            $ip = 'ninguna';
                $pcID = php_uname('n'); // Obtiene el nombre del PC
                $log  = "User: ".$ip.' - '.date("F j, Y, g:i a").PHP_EOL.
                "PCid: ".$pcID.PHP_EOL.
                "Detail: ".$mail->ErrorInfo .' No se pudo enviar correo a: ' . $arrayEmails . PHP_EOL.
                "-------------------------".PHP_EOL;
                //Save string to log, use FILE_APPEND to append.
                file_put_contents('../../logs/logMailError.txt', $log, FILE_APPEND);
                $detalleMail = 'Error al enviar el correo. Mailer Error: '. $mail->ErrorInfo;
            return array('status' => 'ERROR', 'message' => $detalleMail ); 
            
        }

    }    


}
