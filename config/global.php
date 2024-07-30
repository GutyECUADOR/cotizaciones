<?php
/* Configurar aqui todas las variables globales a utilizar*/
define("APP_NAME", "Adfolsa Web Forms");
define("EMPRESA_NAME", "Adfolsa & Vitador");
define("APP_UNIQUE_KEY", "AdfolsaVita2021$");
define("LOGO_NAME", "./assets/img/logo.png");
define("PATH_LOGO_CLARO", "./assets/img/logo-claro.png");
define("PATH_LOGO_OSCURO", "./assets/img/logo.png");
define("PATH_OUT_OFF_TIME", "./assets/img/out-off-time.jpg");
define("PATH_NO_PERMISSION", "./assets/img/no-permission.jpg");
define("APP_VERSION", "2023.01.13");
define("ROOT_PATH","");   //Root del proyecto
define("DEFAULT_DBName","MODELO");  // PARA HACER PRUEBAS EN EL API cuando no esa login
define("DEFAULT_DBName_wssp","wssp");  
define("HOST_API_SHOPIFY","");   //URL de API Shopiy
/*Envio de correos se debe definir en archivo ENV */



define("IMAGES_UPLOAD_DIR", $_SERVER['DOCUMENT_ROOT'].'/uploadsCotizaciones');

define("VIEWS_PATH","/views");
define("CONFIG_FILE","./config/configuraciones.xml");
define("DEFAULT_EMAIL","ventas@adfolsa.com");

/*Envio de correos */
define("DEFAULT_SMTP","mail.adfolsa.com.ec");
define("DEFAULT_SENDER_EMAIL","no-reply@adfolsa.com.ec");
define("DEFAULT_EMAILPASS","Noreply2021$.");
define("EDOCS_MAIL","");

/*URL Body Email*/
define("LOGO_ONLINE","https://adfolsa.com.ec/img/resources/logo.png");
define("SITIOWEB_ONLINE","https://adfolsa.com.ec/");
define("BODY_EMAIL_TEXT","Reciba un cordial saludo de quienes conformamos ".EMPRESA_NAME.", estamos atendiendo a su requerimiento por lo que encontrara el documento solicitado adjunto en este correo");