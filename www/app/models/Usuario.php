<?php
namespace docweb\models;

/**
 * Modelo de acceso a datos Usuario
 */
class Usuario{
    private $DB;

    /**
     * Devuelve el objeto de acceso a la base de datos.
     * @return DB Objeto de acceso a la base de datos
     */
    private function getDB():DB{
        return $this->DB;
    }

    /**
     * Constructor
     * @param DB $DB Objeto conexión a la base de datos para trabajar. si no se le pasa trata de recuperarlo del fichero de configuración.
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
     * Cierra conexión al base de datos antes de destruir el objeto.
     */
    public function __destruct(){
        if(!is_null($this->DB)){
            $this->DB->cerrarConexion();
        }
    }

    /**
     * Devuelve true si la tabla de usuarios no está vacia (existe al menos un usuario)
     * @return bool True si al menos existe un usuario en la base de datos.
     */
    public function existenUsuarios():bool{
        try{
            $resultado = $this->DB->consultaSql("SELECT count(*) AS numeroUsuarios FROM usuarios");
            if(!empty($resultado) && $resultado[0]['numeroUsuarios']>0){
                return true;
            }
        }catch(Exception $e){
            echo "Error comprobando usuarios.",$e->getMessage();
        }
        return false;
    }

    /**
     * Devuelve true si existe el usuario con login indicado.
     * @param string $login login del usuario
     * @return true si existe un usuario con login indicado
     */
    public function existeUsuarioLogin(string $login):bool{
        try{
            $resultado = $this->DB->consultaSqlParametros("SELECT count(*) AS existe FROM usuarios WHERE login=?",[$login]);
            if(!empty($resultado) && $resultado[0]['existe']==1){
                return true;
            }
        }catch(Exception $e){
            error_log("Error comprobando usuarios.",$e->getMessage());
        }
        return false;
    }

    /**
     * Devuelve true si existe el usuario con ID indicado
     * @param int $id El id del usuario a buscar
     * @return true si existe un usuario con id indicado
     */
    public function existeUsuarioId(int $id):bool{
        try{
            $resultado = $this->DB->consultaSqlParametros("SELECT count(*) AS existe FROM usuarios WHERE ID=?",[$id]);
            if($resultado[0]['existe']==1){
                return true;
            }
        }catch(Exception $e){
            echo "Error comprobando usuarios.",$e->getMessage();
        }
        return false;
    }

    /**
     * Devuelve true si el usuario y contraseña están dados de alta
     * @param   string  $login  El login del usuario
     * @param   string  $pass   La contraseña del usuario (original, sin encriptar)
     * @return  bool    true si el usuario existe (solo 1 usuario) y la contraseña es correcta
     */
    public final function checkLoginUsuario(string $login,string $pass){
        try{
            $resultado = $this->DB->consultaSqlParametros("SELECT pass FROM usuarios WHERE login=?",[$login]);
            if(!empty($resultado) && count($resultado)==1){//solo puede haber un usuario
                if(password_verify($pass,$resultado[0]['pass'])){
                    return true;
                }
            }
        }catch(Exception $e){
            echo "Error comprobando usuarios.",$e->getMessage();
        }
        return false;
    }

    /**
     * Añade el usuario con los datos indicados
     * @param UsuarioData $usuario Objeto con los datos del usuario
     * @return int ID del usuario en la base de datos
     */
    public function addUsuario(UsuarioData $usuario):int{
        $login = $usuario->getLogin();
        $pass=$usuario->getPass();
        $rol=$usuario->getRol();
        $nombre = $usuario->getNombre();
        $email = $usuario->getEmail();
        $resultado = $this->DB->sentenciaSqlParametros("INSERT INTO  usuarios(login,pass,rol,nombre,email) VALUES (?,?,?,?,?)",[$login,$pass,$rol,$nombre,$email]);
        return $resultado;
    }

