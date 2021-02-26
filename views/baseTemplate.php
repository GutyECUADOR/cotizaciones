<?php

use App\Controllers\RouteController;

?>
<!DOCTYPE html>
<html lang="es">

  <head>

      <!-- Disable cache -->
      <meta http-equiv="Expires" content="0">
      <meta http-equiv="Last-Modified" content="0">
      <meta http-equiv="Cache-Control" content="no-cache, mustrevalidate">
      <meta http-equiv="Pragma" content="no-cache">
      
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
     
      <link rel="shortcut icon" href="<?php echo ROOT_PATH; ?>assets\css\img\favicon.ico">

      <!-- CSS Bootstrap 3.3.7 -->
      <link rel="stylesheet" href="<?php echo ROOT_PATH; ?>assets\css\bootstrap.min.css">
     
      <!-- Librerias-->
      <link rel="stylesheet" href="<?php echo ROOT_PATH; ?>assets\css\bootstrap-datepicker.css">
      <link rel="stylesheet" href="<?php echo ROOT_PATH; ?>assets\css\font-awesome.min.css">

      <!-- CSS Propios -->
      
      <link rel="stylesheet" href="<?php echo ROOT_PATH; ?>assets\css\loaders.css">
      <link rel="stylesheet" href="<?php echo ROOT_PATH; ?>assets\css\pnotify.custom.min.css">

      <!-- Custom CSS -->
      <link rel="stylesheet" href="<?php echo ROOT_PATH; ?>assets\css\custom.css">
       
      <!-- CSS Paginas -->

      <title><?php echo APP_NAME; ?></title>

  </head>

  <body>
    
    <?php
        $inicioController = new RouteController;
        $inicioController->actionCatcherController();
    ?>
      
    
  </body>
</html>


