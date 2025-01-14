<?php
require_once '../app/models/UsuarioData.php';
use \docweb\models\UsuarioData as UsuarioData;

// Incluir la libreria PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
/**
 * Controlador para login de usuarios 
 * Gestiona también altas/recuperar contraseña
 */
class Login extends Control{
    /**
     * Gestiona autenticación en la aplicación
     * @param string $loginPrefill Usuario por defecto (=null por defecto)
     */
    public function login(string $loginPrefill=null){
        if($this::checkValidDB()){
            //Comprobación de formulario enviado
            $data=[];
            if(!is_null($loginPrefill)){
                $data['login']=$loginPrefill;
            }
            if(!empty($_POST["login"])){
                $data['login']=$_POST["login"];
                if(!empty($_POST["pass"])){
                    $data['pass']=$_POST["pass"];
                    $UserModel = $this->load_model("Usuario");
                    $UsuarioOK = $UserModel->checkLoginUsuario($_POST["login"],$_POST["pass"]);
                    if($UsuarioOK){
                        $_SESSION["login"]=$_POST["login"];
                        $Usuario = $UserModel->getUsuarioLogin($_SESSION["login"]);

                        $_SESSION["nombre"]=$Usuario->getNombre();
                        $_SESSION["rol"]=$Usuario->getRol();
                        $_SESSION["IdUsuario"]=$Usuario->getId();
                        header('Location: /principal/portada'); //Página principal del usuario
                        return true;
                    }else{
                        $data["usuarioNoValido"]=true; //Usuario no correcto en BBDD
                        error_log("Intento de inicio de sesión con usuario no correcto (".$_POST["login"].")");
                    }
                }else{
                    $data["usuarioNoValido"]=true; //Usuario sin pass
                }
            }
            $this->load_view("login",$data);
        }
    }

    /**
     * Cierra la sesión, vuelve a la pantalla de login.
     */
    public function logout(){
        session_destroy();
        header('Location: /login/login'); //Usuario creado... redirige a Login.
    }

    /**
     * Añade un usuario nuevo (registra nuevo usuario)
     */
    public function registra(){
        if($this::checkValidDB()){
            $datos = [];
            if(!empty($_POST["login"])&& !empty($_POST["pass"])){            
                $login = $_POST["login"];
                $UserModel = $this->load_model("Usuario");
                $usuarioExiste = $UserModel->existeUsuarioLogin($login);    
                $pass = $_POST["pass"];
                //TODO: Mejorar seguridad, para pruebas contraseñas +3 caracteres suficiente
                if(strlen($_POST["pass"])<=3){ 
                    $datos['error']="Contraseña inválida. Pocos caracteres.";
                }elseif(!$usuarioExiste && empty($datos['passNoValido'])){
                    //El primer usuario dado de alta siempre es admin, el resto usuarios a menos que el admin original los habilite.
                    if($UserModel->existenUsuarios()){
                        $rol="user"; //existen usuarios, se asigna rol de usuario
                    }else{
                        $rol="admin";//no existen usuarios, se asigna rol de administrador
                    }
                    $UserModel->addUsuario(new UsuarioData(null,'',$login,$pass,'',$rol));
                    header('Location: /login/login/'.$login); //Usuario creado... redirige a Login.
                }            
            }
            if(!empty($login)){
                $datos["login"]=$login;
                if($usuarioExiste){
                    $datos["error"] = 'El usuario ya existe.';
                }
            }
            $this->load_view("registro",$datos);    
        }
    }

    /**
     * Solicita mail para recuperar contraseña.
     * Si el mail existe envía correo de recuperación.
     */
    public function recuperaContrasenaMail(){
        if($this::checkValidDB()){
            $data=[];
            if(!empty($_POST['recuperar'])){
                if(!empty($_POST['login'] && !empty($_POST['email']))){
                    //validar que los datos existen en BBDD
                    $UserModel = $this->load_model("Usuario");
                    $User = $UserModel->getUsuarioLogin($_POST['login']);
                    if($User && $User->getEmail()==$_POST['email']){
                        $codigoRecuperacion = $UserModel->generarCodigoRecuperaPass($User->getID());
                        $urlRecuperacion = $_SERVER["HTTP_ORIGIN"].'/AdminUsuarios/cambiaPassCodigo/'.$codigoRecuperacion;
                        $resultadoMail = Email::enviar('pcgerard@gmail.com','Recuperación cuenta WebDoc',"Estimado usuario. Puede recuperar su contraseña accediendo a <a href=$urlRecuperacion>este vínculo</a><br>El código de recuperación es: <b>$codigoRecuperacion</b>");
                        if($resultadoMail){
                            $data['mensaje'] ='El mensaje para recuperar contraseña se ha enviado.';
                        } else {
                            $data['error']="No se puede enviar mensaje mail recuperación cuenta.";
                        }
                    }else{
                        $data['error']='Usuario o mail no válidos.';
                    }
                }
                foreach($_POST as $c => $v){
                    $data[$c] = $v;
                }                
            }
            $this->load_view("recuperaContrasenaMail",$data);   
        }
    }

    /**
     * Devuelve True si es posible conectarse a la base de datos. En caso contrario reenvía a página error.
     * @return true si es posible establecer conexión con la base de datos.
     */
    private function checkValidDB():bool{
        $Db = new DB();
        if(!$Db->isConnected()){
            $config = Configuracion::getConfiguracion();
            $this->load_view("error",['error'=>'Base de datos '.$config->db->basedatos.' inaccesible en '.$config->db->servidor.'. Revisar configuracion.']);
        }
        return $Db->isConnected();
    }

}