    /**
     * Actualiza datos de usuario con ID indicado
     * @param UsuarioData $Usuario Objeto con datos de usuario.
     * @return bool True si se actualiza correctamente.
     */
    public function updateUsuarioID(UsuarioData $Usuario):bool {
        try{
            $resultado = $this->DB->sentenciaSqlParametros("UPDATE usuarios SET nombre=?,login=?,email=?,rol=? WHERE id=?",[$Usuario->getNombre(),$Usuario->getLogin(),$Usuario->getEmail(), $Usuario->getRol(),$Usuario->getId()]);
            return $resultado;
        }catch(Exception $e){
            echo "Error actualizando usuario.",$e->getMessage();
        }
        return false;
    }

    /**
     * Devuelve el usuario con Login indicado
     * @param string login Login del usuario a buscar
     * @return UsuarioData datos del usuario recuperado.false si no encontrado
     */
    public function getUsuarioLogin(string $login):UsuarioData|bool{
        try{
            $resultado = $this->DB->consultaSqlParametros("SELECT id,nombre,login,email,rol FROM usuarios WHERE login=?",[$login]);
            if(count($resultado)==1){//usuario único
                $Usuario = new UsuarioData($resultado[0]['id'],$resultado[0]['nombre'],$resultado[0]['login'],null,$resultado[0]['email'],$resultado[0]['rol']);
                return $Usuario;
            }
        }catch(Exception $e){
            echo "Error comprobando usuarios.",$e->getMessage();
        }
        return false;
    }

    /**
     * Devuelve información del usuario con ID indicado
     * @param int $id ID del usuario a recuperar información
     * @return UsuarioData datos del usuario recuperado.
     */
    public function getUsuarioId(int $idUsuario):UsuarioData{
        try{
            $resultado = $this->DB->consultaSqlParametros("SELECT id,nombre,login,email,rol FROM usuarios WHERE id=?",[$idUsuario]);
            if(count($resultado)==1){//usuario único
                $Usuario = new UsuarioData($resultado[0]['id'],$resultado[0]['nombre'],$resultado[0]['login'],null,$resultado[0]['email'],$resultado[0]['rol']);
                return $Usuario;
            }
        }catch(Exception $e){
            echo "Error comprobando usuarios.",$e->getMessage();
        }
        return false;
    }

    /**
     * Devuelve un array con todos los usuarios
     * @return array[UsuarioData] Array con los usuarios (UsuarioData). False en caso de no encontrar.
     */
    public function getUsuarios(){
        try{
            $resultado = $this->DB->consultaSql("SELECT id,nombre,login,email,rol  FROM usuarios");
            $ArrUsuarios=[];
            foreach($resultado as $v){
                array_push($ArrUsuarios,new UsuarioData($v['id'],$v['nombre'],$v['login'],null,$v['email'],$v['rol']));
            }
            return $ArrUsuarios;
        }catch(Exception $e){
            echo "Error listando usuarios.",$e->getMessage();
        }
        return false;
    }

    /**
     * Devuelve un array con todos los usuarios con un rol específico
     * @param string $rol Rol a buscar (admin, repoadmin, user)
     * @return array[UsuarioData] Array de usuarios que tienen el rol indicado.
     */
    public function getUsuariosRol(string $rol){
        try{
            $resultado = $this->DB->consultaSqlParametros("SELECT id,nombre,login,email,rol  FROM usuarios WHERE rol=?",[$rol]);
            $ArrUsuarios=[];
            foreach($resultado as $v){
                array_push($ArrUsuarios,new UsuarioData($v['id'],$v['nombre'],$v['login'],null,$v['email'],$v['rol']));
            }
            return $ArrUsuarios;
        }catch(Exception $e){
            echo "Error listando usuarios con rol $rol.",$e->getMessage();
        }
        return false;
    }

