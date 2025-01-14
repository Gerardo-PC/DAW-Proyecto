<?php
/**
 * Fichero de configuración, generalmente cargado desde init.php
 * Incluye constantes que apuntan al directorio de aplicación y URL.
 */
// Constantes que apuntan al directorio de aplicación y a la URL.
define('APP', dirname(dirname(__FILE__)));
define('URL', $_SERVER["REQUEST_SCHEME"].'://'.$_SERVER["SERVER_NAME"]);
// Página principal por defecto.
define('PRINCIPAL', URL.'/portada.php');
// Fichero con configuración de la BBDD y otros, para uso desde clase Configuración.
define("FICHERO_CONFIG",$_SERVER['DOCUMENT_ROOT'].'/app/config/configuracion.json');
