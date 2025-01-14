<?php
namespace docweb\models;

require_once('IdentificadorData.php');

/**
 * Modelo de acceso a datos Identificadores de Usuario
 */
class Identificador{
    private $DB;
    
    /**
     * Devuelve la base de datos.
     * @return DB Objeto Base de datos al que está conectado.
     */
    private function getDB():DB{
        return $this->DB;
    }

    /**
     * Constructor
     * @param DB $DB Objeto de conexión a la base de datos para trabajar. si no se le pasa trata de recuperarlo del fichero de configuración.
     */
    public function __construct($DB=null){
        if(is_null($DB)){
            //Si no se le pasa controlador trata de recuperarlo de la configuración.
            $config = \Configuracion::getConfiguracion();           
            $DB = new \DB($config->db->usuario,$config->db->pass,$config->db->servidor,$basedatos=$config->db->basedatos);
        }
        $this->DB = $DB;
    }

    /**
     * Cierra la conexión a base datos y destruye el objeto.
     */
    public function __destruct(){
        if(!is_null($this->DB)){
            $this->DB->cerrarConexion();
        }
    }

    /**
     * Devuelve un array con todos los identificadores de un usuario en un repositorio específico
     * @param int $idUsuario id del usuario a buscar
     * @param int $idRepositorio id del repositorio a buscar
     * @return array<IdentificadorData> Array de identificadores encontrados
     */
    public function getIdentificadoresUsuariosRepo(int $idUsuario, int $idRepositorio=null):Array{
        try{
            $sql=<<<SENTENCIASQL
                SELECT identificadores.ID, identificadores.ID_Usuario,identificadores.clave,identificadores.valor,identificadores.ID_Repositorio, repositorios.nombre
                FROM identificadores JOIN repositorios ON identificadores.ID_Repositorio=repositorios.ID
                WHERE ID_Usuario = ?
            SENTENCIASQL;
            $parametros = [$idUsuario];
            if(!empty($idRepositorio)){
                $sql=$sql." AND ID_Repositorio=?";
                $parametros = [$idUsuario,$idRepositorio];
            }
            $resultado = $this->DB->consultaSqlParametros($sql,$parametros);
            $ArrIdentificadores = [];
            foreach($resultado as $v){
                $Identificador = new IdentificadorData($v['ID'],$v['ID_Usuario'],$v['clave'],$v['valor'],$v['ID_Repositorio'],$v['nombre']);
                array_push($ArrIdentificadores,$Identificador);
            }
            return $ArrIdentificadores;
        }catch(Exception $e){
            echo "Error listando identificadores usuario $idUsuario en repositorio $idRepositorio.",$e->getMessage();
        }
        return false;
    }

    /**
     * Añade un identificador
     * @param IdentificadorData $identificador Identificador a añadir
     * @return int ID del identificador insertado
     */
    public function addIdentificador(IdentificadorData $Identificador):int{
        $idUsuario = $Identificador->getIdUsuario();
        $clave = $Identificador->getClave();
        $valor = $Identificador->getValor();
        $idRepositorio = $Identificador->getIdRepositorio();
        $resultado = $this->DB->sentenciaSqlParametros("INSERT INTO  identificadores(ID_Usuario,clave,valor,ID_Repositorio) VALUES (?,?,?,?)",[$idUsuario,$clave,$valor,$idRepositorio]);
        return $resultado;
    }

    /**
     * Devuelve los datos de un identificador indicado, null si no existe.
     * @param int $idIdentificador Id del identificador en la BBDD
     * @return IdentificadorData el objeto con datos del identificador
     */
    public function getIdentificadorId(int $idIdentificador):IdentificadorData|null{
        try{
            $resultado = $this->DB->consultaSqlParametros("SELECT ID, ID_Usuario,clave,valor,ID_Repositorio FROM identificadores WHERE ID=?",[$idIdentificador]);            
            if(!empty($resultado)){
                $Identificador = new IdentificadorData($resultado[0]['ID'],$resultado[0]['ID_Usuario'],$resultado[0]['clave'],$resultado[0]['valor'],$resultado[0]['ID_Repositorio']);
                return $Identificador;
            }
        }catch(Exception $e){
            echo "Error recuperando identificador $idIdentificador.",$e->getMessage();
        }
        return null;
    }

    /**
     * Actualiza un identificador indicado.
     * @param IdentificadorData $IdentificadorData Datos del identificador a actualizar
     * @return int id del registro actualizado.
     */
    public function updateIdentificadorId($IdentificadorData):int{
        try{
            $sql=<<<SENTENCIASQL
                UPDATE identificadores
                SET ID_Usuario=?, clave=?,valor=?,ID_Repositorio=?
                WHERE ID = ?
            SENTENCIASQL;
            $resultado = $this->DB->sentenciaSqlParametros($sql,[$IdentificadorData->getIdUsuario(),$IdentificadorData->getClave(),$IdentificadorData->getValor(),$IdentificadorData->getIdRepositorio(),$IdentificadorData->getID()]);
            return $resultado;
        }catch(Exception $e){
            echo "Error actualizando identificador $idIdentificador.",$e->getMessage();
        }
        return null;
    }

    /**
     * Borra el identificador con el ID indicado.
     * @param int $idIdentificador Id del identificador en la BBDD
     */
    public function deleteIdentificadorID(int $idIdentificador){
        try{
            $resultado = $this->DB->consultaSqlParametros("DELETE FROM identificadores WHERE ID=?",[$idIdentificador]);            
            return $resultado;
        }catch(Exception $e){
            echo "Error recuperando identificador $idIdentificador.",$e->getMessage();
        }
        return null;
    }

}