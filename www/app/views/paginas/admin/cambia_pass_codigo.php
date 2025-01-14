<h1>Información usuario</h1>
<p>Recuperando contraseña usuario código <?=$datos['codigo']?> </p>
<form action='/AdminUsuarios/cambiaPassCodigo/<?=$datos['codigo']?>' method="POST">
    <label for="nuevoPass1">Nueva Contraseña:</label> <input type="password" id = "nuevoPass1" name="nuevoPass1" value='<?=empty($datos['nuevoPass1'])?'':$datos['nuevoPass1']?>'>
    <label for="nuevoPass2">Repetir nueva contraseña:</label> <input type="password" id = "nuevoPass2" name="nuevoPass2" value='<?=empty($datos['nuevoPass2'])?'':$datos['nuevoPass2']?>'> 
    <?php if(!empty($datos['error'])){echo "<p style='color:red'>".$datos['error']."</p>";} ?>
    <input type="submit" value="ACTUALIZAR">
</form>

<?php
    VistaComponentes::menuBar([
        (Object)['tooltip'=>'Volver a Login',
                'url'=>'/Login/Login/',
                'icono'=>'/img/icon/home.svg']
    ]);
?>


