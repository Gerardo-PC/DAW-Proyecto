<?php
namespace docweb\models;
/**
 * Clase para encapsular datos de Usuario
 */
final class UsuarioData{
    private $ID;
    private $nombre;
    private $login;
    private $pass;
    private $email;
    private $rol;

    /**
     * Recupera ID de usuario
     * @return int ID del usuario
     */
    public function getID():int{
        return $this->ID;
    }
    /**
     * Recupera el nombre del usuario
     * @return string Nombre del usuario
     */
    public function getNombre():string{
        return $this->nombre;
    }
    /**
     * Recupera el Login del usuario
     * @return string Login del usuario
     */
    public function getLogin():string {
        return $this->login;
    }
    /**
     * Recupera el Pass del usuario
     * @return string Contraseña del usuario
     */
    public function getPass(){
        return $this->pass;
    }
    /**
     * Recupera el e-mail del usuario
     * @return string Mail del usuario
     */
    public function getEmail():string{
        return $this->email;
    }
    /**
     * Recupera el Rol del usuario
     * @return string Rol del usuario
     */
    public function getRol():string{
        return $this->rol;
    }

    /**
     * Indica si el usuario es Admin
     * @return bool True si el usuario es Administrador
     */
    public function isAdmin():bool{
        return $this->rol==='admin';
    }
    /**
     * Indica si el usuario es Administrador de repositorios
     * @return bool True si el usuario es Administrador de repositorios
     */
    public function isRepoAdmin():bool{
        return $this->rol==='repoadmin';
    }
    /**
     * Indica si el usuario tiene rol de Usuario
     * @return bool True si el usuario tiene rol de Usuario
     */
    public function isUser():bool{
        return $this->rol==='user';
    }

    /**
     * Actualiza el ID del usuario
     * @param int|null $id Nuevo ID del usuario
     */
    public function setID(int|null $id){
        $this->ID = $id;
    }
    /**
     * Actualiza el nombre del usuario
     * @param string $nombre Nuevo nombre del usuario
     */
    public function setNombre(string $nombre){
        $this->nombre = $nombre;
    }
    /**
     * Actualiza el Login del usuario
     * @param string $login Nuevo login del usuario
     */
    public function setLogin(string $login){
        $this->login = $login;
        //Si el nombre está vacío pone el login como nombre.
        if(empty($this->nombre)){
            $this->nombre = $login;
        }
    }
    /**
     * Actualiza la Contraseña del usuario
     * @param string|null $pass Nueva contraseña del usuario
     */
    public function setPass(string|null $pass){
        if(is_null($pass)){
            $this->pass=null;
        }else{
            $this->pass = password_hash($pass,PASSWORD_BCRYPT);
        }
    }
    /**
     * Actualiza el mail del usuario
     * @param string $email Nuevo email del usuario.
     */
    public function setEmail($email){
        $this->email=$email;
    }
    /**
     * Actualiza el rol del usuario.
     * @param string $rol Nuevo rol del usuario ("admin","repoadmin","user")
     */
    public function setRol($rol){
        $this->rol=$rol;
    }

    /**
     * Constructor
     * @param int       $id     ID del usuario
     * @param string    $nombre Nombre asignado
     * @param string    $login  Login asignado
     * @param string    $pass   Contraseña
     * @param string    $email  Mail del usuario
     * @param string    $rol    Rol que dispone
     */
    public function __construct($id,$nombre,$login,$pass,$email,$rol){
        $this->setID($id);
        $this->setNombre($nombre);
        $this->setLogin($login);
        $this->setPass($pass);
        $this->setEmail($email);
        $this->setRol($rol);
    }

}