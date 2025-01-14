<?php
namespace docweb\models;
/**
 * Clase para mantener datos de Identificador
 */
final class IdentificadorData{

    private $ID;
    private $ID_Usuario;
    private $clave;
    private $valor;
    private $ID_Repositorio;
    private $nombreRepositorio;

    /**
     * Devuelve el ID del Identificador
     * @return int ID del Identificador
     */
    public function getID():int{
        return $this->ID;
    }
    /**
     * Devuelve ID Usuario
     * @return int ID Usuario
     */
    public function getIdUsuario():int{
        return $this->ID_Usuario;
    }
    /**
     * Devuelve la clave del identificador (nombre del campo)
     * @return string Nombre de la clave (campo)
     */
    public function getClave():string{
        return $this->clave;
    }
    /**
     * Devuelve el valor necesario para ese identificador
     * @return string Valor necesario que cumpla el campo.
     */
    public function getValor():string{
        return $this->valor;
    }
    /**
     * Devuelve el ID del repositorio
     * @return int ID del repositorio
     */
    public function getIdRepositorio():int{
        return $this->ID_Repositorio;
    }
    /**
     * Devuelve el nombre del repositorio
     * @return string Nombre del repositorio
     */
    public function getNombreRepositorio():string{
        return $this->nombreRepositorio;
    }

    /**
     * Actualiza el ID del identificador.
     * @param int|null $ID nuevo ID del identificador.
     */
    public function setID(int|null $ID){
        $this->ID = $ID;
    }
    /**
     * Actualiza el ID del usuario
     * @param int $IdUsuario nuevo ID del usuario
     */
    public function setIdUsuario(int $IdUsuario){
        $this->ID_Usuario=$IdUsuario;
    }
    /**
     * Actualiza la clave (campo)
     * @param string $clave Nueva Clave (nombre de campo)
     */
    public function setClave(string $clave){
        $this->clave=$clave;
    }
    /**
     * Actualiza el valor del identificador
     * @param string $valor Nuevo valor que debe cumplir el identificador.
     */
    public function setValor(string $valor){
        $this->valor=$valor;
    }
    /**
     * Actualiza el ID del repositorio.
     * @param int $IdRepositorio Nuevo ID del repositorio
     */
    public function setIdRepositorio(int $IdRepositorio){
        $this->ID_Repositorio = $IdRepositorio;
    }
    /**
     * Actualiza el nombre del repositorio
     * @param string $nombreRepositorio
     */
    public function setNombreRepositorio($nombreRepositorio){
        $this->nombreRepositorio=$nombreRepositorio;
    }

    /**
     * Constructor
     * @param int       $ID                 ID del identificador.
     * @param int       $ID_Usuario         ID del usuario
     * @param string    $clave              Nombre de la clave (campo)
     * @param string    $valor              Valor de la clave
     * @param int       $ID_Repositorio     ID del repositorio
     * @param string    $nombreRepositorio  ='' Nombre del repositorio o blanco ''  por defecto.
     */
    public function __construct($ID,$ID_Usuario,$clave,$valor,$ID_Repositorio,$nombreRepositorio=''){
        $this->setID($ID);
        $this->setIdUsuario($ID_Usuario);
        $this->setClave($clave);
        $this->setValor($valor);
        $this->setIdRepositorio($ID_Repositorio);
        $this->setNombreRepositorio($nombreRepositorio);
    }

    /**
     * Clona el identificador.
     */
    public function __clone(){
        //tras clonar el ID ya no vale
        $this->setID(null);
    }
}