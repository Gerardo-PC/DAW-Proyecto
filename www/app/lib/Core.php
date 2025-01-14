<?php
/**
 * Clase núcleo de la aplicación.
 * Reconoce la URL solicitada e instancia el controlador y métodos correspondientes (con sus parámetros)
 * Si no existe en la aplicación devuelve error 404 y redirige a portada.
 */
class Core
{
    protected $controlador;
    protected $metodo;
    protected $parametros = [];

    /**
     * Constructor que gestiona la llamada al controlador correspondiente.
     */
    public function __construct(){
        session_start();
        
        $url = $this->getUrl();

        if(!is_null($url) && file_exists('../app/controllers/'.ucwords($url[0]).'.php')){
            $this->controlador = ucwords($url[0]);
            unset($url[0]);
            //Controlador existe
            require_once '../app/controllers/'.$this->controlador.'.php';

            //Instancia controlador y busca el método
            $this->controlador = new $this->controlador;
            if(isset($url[1])){
                if(method_exists($this->controlador, $url[1])){
                    $this->metodo = $url[1];
                    unset($url[1]);
                    
                    //Prepara los parámetros si existen
                    $this->parametros = $url ? array_values($url):[];    
        
                    //Llama a la función del controlador
                    call_user_func_array([$this->controlador, $this->metodo],$this->parametros);
                }else{
                    //si el método no existe devuelve 404
                    http_response_code(404);
                    header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found",TRUE,http_response_code());
                    var_dump($url);
                    die("404 NO ENCONTRADO");
                }
            }
        }else{
            //Si no encuentra controlador envía a página de inicio.
            header("Location: ".PRINCIPAL);
        }
    }


    /**
     * Devuelve array con la URL, separada por / y sin carácteres inválidos
     * @return array con la url [0]=controlador [1]=método [2]=parámetros
     */
    public function getUrl(){
        if(isset($_GET['url'])){
            $url = rtrim($_GET['url'],'/');
            $url = filter_var($url,FILTER_SANITIZE_URL);
            $url = explode('/',$url);
            return $url;
        }
    }
}