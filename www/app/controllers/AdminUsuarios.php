<?php
require_once '../app/models/UsuarioData.php';
require_once '../app/models/IdentificadorData.php';
require_once '../app/models/RepositorioData.php';

use \docweb\models\UsuarioData as UsuarioData;
use \docweb\models\IdentificadorData as IdentificadorData;
use \docweb\models\RepositorioData as RepositorioData;

/**
 * Controlador para gestionar usuarios.
 */
class AdminUsuarios extends Control{
    /**
     * Lista los usuarios
     * @param int $idRepositorio id del repositorio a mostrar. Si se deja en blanco (=null) muestra todos. (Debe ser admin general.)
     */
    public function listaUsuarios(int $idRepositorio=null){
        if(Sesion::checkValidSession()){
            $data['usuarios']=[];
            if(empty($idRepositorio)){
                if($_SESSION['rol']=='admin'){
                    //Lista de usuarios completa como administrador
                    $UserModel = $this->load_model("Usuario");    
                    $listaUsuarios = $UserModel->getUsuarios();
                    $data['usuarios']=$listaUsuarios;
                }else{
                    $data['error']="Sólo un administrador general puede ver todos los usuarios";//no acceso
                }
            }else{
                //recupera el repositorio
                $RepoModel = $this->load_model("Repositorio");
                $Repositorio= $RepoModel->getRepositorioId($idRepositorio);
                if($_SESSION['IdUsuario']==$Repositorio->getIdAdmin() || $_SESSION['rol']=='admin'){
                    //Lista de usuarios limitada por repositorio
                    $UserModel = $this->load_model("Usuario");    
                    $listaUsuarios = $UserModel->getUsuariosRepo($idRepositorio);
                    $data['usuarios']=$listaUsuarios;
                }else{
                    $data['error']="No tienes acceso al repositorio $idRepositorio";//no acceso
                }
            }
            $this->load_view("admin/lista_usuarios",$data);
        }else{
            header('Location: /login/login'); //Página principal del usuario
        }
    }

    /**
     * añadir un nuevo usuario.
     */
    public function addUsuarioNuevo(){
        //Solo los administradores o administradores de repositorios pueden dar de alta un usuario
        if($_SESSION['rol']=='admin' || $_SESSION['rol']=='repoadmin'){
            $data = ['rol'=>'user']; //por defecto es usuario
            if(!empty($_POST['login']) && !empty($_POST['rol']) && !empty($_POST['pass'])){
                $login = $_POST['login'];
                $UserModel = $this->load_model("Usuario");
                if(!$UserModel->existeUsuarioLogin($login)){
                    $nombre = empty($_POST['nombre'])?"":$_POST['nombre'];
                    $email = empty($_POST['email'])?"":$_POST['email'];
                    $rol = $_POST['rol'];                    
                    $pass = $_POST["pass"];
                    $UserModel->addUsuario(new UsuarioData(null,$nombre,$login,$pass,$email,$rol));
                    $usuarioRecuperado= $UserModel->getUsuarioLogin($login);
                    header("Location: /AdminUsuarios/EditUsuarioId/".$usuarioRecuperado->getId());
                }else{
                    $error='El login propuesto ya existe.';
                }
            }elseif(!empty($_POST['login']) || !empty($_POST['rol']) || !empty($_POST['pass'])) {
                $data['error']='Necesario cubrir login, contraseña y rol asignado.';
            }
            foreach($_POST as $c=>$v){
                $data[$c]=$v;
            }
            if(!empty($error)){
                $data['error']=$error;
            }
            $this->load_view("admin/add_usuario",$data);
        }else{
            header('Location: /principal/portada'); //Página principal del usuario
        }
    }

