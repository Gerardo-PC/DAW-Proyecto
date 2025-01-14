<h1>Información usuario</h1>
<p>Cambiando contraseña usuario <?=$datos['id']?> </p>
<form action='/AdminUsuarios/cambiaPass/<?=$datos['id']?>' method="POST">
    <label for="passActual">Contraseña actual:</label> <input type="password" id = "passActual" name="passActual" value='<?=empty($datos['passActual'])?'':$datos['passActual']?>'>
    <label for="nuevoPass1">Nueva Contraseña:</label> <input type="password" id = "nuevoPass1" name="nuevoPass1" value='<?=empty($datos['nuevoPass1'])?'':$datos['nuevoPass1']?>'>
    <label for="nuevoPass2">Repetir nueva contraseña:</label> <input type="password" id = "nuevoPass2" name="nuevoPass2" value='<?=empty($datos['nuevoPass2'])?'':$datos['nuevoPass2']?>'>
    <input type="submit" value="ACTUALIZAR">
    <?php if(!empty($datos['error'])){echo "<p class='msgerror'>".$datos['error']."</p>";} ?>
</form>

<?php
    VistaComponentes::menuBar([
        (Object)['tooltip'=>'Volver a usuario',
                'url'=>'/AdminUsuarios/editUsuarioID/'.$datos['id'],
                'icono'=>'/img/icon/user-share.svg'],
        (Object)['tooltip'=>'Página principal',
                'url'=>'/principal/portada',
                'icono'=>'/img/icon/home.svg']         
    ]);
?>