<?php
/**
 * Clase implementa acceso a ficheros archivadores Docuware
 * Extiende IRepositorioAdaptar
 */
class RepoAdapterDW implements IRepositorioAdapter{

    private $idRepo; //Id del repositorio.
    private $url;
    private $login;
    private $pass;
    private $idArchivador;
    private $token; //Token acceso OAuth2
    private $tokenExpiresTime; // Tiempo en el que el token deja de ser válido

    /**
     * actualiza el ID del repositorio
     * @param int $idRepo ID del repositorio
     */
    private function setIdRepo(int $idRepo){
        $this->idRepo = $idRepo;
    }
    /**
     * Actualiza la URL del repositorio (formato: https://MISUBDOMINIO.docuware.cloud)
     * @param string $url del repositorio
     */
    private function setUrl(string $url){
        $url= parse_url($url, PHP_URL_SCHEME).':/'.parse_url($url, PHP_URL_HOST);
        $this->url = $url;
    }
    /**
     * Actualiza el login empleado para acceder al repositorio
     * @param string $login
     */
    private function setLogin(string $login){
        $this->login=$login;
    }
    /**
     * Actualiza la contraseña empleada para acceder al repositorio
     * @param string $pass
     */
    private function setPass(string $pass){
        $this->pass=$pass;
    }
    /**
     * Actualiza el ID del archivador donde se encuentran los documentos.
     * @param string $idArchivador
     */
    private function setIdArchivador(string $idArchivador){
        $this->idArchivador=$idArchivador;
    }
    /**
     * Actualiza el Token empleado para acceso OAuth a Docuware
     * @param string|null $token Token empleado
     * @param int $validSecs Número de segundos que quedan para ser válido.
     */
    private function setToken(string|null $token, int $validSecs){
        $this->token = $token;
        $this->tokenExpiresTime = time()+$validSecs-1; //Quita 1 segundo para redondear tiempo en recuperar y guardar...
    }

    /**
     * Recupera el ID del repositorio
     * @return int ID del repositorio
     */
    private function getIdRepo():int{
        return $this->idRepo;
    }
    /**
     * Recupera la URL del repositorio 
     * @return string URL de tenant Docuware (formato: https://MISUBDOMINIO.docuware.cloud)
     */
    private function getUrl():string{
        return $this->url;
    }
    /**
     * Recupera la URL completa 
     * @return string URL completa de la plataforma Docuware (incluyendo /Docuware/Platform)
     */
    private function getUrlPlatform():string{
        return $this->url.'/Docuware/Platform';
    }
    /**
     * Recupera el login empleado para acceso a tenant Docuware
     * @return string Login de acceso a repositorio Docuware
     */
    private function getLogin():string{
        return $this->login;
    }
    /**
     * Recupera la contraseña de acceso a tenant Docuware
     * @return string Contraseña de acceso a repositorio
     */
    private function getPass():string{
        return $this->pass;
    }
    /**
     * Recupera el ID de archivador Docuware donde están los documentos.
     * @return string ID del archivador
     */
    private function getIdArchivador():string{
        return $this->idArchivador;
    }
    /**
     * Recupera el Token de acceso OAuth empleado.
     * @return string Token de acceso OAuth
     */
    private function getToken():string{
        return $this->token;
    }
    /**
     * Constructor de repositorio.
     * @param int       $idRepo     ID del repositorio
     * @param string    $direccion  URL del tenant
     * @param string    $login      Login empleado para acceder al repositorio (o null) 
     * @param string    $pass       Contraseña empleada para acceder al repositorio (o null)
     * @param mixed     $extra      GUID Archivador documentos
     */
    public function __construct(int $idRepo, string $url, string $login=null, string $pass=null, mixed $extra=null){
        $this->setIdRepo($idRepo);
        $this->setUrl($url);
        $this->setLogin($login);
        $this->setPass($pass);
        $this->setIdArchivador($extra);
        $this->conecta($url, $login,$pass, $extra);
    }
    /**
     * Desconecta del repositorio (llama a desconecta()) y destruye el objeto.
     */
    public function __destruct(){
        try{
            $this->desconecta();
        }catch(Exception $e){
            error_log('Error destruyendo objeto RepoAdapterDW '.$e->getMessage());
        }
    }

