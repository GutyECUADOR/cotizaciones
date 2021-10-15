
 <!-- CSS Propios -->
  <link rel="stylesheet" href="<?php echo ROOT_PATH; ?>assets\css\circlemenu.css">

 <?php include 'sis_modules/header_main.php'?>

    <div id="menu_circle">
      <div class='selector'>
          <ul>
            
            <li onclick="location.href='?action=ventas';">
                <input id='c3' type='checkbox' />
                <label for='c3' title='VENTAS'><i class="fa fa-shopping-cart fa-3x" aria-hidden="true"></i></i></label>   
              </a>
            </li>

          </ul>
          <button id='center_logo'></button>
          
      </div>
    </div>
      
    <!-- USO JQUERY, y Bootstrap CDN-->
    <script src="assets\js\libs\jquery.min.js"></script>
    <script src="assets\js\libs\bootstrap.min.js"></script>
    <script src="assets\js\libs\moment.min.js"></script>
    <script src="assets\js\pages\inicio.js?<?php echo date('Ymdhiiss')?>"></script>