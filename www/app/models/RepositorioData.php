<?php
namespace docweb\models;
require_once("TipoRepositorio.php");

/**
 * Clase para manejar datos de Repositorio (genérico)
 */
final class RepositorioData{

    private int|null $ID;
    private string $nombre;
    private string $ruta;
    private string|null $login;
    private string|null $pass;
    private TipoRepositorio|null $tipo;
    private int $IdAdmin; //ID del administrador de repositorio
    private mixed $extra;  //Datos adicionales repositorio (Ej. ID de archivador en Docuware)

    /**
     * Recupera el ID del repositorio
     * @return int ID del repositorio
     */
    public function getID():int{
        return $this->ID;
    }
    /**
     * Recupera el Nombre de repositorio
     * @return string Nombre del repositorio
     */
    public function getNombre():string{
        return $this->nombre;
    }
    /**
     * Recupera la ruta del repositorio
     * @return string Ruta de acceso al repositorio
     */
    public function getRuta():string{
        return $this->ruta;
    }
    /**
     * Recupera el login de acceso al repositorio
     * @return string|null Login de acceso al repositorio o null
     */
    public function getLogin():string|null{
        return $this->login;
    }
    /**
     * Recupera contraseña de acceso al repositorio
     * @return string|null Contraseña de acceso o null
     */
    public function getPass():string|null{
        return $this->pass;
    }
    /**
     * Recupera el tipo del repositorio
     * @return TipoRepositorio Tipo del repositorio (enum)
     */
    public function getTipo():TipoRepositorio{
        return $this->tipo;
    }
    /**
     * Recupera el ID del administrador del repositorio
     * @return int ID del administrador en repositorio en la base de datos
     */
    public function getIdAdmin():int{
        return $this->IdAdmin;
    }
    /**
     * Recupera información extra del repositorio (objeto o null si no tiene)
     * @return object|null el objeto extra del repositorio o null si no tiene.
     */
    public function getExtraObject(){
        if(is_null($this->extra)){
            return null;
        }else{
            return unserialize(base64_decode($this->extra));
        }        
    }
    /**
     * Recupera información extra del repositorio serializada.
     * @return string información extra del objeto serializada (base64)
     */
    public function getExtraSerialized(){
        if(is_null($this->extra)){
            return null;
        }else{
            return $this->extra;
        }        
    }

    /**
     * Actualiza el ID del repositorio
     * @param int $id
     */
    public function setID(int|null $id){
        $this->ID=$id;
    }
    /**
     * Actualiza el nombre del repositorio
     * @param string $nombre
     */
    public function setNombre(string $nombre){
        $this->nombre = $nombre;
    }
    /**
     * Actualiza la ruta del repositorio
     * @param string $ruta
     */
    public function setRuta(string $ruta){
        $this->ruta=$ruta;
    }
    /**
     * Actualiza el login de acceso al repositorio
     * @param string $login Login de acceso al repositorio
     */
    public function setLogin(string|null $login){
        $this->login = $login;
    }
    /**
     * Actualiza la contraseña de acceso al repositorio.
     * @param string $pass Contraseña de acceso al repositorio
     */
    public function setPass(string|null $pass){
        $this->pass=$pass;
    }
    /**
     * Actualiza el tipo de repositorio
     * @param TipoRepositorio Tipo del repositorio a almacenar datos.
     */
    public function setTipo(string | TipoRepositorio $tipo){
        if(is_string($tipo)){
            $this->tipo=TipoRepositorio::fromString($tipo);
        }elseif(get_class($tipo)=='docweb\models\TipoRepositorio'){
            $this->tipo=$tipo;
        }else{
            $this->tipo=null;
        }
    }
    /**
     * Actualiza el ID del administrador del repositorio
     * @param int $idAdmin ID del administrador del repositorios en la base de datos
     */
    public function setIdAdmin(int $idAdmin){
        $this->IdAdmin=$idAdmin;
    }
    /**
     * Actualiza información adicional de repositorio
     * @param mixed $extra Información adicional de repositorio.
     */
    public function setExtraObject(mixed $extra){
        if(is_null($extra)){
            $this->extra=null;    
        }else{
            $this->extra = base64_encode(serialize($extra));
        }
    }

    /**
     * Actualiza información adicional de repositorio, pasándola serializada (base64)
     * @param string $extra Información adicional de repositorio, serializada (base64)
     */
    public function setExtraSerialized(string $extra){
        $this->extra=$extra;
    }

    /**
     * Constructor
     * @param int|null                  $id             ID del repositorio
     * @param string                    $nombre         Nombre del repositorio 
     * @param string                    $ruta           Ruta de acceso
     * @param string|null               $login          Login empleado para acceder
     * @param string|null               $pass           Contraseña empleada para acceder
     * @param TipoRepositorio|string    $tipo           Tipo de repositorio
     * @param int                       $idAdmin        ID del administrador
     * @param mixed                     $extra =null    Información adicional.
     */
    public function __construct(int|null $id,string $nombre,string $ruta,string|null $login, string|null $pass, TipoRepositorio|string $tipo,int $idAdmin,mixed $extra=null){
        $this->setID($id);
        $this->setNombre($nombre);
        $this->setRuta($ruta);
        $this->setLogin($login);
        $this->setPass($pass);
        $this->setTipo($tipo);
        $this->setIdAdmin($idAdmin);
        $this->setExtraObject($extra);
    }

    /**
     * Se ejecuta al clonar el repositorio. Anula el ID anterior.
     */
    public function __clone(){
        //tras clonar el ID ya no vale
        $this->setID(null);
    }

    /**
     * Clase que devuelve un repositorio con datos por defecto para creación...
     * @return RepositorioData Nuevo repositorio con datos por defecto.
     */
    public static function getNewDefaultRepositorio(){
        return new RepositorioData(null,"Nuevo repositorio",".",null,null,TipoRepositorio::Ficheros,0);
    }
}