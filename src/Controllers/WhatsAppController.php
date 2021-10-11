<?php namespace App\Controllers;

use Twilio\Rest\Client;

class WhatsAppController  {

    private $documentosController;

    public function __construct() {
        $this->documentosController = new DocumentoController();
    }

    public function sendMessage(object $datosWhatsApp){
        
        $sid = 'ACced1c6889214a8db51239242bd62d297'; //getenv("TWILIO_ACCOUNT_SID");
        $token = 'f0d0975ab48cb4b7e37a83f497ed684d'; //getenv("TWILIO_AUTH_TOKEN");
        $twilio = new Client($sid, $token);
        
        $message = $twilio->messages
                          ->create("whatsapp:+593999887479", // to
                                   [
                                       "from" => "whatsapp:+14155238886",
                                       "body" => "API TEST mensaje",
                                       "mediaUrl" => ["http://www.africau.edu/images/default/sample.pdf"]
                                   ]
                          );
        
        return $arrayName = array('status' => $message->status, 'id' => $message->sid, 'message' => $message->error_message);

    }
}