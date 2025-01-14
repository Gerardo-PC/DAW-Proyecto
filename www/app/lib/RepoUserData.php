<?php
/**
 * Clase para manejar datos de repositorio que puede visualizar un usuario. Gestiona nombre de repositorio y ficheros asociados.
 */
class RepoUserData{
    private string $nombre;

    private array $ficheros; //Array de FileUserData que puede manejar el usuario

    /**
     * Recupera el nombre del fichero
     * @return string Nombre del fichero.
     */
    public function getNombre():string{
        return $this->nombre;
    }

    /**
     * Actualiza el nombre del fichero
     * @param string $nombre Nombre del fichero.
     */
    public function setNombre(string $nombre){
        $this->nombre = $nombre;
    }

    /**
     * Recupera los ficheros
     * @return array<FileUserData> Array con información de ficheros del repositorio
     */
    public function getFicheros():array{
        return $this->ficheros;
    }

    /**
     * Actualiza el listado de información de ficheros que contiene el repositorio
     * @param array<FileUserData> $ficheros Array de datos de ficheros que contiene el repositorio.
     */
    public function setFicheros(array $ficheros){
        $this->ficheros = $ficheros;
    }

    /**
     * Constructor
     * @param string $nombre Nombre del repositorio
     * @param array<FileUserData> $ficheros Array con información de ficheros del repositorio.
     */
    public function __construct(string $nombre, array $ficheros){
        $this->setNombre($nombre);
        $this->setFicheros($ficheros);
    }

    /**
     * Destruye el objeto.
     */
    public function __destruct(){
        //. . . no es necesario hacer nada.
    }
}