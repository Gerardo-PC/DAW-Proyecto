<?php
namespace docweb\models;
/**
 * Enumerador de tipos de repositorio
 */
enum TipoRepositorio:string{
    case Ficheros="ficheros";
    case Docuware="docuware";

    /**
     * devuelve el tipo de repositorio desde una cadena pasada
     * @param string $strRepositorio Nombre de repositorio en cadena
     * @return TipoRepositorio|null Tipo de repositorio o null si la cadena no corresponde a ninguno.
     */
    public static function fromString($strRepositorio):TipoRepositorio|null{
        return match($strRepositorio){
            'ficheros'=> TipoRepositorio::Ficheros,            
            'docuware'=>TipoRepositorio::Docuware,
            default=>null,
        };
    }
}