<?php
require_once '../app/models/UsuarioData.php';
require_once '../app/models/RepositorioData.php';
require_once '../app/models/TipoRepositorio.php';

use \docweb\models\UsuarioData as UsuarioData;
use \docweb\models\RepositorioData as RepositorioData;
use \docweb\models\TipoRepositorio as TipoRepositorio;

/**
 * Controlador para administración de repositorios
 */
class AdminRepositorios extends Control{
    /**
     * Lista los repositorios
     */
    public function listaRepositorios(){
        //Sólo admin puede ver todos
        if($_SESSION['rol']=='admin'){
            $RepoModel = $this->load_model("Repositorio");
            $listaRepositorios = $RepoModel->getRepositorios();
            $data= ['repositorios'=>$listaRepositorios];
            $this->load_view("repoadmin/lista_repositorios",$data);
        }
        if($_SESSION['rol']=='repoadmin'){
            $RepoModel = $this->load_model("Repositorio");
            $listaRepositorios = $RepoModel->getRepositorios($_SESSION['IdUsuario']);
            $data= ['repositorios'=>$listaRepositorios];
            $this->load_view("repoadmin/lista_repositorios",$data);
        }
    }

    /**
     * Añade un repositorio nuevo
     */
    public function addRepoNuevo(){        
        $data['repositorio']= RepositorioData::getNewDefaultRepositorio();
        if(!empty($_POST['tipo'])){
            $data['repositorio']->setTipo($_POST['tipo']);
        }
        if(!empty($_POST['nombre'])){
            $data['repositorio']->setNombre($_POST['nombre']);
        }
        if(!empty($_POST['ruta'])){
            $data['repositorio']->setRuta($_POST['ruta']);
        }
        if(!empty($_POST['login'])){
            $data['repositorio']->setLogin($_POST['login']);
        }else{
            $data['repositorio']->setLogin(''); //login por defecto
        }
        if(!empty($_POST['pass'])){
            $data['repositorio']->setPass($_POST['pass']);
        }else{
            $data['repositorio']->setPass(''); //pass por defecto
        }

        if(!empty($_POST['ID_admin'])){
            $data['repositorio']->setIdAdmin($_POST['ID_admin']);
        }
        if(!empty($_POST['extra'])){
            $data['repositorio']->setExtraObject($_POST['extra']);
        }else{
            $data['repositorio']->setExtraObject(''); //Extra por defecto. Para compatibilidad BBDD hosting
        }

        if($_SESSION['rol']=='admin' || $_SESSION['rol']=='repoadmin'){ //solo los administradores generales o administradores de repositorios pueden añadir repositorios
            if(!empty($_POST['nombre']) && !empty($_POST['ruta']) && !empty($_POST['ID_admin']) && !empty($_POST['tipo'])){
                $RepoModel = $this->load_model("Repositorio");
                $RepoModel->addRepositorio($data['repositorio']);
                header("location: /AdminRepositorios/listaRepositorios");
            }

            if($_SESSION['rol']=='admin'){
                //Como administrador carga los posibles administradores...
                $UserModel = $this->load_model("Usuario");
                $data['repoadmins'] = $UserModel->getUsuariosRol('repoadmin');
            }else{
                //Como repoadmin sólo puede ser el mismo el administrador
                $repoAdmin = new UsuarioData($_SESSION['IdUsuario'],$_SESSION['login'],null,null,null,$_SESSION['rol']);
                $data['repoadmins'] = [$repoAdmin];
            }
            $this->load_view("repoadmin/add_repositorio",$data);
        }
    }

    /**
     * Edita datos del repositorio indicado
     * @param int $id ID del repositorio a editar.
     */
    public function editRepoId(int $id){
        $RepoModel = $this->load_model("Repositorio");
        $data['repositorio']= $RepoModel->getRepositorioId($id);

        if(!empty($_POST['tipo'])){
            $data['repositorio']->setTipo($_POST['tipo']);
        }
        if(!empty($_POST['nombre'])){
            $data['repositorio']->setNombre($_POST['nombre']);
        }
        if(!empty($_POST['ruta'])){
            $data['repositorio']->setRuta($_POST['ruta']);
        }
        if(!empty($_POST['login'])){
            $data['repositorio']->setLogin($_POST['login']);
        }
        if(!empty($_POST['pass'])){
            $data['repositorio']->setPass($_POST['pass']);
        }
        if(!empty($_POST['ID_admin'])){
            $data['repositorio']->setIdAdmin($_POST['ID_admin']);
        }
        if(!empty($_POST['extra'])){
            $data['repositorio']->setExtraObject($_POST['extra']);
        }

        if(!empty($_POST['nombre']) && !empty($_POST['ruta'])&& !empty($_POST['ID_admin']) && !empty($_POST['tipo'])){
            $RepoModel->updateRepositorioID($data['repositorio']);
            header("location: /AdminRepositorios/listaRepositorios");
        }else{
            if($_SESSION['rol']=='admin'){
                //Como administrador carga los posibles administradores...
                $UserModel = $this->load_model("Usuario");
                $data['repoadmins'] = $UserModel->getUsuariosRol('repoadmin');
            }else{
                //Como repoadmin sólo puede ser el mismo el administrador
                $repoAdmin = new UsuarioData($_SESSION['IdUsuario'],$_SESSION['login'],null,null,null,$_SESSION['rol']);
                $data['repoadmins'] = [$repoAdmin];
            }
            //Si hubo un post y estamos aquí es que había datos pendientes
            if(!empty($_POST)){
                $data['error']='Datos incorrectos.';
            }
            $this->load_view("repoadmin/info_repositorio",$data);
        }
    }

    /**
     * Borra el repositorio con ID Pasado
     * @param int $id ID del repositorio a editar.
     */
    public function borraRepoId(int $id){
        $RepoModel = $this->load_model("Repositorio");
        $RepoModel->deleteRepositorioID($id);
        header("location: /AdminRepositorios/listaRepositorios");
    }


    /**
     * Muestra información conexión del repositorio indicado...
     * @param int $id ID del repositorio a mostrar información
     */
    public function testRepoId(int $id){
        $RepoModel = $this->load_model("Repositorio");
        $repositorio = $RepoModel->getRepositorioAdapter($id);

        $data = ['id'=>$id];
        
        $data['info'] = $repositorio->getInfo();
        $data['campos'] = $repositorio->getCampos();
        $data['repositorio']=$repositorio;

        $this->load_view("repoadmin/test_repositorio",$data);
    }


    /**
     * Crea un clon del repositorio indicado
     * @param int $id ID del repositorio a clonar
     */
    public function clonRepoID(int $id){
        $RepoModel = $this->load_model("Repositorio");
        $RepositorioOriginal = $RepoModel->getRepositorioId($id);
        if(!empty($RepositorioOriginal)){
            $RepositorioClon = clone $RepositorioOriginal;
            $ID = $RepoModel->addRepositorio($RepositorioClon);
            header('Location: /AdminRepositorios/listaRepositorios/'.$id);
        }

    }


}