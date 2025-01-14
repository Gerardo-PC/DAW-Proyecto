<?php
namespace docweb\models;
require_once 'RepositorioData.php';

/**
 * Modelo de acceso a datos de Repositorios
 */
class Repositorio{
    private $DB;

    /**
     * Constructor, indicando Objeto de conexión a base de datso.
     * @param DB $DB - Objeto base de datos a conectar o null para que la cree automáticamente.
     */
    public function __construct($DB=null){
        if(is_null($DB)){
            //Si no se le pasa datos DB trata de recuperarlo de la configuración.
            $config = \Configuracion::getConfiguracion();           
            $DB = new \DB($config->db->usuario,$config->db->pass,$config->db->servidor,$basedatos=$config->db->basedatos);
        }
        $this->DB = $DB;
    }
    /**
     * Desconecta de la base de datos antes de destruir el objeto
     */
    public function __destruct(){
        if(!is_null($this->DB)){
            $this->DB->cerrarConexion();
        }
    }

    /**
     * Devuelve un array con todos los repositorios
     * @param int $idRepoAdmin Si se indica id de usuario repoadmin filtra por los repositorios de ese usuario.
     * @return array<RepositorioData> Array de repositorios recuperados o false si no recupera ninguno.
     */
    public function getRepositorios(int $idRepoAdmin=null){
        try{
            $sql=<<<SENTENCIASQL
            SELECT id,nombre,ruta,tipo,ID_Admin 
            FROM repositorios
            SENTENCIASQL;
            if(!empty($idRepoAdmin)){
                $sql = $sql." WHERE ID_Admin=?";
                $parametros=[$idRepoAdmin];
            }else{
                $parametros=[];
            }
            $resultado = $this->DB->consultaSqlParametros($sql,$parametros);

            $arrRepositorios = [];
            foreach($resultado as $v){
                $repositorio = new RepositorioData($v['id'],$v['nombre'],$v['ruta'],null, null, $v['tipo'],$v['ID_Admin']);
                array_push($arrRepositorios,$repositorio);
            }
            return $arrRepositorios;
        }catch(Exception $e){
            error_log("Error listando repositorios.",$e->getMessage());
        }
        return false;
    }

    /**
     * Crea un nuevo repositorio
     * @param RepositorioData $Repositorio el objeto con los datos del repositorio
     * @return int ID del repositorio creado en la BBDD.
     */
    public function addRepositorio(RepositorioData $Repositorio):int{
        $resultado = $this->DB->sentenciaSqlParametros("INSERT INTO  repositorios(nombre,ruta,login,pass,tipo,id_admin,extraData) VALUES (?,?,?,?,?,?,?)",[$Repositorio->getNombre(),$Repositorio->getRuta(), $Repositorio->getLogin(),$Repositorio->getPass(), $Repositorio->getTipo()->value,$Repositorio->getIdAdmin(),$Repositorio->getExtraSerialized()]);
        return $resultado;
    }

    /**
     * Recupera un repositorio con ID indicado.
     * @param int $id Identificador del repositorio
     * @return RepositorioData Datos de repositorio o null si no encontró.
     */
    public function getRepositorioId(int $id):RepositorioData|null{
        try{
            $resultado = $this->DB->consultaSqlParametros("SELECT id,nombre,ruta,login,pass,tipo,ID_Admin,extraData FROM repositorios WHERE id=?",[$id]);
            if(count($resultado)==1){//repositorio único
                $repositorio=new RepositorioData($resultado[0]['id'],$resultado[0]['nombre'],$resultado[0]['ruta'],$resultado[0]['login'],$resultado[0]['pass'],$resultado[0]['tipo'],$resultado[0]['ID_Admin']);
                if(!empty($resultado[0]['extraData'])){
                    $repositorio->setExtraSerialized($resultado[0]['extraData']);
                }
                return $repositorio;
            }
        }catch(Exception $e){
            error_log("Error recuperando información de repositorio.",$e->getMessage());
        }
        return null;
    }

    /**
     * Actualiza repositorio ID
     * @param RepositorioData Datos del repositorio
     * @return int ID del repositorio en BBDD
     */
    public function updateRepositorioID(RepositorioData $Repositorio){
        try{
            $resultado = $this->DB->sentenciaSqlParametros("UPDATE repositorios  SET nombre=?,ruta=?,login=?,pass=?, tipo=?,id_admin=?,extraData=? WHERE id=?",[ $Repositorio->getNombre(), $Repositorio->getRuta(),  $Repositorio->getLogin(), $Repositorio->getPass(), $Repositorio->getTipo()->value,  $Repositorio->getIdAdmin(),$Repositorio->getExtraSerialized(),$Repositorio->getID()]);
            return $resultado;
        }catch(Exception $e){
            error_log("Error actualizando repositorio.",$e->getMessage());
        }
        return false;
    }

    /**
     * Elimina el repositorio con el ID indicado
     * @param int $id ID del repositorio
     * @return bool false si existió algún error
     */
    public function deleteRepositorioID(int $id){
        try{
            $resultado = $this->DB->sentenciaSqlParametros("DELETE FROM repositorios WHERE id=?",[$id]);
            return $resultado;
        }catch(Exception $e){
            error_log("Error borrando repositorio.",$e->getMessage());
        }
        return false;
    }

    /**
     * Devuelve un adaptador para el repositorio con ID indicado
     * @param int $id ID del repositorio
     * @return IRepositorioAdapter - Adaptador para el repositorio con ID indicado o null si no es posible.
     */
    public function getRepositorioAdapter(int $id){
        try{
            $Repositorio = $this->getRepositorioId($id);
            $adaptador = null;
            switch($Repositorio->getTipo()){                
                case TipoRepositorio::Ficheros :
                    $adaptador = new \RepoAdapterLocal($id, $Repositorio->getRuta(),$Repositorio->getLogin(),$Repositorio->getPass());
                    break;
                case TipoRepositorio::Docuware :
                    $adaptador = new \RepoAdapterDW($id, $Repositorio->getRuta(),$Repositorio->getLogin(),$Repositorio->getPass(),$Repositorio->getExtraObject());
                    break;
                default:
                    //. . .
                    $adaptador = null; //no implementado todavía            
                break;
            }
            return $adaptador;
        }catch(Exception $e){
            echo "Error recuperando adaptador de repositorio.",$e->getMessage();
        }
        return null;
    }

}