<?php

/**
 * Controlador para la página principal de usuario
 */
class Principal extends Control{

    /**
     * Redirige a la portada correspondiente en función del rol de usuario
     */
    public function portada(){
        if(!isset($_SESSION['login']) || !isset($_SESSION['rol'])){
            header('Location: /usuario/login/');
        }else{
            switch($_SESSION['rol']){
                case 'admin':
                    header('Location: /Principal/admin/');
                    break;
                case 'repoadmin':
                    header('Location: /Principal/repoadmin/');
                    break;
                case 'user':
                    header('Location: /Principal/user/');
                    break;
                default:
                //Sólo se permiten roles de admin, repoadmin o user, en caso contrario vuelta a login
                header('Location: /login/login/');
            }
        }        
    }

    /**
     * Muestra la portada de administrador (gestión usuarios y repositorios)
     */
    public function admin(){
        if(isset($_SESSION['rol']) && $_SESSION['rol']=='admin'){
            $this->load_view("admin/principal_admin");
        }else{
            header('Location: /login/login/');
        }
    }

    /**
     * Muestra la portada de administrador de repositorios.
     * Gestiona repositorios.
     */
    public function repoadmin(){
        if(isset($_SESSION['rol']) && $_SESSION['rol']=='repoadmin'){
            $this->load_view("repoadmin/principal_repoadmin",);
        }else{
            header('Location: /login/login/');
        }
    }

    /**
     * Muestra la portada de usuarios, con listado repositorios y ficheros que tiene acceso.
     */
    public function user(){
        if(!isset($_SESSION['rol']) || $_SESSION['rol']!='user'){
            header('Location: /login/login/');
        }else{
            $data=[];
            $UserModel = $this->load_model("Usuario");
            $RepoModel = $this->load_model("Repositorio");
            $IdenModel = $this->load_model("Identificador");
            //repositorios que tiene acceso
            $data['repositorios']=[];
            $arrRepoIDs = $UserModel->getReposUserId($_SESSION['IdUsuario']);
            foreach($arrRepoIDs as $v){
                $Repositorio = $RepoModel->getRepositorioID($v);
                $nombreRepo = $Repositorio->getNombre();
                
                $identificadores = $IdenModel->getIdentificadoresUsuariosRepo($_SESSION['IdUsuario'],$v);

                $arrFiltroIdentificadores = [];
                foreach($identificadores as $valorIden){
                    $arrFiltroIdentificadores[$valorIden->getClave()] = $valorIden->getValor();
                }           
                $RepoAdapter = $RepoModel->getRepositorioAdapter($v);
                $ficherosRepo = $RepoAdapter->getDocuments($arrFiltroIdentificadores);

                //$repoUserData = new RepoUserData($nombreRepo,$ficherosRepo);
                $repoUserData = new RepoUserData($nombreRepo,$ficherosRepo);
                array_push($data['repositorios'],$repoUserData);
            }
            $this->load_view("user/principal_user",$data);
        }
    }


}
