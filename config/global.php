<?php
/* Configurar aqui todas las variables globales a utilizar*/
define("APP_NAME", "WebForms Imperium");
define("EMPRESA_NAME", "Imperium Restaurante");
define("APP_UNIQUE_KEY", "Imperium2021$");
define("LOGO_NAME", "./assets/img/logo.png");
define("APP_VERSION", "2.7.0");
define("ROOT_PATH","");   //Root del proyecto

define("IMAGES_UPLOAD_DIR", $_SERVER['DOCUMENT_ROOT'].'/uploadsCotizaciones');

define("VIEWS_PATH","/views");
define("CONFIG_FILE","./config/configuraciones.xml");
define("DEFAULT_DBName","MODELO1");
define("DEFAULT_EMAIL","");

/*Envio de correos */
define("DEFAULT_SMTP","smtp.gmail.com");
define("DEFAULT_SENDER_EMAIL","");
define("DEFAULT_EMAILPASS","");

/*URL Body Email*/
define("LOGO_ONLINE","http://www.agricolabaquero.com/img/resources/logo.png");
define("SITIOWEB_ONLINE","http://www.agricolabaquero.com");
define("BODY_EMAIL_TEXT","Reciba un cordial saludo de quienes conformamos IMPERIUM RESTAURANTE, estamos atendiendo a su requerimiento por lo que encontrara el documento solicitado adjunto en este correo");
