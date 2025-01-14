<?php
/**
 * Controlador para operaciones con ficheros. 
 * Descarga o muestra los ficheros.
 */
class Fichero extends Control{

    /**
     * Descarga el fichero indicado con idRepo e idFichero
     * Si forzarDescarga = true lo descarga directamente
     * En caso contrario los ficheros .pdf los muestra en un iframe y el resto los descarga.
     * @param int $idRepo ID del repositorio del que descargar el fichero
     * @param string $idFichero ID del fichero a descargar
     * @param forzarDescarga (opcional). Por defecto a false. Si true fuerza descarga del fichero aunque sea .pdf
     */
    public function descargar(int $idRepo,string $idFichero,$forzarDescarga=false){
        try{
            //echo "descargando fichero $idFichero de repo $idRepo";
            $RepoModel = $this->load_model("Repositorio");
            $Repositorio = $RepoModel->getRepositorioID($idRepo);

            $RepoAdapter = $RepoModel->getRepositorioAdapter($idRepo);            
            $ficheroDescargaInfo = $RepoAdapter->getDocumentInfo($idFichero);

            $ficheroDescarga = $RepoAdapter->getDocument($idFichero);
            $rutaDescarga = stream_get_meta_data($ficheroDescarga)['uri'];

            //Los ficheros .pdf los muestra en iframe, los otros ficheros los descarga directamente...

            if(str_ends_with($ficheroDescargaInfo->nombre,'.pdf') && !$forzarDescarga){
                //Muestra ficheros (.pdf) en un iFrame.
                $b64=base64_encode(file_get_contents($rutaDescarga));
                $data = ['rutaDescarga'=>$rutaDescarga,'nombreFichero'=>$ficheroDescargaInfo->nombre,'b64'=>$b64,'idRepo'=>$idRepo,'idFichero'=>$idFichero];
                $this->load_view("user/descarga_fichero",$data);
            }else{
                //Descarga directa del fichero
                header('Content-Disposition: attachment; filename="'.$ficheroDescargaInfo->nombre.'"');
                readfile($rutaDescarga);    
            }

        }catch(Exception $e){
            error_log("Error descargando fichero ".$e->getMessagee());
            echo "Error descargando fichero ".$e->getMessagee();
        }
        
    }

}