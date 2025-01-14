<?php
/**
 * Clase padre de todos los controladores
 * Incluye métodos que permiten cargar modelos o vistas.
 */
class Control{
    /**
     * Carga el modelo indicado
     * @param string $model Nombre del modelo datos a cargar
     */
    public function load_model(string $model){
        require_once '../app/models/'.ucwords($model).'.php';
        $model = 'docweb\\models\\'.$model; //añade el namespace
        return new $model;
    }

    /**
     * Carga la vista indicada, con los datos facilitados
     * @param string $view Nombre de la vista a cargar
     * @param array $datos Array de datos pasados a la vista, Opcional. Por defecto array vacío.
     */
    public function load_view($view, $datos = []){
        if(file_exists('../app/views/paginas/'.$view.'.php')){
            //carga la vista con cabeceras y pie estándar.
            require_once '../app/views/inc/header.php';
            require_once '../app/views/paginas/'.$view.'.php';
            require_once '../app/views/inc/footer.php';
        }else{
            http_response_code(404);
            header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found",TRUE,http_response_code());
            die("404 NO ENCONTRADO ($view)");
        }
    }
}
