<nav class="navbar navbar-default ">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="#" data-toggle="modal" data-target="#modal_info_sesion">
        <span><img alt="Brand" height="25" src="<?php echo LOGO_NAME?>"></span>
        <span style="margin-left:5px"><?php echo APP_NAME; ?></span>
      </a>
      
    </div>

    
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">  
        <li><a href="?action=inicio"><i class="fa fa-home" aria-hidden="true"></i></i> Inicio</a></li>
      </ul>

      <ul class="nav navbar-nav navbar-right">
        <?php
              if (isset($_SESSION["usuarioRUC".APP_UNIQUE_KEY])){
                echo '
                <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-user" aria-hidden="true"></i></i> Bienvenido, '.$_SESSION["usuarioNOMBRE".APP_UNIQUE_KEY].'<span class="caret"></span></a>
                  <ul class="dropdown-menu">
                    <li><a href="?action=logout"><span class="glyphicon glyphicon-log-in" ></span> Cerrar Sesión </a></li>
                    
                  </ul>
                </li>
                ';
                
            }else{
              echo '
              
                <li><a id="liveclock"></a></li>
                <li><a href="?action=logout">Iniciar Sesión</a></li>
              ';
            }
        ?>

       
      </ul>
      
      
    </div><!-- /.navbar-collapse -->

  </div><!-- /.container-fluid -->
</nav>