    /**
     * Conecta a repositorio Docuware y obtiene token Oauth2
     * @param dirección de conexión (url)
     * @param login usuario loguear
     * @param pass Contraseña usuario
     * @param extra UID del archivador a conectar
     * @return bool true si conecta correctamente y obtiene token. False en caso contrario.
     */
    public function conecta(string $direccion, string $login=null, string $pass=null, mixed $extra=null):bool{
        $this->setUrl($direccion);
        if(!is_null($login)){$this->setLogin($login);};
        if(!is_null($pass)){$this->setPass($pass);};
        if(!is_null($extra)){$this->setIdArchivador($extra);};
        if(!is_null($this->getUrlPlatform()) && !is_null($this->getLogin()) && !is_null($this->getPass()) && !is_null($this->getIdArchivador())){
            try{
                $IdentityServiceURL = $this->GetResponsibleIdentityService();
                if(!empty($IdentityServiceURL)){
                    $TokenEndpoint = $this->GetIdentityServiceConfiguration($IdentityServiceURL);
                    $this->RequestTokenWUsernamePassword($TokenEndpoint);
                }
            }catch(Exception $e){
                echo "Problema conectando a ".$this->getUrl()." ->".$e->getMessage();
            }
        }
        return false;
    }
    /**
     * Desconecta del repositorio.
     */
    public function desconecta(){
        $this->setToken(null,0);
    }
    /**
     * Valida la conexión a repositorio.
     * @return bool True si está conectado
     */
    public function validaConexion():bool{
        //valida tratando de recuperar información de organización conectada.
        try{
            $organizacion = $this->getOrganizacion();
            return !empty($organizacion);
        }catch(Exception $e){
            error_log("Error validando conexión: ".$e->getMessage());
        }
        return false;
    }
    /**
     * Devuelve información del archivador Docuware conectado.
     * Devuelve nombre de la organización a la que está conectado.
     */
    public function getInfo():object|null|array{
        $organization = $this->getOrganization();
        return $organization;
    }
    /**
     * Devuelve los campos accesibles del Archivador
     * @return array Array con los campos del Archivador al que está conectado.
     */
    public function getCampos():array{
        $fileCabinetInfo =  $this->getFileCabinetInformation();
        $arrCampos=[];
        if(!empty($fileCabinetInfo)){
            foreach($fileCabinetInfo->Fields as $Campo){
                array_push($arrCampos,$Campo->DBFieldName);
            }
        }
        return $arrCampos;
    }
    /**
     * Devuelve información de los documentos del repositorio que cumplen los campos de filtro.
     * @param   array   $camposFiltro Array asociativo con campos => valores para filtrar.
     * @return  array<FileUserData>   Array de FileUserData con información de ficheros.
     */
    public function getDocuments(array $camposFiltro):array{
        $arrCondiciones = [];
        $additionalInfoConsulta = '';
        foreach($camposFiltro as $c=>$v){
            $c = (object)['DBName'=>$c,'Value'=>[$v]];
            array_push($arrCondiciones,$c);
            $additionalInfoConsulta.="[".$v."]";
        }

        $objConsulta = (object) ['Condition'=>$arrCondiciones,'Operation'=>'And'];
        $jsonConsulta = json_encode($objConsulta);
        $objRespuesta = $this->searchDocumentsSingleFileCabinet($jsonConsulta);

        //Construye el array de respuesta con los datos de ficheros
        $arrRespuesta = [];
        foreach($objRespuesta->Items as $c => $Item){
            $nombreFichero = $Item->Title.$Item->Fields[4]->Item;
            $idFichero = $Item->Id;
            $idRepositorio =  $this->getIdRepo();
            $additionalInfo = '[No info]';
            //Información adicional básica... los campos empleados para filtrar...
            if(!is_null($additionalInfoConsulta)){
                $additionalInfo = $additionalInfoConsulta;
            }

            // Recorre los campos de sistema en DW y los pone como info. adicional. Realmente es información no necesaria, lo interesante serían los campos personalizados
            // pero es necesaria una llamada por cada fichero y validar si todos los campos personalizados tienen info que se pueda ver.
            // foreach($Item->Fields as $f){
            //     if(!is_null($f) && property_exists($f,'Item') && !is_object($f->Item)){
            //         $additionalInfo .='['.$f->FieldName.' = '.$f->Item.']';
            //     }
            // }
            /** TODO: Devolver campos información adicional adicionales en repositorio Docuware */
            $fichero = new FileUserData($nombreFichero,$idFichero,$idRepositorio,$additionalInfo);
            array_push($arrRespuesta,$fichero);
        }
        return $arrRespuesta;
    }
    /**
     * Selecciona el documento con ID indicado
     * TODO: Pendiente implementar 
     */
    public function selectDocumentID(string $idDocumento):bool{

    }
    /**
     * Devuelve información del documento indicado
     * @param string idDocumento ID del documento para recuperar información
     * @return object|null Objeto con campos que son información del documento o null si no es posible recuperarla.
     */
    public function getDocumentInfo(string $idDocumento):object|null{
        $documentRawInfo = $this->getSpecificDocumentFromFileCabinet($idDocumento);
        $nombre = $documentRawInfo->Title;
        $id = $documentRawInfo->Id;
        $mime = $documentRawInfo->ContentType;
        $campos = '';
        foreach($documentRawInfo->Fields as $f){
            if(!is_null($f) && property_exists($f,'Item') && !is_object($f->Item)){
                $campos .='['.$f->FieldName.' = '.$f->Item.']';
                //Si es el campo de la extensión la añade al nombre de documento.
                if($f->FieldName=="DWEXTENSION"){
                    if(count($documentRawInfo->Sections)==1){
                        $nombre.=$f->Item;
                    }else{
                        $nombre.='.zip'; //Ficheros con varias secciones descargan como .zip.
                    }
                }
            }
        }
        if(!empty($documentRawInfo)){
            return (object) [
                'nombre'=>$nombre,
                'id'=>$id,
                //'ruta'=>$strRuta,
                //'rutaCompleta'=>$strRutaCompleta,
                'info'=>$campos,
                'mime'=>$mime
            ];
        }
        return null;
    }
    /**
     * Recupera el icono del documento con ID pasado
     * @param string $idDocumento
     * TODO: Pendiente implementar.
     */
    public function getDocumentIcon(string $idDocumento):object{

    }
    /**
     * Recupera el documento con ID indicado.
     * @param string $idDocumento
     * @return string Dirección de archivo temporal (tmpfile()) donde se guardó el fichero desde el repositorio.
     */
    public function getDocument(string $idDocumento):mixed{
        $fichero = $this->downloadDocument($idDocumento);
        if(!is_null($fichero)){
            $tmp = tmpfile();
            fwrite($tmp,$fichero);
            return $tmp;
        }
        return null;
    }