    /**
     * Devuelve un array con todos los usuarios con identificadores en un repositorio específico
     * @param int $idRepositorio id del repositorio a buscar
     * @return array[UsuarioData] con los usuarios posibles del repositorio.
     */
    public function getUsuariosRepo(int $idRepositorio){
        try{
            $resultado = $this->DB->consultaSqlParametros("SELECT DISTINCT usuarios.id,usuarios.nombre,usuarios.login,usuarios.email,usuarios.rol FROM usuarios INNER JOIN identificadores ON usuarios.ID = identificadores.ID_Usuario WHERE identificadores.ID_Repositorio=?",[$idRepositorio]);
            $ArrUsuarios=[];
            foreach($resultado as $v){
                array_push($ArrUsuarios,new UsuarioData($v['id'],$v['nombre'],$v['login'],null,$v['email'],$v['rol']));
            }
            return $ArrUsuarios;
        }catch(Exception $e){
            echo "Error listando usuarios con rol $rol.",$e->getMessage();
        }
        return false;
    }

    /**
     * Devuelve una lista de identificadores de repositorios donde el usuario tiene acceso
     * @param int $idUsuario ID del usuario
     * @return array con los ID's de los repositorios donde el usuario tiene acceso.
     */
    public function getReposUserId(int $idUsuario):array{
        $repos=[];
        try{
            $repos = $this->DB->consultaSqlParametros("SELECT DISTINCT ID_Repositorio FROM identificadores WHERE ID_Usuario=?",[$idUsuario]);
            $arrRepos = [];
            foreach($repos as $v){
                array_push($arrRepos,$v['ID_Repositorio']);
            }
            return $arrRepos;
        }catch(Exception $e){
            echo "Error recuperando repositorios de usuario $idUsuario.",$e->getMessage();
        }
        return null;
    }

    /**
     * Actualiza contraseña de usuario con ID indicado
     * @param int $id ID del usuario a actualizar en la base de datos
     * @param string $pass Contraseña nueva del usuario.
     */
    public function updatePassUsuarioID(int $id,string $pass){
        try{
            $resultado = $this->DB->sentenciaSqlParametros("UPDATE usuarios SET pass=? WHERE id=?",[$pass, $id]);
            return $resultado;
        }catch(Exception $e){
            echo "Error actualizando contraseña usuario.",$e->getMessage();
        }
        return false;
    }

    /**
     * borra el usuario con ID pasado
     * @param int $id ID del usuario a borrar
     */
    public function deleteUsuarioID(int $id){
        try{
            $resultado = $this->DB->sentenciaSqlParametros("DELETE FROM usuarios WHERE id=?",[$id]);
            return $resultado;
        }catch(Exception $e){
            echo "Error borrando usuario.",$e->getMessage();
        }
        return false;
    }

    /**
     * Genera un código de recuperación de contraseña, para envío por mail.
     * Elimina cualquier código generado anterior.
     * @param int $id ID del usuario a recuperar contraseña
     * @return string Código generado.
     */
    public function generarCodigoRecuperaPass(int $id):string{
        try{
            $this->DB->sentenciaSqlParametros("DELETE FROM codigosRecuperaPass WHERE usuario_ID=?",[$id]);
            $codigo = hash('sha256',uniqid('',true));
            $resultado = $this->DB->sentenciaSqlParametros("INSERT INTO codigosRecuperaPass(usuario_ID,codigoRecuperacion) VALUES(?,?)",[$id,$codigo]);
            return $codigo;
        }catch(Exception $e){
            echo "Error generando código recuperación contraseña.",$e->getMessage();
        }
        return false;
    }

    /**
     * Recupera el usuario correspondiente al código de recuperación enviado o null en caso contrario
     * @param string $codigo código de recuperación de usuario.
     * @return UsuarioData|null Datos del usuario recuperado o null
     */
    public function getUsuarioCodigoRecuperaPass(string $codigo):UsuarioData|null{
        try{
            $resultadoCodigo = $this->DB->consultaSqlParametros("SELECT usuario_ID FROM codigosRecuperaPass WHERE codigoRecuperacion=?",[$codigo]);
            if(count($resultadoCodigo)==1 && !is_null($resultadoCodigo[0]['usuario_ID'])){
                return $this->getUsuarioId($resultadoCodigo[0]['usuario_ID']);
            }
        }catch(Exception $e){
            echo "Error recuperando usuario desde código recuperación.",$e->getMessage();
        }
        return null;
    }
    
}
