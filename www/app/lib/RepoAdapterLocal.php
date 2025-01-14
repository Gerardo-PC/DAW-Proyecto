<?php
/**
 * Clase implementa acceso a ficheros locales
 * Implementa interfaz IRepositorioAdapter.
 */
class RepoAdapterLocal implements IRepositorioAdapter{
    private $idRepo; // ID del repositorio
    private $rutaRaiz;

    private $maxDeepLevel=0; //máximo nivel de directorios en rutas
    private $arrDocumentos=array(); //Array de documentos

    private $timeLastRefresh=null; //momento de última actualización directorios.

    private $selectedDocument=null; //documento seleccionado.

    /**
     * Conecta al repositorio. Actualmente accede con credenciales de proceso php.
     * @param string    $direccion  Dirección / URL del repositorio
     * @param string    $login      Login. No se usa. Mantiene por compatibilidad con Interfaz.
     * @param string    $pass       Contraseña. No se usa. Mantiene por compatibilidad con Interfaz.
     * @param mixed     $extra      Datos adicionales. No se usan en repositorio local.
     */
    public function conecta(string $direccion, string $login=null, string $pass=null, mixed $extra=null):bool{
        if(file_exists($direccion) && is_dir($direccion)){
            $this->rutaRaiz = $direccion;

            //Rellena el array de documentos
            $this->rellenaArrDocumentos();

            return true;
        }
        return false;
    }
    /**
     * Desconecta del repositorio. No es necesario desconectar del repositorio local.
     */
    public function desconecta(){
        throw new Exception("No implementado.");
    }
    /**
     * Valida que la ruta sea accesible. No implementado porque no es necesario conectar.
     * TODO: Posible validar que validaConexion() en repositorio locales compruebe si la ruta existe.
     */
    public function validaConexion():bool{
        throw new Exception("No implementado.");
    }
    /**
     * Devuelve un objeto con información básica del repositorio
     * @return object|null|array con datos generales del repositorio.
     */
    public function getInfo():object|null|array{
        $info = (object) [
            'ruta'=>$this->rutaRaiz,
            'niveles'=>$this->maxDeepLevel,
            'documentos'=>count($this->arrDocumentos),
            'campos'=>$this->getCampos(),
            'actualizado'=>Date('Y-m-d H:i:s',$this->timeLastRefresh)
        ];
        return $info;
    }
    /**
     * Devuelve como campos los niveles de directorio L1, L2, etc.
     * @return array con los niveles de directorios numerados (["L1","L2", etc...]) hasta máxima profundidad de directorios.
     */
    public function getCampos():array{
        $campos = array();
        for($i=1;$i<=$this->maxDeepLevel;$i++){
            array_push($campos,"L$i");
        }
        return $campos;
    }
    /**
     * Devuelve los documentos recuperados conforme filtro
     * El filtro indica campos tipo "L1" = "2020" en un array asociativo
     * @param camposFiltro Array["L1"]=2020 (asociativo campo->valor filtro) con todos los campos a filtrar
     * @return array<FileUserData> con datos de los ficheros devueltos.
     */
    public function getDocuments(array $camposFiltro):array{
        $ficherosRepo = array_filter($this->arrDocumentos,function($v, $k) use($camposFiltro){
            $valido=true;
            foreach($camposFiltro as $kFiltro=>$vFiltro){
                $nivel = str_replace('L','',$kFiltro);
                if(is_numeric($nivel)){
                    $indice = intval($nivel);
                    if($indice<count($v)){
                        $valido = $valido && $v[$indice]==$vFiltro;
                    }else{
                        $valido = false; //si el nivel de filtro está fuera no considera válido.
                    }
                }
            }
            return $valido;
        }, ARRAY_FILTER_USE_BOTH);

        $arrFicherosUser = [];
        foreach($ficherosRepo as $f){
            
            $infoFichero = "";    
            for($i=1;$i<count($f);$i++){
                if(!empty($f[$i])){
                    $infoFichero.="[".$f[$i]."]";
                }
            }
            
            // no envío información completa, sólo pública
            // $infoFichero = $RepoAdapter->getDocumentInfo($f[0]->id);
            $fichero = new FileUserData($f[0]->fichero,$f[0]->id,$this->idRepo,$infoFichero);
            array_push($arrFicherosUser,$fichero);
        }
        return $arrFicherosUser;       
    }
   