    /**
     * Devuelve la URL responsable de identificación del sistema Docuware
     * @return string|null URL de identificación del tenant Docuware
     */
    private function GetResponsibleIdentityService():string|null{
        $IdentityServiceUri = null;

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->getUrlPlatform().'/Home/IdentityServiceInfo',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array('Accept: application/json'),
        ));

        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        curl_close($curl);
        if($httpcode===200){
            $objRespuesta = json_decode($response);            
            return $objRespuesta->IdentityServiceUrl; 
        }else{
            return null;
        }
    }

    /**
     * Maneja configuración de identificación y devuelve la url para solicitar tokens Oauth2
     * @Param string IdentityServiceUrl
     */
    private function GetIdentityServiceConfiguration($IdentityServiceUrl){
        $curl = curl_init();
    
        curl_setopt_array($curl, array(
            CURLOPT_URL => $IdentityServiceUrl.'/.well-known/openid-configuration',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));
        
        $response = curl_exec($curl);
        
        $httpcode = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        curl_close($curl);
        if($httpcode===200){
            $objRespuesta = json_decode($response);            
            return $objRespuesta->token_endpoint; 
        }else{
            return null;
        }

    }

    /**
     * Solicita un token de identificación Oauth en la url indicada
     * @param string $TokenEndpoint URL donde gestionar token Oauth.
     */
    private function RequestTokenWUsernamePassword($TokenEndpoint){
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $TokenEndpoint,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => 'grant_type=password&scope=docuware.platform&client_id=docuware.platform.net.client&username='.$this->getLogin().'&password='.$this->getPass(),
        CURLOPT_HTTPHEADER => array(
            'Accept: application/json',
            'Content-Type: application/x-www-form-urlencoded'
        ),
        ));

        $response = curl_exec($curl);

        $httpcode = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        curl_close($curl);
        if($httpcode===200){
            $objRespuesta = json_decode($response);
            $this->setToken($objRespuesta->access_token,$objRespuesta->expires_in);
            return $objRespuesta->access_token;
        }else{
            return null;
        }

    }

    /**
     * Devuelve información de la Organización almacenada en Docuware
     */
    private function getOrganization(){
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $this->getUrlPlatform().'/Organizations',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Accept: application/json',
            'Authorization: Bearer '.$this->getToken(),
        ),
        ));

        $response = curl_exec($curl);

        $httpcode = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        curl_close($curl);

        if($httpcode===200){
            $objRespuesta = json_decode($response);
            return [$objRespuesta->Organization[0]->Name.' (GUID '.$objRespuesta->Organization[0]->Guid.')'];
        }else{
            return null;
        }
    }

    /**
     * Devuelve información del archivador conectado
     */
    private function getFileCabinetInformation(){
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $this->getUrlPlatform().'/FileCabinets/'.$this->getIdArchivador(),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Accept: application/json',
            'Authorization: Bearer '.$this->getToken(),
        ),
        ));

        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        curl_close($curl);
        if($httpcode===200){
            $objRespuesta = json_decode($response);
            return $objRespuesta;
        }else{
            return null;
        }
    }

    /**
     * Devuelve los documentos del archivador con una búsqueda conforme el json enviado
     * Json ejemplo:
     *   {
     *    "Condition":[ {"DBName":"COMPANY_NAME", "Value":["US-Steel"]},
     *                  {"DBName":"DOCUMENT_DATE", "Value":["2010-03-01", "2010-03-30"]}
     *                ],
     *    "Operation":"Or"
     *   }
     */
    private function searchDocumentsSingleFileCabinet(string $jsonConsulta){
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => $this->getUrlPlatform().'/FileCabinets/'.$this->getIdArchivador().'/Query/DialogExpression?Fields=DOCUMENT_TYPE&SortOrder=DOCUMENT_TYPE+Asc&DialogId=00000000-0000-0000-0000-000000000000',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS =>$jsonConsulta,
          CURLOPT_HTTPHEADER => array(
            'Accept: application/json',
            'Content-Type: application/json',
            'Authorization: Bearer '.$this->getToken(),
          ),
        ));
        
        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        curl_close($curl);
        if($httpcode===200){
            $objRespuesta = json_decode($response);
            return $objRespuesta;
        }else{
            return null;
        }
    }

    /**
     * Devuelve información de un documento específico.
     * @param int $idDoc ID del documento en archivador Docuware.
     */
    private function getSpecificDocumentFromFileCabinet(int $idDoc):object|null{
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
          CURLOPT_URL => $this->getUrlPlatform().'/FileCabinets/'.$this->getIdArchivador().'/Documents/'.$idDoc,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_HTTPHEADER => array(
            'Accept: application/json',
            'Authorization: Bearer '.$this->getToken(),
          ),
        ));
        
        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        curl_close($curl);
        if($httpcode===200){
            $objRespuesta = json_decode($response);
            return $objRespuesta;
        }else{
            return null;
        }
    }

    /**
     * Descarga -y devuelve el contenido- del documento con ID pasado
     * @param int $idDoc ID del documento en repositorio Docuware
     */
    private function downloadDocument($idDoc){

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $this->getUrlPlatform().'/FileCabinets/'.$this->getIdArchivador().'/Documents/'.$idDoc.'/FileDownload?TargetFileType=Auto&KeepAnnotations=false',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Accept: application/json',
            'Authorization: Bearer '.$this->getToken(),
        ),
        ));

        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        curl_close($curl);
        if($httpcode===200){
            return $response;
        }else{
            return null;
        }

    }
}