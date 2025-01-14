<h1>Repositorios <?= $_SESSION['nombre']?></h1>
<?php

    if(!empty($datos['repositorios'])){
        echo "<div class='paginaRepositorios'>";
        echo "<div class='contenedorRepositorios'>";
        $textoInfoFiltros='';
        foreach($datos['repositorios'] as $repositorio){

            echo "<div id='tab".$repositorio->getNombre()."' class='contenedorRepo'>";            
            echo "<a href='#tab".$repositorio->getNombre()."'><h3>Repositorio:".$repositorio->getNombre()."</h3></a>";
            
            echo "<div class='filesRepo'>";
            //echo "<ul>";
            foreach($repositorio->getFicheros() as $fichero){
                //echo "<li>";
                echo "<div class='fileItem' id='".$fichero->getIdFichero()."'>";
                    echo "<img class='iconoDocumento' src='/img/FicheroRepos.svg' alt='imagen genérica de documento'/>";
                    echo "<p class='nombreDocumento'>".$fichero->getNombrefichero()."</p>";
                    if(!empty($fichero->getAdditionalInfo())){
                        echo "<p class='infoDocumento'>".$fichero->getAdditionalInfo()."</p>";
                        $textoInfoFiltros.=$fichero->getAdditionalInfo(); //añade el texto de información de filtros en una cadena total para filtrar luego.
                    }
                    $idRepositorio = $fichero->getIdRepositorio();
                    $idFichero=$fichero->getIdFichero();
                    echo "<a class='descargaDocumento' href='/fichero/descargar/$idRepositorio/$idFichero'>Descargar</a>";
                    echo "</div>";
                //echo "</li>";
            }
            //echo "</ul>";
            echo "</div>"; //filesRepo
            echo "</div>"; //contenedorRepo
        }

        echo "</div>"; //contenedor Repositorios (todos)

        echo "<div id='barraFiltros' class='barraFiltros'>"; //Ubicación para los filtros.
        echo "<img id='showfilter' class='btnFiltro btnFiltroGrande' src='/img/icon/filter-cancel.svg' alt='filtro'/>";
        echo "<div id='cloudFiltros'>";
        $etiquetas = [];
        //Extrae texto entre corchetes para uso como filtros.
        if(preg_match_all('/\[.*?\]/',$textoInfoFiltros,$filtrosAll)){
            sort($filtrosAll[0]);
            foreach($filtrosAll[0] as $t){
                if(!in_array($t,$etiquetas)){
                    array_push($etiquetas,$t);
                    echo "<span class='filtroDoc'>$t</span>";
                }
            }
        echo "<script defer type='text/javascript' src='/js/filtroDocs.js'></script>";
        echo "<script defer type='text/javascript' src='/js/mejoraNombreFicheros.js'></script>";
        }
        echo "<input type='text' id='filtroNombreDoc'></input> ";
        echo "</div>"; //cloudFiltros ... nube de etiquetas con posibles filtros.
        echo "</div>"; //barraFiltros (barra de filtros)
        echo "</div>"; //paginaRepositorios (página completa.);

    }else{
        echo "<p> Ningún documento disponible </p>";
    }
    


?>
