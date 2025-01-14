<?php
/**
 * Interface que permite conectarse y gestionar repositorios
 * Implementar este interfaz permitiría conectarse a distintos tipos de repositorios.
 * Funciona como Adaptador de distintos tipos de repositorio para la aplicación.
 */
interface IRepositorioAdapter
{
    /**
     * Constructor de repositorio.
     * @param int       $idRepo     ID del repositorio
     * @param string    $direccion  Dirección / URL del repositorio
     * @param string    $login      Login empleado para acceder al repositorio (o null) 
     * @param string    $pass       Contraseña empleada para acceder al repositorio (o null)
     * @param mixed     $extra      Datos adicionales para acceso a repositorio (ID Biblioteca, etc.) (o null)
     */
    public function __construct(int $idRepo, string $direccion, string $login=null, string $pass=null, mixed $extra=null);
    /**
     * Destruye el objeto desconectando del repositorio si es necesario.
     */
    public function __destruct();
    /**
     * Conecta al repositorio
     * @param string    $direccion  Dirección / URL del repositorio
     * @param string    $login      Login empleado para acceder al repositorio (o null) 
     * @param string    $pass       Contraseña empleada para acceder al repositorio (o null)
     * @param mixed     $extra      Datos adicionales para acceso a repositorio (ID Biblioteca, etc.) (o null)
     */
    public function conecta(string $direccion, string $login=null, string $pass=null, mixed $extra=null):bool;
    /**
     * Desconecta del repositorio.
     */
    public function desconecta();
    /**
     * Valida si se está conectado al repositorio.
     * @return bool True si se está conectado al repositorio.
     */
    public function validaConexion():bool;
    /**
     * Recupera información posible del repositorio.
     * @return object|null|array (La información posible no está definida, distintos repositorios podrían devolver información diferente)
     */
    public function getInfo():object|null|array;
    /**
     * Devuelve los posibles campos de filtro del repositorio.
     * @return array Array de campos por los que es posible filtrar en el repositorio
     */
    public function getCampos():array;
    /**
     * Devuelve información de los documentos del repositorio que cumplen los campos de filtro.
     * @param   array   $camposFiltro Array asociativo con campos => valores para filtrar.
     * @return  array   Array de FileUserData con información de ficheros.
     */
    public function getDocuments(array $camposFiltro):array; //Array de FileUserData
    /**
     * Permite seleccionar un documento con ID indicado
     * @param   string $idDocumento El ID del documento a seleccionar
     */
    public function selectDocumentID(string $idDocumento):bool;
    /**
     * Devuelve información del documento indicado
     * @param   string  $idDocumento ID del documento a recuperar
     * @return  object|null Información del documento o null si no existe.
     */
    public function getDocumentInfo(string $idDocumento):object|null;
    /**
     * Devuelve el icono del documento o tipo de documento indicado 
     * @param   string  $idDocumento ID del documento a recuperar
     * @return  object  Icono del documento.
     */
    public function getDocumentIcon(string $idDocumento):object;
    /**
     * Recupera el documento indicado (Recupera del repositorio)
     * @param   string  $idDocumento ID del documento a recuperar
     * @return  mixed   El fichero con ID indicado.
     */
    public function getDocument(string $idDocumento):mixed;
}