    /**
     * Selecciona el documento indicado
     * @param string $idDocumento ID del documento a seleccionar. El ID en este caso es el id del documento generado por el hash.
     * @return bool true si logra seleccionarlo correctamente.
     */
    public function selectDocumentID(string $idDocumento):bool{
        $documento = array_filter($this->arrDocumentos,function($v,$k) use ($idDocumento){
            return ($v[0]->id==$idDocumento);
        }, ARRAY_FILTER_USE_BOTH);
        if(count($documento)==1){
            $this->selectedDocument =$idDocumento;
            return true;
        }
        return false;
    }

    /**
     * Recupera información de un documento con ID facilitado.
     * @param string $idDocumento ID del documento que queramos recuperar información.
     * @return object|null Objeto (stdClass) con propiedades indicando información del documento pasado.
     */
    public function getDocumentInfo(string $idDocumento):object|null{
        $documento = array_filter($this->arrDocumentos,function($v,$k) use ($idDocumento){
            return ($v[0]->id==$idDocumento);
        }, ARRAY_FILTER_USE_BOTH);
        if(count($documento)==1){
            $k =array_keys($documento)[0];
            $nombre = $documento[$k][0]->fichero;
            $id = $documento[$k][0]->id;
            $arrRuta = array_slice($documento[$k],1,count($documento[$k])-1);
            $strRuta = implode(DIRECTORY_SEPARATOR,$arrRuta);
            $strRutaCompleta = $this->rutaRaiz.DIRECTORY_SEPARATOR.$strRuta.DIRECTORY_SEPARATOR.$nombre;
            while(str_contains($strRutaCompleta,DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR)){
                $strRutaCompleta = str_replace(DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR,DIRECTORY_SEPARATOR,$strRutaCompleta); //elimina duplicados en símbolo de directorio
            }            
            $fMime = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($fMime,$strRutaCompleta);
            return (object) [
                'nombre'=>$nombre,
                'id'=>$id,
                'ruta'=>$strRuta,
                'rutaCompleta'=>$strRutaCompleta,
                'mime'=>$mime
            ];
        }
        return null;
    }
    public function getDocumentIcon(string $idDocumento):object{
        throw new Exception("No implementado.");
    }

    /**
     * Devuelve una copia del documento para descarga
     * @param string idDocumento ID del documento a recuperar.
     */
    public function getDocument(string $idDocumento):mixed{
        $fileInfo = $this->getDocumentInfo($idDocumento);
        if(!is_null($fileInfo->rutaCompleta)){
            $fOriginal = fopen($fileInfo->rutaCompleta,'r');
            $fDescarga = tmpfile();
            stream_copy_to_stream($fOriginal,$fDescarga);
            return $fDescarga;
        }
        return null;
    }

    private function recorreRuta(string $ruta,int $nivel=0){
        if(file_exists($ruta) && is_dir($ruta)){
            $hijos = scandir($ruta);
            foreach($hijos as $hijo){
                if($hijo != "." && $hijo!=".."){
                    if($nivel>$this->maxDeepLevel){ //actualiza nivel de profundidad de directorio máximo
                        $this->maxDeepLevel=$nivel;
                    }
                    $rutaHijo = $ruta.DIRECTORY_SEPARATOR.$hijo;
                    // echo "<p>[$nivel] - $rutaHijo</p>";
                    if(is_dir($ruta.DIRECTORY_SEPARATOR.$hijo)){
                        $this->recorreRuta($ruta.DIRECTORY_SEPARATOR.$hijo,$nivel+1);
                    }
                }
            }
        }else{
            return null;
        }
    }

