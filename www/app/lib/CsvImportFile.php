<?php
require_once '../app/models/Usuario.php';
require_once '../app/models/UsuarioData.php';
require_once '../app/models/Identificador.php';
require_once '../app/models/IdentificadorData.php';

use \docweb\models\UsuarioData as UsuarioData;
use \docweb\models\Usuario as Usuario;
use \docweb\models\IdentificadorData as IdentificadorData;
use \docweb\models\Identificador as Identificador;

/**
 * clase de acceso a datos de fichero CSV para importación
 * Permite guardar en BBDD los campos conforme mapas facilitados.
 */
class CsvImportFile{

    /**
     * Lee datos de un fichero CSV
     * @param string $rutaFicheroCSV Ruta al fichero
     * @param string  $separador Caracter empleado como separador
     * @return true|array asociativo ['cabeceraCSV'] ['datosCSV'] ['erroresCSV'] con datos leidos. false si no se logra leer.
     */
    public static function readCsvFile(string $rutaFicheroCSV, string $separador=';'):bool|array{
        $respuesta=['cabeceraCSV'=>[], 'datosCSV'=>[], 'erroresCSV'=>[]];
        if(file_exists($rutaFicheroCSV)){
            try{       
                $ficheroCSV = fopen($rutaFicheroCSV,'r');        
                if($ficheroCSV){
                    $respuesta['cabeceraCSV']=fgetcsv($ficheroCSV,null,$separador);
                    $camposCabeceraCSV=count($respuesta['cabeceraCSV']);
                    $respuesta['datosCSV']=[];
                    $respuesta['erroresCSV']=[];
                    $lineaCSV = 1;
                    while($datosCSV=fgetcsv($ficheroCSV,null,$separador)){
                        if(count($datosCSV)==$camposCabeceraCSV){
                            array_push($respuesta['datosCSV'],$datosCSV);
                        }else{
                            array_push($respuesta['erroresCSV'],"Error en CSV línea $lineaCSV. El número de campos no coincide (leidos ".count($datosCSV)." de ".$camposCabeceraCSV.") ");
                        }
                        $lineaCSV++;
                    }
                }else{
                    $respuesta['error']='Necesario fichero CSV válido.';
                }
            }catch(Exception $e){
                echo "Error gestionando CSV.",$e->getMessage();
                unset($respuesta['cabeceraCSV']);
                unset($respuesta['datosCSV']);
                unset($respuesta['erroresCSV']);
                if(file_exists($rutaFicheroCSV)){
                    unlink($rutaFicheroCSV);
                }
                $respuesta = false;
            }finally{
                fclose($ficheroCSV);
            }
        }else{
            $respuesta['error']='Fichero no existe.';
        }
        return $respuesta;
    }

    /**
     * Lee datos del CSV e importa en la base de datos conforme mapas de usuarios e identificadores indicados.
     * @param string $rutaFicheroCSV Ruta del fichero para leer los datos
     * @param string $separador Caracter separador (habitual ; o ,)
     * @param array $usuarioCsvMap[] Array asociativo con el mapa de campos para datos de usuario.
     * @param array $identificadoresMap[] Array con el mapa de identificadores. Una entrada para cada identificador y [0=>Campo Repo, 1=>Valor]
     * @param int $IdRepositorio Identificador del repositorio donde se importan.
     * @param string $usrExiste Comportamiento en caso de que el usuario exista ("usrReplace"->Reemplazar usuario -comportamiento por defecto-, "usrAdd"->Añade identificadores, "usrSkip"->Omite el usuario)
     * @return int número de usuarios actualizados.
     */
    public static function createDBFromCSV(string $rutaFicheroCSV, string $separador, $usuarioCsvMap, $identificadoresMap, int $IdRepositorio,string $usrExiste='usrReplace'):int{
        try{
            $usuarioModel = new Usuario();
            $identificadorModel = new Identificador();
            $CSVData = CsvImportFile::readCsvFile($rutaFicheroCSV,$separador);
            $arrIdUsuarios=[];
            if(empty($erroresCSV)){
                foreach($CSVData['datosCSV'] as $c => $linea){
                    $nombre = $linea[$usuarioCsvMap['nombre']];
                    $login = $linea[$usuarioCsvMap['login']];
                    $pass = $linea[$usuarioCsvMap['pass']];
                    $email = $linea[$usuarioCsvMap['email']];                
                    $usuario = new UsuarioData(null,$nombre,$login,$pass,$email,'user');

                    $usuarioExiste = $usuarioModel->getUsuarioLogin($login);
                    if($usuarioExiste && $usrExiste=='usrSkip'){
                        // ... El usuario existe y se indica que no se modifique. no hace nada... 
                    }else{
                        if($usuarioExiste && $usrExiste=='usrReplace'){
                            //Borra antes de volverlo a crear. El nuevo tendrá otro identificador.
                            $usuarioModel->deleteUsuarioID($usuarioExiste->getId());
                        }                       
                        if(!$usuarioExiste || ($usuarioExiste && $usrExiste=='usrReplace')){
                            //Si el usuario no existe... o si hay que reemplazarlo... lo crea...
                            $IdUsuario = $usuarioModel->addUsuario($usuario);
                        }else{
                            //usrExiste es ='usrAdd' y debe añadir
                            $IdUsuario = $usuarioExiste->getID();
                        }
                        if($IdUsuario>0){
                            array_push($arrIdUsuarios,$IdUsuario);
                            $identificadoresActuales = $identificadorModel->getIdentificadoresUsuariosRepo($IdUsuario,$IdRepositorio);
                            foreach($identificadoresMap as $i=>$v){
                                $identificador = new IdentificadorData(null,$IdUsuario,$v[0],$linea[$v[1]],$IdRepositorio);
                                //Comprueba que el identificador no exista
                                $identificadorExiste = false;
                                foreach($identificadoresActuales as $c=>$v){
                                    if($v->getIdUsuario()==$IdUsuario &&
                                       $v->getClave()==$identificador->getClave() &&
                                       $v->getValor()==$identificador->getValor() &&
                                       $v->getIdRepositorio()==$identificador->getIdRepositorio()){
                                            $identificadorExiste=true;
                                       }
                                }
                                if(!$identificadorExiste){                                    
                                    $identificadorModel->addIdentificador($identificador);
                                }
                            }
                        }else{
                            error_log("ERROR importando usuario.");
                        }
                    }
                }
            }
        }catch(Exception $e){
            error_log("Error importando desde CSV.".$e->gerMessage(),);
            if(!empty($arrIdUsuarios)){
                error_log("Usuarios importados antes de error:".implode(';',$arrIdUsuarios));
            }
        }
        return count($arrIdUsuarios);
    }

}