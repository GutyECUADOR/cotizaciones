<?php
/* Configurar aqui todas las variables globales a utilizar*/
define("APP_NAME", "Adfolsa & Vitador");
define("EMPRESA_NAME", "Adfolsa & Vitador");
define("APP_UNIQUE_KEY", "AdfolsaVita2021$");
define("LOGO_NAME", "./assets/img/logo.png");
define("APP_VERSION", "05.08.2021");
define("ROOT_PATH","");   //Root del proyecto

define("IMAGES_UPLOAD_DIR", $_SERVER['DOCUMENT_ROOT'].'/uploadsCotizaciones');

define("VIEWS_PATH","/views");
define("CONFIG_FILE","./config/configuraciones.xml");
define("DEFAULT_DBName","MODELO");
define("DEFAULT_EMAIL","");

/*Envio de correos */
define("DEFAULT_SMTP","smtp.gmail.com");
define("DEFAULT_SENDER_EMAIL","");
define("DEFAULT_EMAILPASS","");
define("EDOCS_MAIL","");

/*URL Body Email*/
define("LOGO_ONLINE","");
define("SITIOWEB_ONLINE","");
define("BODY_EMAIL_TEXT","Reciba un cordial saludo de quienes conformamos".EMPRESA_NAME.", estamos atendiendo a su requerimiento por lo que encontrara el documento solicitado adjunto en este correo");
