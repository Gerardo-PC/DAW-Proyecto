<?php
require_once '../app/models/UsuarioData.php';
require_once '../app/models/RepositorioData.php';
require_once '../app/models/TipoRepositorio.php';
require_once '../app/models/IdentificadorData.php';

use \docweb\models\UsuarioData as UsuarioData;
use \docweb\models\RepositorioData as RepositorioData;
use \docweb\models\TipoRepositorio as TipoRepositorio;
use \docweb\models\IdentificadorData as IdentificadorData;

/**
 * Controlador para gestionar la importación masiva de usuarios de Repositorio
 * desde ficheros CSV
 */
class ImportUsuariosRepo extends Control{

    /**
     * Selecciona las opciones de importación de usuarios
     * @param int $IdRepo ID del repositorio
     */
    public function selectImportOptions(int $IdRepo){
        $data = [];
        if($_SERVER["REQUEST_METHOD"] == "POST"){
            if(isset($_POST["importFichero"])){
                //recuperando datos de formulario
                if(!empty($_FILES['ficheroCSV']['tmp_name'])){
                    $_SESSION['tmp_CSV']=['fichero'=>'/tmp/'.uniqid(),'separador'=>empty($_POST['separadorCSV'])?';':$_POST['separadorCSV']];
                    move_uploaded_file($_FILES['ficheroCSV']['tmp_name'],$_SESSION['tmp_CSV']['fichero']);
                    $lectura = CsvImportFile::readCsvFile($_SESSION['tmp_CSV']['fichero'],$_SESSION['tmp_CSV']['separador']);
                    if($lectura){
                        $data['cabeceraCSV']=$lectura['cabeceraCSV'];
                        $data['datosCSV']=$lectura['datosCSV'];
                        $data['erroresCSV']=$lectura['erroresCSV'];
                    }
                }
            }
            if(isset($_POST['procesarDatos'])){
                if(isset($_POST['nombre']) && isset($_POST['login']) && isset($_POST['pass']) && isset($_POST['email'])){
                    //Array con datos del usuario
                    $usuarioCsvMap=[];
                    $usuarioCsvMap['nombre']=$_POST['nombre'];
                    $usuarioCsvMap['login']=$_POST['login'];
                    $usuarioCsvMap['pass']=$_POST['pass'];
                    $usuarioCsvMap['email']=$_POST['email'];
                    //Array con datos de identificadores
                    $identificadoresMap = [];
                    if(isset($_POST['camposRepositorio'])){
                        foreach($_POST['camposRepositorio'] as $c=>$v){
                            if($v>=0){
                                array_push($identificadoresMap,[$c,$v]);
                            }
                        }
                        if(count($identificadoresMap)>0){
                            //debe existir, al menos, un identificador
                            $importOK = CsvImportFile::createDBFromCSV($_SESSION['tmp_CSV']['fichero'],$_SESSION['tmp_CSV']['separador'], $usuarioCsvMap, $identificadoresMap,$IdRepo,$_POST["usuarioExiste"]);
                            //var_dump($usuarioCsvMap);
                            //var_dump($identificadoresMap);
                        }else{
                            $data['error']='Identificadores no definidos. Debe existir al menos uno.';
                        }
                    }else{
                        $data['error']='Usuarios sin campos mapeados..';
                    }
                }else{
                    $data['error']='Datos de usuarios inválidos.';
                }
            }
        }

        $RepoModel = $this->load_model("Repositorio");
        $data['repositorio']= $RepoModel->getRepositorioId($IdRepo);
        $repositorio = $RepoModel->getRepositorioAdapter($IdRepo);
        $data['camposRepositorio'] = $repositorio->getCampos();            
        
        $this->load_view("repoadmin/import_usuarios",$data);

    }

}