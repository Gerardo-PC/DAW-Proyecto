<?php
//Cargador automático de clases de la librería
spl_autoload_register(function($clase){
    require_once 'lib/'.$clase.'.php';
});
//Carga de la configuración básica
require_once 'config/config.php';
