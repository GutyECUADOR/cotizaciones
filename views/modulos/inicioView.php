
 <!-- CSS Propios -->
  <link rel="stylesheet" href="<?php echo ROOT_PATH; ?>assets\css\circlemenu.css">

 <?php include 'sis_modules/header_main.php'?>

    <div id="menu_circle">
      <div class='selector'>
          <ul>
            <li onclick="location.href='?action=formularioCortes';">
                <input id='c1' type='checkbox'/>
                <label for='c1' title='Formulario de Cortes'><i class="fa fa-retweet fa-3x" aria-hidden="true"></i></i></label>   
              </a>
            </li>

            <li onclick="location.href='?action=creacionReceta';">
                <input id='c2' type='checkbox' />
                <label for='c2' title='Creacion de receta'><i class="fa fa-cutlery fa-3x" aria-hidden="true"></i></i></label>   
              </a>
            </li>
          
            <li onclick="location.href='?action=cotizaciones';">
                <input id='c3' type='checkbox' />
                <label for='c3' title='Punto de Venta - Cotizaciones'><i class="fa fa-shopping-cart fa-3x" aria-hidden="true"></i></i></label>   
              </a>
            </li>
          

<!-- 
            <li onclick="location.href='/wssp/ws-admin/';">
              <input id='c4' type='checkbox'/>
            <label id='menu4' for='c4' title='Administración'></label>
            </li>

            <li onclick="location.href='/wssp/ws-evanfitriones/';">
              <input id='c5' type='checkbox'/>
            <label id='menu5' for='c5' title='Registros de Anfitriones'></label>
            </li>

            <li onclick="location.href='/wssp/ws-evaluagge/';">
              <input id='c6' type='checkbox'/>
            <label id='menu6' for='c6' title='Evaluación de Jefes'></label>
            </li>

            <li onclick="location.href='../intranetApp-v2.1';">
              <input id='c7' type='checkbox'/>
              <label id='menu7' for='c7' title='Mantenimiento de Equipos'></label>
            </li>

            <li onclick="location.href='/wssp/ws-estadovehiculo/ordenPedido.php';">
              <input id='c8' type='checkbox'/>
              <label id='menu8' for='c8' title='Orden de Pedido (Vehiculos)'></label>   
            </li>

            <li onclick="location.href='/wssp/ws-estadovehiculo/';">
              <input id='c9' type='checkbox'/>
              <label id='menu9' for='c9' title='Estado del Vehiculo'></label>   
            </li>

            <li onclick="location.href='../carteraclientes';">
              <input id='c10' type='checkbox'/>
              <label id='menu10' for='c10' title='Cartera de Clientes'></label>   
            </li>

            <li onclick="location.href='../cotizacionesApp';">
              <input id='c11' type='checkbox'/>
              <label id='menu11' for='c11' title='Cotizaciones Web'></label>   
            </li>

 -->
           
          </ul>
          <button id='center_logo'></button>
          
      </div>
    </div>
      
    <!-- USO JQUERY, y Bootstrap CDN-->
    <script src="assets\js\jquery.min.js"></script>
    <script src="assets\js\bootstrap.min.js"></script>
    <script src="assets\js\moment.min.js"></script>
    <script src="assets\js\inicio.js?<?php echo date('Ymdhiiss')?>"></script>