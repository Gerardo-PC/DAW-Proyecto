<?php
use \docweb\models\RepositorioData as RepositorioData;
/**
 * Clase que gestiona llamadas API de repositorios (generalmente para dar servicios a llamadas Ajax)
 */
class ApiRepositorios extends Control{
    /**
     * Devuelve las claves de un repositorio
     * @param int $id idRepositorio
     * Devuelve -imprime- json con las claves del repositorio indicado (array [[clave1],[clave2]]) o vacío [] si no encuentra.
     */
    public function getRepoClaves(int $id){
        $respuesta = '[]';
        if(!empty($id) && ($_SESSION['rol']=='admin' || $_SESSION['rol']=='repoadmin')){
            $RepoModel = $this->load_model("Repositorio");
            $Repositorio = $RepoModel->getRepositorioId($id);            
            if($_SESSION['rol']=='admin' || ($_SESSION['rol']=='repoadmin' && $Repositorio->getIdAdmin()==$_SESSION["IdUsuario"])){
                $RepoAdapter = $RepoModel->getRepositorioAdapter($id);
                $campos = $RepoAdapter->getCampos();
                if(!empty($campos)){
                    $respuesta = json_encode($campos);
                }
            }
        }
        header("Content-type: application/json; charset=utf-8");
        echo $respuesta;
    }
}