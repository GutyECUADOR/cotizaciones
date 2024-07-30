
 <!-- CSS Propios -->
  <link rel="stylesheet" href="<?php echo ROOT_PATH; ?>assets\css\circlemenu.css">

 <?php include 'sis_modules/header_main.php'?>

    <div id="menu_circle">
      <div class='selector'>
          <ul>
            <li>
                <input id='opcion2' type='checkbox' />
                <label for='opcion2' title='Ventas'><i onclick="location.href='?action=ventas';" class="fas fa-cash-register fa-3x"></i></label>
            </li>
        
          </ul>
          <button id='center_logo'></button>
          
      </div>

       <!-- Modal Info sesion -->
       <?php require_once 'sis_modules/modal_info_session.php'?>
       
    </div>
      
    <!-- USO JQUERY, y Bootstrap CDN-->
    <script src="assets\js\libs\jquery.min.js"></script>
    <script src="assets\js\libs\bootstrap.min.js"></script>
    <script src="assets\js\libs\moment.min.js"></script>
    <script src="assets\js\pages\inicio.js?<?php echo date('Ymdhiiss')?>"></script>