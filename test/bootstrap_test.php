<?php
//Cargador automático de clases de la librería
spl_autoload_register(function($clase){
    if(!str_contains($clase, 'PHPUnitPHAR')){ //Evito la carga de las propias clases de Test.
        require_once '../www/app/lib/'.$clase.'.php';
    }
});
//Carga de la configuración básica
//require_once '../www/app/config/config.php';
