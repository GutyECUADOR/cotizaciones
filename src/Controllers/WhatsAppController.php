<?php namespace App\Controllers;

use Twilio\Rest\Client;

class WhatsAppController  {

    private $documentosController;

    public function __construct() {
        $this->documentosController = new DocumentoController();
    }

    public function sendMessage(object $datosWhatsApp){
        
        $sid = $_ENV['TWILIO_ACCOUNT_SID'];
        $token = $_ENV['TWILIO_AUTH_TOKEN'];
        $twilio = new Client($sid, $token);
        
        $message = $twilio->messages
                          ->create("whatsapp:+593999887479", // to
                                   [
                                       "from" => "whatsapp:+14155238886",
                                       "mediaUrl" => ["http://adfolsa.com.ec/docs/992014COT00023345.pdf"],
                                       "body" => $datosWhatsApp->mensaje
                                   ]
                          );
        
        return $arrayName = array('status' => $message->status, 'id' => $message->sid);

    }
}