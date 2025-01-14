<?php

/**
 * Clase para manejar datos de un fichero que puede ver el cliente (usuario)
 */
class FileUserData{
    private $nombreFichero;
    private $idFichero;
    private $idRepositorio;
    private $additionalInfo;
    

    /**
     * Devuelve el nombre del fichero.
     */
    public function getNombreFichero(){
        return $this->nombreFichero;
    }
    /**
     * Devuelve información adicional del fichero.
     */
    public function getAdditionalInfo(){
        return $this->additionalInfo;
    }
    /**
     * Devuelve el ID del fichero.
     */
    public function getIdFichero(){
        return $this->idFichero;
    }
    /**
     * Devuelve el ID del repositorio
     */
    public function getIdRepositorio(){
        return $this->idRepositorio;
    }

    /**
     * Clase que mantiene datos de fichero que puede ver el usuario.
     * @param string $nombreFichero Nombre reconocible del fichero.
     * @param string $idFichero ID del fichero en el repositorio.
     * @param int $idRepositorio ID del repositorio al que pertenece.
     * @param string $additionalInfo Información adicional del fichero.
     */
    public function __construct(string $nombreFichero,string $idFichero,int $idRepositorio, $additionalInfo=null){
        $this->nombreFichero=$nombreFichero;
        $this->idFichero=$idFichero;
        $this->idRepositorio=$idRepositorio;
        $this->additionalInfo=$additionalInfo;
    }

}