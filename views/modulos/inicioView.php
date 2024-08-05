
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

            <li>
                <input id='opcion3' type='checkbox' />
                <label for='opcion3' title="Inventario" style="color:#d5d2d2"><i onclick="" class="fa fa-cubes fa-3x"></i></label>   
            </li>

            <li>
                <input id='opcion4' type='checkbox' />
                <label for='opcion4' title="Utilitarios" style="color:#d5d2d2"><i onclick="" class="fas fa-toolbox fa-3x"></i></label>   
            </li>

            <li>
                <input id='opcion5' type='checkbox' />
                <label for='opcion5' title="Shopify Form - Módulo de Integración con Shopify" style="color:#d5d2d2"><i onclick="" class="fab fa-shopify fa-3x"></i></label>   
            </li>

            <li>
                <input id='opcion5' type='checkbox' />
                <label for='opcion5' title="Módulo de Integración con Vtex" style="color:#d5d2d2"><i onclick="" class="fa-brands fa-vimeo fa-3x"></i></label>   
            </li>

            <li>
                <input id='opcion6' type='checkbox' />
                <label for='opcion6' title="Generación Guias de Envio - Módulo de Integración con Tramaco" style="color:#d5d2d2"><i onclick=""  class="fas fa-truck fa-2x"></i></label>   
            </li>

            <li >
                <input id='serviciocliente' type='checkbox' />
                <label for='serviciocliente' title="Generación de Tickets - Servicio al Cliente" style="color:#d5d2d2"><i onclick="" class="fa fa-ticket fa-2x"></i></label>   
            </li>

            <li>
                <input id='mantenimientoEquipos' type='checkbox' />
                <label for='mantenimientoEquipos' title="Agendamiento - Mantenimiento de equipos" style="color:#d5d2d2"><i onclick="" class="fa fa-wrench fa-3x"></i></label>   
            </li>

            <li>
                <input id='evaluacionempleados' type='checkbox' />
                <label for='evaluacionempleados' title="Evaluación de Empleados" style="color:#d5d2d2"><i onclick="" class="fa fa-list-check fa-3x"></i></label>   
            </li>

            <li>
                <input id='valesperdida' type='checkbox' />
                <label for='valesperdida' title="Vales por pérdida" style="color:#d5d2d2"><i onclick="" class="fa fa-file-text-o fa-3x"></i></label>   
            </li>

            <li>
                <input id='anfitriones' type='checkbox' />
                <label for='anfitriones' title="Formulario de Anfitriones" style="color:#d5d2d2"><i onclick=""  class="fa fa-users fa-3x"></i></label>   
            </li>

            <li>
                <input id='evaluacionjefes' type='checkbox' />
                <label for='evaluacionjefes' title="Evaluación de Jefes" style="color:#d5d2d2"><i onclick="" class="fa fa-handshake-o fa-3x"></i></label>   
            </li>

            <li>
                <input id='ordenpedido' type='checkbox' />
                <label for='ordenpedido' title="Orden de Pedido -Vehiculos" style="color:#d5d2d2"><i onclick="" class="fa fa-car fa-3x"></i></label>   
            </li>

            <li>
                <input id='checklistlocales' type='checkbox' />
                <label for='checklistlocales' title="Checklist locales" style="color:#d5d2d2"><i onclick="" class="fa fa-check fa-3x"></i></label>   
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