<?php
/**
 * Clase que permite recuperar la configuración almacenada en fichero configuracion.json
 */
class Configuracion{
    public static function getConfiguracion():object{
        if(file_exists(FICHERO_CONFIG)){
            $config_json = file_get_contents(FICHERO_CONFIG);
            $config=json_decode($config_json);
            return $config;
        }else{
            die("Error, el fichero ".FICHERO_CONFIG." no existe.");
        }
    }
}