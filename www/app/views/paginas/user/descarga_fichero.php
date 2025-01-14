<div class='previewPdfContainer'>

<a class='btnDescarga' href='/fichero/descargar/<?=$datos['idRepo']?>/<?=$datos['idFichero']?>/true'>
        <div >
        <img src='/img/icon/file-download.svg' alt='imagen descargar fichero'/>
        <h2><?=$datos['nombreFichero']?></h2>
        </div>
</a>

<iframe name='fichero' class='previewFile'
        title='<?=$datos['nombreFichero']?>'
        src='data:application/pdf;base64,<?=$datos['b64']?>#view=fit'>
</iframe>
</div>

<?php
    VistaComponentes::menuBar([
        (Object)['tooltip'=>'Página principal',         'url'=>'/principal/portada',            'icono'=>'/img/icon/home.svg']
    ]);
?>