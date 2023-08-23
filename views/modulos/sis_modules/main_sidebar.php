    
<?php


function getActive($action){
   if ($_GET['action'] == $action){
    return "active";
    }
    
}
?>

<div id="sidebar-wrapper">
    <ul class="sidebar-nav">
        <li class="sidebar-brand text-center">
            <a href="#" data-toggle="modal" data-target="#modal_info_sesion">
              <img alt="Sin lago" height="25" src="<?php echo PATH_LOGO_CLARO?>">
            </a>
        </li>

        <?php
            foreach ($menus as $menu) {
                if (trim($menu["activo"]) && in_array(trim($menu["action"]), $_SESSION["arrayModulosAccess".APP_UNIQUE_KEY]) ) {
        ?>

        <li><a href="?action=<?php echo trim($menu['action']) ?>" class="<?php echo getActive(trim($menu['action']))?>"><i class="<?php echo trim($menu['iconClass']) ?>"></i> <?php echo $menu['nombre'] ?></a></li>
        
        <?php
                }
            }
        ?>
    </ul>
</div>
<!-- /#sidebar-wrapper -->