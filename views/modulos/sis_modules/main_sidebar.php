    
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
              <img alt="Sin lago" height="50" src="<?php echo PATH_LOGO_CLARO?>">
            </a>
        </li>

        <?php
            foreach ($menus as $option) {
                if (trim($option["activo"])) {
        ?>

        <li><a href="?action=<?php echo trim($option['action']) ?>" class="<?php echo getActive(trim($option['action']))?>"><i class="<?php echo trim($option['iconClass']) ?>"></i> <?php echo $option['nombre'] ?></a></li>
        
        <?php
                }
            }
        ?>
    </ul>
</div>
<!-- /#sidebar-wrapper -->