    /**
     * Rellena el array de documentos.
     * Cada item del array tendrá:
     *      En el elemento 0 el nombre del fichero
     *      En los siguientes elementos en nombre de la carpeta de ese nivel (Ej. /2024/fiscal/publico => [1]='2024' [2]='fiscal' [3]='publico')
     *      Relleno con Null hasta el máximo de niveles de carpeta ($this->maxDeepLevel)
     * 
     */
    private function rellenaArrDocumentos(string $ruta=null,int $nivel=0){
        if(is_null($ruta)){
            $ruta=$this->rutaRaiz;
            $this->arrDocumentos = array();
        }
        if(file_exists($ruta) && is_dir($ruta)){
            $hijos = scandir($ruta);
            foreach($hijos as $hijo){
                if($hijo != "." && $hijo!=".."){
                    if($nivel>$this->maxDeepLevel){ //actualiza nivel de profundidad de directorio máximo
                        $this->maxDeepLevel=$nivel;
                    }
                    $rutaHijo = $ruta.DIRECTORY_SEPARATOR.$hijo;
                    // echo "<p>[$nivel] - $rutaHijo</p>";
                    if(is_dir($rutaHijo)){
                        $this->rellenaArrDocumentos($rutaHijo,$nivel+1);
                    }else{
                        //si es fichero lo inserta en array
                        //elimina la ruta raiz
                        $rutaInsertar = str_replace($this->rutaRaiz,'',$rutaHijo);                        
                        $arrDirectorios = explode(DIRECTORY_SEPARATOR,$rutaInsertar);
                        //añade el nombre en la primera posición, y lo elimina de la ruta
                        //$arrItem = array(array_pop($arrDirectorios));

                        //La primera posición será el nombre + id
                        $arrItem = [(object) ['fichero'=>array_pop($arrDirectorios),'id'=>hash('sha256',$rutaHijo)]];

                        //añade los directorios
                        foreach($arrDirectorios as $dir){  
                            if(strlen($dir)>0){
                                array_push($arrItem,$dir);
                            }
                        }
                        //echo "<p style='color:red'>Insertando</p>";
                        //var_dump($arrItem);
                        
                        array_push($this->arrDocumentos,$arrItem);
                        
                    }
                }
            }
            //a la salida de última iteración de nivel, rellena con null todos los items hasta $this->maxDeepLevel
            if($nivel==0){ 
                $this->timeLastRefresh = time();

                // echo "<p style='color:green'> Rellenando nivel de profundidad máximo: $this->maxDeepLevel </p>";
                for($i=0;$i<count($this->arrDocumentos);$i++){
                    while(count($this->arrDocumentos[$i])<=$this->maxDeepLevel){
                        array_push($this->arrDocumentos[$i],null);
                    }
                    //actualiza hash en elemento 0. Algoritmo crc32 para mayor velocidad...
                    //$hash =hash('crc32', implode($this->arrDocumentos[$i]).$this->timeLastRefresh);
                    //$this->arrDocumentos[$i][0] = (object) ['fichero'=>$this->arrDocumentos[$i][0],'id'=>$hash];

                }
            }
        }else{
            return null;
        }
    }

    /**
     * Constructor de repositorio.
     * @param int       $idRepo     ID del repositorio
     * @param string    $direccion  Dirección (Ruta local)
     * @param string    $login      Login empleado - No se utiliza.
     * @param string    $pass       Contraseña empleada - No se utiliza.
     * @param mixed     $extra      Datos adicionales para acceso a repositorio. No es necesario en repos locales.
     */
    public function __construct(int $idRepo, string $direccion, string $login=null, string $pass=null, mixed $extra=null){
        $this->idRepo = $idRepo;
        $this->conecta($direccion,$login,$pass);
    }
    /**
     * Destruye el objeto. No necesario desconectar de repositorios locales.
     */
    public function __destruct(){
        //no hay nada de que desconectar.
    }
}