    /**
     * Edita el usuario con ID indicado
     * @param int $id ID del usuario a editar
     */
    public function editUsuarioID(int $id){
        if(Sesion::checkValidSession()){
            if(!empty($id)){
                $UserModel = $this->load_model("Usuario");
                $IdentificadorModel = $this->load_model("identificador");
                if(!empty($_POST['login']) && !empty($_POST['rol'])){
                    $Usuario=new UsuarioData($id,empty($_POST['nombre'])?"":$_POST['nombre'],$_POST['login'],null,empty($_POST['email'])?"":$_POST['email'],$_POST['rol']);
                    //Solo se puede editar el usuario si es el propio, o es admin. Sólo Admin puede cambiar Rol
                    if($_SESSION['rol']=='admin' || ($_SESSION['IdUsuario']==$id && $_SESSION['rol']==$_POST['rol'])){
                        //Intento de cambiar el login por uno que ya existe?
                        $UsuarioActual = $UserModel->getUsuarioId($id);                    
                        if($UsuarioActual->getLogin()==$Usuario->getLogin() || !$UserModel->existeUsuarioLogin($Usuario->getLogin())){ //El login debe ser el mismo o el usuario con nuevo login no debe existir
                            $UserModel->updateUsuarioID($Usuario);
                        }else{
                            $error='El login propuesto ya existe.';
                        }
                    }else{
                        $error='Sólo el administrador puede editar usuarios externos.';
                    }
                }
                $data =['usuario' => $UserModel->getUsuarioId($id)];
                $data['identificadores']=$IdentificadorModel->getIdentificadoresUsuariosRepo($id);
                if(!empty($error)){
                    $data['error']=$error;
                }
                $this->load_view("admin/info_usuario",$data);
            }
        }else{
            header('Location: /login/logout');
        }
    }

    /**
     * Borra el usuario con el ID indicado
     * @param int $id ID del usuario a editar
     */
    public function borraUsuarioID(int $id){
        if(!empty($id)){
            //Sólo se puede eliminar usuarios si se es admin, y no es el usuario propio
            if($_SESSION['rol']=='admin' && $_SESSION['IdUsuario']!=$id){
                $UserModel = $this->load_model("Usuario");
                $UserModel->deleteUsuarioID($id);
            }
        }
        header("Location: /AdminUsuarios/listaUsuarios/");
    }

    /**
     * Cambia la contraseña del usuario con ID indicado
     * @param int $id ID del usuario
     */
    public function cambiaPass(int $id){
        $data=['id'=>$id];
        if(!empty($_POST['passActual']) && !empty($_POST['nuevoPass1']) && !empty($_POST['nuevoPass2'])  ){
            $UserModel = $this->load_model("usuario");
            $Usuario = $UserModel->getUsuarioId($id);
            $UsuarioOK = $UserModel->checkLoginUsuario($Usuario->getLogin(),$_POST["passActual"]);
            if($UsuarioOK){   
                if($_POST['nuevoPass1'] == $_POST['nuevoPass2']){
                    $Usuario->setPass($_POST['nuevoPass1']);
                    $UserModel->updatePassUsuarioID($Usuario->getID(),$Usuario->getPass());
                    header("Location: /AdminUsuarios/editUsuarioId/".$id);
                    return;
                }else{
                    $data['error']='La contraseñas nuevas no coinciden.';
                }
            }else{
                $data['error']='La contraseña actual no es válida.';
            }
        }
        if(!empty($_POST['passActual'])){
            $data['passActual']=$_POST['passActual'];
        }
        if(!empty($_POST['nuevoPass1'])){
            $data['nuevoPass1']=$_POST['nuevoPass1'];
        }
        if(!empty($_POST['nuevoPass2'])){
            $data['nuevoPass2']=$_POST['nuevoPass2'];
        }
        $this->load_view("admin/cambia_pass",$data);
    }

    /**
     * Cambia la contraseña del usuario con código de recuperación indicado
     * @param string $codigo Codigo de recuperación (enviado a mail) del usuario
     */
    public function cambiaPassCodigo(string $codigo){
        $data=['codigo'=>$codigo];
        $UserModel = $this->load_model("Usuario");
        $Usuario = $UserModel->getUsuarioCodigoRecuperaPass($codigo);
        if(!is_null($Usuario)){
            if(!empty($_POST['nuevoPass1']) && !empty($_POST['nuevoPass2'])  ){
                if($_POST['nuevoPass1'] == $_POST['nuevoPass2']){
                    $Usuario->setPass($_POST['nuevoPass1']);
                    $UserModel->updatePassUsuarioID($Usuario->getID(),$Usuario->getPass());
                    header("Location: /Login/Login");
                    return;
                }else{
                    $data['error']='La contraseñas nuevas no coinciden.';
                }
            }
            if(!empty($_POST['nuevoPass1'])){
                $data['nuevoPass1']=$_POST['nuevoPass1'];
            }
            if(!empty($_POST['nuevoPass2'])){
                $data['nuevoPass2']=$_POST['nuevoPass2'];
            }
            $this->load_view("admin/cambia_pass_codigo",$data);
        }
    }

