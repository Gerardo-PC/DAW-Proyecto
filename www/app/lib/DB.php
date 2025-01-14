<?php
/**
 * Clase que gestiona conexión directa a base de datos
 * Permite conectarse a base de datos webdoc.
 * Si no existe trata de crearla.
 * Emplea PDO + mysql.
 */
class DB{
    //objeto de conexión
    private $con = null; 

    /**
     * Devuelve el objeto de conexión a la base de datos
     * (null si no existe)
     */
    public function getConexion():object{
        return $this->con;
    }

    /**
     * Crea una conexión a base de datos para usar posteriormente
     * @param usr Usuario para conectarse
     * @param pwd Contraseña de la BBDD
     * @param srv Servidor al que se quiere conectar
     * @param bbdd Base de datos a emplear (puede omitirse)
     * @return devuelve el objeto de conexión a BBDD. null si no logra crear el objeto.
     */
    function crearConexion($usr,$pwd,$srv,$bbdd=null):object|null{
        $this->con = null;
        $srv = str_replace('localhost',$_SERVER['SERVER_ADDR'],$srv);
        try{
            $this->con= new PDO("mysql:host=$srv",$usr,$pwd);
            if(!is_null($bbdd)){
                $this->seleccionarBBDD($bbdd);
            }
            $this->con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);            
        }catch(PDOException $e){
            error_log("Error de PDO creando conexión BBDD (Usuario: $usr en Servidor: $srv)");
            error_log($e->getMessage());
        }catch(Exception $e){
            error_log("Error general creando conexión BBDD (Usuario: $usr en Servidor: $srv)");
            error_log($e->getMessage());
        }
        return $this->con;
    }
    
    /**
     * Elimina la conexión actual
     */
    public function cerrarConexion(){
        $this->con=null;
    }

    /**
     * Devuelve true si el objeto está conectado
     * False en caso contrario
     * @return bool true si está conectado (creado objeto de tipo PDO, no nulo y existe base de datso webdoc.)
     */
    public function isConnected():bool{
        return (!is_null($this->con) && get_class($this->con)==="PDO" && $this->existeDb('webdoc'));
    }

    /**
     * Devuelve True si la base de datos existe, false en caso contrario.
     * Si no existe la trata de inicializar a partir de un fichero con el mismo nombre en app/config/[base datos]_schema.sql
     * @param string $db nombre de la base de datos (ej. 'webdoc')
     * @param bool $crearSiNoExiste=true Indica opcionalmente si debe intentar crear la BBDD si no existe.
     * @return bool true si la base de datos existe y false en caso contrario.
     */
    function existeDB(string $db,bool $crearSiNoExiste=true):bool{
        if(empty($this->con)){
            $this->con=$this->crearConexion();
        }
        try{
            $stm=$this->con->prepare("SHOW DATABASES LIKE '$db'");
            $stm->setFetchMode(PDO::FETCH_ASSOC);
            $stm->execute();
            $resultado = $stm->fetchAll();
            if(count($resultado)==1){
                //La base de datos existe.
                $stm=$this->con->prepare("SHOW TABLES FROM $db");
                $stm->setFetchMode(PDO::FETCH_ASSOC);
                $stm->execute();
                $resultado = $stm->fetchAll();
                if(count($resultado)>3){    //Mejorable comprobación tabla a tabla pero aceptamos que existe la estructura si tiene, al menos, 4 tablas
                    return true;
                }elseif($crearSiNoExiste){
                    $this->creaDBFromScript($db);
                    return($this->existeDB($db,false));
                }
            }elseif($crearSiNoExiste){
                $this->creaDBFromScript($db);
                return($this->existeDB($db,false));
            }
        }catch(PDOException $e){
            error_log("Error PDO existeDB $db");
            error_log($e->getMessage());
        }catch(Exception $e){
            error_log("Error comprobando existeDB $db");
            error_log($e->getMessage());
        }
        return false;
    }

    /**
     * Lanza el script de creación de la base de datos.
     * @param string $db nombre de la base de datos (ej. 'webdoc')
     * @return true si consigue crearla correctamente, en caso contrario interrumpe ejecución...
     */
    private function creaDBFromScript($db):bool{
        if(file_exists("../app/config/".$db."_schema.sql")){
            $this->ejecutaScript("../app/config/".$db."_schema.sql");
            return true;
        }else{
            die("Imposible acceder a base datos $db o crearla desde ../app/config/".$db."_schema.sql");
        }
        return false;
    }

    /**
     * Selecciona la base de datos
     * @param bbdd nombre de la base de datos a conectar
     * @return bool true si logra conectarse, false en caso contrario.
     */
    public function seleccionarBBDD($bbdd):bool{
        if(empty($this->con)){
            $con=crearConexion();
        }
        try{
            if($this->existeDB($bbdd)){
                $this->ejecutarSql("USE $bbdd");
                $resultado = $this->consultaSql("SELECT DATABASE()");
                if(!empty($resultado) && $resultado[0]["DATABASE()"]==$bbdd){
                    return true;
                }
            }
        }catch(PDOException $e){
            error_log("Error seleccionando BBDD ($bbdd)");
            error_log($e->getMessage());
        }
        return false;
    }

    /**
     * Ejecuta la sentencia sql indicaa (envoltorio para sentenciaSqlParametros sin parámetros.)
     * @deprecated Eliminar para sustitución por sentenciaSqlParametros. Problemas conocidos en llamada.
     * @param sql sentencia a ejecutar
     * @return int último ID insertado, si procede. 0 en caso contrario. <- PROBLEMA EN TEST!!!
     */
    public function ejecutarSql($sql): int{
        $id=$this->sentenciaSqlParametros($sql,[]);
        return $id;
    }

    
    /**
     * Ejecuta sentencia con parámetros
     * @param sql sentencia SQL
     * @param arrParams Array de parámetros
     * @return int último ID insertado si procede o 0 en caso contrario.
     */
    public function sentenciaSqlParametros($sql,$arrParams=[]): int{
        if(empty($arrParams)){
            $this->consultaSql($sql);
        }else{
            if(empty($this->con)){
                $this->con=$this->crearConexion();
            }
            try{
                $stm=$this->con->prepare($sql);
                for($i=0;$i<count($arrParams);$i++){
                    $stm->bindParam($i+1,$arrParams[$i]);
                }
                $stm->setFetchMode(PDO::FETCH_ASSOC);
                $stm->execute();
                return $this->con->lastInsertId();
            }catch(PDOException $e){
                error_log("Error ejecutando consulta con parámetros $sql");
                error_log(print_r($e),true);
                return 0;
            }
        }
        return 0;
    }

    /**
     * alias simplificado de consultaSqlParametros
     * @deprecated Eliminar para sustitución por consultaSqlParametros. Problemas conocidos en llamada.
     * @param sql sentencia SQL
     * @return array de la consulta (array asociativo) o null si hay error.
     */
    public function consultaSql($sql):array|null{
        $id = $this->consultaSqlParametros($sql,[]);
        return $id;
    }

    /**
     * Lanza la consulta con parámetros.
     * @param sql consulta a realizar
     * @param arrParams array con parametros de la consulta (o vacío =[] por defecto si no tiene.)
     * @return array de la consulta (array asociativo) o null si hay error.
     */
    public function consultaSqlParametros($sql,$arrParams=[]):array|null{
        if(empty($this->con)){
            $this->con=$this->crearConexion();
        }
        try{
            $stm=$this->con->prepare($sql);
            for($i=0;$i<count($arrParams);$i++){
                $stm->bindParam($i+1,$arrParams[$i]);
            }
            $stm->setFetchMode(PDO::FETCH_ASSOC);
            $stm->execute();
            return $stm->fetchAll();
        }catch(PDOException $e){
            error_log("Error consulta con parámetros ($sql)".print_r($arrParams,true));
            error_log($e->getMessage());
        }
        return null;
    }


    /**
     * Ejecuta el script SQL del fichero indicado.
     * @param ficheroScript Ruta del fichero para ejecutar.
     * @return bool true si lanzó la ejecución (con éxito o no), false si no existe el fichero
     */
    public function ejecutaScript(string $ficheroScript):bool{
        if(file_exists($ficheroScript)){
            try{
                $scriptCreacion = file_get_contents($ficheroScript);
                $this->consultaSql($scriptCreacion);        
            }catch(Exception $e){
                error_log($e);
            }
        }else{
            //throw new Exception("El fichero de Script $ficheroScript no existe.");
            error_log("El fichero de Script $ficheroScript no existe.");
            return false;
        }
        return true;
    }

    /**
     * Constructor, es posible pasarle parámetros de la base de datos
     * en caso contrario trata de buscarlos en configuración.
     * @param string $usr Usuario de la base de datos
     * @param string $pwd Contraseña de la base de datos
     * @param string $srv Dirección del servidor de la base de datos (IP:Puerto Ej. 192.168.100.101:3306)
     * @param string $db Base de datos que se emplea del servidor (webdoc)
     */
    function __construct(string $usr=null,string $pwd=null,string $srv=null,string $db=null){
        if(!is_null($usr) && !is_null($pwd) && !is_null($srv)){
            $this->crearConexion($usr,$pwd,$srv,$db);
        }else{
            $config = Configuracion::getConfiguracion();
            $usr= $config->db->usuario;
            $pwd=$config->db->pass;
            $srv=$config->db->servidor;
            $db=$config->db->basedatos;
            $this->crearConexion($usr,$pwd,$srv,$db);
        }     
    }

    /**
     * Cierra la conexión al eliminar el objeto.
     */
    function __destruct(){
        $this->cerrarConexion();
    }


}