    /**
     * Crea un identificador nuevo para un usuario manualmente
     * @param int $idUsuario ID del usuario al que pertenece el identificador (=null por defecto)
     */
    public function crearIdentificador(int $idUsuario=null){
        //Solo el administrador general puede hacer esto
        if($_SESSION['rol']=='admin' || $_SESSION['rol']=='repoadmin'){
            $UserModel = $this->load_model("Usuario");
            $IdentificadorModel = $this->load_model("Identificador");
            if($UserModel->existeUsuarioId($idUsuario)){
                if(!empty($_POST['ID_Repositorio']) &&!empty($_POST['clave']) &&!empty($_POST['valor'])){

                    $Identificador = new IdentificadorData(null,$idUsuario,$_POST['clave'],$_POST['valor'],$_POST['ID_Repositorio']);
                    $IdentificadorModel->addIdentificador($Identificador);
                    header("Location: /AdminUsuarios/EditUsuarioId/".$idUsuario);
                }
                $data=['id'=>$idUsuario];
                foreach($_POST as $c=>$v){
                    $data[$c]=$v;
                }
                $RepoModel = $this->load_model("Repositorio");
                if($_SESSION['rol']=='admin'){
                    $data['repositorios'] = $RepoModel->getRepositorios();
                }elseif($_SESSION['rol']=='repoadmin'){
                    $data['repositorios'] = $RepoModel->getRepositorios($_SESSION['IdUsuario']);
                }
                $this->load_view("admin/add_identificador",$data);                
            }else{
                //Usuario no existe
                header("Location: /AdminUsuarios/listaUsuarios");    
            }
        }else{
            //no se es administrador
            header("Location: /AdminUsuarios/listaUsuarios");
        }
    }

    /**
     * Modifica un identificador de un usuario
     * @param int $idUsuario ID del usuario al que pertenece el identificador
     */
    public function editIdentificador(int $idIdentificador=null){
        //Solo el administrador general puede hacer esto
        if($_SESSION['rol']=='admin'){
            $UserModel = $this->load_model("Usuario");
            $IdentificadorModel = $this->load_model("Identificador");
            $Identificador = $IdentificadorModel->getIdentificadorId($idIdentificador);
            if(!empty($Identificador)){
                if(!empty($_POST['ID_Repositorio']) &&!empty($_POST['clave']) &&!empty($_POST['valor']) && !empty($_POST['ID_Usuario'])){
                    $IdentificadorModel->updateIdentificadorId(new IdentificadorData($idIdentificador,$_POST['ID_Usuario'],$_POST['clave'],$_POST['valor'],$_POST['ID_Repositorio']));
                    header("Location: /AdminUsuarios/EditUsuarioId/".$_POST['ID_Usuario']);
                }else{
                    $data=['identificador'=>$Identificador];
                    foreach($_POST as $c=>$v){
                        $data[$c]=$v;
                    }                    
                    //Carga listado repositorios
                    $RepoModel = $this->load_model("Repositorio");
                    $data['repositorios'] = $RepoModel->getRepositorios();
                    //Carga listado usuarios
                    $data['usuarios'] = $UserModel->getUsuarios();
                    $this->load_view("admin/edit_identificador",$data);
                }
            }
        }else{
            //no se es administrador
            header("Location: /AdminUsuarios/listaUsuarios");
        }
    }
    
    /**
     * Clona el identificador pasado y reenvía a la página para editarlo.
     * Sólo el administrador general puede clonar identificadores.
     * @param int $idIdentificador ID del identificador a clonar.
     */
    public function cloneIdentificador(int $idIdentificador){
        //Solo el administrador general puede hacer esto
        if($_SESSION['rol']=='admin'){
            $IdentificadorModel = $this->load_model("Identificador");
            $identificador = $IdentificadorModel->getIdentificadorId($idIdentificador);
            if(!empty($identificador)){
                $identificador_clon = clone $identificador;
                $ID = $IdentificadorModel->addIdentificador($identificador_clon);
                header("Location: /AdminUsuarios/editIdentificador/$ID");
            }
        }
    }

    /**
     * borra el identificador pasado. Sólo el administrador puede borrarlo.
     * @param int $idIdentificador ID del identificador a borrar en la base de datos.
     */
    public function borraIdentificador(int $idIdentificador){
        //Solo el administrador general puede hacer esto
        if($_SESSION['rol']=='admin'){
            $IdentificadorModel = $this->load_model("Identificador");
            $Identificador = $IdentificadorModel->getIdentificadorId($idIdentificador);
            $IdentificadorModel->deleteIdentificadorID($Identificador->getID());
            header("Location: /AdminUsuarios/editUsuarioID/".$Identificador->getIdUsuario());
        }
    }
}